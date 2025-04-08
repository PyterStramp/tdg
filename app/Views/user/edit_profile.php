<div class="content-header">
    <h1>Editar Mi Perfil</h1>
    <div class="actions">
        <a href="<?php echo baseUrl('user/profile'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Perfil
        </a>
    </div>
</div>

<?php showFlashMessage(); ?>

<div class="card">
    <div class="card-header">
        <h2>Información Personal</h2>
    </div>
    <div class="card-body">
        <form action="<?php echo baseUrl('user/update_profile'); ?>" method="post" class="form">
            <!-- Campo oculto para CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="username">Nombre de Usuario</label>
                    <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="form-control" disabled>
                    <small class="form-text text-muted">El nombre de usuario no se puede cambiar.</small>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="email">Correo Electrónico <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control" required>
                    <small class="form-text text-muted">El correo electrónico debe ser único y válido.</small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="full_name">Nombre Completo</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" class="form-control">
                <small class="form-text text-muted">Su nombre completo para identificación en el sistema.</small>
            </div>
            
            <div class="user-info-panel">
                <h3><i class="fas fa-info-circle"></i> Información de la cuenta</h3>
                <div class="info-row">
                    <span class="info-label">Código único:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['code']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de registro:</span>
                    <span class="info-value"><?php echo formatDate($user['created_at']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Último acceso:</span>
                    <span class="info-value"><?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Nunca'; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estado de la cuenta:</span>
                    <span class="info-value">
                        <?php if ($user['active']): ?>
                            <span class="badge badge-success">Activa</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactiva</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <a href="<?php echo baseUrl('user/profile'); ?>" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h2>Seguridad de la Cuenta</h2>
    </div>
    <div class="card-body">
        <p>Para cambiar su contraseña y configurar las opciones de seguridad, utilice el siguiente enlace:</p>
        <div class="text-center">
            <a href="<?php echo baseUrl('user/change_password'); ?>" class="btn btn-warning">
                <i class="fas fa-key"></i> Cambiar Contraseña
            </a>
        </div>
    </div>
</div>

<style>
.mt-4 {
    margin-top: 20px;
}

.text-center {
    text-align: center;
}

.user-info-panel {
    background-color: #f8f9fa;
    border-radius: 4px;
    padding: 15px;
    margin: 20px 0;
    border-left: 4px solid var(--info-color);
}

.user-info-panel h3 {
    margin-top: 0;
    font-size: 16px;
    color: var(--dark-color);
    margin-bottom: 15px;
}

.user-info-panel .info-row {
    margin-bottom: 8px;
    display: flex;
}

.user-info-panel .info-label {
    min-width: 150px;
    font-weight: 600;
}
</style>