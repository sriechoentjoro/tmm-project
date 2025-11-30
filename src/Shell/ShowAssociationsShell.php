<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Inflector;

/**
 * Show Associations Shell
 * 
 * Shows all tables with their actual foreign keys and CakePHP associations
 * Use this as a reference BEFORE baking to determine view template tabs
 * 
 * Usage: bin/cake show_associations
 * Output: Markdown documentation of all tables and their relationships
 */
class ShowAssociationsShell extends Shell
{
    /**
     * Get all configured database connections
     */
    protected function getConfiguredConnections()
    {
        $connections = [];
        
        try {
            $configuredConnections = ConnectionManager::configured();
            
            foreach ($configuredConnections as $name) {
                if (strpos($name, 'test') !== false || strpos($name, 'debug') !== false) {
                    continue;
                }
                
                try {
                    $connection = ConnectionManager::get($name);
                    $config = $connection->config();
                    $dbName = isset($config['database']) ? $config['database'] : $name;
                    $connections[$name] = $dbName;
                } catch (\Exception $e) {
                    // Skip
                }
            }
        } catch (\Exception $e) {
            $connections = ['default' => 'default'];
        }
        
        // Remove duplicates - keep first occurrence
        $dbToConnections = [];
        foreach ($connections as $name => $dbName) {
            if (!isset($dbToConnections[$dbName])) {
                $dbToConnections[$dbName] = $name;
            }
        }
        
        $uniqueConnections = [];
        foreach ($dbToConnections as $dbName => $name) {
            $uniqueConnections[$name] = $dbName;
        }
        
        return $uniqueConnections;
    }
    
    /**
     * Get associations from CakePHP Model file with foreign key information
     */
    protected function getModelAssociations($modelName)
    {
        $associations = [];
        
        try {
            $tablePath = ROOT . DS . 'src' . DS . 'Model' . DS . 'Table' . DS . $modelName . 'Table.php';
            
            if (!file_exists($tablePath)) {
                return $associations;
            }
            
            $content = file_get_contents($tablePath);
            
            // Match association definitions with their full config
            if (preg_match_all('/\$this->(belongsTo|hasMany|hasOne|belongsToMany|hasAndBelongsToMany)\([\'"](\w+)[\'"]([^;]*)\]/s', $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $assocType = $match[1];
                    $targetModel = $match[2];
                    $config = $match[3];
                    
                    if (!isset($associations[$assocType])) {
                        $associations[$assocType] = [];
                    }
                    
                    // Extract foreignKey
                    $foreignKey = null;
                    if (preg_match('/[\'"]foreignKey[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]/', $config, $fkMatch)) {
                        $foreignKey = $fkMatch[1];
                    }
                    
                    // Extract strategy for cross-database detection
                    $strategy = null;
                    if (preg_match('/[\'"]strategy[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]/', $config, $strategyMatch)) {
                        $strategy = $strategyMatch[1];
                    }
                    
                    $associations[$assocType][] = [
                        'model' => $targetModel,
                        'foreignKey' => $foreignKey,
                        'strategy' => $strategy
                    ];
                }
            }
            
        } catch (\Exception $e) {
            // Silently fail
        }
        
        return $associations;
    }
    
