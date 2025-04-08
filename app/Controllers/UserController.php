<?php
/**
 * UserController - Controlador para gestión de usuarios
 */
class UserController extends Controller {
    private $userModel;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->userModel = $this->loadModel('UserModel');
    }
    
    /**
     * Muestra la lista de usuarios
     */
    public function index() {
        // Verificar autenticación y permisos
        requireLogin();
        requireRole('admin');
        
        // Obtener todos los usuarios
        $users = $this->userModel->getAllWithRoles();
        
        $data = [
            'pageTitle' => 'Usuarios',
            'users' => $users
        ];
        
        $this->view->render('user/index', $data);
    }
    
    /**
     * Muestra el formulario para crear un nuevo usuario
     */
    public function create() {
        // Verificar autenticación y permisos
        requireLogin();
        requireRole('admin');
        
        // Cargar roles para el select
        $roleModel = $this->loadModel('RoleModel');
        $roles = $roleModel->getAll();
        
        $data = [
            'pageTitle' => 'Crear Usuario',
            'roles' => $roles
        ];
        
        $this->view->render('user/create', $data);
    }
    
    /**
     * Procesa el formulario para crear un nuevo usuario
     */
    public function store() {
        // Verificar autenticación y permisos
        requireLogin();
        requireRole('admin');
        
        // Verificar si es una solicitud POST
        if (!$this->isPost()) {
            $this->redirect('user');
            return;
        }
        
        // Obtener y validar datos del formulario
        $username = $this->getPost('username');
        $email = $this->getPost('email');
        $password = $this->getPost('password');
        $passwordConfirm = $this->getPost('password_confirm');
        $fullName = $this->getPost('full_name');
        $active = $this->getPost('active') ? 1 : 0;
        $roleIds = isset($_POST['roles']) ? $_POST['roles'] : [2]; // Por defecto rol 'user' (ID 2)
        
        // Validar campos obligatorios
        if (empty($username) || empty($email) || empty($password)) {
            $this->redirectWithMessage('user/create', 'Todos los campos obligatorios deben ser completados', 'error');
            return;
        }
        
        // Validar que las contraseñas coincidan
        if ($password !== $passwordConfirm) {
            $this->redirectWithMessage('user/create', 'Las contraseñas no coinciden', 'error');
            return;
        }
        
        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithMessage('user/create', 'El formato del email no es válido', 'error');
            return;
        }
        
        // Verificar si el nombre de usuario ya existe
        if ($this->userModel->usernameExists($username)) {
            $this->redirectWithMessage('user/create', 'El nombre de usuario ya está en uso', 'error');
            return;
        }
        
        // Verificar si el email ya existe
        if ($this->userModel->emailExists($email)) {
            $this->redirectWithMessage('user/create', 'El email ya está en uso', 'error');
            return;
        }
        
        // Preparar datos del usuario
        $userData = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'full_name' => $fullName,
            'active' => $active
        ];
        
        // Crear usuario
        $userId = $this->userModel->createUser($userData);
        
        if (!$userId) {
            $this->redirectWithMessage('user/create', 'Error al crear el usuario. Inténtelo de nuevo.', 'error');
            return;
        }
        
        // Asignar roles al usuario
        $this->userModel->assignRoles($userId, $roleIds);

        // Enviar correo electrónico de bienvenida
        try {
            // Inicializar el servicio de correo
            $emailService = new EmailService();
            
            // Enviar correo de recuperación
            $emailSent = $emailService->sendWelcome($email, $fullName, $username, $password);
            
            // Registrar resultado del envío
            if ($emailSent) {
                logMessage("Correo de bienvenida enviado a {$email}", 'info');
            } else {
                logMessage("Error al enviar correo de bienvenida a {$email}", 'error');
            }
        } catch (Exception $e) {
            logMessage("Excepción al enviar correo: " . $e->getMessage(), 'error');
        }
        
        // Redirigir a la lista de usuarios con mensaje de éxito
        $this->redirectWithMessage('user', 'Usuario creado correctamente', 'success');
    }
    
    /**
     * Muestra el formulario para editar un usuario
     * 
     * @param int $id ID del usuario a editar
     */
    public function edit($id = null) {
        // Verificar autenticación y permisos
        requireLogin();
        requireRole('admin');
        
        // Validar que se proporcionó un ID
        if (!$id) {
            $this->redirectWithMessage('user', 'ID de usuario no especificado', 'error');
            return;
        }
        
        // Obtener datos del usuario
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            $this->redirectWithMessage('user', 'Usuario no encontrado', 'error');
            return;
        }
        
        // Obtener roles del usuario
        $userRoles = $this->userModel->getUserRoleIds($id);
        
        // Cargar roles para el select
        $roleModel = $this->loadModel('RoleModel');
        $roles = $roleModel->getAll();
        
        $data = [
            'pageTitle' => 'Editar Usuario',
            'user' => $user,
            'userRoles' => $userRoles,
            'roles' => $roles
        ];
        
        $this->view->render('user/edit', $data);
    }
    
    /**
     * Procesa el formulario para actualizar un usuario
     * 
     * @param int $id ID del usuario a actualizar
     */
    public function update($id = null) {
        // Verificar autenticación y permisos
        requireLogin();
        requireRole('admin');
        
        // Validar que se proporcionó un ID
        if (!$id) {
            $this->redirectWithMessage('user', 'ID de usuario no especificado', 'error');
            return;
        }
        
        // Verificar si es una solicitud POST
        if (!$this->isPost()) {
            $this->redirect('user');
            return;
        }
        
        // Obtener usuario existente
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            $this->redirectWithMessage('user', 'Usuario no encontrado', 'error');
            return;
        }
        
        // Obtener y validar datos del formulario
        $username = $this->getPost('username');
        $email = $this->getPost('email');
        $password = $this->getPost('password');
        $passwordConfirm = $this->getPost('password_confirm');
        $fullName = $this->getPost('full_name');
        $active = $this->getPost('active') ? 1 : 0;
        $roleIds = isset($_POST['roles']) ? $_POST['roles'] : [2]; // Por defecto rol 'user' (ID 2)
        
        // Validar campos obligatorios
        if (empty($username) || empty($email)) {
            $this->redirectWithMessage('user/edit/' . $id, 'Los campos username y email son obligatorios', 'error');
            return;
        }
        
        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithMessage('user/edit/' . $id, 'El formato del email no es válido', 'error');
            return;
        }
        
        // Verificar si el nombre de usuario ya existe (excluyendo el usuario actual)
        if ($username !== $user['username'] && $this->userModel->usernameExists($username)) {
            $this->redirectWithMessage('user/edit/' . $id, 'El nombre de usuario ya está en uso', 'error');
            return;
        }
        
        // Verificar si el email ya existe (excluyendo el usuario actual)
        if ($email !== $user['email'] && $this->userModel->emailExists($email)) {
            $this->redirectWithMessage('user/edit/' . $id, 'El email ya está en uso', 'error');
            return;
        }
        
        // Preparar datos del usuario
        $userData = [
            'username' => $username,
            'email' => $email,
            'full_name' => $fullName,
            'active' => $active
        ];
        
        // Actualizar contraseña solo si se proporcionó una nueva
        if (!empty($password)) {
            // Validar que las contraseñas coincidan
            if ($password !== $passwordConfirm) {
                $this->redirectWithMessage('user/edit/' . $id, 'Las contraseñas no coinciden', 'error');
                return;
            }
            
            $userData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }
        
        // Actualizar usuario
        $success = $this->userModel->update($id, $userData);
        
        if (!$success) {
            $this->redirectWithMessage('user/edit/' . $id, 'Error al actualizar el usuario. Inténtelo de nuevo.', 'error');
            return;
        }
        
        // Actualizar roles del usuario
        $this->userModel->assignRoles($id, $roleIds);
        
        // Redirigir a la lista de usuarios con mensaje de éxito
        $this->redirectWithMessage('user', 'Usuario actualizado correctamente', 'success');
    }
    
    /**
     * Elimina un usuario
     * 
     * @param int $id ID del usuario a eliminar
     */
    public function delete($id = null) {
        // Verificar autenticación y permisos
        requireLogin();
        requireRole('admin');
        
        // Validar que se proporcionó un ID
        if (!$id) {
            $this->redirectWithMessage('user', 'ID de usuario no especificado', 'error');
            return;
        }
        
        // No permitir eliminar el propio usuario
        if ($id == $_SESSION['user_id']) {
            $this->redirectWithMessage('user', 'No puedes eliminar tu propio usuario', 'error');
            return;
        }
        
        // Eliminar usuario
        $success = $this->userModel->delete($id);
        
        if (!$success) {
            $this->redirectWithMessage('user', 'Error al eliminar el usuario. Inténtelo de nuevo.', 'error');
            return;
        }
        
        // Redirigir a la lista de usuarios con mensaje de éxito
        $this->redirectWithMessage('user', 'Usuario eliminado correctamente', 'success');
    }
    
    /**
     * Muestra el perfil del usuario actual
     */
    public function profile() {
        // Verificar autenticación
        requireLogin();
        
        // Obtener datos del usuario actual
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            $this->redirectWithMessage('home', 'Error al cargar el perfil', 'error');
            return;
        }
        
        // Obtener roles del usuario
        $roles = $this->userModel->getUserRoles($userId);
        
        $data = [
            'pageTitle' => 'Mi Perfil',
            'user' => $user,
            'roles' => $roles
        ];
        
        $this->view->render('user/profile', $data);
    }
    
    /**
     * Muestra el formulario para editar el perfil del usuario actual
     */
    public function edit_profile() {
        // Verificar autenticación
        requireLogin();
        
        // Obtener datos del usuario actual
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            $this->redirectWithMessage('home', 'Error al cargar el perfil', 'error');
            return;
        }
        
        $data = [
            'pageTitle' => 'Editar Mi Perfil',
            'user' => $user
        ];
        
        $this->view->render('user/edit_profile', $data);
    }
    
    /**
     * Procesa el formulario para actualizar el perfil del usuario actual
     */
    public function update_profile() {
        // Verificar autenticación
        requireLogin();
        
        // Verificar si es una solicitud POST
        if (!$this->isPost()) {
            $this->redirect('user/profile');
            return;
        }
        
        // Obtener ID del usuario actual
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            $this->redirectWithMessage('home', 'Error al cargar el perfil', 'error');
            return;
        }
        
        // Obtener y validar datos del formulario
        $email = $this->getPost('email');
        $fullName = $this->getPost('full_name');
        
        // Validar campos obligatorios
        if (empty($email)) {
            $this->redirectWithMessage('user/edit_profile', 'El correo electrónico es obligatorio', 'error');
            return;
        }
        
        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithMessage('user/edit_profile', 'El formato del email no es válido', 'error');
            return;
        }
        
        // Verificar si el email ya existe (excluyendo el usuario actual)
        if ($email !== $user['email'] && $this->userModel->emailExists($email)) {
            $this->redirectWithMessage('user/edit_profile', 'El email ya está en uso', 'error');
            return;
        }
        
        // Preparar datos del usuario
        $userData = [
            'email' => $email,
            'full_name' => $fullName
        ];
        
        // Actualizar usuario
        $success = $this->userModel->update($userId, $userData);
        
        if (!$success) {
            $this->redirectWithMessage('user/edit_profile', 'Error al actualizar el perfil. Inténtelo de nuevo.', 'error');
            return;
        }
        
        // Actualizar datos de sesión
        $_SESSION['full_name'] = $fullName;
        
        // Redirigir al perfil con mensaje de éxito
        $this->redirectWithMessage('user/profile', 'Perfil actualizado correctamente', 'success');
    }
    
    /**
     * Muestra el formulario para cambiar la contraseña del usuario actual
     */
    public function change_password() {
        // Verificar autenticación
        requireLogin();
        
        $data = [
            'pageTitle' => 'Cambiar Contraseña'
        ];
        
        $this->view->render('user/change_password', $data);
    }
    
    /**
     * Procesa el formulario para cambiar la contraseña del usuario actual
     */
    public function update_password() {
        // Verificar autenticación
        requireLogin();
        
        // Verificar si es una solicitud POST
        if (!$this->isPost()) {
            $this->redirect('user/profile');
            return;
        }
        
        // Obtener ID del usuario actual
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            $this->redirectWithMessage('home', 'Error al cargar el perfil', 'error');
            return;
        }
        
        // Obtener y validar datos del formulario
        $currentPassword = $this->getPost('current_password');
        $newPassword = $this->getPost('new_password');
        $confirmPassword = $this->getPost('confirm_password');
        
        // Validar campos obligatorios
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $this->redirectWithMessage('user/change_password', 'Todos los campos son obligatorios', 'error');
            return;
        }
        
        // Verificar contraseña actual
        if (!password_verify($currentPassword, $user['password'])) {
            $this->redirectWithMessage('user/change_password', 'La contraseña actual es incorrecta', 'error');
            return;
        }
        
        // Validar que las nuevas contraseñas coincidan
        if ($newPassword !== $confirmPassword) {
            $this->redirectWithMessage('user/change_password', 'Las nuevas contraseñas no coinciden', 'error');
            return;
        }
        
        // Validar longitud de la nueva contraseña
        if (strlen($newPassword) < 8) {
            $this->redirectWithMessage('user/change_password', 'La nueva contraseña debe tener al menos 8 caracteres', 'error');
            return;
        }
        
        // Actualizar contraseña
        $userData = [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ];
        
        $success = $this->userModel->update($userId, $userData);
        
        if (!$success) {
            $this->redirectWithMessage('user/change_password', 'Error al actualizar la contraseña. Inténtelo de nuevo.', 'error');
            return;
        }
        
        // Redirigir al perfil con mensaje de éxito
        $this->redirectWithMessage('user/profile', 'Contraseña actualizada correctamente', 'success');
    }
}