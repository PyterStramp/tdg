<!--app/Views/module_logs/user.php-->
<div class="content-header">
    <h1>Accesos del Usuario: <?php echo htmlspecialchars($user['username']); ?></h1>
    <div class="actions">
        <a href="<?php echo baseUrl('module_logs'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Registros
        </a>
        <?php if (isAdmin()): ?>
            <a href="<?php echo baseUrl('user_admin/edit/' . $user['id']); ?>" class="btn btn-primary">
                <i class="fas fa-user-edit"></i> Editar Usuario
            </a>
        <?php endif; ?>
    </div>
</div>

<?php showFlashMessage(); ?>

<div class="card mb-4">
    <div class="card-header">
        <h2>Detalles del Usuario</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <p><strong>Usuario:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Nombre completo:</strong> <?php echo htmlspecialchars($user['full_name'] ?: 'No disponible'); ?></p>
            </div>
            <div class="col-md-4">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Estado:</strong> 
                    <span class="badge <?php echo $user['active'] ? 'text-bg-success' : 'text-bg-secondary'; ?>">
                        <?php echo $user['active'] ? 'Activo' : 'Inactivo'; ?>
                    </span>
                </p>
            </div>
            <div class="col-md-4">
                <p><strong>Total de accesos:</strong> <?php echo $pagination['totalItems']; ?></p>
                <p><strong>Último acceso:</strong> <?php echo isset($user['last_login']) && $user['last_login'] ? date('d/m/Y H:i:s', strtotime($user['last_login'])) : 'Nunca'; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h2>Filtros</h2>
    </div>
    <div class="card-body">
        <form action="<?php echo baseUrl('module_logs/user/' . $user['id']); ?>" method="post" class="filter-form">
            <div class="row">
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="date_from">Fecha Desde</label>
                        <input type="date" id="date_from" name="date_from" class="form-control" value="<?php echo $filters['date_from']; ?>">
                    </div>
                </div>
                <div class="col-md-4">
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
                <a href="<?php echo baseUrl('module_logs/user/' . $user['id']); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-undo"></i> Reiniciar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>Módulos Accedidos</h3>
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
                                                <i class="fas fa-eye"></i> Ver Detalle
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
        <h2>Historial de Accesos</h2>
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
                                        }   else {
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
                                <a class="page-link" href="<?php echo baseUrl('module_logs/user/' . $user['id'] . '?page=1'); ?>">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                            </li>
                            
                            <!-- Página anterior -->
                            <li class="page-item <?php echo $pagination['page'] == 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo baseUrl('module_logs/user/' . $user['id'] . '?page=' . ($pagination['page'] - 1)); ?>">
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
                                    <a class="page-link" href="<?php echo baseUrl('module_logs/user/' . $user['id'] . '?page=' . $i); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Página siguiente -->
                            <li class="page-item <?php echo $pagination['page'] == $pagination['totalPages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo baseUrl('module_logs/user/' . $user['id'] . '?page=' . ($pagination['page'] + 1)); ?>">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                            </li>
                            
                            <!-- Última página -->
                            <li class="page-item <?php echo $pagination['page'] == $pagination['totalPages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo baseUrl('module_logs/user/' . $user['id'] . '?page=' . $pagination['totalPages']); ?>">
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
.progress {
    height: 25px;
    font-weight: bold;
}
.progress-bar {
    padding-left: 5px;
    text-align: left;
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
    const autoSubmitFields = ['module_id'];
    
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