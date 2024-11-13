<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = $_POST['nombre_usuario'];
    $contrasena = $_POST['contrasena'];

    // Consulta para verificar el nombre de usuario y contraseña
    $sql = "SELECT rol, contrasena FROM clientes WHERE nombre_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre_usuario);
    $stmt->execute();
    $stmt->bind_result($rol, $hashed_password);
    $stmt->fetch();
    $stmt->close();

    if ($hashed_password && password_verify($contrasena, $hashed_password)) {
        if ($rol == 'admin') {
            header("Location: admin.html");
            exit();
        } elseif ($rol == 'usuario' || $rol == 'guia') {
            header("Location: cliente.html");
            exit();
        }
    } else {
        echo "<p class='error'>Usuario o contraseña incorrectos</p>";
    }
}

$conn->close();
?>
