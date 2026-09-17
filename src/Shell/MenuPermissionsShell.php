<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;

/**
 * Show which actions each role is actually granted on a controller.
 *
 * Reads only. Nothing here writes.
 *
 * Worth having because the answer is not what the /menus page appears to say.
 * The per-role cell there holds role_menus.granted_actions, and "*" reads like
 * "every action on this controller". It is not:
 * AppController::getMenuRolePermissions() treats "*" (and an empty value) as
 * the menu's own action plus index and view - deliberately, so that granting
 * one report does not hand over every other report on the same controller. Only
 * an explicit comma-separated list grants anything beyond that, so add, edit and
 * delete reach a non-administrator only when somebody types them in.
 *
 * The second surprise is the container rule: a menu that has an active child
 * menu assigned to the same role is skipped entirely, because it is a
 * navigation tab whose URL is just the landing page. Setting granted_actions on
 * such a parent has no effect at all - the value has to go on a child row.
 *
 * This shell applies both rules and prints the resulting action list, so the
 * question "can tmm-accounting press Edit on a journal?" has an answer from the
 * database rather than from reading the code.
 *
 * Usage:
 *
 *   bin/cake menu_permissions              # Journals
 *   bin/cake menu_permissions Reports      # any controller
 *   bin/cake menu_permissions --all        # every controller, one line each
 */
class MenuPermissionsShell extends Shell
{
    /** Where role_menus and roles live. */
    const AUTH_DB = 'cms_authentication_authorization';

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->setDescription('Show the actions each role is granted on a controller, as the permission check computes them.');
        $parser->addArgument('controller', [
            'help' => 'Controller name, e.g. Journals. Defaults to Journals.',
            'required' => false,
        ]);
        $parser->addOption('all', [
            'help' => 'Summarise every controller instead of detailing one.',
            'boolean' => true,
        ]);

