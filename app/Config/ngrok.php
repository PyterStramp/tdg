<?php

// Detectar si estamos en ngrok
if (strpos($_SERVER['HTTP_HOST'], 'ngrok.io') !== false || strpos($_SERVER['HTTP_HOST'], 'ngrok-free.app') !== false) {
    // Forzar protocolo HTTPS
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['REQUEST_SCHEME'] = 'https';
    
    // Reemplazar BASE_URL con HTTPS
    $ngrokBaseUrl = 'https://' . $_SERVER['HTTP_HOST'];
    $ngrokBaseUrl .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    $ngrokBaseUrl = rtrim($ngrokBaseUrl, '/');
    
    // Redefinir la constante
    if (defined('BASE_URL')) {
        // Usar runkit si está disponible, o simplemente sobrescribir
        define('NGROK_BASE_URL', $ngrokBaseUrl);
    } else {
        define('BASE_URL', $ngrokBaseUrl);
    }
    
    // Establecer headers adicionales
    header("Content-Security-Policy: upgrade-insecure-requests");
}