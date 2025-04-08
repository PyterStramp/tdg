<?php
/**
 * Router - Gestiona el enrutamiento de las solicitudes
 */
class Router {
    private $routes = [];
    
    /**
     * Añade una ruta al router
     * 
     * @param string $route Ruta URL
     * @param array $controller Controlador y método a ejecutar
     */
    public function add($route, $controller) {
        $this->routes[$route] = $controller;
    }
    
    /**
     * Obtiene un controlador para una ruta dada
     * 
     * @param string $url URL a buscar
     * @return array|null Controlador y método o null si no se encuentra
     */
    public function match($url) {
        if (array_key_exists($url, $this->routes)) {
            return $this->routes[$url];
        }
        
        // Buscar rutas con parámetros
        foreach ($this->routes as $route => $controller) {
            $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Eliminar la coincidencia completa
                return ['controller' => $controller, 'params' => $matches];
            }
        }
        
        return null;
    }
}