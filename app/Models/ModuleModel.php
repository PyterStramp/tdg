<?php
/**
 * ModuleModel - Modelo para la gestión de módulos
 */
class ModuleModel extends Model {
    protected $table = 'modules';
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Obtiene un módulo por su slug
     * 
     * @param string $slug Slug del módulo
     * @return array|null Módulo encontrado o null
     */
    public function getBySlug($slug) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE slug = ?
        ");
        
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    /**
     * Verifica si un slug ya existe
     * 
     * @param string $slug Slug a verificar
     * @param int $excludeId ID de módulo a excluir de la verificación (opcional)
     * @return bool True si existe, false si no
     */
    public function slugExists($slug, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM {$this->table}
                WHERE slug = ? AND id != ?
            ");
            
            $stmt->bind_param('si', $slug, $excludeId);
        } else {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM {$this->table}
                WHERE slug = ?
            ");
            
            $stmt->bind_param('s', $slug);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    /**
     * Registra un acceso a un módulo
     * 
     * @param int $userId ID del usuario
     * @param int $moduleId ID del módulo
     * @param string $ipAddress Dirección IP del usuario
     * @param string $userAgent User agent del navegador
     * @param string $sessionId ID de la sesión
     * @return bool True si se registró correctamente, false si no
     */
    public function logAccess($userId, $moduleId, $ipAddress, $userAgent, $sessionId) {
        $stmt = $this->db->prepare("
            INSERT INTO module_access_logs 
            (user_id, module_id, ip_address, user_agent, session_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param('iisss', $userId, $moduleId, $ipAddress, $userAgent, $sessionId);
        
        return $stmt->execute();
    }
    
    /**
     * Obtiene los módulos accesibles para un usuario
     * 
     * @param int $userId ID del usuario
     * @return array Lista de módulos accesibles
     */
    public function getAccessibleModules($userId) {
        // Obtener los roles del usuario
        $query = "
            SELECT DISTINCT r.id
            FROM roles r
            JOIN user_roles ur ON r.id = ur.role_id
            WHERE ur.user_id = ?
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $rolesResult = $stmt->get_result();
        
        $roleIds = [];
        while ($role = $rolesResult->fetch_assoc()) {
            $roleIds[] = $role['id'];
        }
        
        if (empty($roleIds)) {
            return [];
        }
        
        // Convertir array a string para la consulta IN
        $roleIdsStr = implode(',', $roleIds);
        
        // Obtener módulos accesibles basados en los roles y permisos
        $query = "
            SELECT DISTINCT m.*, 
            (
                SELECT GROUP_CONCAT(p.name)
                FROM role_module_permissions rmp
                JOIN permissions p ON rmp.permission_id = p.id
                WHERE rmp.module_id = m.id AND rmp.role_id IN ({$roleIdsStr})
            ) as permissions
            FROM {$this->table} m
            JOIN role_module_permissions rmp ON m.id = rmp.module_id
            WHERE rmp.role_id IN ({$roleIdsStr})
            AND m.active = 1
            AND EXISTS (
                SELECT 1 FROM role_module_permissions rmp2
                JOIN permissions p ON rmp2.permission_id = p.id
                WHERE rmp2.module_id = m.id 
                AND rmp2.role_id IN ({$roleIdsStr})
                AND p.name = 'view'
            )
            ORDER BY m.order_index ASC, m.name ASC
        ";
        
        $result = $this->db->query($query);
        
        $modules = [];
        while ($module = $result->fetch_assoc()) {
            // Convertir la cadena de permisos a un array
            $permissionsStr = $module['permissions'];
            if ($permissionsStr) {
                $module['permissions'] = array_unique(explode(',', $permissionsStr));
            } else {
                $module['permissions'] = [];
            }
            
            $modules[] = $module;
        }
        
        return $modules;
    }
    
    /**
     * Verifica si un usuario tiene un permiso específico para un módulo
     * 
     * @param int $userId ID del usuario
     * @param int $moduleId ID del módulo
     * @param string $permissionName Nombre del permiso
     * @return bool True si tiene permiso, false si no
     */
    public function userHasModulePermission($userId, $moduleId, $permissionName) {
        // Obtener los roles del usuario
        $query = "
            SELECT DISTINCT r.id
            FROM roles r
            JOIN user_roles ur ON r.id = ur.role_id
            WHERE ur.user_id = ?
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $rolesResult = $stmt->get_result();
        
        $roleIds = [];
        while ($role = $rolesResult->fetch_assoc()) {
            $roleIds[] = $role['id'];
        }
        
        if (empty($roleIds)) {
            return false;
        }
        
        // Convertir array a string para la consulta IN
        $roleIdsStr = implode(',', $roleIds);
        
        // Verificar si alguno de los roles tiene el permiso específico
        $query = "
            SELECT COUNT(*) as has_permission
            FROM role_module_permissions rmp
            JOIN permissions p ON rmp.permission_id = p.id
            WHERE rmp.module_id = ?
            AND rmp.role_id IN ({$roleIdsStr})
            AND p.name = ?
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('is', $moduleId, $permissionName);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['has_permission'] > 0;
    }
    
    /**
     * Asigna un permiso a un rol para un módulo específico
     * 
     * @param int $roleId ID del rol
     * @param int $moduleId ID del módulo
     * @param int $permissionId ID del permiso
     * @return bool True si se asignó correctamente, false si no
     */
    public function assignPermission($roleId, $moduleId, $permissionId) {
        // Verificar si ya existe la asignación
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM role_module_permissions
            WHERE role_id = ? AND module_id = ? AND permission_id = ?
        ");
        
        $stmt->bind_param('iii', $roleId, $moduleId, $permissionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            return true; // Ya existe, consideramos éxito
        }
        
        // Crear la asignación
        $stmt = $this->db->prepare("
            INSERT INTO role_module_permissions 
            (role_id, module_id, permission_id)
            VALUES (?, ?, ?)
        ");
        
        $stmt->bind_param('iii', $roleId, $moduleId, $permissionId);
        
        return $stmt->execute();
    }
    
    /**
     * Revoca un permiso de un rol para un módulo específico
     * 
     * @param int $roleId ID del rol
     * @param int $moduleId ID del módulo
     * @param int $permissionId ID del permiso
     * @return bool True si se revocó correctamente, false si no
     */
    public function revokePermission($roleId, $moduleId, $permissionId) {
        $stmt = $this->db->prepare("
            DELETE FROM role_module_permissions
            WHERE role_id = ? AND module_id = ? AND permission_id = ?
        ");
        
        $stmt->bind_param('iii', $roleId, $moduleId, $permissionId);
        
        return $stmt->execute();
    }
    
    /**
     * Obtiene los permisos asignados a un rol para un módulo
     * 
     * @param int $roleId ID del rol
     * @param int $moduleId ID del módulo
     * @return array Lista de IDs de permisos
     */
    public function getModuleRolePermissions($roleId, $moduleId) {
        $stmt = $this->db->prepare("
            SELECT permission_id
            FROM role_module_permissions
            WHERE role_id = ? AND module_id = ?
        ");
        
        $stmt->bind_param('ii', $roleId, $moduleId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row['permission_id'];
        }
        
        return $permissions;
    }
    
    /**
     * Actualiza el estado activo de un módulo
     * 
     * @param int $moduleId ID del módulo
     * @param bool $active Nuevo estado
     * @return bool True si se actualizó correctamente, false si no
     */
    public function updateActiveStatus($moduleId, $active) {
        $active = $active ? 1 : 0;
        
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET active = ?
            WHERE id = ?
        ");
        
        $stmt->bind_param('ii', $active, $moduleId);
        
        return $stmt->execute();
    }

    /**
    * Inserta un nuevo módulo en la base de datos
    * 
    * @param array $moduleData Datos del módulo a insertar
    * @return int|false ID del módulo insertado o false en caso de error
    */
    public function insert($moduleData) {
        // Campos permitidos para la tabla modules
        $allowedFields = [
            'name', 'slug', 'description', 'icon', 
            'directory_path', 'entry_file', 'active', 'order_index'
        ];
    
        // Filtrar solo los campos permitidos
        $filteredData = array_intersect_key($moduleData, array_flip($allowedFields));
    
        // Verificar campos obligatorios
        if (!isset($filteredData['name']) || empty($filteredData['name'])) {
            logMessage("Error al insertar módulo: Nombre es obligatorio", 'error');
            return false;
        }
    
        if (!isset($filteredData['slug']) || empty($filteredData['slug'])) {
            logMessage("Error al insertar módulo: Slug es obligatorio", 'error');
            return false;
        }
    
        if (!isset($filteredData['directory_path']) || empty($filteredData['directory_path'])) {
            logMessage("Error al insertar módulo: Ruta del directorio es obligatoria", 'error');
            return false;
        }
    
        // Asegurarse de que el valor active sea numérico (0 o 1)
        if (isset($filteredData['active'])) {
            $filteredData['active'] = $filteredData['active'] ? 1 : 0;
        } else {
            $filteredData['active'] = 1; // Por defecto activo
        }
    
        // Asegurarse de que order_index sea numérico
        if (isset($filteredData['order_index'])) {
            $filteredData['order_index'] = (int)$filteredData['order_index'];
        } else {
            $filteredData['order_index'] = 0; // Por defecto 0
        }
    
        // Asegurarse de que entry_file tenga un valor por defecto
        if (!isset($filteredData['entry_file']) || empty($filteredData['entry_file'])) {
            $filteredData['entry_file'] = 'index.html';
        }
    
        // Construir la consulta SQL
        $columns = implode(', ', array_keys($filteredData));
        $placeholders = implode(', ', array_fill(0, count($filteredData), '?'));
    
        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
    
        try {
            // Preparar la consulta
            $stmt = $this->db->prepare($query);
        
            if (!$stmt) {
                logMessage("Error al preparar la consulta de inserción de módulo: " . $this->db->error, 'error');
                return false;
            }
        
            // Construir el string de tipos para bind_param
            $types = '';
            foreach ($filteredData as $value) {
                if (is_int($value)) {
                    $types .= 'i'; // Integer
                } elseif (is_float($value)) {
                    $types .= 'd'; // Double
                } else {
                    $types .= 's'; // String
                }
            }
        
            // Valores para bind_param como array
            $bindValues = array_values($filteredData);
        
            // Referencia al array para bind_param
            $bindParams = [];
            $bindParams[] = $types;
        
            foreach ($bindValues as $key => $value) {
                $bindParams[] = &$bindValues[$key];
            }
        
            // Vincular parámetros
            call_user_func_array([$stmt, 'bind_param'], $bindParams);
        
            // Ejecutar la consulta
            if (!$stmt->execute()) {
                logMessage("Error al ejecutar la consulta de inserción de módulo: " . $stmt->error, 'error');
                return false;
            }
        
            // Obtener el ID insertado
            $moduleId = $this->db->insert_id;
        
            // Registrar en el log
            logMessage("Módulo insertado correctamente con ID: {$moduleId}", 'info');
        
            return $moduleId;
        } catch (Exception $e) {
            logMessage("Excepción al insertar módulo: " . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Obtiene estadísticas de uso de un módulo
     * 
     * @param int $moduleId ID del módulo
     * @param string $startDate Fecha de inicio (formato Y-m-d)
     * @param string $endDate Fecha de fin (formato Y-m-d)
     * @return array Estadísticas de uso
     */
    public function getModuleUsageStats($moduleId, $startDate = null, $endDate = null) {
        $params = [];
        $types = '';
        $whereClause = 'WHERE module_id = ?';
        
        $params[] = $moduleId;
        $types .= 'i';
        
        if ($startDate) {
            $whereClause .= ' AND access_timestamp >= ?';
            $params[] = $startDate . ' 00:00:00';
            $types .= 's';
        }
        
        if ($endDate) {
            $whereClause .= ' AND access_timestamp <= ?';
            $params[] = $endDate . ' 23:59:59';
            $types .= 's';
        }
        
        // Total de accesos
        $query = "
            SELECT COUNT(*) as total_accesses
            FROM module_access_logs
            {$whereClause}
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $totalResult = $stmt->get_result();
        $totalRow = $totalResult->fetch_assoc();
        
        // Usuarios únicos
        $query = "
            SELECT COUNT(DISTINCT user_id) as unique_users
            FROM module_access_logs
            {$whereClause}
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $usersResult = $stmt->get_result();
        $usersRow = $usersResult->fetch_assoc();
        
        // Accesos por día
        $query = "
            SELECT DATE(access_timestamp) as date, COUNT(*) as count
            FROM module_access_logs
            {$whereClause}
            GROUP BY DATE(access_timestamp)
            ORDER BY date ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $dailyResult = $stmt->get_result();
        
        $dailyAccess = [];
        while ($row = $dailyResult->fetch_assoc()) {
            $dailyAccess[$row['date']] = $row['count'];
        }
        
        return [
            'total_accesses' => $totalRow['total_accesses'],
            'unique_users' => $usersRow['unique_users'],
            'daily_access' => $dailyAccess
        ];
    }
}