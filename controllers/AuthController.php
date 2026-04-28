<?php
// controllers/AuthController.php
// Responsabilidad: lógica de autenticación y gestión de sesión

require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController {

    private UsuarioModel $model;

    public function __construct() {
        $this->model = new UsuarioModel();
    }

    // ----------------------------------------------------------
    // GET /login — mostrar formulario
    // ----------------------------------------------------------
    public function showLogin(): void {
        // Si ya hay sesión activa, redirigir al catálogo
        if (!empty($_SESSION['usuario'])) {
            header('Location: index.php?action=catalogo');
            exit;
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    // ----------------------------------------------------------
    // POST /login — procesar credenciales
    // ----------------------------------------------------------
    public function processLogin(): void {
        $usuario    = trim($_POST['usuario']    ?? '');
        $contrasena = trim($_POST['contrasena'] ?? '');

        // Validar campos vacíos
        if ($usuario === '' || $contrasena === '') {
            $_SESSION['auth_error'] = 'campos_vacios';
            header('Location: index.php?action=login');
            exit;
        }

        // Autenticar contra BD
        $user = $this->model->autenticar($usuario, $contrasena);

        if ($user === false) {
            $_SESSION['auth_error'] = 'credenciales_invalidas';
            header('Location: index.php?action=login');
            exit;
        }

        // Sesión exitosa
        $_SESSION['usuario'] = $user['Usuario'];
        $_SESSION['nombre']  = $user['Nombre'];
        unset($_SESSION['auth_error']);

        header('Location: index.php?action=catalogo');
        exit;
    }

    // ----------------------------------------------------------
    // Cerrar sesión
    // ----------------------------------------------------------
    public function logout(): void {
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
}
