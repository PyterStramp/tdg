<?php
/**
 * Model - Clase base para todos los modelos
 */
class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    /**
     * Constructor
     */
    public function __construct() {
        global $conn;
        $this->db = $conn;
    }
    
    /**
     * Obtiene todos los registros de la tabla
     * 
     * @param string $orderBy Campo para ordenar
     * @param string $order Dirección de ordenamiento (ASC, DESC)
     * @return array Registros encontrados
     */
    public function getAll($orderBy = null, $order = 'ASC') {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$order}";
        }
        
        $result = $this->db->query($sql);
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    /**
     * Obtiene un registro por su ID
     * 
     * @param int $id ID del registro
     * @return array|null Registro encontrado o null
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    /**
     * Busca registros según condiciones
     * 
     * @param array $conditions Condiciones de búsqueda (campo => valor)
     * @return array Registros encontrados
     */
    public function findBy($conditions) {
        $sql = "SELECT * FROM {$this->table} WHERE ";
        $params = [];
        $types = '';
        
        $i = 0;
        foreach ($conditions as $field => $value) {
            if ($i > 0) {
                $sql .= " AND ";
            }
            
            $sql .= "{$field} = ?";
            $params[] = $value;
            
            // Determinar el tipo de dato para bind_param
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_double($value)) {
                $types .= 'd';
            } elseif (is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            
            $i++;
        }
        
        $stmt = $this->db->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    /**
     * Inserta un nuevo registro
     * 
     * @param array $data Datos a insertar (campo => valor)
     * @return int|bool ID del registro insertado o false si falla
     */
    public function insert($data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($sql);
        
        $types = '';
        $values = [];
        
        foreach ($data as $value) {
            $values[] = $value;
            
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_double($value)) {
                $types .= 'd';
            } elseif (is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
        }
        
        $stmt->bind_param($types, ...$values);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        return false;
    }
    
    /**
     * Actualiza un registro
     * 
     * @param int $id ID del registro a actualizar
     * @param array $data Datos a actualizar (campo => valor)
     * @return bool True si se actualizó, false si no
     */
    public function update($id, $data) {
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        
        $stmt = $this->db->prepare($sql);
        
        $types = '';
        $values = [];
        
        foreach ($data as $value) {
            $values[] = $value;
            
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_double($value)) {
                $types .= 'd';
            } elseif (is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
        }
        
        // Añadir el ID al final
        $values[] = $id;
        $types .= 'i';
        
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }
    
    /**
     * Elimina un registro
     * 
     * @param int $id ID del registro a eliminar
     * @return bool True si se eliminó, false si no
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->bind_param('i', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Ejecuta una consulta SQL personalizada
     * 
     * @param string $sql Consulta SQL
     * @param array $params Parámetros para la consulta
     * @param string $types Tipos de datos de los parámetros
     * @return array|bool Resultados o false si falla
     */
    public function query($sql, $params = [], $types = '') {
        $stmt = $this->db->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result === false) {
            return false;
        }
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
}