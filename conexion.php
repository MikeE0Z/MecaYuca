<?php
$servername = "localhost";
$username = "root";
$password = "123";
$dbname = "MecaYuca";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
