<?php
// models/UsuarioModel.php
// Responsabilidad: acceso a datos de la tabla Usuarios

require_once __DIR__ . '/../config/db.php';

class UsuarioModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Busca un usuario por su nombre de usuario.
     * Retorna el registro completo o false si no existe.
     */
    public function findByUsuario(string $usuario): array|false {
        $stmt = $this->db->prepare(
            'SELECT Usuario, Contrasena, Nombre, Email
               FROM Usuarios
              WHERE Usuario = :usuario
              LIMIT 1'
        );
        $stmt->execute([':usuario' => $usuario]);
        return $stmt->fetch();
    }

    /**
     * Verifica credenciales: usuario existe y contraseña coincide con el hash.
     * Retorna el registro del usuario (sin contraseña) o false.
     */
    public function autenticar(string $usuario, string $contrasena): array|false {
        $row = $this->findByUsuario($usuario);

        if ($row === false) {
            return false;
        }

        if ($contrasena !== $row['Contrasena'] && !password_verify($contrasena, $row['Contrasena'])) {
    return false;
}

        // No exponer el hash al controlador
        unset($row['Contrasena']);
        return $row;
    }
}
