<?php
// controllers/LibroController.php
// CONTROLADOR - Maneja las transiciones del Estado E2 al E6.
//
// Transiciones implementadas:
//   T04 - (entrada) Login exitoso -> mostrar catalogo
//   T06 - Filtro aplicado (Titulo / Autor / ISBN)
//   T07 - Paginacion (avanzar/retroceder)
//   T08 - Clic en "Nuevo Libro" -> E3
//   T09 - Clic en "Ver detalles" -> E4
//   T10 - Clic en "Editar" -> E5
//   T11 - Clic en "Eliminar" -> E6
//   T12 - Alta invalida (ISBN duplicado)
//   T13 - Alta invalida (datos requeridos)
//   T14 - Alta exitosa -> regresar a E2
//   T15 - Volver desde E3 sin guardar -> E2
//   T16 - Volver desde E4 -> E2
//   T17 - Cambio invalido (datos requeridos)
//   T18 - Cambio exitoso -> regresar a E2
//   T20 - Volver desde E5 sin modificar -> E2
//   T21 - Baja exitosa -> regresar a E2
//   T22 - Cancelar eliminacion -> regresar a E2

require_once __DIR__ . '/../models/LibroModel.php';

class LibroController {

    private LibroModel $model;

    public function __construct() {
        $this->model = new LibroModel();
    }

    // ----------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------

