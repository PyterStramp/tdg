<?php
/**
 * AuthController - Controlador para la autenticación de usuarios
 */
class AuthController extends Controller {
    private $userModel;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->userModel = $this->loadModel('UserModel');
    }
    
    /**
     * Muestra la página de login
     */
    public function login() {
        // Si ya está autenticado, redirigir al dashboard
        if (isUserLoggedIn()) {
            $this->redirect('home');
            return;
        }
        
        $this->view->render('auth/login');
    }
    
    /**
     * Procesa la autenticación del usuario
     */
    public function authenticate() {
        // Si no es una solicitud POST, redirigir a login
        if (!$this->isPost()) {
            $this->redirect('auth/login');
            return;
        }
        
        // Obtener datos del formulario
        $username = $this->getPost('username');
        $password = $this->getPost('password');
        
        // Validar que se hayan proporcionado ambos campos
        if (empty($username) || empty($password)) {
            $this->redirectWithMessage('auth/login', 'Debe proporcionar usuario y contraseña', 'error');
            return;
        }
        
        // Buscar el usuario por nombre de usuario o email
        $user = $this->userModel->findByUsernameOrEmail($username);
        
        // Verificar si se encontró el usuario
        if (!$user) {
            $this->redirectWithMessage('auth/login', 'Usuario o contraseña incorrectos', 'error');
            return;
        }
        
        // Verificar si el usuario está activo
        if (!$user['active']) {
            $this->redirectWithMessage('auth/login', 'Su cuenta está desactivada. Contacte al administrador.', 'error');
            return;
        }
        
        // Verificar la contraseña
        if (!password_verify($password, $user['password'])) {
            $this->redirectWithMessage('auth/login', 'Usuario o contraseña incorrectos', 'error');
            return;
        }
        
        // Autenticación exitosa - Guardar datos del usuario en la sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        
        // Actualizar la fecha del último inicio de sesión
        $this->userModel->updateLastLogin($user['id']);
        
        // Obtener los roles del usuario
        $this->loadUserRoles($user['id']);
        
        // Cargar módulos accesibles
        $this->loadAccessibleModules($user['id']);
        
        // Registrar el acceso
        $this->logAccess($user['id']);
        
        // Redirigir al dashboard
        $this->redirect('home');
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        // Destruir la sesión
        session_destroy();
        
        // Redirigir a login
        $this->redirect('auth/login');
    }
    
    /**
     * Muestra el formulario de recuperación de contraseña
     */
    public function recover() {
        // Si ya está autenticado, redirigir al dashboard
        if (isUserLoggedIn()) {
            $this->redirect('home');
            return;
        }
        
        $this->view->render('auth/recover');
    }
    
    /**
     * Procesa la solicitud de recuperación de contraseña
     */
    public function request_reset() {
        // Si no es una solicitud POST, redirigir a recover
        if (!$this->isPost()) {
            $this->redirect('auth/recover');
            return;
        }
        // Obtener email del formulario
        $email = $this->getPost('email');
        
        // Validar que se haya proporcionado email
        if (empty($email)) {
            $this->redirectWithMessage('auth/recover', 'Debe proporcionar una dirección de correo electrónico', 'error');
            return;
        }
        
        // Buscar el usuario por email
        $user = $this->userModel->findByUsernameOrEmail($email);
        
        // Por seguridad, no indicar si el email existe o no
        if (!$user) {
            $this->redirectWithMessage('auth/reset_sent', 'Si su correo está registrado, recibirá instrucciones al correo registrado para restablecer su contraseña', 'info');
            return;
        }
        
        // Verificar si el usuario está activo
        if (!$user['active']) {
            $this->redirectWithMessage('auth/recover', 'Su usuario no está activo. Contacte con el administrador', 'error');
            return;
        }
        
        // Generar token único de restablecimiento
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Guardar token en la base de datos
        $success = $this->userModel->createPasswordResetToken($user['id'], $token, $expires);
        
        if (!$success) {
            $this->redirectWithMessage('auth/recover', 'Error al procesar la solicitud. Inténtelo de nuevo.', 'error');
            return;
        }
        
        // Enviar email con enlace de restablecimiento (simulado)
        $resetLink = BASE_URL . "/auth/reset/{$token}";
        
        // Enviar correo electrónico
        try {
            // Inicializar el servicio de correo
            $emailService = new EmailService();
            
            // Enviar correo de recuperación
            $emailSent = $emailService->sendPasswordReset($user['email'], $user['full_name'], $resetLink);
            
            // Registrar resultado del envío
            if ($emailSent) {
                logMessage("Correo de recuperación enviado a {$user['email']}", 'info');
            } else {
                logMessage("Error al enviar correo de recuperación a {$user['email']}", 'error');
            }
        } catch (Exception $e) {
            logMessage("Excepción al enviar correo: " . $e->getMessage(), 'error');
        }
        
        // Guardar el enlace en los logs (para desarrollo)
        logMessage("RECUPERACIÓN DE CONTRASEÑA: Link de recuperación para {$user['email']}: {$resetLink}", 'info');  

        
        // Redirigir a la página de confirmación
        $this->redirectWithMessage('auth/reset_sent', 'Se han enviado instrucciones para restablecer su contraseña a su correo electrónico', 'success');
    }
    
    /**
     * Muestra la página de confirmación de envío de email
     */
    public function reset_sent() {
        $this->view->render('auth/reset_sent');
    }
    
    /**
     * Muestra el formulario para restablecer la contraseña
     * 
     * @param string $token Token de restablecimiento
     */
    public function reset($token = null) {
        // Si no se proporcionó token, redirigir a login
        if (!$token) {
            $this->redirectWithMessage('auth/login', 'Enlace de restablecimiento inválido o expirado', 'error');
            return;
        }
        
        // Verificar si el token es válido
        $resetInfo = $this->userModel->getPasswordResetToken($token);
        
        if (!$resetInfo || strtotime($resetInfo['expires']) < time()) {
            $this->redirectWithMessage('auth/login', 'Enlace de restablecimiento inválido o expirado', 'error');
            return;
        }
        
        $data = [
            'pageTitle' => 'Restablecer Contraseña',
            'token' => $token
        ];
        
        $this->view->render('auth/reset', $data);
    }
    
    /**
     * Procesa el restablecimiento de contraseña
     */
    public function complete_reset() {
        // Si no es una solicitud POST, redirigir a login
        if (!$this->isPost()) {
            $this->redirect('auth/login');
            return;
        }
        
        // Obtener datos del formulario
        $token = $this->getPost('token');
        $password = $this->getPost('password');
        $confirmPassword = $this->getPost('confirm_password');
        
        // Validar que se hayan proporcionado todos los campos
        if (empty($token) || empty($password) || empty($confirmPassword)) {
            $this->redirectWithMessage('auth/reset' . $token, 'Todos los campos son obligatorios', 'error');
            return;
        }
        
        // Validar que las contraseñas coincidan
        if ($password !== $confirmPassword) {
            $this->redirectWithMessage('auth/reset' . $token, 'Las contraseñas no coinciden', 'error');
            return;
        }
        
        // Validar longitud de la contraseña
        if (strlen($password) < 8) {
            $this->redirectWithMessage('auth/reset' . $token, 'La contraseña debe tener al menos 8 caracteres', 'error');
            return;
        }
        
        // Verificar si el token es válido
        $resetInfo = $this->userModel->getPasswordResetToken($token);
        
        if (!$resetInfo || strtotime($resetInfo['expires']) < time()) {
            $this->redirectWithMessage('auth/login', 'Enlace de restablecimiento inválido o expirado', 'error');
            return;
        }
        
        // Obtener el usuario
        $user = $this->userModel->getById($resetInfo['user_id']);
        
        if (!$user || !$user['active']) {
            $this->redirectWithMessage('auth/login', 'Usuario no encontrado o desactivado', 'error');
            return;
        }
        
        // Actualizar contraseña
        $userData = [
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ];
        
        $success = $this->userModel->update($user['id'], $userData);
        
        if (!$success) {
            $this->redirectWithMessage('auth/reset' . $token, 'Error al actualizar la contraseña. Inténtelo de nuevo.', 'error');
            return;
        }
        
        // Eliminar todos los tokens de restablecimiento para este usuario
        $this->userModel->deletePasswordResetTokens($user['id']);

        // Notificar por correo sobre el cambio de contraseña
        try {
            // Inicializar el servicio de correo
            $emailService = new EmailService();
                    
            // Crear asunto y cuerpo del correo
            $subject = APP_NAME . ' - Contraseña actualizada';
            $body = '
            <html>
            <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
                    <h2 style="color: #3498db;">Contraseña actualizada</h2>
                    <p>Hola ' . htmlspecialchars($user['full_name'] ?: $user['username']) . ',</p>
                    <p>Tu contraseña ha sido actualizada correctamente.</p>
                    <p>Si no realizaste este cambio, por favor contacta inmediatamente con soporte.</p>
                    <p>Saludos,<br>' . APP_NAME . '</p>
                </div>
            </body>
            </html>';
            
            // Enviar correo
            $emailService->send($user['email'], $subject, $body);
        } catch (Exception $e) {
            // Solo registrar el error, no afectar el flujo del usuario
            logMessage("Error al enviar notificación de cambio de contraseña: " . $e->getMessage(), 'error');
        }
        
        // Redirigir a login con mensaje de éxito
        $this->redirectWithMessage('auth/login', 'Su contraseña ha sido restablecida correctamente. Puede iniciar sesión ahora.', 'success');
    }
    
    /**
     * Carga los roles del usuario
     */
    private function loadUserRoles($userId) {
        $roles = $this->userModel->getUserRoles($userId);
        
        $_SESSION['roles'] = $roles;
    }
    
    /**
     * Carga los módulos accesibles por el usuario
     */
    private function loadAccessibleModules($userId) {
        // Cargar módulos accesibles directamente
        $moduleModel = $this->loadModel('ModuleModel');

        // Obtener módulos según roles
        $modules = $moduleModel->getAccessibleModules($userId);   
        
        if (empty($modules)) {
            $_SESSION['accessible_modules'] = [];
            return;
        }
        
        
        $_SESSION['accessible_modules'] = $modules;
    }
    
    /**
     * Registra el acceso del usuario
     */
    private function logAccess($userId) {
        $this->userModel->logAccess($userId, 'login');
    }
}