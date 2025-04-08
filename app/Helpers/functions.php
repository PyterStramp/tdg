<?php
/**
 * Funciones auxiliares generales
 */

/**
 * Muestra un mensaje flash si existe y lo elimina
 * 
 * @param bool $return Si debe devolver el HTML en lugar de imprimirlo
 * @return string|void HTML del mensaje si $return es true, void si no
 */
function showFlashMessage($return = false) {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $html = '<div class="alert alert-' . $message['type'] . '">' . $message['text'] . '</div>';
        unset($_SESSION['flash_message']);
        
        if ($return) {
            return $html;
        }
        
        echo $html;
    }
    
    if ($return) {
        return '';
    }
}

/**
 * Sanitiza un valor para su uso seguro en HTML
 * 
 * @param mixed $value Valor a sanitizar
 * @return mixed Valor sanitizado
 */
function sanitize($value) {
    if (is_array($value)) {
        foreach ($value as $key => $val) {
            $value[$key] = sanitize($val);
        }
        return $value;
    }
    
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Genera un token CSRF y lo guarda en la sesión
 * 
 * @return string Token CSRF
 */
function generateCsrfToken() {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    
    return $token;
}

/**
 * Verifica si un token CSRF es válido
 * 
 * @param string $token Token a verificar
 * @return bool True si es válido, false si no
 */
function validateCsrfToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    $valid = ($_SESSION['csrf_token'] === $token);
    
    // Eliminar el token para evitar reutilización
    unset($_SESSION['csrf_token']);
    
    return $valid;
}

/**
 * Verifica si una cadena es un correo electrónico válido
 * 
 * @param string $email Correo a verificar
 * @return bool True si es válido, false si no
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Obtiene la URL base de la aplicación
 * 
 * @param string $path Ruta a añadir a la URL base
 * @return string URL completa
 */
function baseUrl($path = '') {
    $url = rtrim(BASE_URL, '/');
    
    if (!empty($path)) {
        $url .= '/' . ltrim($path, '/');
    }
    
    return $url;
}

/**
 * Redirige a una URL
 * 
 * @param string $url URL a redirigir
 */
function redirect($url) {
    header('Location: ' . baseUrl($url));
    exit;
}

/**
 * Redirige con un mensaje flash
 * 
 * @param string $url URL a redirigir
 * @param string $message Mensaje a mostrar
 * @param string $type Tipo de mensaje (success, error, info, warning)
 */
function redirectWithMessage($url, $message, $type = 'info') {
    $_SESSION['flash_message'] = [
        'text' => $message,
        'type' => $type
    ];
    
    redirect($url);
}

/**
 * Formatea una fecha según la configuración del sistema
 * 
 * @param mixed $date Fecha a formatear
 * @param string $format Formato a utilizar
 * @return string Fecha formateada
 */
function formatDate($date, $format = 'd/m/Y H:i:s') {
    if (empty($date) || $date == '0000-00-00 00:00:00') {
        return 'N/A';
    }
    
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

/**
 * Trunca un texto a una longitud determinada
 * 
 * @param string $text Texto a truncar
 * @param int $length Longitud máxima
 * @param string $suffix Sufijo a añadir si se trunca
 * @return string Texto truncado
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Obtiene la IP real del cliente
 * 
 * @return string Dirección IP
 */
function getClientIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    return $ip;
}

/**
 * Registra un mensaje en el log de la aplicación
 * 
 * @param string $message Mensaje a registrar
 * @param string $level Nivel de mensaje (info, warning, error, debug)
 */
function logMessage($message, $level = 'info') {
    // Asegurar que existe la carpeta de logs
    $logDir = ROOT_PATH . '/logs';
    if (!is_dir($logDir)) {
        // Intentar crear la carpeta
        if (!mkdir($logDir, 0755, true)) {
            // No se pudo crear la carpeta, usar error_log de PHP
            error_log("No se pudo crear la carpeta de logs: $logDir");
            error_log("$level: $message");
            return;
        }
    }
    
    // Verificar que la carpeta tiene permisos de escritura
    if (!is_writable($logDir)) {
        error_log("La carpeta de logs no tiene permisos de escritura: $logDir");
        error_log("$level: $message");
        return;
    }
    
    $logFile = $logDir . '/' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
    
    // Intentar escribir en el archivo
    $result = file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Si no se pudo escribir, usar error_log
    if ($result === false) {
        error_log("No se pudo escribir en el archivo de log: $logFile");
        error_log("$level: $message");
    }
}