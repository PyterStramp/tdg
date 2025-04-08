<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>
    
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <link rel="stylesheet" href="<?php echo baseUrl('css/normalize.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/styles.css'); ?>">
    <link rel="icon" type="image/x-icon" href="<?php echo baseUrl('images/favico.ico'); ?>"">
    
    <!-- Estilos adicionales según sección -->
    <?php 
    // Determinar la sección actual
    $currentUrl = $_SERVER['REQUEST_URI'];
    $isUserSection = strpos($currentUrl, '/user') !== false;
    
    if ($isUserSection): 
    ?>
        <link rel="stylesheet" href="<?php echo baseUrl('css/user.css'); ?>">
    <?php endif; ?>
    
    <!-- Scripts JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>     
    <script src="<?php echo baseUrl('js/main.js'); ?>" defer></script>
    
    <?php if ($isUserSection): ?>
        <script src="<?php echo baseUrl('js/user.js'); ?>" defer></script>
    <?php endif; ?>
    
    <?php if (isset($extraStyles)): ?>
        <?php foreach ($extraStyles as $style): ?>
            <link rel="stylesheet" href="<?php echo baseUrl($style); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (isset($extraScripts)): ?>
        <?php foreach ($extraScripts as $script): ?>
            <script src="<?php echo baseUrl($script); ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php if (isUserLoggedIn()): ?>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="<?php echo baseUrl(); ?>">
                    <span class="app-name"><?php echo APP_NAME; ?></span>
                </a>
            </div>
            
            <nav class="main-nav">
                <button class="menu-toggle" aria-label="Abrir menú">
                    <i class="fas fa-bars"></i>
                </button>
                
                <ul class="nav-menu">
                    <li><a href="<?php echo baseUrl(); ?>"><i class="fas fa-home"></i> Inicio</a></li>
                    
                    <?php if (!empty($_SESSION['accessible_modules'])): ?>
                        <li class="has-submenu">
                            <a href="<?php echo baseUrl('module_admin'); ?>"><i class="fas fa-folder"></i> Módulos</a>
                            <ul class="submenu">
                                <?php foreach ($_SESSION['accessible_modules'] as $module): ?>
                                    <li>
                                        <a href="<?php echo baseUrl('module/' . $module['slug']); ?>">
                                            <?php if (!empty($module['icon'])): ?>
                                                <i class="<?php echo $module['icon']; ?>"></i>
                                            <?php else: ?>
                                                <i class="fas fa-puzzle-piece"></i>
                                            <?php endif; ?>
                                            <?php echo $module['name']; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>

                    <?php endif; ?>
                    
                    <?php if (isAdmin()): ?>
                        <li class="has-submenu">
                            <a href="#"><i class="fas fa-cog"></i> Administración </a>
                            <ul class="submenu">
                                <li><a href="<?php echo baseUrl('user'); ?>"><i class="fas fa-users"></i> Usuarios</a></li>
                                <!--<li><a href="<?//php echo baseUrl('role'); ?>"><i class="fas fa-user-tag"></i> Roles</a></li>-->
                                <li><a href="<?php echo baseUrl('module_admin'); ?>"><i class="fas fa-cubes"></i> Gestión de Módulos</a></li>
                                <li><a href="<?php echo baseUrl('module_logs'); ?>"><i class="fas fa-history"></i> Registros de Módulos</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <div class="user-menu">
                <div class="user-info">
                    <span class="user-name"><?php echo getCurrentUserFullName(); ?></span>
                    <img src="<?php if (isAdmin()): echo baseUrl('images/default-avatar.png'); else: echo baseUrl('images/default-avatar-user.png'); endif; ?>" alt="Avatar" class="user-avatar">
                    <i class="fas fa-chevron-down"></i>
                </div>
                
                <ul class="user-dropdown">
                    <li><a href="<?php echo baseUrl('user/profile'); ?>"><i class="fas fa-user"></i> Mi perfil</a></li>
                    <li><a href="<?php echo baseUrl('user/change_password'); ?>"><i class="fas fa-key"></i> Cambiar contraseña</a></li>
                    <li><a href="<?php echo baseUrl('auth/logout'); ?>"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </header>
    
    <div class="main-content">
    <?php endif; ?>
    
    <div class="container">
        <!-- Aquí comienza el contenido de la página -->