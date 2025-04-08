<?php
/**
 * View - Clase para renderizar vistas
 */
class View {
    private $data = [];
    
    /**
     * Asigna datos para usar en la vista
     * 
     * @param string $key Nombre de la variable
     * @param mixed $value Valor a asignar
     */
    public function set($key, $value) {
        $this->data[$key] = $value;
    }
    
    /**
     * Asigna múltiples datos a la vez
     * 
     * @param array $data Datos a asignar (clave => valor)
     */
    public function setMultiple($data) {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
    }
    
    /**
     * Renderiza una vista
     * 
     * @param string $view Ruta de la vista a renderizar
     * @param array $data Datos adicionales para la vista
     * @param bool $includeTemplate Si se debe incluir la plantilla completa
     */
    public function render($view, $data = [], $includeTemplate = true) {
        // Combinar datos existentes con los nuevos
        $this->setMultiple($data);
        
        // Extraer variables para que estén disponibles en la vista
        extract($this->data);
        
        // Ruta completa de la vista
        $viewFile = VIEWS_PATH . '/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            throw new Exception("Vista '{$view}' no encontrada");
        }
        
        // Iniciar buffer de salida
        ob_start();
        
        // Incluir la vista
        include $viewFile;
        
        // Obtener contenido del buffer
        $content = ob_get_clean();
        
        // Si se debe incluir la plantilla completa
        if ($includeTemplate) {
            // Incluir header
            include VIEWS_PATH . '/templates/header.php';
            
            // Mostrar contenido
            echo $content;
            
            // Incluir footer
            include VIEWS_PATH . '/templates/footer.php';
        } else {
            // Mostrar solo el contenido sin plantilla
            echo $content;
        }
    }
    
    /**
     * Renderiza un fragmento de vista (sin plantilla completa)
     * 
     * @param string $view Ruta de la vista a renderizar
     * @param array $data Datos para la vista
     */
    public function renderPartial($view, $data = []) {
        $this->render($view, $data, false);
    }
    
    /**
     * Renderiza una vista como JSON
     * 
     * @param array $data Datos a convertir a JSON
     */
    public function renderJson($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}