<?php
require __DIR__ . '/../vendor/autoload.php';

use Cake\Auth\DefaultPasswordHasher;

$hasher = new DefaultPasswordHasher();
$password = 'password123';
$hash = $hasher->hash($password);

echo "UPDATE users SET password = '$hash' WHERE username = 'admin';";
