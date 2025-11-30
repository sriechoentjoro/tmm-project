<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;

/**
 * List Tables Shell
 * 
 * Lists all tables across all database connections with foreign key relationships
 * 
 * Usage: bin/cake list_tables
 */
class ListTablesShell extends Shell
{
    /**
     * Get all configured database connections from config
     * 
     * @return array Key-value pairs of connection names and database names
     */
    protected function getConfiguredConnections()
    {
        $connections = [];
        
        try {
            // Get all configured datasources
            $configuredConnections = ConnectionManager::configured();
            
            foreach ($configuredConnections as $name) {
                // Skip test and debug connections
                if (strpos($name, 'test') !== false || strpos($name, 'debug') !== false) {
                    continue;
                }
                
                try {
                    $connection = ConnectionManager::get($name);
                    $config = $connection->config();
                    
                    // Get database name from connection config
                    $dbName = isset($config['database']) ? $config['database'] : $name;
                    $connections[$name] = $dbName;
                    
                } catch (\Exception $e) {
                    // Skip connections that can't be established
                    $this->err("Warning: Could not connect to '{$name}': " . $e->getMessage());
                }
            }
            
        } catch (\Exception $e) {
            $this->err("Error reading database configuration: " . $e->getMessage());
            // Fallback to default connection only
            $connections = ['default' => 'default'];
        }
        
        return $connections;
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
            
            // Dynamically detect all association types using a generic pattern
            // Matches: $this->associationType('TargetModel', [...])
            if (preg_match_all('/\$this->(belongsTo|hasMany|hasOne|belongsToMany|hasAndBelongsToMany)\([\'"](\w+)[\'"]([^;]*)\]/s', $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $assocType = $match[1];  // belongsTo, hasMany, etc.
                    $targetModel = $match[2]; // Target model name
                    $config = $match[3]; // Association config array
                    
                    if (!isset($associations[$assocType])) {
                        $associations[$assocType] = [];
                    }
                    
                    // Extract foreignKey from config if present
                    $foreignKey = null;
                    if (preg_match('/[\'"]foreignKey[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]/', $config, $fkMatch)) {
                        $foreignKey = $fkMatch[1];
                    }
                    
                    $associations[$assocType][] = [
                        'model' => $targetModel,
                        'foreignKey' => $foreignKey
                    ];
                }
            }
            
        } catch (\Exception $e) {
            // Silently fail
        }
        