    /** T04 / Proteccion de rutas: redirige al login si no hay sesion */
    private function requireAuth(): void {
        if (empty($_SESSION['usuario'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    private function str(string $key): string {
        return trim($_POST[$key] ?? '');
    }

    private function collectForm(): array {
        $datos = [
            'isbn'      => $this->str('isbn'),
            'titulo'    => $this->str('titulo'),
            'autor'     => $this->str('autor'),
            'editorial' => $this->str('editorial'),
            'sinopsis'  => $this->str('sinopsis'),
            'anio'      => $this->str('anio'),
            'paginas'   => $this->str('paginas'),
            'precio'    => $this->str('precio'),
            'ubicacion' => $this->str('ubicacion'),
            'copias'    => $this->str('copias'),
            'categoria' => $this->str('categoria'),
            'estado'    => $this->str('estado'),
        ];

        $requeridos = ['isbn','titulo','autor','editorial','anio','paginas','precio','ubicacion','copias','categoria'];
        $errores = [];
        foreach ($requeridos as $campo) {
            if ($datos[$campo] === '') {
                $errores[] = $campo;
            }
        }

        return [$datos, $errores];
    }

    // ----------------------------------------------------------
    // T04 / T06 / T07 - Catalogo
    // ----------------------------------------------------------
    public function catalogo(): void {
        $this->requireAuth();

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $filtro = trim($_GET['filtro'] ?? '');
        $campo  = $_GET['campo'] ?? '';

        $libros       = $this->model->getCatalogo($pagina, $filtro, $campo);
        $total        = $this->model->getTotalLibros($filtro, $campo);
        $totalPaginas = (int) ceil($total / 10) ?: 1;

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/libros/catalogo.php';
    }

    // ----------------------------------------------------------
    // T08 - Nuevo Libro (GET)
    // ----------------------------------------------------------
    public function nuevoForm(): void {
        $this->requireAuth();
        $errores  = $_SESSION['form_errores'] ?? [];
        $datos    = $_SESSION['form_datos']   ?? [];
        $errorMsg = $_SESSION['form_msg']     ?? '';
        unset($_SESSION['form_errores'], $_SESSION['form_datos'], $_SESSION['form_msg']);
        require __DIR__ . '/../views/libros/nuevo.php';
    }

    // ----------------------------------------------------------
    // T12 / T13 / T14 - Nuevo Libro (POST)
    // ----------------------------------------------------------
    public function nuevoGuardar(): void {
        $this->requireAuth();

        [$datos, $errores] = $this->collectForm();

        // T13 - Datos requeridos vacios
        if (!empty($errores)) {
            $_SESSION['form_errores'] = $errores;
            $_SESSION['form_datos']   = $datos;
            $_SESSION['form_msg']     = 'datos_requeridos';
            header('Location: index.php?action=nuevo');
            exit;
        }

        // T12 - ISBN duplicado
        if ($this->model->existeISBN($datos['isbn'])) {
            $_SESSION['form_errores'] = ['isbn'];
            $_SESSION['form_datos']   = $datos;
            $_SESSION['form_msg']     = 'isbn_duplicado';
            header('Location: index.php?action=nuevo');
            exit;
        }

        // T14 - Alta exitosa -> regresar a E2
        $this->model->insertar($datos);
        $_SESSION['flash'] = ['tipo' => 'success', 'texto' => 'Libro registrado correctamente.'];
        header('Location: index.php?action=catalogo');
        exit;
    }

    // ----------------------------------------------------------
    // T09 / T16 - Detalles
    // ----------------------------------------------------------
    public function detalles(): void {
        $this->requireAuth();
        $isbn  = $_GET['isbn'] ?? '';
        $libro = $this->model->getByISBN($isbn);
        if (!$libro) {
            header('Location: index.php?action=catalogo');
            exit;
        }
        require __DIR__ . '/../views/libros/detalles.php';
    }

    // ----------------------------------------------------------
    // T10 - Editar (GET)
    // ----------------------------------------------------------
    public function editarForm(): void {
        $this->requireAuth();
        $isbn  = $_GET['isbn'] ?? '';
        $libro = $_SESSION['form_datos'] ?? $this->model->getByISBN($isbn);

        if (!$libro) {
            header('Location: index.php?action=catalogo');
            exit;
        }

        $errores  = $_SESSION['form_errores'] ?? [];
        $errorMsg = $_SESSION['form_msg']     ?? '';
        unset($_SESSION['form_errores'], $_SESSION['form_datos'], $_SESSION['form_msg']);

        require __DIR__ . '/../views/libros/editar.php';
    }

    // ----------------------------------------------------------
    // T17 / T18 - Editar (POST)
    // ----------------------------------------------------------
    public function editarGuardar(): void {
        $this->requireAuth();

        $isbn = trim($_POST['isbn'] ?? '');
        [$datos, $errores] = $this->collectForm();
        $datos['isbn'] = $isbn;

        // T17 - Datos requeridos vacios (excepto ISBN)
        $erroresFiltrados = array_filter($errores, fn($e) => $e !== 'isbn');
        if (!empty($erroresFiltrados)) {
            $_SESSION['form_errores'] = $erroresFiltrados;
            $_SESSION['form_datos']   = $datos;
            $_SESSION['form_msg']     = 'datos_requeridos';
            header("Location: index.php?action=editar&isbn=" . urlencode($isbn));
            exit;
        }

        // T18 - Cambio exitoso -> regresar a E2
        $this->model->actualizar($datos);
        $_SESSION['flash'] = ['tipo' => 'success', 'texto' => 'Libro actualizado correctamente.'];
        header('Location: index.php?action=catalogo');
        exit;
    }

    // ----------------------------------------------------------
    // T11 - Eliminar (GET) — modal de confirmacion
    // ----------------------------------------------------------
    public function eliminarConfirmar(): void {
        $this->requireAuth();
        $isbn  = $_GET['isbn'] ?? '';
        $libro = $this->model->getByISBN($isbn);
        if (!$libro) {
            header('Location: index.php?action=catalogo');
            exit;
        }
        require __DIR__ . '/../views/libros/eliminar.php';
    }

    // ----------------------------------------------------------
    // T21 / T22 - Eliminar (POST)
    // ----------------------------------------------------------
    public function eliminarEjecutar(): void {
        $this->requireAuth();
        $isbn = trim($_POST['isbn'] ?? '');
        if ($isbn !== '') {
            // T21 - Baja exitosa
            $this->model->eliminar($isbn);
            $_SESSION['flash'] = ['tipo' => 'warning', 'texto' => 'Libro eliminado del catalogo.'];
        }
        // T22 - Cancelar simplemente regresa a E2 via GET desde la vista
        header('Location: index.php?action=catalogo');
        exit;
    }
}
