<?php
require_once 'config/db.php';

// Verificar que sea administrador
if (!isAdmin()) {
    $_SESSION['error'] = 'Acceso denegado';
    header('Location: index.php');
    exit;
}

$conn = getConnection();
$action = $_REQUEST['action'] ?? '';

// AGREGAR CATEGORÍA
if ($action == 'agregar') {
    $nombre = trim($_POST['nombre']);
    $imagen = trim($_POST['imagen']);
    
    if (empty($nombre)) {
        $_SESSION['error'] = 'El nombre de la categoría es obligatorio';
        header('Location: admin_categorias.php');
        exit;
    }
    
    // Verificar si ya existe
    $sql_check = "SELECT id FROM categorias WHERE nombre='$nombre'";
    if ($conn->query($sql_check)->num_rows > 0) {
        $_SESSION['error'] = 'Ya existe una categoría con ese nombre';
        header('Location: admin_categorias.php');
        exit;
    }
    
    $sql = "INSERT INTO categorias (nombre, imagen) VALUES ('$nombre', '$imagen')";
    
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Categoría '$nombre' agregada exitosamente";
    } else {
        $_SESSION['error'] = 'Error al agregar la categoría';
    }
    
    header('Location: admin_categorias.php');
    exit;
}

// EDITAR CATEGORÍA
else if ($action == 'editar') {
    $id = intval($_POST['id_categoria']);
    $nombre = trim($_POST['nombre']);
    $imagen = trim($_POST['imagen']);
    
    if (empty($nombre)) {
        $_SESSION['error'] = 'El nombre de la categoría es obligatorio';
        header('Location: admin_categorias.php?editar=' . $id);
        exit;
    }
    
    $sql = "UPDATE categorias SET nombre='$nombre', imagen='$imagen' WHERE id='$id'";
    
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Categoría actualizada exitosamente";
    } else {
        $_SESSION['error'] = 'Error al actualizar la categoría';
    }
    
    header('Location: admin_categorias.php');
    exit;
}

// ELIMINAR CATEGORÍA
else if ($action == 'eliminar') {
    $id = intval($_GET['id']);
    
    // Verificar que no tenga productos
    $sql_check = "SELECT COUNT(*) as total FROM productos WHERE id_categoria='$id'";
    $total = $conn->query($sql_check)->fetch_assoc()['total'];
    
    if ($total > 0) {
        $_SESSION['error'] = 'No se puede eliminar una categoría que tiene productos asociados';
        header('Location: admin_categorias.php');
        exit;
    }
    
    $sql = "DELETE FROM categorias WHERE id='$id'";
    
    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Categoría eliminada exitosamente';
    } else {
        $_SESSION['error'] = 'Error al eliminar la categoría';
    }
    
    header('Location: admin_categorias.php');
    exit;
}

$conn->close();
header('Location: admin_categorias.php');
exit;
?>