<?php
// Test script to check apprentices data
require dirname(__DIR__) . '/vendor/autoload.php';

use Cake\Datasource\ConnectionManager;

// Load configuration
require dirname(__DIR__) . '/config/bootstrap.php';

// Get apprentices table
$apprenticesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Apprentices');

echo "<h2>Apprentices Table Test</h2>";

// Count total
$total = $apprenticesTable->find()->count();
echo "<p>Total apprentices: $total</p>";

// Get first 5 records
$apprentices = $apprenticesTable->find()
    ->limit(5)
    ->toArray();

echo "<h3>Sample Records:</h3>";
echo "<pre>";
foreach ($apprentices as $apprentice) {
    echo "ID: {$apprentice->id}\n";
    echo "Name: {$apprentice->name}\n";
    echo "apprentice_order_id: " . (isset($apprentice->apprentice_order_id) ? $apprentice->apprentice_order_id : 'NULL') . "\n";
    echo "---\n";
}
echo "</pre>";

// Count by apprentice_order_id
echo "<h3>Count by apprentice_order_id:</h3>";
$counts = $apprenticesTable->find()
    ->select(['apprentice_order_id', 'count' => $apprenticesTable->find()->func()->count('*')])
    ->where(['apprentice_order_id IS NOT' => null])
    ->group('apprentice_order_id')
    ->toArray();

echo "<pre>";
foreach ($counts as $count) {
    echo "apprentice_order_id: {$count->apprentice_order_id} => count: {$count->count}\n";
}
echo "</pre>";
