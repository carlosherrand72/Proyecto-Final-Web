<?php
require_once 'config/db.php';

$conn = getConnection();

// Obtener categorías
$sql = "SELECT * FROM categorias ORDER BY id";
$categorias = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>Hardware Store - Inicio</title>
  <link rel="stylesheet" href="css/index.css">
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

  <main>
    <?php while ($categoria = $categorias->fetch_assoc()): ?>
    <div class="product-card">
      <div class="product-image">
        <img src="Images/<?php echo $categoria['imagen']; ?>" 
             alt="<?php echo $categoria['nombre']; ?>"
             onerror="this.src='https://via.placeholder.com/300x300/3498db/ffffff?text=<?php echo urlencode($categoria['nombre']); ?>'">
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
        window.location.href = 'api/logout.php';
      }
    }
  </script>
</body>
</html>

<?php $conn->close(); ?>