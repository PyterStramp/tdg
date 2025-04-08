# Sistema de Módulos

Este repositorio contiene un sistema de gestión modular basado en PHP Vanilla, diseñado para permitir la integración y administración de diferentes módulos de funcionalidad.

## Características

- **Arquitectura MVC**: Sistema organizado con el patrón Modelo-Vista-Controlador
- **Gestión de usuarios**: Autenticación completa, recuperación de contraseñas
- **Control de acceso**: Sistema de roles y permisos para módulos
- **Módulos dinámicos**: Capacidad para crear, instalar y gestionar módulos
- **Servicio de correo**: Integración con sistema de notificaciones por email
- **Registro de actividad**: Seguimiento del uso de módulos y accesos

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web Apache (con mod_rewrite) (Recomendado) o Nginx
- Extensiones PHP: mysqli, mbstring, json, session
- El entorno fue desarrollado en XAMPP, entonces cualquier configuración que brinde XAMPP en Apache o MySQL fue pensada para XAMPP
- PHPMailer, se debe extraer en vendor/PHPMailer, así mismo fue probado en la versión 6.9.3

## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/PyterStramp/tdg.git
   cd sistema-modulos
   ```

2. **Configurar la base de datos**
   - Importar el esquema de base de datos desde `database/tdg.sql`
   ```bash
   mysql -u usuario -p nombre_base_datos < database/tdg.sql
   ```

3. **Configurar el entorno**
   - Copiar los archivos de configuración de ejemplo
   ```bash
   cp app/Config/config.example.php app/Config/config.php
   cp app/Config/database.example.php app/Config/database.php
   ```
   - Editar los archivos con tus datos de configuración

4. **Configurar permisos**
   ```bash
   chmod -R 755 public/
   chmod -R 755 app/Modules/
   chmod 755 logs/
   ```

5. **Configurar el servidor web**
   - Para Apache, asegúrate de que el mod_rewrite esté habilitado
   - Configura el documento raíz del servidor para que apunte al directorio `public/`

## Estructura del Proyecto

```
project/
├── app/                       # Código de la aplicación
│   ├── Config/                # Configuraciones de la aplicación
│   ├── Controllers/           # Controladores que procesan las solicitudes
│   ├── Helpers/               # Funciones auxiliares
│   ├── Models/                # Clases para manejo de datos
│   ├── modules/               # Carpeta para alojar los módulos implementados
│   ├── Services/              # Servicios de la aplicación
│   └── Views/                 # Plantillas para la presentación
├── core/                      # Núcleo del sistema MVC
├── database/                  # Esquemas y scripts de base de datos
├── logs/                      # Directorio para logs del sistema (no incluido en Git)
├── public/                    # Punto de entrada público
└── vendor/                    # Bibliotecas externas (no incluido en Git)
```

## Creación y Gestión de Módulos

### Estructura de un Módulo

Cada módulo tiene la siguiente estructura mínima:
```
module-nombre/
├── index.html            # Punto de entrada del módulo
└── [otros archivos...]   # Archivos adicionales del módulo
```

### Crear un Nuevo Módulo

1. Accede al panel de administración
2. Navega a "Administración de Módulos"
3. Haz clic en "Crear Nuevo Módulo"
4. Completa la información requerida
5. Sube los archivos del módulo o utiliza el esquema predeterminado

## Configuración del Servicio de Email

Para habilitar las funcionalidades de correo electrónico:

1. Edita `app/Config/config.php`
2. Configura los parámetros SMTP:
   ```php
   define('MAIL_SMTP_HOST', 'smtp.tuservidor.com');
   define('MAIL_SMTP_PORT', 587);
   define('MAIL_SMTP_USER', 'tu_usuario');
   define('MAIL_SMTP_PASS', 'tu_contraseña');
   define('MAIL_SMTP_SECURE', 'tls');
   ```

## Configuración de la base de datos

Para habilitar las funcionalidades de correo electrónico:

1. Edita `app/Config/database.php`
2. Configura los parámetros SMTP:
   ```php
   // Parámetros de conexión
    $db_host = 'tu_host';
    $db_name = 'nombre_bd';
    $db_user = 'usuario';
    $db_pass = 'contrasenia';
    ```

## Seguridad

- Los archivos de configuración con información sensible no se incluyen en el repositorio
- Se recomienda cambiar la clave de encriptación en producción
- Actualiza regularmente las dependencias
