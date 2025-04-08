/**
 * Script para validación avanzada de formularios de usuario
 */
document.addEventListener('DOMContentLoaded', function() {
    // Validación de formulario de usuario (create/edit/edit_profile)
    const userForm = document.querySelector('form.form');
    
    if (userForm) {
        // Agregamos validación en tiempo real
        const inputs = userForm.querySelectorAll('input, select, textarea');
        inputs.forEach(function(input) {
            // Validación al perder el foco
            input.addEventListener('blur', function() {
                validateInput(this);
            });
            
            // Limpieza de errores al volver a escribir
            input.addEventListener('input', function() {
                clearError(this);
            });
        });
        
        // Validación al enviar el formulario
        userForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validar todos los campos requeridos
            const requiredInputs = userForm.querySelectorAll('[required]');
            requiredInputs.forEach(function(input) {
                if (!validateInput(input)) {
                    isValid = false;
                }
            });
            
            // Validar campos específicos
            const emailInput = userForm.querySelector('[type="email"]');
            if (emailInput && !validateEmail(emailInput)) {
                isValid = false;
            }
            
            // Validar contraseñas si existen
            const passwordInput = userForm.querySelector('#password, #new_password');
            const confirmInput = userForm.querySelector('#password_confirm, #confirm_password');
            
            if (passwordInput && confirmInput && passwordInput.value) {
                if (!validatePassword(passwordInput)) {
                    isValid = false;
                }
                
                if (passwordInput.value !== confirmInput.value) {
                    showError(confirmInput, 'Las contraseñas no coinciden');
                    isValid = false;
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                // Scroll al primer error
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    }
    
    /**
     * Valida un campo individual
     */
    function validateInput(input) {
        clearError(input);
        
        // No validar si está deshabilitado
        if (input.disabled) {
            return true;
        }
        
        // Validar requerido
        if (input.hasAttribute('required') && !input.value.trim()) {
            showError(input, 'Este campo es obligatorio');
            return false;
        }
        
        // Validaciones específicas por tipo
        if (input.type === 'email' && input.value.trim()) {
            return validateEmail(input);
        }
        
        // Validación de contraseña
        if ((input.id === 'password' || input.id === 'new_password') && input.value.trim()) {
            return validatePassword(input);
        }
        
        return true;
    }
    
    /**
     * Valida formato de email
     */
    function validateEmail(input) {
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        
        if (!emailRegex.test(input.value.trim())) {
            showError(input, 'Ingrese un correo electrónico válido');
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida requisitos de contraseña
     */
    function validatePassword(input) {
        const password = input.value.trim();
        
        if (password.length < 8) {
            showError(input, 'La contraseña debe tener al menos 8 caracteres');
            return false;
        }
        
        // Opcional: validar complejidad
        let hasLower = /[a-z]/.test(password);
        let hasUpper = /[A-Z]/.test(password);
        let hasNumber = /\d/.test(password);
        let hasSpecial = /[^a-zA-Z0-9]/.test(password);
        
        if (!(hasLower && hasUpper && hasNumber && hasSpecial)) {
            showError(input, 'La contraseña debe incluir mayúsculas, minúsculas, números y símbolos');
            return false;
        }
        
        return true;
    }
    
    /**
     * Muestra un mensaje de error para un campo
     */
    function showError(input, message) {
        input.classList.add('is-invalid');
        
        // Buscar si ya existe un mensaje de error
        let errorElement = input.parentElement.querySelector('.error-message');
        
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            input.parentElement.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
    }
    
    /**
     * Elimina un mensaje de error
     */
    function clearError(input) {
        input.classList.remove('is-invalid');
        
        const errorElement = input.parentElement.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
    }
    
    // Agregar funcionalidad de mostrar/ocultar contraseña
    const passwordFields = document.querySelectorAll('input[type="password"]');
    
    passwordFields.forEach(function(field) {
        const wrapper = document.createElement('div');
        wrapper.className = 'password-toggle-wrapper';
        
        // Clonar atributos al wrapper
        field.parentNode.insertBefore(wrapper, field);
        wrapper.appendChild(field);
        
        // Agregar botón de toggle
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'password-toggle-btn';
        toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
        toggleBtn.title = 'Mostrar contraseña';
        wrapper.appendChild(toggleBtn);
        
        // Funcionalidad de toggle
        toggleBtn.addEventListener('click', function() {
            if (field.type === 'password') {
                field.type = 'text';
                toggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
                toggleBtn.title = 'Ocultar contraseña';
            } else {
                field.type = 'password';
                toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
                toggleBtn.title = 'Mostrar contraseña';
            }
        });
    });
});

// Agregar estilos para el toggle de contraseña
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .password-toggle-wrapper {
            position: relative;
        }
        
        .password-toggle-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6c757d;
            font-size: 16px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
        }
        
        .password-toggle-btn:hover {
            color: #007bff;
        }
        
        input[type="password"],
        input[type="text"].password-visible {
            padding-right: 40px;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 80%;
            margin-top: 5px;
        }
        
        .is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
    `;
    document.head.appendChild(style);
});