    <div class="container login-container">
        <div class="login-header">
            <h1>Recuperar Contraseña</h1>
            <p>Ingrese su correo electrónico para recibir instrucciones</p>
        </div>

        <?php showFlashMessage(); ?>

        <form action="<?php echo baseUrl('auth/request_reset'); ?>" method="post" class="login-form">
            <!-- Campo oculto para CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required autofocus placeholder="Ingrese su correo registrado">
                <small class="form-text text-muted">Recibirá un enlace para crear una nueva contraseña.</small>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Enviar Instrucciones
                </button>
            </div>

            <div class="form-footer">
                <p><a href="<?php echo baseUrl('auth/login'); ?>"><i class="fas fa-arrow-left"></i> Volver al inicio de sesión</a></p>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validación básica del formulario
            document.querySelector('form').addEventListener('submit', function(e) {
                const emailInput = document.getElementById('email');
                if (!emailInput.value.trim()) {
                    e.preventDefault();
                    alert('Por favor, ingrese su correo electrónico.');
                    emailInput.focus();
                }
            });
        });
    </script>