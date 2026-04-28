<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Libro — OLP300</title>
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

  <?php if ($errorMsg === 'datos_requeridos'): ?>
    <div class="alert alert-danger">⚠ Datos requeridos. Completa todos los campos marcados.</div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">

      <div class="page-header" style="margin-bottom:24px;">
        <h1 style="font-size:1.6rem;">Edición de Libro</h1>
        <a href="index.php?action=catalogo" class="btn btn-secondary">← Volver</a>
      </div>

      <form method="POST" action="index.php?action=editar">
        <!-- ISBN fijo, no editable -->
        <input type="hidden" name="isbn" value="<?= htmlspecialchars($libro['ISBN']) ?>">

        <div class="form-grid">

          <div class="form-group full">
            <label for="titulo">Título del libro</label>
            <input type="text" id="titulo" name="titulo"
              value="<?= htmlspecialchars($libro['Titulo'] ?? '') ?>"
              class="<?= in_array('titulo', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group full">
            <label for="autor">Autor</label>
            <input type="text" id="autor" name="autor"
              value="<?= htmlspecialchars($libro['Autor'] ?? '') ?>"
              class="<?= in_array('autor', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group full">
            <label for="editorial">Editorial</label>
            <input type="text" id="editorial" name="editorial"
              value="<?= htmlspecialchars($libro['Editorial'] ?? '') ?>"
              class="<?= in_array('editorial', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group full">
            <label>ISBN <span style="color:var(--text-muted);font-size:.78rem;">(no editable)</span></label>
            <input type="text" value="<?= htmlspecialchars($libro['ISBN']) ?>" readonly>
          </div>

          <div class="form-group full">
            <label for="sinopsis">Sinopsis</label>
            <textarea id="sinopsis" name="sinopsis"><?= htmlspecialchars($libro['Sinopsis'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label for="anio">Año de publicación</label>
            <input type="number" id="anio" name="anio"
              min="1000" max="2099"
              value="<?= htmlspecialchars($libro['AnioPublicacion'] ?? '') ?>"
              class="<?= in_array('anio', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group">
            <label for="paginas">Número de páginas</label>
            <input type="number" id="paginas" name="paginas" min="1"
              value="<?= htmlspecialchars($libro['NumeroPaginas'] ?? '') ?>"
              class="<?= in_array('paginas', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group">
            <label for="precio">Precio</label>
            <input type="number" id="precio" name="precio" min="0" step="0.01"
              value="<?= htmlspecialchars($libro['Precio'] ?? '') ?>"
              class="<?= in_array('precio', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group">
            <label for="copias">Número de copias</label>
            <input type="number" id="copias" name="copias" min="1"
              value="<?= htmlspecialchars($libro['NumeroCopias'] ?? '') ?>"
              class="<?= in_array('copias', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group full">
            <label for="ubicacion">Ubicación en biblioteca</label>
            <input type="text" id="ubicacion" name="ubicacion"
              value="<?= htmlspecialchars($libro['Ubicacion'] ?? '') ?>"
              class="<?= in_array('ubicacion', $errores) ? 'error' : '' ?>">
          </div>

          <div class="form-group full">
            <label for="categoria">Categoría</label>
            <select id="categoria" name="categoria"
              class="<?= in_array('categoria', $errores) ? 'error' : '' ?>">
              <?php
                $categorias = ['Ficción','Ciencia Ficción','No Ficción','Infantil','Juvenil',
                               'Historia','Ciencia','Tecnología','Arte','Filosofía','Derecho','Medicina','Otro'];
                foreach ($categorias as $cat):
                  $sel = ($libro['Categoria'] ?? '') === $cat ? 'selected' : '';
              ?>
              <option value="<?= $cat ?>" <?= $sel ?>><?= $cat ?></option>
              <?php endforeach; ?>
            </select>
          </div>

        </div>

        <div style="margin-top:24px;">
          <button type="submit" class="btn btn-primary">💾 Guardar libro</button>
        </div>

      </form>
    </div>
  </div>

</main>
</body>
</html>
