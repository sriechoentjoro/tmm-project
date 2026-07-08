<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Audit Controller
 *
 * Landing/guide page: shows each role their accessible management features,
 * organised by category, so they know exactly what to check and manage.
 */
class AuditController extends AppController
{
    public function index()
    {
        $isAdmin     = $this->hasRole('administrator');
        $roleNames   = isset($this->currentUser['role_names'])
                       ? (array) $this->currentUser['role_names']
                       : [];
        $displayName = isset($this->currentUser['username'])
                       ? $this->currentUser['username']
                       : 'User';

        // Fetch all active menus with their children
        $conn = \Cake\Datasource\ConnectionManager::get('cms_masters');
        $rows = $conn->execute(
            'SELECT id, title, url, controller, `action`, icon, category,
                    parent_id, sort_order
             FROM menus
             WHERE is_active = 1
             ORDER BY category, sort_order, id'
        )->fetchAll('assoc');

        // Build parent→children map
        $parents  = [];
        $children = [];
        foreach ($rows as $r) {
            if ($r['parent_id'] === null) {
                $parents[$r['id']] = $r;
            } else {
                $children[(int)$r['parent_id']][] = $r;
            }
        }

        // Get the role's directly-assigned leaf menu IDs (same logic as getAllowedMenuIds)
        $allowedIds = $isAdmin ? null : $this->getAllowedMenuIds();

        // Build guide: category → list of feature items
        $guide = [];
        foreach ($parents as $pid => $parent) {
            $kids = isset($children[$pid]) ? $children[$pid] : [];

            if (!$isAdmin) {
                // Keep only children explicitly assigned to this role
                $kids = array_values(array_filter($kids, function($k) use ($allowedIds) {
                    return in_array((int)$k['id'], $allowedIds);
                }));
                // Skip parent if neither it nor any child is assigned
                if (empty($kids) && !in_array((int)$pid, $allowedIds)) {
                    continue;
                }
            }

            $cat = ($parent['category'] !== null && $parent['category'] !== '')
                   ? $parent['category']
                   : 'Other';

            // System menus (Audit, Menu Management) are infrastructure tools,
            // not core role features — exclude them from the guide.
            if ($cat === 'System') {
                continue;
            }

            // Each feature item: use child leaves, or the parent itself if leaf
            if (!empty($kids)) {
                foreach ($kids as $k) {
                    if ($k['url'] && $k['url'] !== '#') {
                        $guide[$cat][] = [
                            'title'  => $k['title'],
                            'url'    => $k['url'],
                            'icon'   => $k['icon'] ?: 'fa-circle',
                            'parent' => $parent['title'],
                        ];
                    }
                }
            } else {
                // Leaf parent with no children assigned
                if ($parent['url'] && $parent['url'] !== '#') {
                    $guide[$cat][] = [
                        'title'  => $parent['title'],
                        'url'    => $parent['url'],
                        'icon'   => $parent['icon'] ?: 'fa-circle',
                        'parent' => null,
                    ];
                }
            }
        }

        // Sort categories: Training first, then alphabetical, System last
        uksort($guide, function($a, $b) {
            $priority = ['Training' => 0, 'Recruitment' => 1, 'Reports' => 2, 'System' => 99];
            $pa = isset($priority[$a]) ? $priority[$a] : 50;
            $pb = isset($priority[$b]) ? $priority[$b] : 50;
            return $pa !== $pb ? $pa - $pb : strcmp($a, $b);
        });

        $this->set(compact('guide', 'roleNames', 'displayName'));
    }

    public function view($id = null)
    {
        // Redirect to index — individual log view no longer used
        return $this->redirect(['action' => 'index']);
    }
}
