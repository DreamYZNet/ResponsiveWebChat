<?php
// Error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create connection
$servername = "localhost";
$username = "XXX";
$password = "XXX";
$dbname = "dreamyz_messages";
$sql = new mysqli($servername, $username, $password, $dbname);
if ($sql->connect_error) {
    die("Connection failed: " . $sql->connect_error);
}
?>