<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Nuevo Libro — OLP300</title>
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<header class="topbar">
  <div class="topbar__brand">
    <div class="topbar__logo">B</div>
    <span class="topbar__title">Consulta libros</span>
  </div>
  <nav class="topbar__nav">
    <span class="topbar__user">👤 <?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span>
    <a href="index.php?action=logout" class="btn-exit">✕ Salir</a>
  </nav>
</header>

<main class="page page-narrow" style="margin-top:28px;">

  <!-- Alertas -->
  <?php if ($errorMsg === 'datos_requeridos'): ?>
    <div class="alert alert-danger">⚠ Datos requeridos. Completa todos los campos marcados.</div>
  <?php elseif ($errorMsg === 'isbn_duplicado'): ?>
    <div class="alert alert-danger">✕ Libro ya existe. El ISBN ingresado ya está registrado en el catálogo.</div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">

      <div class="page-header" style="margin-bottom:24px;">
        <h1 style="font-size:1.6rem;">Nuevo Libro</h1>
        <a href="index.php?action=catalogo" class="btn btn-secondary">← Volver</a>
      </div>

      <form method="POST" action="index.php?action=nuevo">

        <div class="form-grid">

          <div class="form-group full">
            <label for="titulo">Título del libro</label>
            <input type="text" id="titulo" name="titulo"
              placeholder="Ingresa la información"
              value="<?= htmlspecialchars($datos['titulo'] ?? '') ?>"
              class="<?= in_array('titulo', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group full">
            <label for="autor">Autor</label>
            <input type="text" id="autor" name="autor"
              placeholder="Ingresa la información"
              value="<?= htmlspecialchars($datos['autor'] ?? '') ?>"
              class="<?= in_array('autor', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group full">
            <label for="editorial">Editorial</label>
            <input type="text" id="editorial" name="editorial"
              placeholder="Ingresa la información"
              value="<?= htmlspecialchars($datos['editorial'] ?? '') ?>"
              class="<?= in_array('editorial', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group full">
            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn"
              placeholder="Ingresa la información"
              value="<?= htmlspecialchars($datos['isbn'] ?? '') ?>"
              class="<?= in_array('isbn', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group full">
            <label for="sinopsis">Sinopsis</label>
            <textarea id="sinopsis" name="sinopsis"
              placeholder="Ingresa la información"><?= htmlspecialchars($datos['sinopsis'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label for="anio">Año de publicación</label>
            <input type="number" id="anio" name="anio"
              placeholder="Ej. 2024" min="1000" max="2099"
              value="<?= htmlspecialchars($datos['anio'] ?? '') ?>"
              class="<?= in_array('anio', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group">
            <label for="paginas">Número de páginas</label>
            <input type="number" id="paginas" name="paginas"
              placeholder="Ingresa la información" min="1"
              value="<?= htmlspecialchars($datos['paginas'] ?? '') ?>"
              class="<?= in_array('paginas', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group">
            <label for="precio">Precio</label>
            <input type="number" id="precio" name="precio"
              placeholder="0.00" min="0" step="0.01"
              value="<?= htmlspecialchars($datos['precio'] ?? '') ?>"
              class="<?= in_array('precio', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group">
            <label for="copias">Número de copias</label>
            <input type="number" id="copias" name="copias"
              placeholder="Ingresa la información" min="1"
              value="<?= htmlspecialchars($datos['copias'] ?? '') ?>"
              class="<?= in_array('copias', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group full">
            <label for="ubicacion">Ubicación en biblioteca</label>
            <input type="text" id="ubicacion" name="ubicacion"
              placeholder="Ej. Sección A – Pasillo 1"
              value="<?= htmlspecialchars($datos['ubicacion'] ?? '') ?>"
              class="<?= in_array('ubicacion', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group full">
            <label for="categoria">Categoría</label>
            <select id="categoria" name="categoria"
              class="<?= in_array('categoria', $errores) ? 'error' : '' ?>">
              <option value="">— Selecciona una categoría —</option>
              <?php
                $categorias = ['Ficción','Ciencia Ficción','No Ficción','Infantil','Juvenil',
                               'Historia','Ciencia','Tecnología','Arte','Filosofía','Derecho','Medicina','Otro'];
                foreach ($categorias as $cat):
                  $sel = ($datos['categoria'] ?? '') === $cat ? 'selected' : '';
              ?>
              <option value="<?= $cat ?>" <?= $sel ?>><?= $cat ?></option>
              <?php endforeach; ?>
            </select>
          </div>

        </div><!-- /.form-grid -->

        <div style="margin-top:24px;">
          <button type="submit" class="btn btn-primary">💾 Guardar libro</button>
        </div>

      </form>
    </div>
  </div>

</main>
</body>
</html>
