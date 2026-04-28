<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Catálogo de Libros — OLP300</title>
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<!-- Topbar -->
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

<main class="page">

  <!-- Flash message -->
  <?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['tipo'] === 'success' ? 'success' : 'warning' ?>">
      <?= $flash['tipo'] === 'success' ? '✓' : '⚠' ?> <?= htmlspecialchars($flash['texto']) ?>
    </div>
  <?php endif; ?>

  <!-- Header -->
  <div class="page-header">
    <div>
      <h1>Libros</h1>
      <p class="page-subtitle">Pestaña para administrar los libros que se encuentran en el sistema.</p>
    </div>
    <a href="index.php?action=nuevo" class="btn btn-primary">＋ Nuevo Libro</a>
  </div>

  <div class="card">

    <!-- Toolbar: filtro + paginación -->
    <div class="card-body" style="padding-bottom: 0;">
      <form method="GET" action="index.php" class="toolbar">
        <input type="hidden" name="action" value="catalogo">
        <div class="toolbar__search">
          <input
            type="text"
            name="filtro"
            value="<?= htmlspecialchars($filtro) ?>"
            placeholder="Buscar...">
          <select name="campo">
            <option value="Titulo" <?= $campo === 'Titulo' ? 'selected' : '' ?>>Título</option>
            <option value="Autor"  <?= $campo === 'Autor'  ? 'selected' : '' ?>>Autor</option>
            <option value="ISBN"   <?= $campo === 'ISBN'   ? 'selected' : '' ?>>ISBN</option>
          </select>
          <button type="submit">🔍</button>
        </div>

        <!-- Paginación -->
        <div class="pagination">
          <?php
            $prevURL = "index.php?action=catalogo&pagina=" . ($pagina - 1) . "&filtro=" . urlencode($filtro) . "&campo=" . urlencode($campo);
            $nextURL = "index.php?action=catalogo&pagina=" . ($pagina + 1) . "&filtro=" . urlencode($filtro) . "&campo=" . urlencode($campo);
          ?>
          <a href="<?= $prevURL ?>" class="<?= $pagina <= 1 ? 'disabled' : '' ?>">‹</a>
          <span class="active"><?= $pagina ?></span>
          <a href="<?= $nextURL ?>" class="<?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">›</a>
        </div>
      </form>
    </div>

    <!-- Tabla -->
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Título</th>
            <th>Autor(es)</th>
            <th>ISBN</th>
            <th>Género</th>
            <th>Copias</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($libros)): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px;">
              No se encontraron libros.
            </td></tr>
          <?php else: ?>
            <?php foreach ($libros as $libro): ?>
            <tr>
              <td><?= htmlspecialchars($libro['Titulo']) ?></td>
              <td><?= htmlspecialchars($libro['Autor'])  ?></td>
              <td style="font-family:monospace;font-size:.83rem;"><?= htmlspecialchars($libro['ISBN']) ?></td>
              <td><span class="badge badge-<?= $libro['Estado'] ?>"><?= $libro['Estado'] ?></span></td>
              <td><?= (int)$libro['NumeroCopias'] ?></td>
              <td>
                <a href="index.php?action=detalles&isbn=<?= urlencode($libro['ISBN']) ?>"
                   class="btn btn-sm btn-primary" title="Ver detalles">Ver detalles</a>
                <a href="index.php?action=editar&isbn=<?= urlencode($libro['ISBN']) ?>"
                   class="btn-icon" title="Editar">✏️</a>
                <a href="index.php?action=eliminar&isbn=<?= urlencode($libro['ISBN']) ?>"
                   class="btn-icon danger" title="Eliminar">🗑</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div><!-- /.card -->

  <p style="text-align:right;margin-top:10px;font-size:.80rem;color:var(--text-muted);">
    Página <?= $pagina ?> de <?= $totalPaginas ?> — <?= $total ?> libro(s) encontrado(s).
  </p>

</main>
</body>
</html>
