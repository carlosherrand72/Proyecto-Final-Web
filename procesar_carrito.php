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
    $total_compra = 0;
    
    // Calcular total de la compra
    $ids = array_keys($_SESSION['carrito']);
    $ids_str = implode(',', $ids);
    $sql = "SELECT id, nombre, precio FROM productos WHERE id IN ($ids_str)";
    $result = $conn->query($sql);
    
    $productos_compra = [];
    while ($producto = $result->fetch_assoc()) {
        $cantidad = $_SESSION['carrito'][$producto['id']];
        $subtotal = $producto['precio'] * $cantidad;
        $total_compra += $subtotal;
        $total_unidades += $cantidad;
        
        $productos_compra[] = [
            'id' => $producto['id'],
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'cantidad' => $cantidad,
            'subtotal' => $subtotal
        ];
    }
    
    // GUARDAR PEDIDO EN LA BASE DE DATOS
    $id_usuario = $_SESSION['user_id'];
    $sql_pedido = "INSERT INTO pedidos (id_usuario, total, estado) 
                   VALUES ($id_usuario, $total_compra, 'Completado')";
    
    if ($conn->query($sql_pedido)) {
        $id_pedido = $conn->insert_id; // Obtener ID del pedido creado
        
        // GUARDAR DETALLE DEL PEDIDO (productos comprados)
        foreach ($productos_compra as $prod) {
            $sql_detalle = "INSERT INTO detalle_pedidos 
                           (id_pedido, id_producto, nombre_producto, precio_unitario, cantidad, subtotal) 
                           VALUES 
                           ($id_pedido, {$prod['id']}, '{$prod['nombre']}', {$prod['precio']}, {$prod['cantidad']}, {$prod['subtotal']})";
            $conn->query($sql_detalle);
            
            // Actualizar stock
            $sql_stock = "UPDATE productos SET stock = stock - {$prod['cantidad']} 
                         WHERE id = {$prod['id']} AND stock >= {$prod['cantidad']}";
            $conn->query($sql_stock);
        }
    }
    
    $conn->close();
    
    // Vaciar carrito
    $_SESSION['carrito'] = array();
    
    // Mensaje con información correcta
    if ($total_productos == 1) {
        $_SESSION['success'] = "¡Compra realizada exitosamente! Pedido #$id_pedido. Se procesó $total_unidades " . ($total_unidades == 1 ? "unidad" : "unidades") . ". Total: $" . number_format($total_compra, 2);
    } else {
        $_SESSION['success'] = "¡Compra realizada exitosamente! Pedido #$id_pedido. Se procesaron $total_productos productos ($total_unidades unidades). Total: $" . number_format($total_compra, 2);
    }
    
    header('Location: index.php');
    exit;
}

// Si no hay acción válida, redirigir al carrito
$_SESSION['error'] = 'Acción no válida';
header('Location: carrito.php');
exit;
?>