        return $parser;
    }

    public function main($controller = null)
    {
        $rows = $this->rows();
        if ($rows === null) {
            return;
        }

        if ($this->param('all')) {
            $this->summary($rows);

            return;
        }

        $this->detail($rows, $controller ?: 'Journals');
    }

    /**
     * Every active role_menus row, resolved to a controller and action.
     *
     * @return array|null Null when the tables cannot be read.
     */
    protected function rows()
    {
        try {
            $raw = ConnectionManager::get(self::AUTH_DB)->execute(
                'SELECT r.name AS role, m.id AS menu_id, m.parent_id, m.title, m.url,
                        m.controller, m.action, rm.granted_actions,
                        (SELECT COUNT(*)
                           FROM role_menus rm2
                           JOIN cms_masters.menus m2 ON m2.id = rm2.menu_id
                          WHERE m2.parent_id = m.id
                            AND rm2.role_id = rm.role_id
                            AND rm2.is_active = 1
                            AND m2.is_active = 1) AS active_children
                   FROM role_menus rm
                   JOIN cms_masters.menus m ON m.id = rm.menu_id
                   JOIN roles r ON r.id = rm.role_id
                  WHERE rm.is_active = 1
                    AND m.is_active = 1
                  ORDER BY r.name, m.id'
            )->fetchAll('assoc');
        } catch (\Throwable $e) {
            $this->err('<error>Could not read the menu tables:</error> ' . $e->getMessage());
            $this->out('Run this on a machine that can reach the database.');

            return null;
        }

        $out = [];
        foreach ($raw as $row) {
            $resolved = $this->resolve($row);
            if (!$resolved) {
                continue;
            }
            list($row['resolved_controller'], $row['resolved_action']) = $resolved;
            $out[] = $row;
        }

        return $out;
    }

    /**
     * The controller and action a menu row points at.
     *
     * Same rules as AppController::parseMenuUrl().
     *
     * @param array $row Menu row.
     * @return array|null
     */
    protected function resolve(array $row)
    {
        if (!empty($row['controller'])) {
            return [$row['controller'], $row['action'] ?: 'index'];
        }

        $url = (string)$row['url'];
        if ($url === '' || $url === '#' || strpos($url, 'http') === 0 || strpos($url, 'javascript:') === 0) {
            return null;
        }
        $parts = array_values(array_filter(explode('/', trim($url, '/'))));
        if (!empty($parts) && in_array($parts[0], ['admin', 'tmm'], true)) {
            array_shift($parts);
        }
        if (empty($parts)) {
            return null;
        }

        return [
            \Cake\Utility\Inflector::camelize(str_replace('-', '_', $parts[0])),
            isset($parts[1]) ? \Cake\Utility\Inflector::variable(str_replace('-', '_', $parts[1])) : 'index',
        ];
    }

    /**
     * What one menu row contributes, after the "*" rule.
     *
     * @param array $row Menu row.
     * @return array Action names.
     */
    protected function contributed(array $row)
    {
        $granted = trim((string)$row['granted_actions']);
        if ($granted !== '' && $granted !== '*') {
            return array_map('trim', explode(',', $granted));
        }

        return array_unique([$row['resolved_action'], 'index', 'view']);
    }

    /**
     * One controller in full: every row that feeds it, per role.
     *
     * @param array $rows Resolved menu rows.
     * @param string $controller Controller name.
     * @return void
     */
    protected function detail(array $rows, $controller)
    {
        $mine = array_filter($rows, function ($row) use ($controller) {
            return strcasecmp($row['resolved_controller'], $controller) === 0;
        });

        $this->out('');
        $this->out(sprintf('<info>%s</info>', $controller));
        $this->out(str_repeat('=', 78));

        if (!$mine) {
            $this->out('No active menu points at this controller, so no role reaches it.');
            $this->out('Only an administrator can open its pages.');
            $this->out('');

            return;
        }

        $byRole = [];
        foreach ($mine as $row) {
            $byRole[$row['role']][] = $row;
        }
        ksort($byRole);

        foreach ($byRole as $role => $roleRows) {
            $this->out('');
            $this->out(sprintf('  <info>%s</info>', $role));

            $effective = [];
            foreach ($roleRows as $row) {
                $isContainer = (int)$row['active_children'] > 0;
                $contributed = $this->contributed($row);

                $this->out(sprintf(
                    '    menu %-4s %-28s %-26s granted_actions=%s',
                    '#' . $row['menu_id'],
                    $this->clip($row['title'], 28),
                    $this->clip($row['url'], 26),
                    $row['granted_actions'] === null || $row['granted_actions'] === '' ? '(empty)' : $row['granted_actions']
                ));

                if ($isContainer) {
                    $this->out('             <warning>skipped: navigation container with active children - its granted_actions is ignored</warning>');
                    continue;
                }

                $this->out('             grants: ' . implode(', ', $contributed));
                $effective = array_merge($effective, $contributed);
            }

            $effective = array_values(array_unique($effective));
            sort($effective);
            $this->out('    => ' . ($effective ? implode(', ', $effective) : '(nothing)'));

            foreach (['add', 'edit', 'delete'] as $action) {
                if (!in_array($action, $effective, true)) {
                    $this->out(sprintf('       <warning>%s is NOT granted</warning>', $action));
                }
            }
        }

        $this->out('');
        $this->out('An administrator bypasses all of this and can reach every action.');
        $this->out('');
    }

    /**
     * Every controller, one line each.
     *
     * @param array $rows Resolved menu rows.
     * @return void
     */
    protected function summary(array $rows)
    {
        $byController = [];
        foreach ($rows as $row) {
            if ((int)$row['active_children'] > 0) {
                continue;
            }
            foreach ($this->contributed($row) as $action) {
                $byController[$row['resolved_controller']][$row['role']][$action] = true;
            }
        }
        ksort($byController);

        $this->out('');
        foreach ($byController as $controller => $roles) {
            $this->out(sprintf('<info>%s</info>', $controller));
            ksort($roles);
            foreach ($roles as $role => $actions) {
                $names = array_keys($actions);
                sort($names);
                $this->out(sprintf('   %-20s %s', $role, implode(', ', $names)));
            }
        }
        $this->out('');
    }

    /**
     * Cut a value to a column width. Counts characters, not bytes, so a title
     * with non-ASCII in it does not push the column out.
     *
     * @param string|null $value Value.
     * @param int $width Column width.
     * @return string
     */
    protected function clip($value, $width)
    {
        $value = (string)$value;

        return mb_strlen($value) > $width ? mb_substr($value, 0, $width - 1) . '…' : $value;
    }
}
