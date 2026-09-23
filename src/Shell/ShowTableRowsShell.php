<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;

/**
 * Print a table's rows in full, one column per line.
 *
 * The comparison tools shorten long values so their columns stay readable,
 * which is right for scanning and wrong for deciding. A promotion_histories
 * row showed as `promotion_reason=Passed training and p…` and
 * `data_snapshot={"id":1,"pre_trainee_…` - enough to know something is there,
 * not enough to judge whether it is worth keeping. This prints the whole
 * value.
 *
 * It reads through the application's own connection configuration, so no
 * password is typed and no database name can be mistyped into the wrong
 * server.
 *
 * There is no free-form WHERE. A table name and an id are enough for what this
 * is for, and a WHERE taken from the command line would be a SQL injection
 * surface built for no real gain.
 *
 * Read only. Nothing here writes.
 *
 * Usage:
 *     bin/cake show_table_rows --table=<name> --connection=<connection>
 *     bin/cake show_table_rows --table=<name> --connection=<connection> --id=1
 *     bin/cake show_table_rows --table=<name> --connection=<connection> --limit=5
 */
class ShowTableRowsShell extends Shell
{
    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Print a table\'s rows in full.')
            ->addOption('table', ['help' => 'Table to read.'])
            ->addOption('connection', ['help' => 'Connection it sits on.'])
            ->addOption('id', ['help' => 'A single row, by primary key.'])
            ->addOption('key', ['help' => 'Primary key column.', 'default' => 'id'])
            ->addOption('limit', ['help' => 'Rows to print.', 'default' => '20']);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $table = trim((string)$this->param('table'));
        $connectionName = trim((string)$this->param('connection'));
        $key = trim((string)$this->param('key'));
        $limit = max(1, (int)$this->param('limit'));

        if ($table === '' || $connectionName === '') {
            $this->abort('Both --table and --connection are required.');
        }
        foreach (['table' => $table, 'key' => $key] as $what => $value) {
            if (!preg_match('/^[a-z0-9_]+$/i', $value)) {
                // These go into SQL that cannot be parameterised.
                $this->abort('That does not look like a ' . $what . ' name.');
            }
        }

        try {
            $connection = ConnectionManager::get($connectionName);
        } catch (\Exception $e) {
            $this->abort('Could not reach ' . $connectionName . ': ' . $e->getMessage());
        }

        $sql = 'SELECT * FROM `' . $table . '`';
        $params = [];
        if ($this->param('id') !== null && $this->param('id') !== false) {
            // The value is parameterised even though the column name cannot be.
            $sql .= ' WHERE `' . $key . '` = ?';
            $params[] = $this->param('id');
        }
        $sql .= ' ORDER BY `' . $key . '` ASC LIMIT ' . $limit;

        try {
            $rows = $connection->execute($sql, $params)->fetchAll('assoc');
        } catch (\Exception $e) {
            $this->abort('Could not read it: ' . $e->getMessage());
        }

        $this->out('');
        $this->out(sprintf('<info>%s</info> in <info>%s</info>', $table, $connectionName));

        if (!$rows) {
            $this->out('  no rows matched');

            return null;
        }

        $width = 0;
        foreach ($rows as $row) {
            foreach (array_keys($row) as $column) {
                $width = max($width, strlen($column));
            }
        }

        foreach ($rows as $row) {
            $this->out('');
            $this->out(sprintf('  <info>%s %s</info>',
                $key, isset($row[$key]) ? $row[$key] : '?'));
            foreach ($row as $column => $value) {
                if ($column === $key) {
                    continue;
                }
                if ($value === null) {
                    $this->out(sprintf('    %-' . $width . 's  <comment>(null)</comment>', $column));
                    continue;
                }
                if ($value === '') {
                    $this->out(sprintf('    %-' . $width . 's  <comment>(empty)</comment>', $column));
                    continue;
                }
                // Wrapped at spaces only. Breaking inside a long unbroken run
                // would split a JSON snapshot mid-token, and a snapshot that
                // cannot be copied out as valid JSON is not much use to the
                // person reading it. A terminal soft-wraps the rest.
                $lines = explode("\n", wordwrap(str_replace("\r", '', (string)$value), 90, "\n", false));
                $this->out(sprintf('    %-' . $width . 's  %s', $column, array_shift($lines)));
                foreach ($lines as $line) {
                    $this->out(sprintf('    %-' . $width . 's  %s', '', $line));
                }
            }
        }

        $this->out('');
        $this->out(sprintf('<info>%d row(s).</info> Nothing was written.', count($rows)));

        return null;
    }
}
