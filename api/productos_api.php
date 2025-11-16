<?php
require_once '../config/db.php';
header('Content-Type: application/json');

// Solo admins pueden modificar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos']);
    exit;
}

$conn = getConnection();
$action = $_REQUEST['action'] ?? '';

// Agregar producto
if ($action == 'agregar') {
    $id_categoria = $_POST['id_categoria'];
    $nombre = $_POST['nombre_producto'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen = $_POST['imagen_producto'];
    
    $sql = "INSERT INTO productos (id_categoria, nombre, descripcion, precio, stock, imagen) 
            VALUES ('$id_categoria', '$nombre', '$descripcion', '$precio', '$stock', '$imagen')";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Producto agregado']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar']);
    }
}

// Editar producto
else if ($action == 'editar') {
    $id = $_POST['id_producto'];
    $nombre = $_POST['nombre_producto'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen = $_POST['imagen_producto'];
    
    $sql = "UPDATE productos 
            SET nombre='$nombre', descripcion='$descripcion', precio='$precio', 
                stock='$stock', imagen='$imagen' 
            WHERE id='$id'";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Producto actualizado']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
    }
}

// Eliminar producto
else if ($action == 'eliminar') {
    $id = $_POST['id_producto'];
    $sql = "DELETE FROM productos WHERE id='$id'";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Producto eliminado']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
    }
}

// Obtener producto
else if ($action == 'obtener') {
    $id = $_GET['id_producto'];
    $sql = "SELECT * FROM productos WHERE id='$id'";
    $result = $conn->query($sql);
    
    if ($producto = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'data' => $producto]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No encontrado']);
    }
}

$conn->close();
?>