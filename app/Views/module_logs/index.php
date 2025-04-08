<!--app/Views/module_logs/index.php-->
<div class="content-header">
    <h1>Registros de Acceso a Módulos</h1>
    <div class="actions">
        <a href="<?php echo baseUrl('module_logs/stats'); ?>" class="btn btn-info">
            <i class="fas fa-chart-bar"></i> Ver Estadísticas
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
        <form action="<?php echo baseUrl('module_logs'); ?>" method="post" class="filter-form">
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
                <a href="<?php echo baseUrl('module_logs'); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-undo"></i> Reiniciar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
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
                                    <th class="text-center">Accesos</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($moduleSummary, 0, 5) as $summary): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($summary['name']); ?></td>
                                        <td class="text-center"><?php echo $summary['access_count']; ?></td>
                                        <td class="text-right">
                                            <a href="<?php echo baseUrl('module_logs/module/' . $summary['id']); ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Detalles
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
    <div class="col-md-6">
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
                                    <th class="text-center">Accesos</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($userSummary, 0, 5) as $summary): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($summary['username']); ?></td>
                                        <td class="text-center"><?php echo $summary['access_count']; ?></td>
                                        <td class="text-right">
                                            <a href="<?php echo baseUrl('module_logs/user/' . $summary['id']); ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Detalles
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

<div class="card">
    <div class="card-header">
        <h2>Registros de Acceso</h2>
        <div class="card-header-info">
            Total: <?php echo $pagination['totalItems']; ?> registros
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($logs)): ?>
            <div class="alert alert-info">
                No se encontraron registros de acceso para los filtros seleccionados.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Módulo</th>
                            <th>Dirección IP</th>
                            <th>Navegador</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($log['access_timestamp'])); ?></td>
                                <td>
                                    <a href="<?php echo baseUrl('module_logs/user/' . $log['user_id']); ?>">
                                        <?php echo htmlspecialchars($log['username']); ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?php echo baseUrl('module_logs/module/' . $log['module_id']); ?>">
                                        <?php echo htmlspecialchars($log['module_name']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                <td>
                                    <?php 
                                    $userAgent = $log['user_agent'];
                                    // Simplificar la información del user-agent
                                    if (preg_match('/Chrome\/([0-9.]+)/', $userAgent, $matches)) {
                                        echo '<i class="fab fa-chrome"></i>'.' Chrome ' . $matches[1];
                                    } elseif (preg_match('/Firefox\/([0-9.]+)/', $userAgent, $matches)) {
                                        echo '<i class="fab fa-firefox-browser"></i>'.' Firefox ' . $matches[1];
                                    } elseif (preg_match('/Edge\/([0-9.]+)/', $userAgent, $matches)) {
                                        echo '<i class="fab fa-edge"></i>'.' Edge ' . $matches[1];
                                    } elseif (preg_match('/Safari\/([0-9.]+)/', $userAgent, $matches)) {
                                        echo '<i class="fab fa-safari"></i>'.' Safari ' . $matches[1];
                                    } else {
                                        echo '<i class="fas fa-window-maximize"></i> '.htmlspecialchars(substr($userAgent, 0, 50)) . '...';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if ($pagination['totalPages'] > 1): ?>
                <div class="pagination-container">
                    <nav aria-label="Navegación de páginas">
                        <ul class="pagination">
                            <!-- Primera página -->
                            <li class="page-item <?php echo $pagination['page'] == 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo baseUrl('module_logs?page=1'); ?>">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                            </li>
                            
                            <!-- Página anterior -->
                            <li class="page-item <?php echo $pagination['page'] == 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo baseUrl('module_logs?page=' . ($pagination['page'] - 1)); ?>">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            </li>
                            
                            <!-- Páginas -->
                            <?php 
                            $startPage = max(1, $pagination['page'] - 2);
                            $endPage = min($pagination['totalPages'], $pagination['page'] + 2);
                            
                            for ($i = $startPage; $i <= $endPage; $i++): 
                            ?>
                                <li class="page-item <?php echo $pagination['page'] == $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo baseUrl('module_logs?page=' . $i); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Página siguiente -->
                            <li class="page-item <?php echo $pagination['page'] == $pagination['totalPages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo baseUrl('module_logs?page=' . ($pagination['page'] + 1)); ?>">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                            </li>
                            
                             <!-- Última página -->
                            <li class="page-item <?php echo $pagination['page'] == $pagination['totalPages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo baseUrl('module_logs?page=' . $pagination['totalPages']); ?>">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <div class="pagination-info">
                        Mostrando <?php echo (($pagination['page'] - 1) * $pagination['perPage']) + 1; ?> - 
                        <?php echo min($pagination['page'] * $pagination['perPage'], $pagination['totalItems']); ?> 
                        de <?php echo $pagination['totalItems']; ?> registros
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
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
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
}
.pagination-info {
    color: #6c757d;
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