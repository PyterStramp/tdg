/**
 * Script principal para el sistema de módulos
 */
document.addEventListener('DOMContentLoaded', function() {
    // Manejo del menú móvil
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
        });
    }
    
    // Manejo de submenús en móvil
    const hasSubmenu = document.querySelectorAll('.has-submenu');
    
    if (hasSubmenu.length > 0) {
        hasSubmenu.forEach(function(item) {
            item.addEventListener('click', function(e) {
                // Solo aplicar en vista móvil
                if (window.innerWidth <= 768) {
                    // Si se hace clic en el enlace principal
                    if (e.target === item.querySelector('a') || e.target === item.querySelector('a i')) {
                        e.preventDefault();
                        this.classList.toggle('active');
                    }
                }
            });
        });
    }
    
    // Ocultar alertas después de 5 segundos
    const alertMessages = document.querySelectorAll('.alert');
    
    if (alertMessages.length > 0) {
        alertMessages.forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });
    }
    
    // Manejo del menú de usuario en móvil
    const userInfo = document.querySelector('.user-info');
    const userDropdown = document.querySelector('.user-dropdown');
    
    if (userInfo && userDropdown) {
        userInfo.addEventListener('click', function() {
            // Solo en móvil cambiamos el comportamiento
            if (window.innerWidth <= 768) {
                userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
            }
        });
        
        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!userInfo.contains(e.target) && !userDropdown.contains(e.target)) {
                if (window.innerWidth <= 768) {
                    userDropdown.style.display = 'none';
                }
            }
        });
    }
    
    // Validación de formularios
    const forms = document.querySelectorAll('form');
    
    if (forms.length > 0) {
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(function(field) {
                    if (!field.value.trim()) {
                        isValid = false;
                        // Marcar campo como inválido
                        field.classList.add('is-invalid');
                        
                        // Si no existe mensaje de error, crearlo
                        let errorMessage = field.nextElementSibling;
                        if (!errorMessage || !errorMessage.classList.contains('error-message')) {
                            errorMessage = document.createElement('div');
                            errorMessage.classList.add('error-message');
                            errorMessage.textContent = 'Este campo es obligatorio';
                            field.parentNode.insertBefore(errorMessage, field.nextSibling);
                        }
                    } else {
                        // Eliminar marca de inválido
                        field.classList.remove('is-invalid');
                        
                        // Eliminar mensaje de error si existe
                        const errorMessage = field.nextElementSibling;
                        if (errorMessage && errorMessage.classList.contains('error-message')) {
                            errorMessage.remove();
                        }
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
            
            // Eliminar mensajes de error al escribir
            const fields = form.querySelectorAll('input, select, textarea');
            fields.forEach(function(field) {
                field.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                        
                        const errorMessage = this.nextElementSibling;
                        if (errorMessage && errorMessage.classList.contains('error-message')) {
                            errorMessage.remove();
                        }
                    }
                });
            });
        });
    }
});