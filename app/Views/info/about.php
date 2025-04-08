<div class="content-header">
    <h1>Acerca de <?php echo APP_NAME; ?></h1>
    <div class="actions">
        <a href="<?php echo baseUrl(); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h2>Información del Sistema</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>Detalles Técnicos</h4>
                <table class="table">
                    <tr>
                        <th>Nombre del Sistema:</th>
                        <td><?php echo APP_NAME; ?></td>
                    </tr>
                    <tr>
                        <th>Versión:</th>
                        <td><?php echo APP_VERSION; ?></td>
                    </tr>
                    <tr>
                        <th>Plataforma:</th>
                        <td>PHP <?php echo phpversion(); ?></td>
                    </tr>
                    <tr>
                        <th>Servidor:</th>
                        <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                    </tr>
                    <tr>
                        <th>Base de Datos:</th>
                        <td>MySQL</td>
                    </tr>
                    <tr>
                        <th>Zona Horaria:</th>
                        <td><?php echo date_default_timezone_get(); ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h4>Componentes del Sistema</h4>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Sistema Base MVC
                        <span class="badge badge-primary badge-pill">Core</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Gestión de Módulos
                        <span class="badge badge-primary badge-pill">Core</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Sistema de Autenticación
                        <span class="badge badge-primary badge-pill">Core</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Control de Acceso Basado en Roles
                        <span class="badge badge-primary badge-pill">Core</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Servicio de Correo Electrónico
                        <span class="badge badge-info badge-pill">PHPMailer 6.9.3</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h2>Propósito y Funcionalidades</h2>
            </div>
            <div class="card-body">
                <p><?php echo APP_NAME; ?> es un sistema modular diseñado para permitir la integración y acceso centralizado a diversas herramientas y aplicaciones. Proporciona un marco unificado donde los usuarios pueden acceder a diferentes módulos según sus roles y permisos.</p>
                
                <h5>Principales Características:</h5>
                <ul>
                    <li>Arquitectura modular extensible</li>
                    <li>Gestión dinámica de módulos</li>
                    <li>Sistema de permisos granular</li>
                    <li>Interfaz de usuario intuitiva</li>
                    <li>Registro detallado de actividades</li>
                    <li>Seguridad robusta</li>
                </ul>
                
                <p>El sistema está diseñado para ser fácilmente ampliable, permitiendo la adición de nuevos módulos y funcionalidades sin afectar el funcionamiento base.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h2>Equipo de Desarrollo</h2>
            </div>
            <div class="card-body">
                <p>Este sistema ha sido desarrollado por un equipo comprometido con la creación de soluciones tecnológicas innovadoras y de alta calidad.</p>
                
                <h5>Liderazgo del Proyecto:</h5>
                <ul>
                    <li><strong>Dirección:</strong> </li>
                    <li><strong>Arquitectura:</strong> </li>
                </ul>
                
                <h5>Desarrollo:</h5>
                <ul>
                    <li><strong>Desarrollo Backend:</strong> </li>
                    <li><strong>Desarrollo Frontend:</strong> </li>
                    <li><strong>Base de Datos:</strong> </li>
                </ul>
                
                <h5>Aseguramiento de Calidad:</h5>
                <ul>
                    <li><strong>Pruebas:</strong> </li>
                    <li><strong>Documentación:</strong> </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h2>Historial de Versiones</h2>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Versión</th>
                    <th>Fecha</th>
                    <th>Cambios Principales</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1.0.0</td>
                    <td>01/04/2025</td>
                    <td>
                        <ul>
                            <li>Lanzamiento inicial</li>
                            <li>Implementación del sistema base</li>
                            <li>Gestión de módulos</li>
                            <li>Sistema de autenticación</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>0.9.0</td>
                    <td>15/03/2025</td>
                    <td>
                        <ul>
                            <li>Versión beta final</li>
                            <li>Corrección de errores críticos</li>
                            <li>Optimización de rendimiento</li>
                            <li>Mejoras en la interfaz de usuario</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>0.5.0</td>
                    <td>01/02/2025</td>
                    <td>
                        <ul>
                            <li>Versión alfa</li>
                            <li>Implementación inicial del núcleo</li>
                            <li>Prototipo de gestión de módulos</li>
                            <li>Sistema básico de autenticación</li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Licencia y Términos de Uso</h2>
    </div>
    <div class="card-body">
        <p>© <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos los derechos reservados.</p>
        <p>© PHP Mailer 6.9.3</p>
        
        <p>Este software se distribuye bajo una licencia privada y está destinado únicamente para uso interno de la organización. No se permite su redistribución, modificación o uso no autorizado.</p>
        
        <h5>Términos de Uso:</h5>
        <ol>
            <li>El acceso al sistema está limitado a usuarios autorizados con credenciales válidas.</li>
            <li>Cada usuario es responsable de mantener la confidencialidad de sus credenciales.</li>
            <li>El uso del sistema debe cumplir con las políticas internas de la organización.</li>
            <li>No se permite el acceso no autorizado o intentos de comprometer la seguridad del sistema.</li>
            <li>La organización se reserva el derecho de monitorear el uso del sistema para fines de seguridad.</li>
        </ol>
        
        <p>Para más información sobre licencias y términos de uso, contacte al departamento de TI.</p>
    </div>
</div>