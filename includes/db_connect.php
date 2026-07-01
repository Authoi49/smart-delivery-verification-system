<?php
/**
 * db_connect.php
 * Central database connection used by every PHP page.
 * Update the credentials below to match your local MySQL setup
 * (XAMPP / WAMP / MAMP default is usually user "root" with empty password).
 */

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "smart_delivery";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
