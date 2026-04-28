<?php
// models/LibroModel.php
// Responsabilidad: acceso a datos de la tabla Libros (CRUD completo)

require_once __DIR__ . '/../config/db.php';

class LibroModel {

    private PDO $db;
    private int $porPagina = 10;

    public function __construct() {
        $this->db = getDB();
    }

    // ----------------------------------------------------------
    // READ – Catálogo con filtro y paginación
    // ----------------------------------------------------------

    /**
     * Retorna libros paginados con filtro opcional por Título, Autor o ISBN.
     */
    public function getCatalogo(int $pagina = 1, string $filtro = '', string $campo = ''): array {
        $offset = ($pagina - 1) * $this->porPagina;
        $params = [];
        $where  = '';

        $camposPermitidos = ['Titulo', 'Autor', 'ISBN'];

        if ($filtro !== '' && in_array($campo, $camposPermitidos, true)) {
            $where = "WHERE $campo LIKE :filtro";
            $params[':filtro'] = '%' . $filtro . '%';
        }

        $stmt = $this->db->prepare(
            "SELECT ISBN, Titulo, Autor, Editorial, Categoria, NumeroCopias, Estado
               FROM Libros
             $where
             ORDER BY Titulo ASC
             LIMIT :limit OFFSET :offset"
        );

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit',  $this->porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,          PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Total de libros para calcular páginas.
     */
    public function getTotalLibros(string $filtro = '', string $campo = ''): int {
        $params = [];
        $where  = '';

        $camposPermitidos = ['Titulo', 'Autor', 'ISBN'];

        if ($filtro !== '' && in_array($campo, $camposPermitidos, true)) {
            $where = "WHERE $campo LIKE :filtro";
            $params[':filtro'] = '%' . $filtro . '%';
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM Libros $where");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    // ----------------------------------------------------------
    // READ – Detalle individual
    // ----------------------------------------------------------

    /**
     * Retorna todos los campos de un libro por ISBN.
     */
    public function getByISBN(string $isbn): array|false {
        $stmt = $this->db->prepare(
            'SELECT * FROM Libros WHERE ISBN = :isbn LIMIT 1'
        );
        $stmt->execute([':isbn' => $isbn]);
        return $stmt->fetch();
    }

    /**
     * Verifica si un ISBN ya existe en la tabla.
     */
    public function existeISBN(string $isbn): bool {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM Libros WHERE ISBN = :isbn LIMIT 1'
        );
        $stmt->execute([':isbn' => $isbn]);
        return $stmt->fetchColumn() !== false;
    }

    // ----------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------

    /**
     * Inserta un nuevo libro. Retorna true en éxito.
     */
    public function insertar(array $datos): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO Libros
                (ISBN, Titulo, Autor, Editorial, Sinopsis,
                 AnioPublicacion, NumeroPaginas, Precio,
                 Ubicacion, NumeroCopias, Categoria)
             VALUES
                (:isbn, :titulo, :autor, :editorial, :sinopsis,
                 :anio, :paginas, :precio,
                 :ubicacion, :copias, :categoria)'
        );
        return $stmt->execute([
            ':isbn'      => $datos['isbn'],
            ':titulo'    => $datos['titulo'],
            ':autor'     => $datos['autor'],
            ':editorial' => $datos['editorial'],
            ':sinopsis'  => $datos['sinopsis'],
            ':anio'      => $datos['anio'],
            ':paginas'   => $datos['paginas'],
            ':precio'    => $datos['precio'],
            ':ubicacion' => $datos['ubicacion'],
            ':copias'    => $datos['copias'],
            ':categoria' => $datos['categoria'],
        ]);
    }

    // ----------------------------------------------------------
    // UPDATE
    // ----------------------------------------------------------

    /**
     * Actualiza un libro existente (ISBN no cambia).
     */
    public function actualizar(array $datos): bool {
        $stmt = $this->db->prepare(
            'UPDATE Libros SET
                Titulo          = :titulo,
                Autor           = :autor,
                Editorial       = :editorial,
                Sinopsis        = :sinopsis,
                AnioPublicacion = :anio,
                NumeroPaginas   = :paginas,
                Precio          = :precio,
                Ubicacion       = :ubicacion,
                NumeroCopias    = :copias,
                Categoria       = :categoria
             WHERE ISBN = :isbn'
        );
        return $stmt->execute([
            ':isbn'      => $datos['isbn'],
            ':titulo'    => $datos['titulo'],
            ':autor'     => $datos['autor'],
            ':editorial' => $datos['editorial'],
            ':sinopsis'  => $datos['sinopsis'],
            ':anio'      => $datos['anio'],
            ':paginas'   => $datos['paginas'],
            ':precio'    => $datos['precio'],
            ':ubicacion' => $datos['ubicacion'],
            ':copias'    => $datos['copias'],
            ':categoria' => $datos['categoria'],
        ]);
    }

    // ----------------------------------------------------------
    // DELETE
    // ----------------------------------------------------------

    /**
     * Elimina un libro por ISBN. Retorna true en éxito.
     */
    public function eliminar(string $isbn): bool {
        $stmt = $this->db->prepare(
            'DELETE FROM Libros WHERE ISBN = :isbn'
        );
        return $stmt->execute([':isbn' => $isbn]);
    }
}
