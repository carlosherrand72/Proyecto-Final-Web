<?php
require_once 'config/db.php';

$conn = getConnection();

// Obtener mensajes de sesión
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// Obtener categorías para sidebar
$sql_categorias = "SELECT * FROM categorias ORDER BY nombre";
$categorias_sidebar = $conn->query($sql_categorias);

// Obtener todas las categorías para mostrar
$sql_productos = "SELECT * FROM categorias ORDER BY id";
$categorias = $conn->query($sql_productos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>Hardware Store - Inicio</title>
  <link rel="stylesheet" href="css/estilos.css">
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
    <?php if (isAdmin()): ?>
      <button class="btn-dashboard" onclick="window.location.href='dashboard.php'">
        📊 Dashboard
      </button>
    <?php else: ?>
      <button class="btn-pedidos" onclick="window.location.href='mis_pedidos.php'">
        📋 Mis Pedidos
      </button>
      <button class="btn-carrito" onclick="window.location.href='carrito.php'">
        🛒 Carrito (<?php echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?>)
      </button>
    <?php endif; ?>
    <button class="btn-logout" onclick="logout()">Cerrar Sesión</button>
  <?php else: ?>
    <button class="btn-login" onclick="window.location.href='login.php'">Login</button>
  <?php endif; ?>
</div>
  </header>

  <div class="container-principal">
    <!-- Sidebar de Categorías -->
    <aside class="sidebar">
      <h2>Categorías</h2>
      <ul class="categorias-list">
        <li>
          <a href="index.php" class="categoria-item active">
            <span class="icono">🏠</span>
            <span>Todas las Categorías</span>
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
      
      <?php if (isAdmin()): ?>
      <div class="sidebar-admin">
        <hr>
        <h3>Administración</h3>
        <a href="admin_categorias.php" class="btn-admin-sidebar">
          ⚙️ Gestionar Categorías
        </a>
      </div>
      <?php endif; ?>
    </aside>

    <!-- Contenido Principal -->
    <div class="contenido-principal">
      <!-- Mensajes -->
      <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
      <?php endif; ?>
      
      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
      <?php endif; ?>
      
      <h2 class="titulo-seccion">Explora Nuestras Categorías</h2>
      
      <!-- Grid de Categorías -->
      <div class="categorias-grid">
        <?php 
        // Mapeo de categorías a imágenes genéricas
        $imagenes_categorias = [
            1 => 'teclado.png',
            2 => 'mouse.png',
            3 => 'monitor.png',
            4 => 'audifonos.png',
            5 => 'gpu.png',
            6 => 'ram.png'
        ];
        
        while ($categoria = $categorias->fetch_assoc()): 
            $imagen = $imagenes_categorias[$categoria['id']] ?? 'default.png';
        ?>
        <a href="productos.php?categoria=<?php echo $categoria['id']; ?>" class="product-card-link">
          <div class="product-card">
            <div class="product-image">
              <img src="Images/<?php echo $imagen; ?>" 
                   alt="<?php echo $categoria['nombre']; ?>">
            </div>
            <div class="product-content">
              <h3><?php echo htmlspecialchars($categoria['nombre']); ?></h3>
              <button class="btn-ver-mas" onclick="event.stopPropagation()">
                Ver productos
              </button>
            </div>
          </div>
        </a>
        <?php endwhile; ?>
      </div>
    </div>
  </div>

  <footer>
    <div class="footer-container">
      <div class="footer-content">
        <!-- Sobre Nosotros -->
        <div class="footer-section footer-about">
          <h3>Hardware Store</h3>
          <p>Tu tienda de confianza para componentes de hardware y accesorios tecnológicos. Ofrecemos los mejores productos al mejor precio.</p>
          <p>Calidad garantizada y envío rápido a todo el país.</p>
        </div>
        
        <!-- Enlaces Rápidos -->
        <div class="footer-section footer-links">
          <h3>Enlaces Rápidos</h3>
          <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="productos.php?categoria=1">Productos</a></li>
            <?php if (isLoggedIn() && !isAdmin()): ?>
              <li><a href="mis_pedidos.php">Mis Pedidos</a></li>
              <li><a href="carrito.php">Carrito</a></li>
            <?php endif; ?>
            <li><a href="login.php">Mi Cuenta</a></li>
          </ul>
        </div>
        
        <!-- Categorías -->
        <div class="footer-section footer-links">
          <h3>Categorías</h3>
          <ul>
            <li><a href="productos.php?categoria=1">Teclados</a></li>
            <li><a href="productos.php?categoria=2">Mouse</a></li>
            <li><a href="productos.php?categoria=3">Monitores</a></li>
            <li><a href="productos.php?categoria=4">Audífonos</a></li>
            <li><a href="productos.php?categoria=5">GPUs</a></li>
            <li><a href="productos.php?categoria=6">RAM</a></li>
          </ul>
        </div>
        
        <!-- Contacto -->
        <div class="footer-section footer-contact">
          <h3>Contáctanos</h3>
          <p><span>📍</span> Av. Abraham Lincoln, Santo Domingo, RD</p>
          <p><span>📞</span> +1 (809) 555-0100</p>
          <p><span>📧</span> info@hardwarestore.com</p>
          <p><span>🕐</span> Lun - Vie: 9:00 AM - 6:00 PM</p>
          
          <div class="footer-social">
            <a href="#" class="social-icon" title="Facebook">📘</a>
            <a href="#" class="social-icon" title="Instagram">📷</a>
            <a href="#" class="social-icon" title="WhatsApp">💬</a>
          </div>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; 2025 Hardware Store. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>

  <script>
    function logout() {
      if (confirm('¿Desea cerrar sesión?')) {
        window.location.href = 'logout.php';
      }
    }
  </script>
</body>
</html>

<?php $conn->close(); ?>