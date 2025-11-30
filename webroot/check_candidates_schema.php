<?php
require dirname(__DIR__) . '/config/bootstrap.php';

use Cake\Datasource\ConnectionManager;

$connection = ConnectionManager::get('cms_lpk_candidates');
$schema = $connection->getSchemaCollection();
$tableSchema = $schema->describe('candidates');

echo "<h2>Candidates Table Fields:</h2>";
echo "<pre>";
$columns = $tableSchema->columns();
foreach ($columns as $column) {
    if (stripos($column, 'propinsi') !== false || stripos($column, 'kabupaten') !== false || 
        stripos($column, 'kecamatan') !== false || stripos($column, 'kelurahan') !== false) {
        echo "- <strong>" . $column . "</strong> (type: " . $tableSchema->getColumnType($column) . ")\n";
    }
}
echo "</pre>";

echo "<h3>All location-related fields:</h3>";
echo "<ul>";
foreach ($columns as $column) {
    if (stripos($column, 'propinsi') !== false || stripos($column, 'kabupaten') !== false || 
        stripos($column, 'kecamatan') !== false || stripos($column, 'kelurahan') !== false ||
        stripos($column, 'address') !== false || stripos($column, 'province') !== false) {
        echo "<li>" . $column . "</li>";
    }
}
echo "</ul>";
