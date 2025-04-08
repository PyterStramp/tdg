<?php
/**
 * RoleModel - Modelo para gestión de roles
 */
class RoleModel extends Model {
    protected $table = 'roles';
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Obtiene todos los roles con el conteo de usuarios
     * 
     * @return array Roles con conteo de usuarios
     */
    public function getAllWithUserCount() {
        $query = "
            SELECT r.*, COUNT(ur.user_id) as user_count
            FROM {$this->table} r
            LEFT JOIN user_roles ur ON r.id = ur.role_id
            GROUP BY r.id
            ORDER BY r.name ASC
        ";
        
        $result = $this->db->query($query);
        
        $roles = [];
        while ($role = $result->fetch_assoc()) {
            $roles[] = $role;
        }
        
        return $roles;
    }
    
    /**
     * Obtiene un rol por su nombre
     * 
     * @param string $name Nombre del rol
     * @return array|null Rol encontrado o null
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
     * Verifica si un nombre de rol ya existe
     * 
     * @param string $name Nombre del rol a verificar
     * @return bool True si existe, false si no
     */
    public function nameExists($name) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM {$this->table}
            WHERE name = ?
        ");
        
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    /**
     * Obtiene los usuarios que tienen un rol específico
     * 
     * @param int $roleId ID del rol
     * @return array Usuarios con el rol
     */
    public function getUsersByRoleId($roleId) {
        $query = "
            SELECT u.*
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            WHERE ur.role_id = ?
            ORDER BY u.username ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $roleId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        $users = [];
        while ($user = $result->fetch_assoc()) {
            $users[] = $user;
        }
        
        return $users;
    }
}