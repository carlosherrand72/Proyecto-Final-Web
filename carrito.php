<?php
require_once 'config/db.php';

// Verificar que el usuario esté logueado
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Debes iniciar sesión para ver el carrito';
    header('Location: login.php');
    exit;
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

$conn = getConnection();

// Obtener categorías para sidebar
$sql_categorias = "SELECT * FROM categorias ORDER BY nombre";
$categorias_sidebar = $conn->query($sql_categorias);

// Obtener mensajes
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// Calcular total
$total = 0;
$items_carrito = array();

if (!empty($_SESSION['carrito'])) {
    $ids = array_keys($_SESSION['carrito']);
    $ids_str = implode(',', $ids);
    
    $sql = "SELECT * FROM productos WHERE id IN ($ids_str)";
    $result = $conn->query($sql);
    
    while ($producto = $result->fetch_assoc()) {
        $cantidad = $_SESSION['carrito'][$producto['id']];
        $subtotal = $producto['precio'] * $cantidad;
        $total += $subtotal;
        
        $items_carrito[] = array(
            'id' => $producto['id'],
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'imagen' => $producto['imagen'],
            'cantidad' => $cantidad,
            'subtotal' => $subtotal
        );
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Hardware Store</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/carrito.css">
</head>
<body>
    <header>
        <div class="header-left">
            <img src="Images/LogoPucmm.png" alt="Logo PUCMM">
            <h1>Hardware Store</h1>
        </div>
        <div class="header-right">
            <span class="user-name">Hola, <?php echo $_SESSION['nombre']; ?></span>
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
                <a href="mis_pedidos.php" class="categoria-item">
                    <span class="icono">📋</span>
                    <span>Mis Pedidos</span>
                </a>
                <a href="carrito.php" class="categoria-item active">
                    <span class="icono">🛒</span>
                    <span>Carrito</span>
                </a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <div class="contenido-principal">
            <h2 class="page-title">🛒 Mi Carrito de Compras</h2>
        
        <!-- Mensajes -->
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (empty($items_carrito)): ?>
            <div class="carrito-vacio">
                <p>🛒 Tu carrito está vacío</p>
                <a href="index.php" class="btn-continuar">Continuar Comprando</a>
            </div>
        <?php else: ?>
            <div class="carrito-contenido">
                <!-- Lista de productos -->
                <div class="carrito-items">
                    <?php foreach ($items_carrito as $item): ?>
                    <div class="carrito-item">
                        <img src="Images/productos/<?php echo $item['imagen']; ?>" 
                             alt="<?php echo $item['nombre']; ?>"
                             onerror="this.src='https://via.placeholder.com/100x100/3498db/ffffff?text=Producto'">
                        
                        <div class="item-info">
                            <h3><?php echo $item['nombre']; ?></h3>
                            <p class="item-precio">$<?php echo number_format($item['precio'], 2); ?></p>
                        </div>
                        
                        <div class="item-cantidad">
                            <form method="POST" action="procesar_carrito.php" style="display: inline;">
                                <input type="hidden" name="action" value="actualizar">
                                <input type="hidden" name="id_producto" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="cantidad" value="<?php echo $item['cantidad'] - 1; ?>" 
                                        class="btn-cantidad">-</button>
                                <span class="cantidad"><?php echo $item['cantidad']; ?></span>
                                <button type="submit" name="cantidad" value="<?php echo $item['cantidad'] + 1; ?>" 
                                        class="btn-cantidad">+</button>
                            </form>
                        </div>
                        
                        <div class="item-subtotal">
                            <p>$<?php echo number_format($item['subtotal'], 2); ?></p>
                        </div>
                        
                        <form method="POST" action="procesar_carrito.php">
                            <input type="hidden" name="action" value="eliminar">
                            <input type="hidden" name="id_producto" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="btn-eliminar-item" 
                                    onclick="return confirm('¿Eliminar este producto del carrito?')">
                                🗑️
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Resumen -->
                <div class="carrito-resumen">
                    <h3>Resumen del Pedido</h3>
                    <div class="resumen-linea">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="resumen-linea">
                        <span>Envío:</span>
                        <span>Gratis</span>
                    </div>
                    <hr>
                    <div class="resumen-total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    
                    <form method="POST" action="procesar_carrito.php">
                        <input type="hidden" name="action" value="finalizar">
                        <button type="submit" class="btn-finalizar">Finalizar Compra</button>
                    </form>
                    
                    <a href="index.php" class="btn-continuar">Continuar Comprando</a>
                    
                    <form method="POST" action="procesar_carrito.php" style="margin-top: 10px;">
                        <input type="hidden" name="action" value="vaciar">
                        <button type="submit" class="btn-vaciar" 
                                onclick="return confirm('¿Vaciar todo el carrito?')">
                            Vaciar Carrito
                        </button>
                    </form>
                </div>
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
                        <li><a href="mis_pedidos.php">Mis Pedidos</a></li>
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