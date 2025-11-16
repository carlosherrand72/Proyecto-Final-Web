<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hardware Store</title>
    <link rel="stylesheet" href="css/template.css">
    <link rel="stylesheet" href="css/login.css">
    <script src="jquery-3.7.1.min.js"></script>
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
            <!-- Tabs para cambiar entre Login y Registro -->
            <div class="tabs">
                <button class="tab-btn active" onclick="mostrarTab('login')">Iniciar Sesión</button>
                <button class="tab-btn" onclick="mostrarTab('registro')">Registrarse</button>
            </div>

            <!-- Formulario de Login -->
            <div id="tab-login" class="tab-content active">
                <h2>Iniciar Sesión</h2>
                <form id="form-login">
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required placeholder="tu@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label>Contraseña:</label>
                        <input type="password" name="password" required placeholder="••••••••">
                    </div>
                    
                    <button type="submit" class="btn-submit">Entrar</button>
                    
                    <div class="mensaje" id="mensaje-login"></div>
                </form>
            </div>

            <!-- Formulario de Registro -->
            <div id="tab-registro" class="tab-content">
                <h2>Crear Cuenta</h2>
                <form id="form-registro">
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
                    
                    <div class="form-group">
                        <label>Teléfono:</label>
                        <input type="tel" name="telefono" placeholder="(809) 555-5555">
                    </div>
                    
                    <div class="form-group">
                        <label>Dirección:</label>
                        <textarea name="direccion" rows="2" placeholder="Calle, número, ciudad"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Contraseña:</label>
                            <input type="password" name="password" required placeholder="••••••••" id="password">
                        </div>
                        
                        <div class="form-group">
                            <label>Confirmar Contraseña:</label>
                            <input type="password" name="password_confirm" required placeholder="••••••••" id="password_confirm">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit">Registrarse</button>
                    
                    <div class="mensaje" id="mensaje-registro"></div>
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

    <script src="js/login.js"></script>
</body>
</html>