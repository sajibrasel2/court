<?php
// Run once to add whatsapp_number column to users table
// Delete after running

$db_host = 'localhost';
$db_user = 'techandc_bot';
$db_pass = '12345Sajibs6@';
$db_name = 'techandc_court';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$conn->query("ALTER TABLE users ADD COLUMN whatsapp_number VARCHAR(20) DEFAULT NULL AFTER phone");
echo "whatsapp_number column added to users table<br>";

$conn->close();
echo "Done! Delete this file now.";
?>
