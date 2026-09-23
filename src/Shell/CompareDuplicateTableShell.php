<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;

/**
 * Put two copies of the same table side by side and say how they differ.
 *
 * bin/check-duplicate-tables.php finds a table name living in two databases
 * and counts the rows in each. Equal counts are where it stops being useful:
 * four rows here and four rows there can be the same four orders, or four
 * entirely different ones. Only the rows themselves settle it, and the answer
 * decides whether moving a table class is a one-line fix or a way to point a
 * feature at the wrong data.
 *
 * apprentice_orders is the case this was written for. The application reads
 * the cms_tmm_trainees copy, while apprentice_order_shares - which points at
 * apprentice_orders by id - lives beside the cms_tmm_apprentices copy. Since
 * CakePHP cannot join across connections, one of those two arrangements is
 * wrong, and which one depends on where the real orders are.
 *
 * Report only. There is no --apply: nothing here should be automatic.
 *
 * Usage:
 *     bin/cake compare_duplicate_table
 *     bin/cake compare_duplicate_table --table=email_templates \
 *         --left=default --right=cms_authentication_authorization
 */
class CompareDuplicateTableShell extends Shell
{
    /** Rows read per copy. Enough for the duplicates on file, with a warning. */
    const LIMIT = 2000;

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Compare two copies of the same table, row by row.')
            ->addOption('table', [
                'help' => 'Table name present in both databases.',
                'default' => 'apprentice_orders',
            ])
            ->addOption('left', [
                'help' => 'First connection.',
                'default' => 'cms_tmm_apprentices',
            ])
            ->addOption('right', [
                'help' => 'Second connection.',
                'default' => 'cms_tmm_trainees',
            ])
            ->addOption('key', [
                'help' => 'Primary key column.',
                'default' => 'id',
            ]);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $table = (string)$this->param('table');
        $key = (string)$this->param('key');
        $sides = ['left' => (string)$this->param('left'), 'right' => (string)$this->param('right')];

        $this->out('');
        $this->out(sprintf('<info>%s</info> in <info>%s</info> and <info>%s</info>',
            $table, $sides['left'], $sides['right']));
        $this->out('');

        $columns = [];
        $rows = [];
        foreach ($sides as $side => $name) {
            try {
                $connection = ConnectionManager::get($name);
                $columns[$side] = $connection->getSchemaCollection()->describe($table)->columns();
                $rows[$side] = $this->readRows($connection, $table, $key);
            } catch (\Exception $e) {
                $this->abort(sprintf('Could not read %s from %s: %s', $table, $name, $e->getMessage()));
            }
        }

        $this->reportColumns($columns, $sides);
        $shared = array_values(array_intersect($columns['left'], $columns['right']));
        $this->reportRows($rows, $shared, $sides, $key);

        if ($table === 'apprentice_orders') {
            // The question that started this. Kept here rather than in a tool
            // of its own because it is the only reason the comparison matters.
            $this->reportShares($rows, $key);
        }

        $this->out('');
        $this->out('<info>Nothing changed.</info> This tool only reads.');

