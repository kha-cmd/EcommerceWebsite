<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'collectoin_shoes';
$port = 3307;
$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>