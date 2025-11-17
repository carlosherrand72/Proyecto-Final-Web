<?php
require_once 'config/db.php';

// Obtener mensajes de sesión
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// Determinar qué tab mostrar
$tab_activo = $_GET['tab'] ?? 'login';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hardware Store</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <header>
        <div class="header-left">
            <img src="Images/LogoPucmm.png" alt="Logo PUCMM">
            <h1>Hardware Store</h1>
        </div>
        <button class="btn-login" onclick="window.location.href='index.php'">Volver al Inicio</button>
    </header>

    <div class="login-container">
        <div class="login-box">
            <!-- Tabs -->
            <div class="tabs">
                <button class="tab-btn <?php echo $tab_activo == 'login' ? 'active' : ''; ?>" 
                        onclick="window.location.href='login.php?tab=login'">
                    Iniciar Sesión
                </button>
                <button class="tab-btn <?php echo $tab_activo == 'registro' ? 'active' : ''; ?>" 
                        onclick="window.location.href='login.php?tab=registro'">
                    Registrarse
                </button>
            </div>

            <!-- Mensajes -->
            <?php if ($error): ?>
                <div class="mensaje">
                    <p class="error"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="mensaje">
                    <p class="success"><?php echo $success; ?></p>
                </div>
            <?php endif; ?>

            <!-- Formulario de Login -->
            <div id="tab-login" class="tab-content <?php echo $tab_activo == 'login' ? 'active' : ''; ?>">
                <h2>Iniciar Sesión</h2>
                <form method="POST" action="procesar_login.php">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required placeholder="tu@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label>Contraseña:</label>
                        <input type="password" name="password" required placeholder="••••••••">
                    </div>
                    
                    <button type="submit" class="btn-submit">Entrar</button>
                </form>
            </div>

            <!-- Formulario de Registro -->
            <div id="tab-registro" class="tab-content <?php echo $tab_activo == 'registro' ? 'active' : ''; ?>">
                <h2>Crear Cuenta</h2>
                <form method="POST" action="procesar_login.php">
                    <input type="hidden" name="action" value="registro">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="nombre" required placeholder="Juan">
                        </div>
                        
                        <div class="form-group">
                            <label>Apellido:</label>
                            <input type="text" name="apellido" required placeholder="Pérez">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required placeholder="tu@email.com">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Contraseña:</label>
                            <input type="password" name="password" required placeholder="••••••••" minlength="6">
                        </div>
                        
                        <div class="form-group">
                            <label>Confirmar Contraseña:</label>
                            <input type="password" name="password_confirm" required placeholder="••••••••" minlength="6">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit">Registrarse</button>
                </form>
            </div>
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