<?php
require_once 'config/db.php';

$conn = getConnection();

// Datos del administrador
$nombre = 'Administrador';
$email = 'admin@tienda.com';
$password = 'admin123';

// Encriptar la contraseña correctamente
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Verificar si ya existe
$check = "SELECT id FROM usuarios WHERE email='$email'";
$result = $conn->query($check);

if ($result->num_rows > 0) {
    // Si existe, actualizar la contraseña
    $sql = "UPDATE usuarios SET password='$password_hash', es_admin=1 WHERE email='$email'";
    if ($conn->query($sql)) {
        echo "✅ Administrador actualizado correctamente<br>";
    } else {
        echo "❌ Error al actualizar: " . $conn->error . "<br>";
    }
} else {
    // Si no existe, crear nuevo
    $sql = "INSERT INTO usuarios (nombre, email, password, es_admin) 
            VALUES ('$nombre', '$email', '$password_hash', 1)";
    if ($conn->query($sql)) {
        echo "✅ Administrador creado correctamente<br>";
    } else {
        echo "❌ Error al crear: " . $conn->error . "<br>";
    }
}

echo "<br><strong>Credenciales:</strong><br>";
echo "Email: admin@tienda.com<br>";
echo "Password: admin123<br>";
echo "<br><a href='login.php'>Ir a Login</a><br>";
echo "<br><strong>⚠️ IMPORTANTE: Elimina este archivo (crear_admin.php) después de usarlo por seguridad.</strong>";

$conn->close();
?>