<!--app/Views/module_admin/edit.php-->
<div class="content-header">
    <h1>Editar Módulo</h1>
    <div class="actions">
        <a href="<?php echo baseUrl('module_admin'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
</div>

<?php showFlashMessage(); ?>

<div class="card">
    <div class="card-header">
        <h2>Información del Módulo</h2>
    </div>
    <div class="card-body">
        <form action="<?php echo baseUrl('module_admin/update/' . $module['id']); ?>" method="post" enctype="multipart/form-data" class="form">
            <!-- Campo oculto para CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="name">Nombre del Módulo <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($module['name']); ?>" required>
                    <div class="form-text text-muted">Nombre descriptivo para el módulo.</div>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="slug">Identificador (Slug)</label>
                    <input type="text" id="slug" name="slug" class="form-control" pattern="[a-z0-9-]+" value="<?php echo htmlspecialchars($module['slug']); ?>">
                    <div class="form-text text-muted">Identificador único para el módulo (solo letras minúsculas, números y guiones). Si se deja en blanco, se generará automáticamente a partir del nombre.</div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($module['description']); ?></textarea>
                <div class="form-text text-muted">Descripción breve sobre la funcionalidad del módulo.</div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="icon">Icono</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i id="icon-preview" class="<?php echo htmlspecialchars($module['icon']); ?>"></i></span>
                        </div>
                        <input type="text" id="icon" name="icon" class="form-control" placeholder="fas fa-puzzle-piece" value="<?php echo htmlspecialchars($module['icon']); ?>">
                    </div>
                    <div class="form-text text-muted">Clase de icono FontAwesome (ej: fas fa-home). <a href="https://fontawesome.com/v5/icons" target="_blank">Ver iconos disponibles</a>.</div>
                </div>
                
                <div class="form-group col-md-3">
                    <label for="order_index">Orden</label>
                    <input type="number" id="order_index" name="order_index" class="form-control" min="0" value="<?php echo (int)$module['order_index']; ?>">
                    <div class="form-text text-muted">Posición de ordenamiento en el menú.</div>
                </div>
                
                <div class="form-group col-md-3">
                    <label for="active">Estado</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="active" name="active" <?php echo $module['active'] ? 'checked' : ''; ?>>
                        <label for="active">
                            <span class="toggle-track"></span>
                            <span class="toggle-text"><?php echo $module['active'] ? 'Activo' : 'Inactivo'; ?></span>
                        </label>
                    </div>
                    <div class="form-text text-muted">Indica si el módulo está disponible para los usuarios.</div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="module_files">Archivos del Módulo</label>
                <input type="file" id="module_files" name="module_files[]" class="form-control-file" multiple>
                <div class="form-text text-muted">
                <p>Seleccione los archivos que conforman el módulo. Puede subir múltiples archivos a la vez.</p>
                    <p>Los archivos deben estar en la misma ruta, entonces asegúrese que los archivos y sus referencias estén en un único directorio, es decir no es soportado referencias tipo img/hero.jpg</p>
                    <p>Tipos de archivo permitidos: HTML, CSS, JavaScript, imágenes (JPG, PNG, GIF, SVG).</p>
                    <p>Si incluye un archivo llamado <code>index.html</code>, este será utilizado como entrada principal del módulo.</p>
                    <p>Si no sube ningún archivo, se creará un archivo <code>index.html</code> básico.</p>
                    <p>Es recomendable no tener código CSS que afecte de manera global, ya sea estilos body o html, porque las incongruencias visuales podrían ser insospechadas.</p>
                </div>
            </div>
            
            <div class="module-info-section">
                <h3>Información del Módulo</h3>
                <div class="info-item">
                    <strong>Directorio:</strong> <?php echo htmlspecialchars($module['directory_path']); ?>
                </div>
                <div class="info-item">
                    <strong>Archivo de entrada:</strong> <?php echo htmlspecialchars($module['entry_file']); ?>
                </div>
                <div class="info-item">
                    <strong>Fecha de creación:</strong> <?php echo isset($module['created_at']) ? date('d/m/Y H:i', strtotime($module['created_at'])) : 'No disponible'; ?>
                </div>
                <div class="info-item">
                    <strong>Última actualización:</strong> <?php echo isset($module['updated_at']) ? date('d/m/Y H:i', strtotime($module['updated_at'])) : 'No disponible'; ?>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <a href="<?php echo baseUrl('module_admin'); ?>" class="btn btn-secondary">
                    Cancelar
                </a>
                <a href="<?php echo baseUrl('module/' . $module['slug']); ?>" class="btn btn-info" target="_blank">
                    <i class="fas fa-eye"></i> Ver Módulo
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generar slug automáticamente a partir del nombre
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    //generar un debounce para un proceso optimizable
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(()=> func.apply(this, args), delay)
        }
    }

    const updateSlug = debounce(function() {
        slugInput.value = generateSlug(nameInput.value);
    }, 300);

    nameInput.addEventListener('input', updateSlug);
    
    // Guardar el valor original del slug
    slugInput.dataset.original = slugInput.value;
    
    function generateSlug(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')         // Reemplazar espacios por guiones
            .replace(/[^\w\-]+/g, '')     // Remover caracteres especiales
            .replace(/\-\-+/g, '-')       // Reemplazar múltiples guiones por uno solo
            .replace(/^-+/, '')           // Eliminar guiones al inicio
            .replace(/-+$/, '');          // Eliminar guiones al final
    }
    
    // Vista previa del icono
    const iconInput = document.getElementById('icon');
    const iconPreview = document.getElementById('icon-preview');
    const defaultIcon = 'fas fa-puzzle-piece';
    
    iconInput.addEventListener('input', function() {
        if (iconInput.value.trim()) {
            // Eliminar todas las clases
            iconPreview.className = '';
            
            // Añadir las nuevas clases
            const iconClasses = iconInput.value.trim().split(' ');
            iconClasses.forEach(cls => {
                iconPreview.classList.add(cls);
            });
        } else {
            // Restaurar el icono por defecto
            iconPreview.className = defaultIcon;
        }
    });
    
    // Actualizar el texto del interruptor de estado
    const activeCheckbox = document.getElementById('active');
    const toggleText = activeCheckbox.parentNode.querySelector('.toggle-text');
    
    activeCheckbox.addEventListener('change', function() {
        toggleText.textContent = this.checked ? 'Activo' : 'Inactivo';
    });
});
</script>

<style>
/* Estilos para el interruptor de activación */
.toggle-switch {
    position: relative;
    margin-top: 10px;
}

.toggle-switch input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-track {
    display: inline-block;
    position: relative;
    width: 50px;
    height: 24px;
    background-color: #ccc;
    border-radius: 12px;
    transition: background-color 0.3s;
    vertical-align: middle;
}

.toggle-track:before {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 18px;
    height: 18px;
    background-color: white;
    border-radius: 50%;
    transition: transform 0.3s;
}

.toggle-text {
    margin-left: 10px;
    vertical-align: middle;
}

.toggle-switch input:checked + label .toggle-track {
    background-color: #4CAF50;
}

.toggle-switch input:checked + label .toggle-track:before {
    transform: translateX(26px);
}

.toggle-switch input:focus + label .toggle-track {
    box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.25);
}

/* Estilos para sección de información */
.module-info-section {
    margin-top: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 5px;
    border-left: 4px solid #6c757d;
}

.module-info-section h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #495057;
    font-size: 1.2rem;
}

.info-item {
    margin-bottom: 8px;
}

.info-item strong {
    font-weight: 600;
    color: #495057;
}

.form-actions {
    margin-top: 30px;
    display: flex;
    gap: 10px;
}
</style>