<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * Copy promotions recorded before the decision trail existed into the trail.
 *
 * The trail starts from the day the logs table was prepared. Promotions made
 * before that left a row in promotion_histories instead - a table nothing
 * reads, now set aside as promotion_histories_old. What is only in there is
 * the date, the person who made the call, and that it happened at all; the
 * identity chain itself is safe in trainees.candidate_id and
 * apprentices.trainee_id, and survives whatever happens to this table.
 *
 * So this copies the three facts across, and nothing else. The data snapshot
 * stays where it is: three kilobytes of JSON in a column meant for a sentence
 * would not be read, and its fidelity is doubtful anyway - every nested list
 * in the one row on file is empty, which either means the person genuinely had
 * no courses, family or education recorded, or that the snapshot was taken
 * without loading them. The trail line points at the row instead.
 *
 * Every line written says it was copied afterwards. That marking is the point:
 * an entry reconstructed later is not worth the same as one written at the
 * moment somebody decided, and a trail that blurs the difference loses what it
 * is for.
 *
 * Report only unless --apply. Running it twice does not write twice.
 *
 * Usage:
 *     bin/cake backfill_promotion_trail
 *     bin/cake backfill_promotion_trail --apply
 */
class BackfillPromotionTrailShell extends Shell
{
    /**
     * What each promotion_type means in the trail's vocabulary.
     *
     * Anything not listed is reported and skipped rather than guessed at: a
     * wrong action name puts the line under the wrong heading for ever.
     */
    const ACTIONS = [
        'trainee to apprentice' => 'trainee.promoteToApprentice',
        'candidate to trainee' => 'candidate.promoteToTrainee',
    ];

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Copy old promotion records into the decision trail.')
            ->addOption('table', [
                'help' => 'Table holding the old records.',
                'default' => 'promotion_histories_old',
            ])
            ->addOption('connection', [
                'help' => 'Connection it sits on.',
                'default' => 'cms_tmm_apprentices',
            ])
            ->addOption('apply', [
                'help' => 'Write the lines. Without it they are only reported.',
                'boolean' => true,
            ]);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $table = trim((string)$this->param('table'));
        $connectionName = trim((string)$this->param('connection'));
        $apply = (bool)$this->param('apply');

        if (!preg_match('/^[a-z0-9_]+$/i', $table)) {
            $this->abort('That does not look like a table name.');
        }

        $logs = TableRegistry::getTableLocator()->get('Audit');
        $map = $logs->columnMap();
        if (!isset($map['action'])) {
            $this->abort('The logs table is not prepared for the trail yet. '
                . 'Run bin/cake add_audit_log first.');
        }

        try {
            $rows = ConnectionManager::get($connectionName)
                ->execute('SELECT * FROM `' . $table . '` ORDER BY id ASC')
                ->fetchAll('assoc');
        } catch (\Exception $e) {
            $this->abort('Could not read ' . $table . ': ' . $e->getMessage());
        }

        $this->out('');
        $this->out(sprintf('<info>%d record(s)</info> in %s', count($rows), $table));
        $this->out('');

        $written = 0;
        $skipped = 0;
        foreach ($rows as $row) {
            $line = $this->lineFor($row, $table);
            if (is_string($line)) {
                $this->out(sprintf('  <warning>skipped</warning>  #%s  %s', $row['id'], $line));
                $skipped++;
                continue;
            }

            if ($this->alreadyThere($logs, $map, $line)) {
                $this->out(sprintf('  <success>already there</success>  #%s  %s about %s',
                    $row['id'], $line['action'], $line['subject_label']));
                $skipped++;
                continue;
            }

            $this->out(sprintf('  %s  #%s  <info>%s</info>  %s  by %s  on %s',
                $apply ? 'writing' : 'would write',
                $row['id'], $line['action'], $line['subject_label'],
                $line['username'] ?: ('user ' . $line['user_id']),
                $line['created']));

            if (!$apply) {
                continue;
            }

            $values = [];
            foreach ($line as $canonical => $value) {
                if (isset($map[$canonical])) {
                    $values[$map[$canonical]] = $value;
                }
            }
            $entity = $logs->newEntity($values, ['validate' => false]);
            if ($logs->save($entity, ['checkRules' => false, 'validate' => false])) {
                $written++;
            } else {
                $this->out(sprintf('    <warning>could not be saved: %s</warning>',
                    json_encode($entity->getErrors())));
            }
        }

