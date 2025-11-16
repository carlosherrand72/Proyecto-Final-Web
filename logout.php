<?php
session_start();

// Destruir sesión
$_SESSION = array();
session_destroy();

// Redirigir al inicio
header('Location: index.php');
exit;
?>