<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'conexion.php';

if (isset($_GET['id'])) {

    $id_producto = $_GET['id'];

    try {

        $sql = "DELETE FROM productos WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("i", $id_producto);

        $stmt->execute();

        $stmt->close();

        header("Location: inventario.php");
        exit();

    } catch (mysqli_sql_exception $e) {

        die("Error crítico al intentar eliminar el registro: " . $e->getMessage());

    }

} else {

    header("Location: inventario.php");
    exit();

}

?>