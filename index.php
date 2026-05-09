<?php
// index.php — Front Controller / Router principal
// Toda peticion HTTP pasa por aqui y se despacha al controlador correcto.
//
// Mapa de acciones y transiciones del DET:
//   login  GET   -> T01 - Mostrar pantalla de autenticacion
//   login  POST  -> T02 / T03 / T04 - Procesar credenciales
//   logout        -> T05 / T19 - Cerrar sesion
//   catalogo      -> T04 (entrada) / T06 (filtro) / T07 (paginacion)
//   nuevo  GET   -> T08 - Mostrar formulario nuevo libro
//   nuevo  POST  -> T12 / T13 / T14 - Procesar nuevo libro
//   detalles      -> T09 / T16 - Ver detalles del libro
//   editar GET   -> T10 - Mostrar formulario edicion
//   editar POST  -> T17 / T18 - Procesar edicion
//   eliminar GET -> T11 - Mostrar confirmacion de eliminacion
//   eliminar POST-> T21 / T22 - Procesar eliminacion

session_start();

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/LibroController.php';

$action = $_GET['action'] ?? 'login';
$method = $_SERVER['REQUEST_METHOD'];

$auth   = new AuthController();
$libros = new LibroController();

match (true) {
    // ── Autenticacion (E1) ─────────────────────────────────────
    $action === 'login'  && $method === 'GET'  => $auth->showLogin(),
    $action === 'login'  && $method === 'POST' => $auth->processLogin(),
    $action === 'logout'                       => $auth->logout(),

    // ── Catalogo (E2) ──────────────────────────────────────────
    $action === 'catalogo'                     => $libros->catalogo(),

    // ── Nuevo Libro (E3) ───────────────────────────────────────
    $action === 'nuevo'  && $method === 'GET'  => $libros->nuevoForm(),
    $action === 'nuevo'  && $method === 'POST' => $libros->nuevoGuardar(),

    // ── Detalles (E4) ──────────────────────────────────────────
    $action === 'detalles'                     => $libros->detalles(),

    // ── Editar (E5) ────────────────────────────────────────────
    $action === 'editar' && $method === 'GET'  => $libros->editarForm(),
    $action === 'editar' && $method === 'POST' => $libros->editarGuardar(),

    // ── Eliminar (E6) ──────────────────────────────────────────
    $action === 'eliminar' && $method === 'GET'  => $libros->eliminarConfirmar(),
    $action === 'eliminar' && $method === 'POST' => $libros->eliminarEjecutar(),

    // ── Fallback ───────────────────────────────────────────────
    default => (function() {
        header('Location: index.php?action=login');
        exit;
    })(),
};
