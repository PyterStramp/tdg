<?php
/**
 * PermissionModel - Modelo para la gestión de permisos
 */
class PermissionModel extends Model {
    protected $table = 'permissions';
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Obtiene un permiso por su nombre
     * 
     * @param string $name Nombre del permiso
     * @return array|null Permiso encontrado o null
     */
    public function getByName($name) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE name = ?
        ");
        
        $stmt->bind_param('s', $name);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    /**
     * Verifica si un nombre de permiso ya existe
     * 
     * @param string $name Nombre a verificar
     * @param int $excludeId ID de permiso a excluir de la verificación (opcional)
     * @return bool True si existe, false si no
     */
    public function nameExists($name, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM {$this->table}
                WHERE name = ? AND id != ?
            ");
            
            $stmt->bind_param('si', $name, $excludeId);
        } else {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM {$this->table}
                WHERE name = ?
            ");
            
            $stmt->bind_param('s', $name);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    /**
     * Obtiene los permisos asignados a un módulo
     * 
     * @param int $moduleId ID del módulo
     * @return array Lista de permisos asignados
     */
    public function getModulePermissions($moduleId) {
        $query = "
            SELECT DISTINCT p.*
            FROM {$this->table} p
            JOIN role_module_permissions rmp ON p.id = rmp.permission_id
            WHERE rmp.module_id = ?
            ORDER BY p.name ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $moduleId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row;
        }
        
        return $permissions;
    }
    
    /**
     * Obtiene los permisos disponibles para un rol específico
     * 
     * @param int $roleId ID del rol
     * @return array Lista de permisos disponibles
     */
    public function getRolePermissions($roleId) {
        $query = "
            SELECT DISTINCT p.id, p.name, p.description,
                   m.id as module_id, m.name as module_name
            FROM {$this->table} p
            JOIN role_module_permissions rmp ON p.id = rmp.permission_id
            JOIN modules m ON rmp.module_id = m.id
            WHERE rmp.role_id = ?
            ORDER BY m.name ASC, p.name ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $roleId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row;
        }
        
        return $permissions;
    }
    
    /**
     * Obtiene los módulos y sus permisos disponibles para un rol
     * 
     * @param int $roleId ID del rol
     * @return array Módulos con sus permisos
     */
    public function getModulesWithPermissionsByRole($roleId) {
        // Primero, obtener todos los módulos
        $query = "
            SELECT m.id, m.name, m.slug, m.description, m.active
            FROM modules m
            ORDER BY m.name ASC
        ";
        
        $result = $this->db->query($query);
        
        $modulesWithPermissions = [];
        while ($module = $result->fetch_assoc()) {
            // Para cada módulo, obtener todos los permisos existentes
            $query = "
                SELECT p.id, p.name, p.description,
                       CASE WHEN rmp.role_id IS NOT NULL THEN 1 ELSE 0 END as has_permission
                FROM permissions p
                LEFT JOIN role_module_permissions rmp ON 
                    p.id = rmp.permission_id AND
                    rmp.module_id = ? AND
                    rmp.role_id = ?
                ORDER BY p.name ASC
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ii', $module['id'], $roleId);
            $stmt->execute();
            $permissionsResult = $stmt->get_result();
            
            $permissions = [];
            while ($permission = $permissionsResult->fetch_assoc()) {
                $permissions[] = $permission;
            }
            
            $module['permissions'] = $permissions;
            $modulesWithPermissions[] = $module;
        }
        
        return $modulesWithPermissions;
    }
}