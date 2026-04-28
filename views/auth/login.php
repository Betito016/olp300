<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar Sesión — OLP300 Biblioteca</title>
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<div class="login-page">
  <div class="login-card">

    <!-- Logo / Branding -->
    <div class="login-card__logo">
      <div class="login-card__emblem">B</div>
      <div class="login-card__title">Librería de la Universidad Ducky</div>
      <div class="login-card__subtitle">Sistema OLP300 — Catálogo de Libros</div>
    </div>

    <!-- Alertas de error -->
    <?php if (!empty($_SESSION['auth_error'])): ?>
      <?php if ($_SESSION['auth_error'] === 'campos_vacios'): ?>
        <div class="alert alert-danger">⚠ Datos requeridos. Ingresa usuario y contraseña.</div>
      <?php else: ?>
        <div class="alert alert-danger">✕ Usuario o contraseña incorrectos.</div>
      <?php endif; unset($_SESSION['auth_error']); ?>
    <?php endif; ?>

    <h2>Iniciar sesión</h2>

    <form method="POST" action="index.php?action=login">

      <div class="form-group">
        <label for="usuario">Usuario (Matrícula / Nómina)</label>
        <input
          type="text"
          id="usuario"
          name="usuario"
          placeholder="Ingresa la información"
          value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
          autocomplete="username"
          required>
      </div>

      <div class="form-group">
        <label for="contrasena">Contraseña</label>
        <input
          type="password"
          id="contrasena"
          name="contrasena"
          placeholder="Ingresa la información"
          autocomplete="current-password"
          required>
        <small style="color:var(--text-muted);font-size:.78rem;margin-top:2px;">
          ¿Olvidaste tu contraseña?
        </small>
      </div>

      <button type="submit" class="btn btn-primary">Iniciar sesión</button>
    </form>

    <div class="login-exit">
      <a href="#" onclick="window.close(); return false;">✕ Salir de la aplicación</a>
    </div>

  </div>
</div>
</body>
</html>
