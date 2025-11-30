# Update apprentices sample data via PHP/CakePHP ORM
<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/bootstrap.php';

use Cake\ORM\TableRegistry;

echo "Updating apprentices with sample apprentice_order_id values...\n\n";

$apprenticesTable = TableRegistry::getTableLocator()->get('Apprentices');

// Update batches
$updates = [
    ['ids' => range(1, 10), 'order_id' => 1],
    ['ids' => range(11, 20), 'order_id' => 2],
    ['ids' => range(21, 30), 'order_id' => 3],
    ['ids' => range(31, 40), 'order_id' => 4],
    ['ids' => range(41, 50), 'order_id' => 5],
];

$totalUpdated = 0;

foreach ($updates as $batch) {
    $result = $apprenticesTable->updateAll(
        ['apprentice_order_id' => $batch['order_id']],
        ['id IN' => $batch['ids']]
    );
    
    echo "Updated {$result} apprentices to apprentice_order_id = {$batch['order_id']}\n";
    $totalUpdated += $result;
}

echo "\nTotal updated: {$totalUpdated}\n\n";

// Verify
echo "Verification:\n";
$counts = $apprenticesTable->find()
    ->select([
        'apprentice_order_id',
        'count' => $apprenticesTable->find()->func()->count('*')
    ])
    ->where(['apprentice_order_id IS NOT' => null])
    ->group('apprentice_order_id')
    ->order(['apprentice_order_id' => 'ASC'])
    ->toArray();

foreach ($counts as $count) {
    echo "apprentice_order_id {$count->apprentice_order_id}: {$count->count} records\n";
}

echo "\nDone!\n";
