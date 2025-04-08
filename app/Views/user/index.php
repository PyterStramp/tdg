<div class="content-header">
    <h1>Gestión de Usuarios</h1>
    <div class="actions">
        <a href="<?php echo baseUrl('user/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </a>
    </div>
</div>

<?php showFlashMessage(); ?>

<div class="card">
    <div class="card-header">
        <h2>Listado de Usuarios</h2>
    </div>
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="empty-data">
                <p>No hay usuarios registrados.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Nombre Completo</th>
                            <th>Roles</th>
                            <th>Estado</th>
                            <th>Último Acceso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php if (!empty($user['roles'])): ?>
                                        <?php foreach ($user['roles'] as $role): ?>
                                            <span class="badge badge-primary"><?php echo htmlspecialchars($role['name']); ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Sin roles</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['active']): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Nunca'; ?>
                                </td>
                                <td class="actions">
                                    <a href="<?php echo baseUrl('user/edit/' . $user['id']); ?>" class="btn-sm btn-info" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="<?php echo baseUrl('user/delete/' . $user['id']); ?>" class="btn-sm btn-danger" title="Eliminar" 
                                           onclick="return confirm('¿Está seguro de eliminar este usuario?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>