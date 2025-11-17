<?php
require_once 'config/db.php';

// Verificar que el usuario esté logueado
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Debes iniciar sesión';
    header('Location: login.php');
    exit;
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id_producto = intval($_POST['id_producto'] ?? $_GET['id_producto'] ?? 0);

// AGREGAR AL CARRITO
if ($action == 'agregar') {
    if ($id_producto > 0) {
        if (isset($_SESSION['carrito'][$id_producto])) {
            $_SESSION['carrito'][$id_producto]++;
        } else {
            $_SESSION['carrito'][$id_producto] = 1;
        }
        
        $_SESSION['success'] = 'Producto agregado al carrito';
    }
    
    // Redirigir de vuelta a la categoría
    $categoria = intval($_GET['categoria'] ?? $_POST['categoria'] ?? 1);
    header("Location: productos.php?categoria=$categoria");
    exit;
}

// ACTUALIZAR CANTIDAD
else if ($action == 'actualizar') {
    $cantidad = intval($_POST['cantidad'] ?? 0);
    
    if ($id_producto > 0) {
        if ($cantidad > 0) {
            $_SESSION['carrito'][$id_producto] = $cantidad;
            $_SESSION['success'] = 'Cantidad actualizada';
        } else {
            unset($_SESSION['carrito'][$id_producto]);
            $_SESSION['success'] = 'Producto eliminado del carrito';
        }
    }
    
    header('Location: carrito.php');
    exit;
}

// ELIMINAR DEL CARRITO
else if ($action == 'eliminar') {
    if ($id_producto > 0 && isset($_SESSION['carrito'][$id_producto])) {
        unset($_SESSION['carrito'][$id_producto]);
        $_SESSION['success'] = 'Producto eliminado del carrito';
    }
    
    header('Location: carrito.php');
    exit;
}

// VACIAR CARRITO
else if ($action == 'vaciar') {
    $_SESSION['carrito'] = array();
    $_SESSION['success'] = 'Carrito vaciado';
    
    header('Location: carrito.php');
    exit;
}

// FINALIZAR COMPRA
else if ($action == 'finalizar') {
    if (empty($_SESSION['carrito'])) {
        $_SESSION['error'] = 'El carrito está vacío';
        header('Location: carrito.php');
        exit;
    }
    
    $conn = getConnection();
    
    // Contar productos y unidades totales
    $total_productos = count($_SESSION['carrito']);
    $total_unidades = 0;
    
    // Actualizar el stock de cada producto
    foreach ($_SESSION['carrito'] as $id => $cantidad) {
        $total_unidades += $cantidad;
        
        // Restar del stock
        $sql = "UPDATE productos SET stock = stock - $cantidad WHERE id = $id AND stock >= $cantidad";
        $conn->query($sql);
    }
    
    $conn->close();
    
    // Vaciar carrito
    $_SESSION['carrito'] = array();
    
    // Mensaje con información correcta
    if ($total_productos == 1) {
        $_SESSION['success'] = "¡Compra realizada exitosamente! Se procesó $total_unidades " . ($total_unidades == 1 ? "unidad" : "unidades") . ". Gracias por tu pedido.";
    } else {
        $_SESSION['success'] = "¡Compra realizada exitosamente! Se procesaron $total_productos productos ($total_unidades unidades en total). Gracias por tu pedido.";
    }
    
    header('Location: index.php');
    exit;
}

// Si no hay acción válida, redirigir al carrito
$_SESSION['error'] = 'Acción no válida';
header('Location: carrito.php');
exit;
?>