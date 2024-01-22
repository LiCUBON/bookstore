<?php 

$host = "127.0.0.1";
$username = "root";
$password = "root";
$dbname = "bookstore";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}
?>