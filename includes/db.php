<?php
$host = 'localhost';
$db = 'nutritrack';
$user = 'root';
$password = 'asna0209';

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>