<?php
/**
 * ErrorController - Controlador para manejar errores
 */
class ErrorController extends Controller {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Muestra la página de error 404
     */
    public function notFound() {
        // Establecer código de estado HTTP 404
        header("HTTP/1.0 404 Not Found");
        
        $data = [
            'pageTitle' => 'Página no encontrada'
        ];
        
        // Renderizar la vista
        $this->view->render('error/404', $data);
    }
    
    /**
     * Muestra la página de error 403 (Acceso denegado)
     */
    public function forbidden() {
        // Establecer código de estado HTTP 403
        header("HTTP/1.0 403 Forbidden");
        
        $data = [
            'pageTitle' => 'Acceso denegado'
        ];
        
        // Renderizar la vista
        $this->view->render('error/403', $data);
    }
    
    /**
     * Muestra la página de error 500 (Error interno)
     * 
     * @param Exception $exception Excepción que causó el error
     */
    public function serverError($exception = null) {
        // Establecer código de estado HTTP 500
        header("HTTP/1.0 500 Internal Server Error");
        
        $data = [
            'pageTitle' => 'Error del servidor',
            'exception' => $exception
        ];
        
        // Registrar el error en el log
        if ($exception) {
            logMessage($exception->getMessage(), 'error');
        }
        
        // Renderizar la vista
        $this->view->render('error/500', $data);
    }
}