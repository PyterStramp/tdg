
    <div class="container login-container">
        <div class="login-header">
            <h1>Revise su correo</h1>
            <p>Hemos enviado instrucciones para recuperar su contraseña</p>
        </div>
        
        <?php showFlashMessage(); ?>
        
        <div class="confirmation-message">
            <div class="icon-success">
                <i class="fas fa-envelope"></i>
            </div>
            
            <p>Si la dirección de correo electrónico proporcionada está registrada en nuestro sistema, recibirá instrucciones para restablecer su contraseña en breve.</p>
            
            <div class="instructions">
                <h3>¿Qué hacer ahora?</h3>
                <ol>
                    <li>Revise su bandeja de entrada y también la carpeta de spam.</li>
                    <li>Siga el enlace proporcionado en el correo electrónico.</li>
                    <li>Cree una nueva contraseña segura.</li>
                </ol>
            </div>
            
            <p class="note">El enlace de restablecimiento será válido por 1 hora.</p>
        </div>
        
        <div class="form-footer">
            <p><a href="<?php echo baseUrl('auth/login'); ?>"><i class="fas fa-arrow-left"></i> Volver al inicio de sesión</a></p>
        </div>
    </div>
    
    <style>
        .confirmation-message {
            text-align: center;
            margin: 30px 0;
        }
        
        .icon-success {
            font-size: 48px;
            color: var(--success-color);
            margin-bottom: 20px;
        }
        
        .instructions {
            text-align: left;
            margin: 20px 0;
            background-color: var(--light-color);
            padding: 15px;
            border-radius: 5px;
        }
        
        .instructions h3 {
            margin-top: 0;
            font-size: 16px;
        }
        
        .instructions ol {
            padding-left: 20px;
            margin-bottom: 0;
        }
        
        .instructions li {
            margin-bottom: 5px;
        }
        
        .note {
            font-style: italic;
            color: var(--gray-color);
            margin-top: 20px;
        }
    </style>