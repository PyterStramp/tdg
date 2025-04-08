<?php
/**
 * Configuración general de la aplicación
 */

// Información de la aplicación
define('APP_NAME', '');
define('APP_VERSION', '');

// Configuración de zonas horarias
date_default_timezone_set('');

// Configuración de sesiones usando session_set_cookie_params en lugar de ini_set
$secure = isset($_SERVER['HTTPS']) ? true : false;
session_set_cookie_params([
    'lifetime' => 3600, // 1 hora
    'path' => '/',
    'domain' => '',
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Configuración de seguridad
define('ENCRYPTION_KEY', '');

// Configuración de rutas URL
$baseUrl = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http');
$baseUrl .= '://' . $_SERVER['HTTP_HOST'];
$baseUrl .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
$baseUrl = rtrim($baseUrl, '/');

define('BASE_URL', $baseUrl);

// Configuración de correo electrónico
define('MAIL_FROM', '');
define('MAIL_NAME', '');

// Configuración SMTP
define('MAIL_SMTP_HOST', ''); // Servidor SMTP
define('MAIL_SMTP_PORT', ''); // Puerto SMTP (587 para TLS, 465 para SSL)
define('MAIL_SMTP_USER', ''); // Usuario SMTP
define('MAIL_SMTP_PASS', ''); // Contraseña SMTP
define('MAIL_SMTP_SECURE', ''); // Protocolo de seguridad (tls o ssl)

// Configuración de debug
define('DEBUG_MODE', true); // Cambiar a false en producción

// Mostrar o no errores según modo de depuración
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}