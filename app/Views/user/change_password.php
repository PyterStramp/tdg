<div class="content-header">
    <h1>Cambiar Contraseña</h1>
    <div class="actions">
        <a href="<?php echo baseUrl('user/profile'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Perfil
        </a>
    </div>
</div>

<?php showFlashMessage(); ?>

<div class="card">
    <div class="card-header">
        <h2>Formulario de Cambio de Contraseña</h2>
    </div>
    <div class="card-body">
        <form action="<?php echo baseUrl('user/update_password'); ?>" method="post" class="form">
            <!-- Campo oculto para CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div class="form-group">
                <label for="current_password">Contraseña Actual <span class="required">*</span></label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
                <small class="form-text text-muted">Ingrese su contraseña actual para verificar su identidad.</small>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="new_password">Nueva Contraseña <span class="required">*</span></label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                    <small class="form-text text-muted">La nueva contraseña debe tener al menos 8 caracteres.</small>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="confirm_password">Confirmar Nueva Contraseña <span class="required">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    <small class="form-text text-muted">Repita la nueva contraseña para confirmar.</small>
                </div>
            </div>
            
            <div class="password-strength-meter">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="password-strength-text">Seguridad de la contraseña: Sin evaluar</small>
            </div>
            
            <div class="password-tips">
                <h3>Recomendaciones para una contraseña segura:</h3>
                <ul>
                    <li>Use al menos 8 caracteres</li>
                    <li>Incluya letras mayúsculas y minúsculas</li>
                    <li>Incluya números y símbolos</li>
                    <li>Evite información personal fácil de adivinar</li>
                    <li>No use la misma contraseña en diferentes sitios</li>
                </ul>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-key"></i> Cambiar Contraseña
                </button>
                <a href="<?php echo baseUrl('user/profile'); ?>" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Script para evaluación de seguridad de contraseña
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('new_password');
    const confirmInput = document.getElementById('confirm_password');
    const progressBar = document.querySelector('.progress-bar');
    const strengthText = document.querySelector('.password-strength-text');
    
    passwordInput.addEventListener('input', evaluatePassword);
    
    function evaluatePassword() {
        const password = passwordInput.value;
        let strength = 0;
        let feedback = '';
        
        // Evaluar longitud
        if (password.length >= 8) {
            strength += 25;
        }
        
        // Evaluar letras mayúsculas y minúsculas
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) {
            strength += 25;
        }
        
        // Evaluar números
        if (password.match(/\d/)) {
            strength += 25;
        }
        
        // Evaluar caracteres especiales
        if (password.match(/[^a-zA-Z0-9]/)) {
            strength += 25;
        }
        
        // Actualizar barra de progreso
        progressBar.style.width = strength + '%';
        
        // Definir color según la fortaleza
        if (strength <= 25) {
            progressBar.className = 'progress-bar bg-danger';
            feedback = 'Muy débil';
        } else if (strength <= 50) {
            progressBar.className = 'progress-bar bg-warning';
            feedback = 'Débil';
        } else if (strength <= 75) {
            progressBar.className = 'progress-bar bg-info';
            feedback = 'Buena';
        } else {
            progressBar.className = 'progress-bar bg-success';
            feedback = 'Excelente';
        }
        
        strengthText.textContent = 'Seguridad de la contraseña: ' + feedback;
    }
    
    // Validar que las contraseñas coinciden
    confirmInput.addEventListener('input', function() {
        if (passwordInput.value !== confirmInput.value) {
            confirmInput.setCustomValidity('Las contraseñas no coinciden');
        } else {
            confirmInput.setCustomValidity('');
        }
    });
});
</script>

<style>
.password-strength-meter {
    margin: 20px 0;
}

.progress {
    height: 10px;
    border-radius: 5px;
    background-color: #e9ecef;
    overflow: hidden;
    margin-bottom: 5px;
}

.progress-bar {
    height: 100%;
    transition: width 0.3s ease;
}

.bg-danger {
    background-color: var(--danger-color);
}

.bg-warning {
    background-color: var(--warning-color);
}

.bg-info {
    background-color: var(--info-color);
}

.bg-success {
    background-color: var(--success-color);
}

.password-strength-text {
    display: block;
    text-align: right;
    font-style: italic;
}

.password-tips {
    margin-top: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 4px;
    border-left: 4px solid var(--info-color);
}

.password-tips h3 {
    font-size: 16px;
    margin-top: 0;
    margin-bottom: 10px;
    color: var(--dark-color);
}

.password-tips ul {
    padding-left: 20px;
    margin-bottom: 0;
}

.password-tips li {
    margin-bottom: 5px;
    font-size: 14px;
}
</style>