    /**
     * Get table columns
     */
    protected function getTableColumns($connection, $tableName)
    {
        try {
            $schema = $connection->getSchemaCollection()->describe($tableName);
            return $schema->columns();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Detect missing cross-database hasMany associations
     * 
     * Scans all tables for foreign keys pointing to models in other databases
     * Reports which hasMany associations are missing from parent models
     * 
     * @param array $allModelsData All models with their data
     * @return array Missing hasMany associations by parent model
     */
    protected function detectMissingHasMany($allModelsData)
    {
        $missing = [];
        
        // For each model, check if other models have FKs pointing to it
        foreach ($allModelsData as $parentModel => $parentData) {
            $parentTable = $parentData['table'];
            $parentConnection = $parentData['connection'];
            $parentHasMany = isset($parentData['associations']['hasMany']) ? $parentData['associations']['hasMany'] : [];
            
            // Expected FK column names - check both singular and plural forms
            // e.g., "Personnels" → check both "personnels_id" AND "personnel_id"
            $expectedFks = [
                Inflector::underscore($parentModel) . '_id',  // plural: personnels_id
                Inflector::underscore(Inflector::singularize($parentModel)) . '_id'  // singular: personnel_id
            ];
            $expectedFks = array_unique($expectedFks); // Remove duplicates if plural=singular
            
            // Check all OTHER models for FK pointing to this parent
            foreach ($allModelsData as $childModel => $childData) {
                if ($childModel === $parentModel) continue; // Skip self
                
                $childTable = $childData['table'];
                $childConnection = $childData['connection'];
                $childColumns = $childData['columns'];
                
                // Check if child has ANY of the expected FK columns
                $foundFk = null;
                foreach ($expectedFks as $fk) {
                    if (in_array($fk, $childColumns)) {
                        $foundFk = $fk;
                        break;
                    }
                }
                
                // If FK found, check if hasMany already defined
                if ($foundFk) {
                    // Check if hasMany already exists in parent
                    $alreadyDefined = false;
                    foreach ($parentHasMany as $assoc) {
                        if ($assoc['model'] === $childModel) {
                            $alreadyDefined = true;
                            break;
                        }
                    }
                    
                    // If not defined, it's missing!
                    if (!$alreadyDefined) {
                        $isCrossDb = ($parentConnection !== $childConnection);
                        
                        if (!isset($missing[$parentModel])) {
                            $missing[$parentModel] = [
                                'connection' => $parentConnection,
                                'database' => $parentData['database'],
                                'missing_associations' => []
                            ];
                        }
                        
                        $missing[$parentModel]['missing_associations'][] = [
                            'child_model' => $childModel,
                            'child_table' => $childTable,
                            'child_connection' => $childConnection,
                            'child_database' => $childData['database'],
                            'foreign_key' => $foundFk,
                            'cross_database' => $isCrossDb
                        ];
                    }
                }
            }
        }
        
        return $missing;
    }

    public function main()
    {
        $connections = $this->getConfiguredConnections();
        $allTablesList = [];
        $tablesByConnection = [];
        $allModelsData = []; // Track all models with their connections and FKs
        
        // First pass: collect all tables with their columns
        foreach ($connections as $name => $dbName) {
            try {
                $connection = ConnectionManager::get($name);
                $tables = $connection->getSchemaCollection()->listTables();
                
                foreach ($tables as $table) {
                    if (!in_array($table, $allTablesList)) {
                        $allTablesList[] = $table;
                    }
                    
                    $columns = $this->getTableColumns($connection, $table);
                    
                    $tablesByConnection[$name][$table] = [
                        'columns' => $columns,
                        'model_exists' => false,
                        'associations' => [],
                        'connection' => $name,
                        'database' => $dbName
                    ];
                }
            } catch (\Exception $e) {
                // Continue
            }
        }
        
        // Second pass: get associations from Model files
        foreach ($tablesByConnection as $connName => $tables) {
            foreach ($tables as $tableName => $tableData) {
                $modelName = Inflector::camelize($tableName);
                $tablePath = ROOT . DS . 'src' . DS . 'Model' . DS . 'Table' . DS . $modelName . 'Table.php';
                
                if (file_exists($tablePath)) {
                    $tablesByConnection[$connName][$tableName]['model_exists'] = true;
                    $associations = $this->getModelAssociations($modelName);
                    $tablesByConnection[$connName][$tableName]['associations'] = $associations;
                    
                    // Store for cross-database analysis
                    $allModelsData[$modelName] = [
                        'table' => $tableName,
                        'connection' => $connName,
                        'database' => $tableData['database'],
                        'columns' => $tableData['columns'],
                        'associations' => $associations
                    ];
                }
            }
        }
        
        // Third pass: detect missing cross-database hasMany associations
        $missingHasMany = $this->detectMissingHasMany($allModelsData);
        
        // Generate Markdown output
        $this->out("");
        $this->out("# Database Tables & Associations Reference");
        $this->out("**Generated:** " . date('Y-m-d H:i:s'));
        $this->out("");
        $this->out("This document shows all live database tables with their foreign keys and CakePHP associations.");
        $this->out("**Use this as reference BEFORE baking MVC templates** to determine what association tabs should be in view templates.");
        $this->out("");
        $this->out("---");
        $this->out("");
        
        foreach ($tablesByConnection as $connName => $tables) {
            $dbName = $connections[$connName];
            $this->out("");
            $this->out("## Database: `{$dbName}` (Connection: `{$connName}`)");
            $this->out("");
            
            foreach ($tables as $tableName => $tableData) {
                $modelName = Inflector::camelize($tableName);
                $hasModel = $tableData['model_exists'];
                $modelStatus = $hasModel ? '✅' : '❌';
                
                $this->out("### {$modelStatus} Table: `{$tableName}`");
                $this->out("");
                
                if (!$hasModel) {
                    $this->out("**Status:** Model not found - needs baking");
                    $this->out("```bash");
                    $this->out("bin\\cake bake model {$modelName} --connection {$connName} --force");
                    $this->out("```");
                    $this->out("");
                    continue;
                }
                
                $associations = $tableData['associations'];
                $columns = $tableData['columns'];
                
                // Find foreign key columns
                $fkColumns = [];
                foreach ($columns as $column) {
                    if (preg_match('/_id$/', $column) && $column !== 'id') {
                        $fkColumns[] = $column;
                    }
                }
                
                $this->out("**Model:** `{$modelName}Table`");
                $this->out("");
                
                if (!empty($fkColumns)) {
                    $this->out("**Foreign Key Columns:**");
                    foreach ($fkColumns as $fkCol) {
                        $this->out("- `{$fkCol}`");
                    }
                    $this->out("");
                }
                
                if (!empty($associations)) {
                    // belongsTo - these create "Parent" sections in view
                    if (isset($associations['belongsTo']) && !empty($associations['belongsTo'])) {
                        $this->out("**BelongsTo Associations** *(Parent records - show in View page header)*:");
                        foreach ($associations['belongsTo'] as $assoc) {
                            $targetTable = Inflector::tableize($assoc['model']);
                            $fk = $assoc['foreignKey'];
                            $strategy = $assoc['strategy'];
                            $crossDb = ($strategy === 'select') ? ' 🔗 Cross-DB' : '';
                            
                            if (!in_array($targetTable, $allTablesList)) {
                                $this->out("  - ⚠️ `{$assoc['model']}` (FK: `{$fk}`) - **TARGET TABLE NOT FOUND**");
                            } else {
                                $this->out("  - `{$assoc['model']}` (FK: `{$fk}`){$crossDb}");
                            }
                        }
                        $this->out("");
                    }
                    
                    // hasMany - these create tabs in view template
                    if (isset($associations['hasMany']) && !empty($associations['hasMany'])) {
                        $this->out("**HasMany Associations** *(Child records - CREATE TAB IN VIEW TEMPLATE)*:");
                        foreach ($associations['hasMany'] as $assoc) {
                            $targetTable = Inflector::tableize($assoc['model']);
                            $fk = $assoc['foreignKey'];
                            $strategy = $assoc['strategy'];
                            $crossDb = ($strategy === 'select') ? ' 🔗 Cross-DB' : '';
                            
                            if (!in_array($targetTable, $allTablesList)) {
                                $this->out("  - ⚠️ `{$assoc['model']}` (FK: `{$fk}`) - **TARGET TABLE NOT FOUND**");
                            } else {
                                $this->out("  - ✨ **`{$assoc['model']}`** (FK: `{$fk}`){$crossDb}");
                                $this->out("    → **Add tab in view.ctp:** `{$modelName}` → `{$assoc['model']}` list");
                            }
                        }
                        $this->out("");
                    }
                    
                    // hasOne - similar to belongsTo but reverse
                    if (isset($associations['hasOne']) && !empty($associations['hasOne'])) {
                        $this->out("**HasOne Associations** *(Related record - show in View page)*:");
                        foreach ($associations['hasOne'] as $assoc) {
                            $targetTable = Inflector::tableize($assoc['model']);
                            $fk = $assoc['foreignKey'];
                            $strategy = $assoc['strategy'];
                            $crossDb = ($strategy === 'select') ? ' 🔗 Cross-DB' : '';
                            
                            if (!in_array($targetTable, $allTablesList)) {
                                $this->out("  - ⚠️ `{$assoc['model']}` (FK: `{$fk}`) - **TARGET TABLE NOT FOUND**");
                            } else {
                                $this->out("  - `{$assoc['model']}` (FK: `{$fk}`){$crossDb}");
                            }
                        }
                        $this->out("");
                    }
                    
                    // belongsToMany - many-to-many, create tabs
                    if (isset($associations['belongsToMany']) && !empty($associations['belongsToMany'])) {
                        $this->out("**BelongsToMany Associations** *(Many-to-many - CREATE TAB IN VIEW TEMPLATE)*:");
                        foreach ($associations['belongsToMany'] as $assoc) {
                            $targetTable = Inflector::tableize($assoc['model']);
                            $strategy = $assoc['strategy'];
                            $crossDb = ($strategy === 'select') ? ' 🔗 Cross-DB' : '';
                            
                            if (!in_array($targetTable, $allTablesList)) {
                                $this->out("  - ⚠️ `{$assoc['model']}` - **TARGET TABLE NOT FOUND**");
                            } else {
                                $this->out("  - ✨ **`{$assoc['model']}`**{$crossDb}");
                                $this->out("    → **Add tab in view.ctp:** `{$modelName}` → `{$assoc['model']}` list");
                            }
                        }
                        $this->out("");
                    }
                } else {
                    $this->out("**No associations defined**");
                    $this->out("");
                }
                
                $this->out("---");
                $this->out("");
            }
        }
        
        // Report Missing Cross-Database hasMany Associations
        if (!empty($missingHasMany)) {
            $this->out("");
            $this->out("---");
            $this->out("");
            $this->out("## ⚠️ Missing Cross-Database hasMany Associations");
            $this->out("");
            $this->out("**CRITICAL:** These associations were NOT detected by bake and need manual addition!");
            $this->out("");
            $this->out("The following parent models have child tables (in other databases) with foreign keys pointing to them,");
            $this->out("but the hasMany association is NOT defined in the parent Table class.");
            $this->out("");
            $this->out("**Impact:** View templates will be missing association tabs, and related data won't be accessible.");
            $this->out("");
            
            $crossDbCount = 0;
            $sameDbCount = 0;
            
            foreach ($missingHasMany as $parentModel => $parentInfo) {
                $this->out("### {$parentModel} ({$parentInfo['connection']}: {$parentInfo['database']})");
                $this->out("");
                $this->out("**Missing hasMany associations:**");
                $this->out("");
                
                foreach ($parentInfo['missing_associations'] as $missing) {
                    if ($missing['cross_database']) {
                        $this->out("- 🔗 **{$missing['child_model']}** (FK: `{$missing['foreign_key']}` in **{$missing['child_database']}**.{$missing['child_table']}) - CROSS-DATABASE");
                        $crossDbCount++;
                    } else {
                        $this->out("- ✨ **{$missing['child_model']}** (FK: `{$missing['foreign_key']}` in {$missing['child_database']}.{$missing['child_table']}) - Same DB");
                        $sameDbCount++;
                    }
                }
                
                $this->out("");
                $this->out("**Code to add to `src/Model/Table/{$parentModel}Table.php`:**");
                $this->out("");
                $this->out("```php");
                foreach ($parentInfo['missing_associations'] as $missing) {
                    $this->out("\$this->hasMany('{$missing['child_model']}', [");
                    $this->out("    'foreignKey' => '{$missing['foreign_key']}',");
                    if ($missing['cross_database']) {
                        $this->out("    'strategy' => 'select', // REQUIRED for cross-database!");
                    }
                    $this->out("]);");
                    $this->out("");
                }
                $this->out("```");
                $this->out("");
                $this->out("**Then update the controller's view() action:**");
                $this->out("");
                $this->out("```php");
                $this->out("// In {$parentModel}Controller::view()");
                foreach ($parentInfo['missing_associations'] as $missing) {
                    $this->out("\$contain[] = '{$missing['child_model']}';");
                }
                $this->out("```");
                $this->out("");
                $this->out("---");
                $this->out("");
            }
            
            $this->out("**Missing Associations Summary:**");
            $this->out("- 🔗 Cross-Database: {$crossDbCount}");
            $this->out("- ✨ Same Database: {$sameDbCount}");
            $this->out("- **Total Missing:** " . ($crossDbCount + $sameDbCount));
            $this->out("");
        }
        
        // Summary
        $totalTables = 0;
        $tablesWithModel = 0;
        $hasMany = 0;
        $belongsToMany = 0;
        
        foreach ($tablesByConnection as $tables) {
            foreach ($tables as $tableData) {
                $totalTables++;
                if ($tableData['model_exists']) {
                    $tablesWithModel++;
                    if (isset($tableData['associations']['hasMany'])) {
                        $hasMany += count($tableData['associations']['hasMany']);
                    }
                    if (isset($tableData['associations']['belongsToMany'])) {
                        $belongsToMany += count($tableData['associations']['belongsToMany']);
                    }
                }
            }
        }
        
        $this->out("");
        $this->out("## Summary");
        $this->out("");
        $this->out("- **Total Tables:** {$totalTables}");
        $this->out("- **Tables with Models:** {$tablesWithModel}");
        $this->out("- **HasMany Associations:** {$hasMany} (these need tabs in view templates)");
        $this->out("- **BelongsToMany Associations:** {$belongsToMany} (these need tabs in view templates)");
        $this->out("- **Total View Tabs Needed:** " . ($hasMany + $belongsToMany));
        $this->out("");
        $this->out("---");
        $this->out("");
        $this->out("## Legend");
        $this->out("");
        $this->out("- ✅ Model exists");
        $this->out("- ❌ Model needs baking");
        $this->out("- ✨ **Association that needs a tab in view template**");
        $this->out("- 🔗 Cross-database association (uses `strategy => 'select'`)");
        $this->out("- ⚠️ Warning: Target table not found in any database");
        $this->out("");
        $this->out("## How to Use This Reference");
        $this->out("");
        $this->out("1. **Before Baking:** Check this document to see what associations exist");
        $this->out("2. **Identify Tabs:** Look for ✨ markers - these are hasMany/belongsToMany that need tabs");
        $this->out("3. **Update Bake Templates:** Modify `src/Template/Bake/Template/view.ctp` to auto-generate tabs");
        $this->out("4. **Example Tab Structure:**");
        $this->out("   ```php");
        $this->out("   <!-- In view.ctp -->");
        $this->out("   <div class=\"related\">");
        $this->out("       <h4><?= __('Related TableName') ?></h4>");
        $this->out("       <?php if (!empty(\$entity->child_records)): ?>");
        $this->out("           <table class=\"table\">");
        $this->out("               <!-- Table content -->");
        $this->out("           </table>");
        $this->out("       <?php endif; ?>");
        $this->out("   </div>");
        $this->out("   ```");
        $this->out("");
    }
}
