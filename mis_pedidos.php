<?php
require_once 'config/db.php';

// Verificar que el usuario esté logueado
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Debes iniciar sesión para ver tus pedidos';
    header('Location: login.php');
    exit;
}

$conn = getConnection();
$id_usuario = $_SESSION['user_id'];

// Obtener todos los pedidos del usuario
$sql_pedidos = "SELECT * FROM pedidos WHERE id_usuario = $id_usuario ORDER BY fecha_pedido DESC";
$pedidos = $conn->query($sql_pedidos);

// Obtener todas las categorías para sidebar
$sql_categorias = "SELECT * FROM categorias ORDER BY nombre";
$categorias_sidebar = $conn->query($sql_categorias);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - Hardware Store</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/pedidos.css">
    <link rel="icon" type="image/png" href="Images/LogoPucmm.png">

</head>
<body>
    <header>
        <div class="header-left">
            <img src="Images/LogoPucmm.png" alt="Logo PUCMM">
            <h1>Hardware Store</h1>
        </div>
        <div class="header-right">
            <span class="user-name">Hola, <?php echo $_SESSION['nombre']; ?></span>
            <button class="btn-carrito" onclick="window.location.href='carrito.php'">
                🛒 Carrito (<?php echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?>)
            </button>
            <button class="btn-logout" onclick="if(confirm('¿Cerrar sesión?')) window.location.href='logout.php'">
                Cerrar Sesión
            </button>
        </div>
    </header>

    <div class="container-principal">
        <!-- Sidebar de Categorías -->
        <aside class="sidebar">
            <h2>Categorías</h2>
            <ul class="categorias-list">
                <li>
                    <a href="index.php" class="categoria-item">
                        <span class="icono">🏠</span>
                        <span>Inicio</span>
                    </a>
                </li>
                <?php while ($cat = $categorias_sidebar->fetch_assoc()): ?>
                <li>
                    <a href="productos.php?categoria=<?php echo $cat['id']; ?>" class="categoria-item">
                        <span class="icono">📦</span>
                        <span><?php echo htmlspecialchars($cat['nombre']); ?></span>
                        <span class="flecha">›</span>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
            
            <div class="sidebar-usuario">
                <hr>
                <h3>Mi Cuenta</h3>
                <a href="mis_pedidos.php" class="categoria-item active">
                    <span class="icono">📋</span>
                    <span>Mis Pedidos</span>
                </a>
                <a href="carrito.php" class="categoria-item">
                    <span class="icono">🛒</span>
                    <span>Carrito</span>
                </a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <div class="contenido-principal">
            <h2 class="page-title">📋 Historial de Pedidos</h2>
            
            <?php if ($pedidos->num_rows == 0): ?>
                <div class="sin-pedidos">
                    <p>📦 Aún no has realizado ninguna compra</p>
                    <a href="index.php" class="btn-comprar">Explorar Productos</a>
                </div>
            <?php else: ?>
                <div class="pedidos-lista">
                    <?php while ($pedido = $pedidos->fetch_assoc()): ?>
                        <?php
                        // Obtener detalles del pedido
                        $id_pedido = $pedido['id'];
                        $sql_detalle = "SELECT * FROM detalle_pedidos WHERE id_pedido = $id_pedido";
                        $detalles = $conn->query($sql_detalle);
                        ?>
                        
                        <div class="pedido-card">
                            <div class="pedido-header">
                                <div class="pedido-info">
                                    <h3>Pedido #<?php echo $pedido['id']; ?></h3>
                                    <p class="pedido-fecha">
                                        📅 <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?>
                                    </p>
                                </div>
                                <div class="pedido-total">
                                    <span class="label">Total:</span>
                                    <span class="monto">$<?php echo number_format($pedido['total'], 2); ?></span>
                                </div>
                                <div class="pedido-estado">
                                    <span class="badge-completado"><?php echo $pedido['estado']; ?></span>
                                </div>
                            </div>
                            
                            <div class="pedido-productos">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio Unit.</th>
                                            <th>Cantidad</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($detalle = $detalles->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($detalle['nombre_producto']); ?></td>
                                            <td>$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                                            <td><?php echo $detalle['cantidad']; ?>x</td>
                                            <td class="subtotal">$<?php echo number_format($detalle['subtotal'], 2); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section footer-about">
                    <h3>Hardware Store</h3>
                    <p>Tu tienda de confianza para componentes de hardware y accesorios tecnológicos.</p>
                </div>
                
                <div class="footer-section footer-links">
                    <h3>Enlaces Rápidos</h3>
                    <ul>
                        <li><a href="index.php">Inicio</a></li>
                        <li><a href="carrito.php">Carrito</a></li>
                    </ul>
                </div>
                
                <div class="footer-section footer-contact">
                    <h3>Contáctanos</h3>
                    <p><span>📞</span> +1 (809) 555-0100</p>
                    <p><span>📧</span> info@hardwarestore.com</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Hardware Store. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>

<?php $conn->close(); ?>