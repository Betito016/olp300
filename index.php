<?php
// index.php — Front Controller (Router principal)
// Toda petición pasa por aquí; se despacha al controlador correcto.

session_start();

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/LibroController.php';

$action = $_GET['action'] ?? 'login';
$method = $_SERVER['REQUEST_METHOD'];

$auth   = new AuthController();
$libros = new LibroController();

match (true) {
    // ── Autenticación ──────────────────────────────────────────
    $action === 'login'  && $method === 'GET'  => $auth->showLogin(),
    $action === 'login'  && $method === 'POST' => $auth->processLogin(),
    $action === 'logout'                       => $auth->logout(),

    // ── Catálogo ───────────────────────────────────────────────
    $action === 'catalogo'                     => $libros->catalogo(),

    // ── Nuevo Libro ────────────────────────────────────────────
    $action === 'nuevo'  && $method === 'GET'  => $libros->nuevoForm(),
    $action === 'nuevo'  && $method === 'POST' => $libros->nuevoGuardar(),

    // ── Detalles ───────────────────────────────────────────────
    $action === 'detalles'                     => $libros->detalles(),

    // ── Editar ─────────────────────────────────────────────────
    $action === 'editar' && $method === 'GET'  => $libros->editarForm(),
    $action === 'editar' && $method === 'POST' => $libros->editarGuardar(),

    // ── Eliminar ───────────────────────────────────────────────
    $action === 'eliminar' && $method === 'GET'  => $libros->eliminarConfirmar(),
    $action === 'eliminar' && $method === 'POST' => $libros->eliminarEjecutar(),

    // ── Fallback ───────────────────────────────────────────────
    default => (function() use ($auth) {
        header('Location: index.php?action=login');
        exit;
    })(),
};
