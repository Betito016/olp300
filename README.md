# OLP300 — Catálogo de Libros en Biblioteca
## Arquitectura de Software — UDEM | Evidencia 10

Aplicación MVC en **PHP + MySQL** para gestionar el catálogo de libros de una biblioteca universitaria.

---

## Stack
- **Lenguaje**: PHP 8.x
- **Base de datos**: MySQL 8 / MariaDB
- **Patrón**: MVC (sin frameworks)
- **Hosting sugerido**: Railway

---

## Estructura del proyecto

```
OLP300/
├── config/
│   ├── db.php          # Conexión PDO (lee variables de entorno)
│   └── schema.sql      # Script de creación de tablas + datos de prueba
├── models/
│   ├── UsuarioModel.php
│   └── LibroModel.php
├── controllers/
│   ├── AuthController.php
│   └── LibroController.php
├── views/
│   ├── auth/login.php
│   └── libros/{catalogo, nuevo, detalles, editar, eliminar}.php
├── public/css/style.css
├── index.php           # Front Controller / Router
├── nixpacks.toml       # Config de build para Railway
└── railway.json
```

---

## Despliegue en Railway (paso a paso)

### 1. Crear cuenta en Railway
Ve a [railway.app](https://railway.app) → Sign Up with GitHub.

### 2. Subir código a GitHub
```bash
git init
git add .
git commit -m "feat: OLP300 MVC inicial"
git remote add origin https://github.com/TU_USUARIO/olp300.git
git push -u origin main
```

### 3. Crear proyecto en Railway
1. En Railway → **New Project** → **Deploy from GitHub repo**
2. Selecciona el repo `olp300`
3. Railway detecta PHP automáticamente con nixpacks

### 4. Agregar base de datos MySQL
1. En tu proyecto Railway → **+ New** → **Database** → **MySQL**
2. Railway crea la BD y expone las variables de entorno automáticamente:
   - `MYSQL_HOST`, `MYSQL_PORT`, `MYSQLUSER`, `MYSQL_PASSWORD`, `MYSQL_DATABASE`

### 5. Configurar variables de entorno en el servicio PHP
En Railway → tu servicio PHP → **Variables** → agrega:

| Variable      | Valor                          |
|---------------|-------------------------------|
| `DB_HOST`     | `${{MySQL.MYSQL_HOST}}`       |
| `DB_PORT`     | `${{MySQL.MYSQL_PORT}}`       |
| `DB_NAME`     | `${{MySQL.MYSQL_DATABASE}}`   |
| `DB_USER`     | `${{MySQL.MYSQLUSER}}`        |
| `DB_PASSWORD` | `${{MySQL.MYSQL_PASSWORD}}`   |

### 6. Inicializar la base de datos
1. En Railway → tu servicio MySQL → **Query** (o conéctate con TablePlus/DBeaver)
2. Ejecuta el contenido de `config/schema.sql`

### 7. Obtener URL pública
Railway → tu servicio PHP → **Settings** → **Networking** → **Generate Domain**

¡Listo! El profe puede acceder desde su laptop sin instalar nada.

---

## Credenciales de prueba
| Usuario | Contraseña |
|---------|-----------|
| admin   | admin123  |

---

## Notas de implementación MVC
- **Modelo**: acceso exclusivo a BD, sin lógica de presentación
- **Vista**: solo HTML + PHP de presentación, sin consultas a BD
- **Controlador**: orquesta modelo y vista, maneja sesión y redirecciones
- **Router**: `index.php` despacha cada `?action=` al método correcto
