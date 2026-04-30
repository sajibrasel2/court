<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_host = 'localhost';
$db_user = 'techandc_bot';
$db_pass = '12345Sajibs6@';
$db_name = 'techandc_court';

echo "<h3>Database Connection Test</h3>";
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    echo "<p style='color:red'>Connection failed: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color:green'>Connected successfully to the database!</p>";
    
    echo "<h3>Tables in Database:</h3>";
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        echo "<ul>";
        while ($row = $result->fetch_array()) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red'>Error showing tables: " . $conn->error . "</p>";
    }
}

echo "<h3>PHP Environment:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "cURL Enabled: " . (function_exists('curl_init') ? 'Yes' : 'No') . "<br>";
echo "MySQLi Enabled: " . (function_exists('mysqli_connect') ? 'Yes' : 'No') . "<br>";
?>
