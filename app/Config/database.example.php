<?php
/**
 * Configuración de la conexión a la base de datos
 */

// Parámetros de conexión (ajustar según tu entorno)
$db_host = '';
$db_name = '';
$db_user = '';
$db_pass = '';

// Establecer conexión
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Verificar conexión
if ($conn->connect_error) {
    die('Error de conexión a la base de datos: ' . $conn->connect_error);
}

// Establecer UTF-8 como conjunto de caracteres
$conn->set_charset('utf8');