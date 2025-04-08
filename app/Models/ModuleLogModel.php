<?php
/**
 * ModuleLogModel - Modelo para la gestión de registros de acceso a módulos
 */
class ModuleLogModel extends Model {
    protected $table = 'module_access_logs';
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Obtiene los registros de acceso con información de usuarios y módulos
     * 
     * @param array $filters Filtros a aplicar a la consulta
     * @param int $limit Límite de registros a retornar
     * @param int $offset Desde qué registro comenzar
     * @return array Registros de acceso
     */
    public function getAccessLogs($filters = [], $limit = 50, $offset = 0) {
        $query = "
            SELECT mal.*, 
                   u.username, u.full_name, 
                   m.name as module_name, m.slug as module_slug
            FROM {$this->table} mal
            JOIN users u ON mal.user_id = u.id
            JOIN modules m ON mal.module_id = m.id
            WHERE 1=1
        ";
        
        $params = [];
        $types = '';
        
        // Aplicar filtros
        if (!empty($filters['user_id'])) {
            $query .= " AND mal.user_id = ?";
            $params[] = $filters['user_id'];
            $types .= 'i';
        }
        
        if (!empty($filters['module_id'])) {
            $query .= " AND mal.module_id = ?";
            $params[] = $filters['module_id'];
            $types .= 'i';
        }
        
        if (!empty($filters['date_from'])) {
            $query .= " AND DATE(mal.access_timestamp) >= ?";
            $params[] = $filters['date_from'];
            $types .= 's';
        }
        
        if (!empty($filters['date_to'])) {
            $query .= " AND DATE(mal.access_timestamp) <= ?";
            $params[] = $filters['date_to'];
            $types .= 's';
        }
        
        // Ordenar por fecha, más reciente primero
        $query .= " ORDER BY mal.access_timestamp DESC";
        
        // Limitar resultados
        if ($limit > 0) {
            $query .= " LIMIT ?, ?";
            $params[] = $offset;
            $params[] = $limit;
            $types .= 'ii';
        }
        
        $stmt = $this->db->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }
        
        return $logs;
    }
    
    /**
     * Cuenta el total de registros según los filtros aplicados
     * 
     * @param array $filters Filtros a aplicar
     * @return int Total de registros
     */
    public function countAccessLogs($filters = []) {
        $query = "
            SELECT COUNT(*) as total
            FROM {$this->table} mal
            WHERE 1=1
        ";
        
        $params = [];
        $types = '';
        
        // Aplicar filtros
        if (!empty($filters['user_id'])) {
            $query .= " AND mal.user_id = ?";
            $params[] = $filters['user_id'];
            $types .= 'i';
        }
        
        if (!empty($filters['module_id'])) {
            $query .= " AND mal.module_id = ?";
            $params[] = $filters['module_id'];
            $types .= 'i';
        }
        
        if (!empty($filters['date_from'])) {
            $query .= " AND DATE(mal.access_timestamp) >= ?";
            $params[] = $filters['date_from'];
            $types .= 's';
        }
        
        if (!empty($filters['date_to'])) {
            $query .= " AND DATE(mal.access_timestamp) <= ?";
            $params[] = $filters['date_to'];
            $types .= 's';
        }
        
        $stmt = $this->db->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
    
    /**
     * Obtiene el resumen de accesos por módulo
     * 
     * @param array $filters Filtros a aplicar
     * @return array Datos de resumen
     */
    public function getModuleAccessSummary($filters = []) {
        $query = "
            SELECT m.id, m.name, COUNT(*) as access_count
            FROM {$this->table} mal
            JOIN modules m ON mal.module_id = m.id
            WHERE 1=1
        ";
        
        $params = [];
        $types = '';
        
        // Aplicar filtros
        if (!empty($filters['user_id'])) {
            $query .= " AND mal.user_id = ?";
            $params[] = $filters['user_id'];
            $types .= 'i';
        }
        
        if (!empty($filters['date_from'])) {
            $query .= " AND DATE(mal.access_timestamp) >= ?";
            $params[] = $filters['date_from'];
            $types .= 's';
        }
        
        if (!empty($filters['date_to'])) {
            $query .= " AND DATE(mal.access_timestamp) <= ?";
            $params[] = $filters['date_to'];
            $types .= 's';
        }
        
        $query .= " GROUP BY m.id ORDER BY access_count DESC";
        
        $stmt = $this->db->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $summary = [];
        while ($row = $result->fetch_assoc()) {
            $summary[] = $row;
        }
        
        return $summary;
    }
    
    /**
     * Obtiene el resumen de accesos por usuario
     * 
     * @param array $filters Filtros a aplicar
     * @return array Datos de resumen
     */
    public function getUserAccessSummary($filters = []) {
        $query = "
            SELECT u.id, u.username, u.full_name, COUNT(*) as access_count
            FROM {$this->table} mal
            JOIN users u ON mal.user_id = u.id
            WHERE 1=1
        ";
        
        $params = [];
        $types = '';
        
        // Aplicar filtros
        if (!empty($filters['module_id'])) {
            $query .= " AND mal.module_id = ?";
            $params[] = $filters['module_id'];
            $types .= 'i';
        }
        
        if (!empty($filters['date_from'])) {
            $query .= " AND DATE(mal.access_timestamp) >= ?";
            $params[] = $filters['date_from'];
            $types .= 's';
        }
        
        if (!empty($filters['date_to'])) {
            $query .= " AND DATE(mal.access_timestamp) <= ?";
            $params[] = $filters['date_to'];
            $types .= 's';
        }
        
        $query .= " GROUP BY u.id ORDER BY access_count DESC";
        
        $stmt = $this->db->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $summary = [];
        while ($row = $result->fetch_assoc()) {
            $summary[] = $row;
        }
        
        return $summary;
    }
}