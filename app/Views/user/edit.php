<div class="content-header">
    <h1>Editar Usuario</h1>
    <div class="actions">
        <a href="<?php echo baseUrl('user'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
</div>

<?php showFlashMessage(); ?>

<div class="card">
    <div class="card-header">
        <h2>Formulario de Edición</h2>
    </div>
    <div class="card-body">
        <form action="<?php echo baseUrl('user/update/' . $user['id']); ?>" method="post" class="form">
            <!-- Campo oculto para CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="username">Nombre de Usuario <span class="required">*</span></label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    <small class="form-text text-muted">El nombre de usuario debe ser único y se usará para iniciar sesión.</small>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="email">Correo Electrónico <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    <small class="form-text text-muted">El correo electrónico debe ser único y válido.</small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control">
                    <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual. La nueva contraseña debe tener al menos 8 caracteres.</small>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="password_confirm">Confirmar Contraseña</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control">
                    <small class="form-text text-muted">Repite la nueva contraseña para confirmar.</small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="full_name">Nombre Completo</label>
                <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label>Roles</label>
                <div class="role-checkboxes">
                    <?php foreach ($roles as $role): ?>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" id="role_<?php echo $role['id']; ?>" name="roles[]" value="<?php echo $role['id']; ?>" <?php echo (in_array($role['id'], $userRoles)) ? 'checked' : ''; ?> class="custom-control-input">
                            <label for="role_<?php echo $role['id']; ?>" class="custom-control-label"><?php echo htmlspecialchars($role['name']); ?></label>
                            <?php if (!empty($role['description'])): ?>
                                <small class="text-muted">(<?php echo htmlspecialchars($role['description']); ?>)</small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <small class="form-text text-muted">Selecciona los roles que tendrá este usuario.</small>
            </div>
            
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" id="active" name="active" class="custom-control-input" <?php echo $user['active'] ? 'checked' : ''; ?>>
                    <label for="active" class="custom-control-label">Usuario Activo</label>
                </div>
                <small class="form-text text-muted">Si está desactivado, el usuario no podrá iniciar sesión.</small>
            </div>
            
            <div class="user-info-panel">
                <div class="info-row">
                    <span class="info-label">Código único:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['code']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de creación:</span>
                    <span class="info-value"><?php echo formatDate($user['created_at']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Último acceso:</span>
                    <span class="info-value"><?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Nunca'; ?></span>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <a href="<?php echo baseUrl('user'); ?>" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>