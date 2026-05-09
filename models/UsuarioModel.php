<?php
// models/UsuarioModel.php
// MODELO - Acceso a datos de la tabla Usuarios.
// Responsabilidad exclusiva: verificar credenciales contra la BD.
// No contiene logica de presentacion ni manejo de sesiones.
//
// Estados del sistema que dependen de este modelo:
//   E1 - Autenticacion (autenticar)
//
// Transiciones relacionadas:
//   T02 - Credenciales invalidas
//   T03 - Campos vacios
//   T04 - Login exitoso

require_once __DIR__ . '/../config/db.php';

class UsuarioModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // ----------------------------------------------------------
    // Buscar usuario por nombre de usuario
    // ----------------------------------------------------------
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

    // ----------------------------------------------------------
    // T02 / T04 - Verificar credenciales
    // Soporta bcrypt (password_verify) y texto plano como fallback
    // ----------------------------------------------------------
    public function autenticar(string $usuario, string $contrasena): array|false {
        $row = $this->findByUsuario($usuario);

        if ($row === false) {
            return false; // T02 - Usuario no existe
        }

        $hashValido   = password_verify($contrasena, $row['Contrasena']);
        $planoValido  = ($contrasena === $row['Contrasena']);

        if (!$hashValido && !$planoValido) {
            return false; // T02 - Contrasena incorrecta
        }

        // T04 - Credenciales validas, no exponer hash al controlador
        unset($row['Contrasena']);
        return $row;
    }
}
