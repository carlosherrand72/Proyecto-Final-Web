<?php
// Configuración simple de la base de datos
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'hardware_store';

// Crear conexión
function getConnection() {
    global $host, $user, $pass, $db;
    
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8");
    return $conn;
}

// Iniciar sesión
session_start();

// Verificar si está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Verificar si es admin
function isAdmin() {
    return isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == true;
}
?>