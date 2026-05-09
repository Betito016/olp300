<?php
// config/db.php — Conexion PDO centralizada
// Responsabilidad: proveer una instancia de conexion a la BD.
// Las credenciales se leen desde variables de entorno (Railway).
//
// Este componente es utilizado exclusivamente por los Modelos:
//   - UsuarioModel.php
//   - LibroModel.php
//
// Estandar: PSR-12
// Patron: MVC — capa de infraestructura compartida por los Modelos

function getDB(): PDO {
    $host     = getenv('DB_HOST')     ?: 'localhost';
    $port     = getenv('DB_PORT')     ?: '3306';
    $dbname   = getenv('DB_NAME')     ?: 'olp300';
    $user     = getenv('DB_USER')     ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        error_log('DB Connection error: ' . $e->getMessage());
        die(json_encode(['error' => 'Error de conexion a la base de datos.']));
    }
}