        return $associations;
    }

    /**
     * Find the most likely foreign key column name in a table schema for a given association target
     *
     * @param array $columns List of column names in the table
     * @param string $assoc Target model name (e.g. PaymentMethods)
     * @return string|null Column name if found, null otherwise
     */
    protected function findForeignKeyColumn(array $columns, $assoc)
    {
        // Candidate patterns (in order)
        $candidates = [];
        $singular = \Cake\Utility\Inflector::singularize($assoc);
        $underscoreSingular = \Cake\Utility\Inflector::underscore($singular);
        $underscore = \Cake\Utility\Inflector::underscore($assoc);
        $tableized = \Cake\Utility\Inflector::tableize($assoc);

        $candidates[] = $underscoreSingular . '_id'; // payment_method_id
        $candidates[] = $underscore . '_id'; // payment_methods_id
        $candidates[] = $tableized . '_id';

        // Also try plural/singular variations
        $candidates[] = \Cake\Utility\Inflector::pluralize($underscoreSingular) . '_id';
        $candidates[] = \Cake\Utility\Inflector::singularize($underscore) . '_id';

        foreach ($candidates as $c) {
            if (in_array($c, $columns)) {
                return $c;
            }
        }

        // Fallback: try to find any column that contains the base token and ends with _id
        $base = $underscoreSingular;
        foreach ($columns as $col) {
            if (substr($col, -3) === '_id' && strpos($col, $base) !== false) {
                return $col;
            }
        }

        return null;
    }

    /**
     * Get foreign key relationships for a table
     */
    protected function getForeignKeys($connection, $tableName)
    {
        $foreignKeys = [];
        
        try {
            $sql = "
                SELECT 
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = :tableName
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            ";
            
            $stmt = $connection->execute($sql, ['tableName' => $tableName]);
            $foreignKeys = $stmt->fetchAll('assoc');
            
        } catch (\Exception $e) {
            // Silently fail if we can't get FK info
        }
        
        return $foreignKeys;
    }

    /**
     * Get tables that reference this table (inverse relationships)
     */
    protected function getReferencedBy($connection, $tableName)
    {
        $referencedBy = [];
        
        try {
            $sql = "
                SELECT DISTINCT
                    TABLE_NAME,
                    COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND REFERENCED_TABLE_NAME = :tableName
            ";
            
            $stmt = $connection->execute($sql, ['tableName' => $tableName]);
            $referencedBy = $stmt->fetchAll('assoc');
            
        } catch (\Exception $e) {
            // Silently fail
        }
        
        return $referencedBy;
    }

    public function main()
    {
        // Get all configured database connections dynamically
        $connections = $this->getConfiguredConnections();

        $this->out("");
        $this->out("===============================================================");
        $this->out("  Database Tables & Associations - Asahi Inventory Management");
        $this->out("===============================================================");
        $this->out("");
        $this->out("  <info>Scanning " . count($connections) . " configured database connections...</info>");
        $this->out("");

        $totalTables = 0;
        $totalForeignKeys = 0;
        $crossDatabaseRefs = [];
        $allRelationships = []; // Store all relationships for visualization
        $allTablesList = []; // List of all existing tables across all DBs
        $orphanedForeignKeys = []; // Track _id columns without corresponding tables
        
        // Detect duplicate database connections
        $dbToConnections = [];
        foreach ($connections as $name => $dbName) {
            if (!isset($dbToConnections[$dbName])) {
                $dbToConnections[$dbName] = [];
            }
            $dbToConnections[$dbName][] = $name;
        }
        
        // Warn about duplicates
        $duplicates = [];
        foreach ($dbToConnections as $dbName => $connNames) {
            if (count($connNames) > 1) {
                $duplicates[$dbName] = $connNames;
            }
        }
        
        if (!empty($duplicates)) {
            $this->out("  <warning>WARNING: Duplicate database connections detected:</warning>");
            foreach ($duplicates as $dbName => $connNames) {
                $this->out("    <comment>{$dbName}</comment> is used by: " . implode(', ', $connNames));
            }
            $this->out("");
        }
        
        // Filter to unique databases only
        $uniqueConnections = [];
        foreach ($dbToConnections as $dbName => $connNames) {
            // Use the first connection name for each unique database
            $uniqueConnections[$connNames[0]] = $dbName;
        }
        $connections = $uniqueConnections;

        // First pass: collect all existing tables across ALL databases
        foreach ($connections as $name => $dbName) {
            try {
                $connection = ConnectionManager::get($name);
                $tables = $connection->getSchemaCollection()->listTables();
                foreach ($tables as $table) {
                    if (!in_array($table, $allTablesList)) {
                        $allTablesList[] = $table;
                    }
                }
            } catch (\Exception $e) {
                // Continue
            }
        }

        foreach ($connections as $name => $dbName) {
            $this->out("Connection: <info>{$name}</info> (<comment>{$dbName}</comment>)");
            
            try {
                $connection = ConnectionManager::get($name);
                $tables = $connection->getSchemaCollection()->listTables();

                if (empty($tables)) {
                    $this->out("  <warning>(no tables found)</warning>");
                } else {
                    $tableCount = count($tables);
                    $totalTables += $tableCount;
                    
                    $this->out("  <success>Found {$tableCount} tables</success>");
                    $this->out("");
                    
                    foreach ($tables as $table) {
                        // Check if Model/Table file exists
                        $modelName = \Cake\Utility\Inflector::camelize($table);
                        $tablePath = ROOT . DS . 'src' . DS . 'Model' . DS . 'Table' . DS . $modelName . 'Table.php';
                        $hasModel = file_exists($tablePath);
                        
                        $indicator = $hasModel ? '[OK]' : '[--]';
                        $color = $hasModel ? 'success' : 'warning';
                        
                        $this->out("  {$indicator} <{$color}>{$table}</{$color}>");
                        
                        // Check for orphaned foreign key columns (columns ending in _id without corresponding table)
                        try {
                            $schema = $connection->getSchemaCollection()->describe($table);
                            $columns = $schema->columns();
                            
                            foreach ($columns as $column) {
                                // Check if column ends with _id
                                if (preg_match('/_id$/', $column) && $column !== 'id') {
                                    // Skip common non-FK columns
                                    if (in_array($column, ['parent_id', 'reference_id', 'user_id'])) {
                                        continue; // These are often self-references or polymorphic
                                    }
                                    
                                    // Extract expected table name (remove _id suffix)
                                    $expectedTable = preg_replace('/_id$/', '', $column);
                                    
                                    // For personnel-related columns, check for 'personnels' table
                                    if (preg_match('/personnel$/', $expectedTable)) {
                                        $expectedTable = 'personnels';
                                    }
                                    
                                    // Check singular form
                                    if (!in_array($expectedTable, $allTablesList)) {
                                        // Check plural form
                                        $pluralTable = \Cake\Utility\Inflector::pluralize($expectedTable);
                                        if (!in_array($pluralTable, $allTablesList)) {
                                            // Check if it's a self-reference (table referencing itself)
                                            if ($expectedTable === \Cake\Utility\Inflector::singularize($table)) {
                                                continue; // Self-reference, skip
                                            }
                                            
                                            $orphanedForeignKeys[] = [
                                                'database' => $name,
                                                'table' => $table,
                                                'column' => $column,
                                                'expected_table' => $expectedTable
                                            ];
                                        }
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // Skip schema errors
                        }
                        
                        if ($hasModel) {
                            // Get associations from Model file
                            $associations = $this->getModelAssociations($modelName);
                            
                            // Process all association types dynamically
                            foreach ($associations as $assocType => $assocList) {
                                foreach ($assocList as $assocData) {
                                    // Handle both old string format and new array format
                                    $assoc = is_array($assocData) ? $assocData['model'] : $assocData;
                                    $foreignKey = is_array($assocData) ? $assocData['foreignKey'] : null;
                                    
                                    // Check if target table exists
                                    $targetTableName = \Cake\Utility\Inflector::tableize($assoc);
                                    
                                    if (!in_array($targetTableName, $allTablesList)) {
                                        // Table doesn't exist - SILENTLY SKIP (don't report as missing)
                                        // This is because Model files may be outdated/hardcoded
                                        // We only want to show what ACTUALLY exists in live databases
                                        continue;
                                    }
                                    
                                    // Determine display symbol based on association type
                                    $symbol = '->';
                                    $color = 'info';
                                    if (in_array($assocType, ['hasMany', 'hasOne'])) {
                                        $symbol = '<-';
                                        $color = 'comment';
                                    } elseif ($assocType === 'belongsToMany' || $assocType === 'hasAndBelongsToMany') {
                                        $symbol = '<->';
                                    }
                                    
                                    // Count only belongsTo as foreign keys (actual FK in database)
                                    if ($assocType === 'belongsTo') {
                                        $totalForeignKeys++;
                                    }
                                    
                                    $this->out("       {$symbol} {$assocType}: <{$color}>{$assoc}</{$color}>");
                                    
                                    // Determine actual foreign key column name (if belongsTo)
                                    $fkColumn = '';
                                    if ($assocType === 'belongsTo') {
                                        // Use foreign key from Model if available
                                        if ($foreignKey) {
                                            $fkColumn = $foreignKey;
                                        } else {
                                            // Try to find in columns list
                                            $fk = $this->findForeignKeyColumn($columns, $assoc);
                                            if ($fk) {
                                                $fkColumn = $fk;
                                            } else {
                                                // Fallback to a reasonable default
                                                $fkColumn = \Cake\Utility\Inflector::underscore(\Cake\Utility\Inflector::singularize($assoc)) . '_id';
                                            }
                                        }
                                    }

                                    // Store relationship for visualization
                                    $allRelationships[] = [
                                        'from_db' => $name,
                                        'from_table' => $table,
                                        'to_table' => $targetTableName,
                                        'column' => $fkColumn,
                                        'type' => $assocType
                                    ];

                                    // Check if it's a cross-database reference (only for belongsTo)
                                    if ($assocType === 'belongsTo') {
                                        $refTable = \Cake\Utility\Inflector::tableize($assoc);
                                        $refExists = in_array($refTable, $tables);
                                        if (!$refExists) {
                                            $crossDatabaseRefs[] = [
                                                'from_db' => $name,
                                                'from_table' => $table,
                                                'from_column' => $fkColumn,
                                                'to_table' => $refTable,
                                                'to_column' => 'id'
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->err("  Error accessing connection '{$name}': " . $e->getMessage());
            }
            
            $this->out("");
        }

        $this->out("===============================================================");
        $this->out("  <info>Summary</info>");
        $this->out("===============================================================");
        $this->out("  Unique databases: " . count($connections));
        $this->out("  Total tables: {$totalTables}");
        $this->out("  Total foreign keys: {$totalForeignKeys}");
        $this->out("  (Only showing associations where target tables exist in databases)");
        $this->out("");
        
        if (!empty($orphanedForeignKeys)) {
            $this->out("  <error>WARNING: Orphaned Foreign Key Columns Detected:</error>");
            $this->out("  (Columns ending in _id but referenced table doesn't exist in any database)");
            $this->out("");
            
            foreach ($orphanedForeignKeys as $orphan) {
                // Try to determine the expected table name with proper pluralization
                $expectedSingular = $orphan['expected_table'];
                $expectedPlural = \Cake\Utility\Inflector::pluralize($expectedSingular);
                
                $this->out(sprintf(
                    "  <error>* %s.%s (%s)</error> -> <comment>%s (id)</comment> [TABLE NOT FOUND]",
                    $orphan['database'],
                    $orphan['table'],
                    $orphan['column'],
                    $expectedPlural
                ));
            }
            $this->out("");
            $this->out("  <warning>Action needed:</warning>");
            $this->out("  1. Create the missing table, OR");
            $this->out("  2. Remove the orphaned column, OR");
            $this->out("  3. Add the database containing the referenced table to config");
            $this->out("");
        }
        
        if (!empty($crossDatabaseRefs)) {
            $this->out("  <warning>Cross-Database References Detected:</warning>");
            $this->out("  (These require 'strategy' => 'select' in associations)");
            $this->out("");
            
            foreach ($crossDatabaseRefs as $ref) {
                $this->out(sprintf(
                    "  - <info>%s.%s (%s)</info> -> <comment>%s (id)</comment>",
                    $ref['from_db'],
                    $ref['from_table'],
                    $ref['from_column'],
                    $ref['to_table']
                ));
            }
            $this->out("");
        }
        
        $this->out("Legend:");
        $this->out("  [OK] - Has Model/Table class");
        $this->out("  [--] - Missing Model/Table class (needs baking)");
        $this->out("  -> Association types: belongsTo (many-to-one)");
        $this->out("  <- Association types: hasMany, hasOne (one-to-many/one)");
        $this->out("  <-> Association types: belongsToMany, hasAndBelongsToMany (many-to-many)");
        $this->out("");
        
        // Generate visual relationship diagram
        $this->generateRelationshipDiagram($allRelationships, $connections);
    }
    
    /**
     * Generate ASCII art relationship diagram
     */
    protected function generateRelationshipDiagram($relationships, $connections)
    {
        $this->out("===============================================================");
        $this->out("  <info>Relationship Visualization (ASCII Diagram)</info>");
        // Group relationships by database for ASCII diagram
        $dbGroups = [];
        foreach ($relationships as $rel) {
            $dbGroups[$rel['from_db']][] = $rel;
        }
        
        foreach ($dbGroups as $dbName => $rels) {
            $this->out("");
            $this->out("+----------------------------------------------------------+");
            $this->out("|  DATABASE: <info>{$dbName}</info>");
            $this->out("+----------------------------------------------------------+");
            
            // Group by table
            $tableGroups = [];
            foreach ($rels as $rel) {
                $tableGroups[$rel['from_table']][] = $rel;
            }
            
            foreach ($tableGroups as $tableName => $tableRels) {
                $this->out("|");
                $this->out("|  +-- TABLE: <comment>{$tableName}</comment>");
                
                // Show belongsTo relationships
                $belongsTo = array_filter($tableRels, function($r) { return $r['type'] === 'belongsTo'; });
                if (!empty($belongsTo)) {
                    $this->out("|  |");
                    $this->out("|  |   <info>BELONGS TO:</info>");
                    foreach ($belongsTo as $rel) {
                        $arrow = "  |   +---> ";
                        $this->out("|{$arrow}<info>{$rel['to_table']}</info> (via {$rel['column']})");
                    }
                }
                
                // Show hasMany relationships
                $hasMany = array_filter($tableRels, function($r) { return $r['type'] === 'hasMany'; });
                if (!empty($hasMany)) {
                    $this->out("|  |");
                    $this->out("|  |   <comment>HAS MANY:</comment>");
                    foreach ($hasMany as $rel) {
                        $arrow = "  |   <--- ";
                        $this->out("|{$arrow}<comment>{$rel['to_table']}</comment>");
                    }
                }
                
                $this->out("|");
            }
        }
        
        $this->out("+----------------------------------------------------------+");
        $this->out("");
        
        // Generate Mermaid diagram code for external visualization
        $this->generateMermaidDiagram($relationships, $connections);
    }
    
    /**
     * Generate Mermaid.js diagram code
     */
    protected function generateMermaidDiagram($relationships, $connections)
    {
        $this->out("===============================================================");
        $this->out("  <info>Mermaid Diagram Code (Copy to https://mermaid.live)</info>");
        $this->out("===============================================================");
        $this->out("");
        $this->out("```mermaid");
        $this->out("erDiagram");
        $this->out("");
        
        // Generate entity definitions grouped by database
        foreach ($connections as $dbKey => $dbName) {
            $this->out("    %% Database: {$dbName}");
        }
        $this->out("");
        
        // Generate relationships
        $processed = [];
        foreach ($relationships as $rel) {
            if ($rel['type'] === 'belongsTo') {
                $key = $rel['from_table'] . '_' . $rel['to_table'];
                if (!isset($processed[$key])) {
                    $from = strtoupper($rel['from_table']);
                    $to = strtoupper($rel['to_table']);
                    $this->out("    {$from} }o--|| {$to} : \"{$rel['column']}\"");
                    $processed[$key] = true;
                }
            }
        }
        
        $this->out("");
        $this->out("```");
        $this->out("");
        $this->out("<info>Instructions:</info>");
        $this->out("  1. Copy the code above (between ```mermaid and ```)");
        $this->out("  2. Go to https://mermaid.live");
        $this->out("  3. Paste the code to visualize the relationships");
        $this->out("");
    }
}
