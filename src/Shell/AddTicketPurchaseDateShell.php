<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * Give a ticket the date it was bought.
 *
 * A ticket records who it is for, its number, its booking reference, its type,
 * its status and its price - and no date at all. The flight dates live on the
 * legs, which is right for a flight and wrong for a cost: a ticket bought in
 * March for an August departure is money spent in March.
 *
 * Having no date, the auto-generated journal used date('Y-m-d') - the day
 * somebody happened to press Generate. A cost recorded months after it was
 * incurred lands in the wrong period, and every figure built on that period is
 * wrong with it. Nothing said so, because a date was always produced.
 *
 * With this column the journal is dated when the money was spent. A ticket
 * whose purchase date has not been filled in is held back on the review page
 * with that as the reason, rather than being posted under today's date.
 *
 * The equivalent SQL, for anyone who would rather run it by hand:
 *
 *     -- cms_tmm_trainee_document_ticketings
 *     ALTER TABLE tickets ADD COLUMN purchase_date DATE NULL;
 *
 * The connection is resolved from TicketsTable rather than named here, so the
 * ALTER cannot land in the wrong database. There is a second tickets table, on
 * the apprentice ticketing connection, read by ApprenticeTicketsTable; no
 * journal is generated from it, so it is left alone.
 *
 * Report only unless --apply.
 *
 * Usage:
 *     bin/cake add_ticket_purchase_date
 *     bin/cake add_ticket_purchase_date --apply
 */
class AddTicketPurchaseDateShell extends Shell
{
    const COLUMN = 'purchase_date';

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Add purchase_date to the tickets table.')
            ->addOption('apply', [
                'help' => 'Run the ALTER. Without it the change is only reported.',
                'boolean' => true,
            ]);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $tickets = TableRegistry::getTableLocator()->get('Tickets');
        $connectionName = $tickets->getConnection()->configName();
        $connection = ConnectionManager::get($connectionName);
        $table = $tickets->getTable();

        $this->out('');
        $this->out(sprintf('Connection: <info>%s</info>, table <info>%s</info>',
            $connectionName, $table));

        try {
            $columns = $connection->getSchemaCollection()->describe($table)->columns();
        } catch (\Exception $e) {
            $this->abort('Could not read ' . $table . ': ' . $e->getMessage());
        }

        if (in_array(self::COLUMN, $columns, true)) {
            $this->out(sprintf('<success>Nothing to do: %s is already there.</success>', self::COLUMN));

            return null;
        }

        $sqlite = strpos(strtolower(get_class($connection->getDriver())), 'sqlite') !== false;
        $sql = sprintf('ALTER TABLE `%s` ADD COLUMN %s %s NULL',
            $table, self::COLUMN, $sqlite ? 'TEXT' : 'DATE');

        $this->out('');
        $this->out('This would run:');
        $this->out('  <info>' . $sql . '</info>');
        $this->out('');

        if (!$this->param('apply')) {
            $this->out('<info>Nothing changed.</info> Run it again with --apply.');
            $this->out('Nullable, so no existing ticket is altered and none is invented a date.');
            $this->out('Until a ticket has one, its cost is held back on the auto-generated');
            $this->out('journal page instead of being posted under today.');

            return null;
        }

        try {
            $connection->execute($sql);
        } catch (\Exception $e) {
            $this->abort('The ALTER failed: ' . $e->getMessage());
        }

        \Cake\Cache\Cache::clear(false, '_cake_model_');

        $this->out(sprintf('<success>%s added.</success>', self::COLUMN));
        $this->out('Clear the application cache so the web process sees it:');
        $this->out('  <info>rm -rf tmp/cache/models/*</info>');
        $this->out('');
        $this->out('Existing tickets have no purchase date. Fill them in on the ticket');
        $this->out('screen; until then their costs are listed as held back rather than');
        $this->out('posted under a date nobody chose.');

        return null;
    }
}
