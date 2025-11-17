<?php
require_once 'config/db.php';

$conn = getConnection();
$id_categoria = $_GET['categoria'] ?? 1;

// Obtener mensajes
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// Obtener producto a editar si existe
$producto_editar = null;
if (isset($_GET['editar'])) {
    $id_editar = $_GET['editar'];
    $sql_editar = "SELECT * FROM productos WHERE id='$id_editar'";
    $producto_editar = $conn->query($sql_editar)->fetch_assoc();
}

// Obtener categoría
$sql_cat = "SELECT * FROM categorias WHERE id='$id_categoria'";
$categoria = $conn->query($sql_cat)->fetch_assoc();

// Obtener productos
$sql_prod = "SELECT * FROM productos WHERE id_categoria='$id_categoria' ORDER BY id DESC";
$productos = $conn->query($sql_prod);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $categoria['nombre']; ?> - Hardware Store</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/productos.css">
</head>
<body>
    <header>
        <div class="header-left">
            <img src="Images/LogoPucmm.png" alt="Logo PUCMM">
            <h1>Hardware Store</h1>
        </div>
        <div class="header-right">
            <?php if (isLoggedIn()): ?>
                <span class="user-name">Hola, <?php echo $_SESSION['nombre']; ?></span>
                <button class="btn-logout" onclick="if(confirm('¿Cerrar sesión?')) window.location.href='logout.php'">
                    Cerrar Sesión
                </button>
            <?php else: ?>
                <button class="btn-login" onclick="window.location.href='login.php'">Login</button>
            <?php endif; ?>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="productos.php?categoria=1">Teclados</a></li>
            <li><a href="productos.php?categoria=2">Mouse</a></li>
            <li><a href="productos.php?categoria=3">Monitores</a></li>
            <li><a href="productos.php?categoria=4">Audífonos</a></li>
            <li><a href="productos.php?categoria=5">GPUs</a></li>
            <li><a href="productos.php?categoria=6">RAM</a></li>
            <?php if (isLoggedIn() && !isAdmin()): ?>
                <li><a href="carrito.php" class="carrito-link">🛒 Carrito (<?php echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?>)</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <h2 class="page-title"><?php echo $categoria['nombre']; ?></h2>
        
        <!-- Mensajes -->
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isAdmin()): ?>
        <!-- Panel de Administración -->
        <div class="admin-panel">
            <h3>Panel de Administración</h3>
            
            <?php if (!$producto_editar): ?>
                <button class="btn-admin" onclick="document.getElementById('form-agregar').style.display='block'">
                    + Agregar Producto
                </button>
            <?php endif; ?>
            
            <!-- Formulario Agregar/Editar -->
            <div id="form-agregar" class="form-producto" style="display: <?php echo $producto_editar ? 'block' : 'none'; ?>;">
                <h4><?php echo $producto_editar ? 'Editar Producto' : 'Agregar Nuevo Producto'; ?></h4>
                <form method="POST" action="procesar_producto.php">
                    <input type="hidden" name="action" value="<?php echo $producto_editar ? 'editar' : 'agregar'; ?>">
                    <input type="hidden" name="id_categoria" value="<?php echo $id_categoria; ?>">
                    <?php if ($producto_editar): ?>
                        <input type="hidden" name="id_producto" value="<?php echo $producto_editar['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Nombre del Producto:</label>
                        <input type="text" name="nombre_producto" required 
                               value="<?php echo $producto_editar['nombre'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="descripcion" rows="3" required><?php echo $producto_editar['descripcion'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Precio:</label>
                            <input type="number" name="precio" step="0.01" min="0" required 
                                   value="<?php echo $producto_editar['precio'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Stock:</label>
                            <input type="number" name="stock" min="0" required 
                                   value="<?php echo $producto_editar['stock'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Nombre de Imagen:</label>
                        <input type="text" name="imagen_producto" placeholder="producto.jpg" 
                               value="<?php echo $producto_editar['imagen'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Guardar</button>
                        <button type="button" class="btn-cancel" 
                                onclick="window.location.href='productos.php?categoria=<?php echo $id_categoria; ?>'">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Productos -->
        <div class="productos-grid">
            <?php while ($producto = $productos->fetch_assoc()): ?>
            <div class="producto-item">
                <div class="producto-imagen">
                    <img src="Images/productos/<?php echo $producto['imagen']; ?>" 
                         alt="<?php echo $producto['nombre']; ?>"
                         onerror="this.src='https://via.placeholder.com/300x300/3498db/ffffff?text=Producto'">
                </div>
                <div class="producto-info">
                    <h3><?php echo $producto['nombre']; ?></h3>
                    <p class="producto-descripcion"><?php echo $producto['descripcion']; ?></p>
                    <p class="producto-precio">$<?php echo number_format($producto['precio'], 2); ?></p>
                    <p class="producto-stock">Stock: <?php echo $producto['stock']; ?> unidades</p>
                    
                    <div class="producto-acciones">
                        <?php if (isAdmin()): ?>
                            <button class="btn-editar" 
                                    onclick="window.location.href='productos.php?categoria=<?php echo $id_categoria; ?>&editar=<?php echo $producto['id']; ?>'">
                                Editar
                            </button>
                            <button class="btn-eliminar" 
                                    onclick="if(confirm('¿Eliminar este producto?')) window.location.href='procesar_producto.php?action=eliminar&id=<?php echo $producto['id']; ?>&categoria=<?php echo $id_categoria; ?>'">
                                Eliminar
                            </button>
                        <?php else: ?>
                            <form method="POST" action="procesar_carrito.php" style="width: 100%;">
                                <input type="hidden" name="action" value="agregar">
                                <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                <input type="hidden" name="categoria" value="<?php echo $id_categoria; ?>">
                                <button type="submit" class="btn-agregar-carrito">Agregar al Carrito 🛒</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <footer>
        <div class="footer-izquierda">
            Carlos
        </div>
        <div class="footer-derecha">
            <a href="#">Tarea</a>
        </div>
    </footer>
</body>
</html>

<?php $conn->close(); ?>