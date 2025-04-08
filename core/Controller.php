<?php
/**
 * Controller - Clase base para todos los controladores
 */
class Controller {
    protected $view;
    protected $model;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->view = new View();
    }
    
    /**
     * Carga un modelo
     * 
     * @param string $model Nombre del modelo a cargar
     * @return object Instancia del modelo
     */
    protected function loadModel($model) {
        $modelFile = MODELS_PATH . '/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            
            if (class_exists($model)) {
                return new $model();
            }
        }
        
        throw new Exception("Modelo '$model' no encontrado");
    }
    
    /**
     * Redirige a una URL
     * 
     * @param string $url URL a redirigir
     */
    protected function redirect($url) {
        header('Location: ' . BASE_URL . '/' . $url);
        exit;
    }
    
    /**
     * Redirige con un mensaje flash
     * 
     * @param string $url URL a redirigir
     * @param string $message Mensaje a mostrar
     * @param string $type Tipo de mensaje (success, error, info, warning)
     */
    protected function redirectWithMessage($url, $message, $type = 'info') {
        $_SESSION['flash_message'] = [
            'text' => $message,
            'type' => $type
        ];
        
        $this->redirect($url);
    }
    
    /**
     * Verifica si la solicitud es POST
     * 
     * @return bool True si es POST, false si no
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Obtiene valores POST sanitizados
     * 
     * @param string $key Clave del valor POST a obtener
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor sanitizado
     */
    protected function getPost($key, $default = null) {
        return isset($_POST[$key]) ? $this->sanitizeInput($_POST[$key]) : $default;
    }
    
    /**
     * Sanitiza una entrada
     * 
     * @param mixed $input Entrada a sanitizar
     * @return mixed Entrada sanitizada
     */
    protected function sanitizeInput($input) {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = $this->sanitizeInput($value);
            }
            return $input;
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}