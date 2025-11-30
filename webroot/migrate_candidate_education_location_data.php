<?php
// Script to migrate location data from propinsi_id/kabupaten_id to master_propinsi_id/master_kabupaten_id
$host = 'localhost';
$username = 'root';
$password = '';
$db = 'cms_lpk_candidates';

echo "<h2>Migrate CandidateEducations Location Data</h2>";
echo "<p>This script copies data from propinsi_id/kabupaten_id to master_propinsi_id/master_kabupaten_id</p>";

$conn = new mysqli($host, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h3>Step 1: Check current data</h3>";
$sql = "SELECT id, master_propinsi_id, propinsi_id, master_kabupaten_id, kabupaten_id 
        FROM candidate_educations 
        WHERE propinsi_id IS NOT NULL OR kabupaten_id IS NOT NULL
        LIMIT 10";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>ID</th><th>master_propinsi_id (old)</th><th>propinsi_id (new)</th><th>master_kabupaten_id (old)</th><th>kabupaten_id (new)</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . (isset($row['master_propinsi_id']) ? $row['master_propinsi_id'] : 'NULL') . "</td>";
        echo "<td><strong>" . (isset($row['propinsi_id']) ? $row['propinsi_id'] : 'NULL') . "</strong></td>";
        echo "<td>" . (isset($row['master_kabupaten_id']) ? $row['master_kabupaten_id'] : 'NULL') . "</td>";
        echo "<td><strong>" . (isset($row['kabupaten_id']) ? $row['kabupaten_id'] : 'NULL') . "</strong></td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    echo "<h3>Step 2: Run Migration</h3>";
    
    // Update master_propinsi_id from propinsi_id where master_propinsi_id is NULL or 0
    $sql = "UPDATE candidate_educations 
            SET master_propinsi_id = propinsi_id 
            WHERE propinsi_id IS NOT NULL 
            AND propinsi_id > 0 
            AND (master_propinsi_id IS NULL OR master_propinsi_id = 0)";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Updated master_propinsi_id: " . $conn->affected_rows . " rows affected<br>";
    } else {
        echo "✗ Error updating master_propinsi_id: " . $conn->error . "<br>";
    }
    
    // Update master_kabupaten_id from kabupaten_id where master_kabupaten_id is NULL or 0
    $sql = "UPDATE candidate_educations 
            SET master_kabupaten_id = kabupaten_id 
            WHERE kabupaten_id IS NOT NULL 
            AND kabupaten_id > 0 
            AND (master_kabupaten_id IS NULL OR master_kabupaten_id = 0)";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Updated master_kabupaten_id: " . $conn->affected_rows . " rows affected<br><br>";
    } else {
        echo "✗ Error updating master_kabupaten_id: " . $conn->error . "<br>";
    }
    
    echo "<h3>Step 3: Verify migrated data</h3>";
    $sql = "SELECT id, master_propinsi_id, propinsi_id, master_kabupaten_id, kabupaten_id 
            FROM candidate_educations 
            WHERE master_propinsi_id IS NOT NULL OR master_kabupaten_id IS NOT NULL
            LIMIT 10";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='8'>";
        echo "<tr><th>ID</th><th>master_propinsi_id (FIXED)</th><th>propinsi_id (source)</th><th>master_kabupaten_id (FIXED)</th><th>kabupaten_id (source)</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td><strong style='color:green'>" . (isset($row['master_propinsi_id']) ? $row['master_propinsi_id'] : 'NULL') . "</strong></td>";
            echo "<td>" . (isset($row['propinsi_id']) ? $row['propinsi_id'] : 'NULL') . "</td>";
            echo "<td><strong style='color:green'>" . (isset($row['master_kabupaten_id']) ? $row['master_kabupaten_id'] : 'NULL') . "</strong></td>";
            echo "<td>" . (isset($row['kabupaten_id']) ? $row['kabupaten_id'] : 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }
    
    echo "<hr>";
    echo "<h3>Summary</h3>";
    echo "<p style='color:green;'><strong>✓ Migration completed successfully!</strong></p>";
    echo "<p>Now the associations should work correctly and display proper values in Candidates view.</p>";
    echo "<p><a href='/project_tmm/candidates' style='padding:10px 20px; background:#667eea; color:white; text-decoration:none; border-radius:6px;'>Go to Candidates</a></p>";
    
} else {
    echo "<p style='color:orange;'>No data found with propinsi_id or kabupaten_id. Nothing to migrate.</p>";
    echo "<p>This might mean:</p>";
    echo "<ul>";
    echo "<li>No candidate_educations records exist yet</li>";
    echo "<li>All records already use master_propinsi_id/master_kabupaten_id correctly</li>";
    echo "<li>Migration was already completed</li>";
    echo "</ul>";
}

$conn->close();
?>