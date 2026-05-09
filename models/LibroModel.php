<?php
// models/LibroModel.php
// MODELO - Acceso a datos de la tabla Libros.
// Responsabilidad exclusiva: ejecutar operaciones SQL sobre la BD.
// No contiene logica de presentacion ni manejo de sesiones.
//
// Estados del sistema que dependen de este modelo:
//   E2 - Catalogo de Libros (getCatalogo, getTotalLibros)
//   E3 - Ingresar Nuevo Libro (existeISBN, insertar)
//   E4 - Detalles del Libro (getByISBN)
//   E5 - Editar Libro (getByISBN, actualizar)
//   E6 - Eliminar Libro (getByISBN, eliminar)

require_once __DIR__ . '/../config/db.php';

class LibroModel {

    private PDO $db;
    private int $porPagina = 10;

    public function __construct() {
        $this->db = getDB();
    }

    // ----------------------------------------------------------
    // E2 - Catalogo con filtro (T06) y paginacion (T07)
    // ----------------------------------------------------------
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
    // E4 / E5 / E6 - Buscar libro individual
    // ----------------------------------------------------------
    public function getByISBN(string $isbn): array|false {
        $stmt = $this->db->prepare(
            'SELECT * FROM Libros WHERE ISBN = :isbn LIMIT 1'
        );
        $stmt->execute([':isbn' => $isbn]);
        return $stmt->fetch();
    }

    // ----------------------------------------------------------
    // E3 - Verificar ISBN duplicado (T12)
    // ----------------------------------------------------------
    public function existeISBN(string $isbn): bool {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM Libros WHERE ISBN = :isbn LIMIT 1'
        );
        $stmt->execute([':isbn' => $isbn]);
        return $stmt->fetchColumn() !== false;
    }

    // ----------------------------------------------------------
    // E3 - Insertar nuevo libro (T14)
    // ----------------------------------------------------------
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
    // E5 - Actualizar libro (T18) — incluye campo Estado
    // ----------------------------------------------------------
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
                Categoria       = :categoria,
                Estado          = :estado
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
            ':estado'    => $datos['estado'] ?? 'disponible',
        ]);
    }

    // ----------------------------------------------------------
    // E6 - Eliminar libro (T21)
    // ----------------------------------------------------------
    public function eliminar(string $isbn): bool {
        $stmt = $this->db->prepare(
            'DELETE FROM Libros WHERE ISBN = :isbn'
        );
        return $stmt->execute([':isbn' => $isbn]);
    }
}
