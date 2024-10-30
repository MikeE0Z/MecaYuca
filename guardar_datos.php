<?php
// Configuración de la base de datos
$servidor = "localhost"; // Cambia si es necesario
$usuario = "tu_usuario"; // Cambia a tu usuario
$contraseña = "tu_contraseña"; // Cambia a tu contraseña
$base_datos = "turismo"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servidor, $usuario, $contraseña, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$descripcion = $_POST['descripcion'];

// Preparar y ejecutar consulta SQL
$sql = "INSERT INTO clientes (nombre, email, telefono, descripcion) VALUES ('$nombre', '$email', '$telefono', '$descripcion')";

if ($conn->query($sql) === TRUE) {
    echo "Los datos se guardaron correctamente.";
} else {
    echo "Error al guardar los datos: " . $conn->error;
}

// Cerrar conexión
$conn->close();
?>