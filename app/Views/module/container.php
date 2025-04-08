<style>
    .module-actions {
        display: flex;
        justify-content: center;
        gap: 20px;
        padding: 20px;
    }
</style>
<div class="module-container">
    <div class="module-header">
        <h1><?php echo $module['name']; ?></h1>
        <?php if (!empty($module['description'])): ?>
            <p class="module-description"><?php echo $module['description']; ?></p>
        <?php endif; ?>
    </div>
    
    <?php showFlashMessage(); ?>
    
    <div class="module-content">
        <?php if (isset($moduleContent)): ?>
            <?php echo $moduleContent; ?>
        <?php else: ?>
            <div class="alert alert-error">
                Error al cargar el contenido del módulo.
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="module-actions">
    <a href="<?php echo baseUrl(); ?>" class="btn">
        <i class="fas fa-arrow-left"></i> Volver al Dashboard
    </a>
    
    <?php if (userHasPermission($module['slug'], 'admin')): ?>
        <a href="<?php echo baseUrl('/module_admin/edit/' . $module['id']); ?>" class="btn btn-primary">
            <i class="fas fa-cog"></i> Configurar Módulo
        </a>
    <?php endif; ?>
</div>