<?php
require_once 'config/db.php';

// Verificar que sea administrador
if (!isAdmin()) {
    $_SESSION['error'] = 'Acceso denegado. Solo administradores.';
    header('Location: index.php');
    exit;
}

$conn = getConnection();

// Obtener mensajes
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// Obtener categoría a editar si existe
$categoria_editar = null;
if (isset($_GET['editar'])) {
    $id_editar = intval($_GET['editar']);
    $sql_editar = "SELECT * FROM categorias WHERE id='$id_editar'";
    $categoria_editar = $conn->query($sql_editar)->fetch_assoc();
}

// Obtener todas las categorías
$sql_categorias = "SELECT * FROM categorias ORDER BY id ASC";
$categorias = $conn->query($sql_categorias);

// Obtener categorías para sidebar
$sql_sidebar = "SELECT * FROM categorias ORDER BY nombre";
$categorias_sidebar = $conn->query($sql_sidebar);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Categorías - Hardware Store</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/admin_categorias.css">
</head>
<body>
    <header>
        <div class="header-left">
            <img src="Images/LogoPucmm.png" alt="Logo PUCMM">
            <h1>Hardware Store</h1>
        </div>
        <div class="header-right">
            <span class="user-name">Hola, <?php echo $_SESSION['nombre']; ?></span>
            <button class="btn-dashboard" onclick="window.location.href='dashboard.php'">
                📊 Dashboard
            </button>
            <button class="btn-logout" onclick="if(confirm('¿Cerrar sesión?')) window.location.href='logout.php'">
                Cerrar Sesión
            </button>
        </div>
    </header>

    <div class="container-principal">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Navegación</h2>
            <ul class="categorias-list">
                <li>
                    <a href="index.php" class="categoria-item">
                        <span class="icono">🏠</span>
                        <span>Inicio</span>
                    </a>
                </li>
                <li>
                    <a href="admin_categorias.php" class="categoria-item active">
                        <span class="icono">⚙️</span>
                        <span>Gestionar Categorías</span>
                    </a>
                </li>
                <li>
                    <a href="dashboard.php" class="categoria-item">
                        <span class="icono">📊</span>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-admin">
                <hr>
                <h3>Categorías Actuales</h3>
                <?php while ($cat = $categorias_sidebar->fetch_assoc()): ?>
                    <a href="productos.php?categoria=<?php echo $cat['id']; ?>" class="categoria-mini">
                        📦 <?php echo htmlspecialchars($cat['nombre']); ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <div class="contenido-principal">
            <h2 class="page-title">⚙️ Gestionar Categorías</h2>

            <!-- Mensajes -->
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Formulario Agregar/Editar Categoría -->
            <div class="admin-panel">
                <h3><?php echo $categoria_editar ? 'Editar Categoría' : 'Agregar Nueva Categoría'; ?></h3>
                
                <form method="POST" action="procesar_categoria.php" class="form-categoria">
                    <input type="hidden" name="action" value="<?php echo $categoria_editar ? 'editar' : 'agregar'; ?>">
                    <?php if ($categoria_editar): ?>
                        <input type="hidden" name="id_categoria" value="<?php echo $categoria_editar['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Nombre de la Categoría:</label>
                        <input type="text" name="nombre" required 
                               placeholder="Ej: Laptops, Impresoras, etc."
                               value="<?php echo $categoria_editar['nombre'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Nombre de la Imagen:</label>
                        <input type="text" name="imagen" 
                               placeholder="Ej: laptop.png"
                               value="<?php echo $categoria_editar['imagen'] ?? ''; ?>">
                        <small>La imagen debe estar en la carpeta Images/</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <?php echo $categoria_editar ? 'Actualizar' : 'Agregar'; ?> Categoría
                        </button>
                        <?php if ($categoria_editar): ?>
                            <button type="button" class="btn-cancel" onclick="window.location.href='admin_categorias.php'">
                                Cancelar
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Lista de Categorías -->
            <div class="categorias-lista">
                <h3>Categorías Existentes</h3>
                
                <div class="tabla-categorias">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Imagen</th>
                                <th>Productos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $categorias->data_seek(0); // Reiniciar el puntero
                            while ($cat = $categorias->fetch_assoc()): 
                                // Contar productos en esta categoría
                                $id_cat = $cat['id'];
                                $sql_count = "SELECT COUNT(*) as total FROM productos WHERE id_categoria='$id_cat'";
                                $total_productos = $conn->query($sql_count)->fetch_assoc()['total'];
                            ?>
                            <tr>
                                <td><?php echo $cat['id']; ?></td>
                                <td class="nombre-categoria">
                                    <strong><?php echo htmlspecialchars($cat['nombre']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($cat['imagen']); ?></td>
                                <td>
                                    <span class="badge-productos"><?php echo $total_productos; ?> productos</span>
                                </td>
                                <td class="acciones">
                                    <button class="btn-editar-small" 
                                            onclick="window.location.href='admin_categorias.php?editar=<?php echo $cat['id']; ?>'">
                                        Editar
                                    </button>
                                    <button class="btn-ver-small" 
                                            onclick="window.location.href='productos.php?categoria=<?php echo $cat['id']; ?>'">
                                         Ver Productos
                                    </button>
                                    <?php if ($total_productos == 0): ?>
                                        <button class="btn-eliminar-small" 
                                                onclick="if(confirm('¿Eliminar categoría <?php echo $cat['nombre']; ?>?')) window.location.href='procesar_categoria.php?action=eliminar&id=<?php echo $cat['id']; ?>'">
                                             Eliminar
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-eliminar-small" disabled title="No se puede eliminar una categoría con productos">
                                             Eliminar
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section footer-about">
                    <h3>Hardware Store</h3>
                    <p>Panel de Administración</p>
                </div>
                
                <div class="footer-section footer-links">
                    <h3>Administración</h3>
                    <ul>
                        <li><a href="admin_categorias.php">Gestionar Categorías</a></li>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="index.php">Ver Tienda</a></li>
                    </ul>
                </div>
                
                <div class="footer-section footer-contact">
                    <h3>Soporte</h3>
                    <p><span>📧</span> admin@hardwarestore.com</p>
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