        return null;
    }

    /**
     * @return array key value => row
     */
    protected function readRows($connection, $table, $key)
    {
        $statement = $connection->execute(
            'SELECT * FROM `' . $table . '` ORDER BY `' . $key . '` ASC LIMIT ' . self::LIMIT);
        $out = [];
        foreach ($statement->fetchAll('assoc') as $row) {
            $out[(string)$row[$key]] = $row;
        }

        if (count($out) === self::LIMIT) {
            $this->out(sprintf('<warning>Only the first %d rows were read.</warning>', self::LIMIT));
        }

        return $out;
    }

    /**
     * Columns one copy has and the other does not. Schema drift is worth
     * seeing before row differences, because it explains some of them.
     */
    protected function reportColumns(array $columns, array $sides)
    {
        $onlyLeft = array_diff($columns['left'], $columns['right']);
        $onlyRight = array_diff($columns['right'], $columns['left']);

        if (!$onlyLeft && !$onlyRight) {
            $this->out(sprintf('  both copies have the same %d column(s)', count($columns['left'])));

            return;
        }

        $this->out('  <warning>the two copies do not have the same columns</warning>');
        if ($onlyLeft) {
            $this->out(sprintf('    only in %s: %s', $sides['left'], implode(', ', $onlyLeft)));
        }
        if ($onlyRight) {
            $this->out(sprintf('    only in %s: %s', $sides['right'], implode(', ', $onlyRight)));
        }
        $this->out('    only the shared columns are compared below');
    }

    /**
     * Which keys are in one copy, the other, or both - and for the ones in
     * both, which columns disagree.
     */
    protected function reportRows(array $rows, array $shared, array $sides, $key)
    {
        $leftKeys = array_keys($rows['left']);
        $rightKeys = array_keys($rows['right']);
        $onlyLeft = array_diff($leftKeys, $rightKeys);
        $onlyRight = array_diff($rightKeys, $leftKeys);
        $both = array_intersect($leftKeys, $rightKeys);

        $this->out('');
        $this->out(sprintf('  %-38s %s', $sides['left'], count($leftKeys) . ' row(s)'));
        $this->out(sprintf('  %-38s %s', $sides['right'], count($rightKeys) . ' row(s)'));
        $this->out('');
        $this->out(sprintf('  %d %s in both, %d only in %s, %d only in %s',
            count($both), $key, count($onlyLeft), $sides['left'], count($onlyRight), $sides['right']));

        if ($onlyLeft) {
            $this->out(sprintf('    only in %s: %s', $sides['left'], implode(', ', $onlyLeft)));
        }
        if ($onlyRight) {
            $this->out(sprintf('    only in %s: %s', $sides['right'], implode(', ', $onlyRight)));
        }

        $differing = [];
        foreach ($both as $id) {
            $fields = [];
            foreach ($shared as $column) {
                $a = $rows['left'][$id][$column];
                $b = $rows['right'][$id][$column];
                // Compared as text: the two drivers can hand back an int and a
                // numeric string for the same stored value.
                if ((string)$a !== (string)$b) {
                    $fields[$column] = [$a, $b];
                }
            }
            if ($fields) {
                $differing[$id] = $fields;
            }
        }

        $this->out('');
        if (!$both) {
            $this->out('  <warning>no key appears in both copies - these are different sets of rows</warning>');

            return;
        }
        if (!$differing) {
            $this->out(sprintf('  <success>every %s present in both holds identical values</success>', $key));

            return;
        }

        $this->out(sprintf('  <warning>%d of %d shared row(s) differ</warning>',
            count($differing), count($both)));
        foreach ($differing as $id => $fields) {
            $this->out(sprintf('    %s %s', $key, $id));
            foreach ($fields as $column => $pair) {
                $this->out(sprintf('      %-26s %-24s | %s',
                    $column,
                    $this->short($pair[0]),
                    $this->short($pair[1])));
            }
        }
    }

    /**
     * Which copy the shares actually point at.
     *
     * apprentice_order_shares lives in cms_tmm_apprentices and holds an
     * apprentice_order_id. If those ids exist in one copy and not the other,
     * that settles which database the shares belong beside.
     */
    protected function reportShares(array $rows, $key)
    {
        $this->out('');
        $this->out('  <info>what apprentice_order_shares points at</info>');

        try {
            $connection = ConnectionManager::get('cms_tmm_apprentices');
            $shares = $connection->execute(
                'SELECT apprentice_order_id, COUNT(*) AS shares
                 FROM `apprentice_order_shares` GROUP BY apprentice_order_id')->fetchAll('assoc');
        } catch (\Exception $e) {
            $this->out('    could not read apprentice_order_shares: ' . $e->getMessage());

            return;
        }

        if (!$shares) {
            $this->out('    no shares recorded, so nothing points at either copy yet');
            $this->out('    - whichever copy is chosen, no existing row is orphaned');

            return;
        }

        $this->out(sprintf('    %-14s %-8s %-18s %s', 'order id', 'shares', 'in apprentices', 'in trainees'));
        foreach ($shares as $row) {
            $id = (string)$row['apprentice_order_id'];
            $this->out(sprintf('    %-14s %-8s %-18s %s',
                $id,
                $row['shares'],
                isset($rows['left'][$id]) ? 'yes' : 'NO',
                isset($rows['right'][$id]) ? 'yes' : 'NO'));
        }
    }

    /**
     * @return string A value short enough to sit in a column.
     */
    protected function short($value)
    {
        if ($value === null) {
            return '(null)';
        }
        $value = str_replace(["\n", "\r"], ' ', (string)$value);

        return mb_strlen($value) > 22 ? mb_substr($value, 0, 21) . '…' : $value;
    }
}
