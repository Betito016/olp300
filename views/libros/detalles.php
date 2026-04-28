<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($libro['Titulo']) ?> — OLP300</title>
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

  <div class="card">

    <!-- Hero -->
    <div class="detail-hero">
      <span class="badge badge-<?= $libro['Estado'] ?>"><?= $libro['Estado'] ?></span>
      <h1><?= htmlspecialchars($libro['Titulo']) ?></h1>
      <div class="author"><?= htmlspecialchars($libro['Autor']) ?></div>
      <div class="meta"><?= htmlspecialchars($libro['AnioPublicacion']) ?> &nbsp;·&nbsp; <?= htmlspecialchars($libro['Editorial']) ?></div>
    </div>

    <div class="detail-body">

      <div style="margin-bottom:18px;">
        <a href="index.php?action=catalogo" class="btn btn-secondary">← Volver</a>
      </div>

      <!-- Sinopsis -->
      <div class="detail-section">
        <h3>Sinopsis</h3>
        <p style="line-height:1.7;color:var(--text-muted);font-size:.93rem;">
          <?= nl2br(htmlspecialchars($libro['Sinopsis'] ?? 'Sin sinopsis disponible.')) ?>
        </p>
      </div>

      <!-- Info adicional -->
      <div class="detail-section">
        <h3>Información adicional</h3>
        <div class="detail-grid">
          <div class="detail-item">
            <label>Editorial</label>
            <span><?= htmlspecialchars($libro['Editorial']) ?></span>
          </div>
          <div class="detail-item">
            <label>Año publicación</label>
            <span><?= htmlspecialchars($libro['AnioPublicacion']) ?></span>
          </div>
          <div class="detail-item">
            <label>Número de páginas</label>
            <span><?= (int)$libro['NumeroPaginas'] ?></span>
          </div>
          <div class="detail-item">
            <label>Precio</label>
            <span>$<?= number_format((float)$libro['Precio'], 2) ?></span>
          </div>
          <div class="detail-item">
            <label>ISBN</label>
            <span style="font-family:monospace;font-size:.85rem;"><?= htmlspecialchars($libro['ISBN']) ?></span>
          </div>
          <div class="detail-item">
            <label>Categoría</label>
            <span><?= htmlspecialchars($libro['Categoria']) ?></span>
          </div>
          <div class="detail-item" style="grid-column: 1 / -1;">
            <label>Ubicación en biblioteca</label>
            <span><?= htmlspecialchars($libro['Ubicacion']) ?></span>
          </div>
        </div>
      </div>

      <!-- Disponibilidad -->
      <div class="detail-section">
        <h3>Disponibilidad</h3>
        <div class="detail-grid">
          <div class="detail-item">
            <label>Total de libros</label>
            <span><?= (int)$libro['NumeroCopias'] ?> copias</span>
          </div>
          <div class="detail-item">
            <label>Estado actual</label>
            <span><span class="badge badge-<?= $libro['Estado'] ?>"><?= $libro['Estado'] ?></span></span>
          </div>
          <div class="detail-item">
            <label>Fecha de registro</label>
            <span><?= htmlspecialchars($libro['FechaRegistro']) ?></span>
          </div>
        </div>
      </div>

    </div>
  </div>

</main>
</body>
</html>
