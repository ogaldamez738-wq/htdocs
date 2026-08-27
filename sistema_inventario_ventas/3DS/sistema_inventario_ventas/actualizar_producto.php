<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: index.php"); 
    exit(); 
}
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = $_POST['id'];
    $nombre = trim($_POST['nombre']);
    $categoria_id = $_POST['categoria'];
    $stock = $_POST['stock'];
    $precio = $_POST['precio'];

    try {
        // Consulta UPDATE con Prepared Statements
        $sql = "UPDATE productos SET nombre_producto = ?, categoria_id = ?, stock = ?, precio = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        // Vincular tipos: s = string, i = int, i = int, d = double, i = int ("siidi")
        $stmt->bind_param("siidi", $nombre, $categoria_id, $stock, $precio, $id_producto);
        $stmt->execute();
        $stmt->close();

        header("Location: inventario.php");
        exit();

    } catch (mysqli_sql_exception $e) {
        die("Error al actualizar el producto: " . $e->getMessage());
    }
} else {
    header("Location: inventario.php");
    exit();
}
?>