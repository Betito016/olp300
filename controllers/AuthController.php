<?php
// controllers/AuthController.php
// CONTROLADOR - Maneja las transiciones del Estado E1 (Autenticacion).
//
// Transiciones implementadas:
//   T01 - Abrir aplicacion -> mostrar pantalla de login
//   T02 - Login invalido (credenciales incorrectas)
//   T03 - Login invalido (campos vacios)
//   T04 - Login exitoso -> ir a E2 (Catalogo)
//   T05 - Salir del sistema desde E1
//   T19 - Cerrar sesion desde E2 -> regresa a E1

require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController {

    private UsuarioModel $model;

    public function __construct() {
        $this->model = new UsuarioModel();
    }

    // ----------------------------------------------------------
    // T01 - Mostrar pantalla de login
    // ----------------------------------------------------------
    public function showLogin(): void {
        if (!empty($_SESSION['usuario'])) {
            header('Location: index.php?action=catalogo');
            exit;
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    // ----------------------------------------------------------
    // T02 / T03 / T04 - Procesar credenciales
    // ----------------------------------------------------------
    public function processLogin(): void {
        $usuario    = trim($_POST['usuario']    ?? '');
        $contrasena = trim($_POST['contrasena'] ?? '');

        // T03 - Campos vacios
        if ($usuario === '' || $contrasena === '') {
            $_SESSION['auth_error'] = 'campos_vacios';
            header('Location: index.php?action=login');
            exit;
        }

        // T02 - Credenciales invalidas
        $user = $this->model->autenticar($usuario, $contrasena);
        if ($user === false) {
            $_SESSION['auth_error'] = 'credenciales_invalidas';
            header('Location: index.php?action=login');
            exit;
        }

        // T04 - Login exitoso -> ir a E2
        $_SESSION['usuario'] = $user['Usuario'];
        $_SESSION['nombre']  = $user['Nombre'];
        unset($_SESSION['auth_error']);
        header('Location: index.php?action=catalogo');
        exit;
    }

    // ----------------------------------------------------------
    // T05 / T19 - Cerrar sesion
    // ----------------------------------------------------------
    public function logout(): void {
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
}
