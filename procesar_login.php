<?php
require_once 'config/db.php';

$conn = getConnection();
$action = $_POST['action'] ?? '';

// PROCESAR LOGIN
if ($action == 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM usuarios WHERE email='$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['es_admin'] = $usuario['es_admin'];
            
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['error'] = 'Contraseña incorrecta';
            header('Location: login.php');
            exit;
        }
    } else {
        $_SESSION['error'] = 'Usuario no encontrado';
        header('Location: login.php');
        exit;
    }
}

// PROCESAR REGISTRO
else if ($action == 'registro') {
    $nombre = $_POST['nombre'] . ' ' . $_POST['apellido'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    // Validar contraseñas
    if ($password !== $password_confirm) {
        $_SESSION['error'] = 'Las contraseñas no coinciden';
        header('Location: login.php?tab=registro');
        exit;
    }
    
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres';
        header('Location: login.php?tab=registro');
        exit;
    }
    
    // Verificar si el email ya existe
    $check = "SELECT id FROM usuarios WHERE email='$email'";
    if ($conn->query($check)->num_rows > 0) {
        $_SESSION['error'] = 'El email ya está registrado';
        header('Location: login.php?tab=registro');
        exit;
    }
    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre', '$email', '$password_hash')";
    
    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Cuenta creada exitosamente. Ya puede iniciar sesión';
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error al crear la cuenta';
        header('Location: login.php?tab=registro');
        exit;
    }
}

$conn->close();
?>