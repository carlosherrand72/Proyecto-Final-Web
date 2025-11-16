<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$conn = getConnection();
$action = $_POST['action'] ?? '';

// LOGIN
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
            
            echo json_encode([
                'success' => true, 
                'message' => 'Bienvenido ' . $usuario['nombre']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    }
}

// REGISTRO
else if ($action == 'registro') {
    $nombre = $_POST['nombre'] . ' ' . $_POST['apellido'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Verificar si el email ya existe
    $check = "SELECT id FROM usuarios WHERE email='$email'";
    if ($conn->query($check)->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'El email ya está registrado']);
        exit;
    }
    
    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre', '$email', '$password')";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Cuenta creada exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear cuenta']);
    }
}

$conn->close();
?>