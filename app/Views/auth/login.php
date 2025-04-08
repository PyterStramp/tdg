<!-- Vista de inicio de sesión -->
<div class="login-container">
    <div class="login-header">
        <h1>Sistema de Módulos</h1>
        <p>Ingrese sus credenciales para acceder</p>
    </div>
    
    <?php showFlashMessage(); ?>
    
    <form action="<?php echo baseUrl('auth/authenticate'); ?>" method="post" class="login-form">
        <!-- Campo oculto para CSRF -->
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        
        <div class="form-group">
            <label for="username">Usuario o Email</label>
            <input type="text" id="username" name="username" required autofocus>
        </div>
        
        <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="password-field">
                <input type="password" id="password" name="password" required>
                <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </div>
        
        <div class="form-footer">
            <p>¿Olvidó su contraseña? <a class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" href="<?php echo baseUrl('auth/recover'); ?>">Recuperar</a></p>
        </div>
    </form>
</div>

<style>
    .password-field {
        position: relative;
    }
    
    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--gray-color);
        cursor: pointer;
    }
    
    .toggle-password:hover {
        color: var(--primary-color);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Botón de mostrar/ocultar contraseña
        const toggleButton = document.querySelector('.toggle-password');
        if (toggleButton) {
            toggleButton.addEventListener('click', function() {
                const input = document.getElementById('password');
                const type = input.type === 'password' ? 'text' : 'password';
                input.type = type;
                
                // Cambiar icono
                const icon = this.querySelector('i');
                if (type !== 'text') {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }
    });
</script>