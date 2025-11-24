<?php
require_once 'config/db.php';

// Validar administrador
if (!isAdmin()) {
    $_SESSION['error'] = 'Acceso denegado';
    header('Location: index.php');
    exit;
}

$conn = getConnection();

/* ======================================================
   1. GANANCIAS TOTALES
   ====================================================== */
$sql_total = "SELECT SUM(total) AS ganancias_totales FROM pedidos";
$ganancias_totales = $conn->query($sql_total)->fetch_assoc()['ganancias_totales'] ?? 0;

/* ======================================================
   2. TOTAL DE PEDIDOS
   ====================================================== */
$sql_count_pedidos = "SELECT COUNT(*) AS total_pedidos FROM pedidos";
$total_pedidos = $conn->query($sql_count_pedidos)->fetch_assoc()['total_pedidos'];

/* ======================================================
   3. TOTAL DE CLIENTES
   ====================================================== */
$sql_clientes = "SELECT COUNT(*) AS total_clientes FROM usuarios WHERE es_admin = 0";
$total_clientes = $conn->query($sql_clientes)->fetch_assoc()['total_clientes'];

/* ======================================================
   4. PRODUCTOS MÁS VENDIDOS
   ====================================================== */
$sql_top = "
    SELECT 
        dp.id_producto,
        p.nombre,
        SUM(dp.cantidad) AS total_vendido
    FROM detalle_pedidos dp
    INNER JOIN productos p ON dp.id_producto = p.id
    GROUP BY dp.id_producto
    ORDER BY total_vendido DESC
    LIMIT 5
";
$top_productos = $conn->query($sql_top);

/* ======================================================
   5. GANANCIAS POR MES (últimos 12 meses)
   ====================================================== */
$sql_mes = "
    SELECT 
        DATE_FORMAT(fecha_pedido, '%Y-%m') AS mes,
        SUM(total) AS ganancias_mes
    FROM pedidos
    GROUP BY mes
    ORDER BY mes DESC
    LIMIT 12
";
$ventas_mes = $conn->query($sql_mes);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrador</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <header>
    <div class="header-left">
      <img src="Images/LogoPucmm.png" alt="Logo PUCMM">
      <h1>Dashboard de Ventas - Administrador</h1>
    </div>
  </header>

<div class="dashboard">
    <div class="card">
        <h2>Ganancias Totales</h2>
        <p>$<?= number_format($ganancias_totales, 2) ?></p>
    </div>

    <div class="card">
        <h2>Total de Pedidos</h2>
        <p><?= $total_pedidos ?></p>
    </div>

    <div class="card">
        <h2>Clientes Registrados</h2>
        <p><?= $total_clientes ?></p>
    </div>
</div>

<h2> Productos Más Vendidos</h2>
<table>
    <tr>
        <th>Producto</th>
        <th>Cantidad Vendida</th>
    </tr>
    <?php while ($row = $top_productos->fetch_assoc()): ?>
    <tr>
        <td><?= $row['nombre'] ?></td>
        <td><?= $row['total_vendido'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<h2> Ganancias por Mes (Últimos 12 meses)</h2>
<table>
    <tr>
        <th>Mes</th>
        <th>Ganancias</th>
    </tr>

    <?php while ($row = $ventas_mes->fetch_assoc()): ?>
    <tr>
        <td><?= $row['mes'] ?></td>
        <td>$<?= number_format($row['ganancias_mes'], 2) ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

<?php $conn->close(); ?>
