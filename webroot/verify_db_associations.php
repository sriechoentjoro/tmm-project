<?php
// Script untuk verifikasi database mapping dan asosiasi

echo "========================================\n";
echo " Database Association Verification\n";
echo "========================================\n\n";

// Load CakePHP
require dirname(__DIR__) . '/config/bootstrap.php';

use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

// 1. Cek koneksi database
echo "1. Checking Database Connections:\n";
echo str_repeat("-", 40) . "\n";

$connections = ['default', 'cms_masters', 'cms_lpk_candidates'];
foreach ($connections as $connName) {
    try {
        $conn = ConnectionManager::get($connName);
        $conn->connect();
        echo "[OK] $connName - Connected\n";
        echo "     Database: " . $conn->config()['database'] . "\n";
    } catch (\Exception $e) {
        echo "[ERROR] $connName - " . $e->getMessage() . "\n";
    }
}

echo "\n2. Checking Master Tables in cms_masters:\n";
echo str_repeat("-", 40) . "\n";

try {
    $conn = ConnectionManager::get('cms_masters');
    
    $masterTables = ['master_stratas', 'master_propinsis', 'master_kabupatens', 'master_kecamatans', 'master_kelurahans'];
    
    foreach ($masterTables as $table) {
        try {
            $result = $conn->execute("SHOW TABLES LIKE '$table'")->fetch();
            if ($result) {
                $count = $conn->execute("SELECT COUNT(*) as count FROM $table")->fetch();
                echo "[OK] $table - Exists ($count[0] rows)\n";
            } else {
                echo "[ERROR] $table - NOT FOUND\n";
            }
        } catch (\Exception $e) {
            echo "[ERROR] $table - " . $e->getMessage() . "\n";
        }
    }
} catch (\Exception $e) {
    echo "[ERROR] Cannot connect to cms_masters: " . $e->getMessage() . "\n";
}

echo "\n3. Checking CandidateEducations Associations:\n";
echo str_repeat("-", 40) . "\n";

try {
    $CandidateEducations = TableRegistry::getTableLocator()->get('CandidateEducations');
    
    echo "Connection: " . $CandidateEducations->getConnection()->configName() . "\n";
    echo "Table: " . $CandidateEducations->getTable() . "\n\n";
    
    // Cek associations
    $associations = ['Candidates', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens'];
    
    foreach ($associations as $assocName) {
        if ($CandidateEducations->hasAssociation($assocName)) {
            $assoc = $CandidateEducations->getAssociation($assocName);
            echo "[OK] Association: $assocName\n";
            echo "     Type: " . $assoc->type() . "\n";
            echo "     Strategy: " . $assoc->getStrategy() . "\n";
            echo "     Foreign Key: " . $assoc->getForeignKey() . "\n";
            
            // Cek target table
            $targetTable = $assoc->getTarget();
            echo "     Target Table: " . $targetTable->getTable() . "\n";
            echo "     Target Connection: " . $targetTable->getConnection()->configName() . "\n";
        } else {
            echo "[ERROR] Association NOT found: $assocName\n";
        }
        echo "\n";
    }
    
} catch (\Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "\n4. Sample Data from candidate_educations:\n";
echo str_repeat("-", 40) . "\n";

try {
    $conn = ConnectionManager::get('cms_lpk_candidates');
    $result = $conn->execute("SELECT id, candidate_id, master_strata_id, master_propinsi_id, master_kabupaten_id FROM candidate_educations LIMIT 3")->fetchAll('assoc');
    
    if ($result) {
        foreach ($result as $row) {
            echo "ID: {$row['id']}, ";
            echo "Candidate: {$row['candidate_id']}, ";
            echo "Strata: {$row['master_strata_id']}, ";
            echo "Propinsi: {$row['master_propinsi_id']}, ";
            echo "Kabupaten: {$row['master_kabupaten_id']}\n";
        }
    } else {
        echo "No data found\n";
    }
} catch (\Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "\n========================================\n";
echo "Verification Complete!\n";
echo "========================================\n";
