<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Eliminar Libro — OLP300</title>
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

<!-- El catálogo se muestra de fondo; el modal encima -->
<main class="page" style="filter: blur(2px); pointer-events:none; opacity:.5; margin-top:28px;">
  <div class="page-header"><h1>Libros</h1></div>
  <div class="card" style="height:300px;"></div>
</main>

<!-- Modal de confirmación -->
<div class="modal-backdrop">
  <div class="modal">
    <div class="modal__icon">🗑️</div>
    <h2>¿Estás seguro(a)?</h2>
    <p>
      Estás a punto de eliminar el libro<br>
      <strong>"<?= htmlspecialchars($libro['Titulo']) ?>"</strong><br>
      Esta acción no se puede deshacer.
    </p>
    <div class="modal__actions">
      <form method="POST" action="index.php?action=eliminar">
        <input type="hidden" name="isbn" value="<?= htmlspecialchars($libro['ISBN']) ?>">
        <button type="submit" class="btn btn-danger">Eliminar</button>
      </form>
      <a href="index.php?action=catalogo" class="btn btn-secondary">Cancelar</a>
    </div>
  </div>
</div>

</body>
</html>
