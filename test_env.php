<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_host = 'localhost';
$db_user = 'techandc_bot';
$db_pass = '12345Sajibs6@';
$db_name = 'techandc_court';

echo "<h3>Database Debug Info</h3>";
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    echo "<p style='color:red'>Connection failed: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color:green'>Connected successfully!</p>";
    
    $tables = ['users', 'cases', 'global_causelist'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "<p style='color:green'>Table '$table' exists.</p>";
            $res = $conn->query("SELECT COUNT(*) as count FROM $table");
            $row = $res->fetch_assoc();
            echo "Row count in '$table': " . $row['count'] . "<br>";
        } else {
            echo "<p style='color:red'>Table '$table' is MISSING!</p>";
        }
    }
}
?>
