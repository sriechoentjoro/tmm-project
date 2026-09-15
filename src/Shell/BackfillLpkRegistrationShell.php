<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

/**
 * Mark the LPKs that finished registering but were never recorded as having
 * finished.
 *
 * Two registration flows write to vocational_training_institutions and they
 * kept score in different columns:
 *
 *   InstitutionRegistration::complete   calls completeRegistration(), which
 *                                       sets is_registered and registered_at
 *   Admin\LpkRegistration::setPassword  only ever moved 'status' to 'active'
 *
 * Every counter and badge that asks "is it registered?" reads is_registered, so
 * an LPK that finished through the admin flow showed Status: Active and
 * Registered: No on the same row and sat in the verify page's Pending count
 * for good. setPassword() records it from now on; this repairs the rows that
 * went through before it did.
 *
 * Only rows with status = 'active' are touched. That status is written in one
 * place - after the account is saved and the password is set - so it is not a
 * guess about what happened, it is the record of it.
 *
 * registered_at is taken from the best evidence available, in this order:
 *
 *   1. the 'activation' entry in stakeholder_activities, which setPassword
 *      writes at the moment it activates the account
 *   2. email_verified_at, which is the closest earlier moment that was recorded
 *   3. nothing - is_registered is still set, because that it happened is
 *      certain even where when it happened was never written down
 *
 * Usage:
 *     bin/cake backfill_lpk_registration            list what would change
 *     bin/cake backfill_lpk_registration --apply    write it
 */
class BackfillLpkRegistrationShell extends Shell
{
    /**
     * Option parser.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription(
                'Set is_registered and registered_at on LPKs whose registration finished ' .
                'through the admin flow, which never recorded it.'
            )
            ->addOption('apply', [
                'help' => 'Write the changes. Without it nothing is written and the rows are only listed.',
                'boolean' => true,
            ]);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $apply = (bool)$this->param('apply');
        $table = TableRegistry::getTableLocator()->get('VocationalTrainingInstitutions');

        try {
            $rows = $table->find()
                ->where([
                    'status' => 'active',
                    'OR' => [
                        'is_registered IS' => null,
                        'is_registered' => 0,
                    ],
                ])
                ->order(['id' => 'ASC'])
                ->all();
        } catch (\Exception $e) {
            // The 'status' column arrives with database/migrations/
            // stakeholder_management_schema.sql. Without it this flow was never
            // installed and there is nothing here to repair.
            $this->abort('Could not read the institutions: ' . $e->getMessage());
        }

        if ($rows->isEmpty()) {
            $this->out('<success>Nothing to do: every active LPK is already marked as registered.</success>');

            return null;
        }

        $activations = $this->_activationTimes();

        $this->out(sprintf('%d active LPK(s) are not marked as registered.', $rows->count()));
        $this->out('');
        $this->out(sprintf('  %-5s %-40s %-21s %s', 'id', 'name', 'registered_at', 'taken from'));
        $this->hr();

        $plan = [];
        foreach ($rows as $row) {
            if (isset($activations[$row->id])) {
                $when = $activations[$row->id];
                $source = 'activation log';
            } elseif (($when = $this->_asTime($row->email_verified_at)) !== null) {
                $source = 'email_verified_at';
            } else {
                $when = null;
                $source = '<warning>no date on record</warning>';
            }

            $plan[] = [$row->id, $when];
            $this->out(sprintf(
                '  %-5d %-40s %-21s %s',
                $row->id,
                $this->_shorten($row->name, 40),
                $when ? $when->format('Y-m-d H:i:s') : '(left empty)',
                $source
            ));
        }
        $this->hr();

        if (!$apply) {
            $this->out('');
            $this->out('<info>Nothing written.</info> Run it again with --apply to make these changes.');

            return null;
        }

        // updateAll, not save(): this is a data repair, and putting these rows
        // back through the entity rules would fail any of them whose location
        // ids or email no longer satisfy a rule added since - refusing to
        // repair a row precisely because it is old.
        $written = 0;
        foreach ($plan as list($id, $when)) {
            $data = ['is_registered' => 1];
            if ($when !== null) {
                // Formatted here rather than handed over as an object. An
                // update takes its types from the column, and where the column
                // is not a real DATETIME the object is cast with __toString(),
                // which is the localised form - "1/5/26, 11:45 AM" went into
                // the database before this line existed.
                $data['registered_at'] = $when->format('Y-m-d H:i:s');
            }
            $written += $table->updateAll($data, ['id' => $id]);
        }

        $this->out('');
        $this->out(sprintf('<success>%d row(s) updated.</success>', $written));

        return null;
    }

    /**
     * When each institution was activated, according to the activity log.
     *
     * setPassword() writes one 'activation' entry per institution at the moment
     * it activates the account, which is the closest thing to a real
     * registered_at that exists for these rows. The earliest is taken, in case
     * an institution was activated more than once.
     *
     * The table is optional - it arrives with the same migration as 'status' -
     * so a missing one means no dates, not a failure.
     *
     * @return array<int, \Cake\I18n\FrozenTime>
     */
    protected function _activationTimes()
    {
        $out = [];
        try {
            $activities = TableRegistry::getTableLocator()->get('StakeholderActivities');
            $rows = $activities->find()
                ->select(['stakeholder_id', 'created'])
                ->where([
                    'activity_type' => 'activation',
                    'stakeholder_type' => 'vocational_training',
                ])
                ->order(['created' => 'ASC'])
                ->all();

            foreach ($rows as $row) {
                $when = $this->_asTime($row->created);
                if ($when !== null && !isset($out[$row->stakeholder_id])) {
                    $out[$row->stakeholder_id] = $when;
                }
            }
        } catch (\Exception $e) {
            $this->out('<warning>No activity log to read dates from: ' . $e->getMessage() . '</warning>');
        }

        return $out;
    }

    /**
     * A date column as a time object, whatever the column turned out to be.
     *
     * The ORM hands back a FrozenTime where the column is a real DATETIME and a
     * plain string where it is not - and these tables are built by hand-written
     * SQL that has not always agreed with itself. Calling ->format() on the
     * string is a fatal error, so nothing is assumed here.
     *
     * @param mixed $value Whatever the column held.
     * @return \Cake\I18n\FrozenTime|null Null for empty, or for text that is
     *     not a date at all.
     */
    protected function _asTime($value)
    {
        if (empty($value)) {
            return null;
        }
        if ($value instanceof \DateTimeInterface) {
            return new \Cake\I18n\FrozenTime($value);
        }

        try {
            return new \Cake\I18n\FrozenTime((string)$value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param string $text Text to shorten.
     * @param int $length Maximum length.
     * @return string
     */
    protected function _shorten($text, $length)
    {
        $text = (string)$text;

        return mb_strlen($text) > $length ? mb_substr($text, 0, $length - 1) . '…' : $text;
    }
}
