<?php
/**
 * Assigns menus to roles in role_menus table.
 * Run: php assign_role_menus.php
 */

$pdo = new PDO(
    'mysql:host=127.0.0.1;port=3306;dbname=cms_authentication_authorization;charset=utf8mb4',
    'tmm_user', '62xe6zyr',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Roles:
// 1 = administrator, 2 = management, 3 = tmm-recruitment,
// 4 = tmm-training, 5 = tmm-documentation, 6 = lpk-penyangga, 7 = tmm-accounting

// Menu IDs grouped by area (from cms_masters.menus):
$DASHBOARDS   = [1, 2, 3, 4, 5];              // Dashboard + sub-dashboards
$RECRUIT_CAND = [23, 24, 25, 26, 27, 28, 29, 30]; // Candidate Management + all children
$RECRUIT_PROM = [36, 37, 38];                  // Candidate Promotion (TMM only)
$RECRUIT_ORD  = [31, 32, 33, 34, 35];          // Apprentice Orders
$TRAINING     = [39, 40, 41, 42, 43,           // Training Batches
                 44, 45, 46,                    // Additional Training
                 47, 48, 49, 50,                // Training Scoring
                 51, 52, 53, 54,                // Certificates & Cards
                 55, 56, 57, 58];               // Trainee Promotions
$DOCS         = [63, 64, 65, 66,               // Trainee Submissions
                 67, 68, 69, 70,               // Departure Docs
                 71, 72, 73, 74, 75,           // Ticketing & Transit
                 76, 77, 78, 79, 80, 81, 82, 83, 84, 85]; // TMM Documentation
$APPRENTICE   = [59, 60, 61, 62];              // Apprentice in Japan
$ACCOUNTING   = [86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98]; // Accounting
$REPORTS      = [99, 100, 101, 102, 103];      // Reports & Analytics
// Stakeholder management: ONLY administrator and tmm-recruitment have access
// Menu 10 (/admin/lpk-registration/create) is admin-only, excluded from recruitment
$STAKEHOLDER_ADMIN      = [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22]; // all
$STAKEHOLDER_RECRUIT    = [6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22];     // excl 10
$SYSTEM       = [104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114]; // System/Admin

// Menu 27 = Candidate Physical Test   → LPK + TMM Recruitment + Admin
// Menu 29 = AO Interview Scoring      → LPK + TMM Recruitment + Admin
// Menu 37 = Promote to Trainee        → TMM Recruitment + Admin
// Menu 38 = Promotion History         → TMM Recruitment + Admin

// Role menu assignments: role_id => [menu_ids...]
$assignments = [
    // 1: Administrator — everything
    1 => array_unique(array_merge(
        $DASHBOARDS, $RECRUIT_CAND, $RECRUIT_PROM, $RECRUIT_ORD,
        $TRAINING, $DOCS, $APPRENTICE, $ACCOUNTING, $REPORTS,
        $STAKEHOLDER_ADMIN, $SYSTEM
    )),

    // 2: Management/Director — dashboards + reports + read-only operational views
    //    NO stakeholder management access
    2 => array_unique(array_merge(
        $DASHBOARDS, $REPORTS,
        [23, 25],       // Candidate Management parent + list (read)
        [39, 41],       // Training Batches parent + manage (read)
        [59],           // Apprentice Management parent (read)
    )),

    // 3: TMM Recruitment — full candidate management + full stakeholder management
    3 => array_unique(array_merge(
        $DASHBOARDS,
        $RECRUIT_CAND,          // Full candidate management incl. physical test & interview
        $RECRUIT_PROM,          // Candidate Promotion menus
        $RECRUIT_ORD,
        $STAKEHOLDER_RECRUIT,   // Full stakeholder CRUD (LPK, AO, Cooperative, SSSI)
        $REPORTS,
    )),

    // 4: TMM Training — training modules only, no stakeholder management
    4 => array_unique(array_merge(
        $DASHBOARDS,
        $TRAINING,
        [23, 25],        // Candidate list (read)
        $REPORTS,
    )),

    // 5: TMM Documentation — documentation + apprentice, no stakeholder management
    5 => array_unique(array_merge(
        $DASHBOARDS,
        $DOCS,
        $APPRENTICE,
        [23, 25],        // Candidate list (read)
        $REPORTS,
    )),

    // 6: LPK Penyangga — candidate ops only (physical test + AO interview scoring)
    //    NO stakeholder management pages
    6 => array_unique(array_merge(
        [1],                            // Main dashboard
        [23, 24, 25, 26, 27, 28, 29],  // Candidate Management + scoring menus
    )),

    // 7: TMM Accounting — accounting + dashboard + financial reports
    7 => array_unique(array_merge(
        $DASHBOARDS,
        $ACCOUNTING,
        [99, 100],       // Reports (limited)
    )),
];

// Clear existing and re-insert
$pdo->exec('DELETE FROM role_menus WHERE 1=1');
echo "Cleared existing role_menus.\n";

$insert = $pdo->prepare(
    'INSERT INTO role_menus (role_id, menu_id, is_active, created, modified)
     VALUES (?, ?, 1, NOW(), NOW())'
);

$roleNames = [
    1 => 'administrator', 2 => 'management', 3 => 'tmm-recruitment',
    4 => 'tmm-training', 5 => 'tmm-documentation', 6 => 'lpk-penyangga',
    7 => 'tmm-accounting',
];

foreach ($assignments as $roleId => $menuIds) {
    sort($menuIds);
    foreach ($menuIds as $menuId) {
        $insert->execute([$roleId, $menuId]);
    }
    echo sprintf("Role %d (%s): assigned %d menus — [%s]\n",
        $roleId, $roleNames[$roleId], count($menuIds), implode(',', $menuIds));
}

echo "\nDone.\n";
