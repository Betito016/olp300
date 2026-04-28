<?php
// controllers/LibroController.php
// Responsabilidad: lógica de negocio para el catálogo CRUD de libros

require_once __DIR__ . '/../models/LibroModel.php';

class LibroController {

    private LibroModel $model;

    public function __construct() {
        $this->model = new LibroModel();
    }

    // ----------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------

    /** Redirige al login si no hay sesión activa */
    private function requireAuth(): void {
        if (empty($_SESSION['usuario'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    /** Sanitiza un string del input */
    private function str(string $key): string {
        return trim($_POST[$key] ?? '');
    }

    /** Recoge y valida campos del formulario libro */
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
        ];

        $errores = [];
        // Todos los campos son obligatorios excepto sinopsis
        $requeridos = ['isbn','titulo','autor','editorial','anio','paginas','precio','ubicacion','copias','categoria'];
        foreach ($requeridos as $campo) {
            if ($datos[$campo] === '') {
                $errores[] = $campo;
            }
        }

        return [$datos, $errores];
    }

    // ----------------------------------------------------------
    // Catálogo
    // ----------------------------------------------------------
    public function catalogo(): void {
        $this->requireAuth();

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $filtro = trim($_GET['filtro'] ?? '');
        $campo  = $_GET['campo'] ?? '';

        $libros = $this->model->getCatalogo($pagina, $filtro, $campo);
        $total  = $this->model->getTotalLibros($filtro, $campo);
        $totalPaginas = (int) ceil($total / 10) ?: 1;

        // Mensaje flash desde operaciones anteriores
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/libros/catalogo.php';
    }

    // ----------------------------------------------------------
    // Nuevo Libro – GET
    // ----------------------------------------------------------
    public function nuevoForm(): void {
        $this->requireAuth();
        $errores = $_SESSION['form_errores'] ?? [];
        $datos   = $_SESSION['form_datos']   ?? [];
        $errorMsg = $_SESSION['form_msg']    ?? '';
        unset($_SESSION['form_errores'], $_SESSION['form_datos'], $_SESSION['form_msg']);
        require __DIR__ . '/../views/libros/nuevo.php';
    }

    // ----------------------------------------------------------
    // Nuevo Libro – POST
    // ----------------------------------------------------------
    public function nuevoGuardar(): void {
        $this->requireAuth();

        [$datos, $errores] = $this->collectForm();

        if (!empty($errores)) {
            $_SESSION['form_errores'] = $errores;
            $_SESSION['form_datos']   = $datos;
            $_SESSION['form_msg']     = 'datos_requeridos';
            header('Location: index.php?action=nuevo');
            exit;
        }

        if ($this->model->existeISBN($datos['isbn'])) {
            $_SESSION['form_errores'] = ['isbn'];
            $_SESSION['form_datos']   = $datos;
            $_SESSION['form_msg']     = 'isbn_duplicado';
            header('Location: index.php?action=nuevo');
            exit;
        }

        $this->model->insertar($datos);
        $_SESSION['flash'] = ['tipo' => 'success', 'texto' => 'Libro registrado correctamente.'];
        header('Location: index.php?action=catalogo');
        exit;
    }

    // ----------------------------------------------------------
    // Detalles
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
    // Editar – GET
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
    // Editar – POST
    // ----------------------------------------------------------
    public function editarGuardar(): void {
        $this->requireAuth();

        // ISBN viene de campo oculto (no editable)
        $isbn = trim($_POST['isbn'] ?? '');
        [$datos, $errores] = $this->collectForm();
        // Para edición, el ISBN ya está fijo; quitar de validación vacíos
        $datos['isbn'] = $isbn;

        // Revalidar sin isbn en requeridos (ya existe)
        $erroresFiltrados = array_filter($errores, fn($e) => $e !== 'isbn');

        if (!empty($erroresFiltrados)) {
            $_SESSION['form_errores'] = $erroresFiltrados;
            $_SESSION['form_datos']   = $datos;
            $_SESSION['form_msg']     = 'datos_requeridos';
            header("Location: index.php?action=editar&isbn=" . urlencode($isbn));
            exit;
        }

        $this->model->actualizar($datos);
        $_SESSION['flash'] = ['tipo' => 'success', 'texto' => 'Libro actualizado correctamente.'];
        header('Location: index.php?action=catalogo');
        exit;
    }

    // ----------------------------------------------------------
    // Eliminar – confirmación modal + POST
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

    public function eliminarEjecutar(): void {
        $this->requireAuth();
        $isbn = trim($_POST['isbn'] ?? '');
        if ($isbn !== '') {
            $this->model->eliminar($isbn);
            $_SESSION['flash'] = ['tipo' => 'warning', 'texto' => 'Libro eliminado del catálogo.'];
        }
        header('Location: index.php?action=catalogo');
        exit;
    }
}
