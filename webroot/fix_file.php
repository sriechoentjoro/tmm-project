<?php
/**
 * Browser-based file recreation script
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Recreating InventoriesTable.php</h1>";

$content = '<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class InventoriesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->setTable(\'inventories\');
        $this->setDisplayField(\'title\');
        $this->setPrimaryKey(\'id\');
        $this->addBehavior(\'Timestamp\');
        
        $this->belongsTo(\'Storages\', [
            \'foreignKey\' => \'storage_id\',
            \'joinType\' => \'LEFT\',
        ]);
        $this->belongsTo(\'Uoms\', [
            \'foreignKey\' => \'uom_id\',
            \'joinType\' => \'LEFT\',
        ]);
        $this->hasMany(\'AdjustStocks\', [\'foreignKey\' => \'inventory_id\']);
        $this->hasMany(\'InventoryImages\', [\'foreignKey\' => \'inventory_id\']);
        $this->hasMany(\'PurchaseReceiptItems\', [\'foreignKey\' => \'inventory_id\']);
        $this->hasMany(\'StockIncomings\', [\'foreignKey\' => \'inventory_id\']);
        $this->hasMany(\'StockOutgoings\', [\'foreignKey\' => \'inventory_id\']);
        $this->hasMany(\'StockTakes\', [\'foreignKey\' => \'inventory_id\']);
    }
    
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer(\'id\')->allowEmptyString(\'id\', null, \'create\')
            ->scalar(\'code\')->maxLength(\'code\', 50)->allowEmptyString(\'code\')
            ->scalar(\'title\')->maxLength(\'title\', 255)->allowEmptyString(\'title\')
            ->scalar(\'rack\')->maxLength(\'rack\', 50)->allowEmptyString(\'rack\')
            ->scalar(\'rack_cell\')->maxLength(\'rack_cell\', 50)->allowEmptyString(\'rack_cell\')
            ->scalar(\'maker\')->maxLength(\'maker\', 100)->allowEmptyString(\'maker\')
            ->decimal(\'qty\')->allowEmptyString(\'qty\')
            ->decimal(\'stock_minimum\')->allowEmptyString(\'stock_minimum\')
            ->scalar(\'image_url\')->maxLength(\'image_url\', 500)->allowEmptyFile(\'image_url\');
        return $validator;
    }
    
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn([\'storage_id\'], \'Storages\'));
        $rules->add($rules->existsIn([\'uom_id\'], \'Uoms\'));
        return $rules;
    }
}
';

$filePath = dirname(__DIR__) . '/src/Model/Table/InventoriesTable.php';

echo "<p>Target file: <code>$filePath</code></p>";
echo "<p>File exists before: " . (file_exists($filePath) ? 'YES' : 'NO') . "</p>";

if (file_exists($filePath)) {
    $oldSize = filesize($filePath);
    echo "<p>Old file size: $oldSize bytes</p>";
    
    if (unlink($filePath)) {
        echo "<p style='color:green;'>✓ Old file deleted</p>";
    } else {
        echo "<p style='color:red;'>✗ Failed to delete old file</p>";
    }
}

// Wait a moment
usleep(100000); // 0.1 seconds

// Write new file
$result = file_put_contents($filePath, $content);

if ($result !== false) {
    echo "<p style='color:green; font-size:20px;'>✓ ✓ ✓ FILE CREATED SUCCESSFULLY!</p>";
    echo "<p>Bytes written: $result</p>";
    
    if (file_exists($filePath)) {
        $newSize = filesize($filePath);
        echo "<p>New file size: <strong>$newSize bytes</strong></p>";
        
        if ($newSize > 0) {
            echo "<p style='color:green;'>✓ File has content!</p>";
            
            // Test syntax
            $output = [];
            $return = 0;
            exec("php -l \"$filePath\" 2>&1", $output, $return);
            
            if ($return === 0) {
                echo "<p style='color:green;'>✓ Syntax check PASSED</p>";
            } else {
                echo "<p style='color:red;'>✗ Syntax error:</p>";
                echo "<pre>" . implode("\n", $output) . "</pre>";
            }
            
            echo "<hr>";
            echo "<h2>Next Steps:</h2>";
            echo "<ol>";
            echo "<li>Run: <code>composer dump-autoload</code> in terminal</li>";
            echo "<li>Restart Apache in XAMPP</li>";
            echo "<li>Visit: <a href='/asahi_v3/parse_test.php'>/asahi_v3/parse_test.php</a> to verify</li>";
            echo "<li>Visit: <a href='/asahi_v3/inventories'>/asahi_v3/inventories</a> to test</li>";
            echo "</ol>";
            
        } else {
            echo "<p style='color:red;'>✗ File is still EMPTY (0 bytes)!</p>";
            echo "<p>This suggests a file system or permissions issue.</p>";
        }
    } else {
        echo "<p style='color:red;'>✗ File does not exist after write!</p>";
    }
} else {
    echo "<p style='color:red;'>✗ file_put_contents() FAILED!</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

echo "<hr>";
echo "<h2>Directory Permissions Check</h2>";
$dir = dirname($filePath);
echo "<p>Directory: $dir</p>";
echo "<p>Directory exists: " . (is_dir($dir) ? 'YES' : 'NO') . "</p>";
echo "<p>Directory writable: " . (is_writable($dir) ? 'YES' : 'NO') . "</p>";
