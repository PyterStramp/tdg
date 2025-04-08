<!-- app/Views/module_admin/index.php -->
<div class="content-header">
    <h1>Administración de Módulos</h1>
    <div class="actions">
        <a href="<?php echo baseUrl('module_admin/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Crear Nuevo Módulo
        </a>
    </div>
</div>

<?php showFlashMessage(); ?>

<div class="card">
    <div class="card-header">
        <h2>Módulos Disponibles</h2>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($modules)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No hay módulos registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($modules as $module): ?>
                        <tr>
                            <td>
                                <i class="<?php echo $module['icon'] ?: 'fas fa-puzzle-piece'; ?>"></i>
                                <?php echo $module['name']; ?>
                            </td>
                            <td><?php echo $module['slug']; ?></td>
                            <td>
                                <span class="badge <?php echo $module['active'] ? 'text-bg-success' : 'text-bg-secondary'; ?>">
                                    <?php echo $module['active'] ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?php echo baseUrl('module_admin/edit/' . $module['id']); ?>" class="btn btn-sm btn-info" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo baseUrl('module_admin/permissions/' . $module['id']); ?>" class="btn btn-sm btn-warning" title="Permisos">
                                        <i class="fas fa-key"></i>
                                    </a>
                                    <a href="<?php echo baseUrl('module_admin/toggle/' . $module['id']); ?>" class="btn btn-sm <?php echo $module['active'] ? 'btn-secondary' : 'btn-success'; ?>" title="<?php echo $module['active'] ? 'Desactivar' : 'Activar'; ?>">
                                        <i class="fas fa-<?php echo $module['active'] ? 'times' : 'check'; ?>"></i>
                                    </a>
                                    <a href="<?php echo baseUrl('module_admin/delete/' . $module['id']); ?>" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este módulo? Esta acción no se puede deshacer.')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>