<?php
/**
 * UserModel - Modelo para gestión de usuarios
 */
class UserModel extends Model {
    protected $table = 'users';
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Busca un usuario por nombre de usuario o correo electrónico
     * 
     * @param string $usernameOrEmail Nombre de usuario o correo
     * @return array|null Usuario encontrado o null
     */
    public function findByUsernameOrEmail($usernameOrEmail) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE username = ? OR email = ?
        ");
        
        $stmt->bind_param('ss', $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    /**
     * Actualiza la fecha del último inicio de sesión
     * 
     * @param int $userId ID del usuario
     * @return bool True si se actualizó, false si no
     */
    public function updateLastLogin($userId) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET last_login = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        
        $stmt->bind_param('i', $userId);
        
        return $stmt->execute();
    }
    
    /**
     * Obtiene los roles de un usuario
     * 
     * @param int $userId ID del usuario
     * @return array Roles del usuario
     */
    public function getUserRoles($userId) {
        $stmt = $this->db->prepare("
            SELECT r.id, r.name, r.description
            FROM roles r
            JOIN user_roles ur ON r.id = ur.role_id
            WHERE ur.user_id = ?
        ");
        
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        $roles = [];
        while ($role = $result->fetch_assoc()) {
            $roles[] = $role;
        }
        
        return $roles;
    }
    
    /**
     * Obtiene solo los IDs de los roles de un usuario
     * 
     * @param int $userId ID del usuario
     * @return array IDs de los roles
     */
    public function getUserRoleIds($userId) {
        $stmt = $this->db->prepare("
            SELECT role_id 
            FROM user_roles 
            WHERE user_id = ?
        ");
        
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        $roleIds = [];
        while ($role = $result->fetch_assoc()) {
            $roleIds[] = $role['role_id'];
        }
        
        return $roleIds;
    }
    
    /**
     * Obtiene los módulos accesibles para un conjunto de roles
     * 
     * @param array $roleIds IDs de los roles
     * @return array Módulos accesibles
     */
    /*
    public function getAccessibleModules($roleIds) {
        if (empty($roleIds)) {
            return [];
        }
        
        $roleIdsString = implode(',', $roleIds);
        
        // Obtener los módulos a los que el usuario tiene acceso a través de sus roles
        $query = "
            SELECT DISTINCT m.id, m.name, m.slug, m.icon, m.order_index,
            (
                SELECT GROUP_CONCAT(p.name)
                FROM role_module_permissions rmp
                JOIN permissions p ON rmp.permission_id = p.id
                WHERE rmp.module_id = m.id AND rmp.role_id IN ({$roleIdsString})
            ) as permissions
            FROM modules m
            JOIN role_module_permissions rmp ON m.id = rmp.module_id
            WHERE rmp.role_id IN ({$roleIdsString})
            AND m.active = TRUE
            AND EXISTS (
                SELECT 1 FROM role_module_permissions rmp2
                JOIN permissions p ON rmp2.permission_id = p.id
                WHERE rmp2.module_id = m.id 
                AND rmp2.role_id IN ({$roleIdsString})
                AND p.name = 'view'
            )
            ORDER BY m.order_index ASC, m.name ASC
        ";
        
        $result = $this->db->query($query);
        
        $modules = [];
        while ($module = $result->fetch_assoc()) {
            // Convertir la cadena de permisos a un array
            $permissionsString = $module['permissions'];
            $permissionsArray = $permissionsString ? explode(',', $permissionsString) : [];
            
            $modules[] = [
                'id' => $module['id'],
                'name' => $module['name'],
                'slug' => $module['slug'],
                'icon' => $module['icon'],
                'permissions' => array_unique($permissionsArray)
            ];
        }
        
        return $modules;
    }*/
    
    /**
     * Registra un acceso del usuario en el log
     * 
     * @param int $userId ID del usuario
     * @param string $actionType Tipo de acción (login, module_access, etc.)
     * @param int $moduleId ID del módulo (opcional)
     * @return bool True si se registró, false si no
     */
    public function logAccess($userId, $actionType, $moduleId = null) {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $sessionId = session_id();
        
        if ($actionType === 'login') {
            // Si es un login, no se necesita moduleId
            $moduleId = null;
        }
        
        if ($actionType === 'module_access' && $moduleId === null) {
            // Si es acceso a módulo, se necesita moduleId
            return false;
        }
        
        if ($moduleId) {
            // Log de acceso a módulo
            $stmt = $this->db->prepare("
                INSERT INTO module_access_logs 
                (user_id, module_id, ip_address, user_agent, session_id)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param('iisss', $userId, $moduleId, $ipAddress, $userAgent, $sessionId);
        } else {
            logMessage('El usuario '. $userId. ' ingresó al módulo '.$moduleId. ' con dirección IP '. $ipAddress. ' con userAgent de '. $userAgent. ' con el session_id de '. $sessionId, 'info');
            return true;
        }
        
        return $stmt->execute();
    }
    
    /**
     * Verifica si un nombre de usuario ya existe
     * 
     * @param string $username Nombre de usuario a verificar
     * @return bool True si existe, false si no
     */
    public function usernameExists($username) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM {$this->table}
            WHERE username = ?
        ");
        
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    /**
     * Verifica si un email ya existe
     * 
     * @param string $email Email a verificar
     * @return bool True si existe, false si no
     */
    public function emailExists($email) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM {$this->table}
            WHERE email = ?
        ");
        
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    /**
     * Crea un nuevo usuario
     * 
     * @param array $userData Datos del usuario
     * @return int|bool ID del usuario creado o false
     */
    public function createUser($userData) {
        // Verificar si el nombre de usuario o email ya existen
        if ($this->usernameExists($userData['username']) || $this->emailExists($userData['email'])) {
            return false;
        }
        
        // Generar código único para el usuario
        $userData['code'] = $this->generateUniqueCode();
        
        return $this->insert($userData);
    }
    
    /**
     * Genera un código único para el usuario
     * 
     * @return string Código único
     */
    private function generateUniqueCode() {
        $code = bin2hex(random_bytes(8)); // Genera un código hexadecimal de 16 caracteres
        
        // Verificar que el código no exista ya
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM {$this->table}
            WHERE code = ?
        ");
        
        $stmt->bind_param('s', $code);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        // Si el código ya existe, generar otro
        if ($row['count'] > 0) {
            return $this->generateUniqueCode();
        }
        
        return $code;
    }
    
    /**
     * Asigna roles a un usuario
     * 
     * @param int $userId ID del usuario
     * @param array $roleIds IDs de los roles a asignar
     * @return bool True si se asignaron todos, false si no
     */
    public function assignRoles($userId, $roleIds) {
        if (empty($roleIds)) {
            return true;
        }
        
        // Eliminar roles actuales
        $stmt = $this->db->prepare("
            DELETE FROM user_roles
            WHERE user_id = ?
        ");
        
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        
        // Asignar nuevos roles
        $success = true;
        foreach ($roleIds as $roleId) {
            $stmt = $this->db->prepare("
                INSERT INTO user_roles (user_id, role_id)
                VALUES (?, ?)
            ");
            
            $stmt->bind_param('ii', $userId, $roleId);
            if (!$stmt->execute()) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Obtiene todos los usuarios con sus roles
     * 
     * @return array Lista de usuarios con sus roles
     */
    public function getAllWithRoles() {
        $users = $this->getAll();
        
        foreach ($users as &$user) {
            $user['roles'] = $this->getUserRoles($user['id']);
        }
        
        return $users;
    }
    
    /**
     * Crea una tabla temporal de tokens para recuperación de contraseña si no existe
     * 
     * @return bool True si se creó o ya existía, false si hubo error
     */
    private function ensurePasswordResetTableExists() {
        $query = "
            CREATE TABLE IF NOT EXISTS password_reset_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token VARCHAR(64) NOT NULL UNIQUE,
                expires DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ";
        
        return $this->db->query($query);
    }
    
    /**
     * Crea un token para restablecer la contraseña
     * 
     * @param int $userId ID del usuario
     * @param string $token Token de restablecimiento
     * @param string $expires Fecha de expiración
     * @return bool True si se creó, false si no
     */
    public function createPasswordResetToken($userId, $token, $expires) {
        // Asegurar que la tabla existe
        if (!$this->ensurePasswordResetTableExists()) {
            return false;
        }
        
        // Eliminar tokens anteriores para este usuario
        $this->deletePasswordResetTokens($userId);
        
        // Crear nuevo token
        $stmt = $this->db->prepare("
            INSERT INTO password_reset_tokens (user_id, token, expires)
            VALUES (?, ?, ?)
        ");
        
        $stmt->bind_param('iss', $userId, $token, $expires);
        
        return $stmt->execute();
    }
    
    /**
     * Obtiene información de un token de restablecimiento
     * 
     * @param string $token Token a verificar
     * @return array|null Información del token o null
     */
    public function getPasswordResetToken($token) {
        // Asegurar que la tabla existe
        if (!$this->ensurePasswordResetTableExists()) {
            return null;
        }
        
        $stmt = $this->db->prepare("
            SELECT * FROM password_reset_tokens
            WHERE token = ?
        ");
        
        $stmt->bind_param('s', $token);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    /**
     * Elimina todos los tokens de restablecimiento de un usuario
     * 
     * @param int $userId ID del usuario
     * @return bool True si se eliminaron, false si no
     */
    public function deletePasswordResetTokens($userId) {
        // Asegurar que la tabla existe
        if (!$this->ensurePasswordResetTableExists()) {
            return false;
        }
        
        $stmt = $this->db->prepare("
            DELETE FROM password_reset_tokens
            WHERE user_id = ?
        ");
        
        $stmt->bind_param('i', $userId);
        
        return $stmt->execute();
    }
    
    /**
     * Elimina los tokens de restablecimiento expirados
     * 
     * @return bool True si se eliminaron, false si no
     */
    public function cleanupExpiredResetTokens() {
        // Asegurar que la tabla existe
        if (!$this->ensurePasswordResetTableExists()) {
            return false;
        }
        
        $now = date('Y-m-d H:i:s');
        
        $stmt = $this->db->prepare("
            DELETE FROM password_reset_tokens
            WHERE expires < ?
        ");
        
        $stmt->bind_param('s', $now);
        
        return $stmt->execute();
    }
}