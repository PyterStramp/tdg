<?php
/**
 * HomeController - Controlador para la página de inicio
 */
class HomeController extends Controller {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Muestra la página de inicio (dashboard)
     */
    public function index() {
        // Verificar si el usuario está autenticado
        requireLogin();

        // Obtener el ID del usuario actual
        $userId = getCurrentUserId();
    
        // Cargar módulos accesibles directamente
        $moduleModel = $this->loadModel('ModuleModel');

        // Obtener módulos según roles
        $modules = $moduleModel->getAccessibleModules($userId);
        
        //asegurar que al menos cargue cuando se vaya a Home
        $_SESSION['accessible_modules'] = $modules;
        
        // Cargar datos para la vista
        $data = [
            'pageTitle' => 'Dashboard',
            'modules' => $modules
        ];
        
        // Renderizar la vista
        $this->view->render('home/index', $data);
    }
}