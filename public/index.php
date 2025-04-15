<?php
/**
 * Front Controller - Punto de entrada único para la aplicación
 */

// Definir constantes de rutas
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CORE_PATH', ROOT_PATH . '/core');
define('CONFIG_PATH', APP_PATH . '/Config');
define('CONTROLLERS_PATH', APP_PATH . '/Controllers');
define('MODELS_PATH', APP_PATH . '/Models');
define('VIEWS_PATH', APP_PATH . '/Views');
define('HELPERS_PATH', APP_PATH . '/Helpers');
define('SERVICES_PATH', APP_PATH . '/Services');

// Verificar existencia de archivos críticos
if (!file_exists(CONFIG_PATH . '/config.php')) {
    die('Error: Archivo de configuración no encontrado en ' . CONFIG_PATH . '/config.php');
}

if (!file_exists(CONFIG_PATH . '/database.php')) {
    die('Error: Archivo de configuración de base de datos no encontrado en ' . CONFIG_PATH . '/database.php');
}

// Verificar phpMailer
$phpMailerFiles = [
    ROOT_PATH . '/vendor/PHPMailer/src/SMTP.php',
    ROOT_PATH . '/vendor/PHPMailer/src/PHPMailer.php',
    ROOT_PATH . '/vendor/PHPMailer/src/Exception.php'
];

foreach ($phpMailerFiles as $phpMailerFile) {
    if (!file_exists($phpMailerFile)) {
        die('Error: Archivo de PHPMailer no encontrado ' . $phpMailerFile);
    }
}

// Cargar configuración
require_once CONFIG_PATH . '/config.php';
require_once CONFIG_PATH . '/database.php';

// Iniciar sesión DESPUÉS de cargar las configuraciones
session_start();

// Verificar existencia de archivos del núcleo
$coreFiles = [
    CORE_PATH . '/Router.php',
    CORE_PATH . '/Controller.php',
    CORE_PATH . '/Model.php',
    CORE_PATH . '/View.php'
];

foreach ($coreFiles as $file) {
    if (!file_exists($file)) {
        die('Error: Archivo del núcleo no encontrado: ' . $file);
    }
}

// Cargar el núcleo MVC
require_once CORE_PATH . '/Router.php';
require_once CORE_PATH . '/Controller.php';
require_once CORE_PATH . '/Model.php';
require_once CORE_PATH . '/View.php';

// Verificar existencia de helpers
if (!file_exists(HELPERS_PATH . '/functions.php')) {
    die('Error: Archivo de funciones helpers no encontrado en ' . HELPERS_PATH . '/functions.php');
}

if (!file_exists(HELPERS_PATH . '/auth.php')) {
    die('Error: Archivo de funciones de autenticación no encontrado en ' . HELPERS_PATH . '/auth.php');
}

// Cargar helpers globales
require_once HELPERS_PATH . '/functions.php';
require_once HELPERS_PATH . '/auth.php';

// Cargar servicios disponibles
if (file_exists(SERVICES_PATH . '/EmailService.php')) {
    require_once SERVICES_PATH . '/EmailService.php';
} else {
    die('Error: Archivo de funciones de servicio de email no encontrado en ' . SERVICES_PATH . '/EmailService.php');
}

// Procesar la solicitud
$router = new Router();

// Obtener la URL desde $_GET['url'] (establecida por .htaccess)
$url = isset($_GET['url']) ? $_GET['url'] : '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Determinar controlador, método y parámetros
//$controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';
$controllerName = '';
if (!empty($url[0])) {
    // Convertir 'module_admin' a 'ModuleAdmin'
    $parts = explode('_', $url[0]);
    $controllerName = '';
    foreach ($parts as $part) {
        $controllerName .= ucfirst($part);
    }
    $controllerName .= 'Controller';
} else {
    $controllerName = 'HomeController';
}

$methodName = !empty($url[1]) ? $url[1] : 'index';

//Caso específico para módulos: gracias -

// Si el controlador es ModuleController y el método tiene guiones, se asume que es un slug
if ($controllerName === 'ModuleController' && strpos($methodName, '-') !== false) {
    $params = [$methodName]; // El método con guiones se convierte en el primer parámetro
    $methodName = 'load';    // El método real a llamar es 'load', ningún otro más
} else {
    $params = array_slice($url, 2);
}

// Verificar autenticación para rutas protegidas
$publicRoutes = [
    'AuthController' => ['login', 'authenticate', 'logout', 'recover', 'request_reset', 'reset_sent', 'reset', 'complete_reset']
];

$requiresAuth = true;
if (isset($publicRoutes[$controllerName]) && in_array($methodName, $publicRoutes[$controllerName])) {
    $requiresAuth = false;
}

// Si la ruta requiere autenticación y el usuario no está autenticado, redirigir al login
if ($requiresAuth && !isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/auth/login');
    exit;
}

// Cargar el controlador
$controllerFile = CONTROLLERS_PATH . '/' . $controllerName . '.php';
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        // Llamar al método con los parámetros
        if (method_exists($controller, $methodName)) {
            call_user_func_array([$controller, $methodName], $params);
        } else {
            // Método no encontrado - mostrar error 404
            $errorControllerFile = CONTROLLERS_PATH . '/ErrorController.php';
            if (file_exists($errorControllerFile)) {
                require_once $errorControllerFile;
                $errorController = new ErrorController();
                $errorController->notFound();
            } else {
                die('Error 404: Página no encontrada y el controlador de errores no está disponible.');
            }
        }
    } else {
        // Controlador no encontrado - mostrar error 404
        $errorControllerFile = CONTROLLERS_PATH . '/ErrorController.php';
        if (file_exists($errorControllerFile)) {
            require_once $errorControllerFile;
            $errorController = new ErrorController();
            $errorController->notFound();
        } else {
            die('Error 404: Página no encontrada y el controlador de errores no está disponible.');
        }
    }
} else {
    // Si no se especificó un controlador o no existe, cargar controlador predeterminado
    if (empty($url[0])) {
        $homeControllerFile = CONTROLLERS_PATH . '/HomeController.php';
        if (file_exists($homeControllerFile)) {
            require_once $homeControllerFile;
            $controller = new HomeController();
            $controller->index();
        } else {
            die('Error: Controlador predeterminado (HomeController.php) no encontrado.');
        }
    } else {
        // Controlador no encontrado - mostrar error 404
        $errorControllerFile = CONTROLLERS_PATH . '/ErrorController.php';
        if (file_exists($errorControllerFile)) {
            require_once $errorControllerFile;
            $errorController = new ErrorController();
            $errorController->notFound();
        } else {
            die('Error 404: Página no encontrada y el controlador de errores no está disponible.');
        }
    }
}