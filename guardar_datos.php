<?php
// Habilitar el reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de la base de datos
$servidor = "localhost"; 
$usuario = "root"; 
$contraseña = "123"; 
$base_datos = "guardar"; 

// Crear conexión
$conn = new mysqli($servidor, $usuario, $contraseña, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Insertar datos si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $descripcion = $_POST['descripcion'];

    // Usar sentencias preparadas para evitar inyecciones SQL
    $stmt = $conn->prepare("INSERT INTO clientes (nombre, email, telefono, descripcion) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $telefono, $descripcion);

    if ($stmt->execute()) {
        echo "Los datos se guardaron correctamente.";
    } else {
        echo "Error al guardar los datos: " . $stmt->error;
    }
    $stmt->close();
}

// Eliminar registro si se solicita
if (isset($_GET['eliminar_id'])) {
    $id = $_GET['eliminar_id'];
    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Reiniciar el AUTO_INCREMENT si la tabla está vacía
    $result = $conn->query("SELECT COUNT(*) as count FROM clientes");
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) {
        $conn->query("ALTER TABLE clientes AUTO_INCREMENT = 1");
    }
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit;
}

// Editar registro si se solicita
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $descripcion = $_POST['descripcion'];

    $stmt = $conn->prepare("UPDATE clientes SET nombre=?, email=?, telefono=?, descripcion=? WHERE id=?");
    $stmt->bind_param("ssssi", $nombre, $email, $telefono, $descripcion, $id);

    if ($stmt->execute()) {
        echo "Los datos se actualizaron correctamente.";
    } else {
        echo "Error al actualizar los datos: " . $stmt->error;
    }
    $stmt->close();
}

// Obtener todos los registros de la tabla "clientes"
$sql = "SELECT * FROM clientes";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .delete-btn {
            color: red;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }
        .edit-btn {
            color: blue;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h2>Registro de Clientes</h2>

    <!-- Formulario para agregar clientes -->
    <form method="POST" action="">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br>
        
        <label>Email:</label><br>
        <input type="email" name="email" required><br>
        
        <label>Teléfono:</label><br>
        <input type="text" name="telefono" required><br>
        
        <label>Descripción:</label><br>
        <textarea name="descripcion" required></textarea><br><br>
        
        <button type="submit" name="agregar">Agregar Cliente</button>
    </form>

    <h2>Lista de Clientes</h2>
    
    <!-- Tabla de clientes -->
    <table>
        <tr>
            <th>ID</th> <!-- Nueva columna para ID -->
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['id']) . "</td> <!-- Mostrar ID -->
                        <td>" . htmlspecialchars($row['nombre']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['telefono']) . "</td>
                        <td>" . htmlspecialchars($row['descripcion']) . "</td>
                        <td>
                            <a href='?eliminar_id=" . $row['id'] . "' class='delete-btn'>&times;</a>
                            <a href='?editar_id=" . $row['id'] . "' class='edit-btn'>✎ Editar</a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No hay clientes registrados</td></tr>"; // Cambia el colspan para la nueva columna
        }
        ?>
    </table>

    <?php
    // Mostrar formulario de edición si se solicita
    if (isset($_GET['editar_id'])) {
        $id = $_GET['editar_id'];
        $stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result_edit = $stmt->get_result();
        $cliente = $result_edit->fetch_assoc();

        if ($cliente) {
            echo "
            <h2>Editar Cliente</h2>
            <form method='POST' action=''>
                <input type='hidden' name='id' value='" . htmlspecialchars($cliente['id']) . "'>
                <label>Nombre:</label><br>
                <input type='text' name='nombre' value='" . htmlspecialchars($cliente['nombre']) . "' required><br>
                
                <label>Email:</label><br>
                <input type='email' name='email' value='" . htmlspecialchars($cliente['email']) . "' required><br>
                
                <label>Teléfono:</label><br>
                <input type='text' name='telefono' value='" . htmlspecialchars($cliente['telefono']) . "' required><br>
                
                <label>Descripción:</label><br>
                <textarea name='descripcion' required>" . htmlspecialchars($cliente['descripcion']) . "</textarea><br><br>
                
                <button type='submit' name='editar'>Actualizar Cliente</button>
            </form>";
        }
        $stmt->close();
    }

    // Cerrar conexión
    $conn->close();
    ?>
</body>
</html>
