-- ============================================================
-- OLP300 – Catálogo de Libros en Biblioteca
-- Script de inicialización de base de datos
-- ============================================================

CREATE DATABASE IF NOT EXISTS olp300
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE olp300;

-- ------------------------------------------------------------
-- Tabla Usuarios
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Usuarios (
    Usuario     VARCHAR(50)  PRIMARY KEY,
    Contrasena  VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt',
    Nombre      VARCHAR(50)  NOT NULL UNIQUE,
    Email       VARCHAR(150) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuario de prueba: admin / admin123
INSERT IGNORE INTO Usuarios (Usuario, Contrasena, Nombre, Email)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin123 (bcrypt)
    'Administrador',
    'admin@biblioteca.udem.mx'
);

-- ------------------------------------------------------------
-- Tabla Libros
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Libros (
    ISBN            VARCHAR(20)     PRIMARY KEY,
    Titulo          VARCHAR(255)    NOT NULL,
    Autor           VARCHAR(255)    NOT NULL,
    Editorial       VARCHAR(150)    NOT NULL,
    Sinopsis        TEXT,
    AnioPublicacion YEAR            NOT NULL,
    NumeroPaginas   INT             NOT NULL,
    Precio          DECIMAL(10,2)   NOT NULL,
    Ubicacion       VARCHAR(100)    NOT NULL,
    NumeroCopias    INT             NOT NULL DEFAULT 1,
    Categoria       VARCHAR(100)    NOT NULL,
    FechaRegistro   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Estado          ENUM('disponible','prestado','mantenimiento','perdido')
                                    NOT NULL DEFAULT 'disponible',
    INDEX idx_titulo (Titulo(50)),
    INDEX idx_autor  (Autor(50))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de ejemplo
INSERT IGNORE INTO Libros
    (ISBN, Titulo, Autor, Editorial, Sinopsis, AnioPublicacion, NumeroPaginas, Precio, Ubicacion, NumeroCopias, Categoria)
VALUES
    ('978-0-06-112008-4','To Kill a Mockingbird','Harper Lee','J.B. Lippincott','Una niña crece en Alabama en los años 30 mientras su padre defiende a un hombre negro acusado injustamente.',1960,281,12.99,'Sección A – Pasillo 1',3,'Ficción'),
    ('978-0-7432-7356-5','The Great Gatsby','F. Scott Fitzgerald','Scribner','La historia del misterioso millonario Jay Gatsby y su obsesión por Daisy Buchanan en los locos años veinte.',1925,180,10.99,'Sección A – Pasillo 2',2,'Ficción'),
    ('978-0-14-028329-7','1984','George Orwell','Secker & Warburg','Una distopía que narra la vida bajo un régimen totalitario de vigilancia omnipresente.',1949,328,11.50,'Sección B – Pasillo 1',4,'Ciencia Ficción'),
    ('978-0-06-093546-9','To Kill a Mockingbird (Ed. Aniversario)','Harper Lee','Harper Perennial','Edición especial con prólogo conmemorativo del 50 aniversario.',2010,323,15.99,'Sección A – Pasillo 1',1,'Ficción'),
    ('978-0-14-303943-3','El Principito','Antoine de Saint-Exupéry','Reynal & Hitchcock','Un piloto perdido en el desierto conoce a un pequeño príncipe que le cuenta sus viajes por el universo.',1943,96,8.99,'Sección C – Pasillo 3',5,'Infantil');
