<?php
/**
 * ModuleAdminController - Controlador para la administración de módulos
 */
class ModuleAdminController extends Controller {
    private $moduleModel;
    private $permissionModel;
    private $roleModel;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->moduleModel = $this->loadModel('ModuleModel');
        $this->permissionModel = $this->loadModel('PermissionModel');
        $this->roleModel = $this->loadModel('RoleModel');
        
        // Verificar permisos de administrador
        requireRole('admin');
    }
    
    /**
     * Lista todos los módulos
     */
    public function index() {
        // Obtener todos los módulos
        $modules = $this->moduleModel->getAll();
        
        $data = [
            'pageTitle' => 'Administración de Módulos',
            'modules' => $modules
        ];

        //asegurar en un index
        $_SESSION['accessible_modules'] = $modules;
        
        $this->view->render('module_admin/index', $data);
    }
    
    /**
     * Muestra el formulario para crear un nuevo módulo
     */
    public function create() {
        $data = [
            'pageTitle' => 'Crear Nuevo Módulo'
        ];
        
        $this->view->render('module_admin/create', $data);
    }
    
    /**
     * Procesa la creación de un nuevo módulo
     */
    public function store() {
        // Verificar si es una solicitud POST
        if (!$this->isPost()) {
            $this->redirect('module_admin');
            return;
        }
        
        // Verificar token CSRF
        $csrfToken = $this->getPost('csrf_token');
        if (!validateCsrfToken($csrfToken)) {
            $this->redirectWithMessage('module_admin/create', 'Error de seguridad: token inválido', 'error');
            return;
        }
        
        // Obtener datos del formulario
        $name = $this->getPost('name');
        $slug = $this->getPost('slug') ?: $this->generateSlug($name);
        $description = $this->getPost('description');
        $icon = $this->getPost('icon');
        $active = $this->getPost('active') ? 1 : 0;
        $orderIndex = $this->getPost('order_index') ?: 0;
        
        // Validar los datos básicos
        if (empty($name)) {
            $this->redirectWithMessage('module_admin/create', 'El nombre del módulo es obligatorio', 'error');
            return;
        }
        
        // Verificar si el slug ya existe
        if ($this->moduleModel->slugExists($slug)) {
            $this->redirectWithMessage('module_admin/create', 'Ya existe un módulo con ese identificador', 'error');
            return;
        }
        
        // Crear directorio para el módulo
        $directoryPath = 'module-' . $slug; // Prefijo para identificar módulos
        $modulesPath = ROOT_PATH . '/app/modules';
        $modulePath = $modulesPath . '/' . $directoryPath;
        
        // Asegurarse de que existe el directorio principal de módulos
        if (!is_dir($modulesPath)) {
            if (!mkdir($modulesPath, 0755, true)) {
                $this->redirectWithMessage('module_admin/create', 'Error: No se pudo crear el directorio principal de módulos', 'error');
                return;
            }
        }
        
        // Crear directorio para este módulo específico
        if (!$this->createModuleDirectory($modulePath)) {
            $this->redirectWithMessage('module_admin/create', 'Error: No se pudo crear el directorio del módulo', 'error');
            return;
        }
        
        // Manejar la subida de archivos
        $entryFile = 'index.html'; // Por defecto
        
        if (isset($_FILES['module_files']) && $_FILES['module_files']['name'][0] !== '') {
            $uploadResult = $this->handleFileUploads($_FILES['module_files'], $modulePath);
            
            if (!$uploadResult['success']) {
                // Eliminar el directorio creado si hay error
                $this->removeModuleDirectory($modulePath);
                $this->redirectWithMessage('module_admin/create', $uploadResult['message'], 'error');
                return;
            }
            
            // Si se subió un archivo de entrada diferente
            if (isset($uploadResult['entry_file'])) {
                $entryFile = $uploadResult['entry_file'];
            }
        } else {
            // Si no se subieron archivos, crear un index.html básico
            $this->createDefaultIndexFile($modulePath);
        }
        
        // Preparar datos para guardar en la base de datos
        $moduleData = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'icon' => $icon ?: 'fas fa-puzzle-piece', // Icono por defecto
            'directory_path' => $directoryPath,
            'entry_file' => $entryFile,
            'active' => $active,
            'order_index' => $orderIndex
        ];
        
        // Guardar en la base de datos
        $moduleId = $this->moduleModel->insert($moduleData);
        
        if (!$moduleId) {
            // Si falla, eliminar el directorio creado
            $this->removeModuleDirectory($modulePath);
            $this->redirectWithMessage('module_admin/create', 'Error al guardar el módulo en la base de datos', 'error');
            return;
        }
        
        // Asignar permisos por defecto (los administradores pueden ver el módulo)
        $this->assignDefaultPermissions($moduleId);
        
        // Registrar en el log
        logMessage("Módulo creado: {$name} (ID: {$moduleId}) por el usuario " . getCurrentUsername(), 'info');
        
        // Redirigir con mensaje de éxito
        $this->redirectWithMessage('module_admin', 'Módulo creado correctamente', 'success');
    }
    
    /**
     * Muestra el formulario para editar un módulo
     * 
     * @param int $id ID del módulo a editar
     */
    public function edit($id = null) {
        // Validar que se proporcionó un ID
        if (!$id) {
            $this->redirectWithMessage('module_admin', 'Módulo no especificado', 'error');
            return;
        }
        
        // Obtener datos del módulo
        $module = $this->moduleModel->getById($id);
        
        if (!$module) {
            $this->redirectWithMessage('module_admin', 'Módulo no encontrado', 'error');
            return;
        }
        
        $data = [
            'pageTitle' => 'Editar Módulo',
            'module' => $module
        ];
        
        $this->view->render('module_admin/edit', $data);
    }
    
    /**
     * Procesa la actualización de un módulo
     * 
     * @param int $id ID del módulo a actualizar
     */
    public function update($id = null) {
        // Validar que se proporcionó un ID
        if (!$id) {
            $this->redirectWithMessage('module_admin', 'Módulo no especificado', 'error');
            return;
        }
        
        // Verificar si es una solicitud POST
        if (!$this->isPost()) {
            $this->redirect('module_admin');
            return;
        }
        
        // Verificar token CSRF
        $csrfToken = $this->getPost('csrf_token');
        if (!validateCsrfToken($csrfToken)) {
            $this->redirectWithMessage('module_admin/edit/' . $id, 'Error de seguridad: token inválido', 'error');
            return;
        }
        
        // Obtener módulo existente
        $module = $this->moduleModel->getById($id);
        
        if (!$module) {
            $this->redirectWithMessage('module_admin', 'Módulo no encontrado', 'error');
            return;
        }
        
        // Obtener datos del formulario
        $name = $this->getPost('name');
        $slug = $this->getPost('slug') ?: $this->generateSlug($name);
        $description = $this->getPost('description');
        $icon = $this->getPost('icon');
        $active = $this->getPost('active') ? 1 : 0;
        $orderIndex = $this->getPost('order_index') ?: 0;
        
        // Validar los datos básicos
        if (empty($name)) {
            $this->redirectWithMessage('module_admin/edit/' . $id, 'El nombre del módulo es obligatorio', 'error');
            return;
        }
        
        // Verificar si el slug ya existe (excluyendo el módulo actual)
        if ($slug !== $module['slug'] && $this->moduleModel->slugExists($slug, $id)) {
            $this->redirectWithMessage('module_admin/edit/' . $id, 'Ya existe un módulo con ese identificador', 'error');
            return;
        }
        
        // Verificar si es necesario renombrar el directorio
        $oldPath = ROOT_PATH . '/app/modules/' . $module['directory_path'];
        $newDirectoryPath = ($slug !== $module['slug']) ? 'module-' . $slug : $module['directory_path'];
        $newPath = ROOT_PATH . '/app/modules/' . $newDirectoryPath;
        
        if ($slug !== $module['slug']) {
            // Renombrar directorio
            if (is_dir($oldPath)) {
                if (!rename($oldPath, $newPath)) {
                    $this->redirectWithMessage('module_admin/edit/' . $id, 'No se pudo renombrar el directorio del módulo', 'error');
                    return;
                }
            } else {
                // Si no existe el directorio, crearlo
                if (!$this->createModuleDirectory($newPath)) {
                    $this->redirectWithMessage('module_admin/edit/' . $id, 'No se pudo crear el directorio del módulo', 'error');
                    return;
                }
                // Crear archivo index.html por defecto
                $this->createDefaultIndexFile($newPath);
            }
        }
        
        // Manejar la subida de archivos (para actualizaciones)
        if (isset($_FILES['module_files']) && $_FILES['module_files']['name'][0] !== '') {
            $uploadResult = $this->handleFileUploads($_FILES['module_files'], $newPath);
            
            if (!$uploadResult['success']) {
                $this->redirectWithMessage('module_admin/edit/' . $id, $uploadResult['message'], 'error');
                return;
            }
            
            // Si se subió un archivo de entrada, actualizar
            if (isset($uploadResult['entry_file'])) {
                $entryFile = $uploadResult['entry_file'];
            } else {
                $entryFile = $module['entry_file']; // Mantener el actual
            }
        } else {
            $entryFile = $module['entry_file']; // Mantener el actual
        }
        
        // Preparar datos para actualizar en la base de datos
        $moduleData = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'icon' => $icon ?: 'fas fa-puzzle-piece',
            'directory_path' => $newDirectoryPath,
            'entry_file' => $entryFile,
            'active' => $active,
            'order_index' => $orderIndex
        ];
        
        // Actualizar en la base de datos
        $success = $this->moduleModel->update($id, $moduleData);
        
        if (!$success) {
            $this->redirectWithMessage('module_admin/edit/' . $id, 'Error al actualizar el módulo en la base de datos', 'error');
            return;
        }
        
        // Registrar en el log
        logMessage("Módulo actualizado: {$name} (ID: {$id}) por el usuario " . getCurrentUsername(), 'info');
        
        // Redirigir con mensaje de éxito
        $this->redirectWithMessage('module_admin', 'Módulo actualizado correctamente', 'success');
    }
    
    /**
     * Elimina un módulo
     * 
     * @param int $id ID del módulo a eliminar
     */
    public function delete($id = null) {
        // Validar que se proporcionó un ID
        if (!$id) {
            $this->redirectWithMessage('module_admin', 'Módulo no especificado', 'error');
            return;
        }
        
        // Obtener datos del módulo
        $module = $this->moduleModel->getById($id);
        
        if (!$module) {
            $this->redirectWithMessage('module_admin', 'Módulo no encontrado', 'error');
            return;
        }
        
        // Eliminar el directorio del módulo
        $modulePath = ROOT_PATH . '/app/modules/' . $module['directory_path'];
        $this->removeModuleDirectory($modulePath);
        
        // Eliminar de la base de datos (esto eliminará automáticamente los permisos asociados gracias a la restricción ON DELETE CASCADE)
        $success = $this->moduleModel->delete($id);
        
        if (!$success) {
            $this->redirectWithMessage('module_admin', 'Error al eliminar el módulo', 'error');
            return;
        }
        
        // Registrar en el log
        logMessage("Módulo eliminado: {$module['name']} (ID: {$id}) por el usuario " . getCurrentUsername(), 'info');
        
        // Redirigir con mensaje de éxito
        $this->redirectWithMessage('module_admin', 'Módulo eliminado correctamente', 'success');
    }
    
    /**
     * Actualiza el estado activo de un módulo
     * 
     * @param int $id ID del módulo
     */
    public function toggle($id = null) {
        // Validar que se proporcionó un ID
        if (!$id) {
            $this->redirectWithMessage('module_admin', 'Módulo no especificado', 'error');
            return;
        }
        
        // Obtener datos del módulo
        $module = $this->moduleModel->getById($id);
        
        if (!$module) {
            $this->redirectWithMessage('module_admin', 'Módulo no encontrado', 'error');
            return;
        }
        
        // Cambiar estado
        $newStatus = $module['active'] ? 0 : 1;
        $success = $this->moduleModel->updateActiveStatus($id, $newStatus);
        
        if (!$success) {
            $this->redirectWithMessage('module_admin', 'Error al actualizar el estado del módulo', 'error');
            return;
        }
        
        // Registrar en el log
        $statusText = $newStatus ? 'activado' : 'desactivado';
        logMessage("Módulo {$statusText}: {$module['name']} (ID: {$id}) por el usuario " . getCurrentUsername(), 'info');
        
        // Redirigir con mensaje de éxito
        $this->redirectWithMessage('module_admin', "Módulo {$statusText} correctamente", 'success');
    }
    
    /**
     * Muestra la página de gestión de permisos de un módulo
     * 
     * @param int $id ID del módulo
     */
    public function permissions($id = null) {
        // Validar que se proporcionó un ID
        if (!$id) {
            $this->redirectWithMessage('module_admin', 'Módulo no especificado', 'error');
            return;
        }
        
        // Obtener datos del módulo
        $module = $this->moduleModel->getById($id);
        
        if (!$module) {
            $this->redirectWithMessage('module_admin', 'Módulo no encontrado', 'error');
            return;
        }
        
        // Obtener roles
        $roles = $this->roleModel->getAll();
        
        // Para cada rol, obtener sus permisos para este módulo
        foreach ($roles as &$role) {
            $role['modulePermissions'] = $this->moduleModel->getModuleRolePermissions($role['id'], $id);
        }
        
        // Obtener todos los permisos disponibles
        $permissions = $this->permissionModel->getAll();
        
        $data = [
            'pageTitle' => 'Gestión de Permisos: ' . $module['name'],
            'module' => $module,
            'roles' => $roles,
            'permissions' => $permissions
        ];
        
        $this->view->render('module_admin/permissions', $data);
    }
    
    /**
     * Procesa la actualización de permisos de un módulo
     * 
     * @param int $id ID del módulo
     */
    public function updatePermissions($id = null) {
        // Validar que se proporcionó un ID
        if (!$id) {
            $this->redirectWithMessage('module_admin', 'Módulo no especificado', 'error');
            return;
        }
        
        // Verificar si es una solicitud POST
        if (!$this->isPost()) {
            $this->redirect('module_admin');
            return;
        }
        
        // Verificar token CSRF
        $csrfToken = $this->getPost('csrf_token');
        if (!validateCsrfToken($csrfToken)) {
            $this->redirectWithMessage('module_admin/permissions/' . $id, 'Error de seguridad: token inválido', 'error');
            return;
        }
        
        // Obtener datos del módulo
        $module = $this->moduleModel->getById($id);
        
        if (!$module) {
            $this->redirectWithMessage('module_admin', 'Módulo no encontrado', 'error');
            return;
        }
        
        // Obtener roles y permisos
        $roles = $this->roleModel->getAll();
        $permissions = $this->permissionModel->getAll();
        
        // Procesar los permisos enviados
        $rolePermissions = $this->getPost('role_permissions');
        
        if (!$rolePermissions || !is_array($rolePermissions)) {
            $this->redirectWithMessage('module_admin/permissions/' . $id, 'No se recibieron permisos para actualizar', 'error');
            return;
        }
        
        // Para cada rol, actualizar sus permisos
        foreach ($roles as $role) {
            // Obtener los permisos actuales del rol para este módulo
            $currentPermissions = $this->moduleModel->getModuleRolePermissions($role['id'], $id);
            
            // Permisos enviados para este rol
            $newPermissions = isset($rolePermissions[$role['id']]) ? $rolePermissions[$role['id']] : [];
            
            // Permisos a añadir
            $permissionsToAdd = array_diff($newPermissions, $currentPermissions);
            foreach ($permissionsToAdd as $permissionId) {
                $this->moduleModel->assignPermission($role['id'], $id, $permissionId);
            }
            
            // Permisos a eliminar
            $permissionsToRemove = array_diff($currentPermissions, $newPermissions);
            foreach ($permissionsToRemove as $permissionId) {
                $this->moduleModel->revokePermission($role['id'], $id, $permissionId);
            }
        }
        
        // Registrar en el log
        logMessage("Permisos actualizados para el módulo: {$module['name']} (ID: {$id}) por el usuario " . getCurrentUsername(), 'info');
        
        // Redirigir con mensaje de éxito
        $this->redirectWithMessage('module_admin/permissions/' . $id, 'Permisos actualizados correctamente', 'success');
    }
    
    /**
     * Muestra estadísticas de uso de un módulo
     * 
     * @param int $id ID del módulo
     */
    public function stats($id = null) {
        // Validar que se proporcionó un ID
        if (!$id) {
            $this->redirectWithMessage('module_admin', 'Módulo no especificado', 'error');
            return;
        }
        
        // Obtener datos del módulo
        $module = $this->moduleModel->getById($id);
        
        if (!$module) {
            $this->redirectWithMessage('module_admin', 'Módulo no encontrado', 'error');
            return;
        }
        
        // Obtener parámetros de filtro
        $startDate = $this->getPost('start_date') ?: date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->getPost('end_date') ?: date('Y-m-d');
        
        // Obtener estadísticas
        $stats = $this->moduleModel->getModuleUsageStats($id, $startDate, $endDate);
        
        $data = [
            'pageTitle' => 'Estadísticas: ' . $module['name'],
            'module' => $module,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
        
        $this->view->render('module_admin/stats', $data);
    }
    
    /**
     * Genera un slug a partir del nombre del módulo
     */
    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Crea el directorio para el módulo
     */
    private function createModuleDirectory($path) {
        if (!is_dir($path)) {
            return mkdir($path, 0755, true);
        }
        return true;
    }
    
    /**
     * Elimina el directorio de un módulo
     */
    private function removeModuleDirectory($path) {
        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);
            foreach ($files as $file) {
                $filePath = $path . '/' . $file;
                if (is_dir($filePath)) {
                    $this->removeModuleDirectory($filePath);
                } else {
                    unlink($filePath);
                }
            }
            return rmdir($path);
        }
        return true;
    }
    
    /**
     * Crea un archivo index.html por defecto
     */
    private function createDefaultIndexFile($path) {
        $content = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo</title>
    <style>
        .module-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        h1 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="module-container">
        <h1>Módulo Nuevo</h1>
        <p>Este es un módulo recién creado. Puedes personalizar este contenido según tus necesidades.</p>
    </div>
</body>
</html>';
        
        $filePath = $path . '/index.html';
        return file_put_contents($filePath, $content);
    }
    
    /**
     * Maneja la subida de archivos para el módulo
     */
    private function handleFileUploads($files, $targetDir) {
        $result = [
            'success' => true,
            'message' => '',
            'entry_file' => null
        ];
        
        // Tipos de archivo permitidos
        $allowedTypes = [
            'text/html' => 'html',
            'text/css' => 'css',
            'application/javascript' => 'js',
            'text/javascript' => 'js',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/svg+xml' => 'svg',
            'application/json' => 'json',
            'text/plain' => 'txt'
        ];
        
        // Verificar que el directorio exista
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                $result['success'] = false;
                $result['message'] = 'No se pudo crear el directorio para el módulo';
                return $result;
            }
        }
        
        // Procesar cada archivo
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $files['tmp_name'][$i];
                $fileName = basename($files['name'][$i]);
                $fileType = $files['type'][$i];
                
                // Validar tipo de archivo
                $isAllowed = false;
                foreach ($allowedTypes as $mimeType => $ext) {
                    if (strpos($fileType, $mimeType) !== false || pathinfo($fileName, PATHINFO_EXTENSION) === $ext) {
                        $isAllowed = true;
                        break;
                    }
                }
                
                if (!$isAllowed) {
                    $result['success'] = false;
                    $result['message'] = 'Tipo de archivo no permitido: ' . $fileName;
                    break;
                }
                
                // Validar archivo HTML (escaneando contenido malicioso)
                //TODO: CREAR UN SANITIZADOR LO SUFICIENTEMENTE ROBUSTO COMO PARA NO JODER ALGO
                /*
                if ($fileType == 'text/html' || pathinfo($fileName, PATHINFO_EXTENSION) === 'html') {
                    $content = file_get_contents($tmpName);
                    if ($this->containsMaliciousCode($content)) {
                        $result['success'] = false;
                        $result['message'] = 'El archivo HTML contiene código potencialmente peligroso: ' . $fileName;
                        break;
                    }
                }*/
                
                // Generar nombre de archivo seguro
                $filePath = $targetDir . '/' . $fileName;
                
                // Mover el archivo
                if (!move_uploaded_file($tmpName, $filePath)) {
                    $result['success'] = false;
                    $result['message'] = 'Error al subir el archivo: ' . $fileName;
                    break;
                }
                
                // Si es index.html, marcarlo como archivo de entrada
                if ($fileName == 'index.html') {
                    $result['entry_file'] = $fileName;
                }
            } else if ($files['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                $result['success'] = false;
                $result['message'] = 'Error al subir el archivo: ' . $this->uploadErrorMessage($files['error'][$i]);
                break;
            }
        }
        
        return $result;
    }
    
    /**
     * Verifica si un contenido HTML tiene código malicioso
     */
    private function containsMaliciousCode($content) {
        // Patrones de código potencialmente malicioso
        $maliciousPatterns = [
            // Scripts potencialmente peligrosos
            '/eval\s*\(/i',
            '/document\.cookie/i',
            '/<script[^>]*src=["\'](https?:)?\/\/(?!.*\.trusted\.com)/i',
            '/base64_decode\s*\(/i',
            // iframes no seguros
            '/<iframe[^>]*src=["\'](https?:)?\/\/(?!.*\.trusted\.com)/i',
            // Eventos JavaScript potencialmente peligrosos
            '/on(load|click|mouseover|error|submit|focus|blur|change)=["\']/i'
        ];
        
        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Devuelve un mensaje descriptivo para errores de subida
     */
    private function uploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'El archivo excede el tamaño máximo permitido por PHP';
            case UPLOAD_ERR_FORM_SIZE:
                return 'El archivo excede el tamaño máximo permitido por el formulario';
            case UPLOAD_ERR_PARTIAL:
                return 'El archivo solo fue subido parcialmente';
            case UPLOAD_ERR_NO_FILE:
                return 'No se subió ningún archivo';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'No se encontró el directorio temporal';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Error al escribir el archivo en el disco';
            case UPLOAD_ERR_EXTENSION:
                return 'Una extensión PHP detuvo la subida del archivo';
            default:
                return 'Error desconocido al subir el archivo';
        }
    }
    
    /**
     * Asigna permisos por defecto al módulo
     */
    private function assignDefaultPermissions($moduleId) {
        // Obtener el ID del rol de administrador
        $adminRole = $this->roleModel->getByName('admin');
        
        if (!$adminRole) {
            return false;
        }
        
        // Obtener permisos disponibles
        $permissions = $this->permissionModel->getAll();
        
        // Asignar todos los permisos al rol de administrador
        foreach ($permissions as $permission) {
            $this->moduleModel->assignPermission($adminRole['id'], $moduleId, $permission['id']);
        }
        
        return true;
    }
}