        $this->out('');
        if (!$apply) {
            $this->out('<info>Nothing changed.</info> Run it again with --apply to write them.');
            $this->out('Each line will say it was copied afterwards rather than recorded');
            $this->out('at the time, and the old table is left exactly as it is.');

            return null;
        }

        $this->out(sprintf('<success>%d line(s) written, %d skipped.</success>', $written, $skipped));
        $this->out('Read them at /audit/trail.');

        return null;
    }

    /**
     * Turn one old record into the values a trail line needs.
     *
     * @return array|string The values, or a sentence saying why not.
     */
    protected function lineFor(array $row, $table)
    {
        $type = strtolower(trim((string)($row['promotion_type'] ?? '')));
        if (!isset(self::ACTIONS[$type])) {
            return 'promotion_type "' . ($row['promotion_type'] ?? '') . '" has no matching action';
        }
        if (!empty($row['is_cancelled'])) {
            // A cancelled promotion is two decisions, not one, and nothing in
            // the record says when it was taken back. Guessing would put a
            // false sequence in the trail.
            return 'this promotion was cancelled - the trail would need both the '
                . 'promotion and the reversal, and the reversal has no date here';
        }

        $sourceTable = (string)($row['source_table'] ?? '');
        $sourceId = (int)($row['source_id'] ?? 0);
        if ($sourceTable === '' || !$sourceId) {
            return 'no source recorded, so there is nobody to attach the line to';
        }

        $snapshot = json_decode((string)($row['data_snapshot'] ?? ''), true);
        $label = is_array($snapshot) && isset($snapshot['name'])
            ? (string)$snapshot['name']
            : $this->nameOf($sourceTable, $sourceId);

        $userId = isset($row['promoted_by']) ? (int)$row['promoted_by'] : null;

        $detail = [
            'backfilled' => sprintf(
                'copied from %s #%s; recorded at the time in that table, not in this trail',
                $table, $row['id']),
            'promoted_to' => trim(sprintf('%s #%s',
                (string)($row['target_table'] ?? '?'), (string)($row['target_id'] ?? '?'))),
            'snapshot' => sprintf('%s #%s, column data_snapshot', $table, $row['id']),
        ];
        if (!empty($row['promotion_reason'])) {
            $detail['reason'] = (string)$row['promotion_reason'];
        }

        return [
            'action' => self::ACTIONS[$type],
            'created' => (string)($row['promotion_date'] ?? ''),
            'user_id' => $userId,
            'username' => $userId ? $this->usernameOf($userId) : null,
            'role_names' => null,
            'subject_type' => Inflector::camelize(Inflector::singularize($sourceTable)),
            'subject_id' => $sourceId,
            'subject_label' => mb_substr((string)$label, 0, 255),
            'detail' => json_encode($detail, JSON_UNESCAPED_UNICODE),
            'ip' => null,
            'user_agent' => null,
        ];
    }

    /**
     * Has this promotion already been copied across?
     *
     * Matched on the action, the subject and the date rather than on anything
     * stored in the old table, so a second run is harmless however the record
     * was written the first time.
     */
    protected function alreadyThere($logs, array $map, array $line)
    {
        $conditions = [];
        foreach (['action', 'subject_id', 'created'] as $canonical) {
            if (!isset($map[$canonical])) {
                return false;
            }
            $conditions[$map[$canonical]] = $line[$canonical];
        }

        try {
            return (bool)$logs->find()->where($conditions)->count();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return string A person's name, or the id if it cannot be read.
     */
    protected function nameOf($table, $id)
    {
        try {
            $alias = Inflector::camelize($table);
            $row = TableRegistry::getTableLocator()->get($alias)->find()
                ->where(['id' => $id])->enableHydration(false)->first();
            if ($row && isset($row['name'])) {
                return (string)$row['name'];
            }
        } catch (\Exception $e) {
        }

        return '#' . $id;
    }

    /**
     * @return string|null The username at the time this runs, or null.
     */
    protected function usernameOf($userId)
    {
        try {
            $row = TableRegistry::getTableLocator()->get('Users')->find()
                ->where(['id' => $userId])->enableHydration(false)->first();
            if ($row && isset($row['username'])) {
                return (string)$row['username'];
            }
        } catch (\Exception $e) {
        }

        return null;
    }
}
