<?php
/**
 * EmailService - Servicio para envío de correos electrónicos
 */
class EmailService {
    private $mailer;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Cargar PHPMailer
        require_once ROOT_PATH . '/vendor/PHPMailer/src/Exception.php';
        require_once ROOT_PATH . '/vendor/PHPMailer/src/PHPMailer.php';
        require_once ROOT_PATH . '/vendor/PHPMailer/src/SMTP.php';
        
        // Crear instancia de PHPMailer
        $this->mailer = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Configurar
            $this->mailer->isSMTP();
            $this->mailer->Host = MAIL_SMTP_HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = MAIL_SMTP_USER;
            $this->mailer->Password = MAIL_SMTP_PASS;
            $this->mailer->SMTPSecure = MAIL_SMTP_SECURE;
            $this->mailer->Port = MAIL_SMTP_PORT;
            
            // Configuración adicional
            $this->mailer->setFrom(MAIL_FROM, MAIL_NAME);
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';
            
            // Modo debug para desarrollo
            if (DEBUG_MODE) {
                $this->mailer->SMTPDebug = 2; // Nivel de depuración (0-4)
                $this->mailer->Debugoutput = function($str, $level) {
                    logMessage("PHPMailer Debug ($level): $str", 'debug');
                };
            }
        } catch (Exception $e) {
            logMessage('Error al inicializar PHPMailer: ' . $e->getMessage(), 'error');
        }
    }
    
    /**
     * Envía un correo electrónico
     * 
     * @param string $to Destinatario
     * @param string $subject Asunto
     * @param string $body Cuerpo del mensaje (HTML)
     * @param string $altBody Cuerpo alternativo (texto plano)
     * @return bool True si se envió correctamente, false si no
     */
    public function send($to, $subject, $body, $altBody = '') {
        try {
            // Reiniciar para nuevo envío
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            // Establecer destinatario
            $this->mailer->addAddress($to);
            
            // Establecer asunto y cuerpo
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = $altBody ?: strip_tags($body);
            
            // Enviar
            $result = $this->mailer->send();
            
            // Registrar éxito
            if ($result) {
                logMessage("Correo enviado con éxito a: $to", 'info');
            }
            
            return $result;
        } catch (Exception $e) {
            // Registrar error
            logMessage('Error al enviar correo: ' . $this->mailer->ErrorInfo, 'error');
            return false;
        }
    }
    
    /**
     * Envía un correo para recuperación de contraseña
     * 
     * @param string $email Correo del usuario
     * @param string $name Nombre del usuario
     * @param string $resetLink Enlace de recuperación
     * @return bool True si se envió correctamente, false si no
     */
    public function sendPasswordReset($email, $name, $resetLink) {
        $subject = APP_NAME . ' - Recuperación de contraseña';
        
        // Crear cuerpo del mensaje HTML
        $body = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                .header { text-align: center; padding-bottom: 10px; border-bottom: 1px solid #eee; margin-bottom: 20px; }
                .content { margin-bottom: 20px; }
                .button { display: inline-block; padding: 10px 20px; background-color: #3498db; color: white; text-decoration: none; border-radius: 5px; }
                .footer { padding-top: 10px; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #777; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>' . APP_NAME . '</h2>
                </div>
                <div class="content">
                    <p>Hola ' . htmlspecialchars($name ?: 'Usuario') . ',</p>
                    <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta.</p>
                    <p>Haz clic en el siguiente enlace para crear una nueva contraseña:</p>
                    <p style="text-align: center;">
                        <a href="' . $resetLink . '" class="button">Restablecer Contraseña</a>
                    </p>
                    <p>O copia y pega esta URL en tu navegador:</p>
                    <p>' . $resetLink . '</p>
                    <p>Este enlace es válido por 1 hora. Después de ese tiempo, deberás solicitar un nuevo enlace de recuperación.</p>
                    <p>Si no solicitaste restablecer tu contraseña, puedes ignorar este mensaje.</p>
                </div>
                <div class="footer">
                    <p>Este es un correo automático, por favor no responder.</p>
                    <p>&copy; ' . date('Y') . ' ' . APP_NAME . '</p>
                </div>
            </div>
        </body>
        </html>';
        
        // Crear cuerpo del mensaje de texto plano
        $altBody = "Hola " . ($name ?: 'Usuario') . ",\n\n" .
                  "Recibimos una solicitud para restablecer la contraseña de tu cuenta.\n\n" .
                  "Para crear una nueva contraseña, visita el siguiente enlace:\n" .
                  $resetLink . "\n\n" .
                  "Este enlace es válido por 1 hora. Después de ese tiempo, deberás solicitar un nuevo enlace de recuperación.\n\n" .
                  "Si no solicitaste restablecer tu contraseña, puedes ignorar este mensaje.\n\n" .
                  "Este es un correo automático, por favor no responder.\n\n" .
                  APP_NAME;
        
        return $this->send($email, $subject, $body, $altBody);
    }
    
    /**
     * Envía un correo de bienvenida a un nuevo usuario
     * 
     * @param string $email Correo del usuario
     * @param string $name Nombre del usuario
     * @param string $username Nombre de usuario en sentido del aplicativo
     * @param string $password Contraseña (opcional, solo si se genera automáticamente)
     * @return bool True si se envió correctamente, false si no
     */
    public function sendWelcome($email, $name, $username, $password = null) {
        $subject = 'Te damos la bienvenida a ' . APP_NAME;
        
        // Crear cuerpo del mensaje HTML
        $body = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                .header { text-align: center; padding-bottom: 10px; border-bottom: 1px solid #eee; margin-bottom: 20px; }
                .content { margin-bottom: 20px; }
                .credentials { background-color: #f9f9f9; padding: 15px; margin: 15px 0; border-radius: 5px; }
                .button { display: inline-block; padding: 10px 20px; background-color: #3498db; color: white; text-decoration: none; border-radius: 5px; }
                .footer { padding-top: 10px; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #777; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Bienvenido a ' . APP_NAME . '</h2>
                </div>
                <div class="content">
                    <p>Hola ' . htmlspecialchars($name ?: 'Usuario') . ',</p>
                    <p>Te damos la bienvenida a nuestro sistema. Tu cuenta ha sido creada exitosamente.</p>';
        
        if ($password) {
            $body .= '
                    <div class="credentials">
                        <h3>Tus credenciales de acceso:</h3>
                        <p><strong>Usuario:</strong> ' . htmlspecialchars($username) . '</p>
                        <p><strong>Contraseña:</strong> ' . htmlspecialchars($password) . '</p>
                        <p>Te recomendamos cambiar esta contraseña después de iniciar sesión por primera vez.</p>
                    </div>';
        }
        
        $body .= '
                    <p style="text-align: center;">
                        <a href="' . BASE_URL . '/auth/login" class="button">Iniciar Sesión</a>
                    </p>
                    <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactar a nuestro equipo de soporte.</p>
                </div>
                <div class="footer">
                    <p>Este es un correo automático, por favor no responder.</p>
                    <p>&copy; ' . date('Y') . ' ' . APP_NAME . '</p>
                </div>
            </div>
        </body>
        </html>';
        
        // Crear cuerpo del mensaje de texto plano
        $altBody = "Hola " . ($name ?: 'Usuario') . ",\n\n" .
                   "Te damos la bienvenida a nuestro sistema. Tu cuenta ha sido creada exitosamente.\n\n";
        
        if ($password) {
            $altBody .= "Tus credenciales de acceso:\n" .
                        "Usuario: " . $username . "\n" .
                        "Contraseña: " . $password . "\n\n" .
                        "Te recomendamos cambiar esta contraseña después de iniciar sesión por primera vez.\n\n";
        }
        
        $altBody .= "Para iniciar sesión, visita: " . BASE_URL . "/auth/login\n\n" .
                    "Si tienes alguna pregunta o necesitas ayuda, no dudes en contactar a nuestro equipo de soporte.\n\n" .
                    "Este es un correo automático, por favor no responder.\n\n" .
                    APP_NAME;
        
        return $this->send($email, $subject, $body, $altBody);
    }
}