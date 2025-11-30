<?php
/**
 * Generate Password Hashes for Test Users
 * Run: php generate_test_passwords.php
 */

require 'vendor/autoload.php';

use Cake\Auth\DefaultPasswordHasher;

$hasher = new DefaultPasswordHasher();

$passwords = [
    'admin123' => 'Admin password',
    'director123' => 'Director password',
    'manager123' => 'Manager password',
    'recruit123' => 'Recruitment password',
    'training123' => 'Training password',
    'doc123' => 'Documentation password',
    'lpk123' => 'LPK password'
];

echo "=== Test Password Hashes for TMM System ===\n\n";

foreach ($passwords as $plaintext => $description) {
    $hash = $hasher->hash($plaintext);
    echo "{$description}:\n";
    echo "Plaintext: {$plaintext}\n";
    echo "Hash: {$hash}\n\n";
}

echo "\n=== SQL Update Statements ===\n\n";

echo "USE system_authentication_authorization;\n\n";

echo "-- Admin (password: admin123)\n";
echo "UPDATE users SET password = '" . $hasher->hash('admin123') . "' WHERE username = 'admin';\n\n";

echo "-- Director (password: director123)\n";
echo "UPDATE users SET password = '" . $hasher->hash('director123') . "' WHERE username = 'director';\n\n";

echo "-- Manager (password: manager123)\n";
echo "UPDATE users SET password = '" . $hasher->hash('manager123') . "' WHERE username = 'manager';\n\n";

echo "-- Recruitment Officers (password: recruit123)\n";
echo "UPDATE users SET password = '" . $hasher->hash('recruit123') . "' WHERE username IN ('recruitment1', 'recruitment2');\n\n";

echo "-- Training Coordinators (password: training123)\n";
echo "UPDATE users SET password = '" . $hasher->hash('training123') . "' WHERE username IN ('training1', 'training2');\n\n";

echo "-- Documentation Officers (password: doc123)\n";
echo "UPDATE users SET password = '" . $hasher->hash('doc123') . "' WHERE username IN ('documentation1', 'documentation2');\n\n";

echo "-- LPK Users (password: lpk123)\n";
echo "UPDATE users SET password = '" . $hasher->hash('lpk123') . "' WHERE username IN ('lpk_semarang', 'lpk_makasar', 'lpk_medan', 'lpk_padang', 'lpk_bekasi');\n\n";

echo "=== Done! ===\n";
