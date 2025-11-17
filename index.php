<?php
require_once 'config/db.php';

$conn = getConnection();

// Obtener mensajes de sesión
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// Obtener categorías
$sql = "SELECT * FROM categorias ORDER BY id";
$categorias = $conn->query($sql);
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
      <?php if (isLoggedIn() && !isAdmin()): ?>
        <li><a href="carrito.php" class="carrito-link">🛒 Carrito (<?php echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?>)</a></li>
      <?php endif; ?>
    </ul>
  </nav>

  <div class="container-main">
    <!-- Mensajes -->
    <?php if ($error): ?>
      <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
  </div>

  <main>
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
    <div class="product-card">
      <div class="product-image">
        <img src="Images/<?php echo $imagen; ?>" 
             alt="<?php echo $categoria['nombre']; ?>">
      </div>
      <div class="product-content">
        <button class="btn-ver-mas" onclick="window.location.href='productos.php?categoria=<?php echo $categoria['id']; ?>'">
          Ver más <?php echo strtolower($categoria['nombre']); ?>
        </button>
      </div>
    </div>
    <?php endwhile; ?>
  </main>

  <footer>
    <div class="footer-izquierda">
      Carlos
    </div>
    <div class="footer-derecha">
        <a href="#">Tarea</a>
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