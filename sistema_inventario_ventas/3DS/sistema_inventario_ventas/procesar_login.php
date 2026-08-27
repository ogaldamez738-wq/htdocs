<?php

session_start();

require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    try {

        $sql = "SELECT id, nombre_completo, password, rol
                FROM usuarios
                WHERE usuario = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $user);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $row = $result->fetch_assoc();

            if ($password === $row['password']) {

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['nombre'] = $row['nombre_completo'];
                $_SESSION['rol'] = $row['rol'];

                header("Location: test_dashboard.php");
                exit();

            } else {

                header("Location: index.php?error=1");
                exit();

            }

        } else {

            header("Location: index.php?error=1");
            exit();

        }

        $stmt->close();

    } catch (mysqli_sql_exception $e) {

        die("Error de autenticación en el servidor: " . $e->getMessage());

    }

} else {

    header("Location: index.php");
    exit();

}
?>