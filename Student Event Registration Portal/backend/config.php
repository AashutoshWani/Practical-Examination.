<?php
// backend/config.php - Database connection for CampusConnect 2026
// Uses your existing database/table.

$db_host = "localhost";
$db_user = "root";           // change to a dedicated DB user in production
$db_pass = "aashu";            // your MySQL/MariaDB password
$db_name = "studentdb";       // your existing database

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
