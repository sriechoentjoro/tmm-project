<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * Find role menu assignments that point at a menu which is gone.
 *
 * The menus live in cms_masters and the assignments in
 * cms_authentication_authorization. Two databases means no foreign key can
 * hold them together, so deleting a menu leaves its assignments behind,
 * pointing at an id that no longer exists.
 *
 * Nothing breaks visibly when that happens - the permission query joins the
 * two and an assignment whose menu is missing simply grants nothing. That is
 * the problem: the permission grid still shows the role as having something,
 * and an administrator reading it back is told a story about access that is
 * no longer true.
 *
 * Report only unless --apply is given, and then only the orphans are deleted:
 * an assignment whose menu still exists is never touched, switched on or off.
 *
 * Usage:
 *     bin/cake check_role_menus             report only
 *     bin/cake check_role_menus --apply     delete the orphaned assignments
 */
class CheckRoleMenusShell extends Shell
{
    const AUTH_DB = 'cms_authentication_authorization';
    const MENU_DB = 'cms_masters';

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Find role menu assignments whose menu no longer exists.')
            ->addOption('apply', [
                'help' => 'Delete the orphaned assignments. Without it they are only reported.',
                'boolean' => true,
            ]);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $apply = (bool)$this->param('apply');

        try {
            $assignments = ConnectionManager::get(self::AUTH_DB)->execute(
                'SELECT rm.id, rm.role_id, rm.menu_id, rm.is_active, r.name AS role_name
                 FROM role_menus rm
                 LEFT JOIN roles r ON r.id = rm.role_id
                 ORDER BY rm.role_id, rm.menu_id'
            )->fetchAll('assoc');
        } catch (\Exception $e) {
            $this->abort('Could not read role_menus: ' . $e->getMessage());
        }

        try {
            $menuRows = ConnectionManager::get(self::MENU_DB)
                ->execute('SELECT id, title FROM menus')
                ->fetchAll('assoc');
        } catch (\Exception $e) {
            $this->abort('Could not read menus: ' . $e->getMessage());
        }

        $menus = [];
        foreach ($menuRows as $row) {
            $menus[(int)$row['id']] = $row['title'];
        }

        $this->out('');
        $this->out(sprintf('<info>%d assignment(s)</info> against <info>%d menu(s)</info>',
            count($assignments), count($menus)));

        $orphans = [];
        $orphanRoles = [];
        foreach ($assignments as $row) {
            if (isset($menus[(int)$row['menu_id']])) {
                continue;
            }
            $orphans[] = $row;
            $label = $row['role_name'] ?: ('role #' . $row['role_id']);
            $orphanRoles[$label] = ($orphanRoles[$label] ?? 0) + 1;
        }

        // An assignment whose ROLE is gone is a second kind of orphan, and
        // worth naming separately: it belongs to nobody rather than pointing
        // at nothing.
        $rolelessCount = 0;
        foreach ($assignments as $row) {
            if ($row['role_name'] === null) {
                $rolelessCount++;
            }
        }

        if ($rolelessCount) {
            $this->out(sprintf('  <warning>%d assignment(s) belong to a role that no longer exists</warning>', $rolelessCount));
        }

        if (!$orphans) {
            $this->out('  <success>every assignment points at a menu that exists</success>');

            return null;
        }

        $this->out('');
        $this->out(sprintf('<warning>%d assignment(s) point at a menu that is gone</warning>', count($orphans)));
        foreach ($orphanRoles as $role => $count) {
            $this->out(sprintf('  %-28s %d', $role, $count));
        }

        $this->out('');
        $this->out('Missing menu ids: ' . implode(', ', array_unique(array_map(function ($row) {
            return $row['menu_id'];
        }, $orphans))));

        if (!$apply) {
            $this->out('');
            $this->out('<info>Nothing changed.</info> Run it again with --apply to delete them.');
            $this->out('They grant nothing as they are; deleting them only stops the');
            $this->out('permission grid reporting access that does not exist.');

            return null;
        }

        $ids = array_map(function ($row) {
            return (int)$row['id'];
        }, $orphans);

        try {
            ConnectionManager::get(self::AUTH_DB)->execute(
                'DELETE FROM role_menus WHERE id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')',
                $ids
            );
        } catch (\Exception $e) {
            $this->abort('Could not delete: ' . $e->getMessage());
        }

        $this->out('');
        $this->out(sprintf('<success>%d orphaned assignment(s) deleted.</success>', count($ids)));
        $this->out('Clear the application cache so the permission list is rebuilt:');
        $this->out('  <info>rm -rf tmp/cache/models/*</info>');

        return null;
    }
}
