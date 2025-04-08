/**
 * JavaScript para los formularios de usuario
 */
document.addEventListener('DOMContentLoaded', function() {
    // Validación de formulario de usuario
    const userForm = document.querySelector('form.form');
    
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            let isValid = true;
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirm');
            
            // Resetear mensajes de error previos
            removeErrorMessages();
            
            // Validar nombre de usuario
            if (!username.value.trim()) {
                showError(username, 'El nombre de usuario es obligatorio');
                isValid = false;
            } else if (username.value.length < 3) {
                showError(username, 'El nombre de usuario debe tener al menos 3 caracteres');
                isValid = false;
            }
            
            // Validar email
            if (!email.value.trim()) {
                showError(email, 'El correo electrónico es obligatorio');
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                showError(email, 'El formato del correo electrónico no es válido');
                isValid = false;
            }
            
            // Si es formulario de creación o si se ingresó contraseña en edición
            const isCreateForm = !document.querySelector('[name="update"]');
            if (isCreateForm || password.value.trim()) {
                // Validar contraseña
                if (isCreateForm && !password.value.trim()) {
                    showError(password, 'La contraseña es obligatoria');
                    isValid = false;
                } else if (password.value.trim() && password.value.length < 8) {
                    showError(password, 'La contraseña debe tener al menos 8 caracteres');
                    isValid = false;
                }
                
                // Validar confirmación de contraseña
                if (password.value !== passwordConfirm.value) {
                    showError(passwordConfirm, 'Las contraseñas no coinciden');
                    isValid = false;
                }
            }
            
            // Validar que se seleccionó al menos un rol
            const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]:checked');
            if (roleCheckboxes.length === 0) {
                const rolesContainer = document.querySelector('.role-checkboxes');
                showErrorAfter(rolesContainer, 'Debe seleccionar al menos un rol');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                // Scroll al primer error
                const firstError = document.querySelector('.error-message');
                if (firstError) {
                    firstError.parentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
        
        // Limpiar mensajes de error cuando se escribe
        const inputs = userForm.querySelectorAll('input, select');
        inputs.forEach(function(input) {
            input.addEventListener('input', function() {
                const errorMessage = this.parentElement.querySelector('.error-message');
                if (errorMessage) {
                    errorMessage.remove();
                    this.classList.remove('is-invalid');
                }
            });
        });
    }
    
    // Función para validar email
    function isValidEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    
    // Función para mostrar mensajes de error
    function showError(input, message) {
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        input.parentElement.appendChild(errorDiv);
    }
    
    // Función para mostrar mensajes de error después de un elemento
    function showErrorAfter(element, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        element.parentElement.appendChild(errorDiv);
    }
    
    // Función para eliminar todos los mensajes de error
    function removeErrorMessages() {
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(function(error) {
            error.remove();
        });
        
        const invalidInputs = document.querySelectorAll('.is-invalid');
        invalidInputs.forEach(function(input) {
            input.classList.remove('is-invalid');
        });
    }
    
    // Confirmación para eliminar usuarios
    const deleteButtons = document.querySelectorAll('a[href*="/user/delete/"]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de que desea eliminar este usuario? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    });
});