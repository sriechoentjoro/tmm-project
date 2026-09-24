<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * Copy email templates written into the wrong database across to the live one.
 *
 * email_templates exists in two databases. The application only ever reads the
 * one EmailTemplatesTable points at; a template written into the other is
 * never found. Nothing says so at the time, either: EmailComponent asks
 * getTemplate() for the key, gets nothing back, writes a line to the error log
 * and returns false. The email simply does not arrive, and the person who
 * wrote the template has no reason to suspect their work went to a table
 * nobody reads.
 *
 * So this is not really about a duplicate table. It is about finding out
 * whether any template is stranded, and moving it if so - before the stray
 * copy is set aside and the question becomes unanswerable.
 *
 * Matched on template_key, never on id: the two tables number their rows
 * independently, and an id that exists in both almost certainly means two
 * different templates.
 *
 * A key already present in the destination is left alone. The live template is
 * the one in use, and overwriting it with an older draft from a table nobody
 * reads would be the opposite of a fix. Those rows are reported so the
 * difference can be looked at by hand if it matters.
 *
 * Report only unless --apply. The source table is never modified.
 *
 * Usage:
 *     bin/cake move_stuck_email_templates
 *     bin/cake move_stuck_email_templates --apply
 */
class MoveStuckEmailTemplatesShell extends Shell
{
    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Copy email templates out of the database nothing reads.')
            ->addOption('from', [
                'help' => 'Connection holding the stranded copy.',
                'default' => 'default',
            ])
            ->addOption('apply', [
                'help' => 'Write the missing templates. Without it they are only reported.',
                'boolean' => true,
            ]);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $from = trim((string)$this->param('from'));
        $apply = (bool)$this->param('apply');

        $templates = TableRegistry::getTableLocator()->get('EmailTemplates');
        $to = $templates->getConnection()->configName();

        if ($from === $to) {
            $this->abort(sprintf('--from is %s, which is the connection the application '
                . 'already reads. There is nothing to move.', $to));
        }

        $this->out('');
        $this->out(sprintf('from <info>%s</info> (read by nothing) to <info>%s</info> (read by the application)',
            $from, $to));

        try {
            $sourceConnection = ConnectionManager::get($from);
            $sourceColumns = $sourceConnection->getSchemaCollection()->describe('email_templates')->columns();
            $stranded = $sourceConnection->execute(
                'SELECT * FROM `email_templates` ORDER BY id ASC')->fetchAll('assoc');
        } catch (\Exception $e) {
            $this->abort('Could not read email_templates on ' . $from . ': ' . $e->getMessage());
        }

        if (!$stranded) {
            $this->out('');
            $this->out('<success>Nothing there.</success> No template is stranded.');

            return null;
        }

        $live = [];
        foreach ($templates->find()->enableHydration(false) as $row) {
            $live[(string)$row['template_key']] = $row;
        }

        // Only what both tables have room for, so a column one side gained
        // later does not stop the copy.
        $targetColumns = $templates->getSchema()->columns();
        $carry = array_values(array_intersect(
            array_intersect($sourceColumns, $targetColumns),
            array_keys($templates->newEntity()->getAccessible() + ['template_key' => true])
        ));
        $dropped = array_diff($sourceColumns, $carry, ['id']);
        if ($dropped) {
            $this->out(sprintf('  columns not carried over: %s', implode(', ', $dropped)));
        }

        $this->out('');
        $moved = 0;
        $present = 0;
        foreach ($stranded as $row) {
            $key = (string)($row['template_key'] ?? '');
            if ($key === '') {
                $this->out(sprintf('  <warning>skipped</warning>  #%s has no template_key', $row['id']));
                continue;
            }

            if (isset($live[$key])) {
                $this->out(sprintf('  <success>already live</success>  %-28s (this copy is left alone)', $key));
                $present++;
                continue;
            }

            $this->out(sprintf('  %s  <info>%-28s</info> %s',
                $apply ? 'moving     ' : 'would move ',
                $key,
                mb_substr((string)($row['subject'] ?? ''), 0, 44)));

            if (!$apply) {
                continue;
            }

            $values = [];
            foreach ($carry as $column) {
                $values[$column] = $row[$column];
            }
            // Never the id: the two tables number themselves independently.
            unset($values['id']);

            $entity = $templates->newEntity($values, ['validate' => false]);
            foreach ($values as $column => $value) {
                $entity->set($column, $value);
            }
            if ($templates->save($entity, ['checkRules' => false, 'validate' => false])) {
                $moved++;
            } else {
                $this->out(sprintf('    <warning>could not be saved: %s</warning>',
                    json_encode($entity->getErrors())));
            }
        }

        $this->out('');
        if (!$apply) {
            $this->out('<info>Nothing changed.</info> Run it again with --apply to move them.');
            $this->out('The stranded table is only read, never altered - so the copies stay');
            $this->out('where they are and can be compared afterwards.');

            return null;
        }

        $this->out(sprintf('<success>%d template(s) moved, %d already live.</success>', $moved, $present));
        if ($moved) {
            $this->out('Check them on the Email Templates screen before relying on them:');
            $this->out('  a template written into a table nobody reads was never rendered,');
            $this->out('  so its variables have never been exercised.');
        }

        return null;
    }
}
