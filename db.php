<?php
// db.php

$host     = "localhost";
$port     = 3306;
$dbname   = "db001";   // change this to your database name
$username = "root";
$password = "123456";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: set charset to utf8mb4
if (!$conn->set_charset("utf8mb4")) {
    die("Error loading character set utf8mb4: " . $conn->error);
}
?>
