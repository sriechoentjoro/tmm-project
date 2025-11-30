<?php
require __DIR__ . '/../vendor/autoload.php';

use Cake\Auth\DefaultPasswordHasher;

$hasher = new DefaultPasswordHasher();
$hash = $hasher->hash('password123');

$pdo = new PDO('mysql:host=localhost;dbname=system_authentication_authorization', 'root', '62xe6zyr');

// Update all users to use the correct hash for 'password123'
$sql = "UPDATE users SET password = :password";
$stmt = $pdo->prepare($sql);
$stmt->execute(['password' => $hash]);

$affected = $stmt->rowCount();
echo "Updated $affected users with password hash for 'password123'\n";
echo "New hash: $hash\n";

// Verify admin user
$stmt = $pdo->query("SELECT username, password FROM users WHERE username='admin'");
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $hasher->check('password123', $user['password'])) {
    echo "\n✓ Admin user password verified successfully!\n";
    echo "You can now login with:\n";
    echo "  Username: admin\n";
    echo "  Password: password123\n";
} else {
    echo "\n✗ Password verification failed\n";
}
