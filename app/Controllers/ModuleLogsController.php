<?php
/**
 * ModuleLogsController - Controlador para la visualización de registros de acceso a módulos
 */
class ModuleLogsController extends Controller {
    private $logModel;
    private $moduleModel;
    private $userModel;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->logModel = $this->loadModel('ModuleLogModel');
        $this->moduleModel = $this->loadModel('ModuleModel');
        $this->userModel = $this->loadModel('UserModel');
        
        // Verificar permisos de administrador
        requireRole('admin');
    }
    
    /**
     * Muestra el listado de accesos con filtros
     */
    public function index() {
        // Obtener parámetros de filtro
        $filters = [];
        
        if ($this->isPost()) {
            $filters['user_id'] = $this->getPost('user_id');
            $filters['module_id'] = $this->getPost('module_id');
            $filters['date_from'] = $this->getPost('date_from');
            $filters['date_to'] = $this->getPost('date_to');
        } else {
            // Valores predeterminados: último mes
            $filters['date_from'] = date('Y-m-d', strtotime('-30 days'));
            $filters['date_to'] = date('Y-m-d');
        }
        
        // Configuración de paginación
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        
        // Obtener registros de acceso
        $logs = $this->logModel->getAccessLogs($filters, $perPage, $offset);
        $totalLogs = $this->logModel->countAccessLogs($filters);
        
        // Calcular paginación
        $totalPages = ceil($totalLogs / $perPage);
        
        // Obtener listas para filtros
        $modules = $this->moduleModel->getAll();
        $users = $this->userModel->getAll();
        
        // Obtener resúmenes
        $moduleSummary = $this->logModel->getModuleAccessSummary($filters);
        $userSummary = $this->logModel->getUserAccessSummary($filters);
        
        $data = [
            'pageTitle' => 'Registros de Acceso a Módulos',
            'logs' => $logs,
            'filters' => $filters,
            'modules' => $modules,
            'users' => $users,
            'moduleSummary' => $moduleSummary,
            'userSummary' => $userSummary,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalLogs,
                'totalPages' => $totalPages
            ]
        ];
        
        $this->view->render('module_logs/index', $data);
    }
    
    /**
     * Muestra estadísticas detalladas de acceso
     */
    public function stats() {
        // Obtener parámetros de filtro
        $filters = [];
        
        if ($this->isPost()) {
            $filters['user_id'] = $this->getPost('user_id');
            $filters['module_id'] = $this->getPost('module_id');
            $filters['date_from'] = $this->getPost('date_from');
            $filters['date_to'] = $this->getPost('date_to');
        } else {
            // Valores predeterminados: último mes
            $filters['date_from'] = date('Y-m-d', strtotime('-30 days'));
            $filters['date_to'] = date('Y-m-d');
        }
        
        // Obtener listas para filtros
        $modules = $this->moduleModel->getAll();
        $users = $this->userModel->getAll();
        
        // Obtener resúmenes
        $moduleSummary = $this->logModel->getModuleAccessSummary($filters);
        $userSummary = $this->logModel->getUserAccessSummary($filters);
        
        $data = [
            'pageTitle' => 'Estadísticas de Acceso a Módulos',
            'filters' => $filters,
            'modules' => $modules,
            'users' => $users,
            'moduleSummary' => $moduleSummary,
            'userSummary' => $userSummary
        ];
        
        $this->view->render('module_logs/stats', $data);
    }
    
    /**
     * Muestra el detalle de accesos para un módulo específico
     * 
     * @param int $id ID del módulo
     */
    public function module($id = null) {
        // Validar que se proporcionó un ID
        if (!$id) {
            $this->redirectWithMessage('module_logs', 'Módulo no especificado', 'error');
            return;
        }
        
        // Obtener datos del módulo
        $module = $this->moduleModel->getById($id);
        
        if (!$module) {
            $this->redirectWithMessage('module_logs', 'Módulo no encontrado', 'error');
            return;
        }
        
        // Obtener parámetros de filtro
        $filters = [
            'module_id' => $id
        ];
        
        if ($this->isPost()) {
            $filters['user_id'] = $this->getPost('user_id');
            $filters['date_from'] = $this->getPost('date_from');
            $filters['date_to'] = $this->getPost('date_to');
        } else {
            // Valores predeterminados: último mes
            $filters['date_from'] = date('Y-m-d', strtotime('-30 days'));
            $filters['date_to'] = date('Y-m-d');
        }
        
        // Configuración de paginación
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        
        // Obtener registros de acceso
        $logs = $this->logModel->getAccessLogs($filters, $perPage, $offset);
        $totalLogs = $this->logModel->countAccessLogs($filters);
        
        // Calcular paginación
        $totalPages = ceil($totalLogs / $perPage);
        
        // Obtener usuarios para el filtro
        $users = $this->userModel->getAll();
        
        // Obtener resumen de usuarios
        $userSummary = $this->logModel->getUserAccessSummary($filters);
        
        $data = [
            'pageTitle' => 'Accesos al Módulo: ' . $module['name'],
            'module' => $module,
            'logs' => $logs,
            'filters' => $filters,
            'users' => $users,
            'userSummary' => $userSummary,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalLogs,
                'totalPages' => $totalPages
            ]
        ];
        
        $this->view->render('module_logs/module', $data);
    }
    
    /**
     * Muestra el detalle de accesos para un usuario específico
     * 
     * @param int $id ID del usuario
     */
    public function user($id = null) {
        // Validar que se proporcionó un ID
        if (!$id) {
            $this->redirectWithMessage('module_logs', 'Usuario no especificado', 'error');
            return;
        }
        
        // Obtener datos del usuario
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            $this->redirectWithMessage('module_logs', 'Usuario no encontrado', 'error');
            return;
        }
        
        // Obtener parámetros de filtro
        $filters = [
            'user_id' => $id
        ];
        
        if ($this->isPost()) {
            $filters['module_id'] = $this->getPost('module_id');
            $filters['date_from'] = $this->getPost('date_from');
            $filters['date_to'] = $this->getPost('date_to');
        } else {
            // Valores predeterminados: último mes
            $filters['date_from'] = date('Y-m-d', strtotime('-30 days'));
            $filters['date_to'] = date('Y-m-d');
        }
        
        // Configuración de paginación
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        
        // Obtener registros de acceso
        $logs = $this->logModel->getAccessLogs($filters, $perPage, $offset);
        $totalLogs = $this->logModel->countAccessLogs($filters);
        
        // Calcular paginación
        $totalPages = ceil($totalLogs / $perPage);
        
        // Obtener módulos para el filtro
        $modules = $this->moduleModel->getAll();
        
        // Obtener resumen de módulos
        $moduleSummary = $this->logModel->getModuleAccessSummary($filters);
        
        $data = [
            'pageTitle' => 'Accesos del Usuario: ' . $user['username'],
            'user' => $user,
            'logs' => $logs,
            'filters' => $filters,
            'modules' => $modules,
            'moduleSummary' => $moduleSummary,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalLogs,
                'totalPages' => $totalPages
            ]
        ];
        
        $this->view->render('module_logs/user', $data);
    }
}