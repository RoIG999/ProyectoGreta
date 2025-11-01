<?php
// nuevoServicio.php
include("conexion.php");

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre'] ?? '');
  $descripcion = trim($_POST['descripcion'] ?? '');
  $precio = (float)($_POST['precio'] ?? 0);
  $duracion = (int)($_POST['duracion'] ?? 0);
  $estado = isset($_POST['estado']) ? 1 : 0;
  $imagen = trim($_POST['imagen'] ?? 'img/servicio-default.jpg');

  // Validaciones
  if (empty($nombre) || empty($descripcion) || $precio <= 0 || $duracion <= 0) {
    header("Location: nuevoServicio.php?error=campos_incompletos");
    exit;
  }

  // Insertar en la base de datos
  $sql = "INSERT INTO servicios (nombre, descripcion, precio, duracion, estado, imagen) VALUES (?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('ssdiis', $nombre, $descripcion, $precio, $duracion, $estado, $imagen);

  if ($stmt->execute()) {
    $stmt->close();
    header("Location: servicios.php?ok=creado");
    exit;
  } else {
    $stmt->close();
    header("Location: nuevoServicio.php?error=insertar");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nuevo Servicio - GRETA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .navbar-nav .nav-link { color: white !important; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg" style="background-color: black;">
  <div class="container-fluid align-items-center">
    <img src="img/LogoGreta.jpeg" alt="LogoGreta" style="width:80px;height:80px;">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Asistencia</a></li>
        <li class="nav-item"><a class="nav-link" href="gestionUsuarios.php">Usuarios</a></li>
        <li class="nav-item"><a class="nav-link active" href="servicios.php">Servicios</a></li>
      </ul>
      <ul class="navbar-nav d-flex flex-row gap-3">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" title="Usuario actual">
            <i class="bi bi-person-circle fs-5"></i> <?= $_SESSION['usuario_nombre'] ?? 'Admin' ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">🔔 Notificaciones</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="logout.php">🚪 Cerrar sesión</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5" style="max-width: 720px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Nuevo Servicio</h2>
    <a href="servicios.php" class="btn btn-secondary">⬅️ Volver</a>
  </div>

  <!-- Mostrar errores -->
  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
      <?php
      switch ($_GET['error']) {
        case 'campos_incompletos': echo '❌ Completá todos los campos obligatorios.'; break;
        case 'insertar': echo '❌ Error al guardar el servicio.'; break;
        default: echo '❌ Ocurrió un error.';
      }
      ?>
    </div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre del servicio *</label>
      <input type="text" class="form-control" id="nombre" name="nombre" required>
    </div>

    <div class="mb-3">
      <label for="descripcion" class="form-label">Descripción *</label>
      <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="precio" class="form-label">Precio ($) *</label>
        <input type="number" class="form-control" id="precio" name="precio" min="0" step="0.01" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="duracion" class="form-label">Duración (minutos) *</label>
        <input type="number" class="form-control" id="duracion" name="duracion" min="1" required>
      </div>
    </div>

    <div class="mb-3">
      <label for="imagen" class="form-label">Imagen (URL)</label>
      <input type="text" class="form-control" id="imagen" name="imagen" placeholder="ej: img/bronceado.jpg">
      <div class="form-text">Dejar vacío para usar imagen predeterminada.</div>
    </div>

    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" id="estado" name="estado" checked>
      <label class="form-check-label" for="estado">Servicio activo</label>
    </div>

    <button type="submit" class="btn btn-success">Guardar Servicio</button>
    <a href="servicios.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>

<footer class="text-white text-center p-3 mt-5" style="background-color:black;">
  <p>&copy; 2025 Grupo GRETA | Contacto: GIA.com</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>