<?php
require_once 'config/db.php';

$conn = getConnection();
$id_categoria = $_GET['categoria'] ?? 1;

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
    <link rel="stylesheet" href="css/template.css">
    <link rel="stylesheet" href="css/productos.css">
    <script src="jquery-3.7.1.min.js"></script>
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
                <button class="btn-logout" onclick="logout()">Cerrar Sesión</button>
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
        </ul>
    </nav>

    <div class="container">
        <h2 class="page-title"><?php echo $categoria['nombre']; ?></h2>
        
        <?php if (isAdmin()): ?>
        <!-- Panel de Administración -->
        <div class="admin-panel">
            <h3>Panel de Administración</h3>
            <button class="btn-admin" onclick="mostrarFormAgregar()">+ Agregar Producto</button>
            
            <!-- Formulario -->
            <div id="form-agregar" class="form-producto" style="display: none;">
                <h4>Agregar Nuevo Producto</h4>
                <form id="formulario-producto">
                    <input type="hidden" name="action" value="agregar">
                    <input type="hidden" name="id_categoria" value="<?php echo $id_categoria; ?>">
                    
                    <div class="form-group">
                        <label>Nombre del Producto:</label>
                        <input type="text" name="nombre_producto" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="descripcion" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Precio:</label>
                            <input type="number" name="precio" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Stock:</label>
                            <input type="number" name="stock" min="0" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Nombre de Imagen:</label>
                        <input type="text" name="imagen_producto" placeholder="producto.jpg">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Guardar</button>
                        <button type="button" class="btn-cancel" onclick="ocultarFormAgregar()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Productos -->
        <div class="productos-grid">
            <?php while ($producto = $productos->fetch_assoc()): ?>
            <div class="producto-item" data-id="<?php echo $producto['id']; ?>">
                <div class="producto-imagen">
                    <img src="images/productos/<?php echo $producto['imagen']; ?>" 
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
                            <button class="btn-editar" onclick="editarProducto(<?php echo $producto['id']; ?>)">
                                Editar
                            </button>
                            <button class="btn-eliminar" onclick="eliminarProducto(<?php echo $producto['id']; ?>)">
                                Eliminar
                            </button>
                        <?php else: ?>
                            <button class="btn-agregar-carrito">Agregar al Carrito</button>
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

    <script src="js/productos.js"></script>
</body>
</html>

<?php $conn->close(); ?>