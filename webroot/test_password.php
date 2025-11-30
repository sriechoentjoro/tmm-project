<?php
require __DIR__ . '/../vendor/autoload.php';

use Cake\Auth\DefaultPasswordHasher;

$hasher = new DefaultPasswordHasher();
$password = 'password123';
$hash = $hasher->hash($password);

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "\n";

// Test verification
$testHash = '$2y$10$abcdefghijklmnopqrstuuVHjdqHHgsv6MQs8c7lnYaW5Cd0NhKK';
if ($hasher->check($password, $testHash)) {
    echo "Verification: OK\n";
} else {
    echo "Verification: FAILED\n";
}

// Check what's in DB
$pdo = new PDO('mysql:host=localhost;dbname=system_authentication_authorization', 'root', '62xe6zyr');
$stmt = $pdo->query("SELECT username, password FROM users WHERE username='admin'");
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "\nUser found in DB:\n";
    echo "Username: {$user['username']}\n";
    echo "Password hash: {$user['password']}\n";
    
    // Test if password matches
    if ($hasher->check('password123', $user['password'])) {
        echo "Password 'password123' matches: YES\n";
    } else {
        echo "Password 'password123' matches: NO\n";
    }
} else {
    echo "\nUser 'admin' NOT FOUND in database\n";
}
