<div class="content-header">
    <h1>Mi Perfil</h1>
</div>

<?php showFlashMessage(); ?>

<div class="card">
    <div class="card-header">
        <h2>Información del Usuario</h2>
    </div>
    <div class="card-body">
        <div class="profile-info">
            <div class="profile-avatar">
                <img src="<?php if (isAdmin()): echo baseUrl('images/default-avatar.png'); else: echo baseUrl('images/default-avatar-user.png'); endif; ?>" alt="Avatar de usuario" class="large-avatar">
            </div>
            
            <div class="profile-details">
                <div class="info-row">
                    <span class="info-label">Nombre de usuario:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Correo electrónico:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Nombre completo:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['full_name'] ?? 'No especificado'); ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Roles:</span>
                    <span class="info-value">
                        <?php if (!empty($roles)): ?>
                            <?php foreach ($roles as $role): ?>
                                <span class="badge badge-primary"><?php echo htmlspecialchars($role['name']); ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="badge badge-secondary">Sin roles</span>
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Estado:</span>
                    <span class="info-value">
                        <?php if ($user['active']): ?>
                            <span class="badge badge-success">Activo</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactivo</span>
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Fecha de registro:</span>
                    <span class="info-value"><?php echo formatDate($user['created_at']); ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Último acceso:</span>
                    <span class="info-value"><?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Nunca'; ?></span>
                </div>
            </div>
        </div>
        
        <div class="profile-actions">
            <a href="<?php echo baseUrl('user/edit_profile'); ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar Perfil
            </a>
            
            <a href="<?php echo baseUrl('user/change_password'); ?>" class="btn btn-secondary">
                <i class="fas fa-key"></i> Cambiar Contraseña
            </a>
        </div>
    </div>
</div>

<!-- Agregar estilos adicionales para el perfil -->
<style>
.profile-info {
    display: flex;
    align-items: flex-start;
    margin-bottom: 30px;
}

.profile-avatar {
    margin-right: 30px;
}

.large-avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 5px solid var(--light-color);
    box-shadow: 0 2px 10px var(--shadow-color);
}

.profile-details {
    flex: 1;
}

.profile-actions {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

.profile-actions .btn {
    margin: 0 10px;
}

@media (max-width: 768px) {
    .profile-info {
        flex-direction: column;
        align-items: center;
    }
    
    .profile-avatar {
        margin-right: 0;
        margin-bottom: 20px;
    }
    
    .profile-details {
        width: 100%;
    }
}
</style>