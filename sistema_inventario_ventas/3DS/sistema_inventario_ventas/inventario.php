<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'conexion.php';

// 1. Verificamos si el usuario envió un término en la barra de búsqueda
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if ($busqueda != '') {
    // 2. Consulta con filtro LIKE para nombre de producto o categoría usando Sentencias Preparadas
    $sql = "SELECT p.id, p.nombre_producto, c.nombre_categoria, p.stock, p.precio
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            WHERE p.nombre_producto LIKE ? OR c.nombre_categoria LIKE ?
            ORDER BY p.id ASC";

    $stmt = $conn->prepare($sql);

    // Concatenamos los comodines % al texto enviado por el usuario
    $param_busqueda = "%" . $busqueda . "%";

    // Vinculamos el parámetro dos veces (para nombre y para categoría)
    $stmt->bind_param("ss", $param_busqueda, $param_busqueda);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $stmt->close();
} else {
    // 3. Consulta general sin filtro para mostrar todo el inventario
    $sql = "SELECT p.id, p.nombre_producto, c.nombre_categoria, p.stock, p.precio
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            ORDER BY p.id ASC";
    $resultado = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inventario - Sistema de Ventas</title>

<style>

body{
    font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif;
    background:#f8fafc;
    padding:20px;
}

.container{
    max-width:1000px;
    margin:auto;
    background:white;
    padding:20px;
    border-radius:8px;
    box-shadow:0 4px 6px rgba(0,0,0,.05);
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:2px solid #e2e8f0;
    padding-bottom:10px;
    margin-bottom:20px;
}

.btn-salir{
    background:#ef4444;
    color:white;
    padding:8px 15px;
    border-radius:5px;
    text-decoration:none;
    font-weight:bold;
}

.btn-editar{
    background-color: #f59e0b;
    color: white;
    padding: 6px 12px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 13px;
    font-weight: bold;
    margin-right: 5px;
}

.btn-editar:hover{
    background-color: #d97706;
}

.btn-eliminar{
    background:#ef4444;
    color:white;
    padding:6px 12px;
    text-decoration:none;
    border-radius:4px;
    font-size:13px;
    font-weight:bold;
}

.btn-eliminar:hover{
    background:#b91c1c;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:12px;
    border-bottom:1px solid #e2e8f0;
}

th{
    background:#f1f5f9;
}

.stock-bajo{
    color:red;
    font-weight:bold;
}

</style>

</head>

<body>

<div class="container">

<div class="header">

<h2>Catálogo de Inventario</h2>

<div>

Usuario:
<strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong>

<a href="dashboard.php" style="background:#64748b;color:white;padding:10px;text-decoration:none;border-radius:5px;margin-left:10px;font-weight:bold;">
🏠 Dashboard
</a>

</div>

</div>

<!-- Barra de Controles: Botón Nuevo + Formulario de Búsqueda -->
<div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">

<a href="nuevo_producto.php" style="background: #3b82f6; color: white; padding: 10px; text-decoration: none; border-radius: 5px; font-weight: bold;">+ Nuevo Producto</a>

<!-- Formulario de Búsqueda (Método GET) -->
<form method="GET" style="display: flex; gap: 10px;">
    <input type="text" name="buscar" placeholder="Buscar producto o categoría..."
           value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>"
           style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; width: 250px;">
    <button type="submit" style="background: #10b981; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">🔍 Buscar</button>
    <a href="inventario.php" style="background: #64748b; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold;">Limpiar</a>
</form>

</div>

<table>

<thead>

<tr>

<th>Código</th>
<th>Producto</th>
<th>Categoría</th>
<th>Stock</th>
<th>Precio</th>
<th>Acciones</th>

</tr>

</thead>

<tbody>

<?php

if($resultado->num_rows > 0){

    while($fila = $resultado->fetch_assoc()){

        $claseStock = ($fila['stock'] < 10) ? 'stock-bajo' : '';

?>

<tr>

<td>
<?php echo $fila['id']; ?>
</td>

<td>
<?php echo htmlspecialchars($fila['nombre_producto']); ?>
</td>

<td>
<?php echo htmlspecialchars($fila['nombre_categoria']); ?>
</td>

<td class="<?php echo $claseStock; ?>">
<?php echo $fila['stock']; ?> unds.
</td>

<td>
$<?php echo number_format($fila['precio'],2); ?>
</td>

<td>

<!-- BOTÓN EDITAR -->
<a href="editar_producto.php?id=<?php echo $fila['id']; ?>" class="btn-editar">
✏️ Editar
</a>

<!-- BOTÓN ELIMINAR -->
<a href="eliminar_producto.php?id=<?php echo $fila['id']; ?>"
class="btn-eliminar"
onclick="return confirm('¿Estás seguro de eliminar el producto: <?php echo htmlspecialchars($fila['nombre_producto']); ?>?');">
🗑️ Eliminar
</a>

</td>

</tr>

<?php

    }

}else{

?>

<tr>

<td colspan="6" style="text-align:center;">
No se encontraron productos coincidentes.
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<?php
$resultado->free();
?>

</body>
</html>