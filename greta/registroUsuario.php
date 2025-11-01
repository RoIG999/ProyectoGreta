<?php
include("conexion.php");

// Obtener datos del usuario logueado
session_start();
$usuario_id = $_SESSION['usuario_id'] ?? 0;
$nombre_usuario = "ROOT";

if ($usuario_id > 0) {
    $sql_user = "SELECT nombre FROM usuarios WHERE id = ?";
    if ($stmt = $conn->prepare($sql_user)) {
        $stmt->bind_param('i', $usuario_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $user_data = $res->fetch_assoc();
            $nombre_usuario = htmlspecialchars($user_data['nombre'] ?? 'ROOT', ENT_QUOTES, 'UTF-8');
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Usuario</title>
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
        <li class="nav-item"><a class="nav-link active" href="gestionUsuarios.php">Usuarios</a></li>
      </ul>
      <ul class="navbar-nav d-flex flex-row gap-3">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" title="Usuario actual">
            <i class="bi bi-person-circle fs-5"></i> <?= $nombre_usuario ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>


<div class="container mt-5" style="max-width: 720px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Registrar Nuevo Usuario</h2>
    <a href="gestionUsuarios.php" class="btn btn-secondary">⬅️ Volver</a>
  </div>

  <!-- Feedback por querystring -->
  <?php
    $ok    = $_GET['ok']    ?? null;   // ej: ok=creado
    $error = $_GET['error'] ?? null;   // ej: error=usuario_duplicado | faltan_campos | insert
    if ($ok === 'creado') {
      echo '<div class="alert alert-success">✅ Usuario registrado correctamente.</div>';
    } elseif ($error) {
      $msg = 'Ocurrió un error.';
      if ($error === 'usuario_duplicado') $msg = 'El nombre de usuario ya existe. Elegí otro.';
      if ($error === 'faltan_campos')    $msg = 'Completá todos los campos obligatorios.';
      if ($error === 'rol_invalido')     $msg = 'Rol inválido.';
      if ($error === 'insert')           $msg = 'No se pudo registrar el usuario.';
      echo '<div class="alert alert-danger">❌ ' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</div>';
    }
  ?>

  <form action="GuardarUsuario.php" method="POST" onsubmit="return validar();">
    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre completo</label>
      <input type="text" class="form-control" id="nombre" name="nombre" required>
    </div>

    <div class="mb-3">
      <label for="usuario" class="form-label">Usuario</label>
      <input type="text" class="form-control" id="usuario" name="usuario" required>
      <div class="form-text">Debe ser único. Se verificará antes de crear.</div>
    </div>

    <div class="mb-3">
      <label for="clave" class="form-label">Contraseña</label>
      <div class="input-group">
        <input type="password" class="form-control" id="clave" name="clave" required minlength="6">
        <button type="button" class="btn btn-outline-secondary" onclick="togglePass()">
          <i class="bi bi-eye"></i>
        </button>
      </div>
      <div class="form-text">Mínimo 6 caracteres.</div>
    </div>

    <div class="mb-3">
  <label for="rol" class="form-label">Rol</label>
  <select class="form-select" id="rol" name="rol" required>
    <option value="" selected disabled>Seleccioná un rol</option>
    <option value="admin">Administrador</option>
    <option value="Supervisor">Supervisor</option>
    <option value="empleado">Empleado</option>
  </select>
</div>

    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" id="estado" name="estado" checked>
      <label class="form-check-label" for="estado">Activo</label>
    </div>

    <button type="submit" class="btn btn-success">Registrar</button>
  </form>
</div>

<footer class="text-white text-center p-3 mt-5" style="background-color:black;">
  <p>&copy; 2025 Grupo GRETA | Contacto: GIA.com</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function validar() {
    const nombre  = document.getElementById('nombre').value.trim();
    const usuario = document.getElementById('usuario').value.trim();
    const clave   = document.getElementById('clave').value;

    if (!nombre || !usuario || !clave) {
      alert('Completá todos los campos.');
      return false;
    }
    if (clave.length < 6) {
      alert('La contraseña debe tener al menos 6 caracteres.');
      return false;
    }
    return true;
  }
  function togglePass() {
    const input = document.getElementById('clave');
    input.type = input.type === 'password' ? 'text' : 'password';
  }
</script>
</body>
</html>
