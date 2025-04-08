<div class="content-header">
    <h1>Estadísticas de Acceso a Módulos</h1>
    <div class="actions">
        <a href="<?php echo baseUrl('module_logs'); ?>" class="btn btn-secondary">
            <i class="fas fa-list"></i> Ver Registros
        </a>
        <a href="<?php echo baseUrl('module_admin'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Módulos
        </a>
    </div>
</div>

<?php showFlashMessage(); ?>

<div class="card mb-4">
    <div class="card-header">
        <h2>Filtros</h2>
    </div>
    <div class="card-body">
        <form action="<?php echo baseUrl('module_logs/stats'); ?>" method="post" class="filter-form">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="user_id">Usuario</label>
                        <select id="user_id" name="user_id" class="form-control">
                            <option value="">Todos los usuarios</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>" <?php echo isset($filters['user_id']) && $filters['user_id'] == $user['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['full_name']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="module_id">Módulo</label>
                        <select id="module_id" name="module_id" class="form-control">
                            <option value="">Todos los módulos</option>
                            <?php foreach ($modules as $module): ?>
                                <option value="<?php echo $module['id']; ?>" <?php echo isset($filters['module_id']) && $filters['module_id'] == $module['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($module['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_from">Fecha Desde</label>
                        <input type="date" id="date_from" name="date_from" class="form-control" value="<?php echo $filters['date_from']; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_to">Fecha Hasta</label>
                        <input type="date" id="date_to" name="date_to" class="form-control" value="<?php echo $filters['date_to']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Aplicar Filtros
                </button>
                <a href="<?php echo baseUrl('module_logs/stats'); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-undo"></i> Reiniciar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h3>Módulos Más Accedidos</h3>
            </div>
            <div class="card-body">
                <?php if (empty($moduleSummary)): ?>
                    <p class="text-muted">No hay datos disponibles para el período seleccionado</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Módulo</th>
                                    <th>Accesos</th>
                                    <th style="width: 50%">Porcentaje</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalAccesses = array_sum(array_column($moduleSummary, 'access_count'));
                                foreach ($moduleSummary as $summary): 
                                    $percentage = ($summary['access_count'] / $totalAccesses) * 100;
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($summary['name']); ?></td>
                                        <td class="text-center"><?php echo $summary['access_count']; ?></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-info" role="progressbar" 
                                                     style="width: <?php echo number_format($percentage, 1); ?>%"
                                                     aria-valuenow="<?php echo number_format($percentage, 1); ?>" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    <?php echo number_format($percentage, 1); ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?php echo baseUrl('module_logs/module/' . $summary['id']); ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h3>Usuarios Más Activos</h3>
            </div>
            <div class="card-body">
                <?php if (empty($userSummary)): ?>
                    <p class="text-muted">No hay datos disponibles para el período seleccionado</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Accesos</th>
                                    <th style="width: 50%">Porcentaje</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalAccesses = array_sum(array_column($userSummary, 'access_count'));
                                foreach ($userSummary as $summary): 
                                    $percentage = ($summary['access_count'] / $totalAccesses) * 100;
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($summary['username']); ?></td>
                                        <td class="text-center"><?php echo $summary['access_count']; ?></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: <?php echo number_format($percentage, 1); ?>%"
                                                     aria-valuenow="<?php echo number_format($percentage, 1); ?>" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    <?php echo number_format($percentage, 1); ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?php echo baseUrl('module_logs/user/' . $summary['id']); ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.filter-form {
    margin-bottom: 0;
}
.form-actions {
    margin-top: 15px;
    display: flex;
    gap: 10px;
}
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.card-header h2, .card-header h3 {
    margin-bottom: 0;
}
.progress {
    height: 25px;
    font-weight: bold;
}
.progress-bar {
    padding-left: 5px;
    text-align: left;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar automáticamente los filtros al cambiar
    const filterForm = document.querySelector('.filter-form');
    const autoSubmitFields = ['user_id', 'module_id'];
    
    autoSubmitFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', function() {
                filterForm.submit();
            });
        }
    });
});
</script>