<?php
$db_host = 'localhost';  // Database host (usually localhost)
$db_username = 'root';
$db_password = '';
$db_name = 'caps';

// Create a database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
