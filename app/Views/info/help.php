<div class="content-header">
    <h1>Ayuda del Sistema</h1>
    <div class="actions">
        <a href="<?php echo baseUrl(); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h2>Introducción</h2>
    </div>
    <div class="card-body">
        <p>Te damos la bienvenida a la sección de ayuda del <?php echo APP_NAME; ?>. Aquí encontrarás información útil sobre cómo utilizar las diferentes funcionalidades del sistema.</p>
        <p>Selecciona un tema del menú para ver la información relacionada o usa el buscador para encontrar respuestas específicas.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card help-navigation mb-4">
            <div class="card-header">
                <h3>Temas de Ayuda</h3>
            </div>
            <div class="list-group list-group-flush" id="helpTopics">
                <a href="#general" class="list-group-item list-group-item-action active" data-toggle="topic">Información General</a>
                <a href="#modules" class="list-group-item list-group-item-action" data-toggle="topic">Navegación por Módulos</a>
                <a href="#permissions" class="list-group-item list-group-item-action" data-toggle="topic">Permisos y Accesos</a>
                <a href="#profile" class="list-group-item list-group-item-action" data-toggle="topic">Mi Perfil</a>
                <?php if (isAdmin()): ?>
                    <a href="#admin" class="list-group-item list-group-item-action" data-toggle="topic">Administración</a>
                <?php endif; ?>
                <a href="#faq" class="list-group-item list-group-item-action" data-toggle="topic">Preguntas Frecuentes</a>
                <a href="#contact" class="list-group-item list-group-item-action" data-toggle="topic">Contacto y Soporte</a>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="help-content">
            <!-- Tema: Información General -->
            <div class="help-topic active" id="general">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2>Información General</h2>
                    </div>
                    <div class="card-body">
                        <h4>¿Qué es <?php echo APP_NAME; ?>?</h4>
                        <p><?php echo APP_NAME; ?> es un sistema modular diseñado para facilitar el acceso a diferentes herramientas y funcionalidades a través de una interfaz unificada. El sistema está organizado en módulos, cada uno con propósitos específicos.</p>

                        <h4>Características Principales</h4>
                        <ul>
                            <li><strong>Interfaz Intuitiva:</strong> Diseño sencillo y fácil de usar.</li>
                            <li><strong>Sistema Modular:</strong> Acceso a diferentes módulos según tus permisos.</li>
                            <li><strong>Control de Acceso:</strong> Sistema de permisos basado en roles.</li>
                            <li><strong>Personalización:</strong> Cada usuario puede acceder a los módulos que necesita.</li>
                        </ul>

                        <h4>Primeros Pasos</h4>
                        <p>Al iniciar sesión, accederás al dashboard principal donde podrás ver los módulos a los que tienes acceso. Simplemente haz clic en cualquier módulo para comenzar a utilizarlo.</p>
                    </div>
                </div>
            </div>

            <!-- Tema: Navegación por Módulos -->
            <div class="help-topic" id="modules">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2>Navegación por Módulos</h2>
                    </div>
                    <div class="card-body">
                        <h4>¿Qué son los módulos?</h4>
                        <p>Los módulos son componentes independientes que ofrecen funcionalidades específicas dentro del sistema. Cada módulo está diseñado para un propósito particular y pueden ser administrados de forma centralizada.</p>

                        <h4>Acceso a Módulos</h4>
                        <p>En la página principal, verás tarjetas que representan los módulos a los que tienes acceso. Cada tarjeta muestra:</p>
                        <ul>
                            <li>Nombre del módulo</li>
                            <li>Un icono representativo</li>
                            <li>Etiquetas que indican tus permisos (Visualizar, Editar, Administrar)</li>
                        </ul>

                        <h4>Navegación entre Módulos</h4>
                        <p>En cualquier momento, puedes volver a la página principal haciendo clic en el logo del sistema o en el enlace "Inicio" en la barra de navegación superior.</p>

                        <h4>Funcionamiento de Módulos</h4>
                        <p>Cada módulo puede tener su propia interfaz y opciones. Al ingresar a un módulo, verás una barra de navegación en la parte superior que te permitirá:</p>
                        <ul>
                            <li>Volver al dashboard principal</li>
                            <li>Acceder a funciones específicas del módulo</li>
                            <li>Configurar opciones (si tienes permisos)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tema: Permisos y Accesos -->
            <div class="help-topic" id="permissions">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2>Permisos y Accesos</h2>
                    </div>
                    <div class="card-body">
                        <h4>Sistema de Permisos</h4>
                        <p>El acceso a los módulos y sus funcionalidades se controla mediante un sistema de permisos basado en roles. Los permisos comunes incluyen:</p>

                        <ul>
                            <li><strong>Visualizar:</strong> Te permite ver y usar el módulo sin realizar cambios.</li>
                            <li><strong>Editar:</strong> Te permite modificar datos o configuraciones dentro del módulo.</li>
                            <li><strong>Administrar:</strong> Te otorga control total sobre el módulo, incluyendo configuraciones avanzadas.</li>
                        </ul>

                        <h4>Roles de Usuario</h4>
                        <p>Cada usuario tiene asignado uno o más roles que determinan sus permisos en el sistema. Si necesitas acceso a módulos adicionales, contacta al administrador del sistema.</p>

                        <h4>Solicitud de Accesos</h4>
                        <p>Si necesitas acceso a módulos adicionales para tu trabajo, debes contactar al administrador del sistema con los siguientes detalles:</p>
                        <ul>
                            <li>Nombre del módulo al que necesitas acceso</li>
                            <li>Tipo de acceso requerido (visualizar, editar, administrar)</li>
                            <li>Justificación del acceso</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tema: Mi Perfil -->
            <div class="help-topic" id="profile">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2>Mi Perfil</h2>
                    </div>
                    <div class="card-body">
                        <h4>Gestión de Cuenta</h4>
                        <p>Puedes acceder a tu perfil de usuario haciendo clic en tu nombre en la esquina superior derecha del sistema. Desde allí podrás:</p>

                        <ul>
                            <li>Ver información de tu cuenta</li>
                            <li>Cambiar tu contraseña</li>
                            <li>Actualizar tu información personal</li>
                            <li>Ver tus roles y permisos</li>
                        </ul>

                        <h4>Cambio de Contraseña</h4>
                        <p>Para cambiar tu contraseña:</p>
                        <ol>
                            <li>Haz clic en tu nombre de usuario en la esquina superior derecha</li>
                            <li>Selecciona "Mi Perfil"</li>
                            <li>Haz clic en "Cambiar Contraseña"</li>
                            <li>Ingresa tu contraseña actual y la nueva contraseña</li>
                            <li>Confirma la nueva contraseña</li>
                            <li>Haz clic en "Guardar"</li>
                        </ol>

                        <div class="alert alert-info">
                            <strong>Nota:</strong> Por seguridad, tu contraseña debe tener al menos 8 caracteres e incluir letras mayúsculas, minúsculas y números.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tema: Administración (solo para administradores) -->
            <?php if (isAdmin()): ?>
                <div class="help-topic" id="admin">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h2>Administración</h2>
                        </div>
                        <div class="card-body">
                            <h4>Gestión de Módulos</h4>
                            <p>Como administrador, tienes acceso a la sección de administración de módulos donde puedes:</p>

                            <ul>
                                <li>Crear nuevos módulos</li>
                                <li>Editar módulos existentes</li>
                                <li>Activar o desactivar módulos</li>
                                <li>Asignar permisos a los roles</li>
                                <li>Ver estadísticas de uso</li>
                            </ul>

                            <h4>Gestión de Usuarios</h4>
                            <p>En la sección de administración de usuarios puedes:</p>

                            <ul>
                                <li>Crear nuevos usuarios</li>
                                <li>Editar información de usuarios</li>
                                <li>Asignar roles a usuarios</li>
                                <li>Activar o desactivar cuentas</li>
                                <li>Restablecer contraseñas</li>
                            </ul>

                            <h4>Registros de Acceso</h4>
                            <p>El sistema mantiene registros detallados de los accesos a módulos. Puedes acceder a estos registros desde la sección de administración para:</p>

                            <ul>
                                <li>Monitorear el uso de módulos</li>
                                <li>Detectar patrones de uso</li>
                                <li>Verificar actividades de usuarios</li>
                                <li>Identificar módulos más utilizados</li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tema: Preguntas Frecuentes -->
            <div class="help-topic" id="faq">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2>Preguntas Frecuentes</h2>
                    </div>
                    <div class="card-body">
                        <div class="faq-accordion">
                            <div class="faq-item">
                                <div class="faq-question" onclick="toggleFaqAnswer(this)">
                                    ¿Cómo puedo acceder a más módulos?
                                    <span class="faq-icon">+</span>
                                </div>
                                <div class="faq-answer">
                                    El acceso a los módulos está determinado por tus roles en el sistema. Si necesitas acceso a módulos adicionales, contacta al administrador del sistema especificando qué módulos necesitas y por qué.
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question" onclick="toggleFaqAnswer(this)">
                                    ¿Olvidé mi contraseña, cómo puedo recuperarla?
                                    <span class="faq-icon">+</span>
                                </div>
                                <div class="faq-answer">
                                    En la pantalla de inicio de sesión, haz clic en "¿Olvidaste tu contraseña?" y sigue las instrucciones. Recibirás un correo con los pasos para restablecer tu contraseña.
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question" onclick="toggleFaqAnswer(this)">
                                    ¿Cómo puedo reportar un problema con un módulo?
                                    <span class="faq-icon">+</span>
                                </div>
                                <div class="faq-answer">
                                    Si encuentras algún problema al usar un módulo, contacta al soporte técnico desde la sección de "Contacto y Soporte" proporcionando detalles sobre el problema, incluyendo el nombre del módulo y los pasos para reproducir el error.
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question" onclick="toggleFaqAnswer(this)">
                                    ¿Es posible usar el sistema en dispositivos móviles?
                                    <span class="faq-icon">+</span>
                                </div>
                                <div class="faq-answer">
                                    Sí, el sistema está diseñado con un enfoque responsive que permite su uso en dispositivos móviles. Sin embargo, para una mejor experiencia, recomendamos utilizar una tablet o computadora.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tema: Contacto y Soporte -->
            <div class="help-topic" id="contact">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2>Contacto y Soporte</h2>
                    </div>
                    <div class="card-body">
                        <h4>Soporte Técnico</h4>
                        <p>Si necesitas ayuda técnica o encuentras algún problema con el sistema, puedes contactar al equipo de soporte:</p>

                        <ul>
                            <li><strong>Email:</strong> soporte@ejemplo.com</li>
                            <li><strong>Teléfono:</strong> (123) 456-7890</li>
                            <li><strong>Horario:</strong> Lunes a Viernes, 9:00 AM - 6:00 PM</li>
                        </ul>

                        <h4>Reportar Problemas</h4>
                        <p>Para reportar un problema, proporciona la siguiente información:</p>

                        <ul>
                            <li>Tu nombre y departamento</li>
                            <li>Descripción detallada del problema</li>
                            <li>Pasos para reproducir el error</li>
                            <li>Capturas de pantalla (si es posible)</li>
                            <li>Fecha y hora en que ocurrió el problema</li>
                        </ul>

                        <h4>Sugerencias y Mejoras</h4>
                        <p>Valoramos tus sugerencias para mejorar el sistema. Si tienes ideas para nuevas funcionalidades o mejoras, envíalas a mejoras@ejemplo.com.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .help-topic {
        display: none;
    }

    .help-topic.active {
        display: block;
    }

    .list-group-item-action {
        cursor: pointer;
    }

    .accordion .btn-link {
        text-decoration: none;
        color: #333;
        font-weight: bold;
        display: block;
        width: 100%;
        text-align: left;
        padding: 0;
    }

    .accordion .btn-link:hover,
    .accordion .btn-link:focus {
        text-decoration: none;
        color: #0056b3;
    }

    /* Estilos para el acordeón de preguntas frecuentes */
    .faq-item {
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }

    .faq-question {
        background-color: #f8f9fa;
        padding: 15px;
        cursor: pointer;
        position: relative;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .faq-question:hover {
        background-color: #e9ecef;
    }

    .faq-icon {
        font-weight: bold;
        font-size: 18px;
        transition: transform 0.3s;
    }

    .faq-answer {
        padding: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out, padding 0.3s ease;
        background-color: #fff;
    }

    .faq-item.active .faq-answer {
        padding: 15px;
        max-height: 500px;
        /* Altura máxima suficiente para el contenido */
    }

    .faq-item.active .faq-icon {
        transform: rotate(45deg);
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejo de navegación por temas
        const topicLinks = document.querySelectorAll('#helpTopics a');
        const topics = document.querySelectorAll('.help-topic');

        // Función para mostrar un tema específico
        function showTopic(topicId) {
            // Ocultar todos los temas
            topics.forEach(topic => {
                topic.classList.remove('active');
            });

            // Desactivar todos los enlaces
            topicLinks.forEach(link => {
                link.classList.remove('active');
            });

            // Mostrar el tema seleccionado
            const selectedTopic = document.getElementById(topicId);
            if (selectedTopic) {
                selectedTopic.classList.add('active');
            }

            // Activar el enlace seleccionado
            const selectedLink = document.querySelector(`[href="#${topicId}"]`);
            if (selectedLink) {
                selectedLink.classList.add('active');
            }
        }

        // Manejar clics en los enlaces de temas
        topicLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const topicId = this.getAttribute('href').substring(1);
                showTopic(topicId);

                // Actualizar URL con hash
                window.location.hash = topicId;
            });
        });

        // Verificar si hay un hash en la URL al cargar
        if (window.location.hash) {
            const topicId = window.location.hash.substring(1);
            showTopic(topicId);
        }
    });

    function toggleFaqAnswer(element) {
        const faqItem = element.parentNode;

        // Si ya está activo, desactivarlo
        if (faqItem.classList.contains('active')) {
            faqItem.classList.remove('active');
        } else {
            // Opcional: cerrar todos los demás elementos abiertos
            const allFaqItems = document.querySelectorAll('.faq-item');
            allFaqItems.forEach(item => {
                item.classList.remove('active');
            });

            // Activar el elemento actual
            faqItem.classList.add('active');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Manejo de navegación por temas
        const topicLinks = document.querySelectorAll('#helpTopics a');
        const topics = document.querySelectorAll('.help-topic');

        // Función para mostrar un tema específico
        function showTopic(topicId) {
            // Ocultar todos los temas
            topics.forEach(topic => {
                topic.classList.remove('active');
            });

            // Desactivar todos los enlaces
            topicLinks.forEach(link => {
                link.classList.remove('active');
            });

            // Mostrar el tema seleccionado
            const selectedTopic = document.getElementById(topicId);
            if (selectedTopic) {
                selectedTopic.classList.add('active');
            }

            // Activar el enlace seleccionado
            const selectedLink = document.querySelector(`[href="#${topicId}"]`);
            if (selectedLink) {
                selectedLink.classList.add('active');
            }
        }

        // Manejar clics en los enlaces de temas
        topicLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const topicId = this.getAttribute('href').substring(1);
                showTopic(topicId);

                // Actualizar URL con hash
                window.location.hash = topicId;
            });
        });

        // Verificar si hay un hash en la URL al cargar
        if (window.location.hash) {
            const topicId = window.location.hash.substring(1);
            showTopic(topicId);
        }
    });
</script>