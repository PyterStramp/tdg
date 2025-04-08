<div class="content-header">
    <h1>Crear Nuevo Usuario</h1>
    <div class="actions">
        <a href="<?php echo baseUrl('user'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
</div>

<?php showFlashMessage(); ?>

<div class="card">
    <div class="card-header">
        <h2>Formulario de Registro</h2>
    </div>
    <div class="card-body">
        <form action="<?php echo baseUrl('user/store'); ?>" method="post" class="form">
            <!-- Campo oculto para CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="username">Nombre de Usuario <span class="required">*</span></label>
                    <input type="text" id="username" name="username" class="form-control" required>
                    <small class="form-text text-muted">El nombre de usuario debe ser único y se usará para iniciar sesión.</small>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="email">Correo Electrónico <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" required>
                    <small class="form-text text-muted">El correo electrónico debe ser único y válido.</small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="password">Contraseña <span class="required">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <small class="form-text text-muted">La contraseña debe tener al menos 8 caracteres.</small>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="password_confirm">Confirmar Contraseña <span class="required">*</span></label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
                    <small class="form-text text-muted">Repite la contraseña para confirmar.</small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="full_name">Nombre Completo</label>
                <input type="text" id="full_name" name="full_name" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Roles</label>
                <div class="role-checkboxes">
                    <?php foreach ($roles as $role): ?>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" id="role_<?php echo $role['id']; ?>" name="roles[]" value="<?php echo $role['id']; ?>" <?php echo ($role['id'] == 2) ? 'checked' : ''; ?> class="custom-control-input">
                            <label for="role_<?php echo $role['id']; ?>" class="custom-control-label"><?php echo htmlspecialchars($role['name']); ?></label>
                            <?php if (!empty($role['description'])): ?>
                                <small class="text-muted">(<?php echo htmlspecialchars($role['description']); ?>)</small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <small class="form-text text-muted">Selecciona los roles que tendrá este usuario. Por defecto, todos los usuarios tienen el rol "user".</small>
            </div>
            
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" id="active" name="active" class="custom-control-input" checked>
                    <label for="active" class="custom-control-label">Usuario Activo</label>
                </div>
                <small class="form-text text-muted">Si está desactivado, el usuario no podrá iniciar sesión.</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Usuario
                </button>
                <a href="<?php echo baseUrl('user'); ?>" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>