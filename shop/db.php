<?php
$servername = "localhost";
$username = "root";
$password = "toor";
$dbname = "simple_shop_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  error_log("Error: " . $conn->connect_error);
  die("Connection failed");
}

$conn->set_charset("utf8mb4");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_session_id = session_id();
?>
