<!--app/Views/module_admin/permissions.php-->
<div class="content-header">
    <h1>Gestión de Permisos: <?php echo htmlspecialchars($module['name']); ?></h1>
    <div class="actions">
        <a href="<?php echo baseUrl('module_admin'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
</div>

<?php showFlashMessage(); ?>

<div class="card mb-4">
    <div class="card-header">
        <h2>Información del Módulo</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($module['name']); ?></p>
                <p><strong>Identificador:</strong> <?php echo htmlspecialchars($module['slug']); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Estado:</strong> 
                    <span class="badge <?php echo $module['active'] ? 'text-bg-success' : 'text-bg-secondary'; ?>">
                        <?php echo $module['active'] ? 'Activo' : 'Inactivo'; ?>
                    </span>
                </p>
                <p><strong>Directorio:</strong> <?php echo htmlspecialchars($module['directory_path']); ?></p>
            </div>
        </div>
        <?php if (!empty($module['description'])): ?>
            <div class="row">
                <div class="col-12">
                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($module['description']); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Permisos por Rol</h2>
        <p class="text-muted mb-0">Asigne los permisos que cada rol tendrá sobre este módulo.</p>
    </div>
    <div class="card-body">
        <form action="<?php echo baseUrl('module_admin/updatePermissions/' . $module['id']); ?>" method="post">
            <!-- Campo oculto para CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div class="permissions-container">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 20%">Rol</th>
                            <th style="width: 80%">Permisos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($roles)): ?>
                            <tr>
                                <td colspan="2" class="text-center">No hay roles registrados en el sistema</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($roles as $role): ?>
                                <tr>
                                    <td>
                                        <div class="role-name font-weight-bold"><?php echo htmlspecialchars($role['name']); ?></div>
                                        <?php if (!empty($role['description'])): ?>
                                            <div class="role-description text-muted small"><?php echo htmlspecialchars($role['description']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="permissions-grid">
                                            <?php if (empty($permissions)): ?>
                                                <p class="text-muted">No hay permisos disponibles</p>
                                            <?php else: ?>
                                                <?php foreach ($permissions as $permission): ?>
                                                    <div class="permission-item">
                                                        <div class="custom-control custom-checkbox">
                                                            <input 
                                                                type="checkbox" 
                                                                class="custom-control-input" 
                                                                id="perm_<?php echo $role['id']; ?>_<?php echo $permission['id']; ?>" 
                                                                name="role_permissions[<?php echo $role['id']; ?>][]" 
                                                                value="<?php echo $permission['id']; ?>"
                                                                <?php echo in_array($permission['id'], $role['modulePermissions']) ? 'checked' : ''; ?>
                                                            >
                                                            <label class="custom-control-label" for="perm_<?php echo $role['id']; ?>_<?php echo $permission['id']; ?>">
                                                                <?php echo htmlspecialchars($permission['name']); ?>
                                                                <?php if (!empty($permission['description'])): ?>
                                                                    <span class="tooltip-icon" data-toggle="tooltip" title="<?php echo htmlspecialchars($permission['description']); ?>">
                                                                        <i class="fas fa-info-circle"></i>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="permissions-quick-actions mt-4 mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Acciones Rápidas</h5>
                            </div>
                            <div class="card-body">
                                <div class="quick-actions-buttons">
                                    <button type="button" class="btn btn-sm btn-outline-secondary mr-2" id="selectAll">Seleccionar Todo</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mr-2" id="deselectAll">Deseleccionar Todo</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="setDefaultPermissions">Configuración Predeterminada</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Guía de Permisos</h5>
                            </div>
                            <div class="card-body">
                                <ul class="permissions-guide mb-0">
                                    <li><strong>view</strong>: Permite visualizar el módulo</li>
                                    <li><strong>edit</strong>: Permite realizar cambios en el módulo</li>
                                    <li><strong>admin</strong>: Permite configurar y administrar el módulo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Permisos
                </button>
                <a href="<?php echo baseUrl('module_admin'); ?>" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    if (typeof $ !== 'undefined' && typeof $.fn.tooltip !== 'undefined') {
        $('.tooltip-icon').tooltip();
    }
    
    // Seleccionar todos los checkboxes
    document.getElementById('selectAll').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.permissions-container input[type=checkbox]');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = true;
        });
    });
    
    // Deseleccionar todos los checkboxes
    document.getElementById('deselectAll').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.permissions-container input[type=checkbox]');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = false;
        });
    });
    
    // Configuración predeterminada
    document.getElementById('setDefaultPermissions').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.permissions-container input[type=checkbox]');
        
        // Primero desactivar todos
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = false;
        });
        
        // Buscar y activar los permisos deseados por defecto
        
        // Admin tiene todos los permisos
        const adminCheckboxes = document.querySelectorAll('.permissions-container input[name^="role_permissions[1]"]');
        adminCheckboxes.forEach(function(checkbox) {
            checkbox.checked = true;
        });
        
        // Los demás roles solo tienen permiso de visualización
        const roles = <?php echo json_encode(array_column($roles, 'id')); ?>;
        const viewPermId = <?php 
            $viewPermId = 0;
            foreach ($permissions as $permission) {
                if ($permission['name'] === 'view') {
                    $viewPermId = $permission['id'];
                    break;
                }
            }
            echo $viewPermId;
        ?>;
        
        if (viewPermId > 0) {
            roles.forEach(function(roleId) {
                if (roleId > 1) { // Asumiendo que 1 es el ID del rol de administrador
                    const viewCheckbox = document.getElementById('perm_' + roleId + '_' + viewPermId);
                    if (viewCheckbox) {
                        viewCheckbox.checked = true;
                    }
                }
            });
        }
    });
});
</script>

<style>
.permissions-container {
    margin-bottom: 20px;
}
.permissions-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 20px;
}
.permission-item {
    min-width: 150px;
}
.tooltip-icon {
    color: #6c757d;
    cursor: help;
    margin-left: 5px;
    font-size: 0.85rem;
}
.permissions-guide {
    list-style-type: none;
    padding-left: 0;
}
.permissions-guide li {
    margin-bottom: 5px;
}
.form-actions {
    margin-top: 30px;
    display: flex;
    gap: 10px;
}
.quick-actions-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
@media (max-width: 768px) {
    .permission-item {
        min-width: 100%;
    }
}
</style>