<div class="dashboard">
    <div class="dashboard-header">
        <h1>Saludos, <?php echo getCurrentUserFullName(); ?></h1>
        <p>Seleccione un módulo para comenzar</p>
        <?php if (isAdmin()): ?>
            <div class="admin-actions">
                <a href="<?php echo baseUrl('module_admin'); ?>" class="btn btn-primary">
                    <i class="fas fa-cogs"></i> Administrar Módulos
                </a>
            </div>
        <?php endif; ?>
    </div>
   
    <?php showFlashMessage(); ?>
   
    <div class="modules-grid">
        <?php 
        // Filtrar los módulos a los que el usuario tiene acceso de visualización
        $accessibleModules = array_filter($modules, function($module) {
            return is_array($module['permissions']) && in_array('view', $module['permissions']);
        });
        if (empty($accessibleModules)): 
        ?>
            <div class="no-modules-message">
                <p>No tienes acceso a ningún módulo en este momento.</p>
                <p>Contacta al administrador del sistema si consideras que esto es un error.</p>
            </div>
        <?php else: ?>
            <?php foreach ($accessibleModules as $module): ?>
                <a href="<?php echo baseUrl('module/' . $module['slug']); ?>" class="module-card">
                    <div class="module-icon">
                        <?php if (!empty($module['icon'])): ?>
                            <i class="<?php echo $module['icon']; ?>"></i>
                        <?php else: ?>
                            <i class="fas fa-puzzle-piece"></i>
                        <?php endif; ?>
                    </div>
                    <div class="module-info">
                        <h3><?php echo $module['name']; ?></h3>
                        <?php
                        // Mostrar solo los permisos que el usuario tiene para este módulo
                        if (!empty($module['permissions'])):
                            $permissionLabels = [
                                'view' => 'Visualizar',
                                'edit' => 'Editar',
                                'admin' => 'Administrar'
                            ];
                            
                            // Filtrar solo los permisos conocidos (para evitar mostrar permisos personalizados)
                            $userPermissions = array_intersect($module['permissions'], array_keys($permissionLabels));
                        ?>
                            <div class="module-permissions">
                                <?php foreach ($userPermissions as $permission): ?>
                                    <span class="permission-badge">
                                        <?php echo $permissionLabels[$permission]; ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>