<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre_producto = trim($_POST['nombre']);
    $categoria_id = $_POST['categoria'];
    $stock = $_POST['stock'];
    $precio = $_POST['precio'];

    try {

        $sql = "INSERT INTO productos
                (nombre_producto, categoria_id, stock, precio)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "siid",
            $nombre_producto,
            $categoria_id,
            $stock,
            $precio
        );

        $stmt->execute();

        $stmt->close();

        header("Location: inventario.php");
        exit();

    } catch (mysqli_sql_exception $e) {

        die("Error al registrar el producto: " . $e->getMessage());

    }

} else {

    header("Location: inventario.php");
    exit();

}
?>