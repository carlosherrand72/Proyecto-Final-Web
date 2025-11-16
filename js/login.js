// Cambiar entre tabs de login y registro
function mostrarTab(tab) {
    $('.tab-btn').removeClass('active');
    $('.tab-content').removeClass('active');
    
    if (tab === 'login') {
        $('.tab-btn:first').addClass('active');
        $('#tab-login').addClass('active');
    } else {
        $('.tab-btn:last').addClass('active');
        $('#tab-registro').addClass('active');
    }
}

// Manejar el envío del formulario de login
$('#form-login').on('submit', function(e) {
    e.preventDefault();
    
    const email = $(this).find('input[name="email"]').val();
    const password = $(this).find('input[name="password"]').val();
    
    $.ajax({
        url: 'api/auth_api.php',
        method: 'POST',
        data: {
            action: 'login',
            email: email,
            password: password
        },
        dataType: 'json',
        beforeSend: function() {
            $('#mensaje-login').html('<p class="loading">Iniciando sesión...</p>');
        },
        success: function(response) {
            if (response.success) {
                $('#mensaje-login').html('<p class="success">' + response.message + '</p>');
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 1000);
            } else {
                $('#mensaje-login').html('<p class="error">' + response.message + '</p>');
            }
        },
        error: function() {
            $('#mensaje-login').html('<p class="error">Error de conexión. Intente nuevamente.</p>');
        }
    });
});

// Manejar el envío del formulario de registro
$('#form-registro').on('submit', function(e) {
    e.preventDefault();
    
    const password = $('#password').val();
    const password_confirm = $('#password_confirm').val();
    
    // Validar que las contraseñas coincidan
    if (password !== password_confirm) {
        $('#mensaje-registro').html('<p class="error">Las contraseñas no coinciden</p>');
        return;
    }
    
    // Validar longitud de contraseña
    if (password.length < 6) {
        $('#mensaje-registro').html('<p class="error">La contraseña debe tener al menos 6 caracteres</p>');
        return;
    }
    
    $.ajax({
        url: 'api/auth_api.php',
        method: 'POST',
        data: $(this).serialize() + '&action=registro',
        dataType: 'json',
        beforeSend: function() {
            $('#mensaje-registro').html('<p class="loading">Creando cuenta...</p>');
        },
        success: function(response) {
            if (response.success) {
                $('#mensaje-registro').html('<p class="success">' + response.message + '</p>');
                setTimeout(function() {
                    mostrarTab('login');
                    $('#form-registro')[0].reset();
                    $('#mensaje-registro').html('');
                }, 2000);
            } else {
                $('#mensaje-registro').html('<p class="error">' + response.message + '</p>');
            }
        },
        error: function() {
            $('#mensaje-registro').html('<p class="error">Error de conexión. Intente nuevamente.</p>');
        }
    });
});