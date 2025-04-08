<?php
/**
 * AssetsController - Controlador para servir archivos estáticos de módulos
 */
class AssetsController extends Controller {
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Método por defecto - Redirige al inicio
     */
    public function index() {
        $this->redirect('home');
    }

    /**
    * Procesa archivos CSS para corregir rutas relativas
    * 
    * @param string $cssContent Contenido CSS original
    * @param string $moduleSlug Slug del módulo
    * @return string Contenido CSS procesado
    */
    private function processCssContent($cssContent, $moduleSlug) {
        // Transformar url(archivo.png) a url(http://localhost/test1/public/assets/serve/modulo?path=archivo.png)
        $pattern = '/url\(\s*[\'"]?((?!https?:\/\/|\/\/|\/|data:|#)[^\'")]+)[\'"]?\s*\)/i';
        $replacement = 'url(' . baseUrl('assets/serve/' . $moduleSlug . '?path=$1') . ')';
    
        return preg_replace($pattern, $replacement, $cssContent);
    }
    
    /**
     * Sirve un archivo estático desde los módulos
     * 
     * @param string $moduleSlug Slug del módulo
     * @param string $filePath Ruta del archivo dentro del módulo
     */
    public function serve($moduleSlug = '', $filePath = '') {
        // Verificar que los parámetros no estén vacíos
        if (empty($moduleSlug)) {
            header('HTTP/1.0 404 Not Found');
            echo 'Módulo no especificado';
            exit;
        }
        
        // La ruta del archivo puede estar en URL, extraerla
        if (empty($filePath) && isset($_GET['path'])) {
            $filePath = $_GET['path'];
        }
        
        if (empty($filePath)) {
            header('HTTP/1.0 404 Not Found');
            echo 'Archivo no especificado';
            exit;
        }
        
        // Construir la ruta completa al archivo
        // Limpiamos la ruta para evitar ataques de traversal
        $filePath = str_replace('..', '', $filePath);
        $fullPath = ROOT_PATH . '/app/modules/module-' . $moduleSlug . '/' . $filePath;
        
        // Registrar para debug
        logMessage("Intentando servir archivo: " . $fullPath, 'info');
        
        // Verificar que el archivo existe
        if (!file_exists($fullPath)) {
            header('HTTP/1.0 404 Not Found');
            echo 'Archivo no encontrado: ' . $fullPath;
            exit;
        }
        
        // Verificar que es un archivo regular
        if (!is_file($fullPath)) {
            header('HTTP/1.0 403 Forbidden');
            echo 'No es un archivo válido';
            exit;
        }
        
        // Determinar el tipo MIME
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'ttf' => 'font/ttf',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2'
        ];
        
        
        $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';
        // Antes de enviar el archivo, procesa el CSS si es necesario
        if ($extension === 'css') {
            $content = file_get_contents($fullPath);
            $processedContent = $this->processCssContent($content, $moduleSlug);

            // Enviar encabezados
            header('Content-Type: ' . $contentType);
            header('Content-Length: ' . strlen($processedContent));

            // Enviar el contenido procesado
            echo $processedContent;
            exit;
        }        
        // Enviar encabezados
        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . filesize($fullPath));
        
        // Enviar el archivo
        readfile($fullPath);
        exit;
    }
}