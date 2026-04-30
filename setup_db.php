<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'court_app';

// Create Database if not exists
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
$conn->query($sql);
$conn->select_db($db);

// Users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100),
    pin VARCHAR(255),
    security_answer VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Cases table
$sql = "CREATE TABLE IF NOT EXISTS cases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    court_id INT,
    court_name VARCHAR(255),
    case_no VARCHAR(100),
    plaintiff TEXT,
    defendant TEXT,
    last_activity TEXT,
    prev_date VARCHAR(50),
    next_date VARCHAR(50),
    last_order TEXT,
    pdf_name VARCHAR(255),
    pdf_data LONGBLOB,
    history JSON,
    last_checked TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$conn->query($sql);

// Saved Courts table
$sql = "CREATE TABLE IF NOT EXISTS saved_courts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    court_id INT,
    court_name VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$conn->query($sql);

echo "Database and tables created successfully.";
?>
