
    <div class="container login-container reset-password-container">
        <div class="login-header">
            <h1>Restablecer Contraseña</h1>
            <p>Cree una nueva contraseña segura</p>
        </div>
        
        <?php showFlashMessage(); ?>
        
        <form action="<?php echo baseUrl('auth/complete_reset'); ?>" method="post" class="login-form" id="resetForm">
            <!-- Campo oculto para CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="form-group">
                <label for="password">Nueva Contraseña</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" required autofocus>
                    <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small class="form-text">La contraseña debe tener al menos 8 caracteres.</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña</label>
                <div class="password-field">
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small class="form-text">Repita la contraseña para confirmar.</small>
            </div>
            
            <div class="password-strength">
                <div class="strength-bar">
                    <div class="strength-progress" id="strength-progress"></div>
                </div>
                <small id="strength-text">Seguridad de la contraseña: No evaluada</small>
            </div>
            
            <div class="password-tips">
                <h3>Recomendaciones para una contraseña segura:</h3>
                <ul>
                    <li id="length-check"><i class="fas fa-times"></i> Al menos 8 caracteres</li>
                    <li id="uppercase-check"><i class="fas fa-times"></i> Al menos una letra mayúscula</li>
                    <li id="lowercase-check"><i class="fas fa-times"></i> Al menos una letra minúscula</li>
                    <li id="number-check"><i class="fas fa-times"></i> Al menos un número</li>
                    <li id="special-check"><i class="fas fa-times"></i> Al menos un carácter especial</li>
                </ul>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <i class="fas fa-key"></i> Cambiar Contraseña
                </button>
            </div>
            
            <div class="form-footer">
                <p><a href="<?php echo baseUrl('auth/login'); ?>"><i class="fas fa-arrow-left"></i> Volver al inicio de sesión</a></p>
            </div>
        </form>
    </div>
    
    
    <style>
        .reset-password-container {
            max-width: 450px;
        }
        
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
        
        .password-strength {
            margin: 15px 0;
        }
        
        .strength-bar {
            height: 5px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        
        .strength-progress {
            height: 100%;
            width: 0;
            background-color: var(--danger-color);
            transition: width 0.3s, background-color 0.3s;
        }
        
        #strength-text {
            font-size: 12px;
            color: var(--gray-color);
            text-align: right;
            display: block;
        }
        
        .password-tips {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .password-tips h3 {
            font-size: 14px;
            margin: 0 0 10px 0;
        }
        
        .password-tips ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .password-tips li {
            margin-bottom: 5px;
            font-size: 13px;
        }
        
        .password-tips li .fa-times {
            color: var(--danger-color);
            margin-right: 5px;
            width: 14px;
        }
        
        .password-tips li .fa-check {
            color: var(--success-color);
            margin-right: 5px;
            width: 14px;
        }
    </style>
    
    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('confirm_password');
            const strengthProgress = document.getElementById('strength-progress');
            const strengthText = document.getElementById('strength-text');
            const submitButton = document.getElementById('submit-btn');
            const form = document.getElementById('resetForm');
            
            // Elementos de verificación de contraseña
            const lengthCheck = document.getElementById('length-check');
            const uppercaseCheck = document.getElementById('uppercase-check');
            const lowercaseCheck = document.getElementById('lowercase-check');
            const numberCheck = document.getElementById('number-check');
            const specialCheck = document.getElementById('special-check');
            
            // Botones de mostrar/ocultar contraseña
            const toggleButtons = document.querySelectorAll('.toggle-password');
            toggleButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const type = input.type === 'password' ? 'text' : 'password';
                    input.type = type;
                    
                    // Cambiar icono
                    const icon = this.querySelector('i');
                    if (type === 'text') {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });
            
            // Evaluar fortaleza de la contraseña
            passwordInput.addEventListener('input', function() {
                evaluatePassword();
            });
            
            // Verificar que las contraseñas coinciden
            confirmInput.addEventListener('input', function() {
                checkPasswordMatch();
            });
            
            // Validar formulario antes de enviar
            form.addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                }
            });
            
            function evaluatePassword() {
                const password = passwordInput.value;
                let strength = 0;
                let checks = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /\d/.test(password),
                    special: /[^A-Za-z0-9]/.test(password)
                };
                
                // Actualizar los indicadores visuales
                updateCheckIcon(lengthCheck, checks.length);
                updateCheckIcon(uppercaseCheck, checks.uppercase);
                updateCheckIcon(lowercaseCheck, checks.lowercase);
                updateCheckIcon(numberCheck, checks.number);
                updateCheckIcon(specialCheck, checks.special);
                
                // Calcular fortaleza
                if (checks.length) strength += 20;
                if (checks.uppercase) strength += 20;
                if (checks.lowercase) strength += 20;
                if (checks.number) strength += 20;
                if (checks.special) strength += 20;
                
                // Actualizar barra de progreso
                strengthProgress.style.width = strength + '%';
                
                // Cambiar color según fortaleza
                if (strength <= 20) {
                    strengthProgress.style.backgroundColor = '#dc3545'; // Rojo
                    strengthText.textContent = 'Seguridad: Muy débil';
                } else if (strength <= 40) {
                    strengthProgress.style.backgroundColor = '#ffc107'; // Amarillo
                    strengthText.textContent = 'Seguridad: Débil';
                } else if (strength <= 60) {
                    strengthProgress.style.backgroundColor = '#fd7e14'; // Naranja
                    strengthText.textContent = 'Seguridad: Media';
                } else if (strength <= 80) {
                    strengthProgress.style.backgroundColor = '#20c997'; // Verde claro
                    strengthText.textContent = 'Seguridad: Buena';
                } else {
                    strengthProgress.style.backgroundColor = '#28a745'; // Verde
                    strengthText.textContent = 'Seguridad: Excelente';
                }
                
                checkPasswordMatch();
            }
            
            function updateCheckIcon(element, isValid) {
                const icon = element.querySelector('i');
                if (isValid) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-check');
                } else {
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-times');
                }
            }
            
            function checkPasswordMatch() {
                if (confirmInput.value && passwordInput.value !== confirmInput.value) {
                    confirmInput.setCustomValidity('Las contraseñas no coinciden');
                } else {
                    confirmInput.setCustomValidity('');
                }
            }
            
            function validateForm() {
                // Verificar longitud mínima
                if (passwordInput.value.length < 8) {
                    alert('La contraseña debe tener al menos 8 caracteres.');
                    passwordInput.focus();
                    return false;
                }
                
                // Verificar que las contraseñas coinciden
                if (passwordInput.value !== confirmInput.value) {
                    alert('Las contraseñas no coinciden.');
                    confirmInput.focus();
                    return false;
                }
                
                return true;
            }
        });
    </script>
</body>
</html>