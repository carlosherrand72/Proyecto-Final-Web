<?php
require_once 'config/db.php';

// Verificar que sea administrador
if (!isAdmin()) {
    $_SESSION['error'] = 'No tienes permisos de administrador';
    header('Location: index.php');
    exit;
}

$conn = getConnection();
$action = $_POST['action'] ?? '';
$id_categoria = $_POST['id_categoria'] ?? $_GET['categoria'] ?? 1;

// AGREGAR PRODUCTO
if ($action == 'agregar') {
    $nombre = $_POST['nombre_producto'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen = $_POST['imagen_producto'];
    
    $sql = "INSERT INTO productos (id_categoria, nombre, descripcion, precio, stock, imagen) 
            VALUES ('$id_categoria', '$nombre', '$descripcion', '$precio', '$stock', '$imagen')";
    
    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Producto agregado exitosamente';
    } else {
        $_SESSION['error'] = 'Error al agregar producto';
    }
    
    header("Location: productos.php?categoria=$id_categoria");
    exit;
}

// EDITAR PRODUCTO
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
        $_SESSION['success'] = 'Producto actualizado exitosamente';
    } else {
        $_SESSION['error'] = 'Error al actualizar producto';
    }
    
    header("Location: productos.php?categoria=$id_categoria");
    exit;
}

// ELIMINAR PRODUCTO
else if ($action == 'eliminar') {
    $id = $_GET['id'];
    
    $sql = "DELETE FROM productos WHERE id='$id'";
    
    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Producto eliminado exitosamente';
    } else {
        $_SESSION['error'] = 'Error al eliminar producto';
    }
    
    header("Location: productos.php?categoria=$id_categoria");
    exit;
}

$conn->close();
?>