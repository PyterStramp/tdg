<?php
/**
 * ModuleController - Controlador para cargar y visualizar módulos
 */
class ModuleController extends Controller {
    private $moduleModel;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->moduleModel = $this->loadModel('ModuleModel');
    }
    
    /**
     * Método por defecto - Redirige y llama al método load
     */
    public function index($slug = '') {
        // Simplemente redirigir al método load
        return $this->load($slug);
    }
    
    /**
     * Carga y muestra un módulo específico
     * 
     * @param string $slug Slug del módulo a cargar
     */
    public function load($slug = '') {
        // Verificar que se proporcionó un slug
        if (empty($slug)) {
            $this->redirectWithMessage('home', 'Módulo no especificado', 'error');
            return;
        }
        
        // Obtener información del módulo
        $module = $this->moduleModel->getBySlug($slug);
        
        if (!$module) {
            $this->redirectWithMessage('home', 'Módulo no encontrado', 'error');
            return;
        }
        
        // Verificar que el módulo esté activo
        if (!$module['active']) {
            $this->redirectWithMessage('home', 'Este módulo está desactivado temporalmente', 'warning');
            return;
        }
        
        // Verificar permisos del usuario actual
        if (!$this->moduleModel->userHasModulePermission(getCurrentUserId(), $module['id'], 'view')) {
            $this->redirectWithMessage('home', 'No tienes permiso para acceder a este módulo', 'error');
            return;
        }
        
        // Comprobar si existe el directorio y archivo de entrada
        $modulePath = ROOT_PATH . '/app/modules/' . $module['directory_path'];
        $entryFile = $modulePath . '/' . $module['entry_file'];
        
        if (!is_dir($modulePath)) {
            $this->redirectWithMessage('home', 'Error: Directorio del módulo no encontrado', 'error');
            return;
        }
        
        if (!file_exists($entryFile)) {
            $this->redirectWithMessage('home', 'Error: Archivo de entrada del módulo no encontrado', 'error');
            return;
        }
        
        // Registrar el acceso
        $logSent = $this->moduleModel->logAccess(
            getCurrentUserId(),
            $module['id'],
            getClientIp(),
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            session_id()
        );
        
        logMessage('El usuario '.getCurrentUserId(). ' ingresó al módulo '. $module['name'], 'info');

        if (!$logSent) {
            $this->redirectWithMessage('home', 'Error: Falló un registro adecuado', 'error');
            return;
        }
        
        // Cargar el contenido del módulo según su tipo
        $extension = pathinfo($entryFile, PATHINFO_EXTENSION);
        $moduleContent = '';
    
        switch (strtolower($extension)) {
            case 'php':
                // Si es PHP, capturar la salida para incluirla en el contenedor
                ob_start();
                include $entryFile;
                $moduleContent = ob_get_clean();
                break;
            
            case 'html':
                // Si es HTML, leer y procesar el contenido
                $htmlContent = file_get_contents($entryFile);
            
                // Modificar rutas relativas para recursos del módulo
                //$moduleBaseUrl = baseUrl('app/modules/' . $module['directory_path'] . '/');
                $moduleContent = $this->processHtmlContent($htmlContent, $slug);
                break;
            
            default:
                $this->redirectWithMessage('home', 'Tipo de archivo de módulo no soportado', 'error');
                return;
        }
        
        // Preparar datos para la vista contenedor
        $data = [
            'pageTitle' => $module['name'],
            'module' => $module,
            'moduleContent' => $moduleContent
        ];
    
        // Renderizar la vista contenedor
        $this->view->render('module/container', $data);
    }

    /**
     * Procesa el contenido HTML para ajustar rutas relativas y aplicar sanitización
     * 
     * @param string $htmlContent Contenido HTML original
     * @param string $moduleSlug Slug del módulo
     * @return string Contenido HTML procesado
     */
    private function processHtmlContent($htmlContent, $moduleSlug) {
        // Transformar rutas relativas para usar el controlador assets
        $patterns = [
            // src="image.jpg" o href="style.css"
            '/(src|href)=(["\'])((?!https?:\/\/|\/\/|\/|data:|#)[^"\']+)(["\'])/i',

            // Mejorada: url(background.jpg) sin comillas o con comillas simples/dobles
            '/url\(\s*(["\']?)(?!https?:\/\/|\/\/|\/|data:|#)([^"\'()]+)(["\']?)\s*\)/i'
        ];
    
        $replacements = [
            '$1=$2' . baseUrl('assets/serve/' . $moduleSlug . '?path=$3') . '$4',
            'url(' . '$1' . baseUrl('assets/serve/' . $moduleSlug . '?path=$2') . '$3)'
        ];
    
        $processedContent = preg_replace($patterns, $replacements, $htmlContent);
    
        // Sanitizar el contenido (eliminando scripts maliciosos, etc.)
        $processedContent = $this->sanitizeHtml($processedContent);

        return $processedContent;   
    }

    /**
    * Sanitiza el contenido HTML para prevenir código malicioso
    * 
    * @param string $html Contenido HTML a sanitizar
    * @return string Contenido HTML sanitizado
    */
    private function sanitizeHtml($html) {
        // TODO: lógica de sanitización
        // Por ejemplo, eliminar scripts externos maliciosos, etc.
    
        $disallowedAttributes = [
            'onabort', 'onblur', 'onchange', 'onclick', 'ondblclick', 
            'onerror', 'onfocus', 'onkeydown', 'onkeypress', 'onkeyup',
            'onload', 'onmousedown', 'onmousemove', 'onmouseout', 
            'onmouseover', 'onmouseup', 'onreset', 'onresize', 
            'onscroll', 'onselect', 'onsubmit', 'onunload'
        ];
    
        foreach ($disallowedAttributes as $attr) {
            $html = preg_replace('/\s' . $attr . '\s*=\s*(["\'])(.*?)\1/i', '', $html);
        }
    
        return $html;
    }    
    
    /**
     * Maneja peticiones AJAX para obtener información del módulo
     * 
     * @param string $slug Slug del módulo
     * @param string $action Acción a realizar
     */
    public function api($slug = '', $action = '') {
        // Verificar que es una petición AJAX
        if (!$this->isAjaxRequest()) {
            echo json_encode(['success' => false, 'message' => 'Acceso no permitido']);
            return;
        }
        
        // Verificar que se proporcionó un slug
        if (empty($slug)) {
            echo json_encode(['success' => false, 'message' => 'Módulo no especificado']);
            return;
        }
        
        // Obtener información del módulo
        $module = $this->moduleModel->getBySlug($slug);
        
        if (!$module) {
            echo json_encode(['success' => false, 'message' => 'Módulo no encontrado']);
            return;
        }
        
        // Verificar que el módulo esté activo
        if (!$module['active']) {
            echo json_encode(['success' => false, 'message' => 'Este módulo está desactivado temporalmente']);
            return;
        }
        
        // Verificar permisos del usuario actual
        if (!$this->moduleModel->userHasModulePermission(getCurrentUserId(), $module['id'], 'view')) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para acceder a este módulo']);
            return;
        }
        
        // Realizar la acción solicitada
        switch ($action) {
            case 'info':
                // Devolver información básica del módulo
                echo json_encode([
                    'success' => true,
                    'module' => [
                        'id' => $module['id'],
                        'name' => $module['name'],
                        'description' => $module['description'],
                        'canEdit' => $this->moduleModel->userHasModulePermission(getCurrentUserId(), $module['id'], 'edit'),
                        'canAdmin' => $this->moduleModel->userHasModulePermission(getCurrentUserId(), $module['id'], 'admin')
                    ]
                ]);
                break;
                
            case 'files':
                // Verificar permiso de edición
                if (!$this->moduleModel->userHasModulePermission(getCurrentUserId(), $module['id'], 'edit')) {
                    echo json_encode(['success' => false, 'message' => 'No tienes permiso para ver los archivos de este módulo']);
                    return;
                }
                
                // Obtener lista de archivos del módulo
                $modulePath = ROOT_PATH . '/app/modules/' . $module['directory_path'];
                $files = $this->getModuleFiles($modulePath);
                
                echo json_encode([
                    'success' => true,
                    'files' => $files
                ]);
                break;
                
            case 'stats':
                // Verificar permiso de administración
                if (!$this->moduleModel->userHasModulePermission(getCurrentUserId(), $module['id'], 'admin')) {
                    echo json_encode(['success' => false, 'message' => 'No tienes permiso para ver las estadísticas de este módulo']);
                    return;
                }
                
                // Obtener estadísticas básicas
                $startDate = $_GET['startDate'] ?? date('Y-m-d', strtotime('-30 days'));
                $endDate = $_GET['endDate'] ?? date('Y-m-d');
                
                $stats = $this->moduleModel->getModuleUsageStats($module['id'], $startDate, $endDate);
                
                echo json_encode([
                    'success' => true,
                    'stats' => $stats
                ]);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
        }
    }
    
    /**
     * Verifica si la petición actual es AJAX
     * 
     * @return bool True si es AJAX, false si no
     */
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    /**
     * Obtiene una lista de archivos de un directorio
     * 
     * @param string $dir Directorio a escanear
     * @param string $prefix Prefijo para mantener la jerarquía en la recursión
     * @return array Lista de archivos
     */
    private function getModuleFiles($dir, $prefix = '') {
        $files = [];
        
        if (!is_dir($dir)) {
            return $files;
        }
        
        $items = scandir($dir);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $path = $dir . '/' . $item;
            $relativePath = $prefix . $item;
            
            if (is_dir($path)) {
                // Es un directorio, obtener archivos de forma recursiva
                $subFiles = $this->getModuleFiles($path, $relativePath . '/');
                $files = array_merge($files, $subFiles);
            } else {
                // Es un archivo
                $files[] = [
                    'name' => $item,
                    'path' => $relativePath,
                    'size' => filesize($path),
                    'modified' => date('Y-m-d H:i:s', filemtime($path)),
                    'type' => pathinfo($item, PATHINFO_EXTENSION)
                ];
            }
        }
        
        return $files;
    }
    
    /**
     * Sanitiza el contenido HTML de un módulo
     * 
     * @param string $content Contenido HTML original
     * @param array $module Información del módulo
     * @return string Contenido sanitizado
     */
    private function sanitizeHtmlContent($content, $module) {
        // Reemplazar rutas relativas
        $moduleBasePath = baseUrl('app/modules/' . $module['directory_path'] . '/');
        
        // Reemplazar atributos src y href relativos
        $content = preg_replace('/(src|href)=(["\'])((?!https?:\/\/|\/\/|\/|data:|#)[^"\']+)(["\'])/i', '$1=$2' . $moduleBasePath . '$3$4', $content);
        
        // Eliminar scripts externos potencialmente peligrosos
        $content = preg_replace('/<script[^>]*src=(["\'])(https?:)?\/\/(?!.*trusted-domains\.com)[^>]*>.*?<\/script>/is', '', $content);
        
        // Eliminar enlaces a hojas de estilo externas potencialmente peligrosas
        $content = preg_replace('/<link[^>]*href=(["\'])(https?:)?\/\/(?!.*trusted-domains\.com)[^>]*>/is', '', $content);
        
        // Añadir atributo sandbox a iframes
        $content = preg_replace('/<iframe/i', '<iframe sandbox="allow-scripts"', $content);
        
        // Añadir encabezado para indicar que el contenido ha sido sanitizado
        $content = "<!-- Contenido sanitizado por el sistema -->\n" . $content;
        
        return $content;
    }
}