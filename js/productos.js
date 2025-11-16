// Mostrar formulario de agregar producto
function mostrarFormAgregar() {
    $('#form-agregar').slideDown();
}

// Ocultar formulario de agregar producto
function ocultarFormAgregar() {
    $('#form-agregar').slideUp();
    $('#formulario-producto')[0].reset();
}

// Enviar formulario de producto (agregar/editar)
$('#formulario-producto').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'api/productos_api.php',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error al procesar la solicitud');
        }
    });
});

// Eliminar producto
function eliminarProducto(id) {
    if (!confirm('¿Está seguro de eliminar este producto?')) {
        return;
    }
    
    $.ajax({
        url: 'api/productos_api.php',
        method: 'POST',
        data: {
            action: 'eliminar',
            id_producto: id
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                $('[data-id="' + id + '"]').fadeOut(300, function() {
                    $(this).remove();
                });
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error al eliminar el producto');
        }
    });
}

// Editar producto
function editarProducto(id) {
    // Obtener datos del producto
    $.ajax({
        url: 'api/productos_api.php',
        method: 'GET',
        data: {
            action: 'obtener',
            id_producto: id
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const producto = response.data;
                
                // Llenar el formulario con los datos
                $('#formulario-producto input[name="action"]').val('editar');
                $('#formulario-producto').append('<input type="hidden" name="id_producto" value="' + id + '">');
                $('#formulario-producto input[name="nombre_producto"]').val(producto.nombre_producto);
                $('#formulario-producto textarea[name="descripcion"]').val(producto.descripcion);
                $('#formulario-producto input[name="precio"]').val(producto.precio);
                $('#formulario-producto input[name="stock"]').val(producto.stock);
                $('#formulario-producto input[name="imagen_producto"]').val(producto.imagen_producto);
                
                // Cambiar título del formulario
                $('#form-agregar h4').text('Editar Producto');
                
                // Mostrar formulario
                mostrarFormAgregar();
            }
        }
    });
}

// Agregar al carrito
function agregarAlCarrito(id) {
    $.ajax({
        url: 'api/carrito_api.php',
        method: 'POST',
        data: {
            action: 'agregar',
            id_producto: id,
            cantidad: 1
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Producto agregado al carrito');
                // Actualizar contador del carrito si existe
                actualizarContadorCarrito();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error al agregar al carrito');
        }
    });
}

// Actualizar contador del carrito
function actualizarContadorCarrito() {
    $.ajax({
        url: 'api/carrito_api.php',
        method: 'GET',
        data: { action: 'contar' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('.carrito-contador').text(response.cantidad);
            }
        }
    });
}

// Cerrar sesión
function logout() {
    if (confirm('¿Desea cerrar sesión?')) {
        window.location.href = 'api/logout.php';
    }
}

// Cargar contador del carrito al cargar la página
$(document).ready(function() {
    actualizarContadorCarrito();
});