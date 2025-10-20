<?php
$host = "localhost";      // or 127.0.0.1
$user = "root";           // your phpMyAdmin username
$pass = "";               // your phpMyAdmin password (leave empty if none)
$dbname = "event__tracker";  // your database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
