<?php
/**
 * Funciones auxiliares para autenticación y autorización
 */

/**
 * Verifica si el usuario está autenticado
 * 
 * @return bool True si está autenticado, false si no
 */
function isUserLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Verifica si el usuario tiene un rol específico
 * 
 * @param string $roleName Nombre del rol a verificar
 * @return bool True si tiene el rol, false si no
 */
function userHasRole($roleName) {
    if (!isUserLoggedIn() || !isset($_SESSION['roles'])) {
        return false;
    }
    
    foreach ($_SESSION['roles'] as $role) {
        if ($role['name'] === $roleName) {
            return true;
        }
    }
    
    return false;
}

/**
 * Verifica si el usuario tiene permiso para un módulo específico
 * 
 * @param string $moduleSlug Slug del módulo
 * @param string $permission Nombre del permiso (view, edit, admin)
 * @return bool True si tiene permiso, false si no
 */
function userHasPermission($moduleSlug, $permission = 'view') {
    if (!isUserLoggedIn() || !isset($_SESSION['accessible_modules'])) {
        return false;
    }
    
    foreach ($_SESSION['accessible_modules'] as $module) {
        if ($module['slug'] === $moduleSlug && in_array($permission, $module['permissions'])) {
            return true;
        }
    }
    
    return false;
}

/**
 * Verifica si el usuario tiene acceso a un módulo (cualquier permiso)
 * 
 * @param string $moduleSlug Slug del módulo
 * @return bool True si tiene acceso, false si no
 */
function userHasModuleAccess($moduleSlug) {
    if (!isUserLoggedIn() || !isset($_SESSION['accessible_modules'])) {
        return false;
    }
    
    foreach ($_SESSION['accessible_modules'] as $module) {
        if ($module['slug'] === $moduleSlug) {
            return true;
        }
    }
    
    return false;
}

/**
 * Verifica si el usuario es administrador
 * 
 * @return bool True si es administrador, false si no
 */
function isAdmin() {
    return userHasRole('admin');
}

/**
 * Obtiene el ID del usuario actual
 * 
 * @return int|null ID del usuario o null si no está autenticado
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Obtiene el nombre de usuario actual
 * 
 * @return string|null Nombre de usuario o null si no está autenticado
 */
function getCurrentUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

/**
 * Obtiene el nombre completo del usuario actual
 * 
 * @return string|null Nombre completo o null si no está autenticado
 */
function getCurrentUserFullName() {
    return isset($_SESSION['full_name']) ? $_SESSION['full_name'] : getCurrentUsername();
}

/**
 * Obtiene los módulos accesibles para el usuario actual
 * 
 * @return array Array de módulos accesibles
 */
function getAccessibleModules() {
    return isset($_SESSION['accessible_modules']) ? $_SESSION['accessible_modules'] : [];
}

/**
 * Requiere que el usuario esté autenticado o redirige
 * 
 * @param string $redirectTo URL a redirigir si no está autenticado
 */
function requireLogin($redirectTo = 'auth/login') {
    if (!isUserLoggedIn()) {
        header('Location: ' . BASE_URL . '/' . $redirectTo);
        exit;
    }
}

/**
 * Requiere que el usuario tenga un rol específico o redirige
 * 
 * @param string $roleName Nombre del rol requerido
 * @param string $redirectTo URL a redirigir si no tiene el rol
 */
function requireRole($roleName, $redirectTo = 'home') {
    requireLogin();
    
    if (!userHasRole($roleName)) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'text' => 'No tiene permisos para acceder a esta sección.'
        ];
        
        header('Location: ' . BASE_URL . '/' . $redirectTo);
        exit;
    }
}

/**
 * Requiere que el usuario tenga permiso para un módulo o redirige
 * 
 * @param string $moduleSlug Slug del módulo
 * @param string $permission Nombre del permiso
 * @param string $redirectTo URL a redirigir si no tiene permiso
 */
function requirePermission($moduleSlug, $permission = 'view', $redirectTo = 'home') {
    requireLogin();
    
    if (!userHasPermission($moduleSlug, $permission)) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'text' => 'No tiene permisos para realizar esta acción.'
        ];
        
        header('Location: ' . BASE_URL . '/' . $redirectTo);
        exit;
    }
}