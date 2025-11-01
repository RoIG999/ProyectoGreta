<?php
// greta/modificarUsuario.php
include("conexion.php");
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

// ---- Guardar cambios (POST) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id      = (int)($_POST['id'] ?? 0);
  $nombre  = trim($_POST['nombre'] ?? '');
  $usuario = trim($_POST['usuario'] ?? '');
  $clave   = (string)($_POST['clave'] ?? ''); // vacío = no cambiar
  $rol     = trim($_POST['rol'] ?? '');
  $estado  = isset($_POST['estado']) ? 1 : 0;

  if ($id <= 0 || $nombre === '' || $usuario === '' || $rol === '') {
    header("Location: gestionUsuarios.php?error=faltan_campos"); exit;
  }

  // (Opcional) normalizar rol
  // Roles permitidos según la base de datos
$rolesPermitidos = ['admin','Supervisor','empleado'];
if (!in_array($rol, $rolesPermitidos, true)) {
  header("Location: gestionUsuarios.php?error=rol_invalido"); exit;
}

  // ¿Usuario duplicado en otro id?
  $sql = "SELECT id FROM usuarios WHERE usuario = ? AND id <> ? LIMIT 1";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('si', $usuario, $id);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($res && $res->num_rows > 0) {
    $stmt->close();
    header("Location: gestionUsuarios.php?error=usuario_duplicado"); exit;
  }
  $stmt->close();

  if ($clave !== '') {
    $hash = password_hash($clave, PASSWORD_DEFAULT);
    $sql = "UPDATE usuarios SET nombre=?, usuario=?, clave=?, rol=?, estado=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssii', $nombre, $usuario, $hash, $rol, $estado, $id);
  } else {
    $sql = "UPDATE usuarios SET nombre=?, usuario=?, rol=?, estado=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssii', $nombre, $usuario, $rol, $estado, $id);
  }

  if ($stmt->execute()) {
    $stmt->close();
    header("Location: gestionUsuarios.php?ok=editado"); exit;
  } else {
    $stmt->close();
    header("Location: gestionUsuarios.php?error=update"); exit;
  }
}

// ---- Cargar datos para el formulario (GET) ----
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || (int)$_GET['id'] <= 0) {
  die("<div class='alert alert-danger'>ID de usuario inválido o no especificado.</div>");
}
$id = (int)$_GET['id'];

$sql = "SELECT * FROM usuarios WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$usuario) {
  die("<div class='alert alert-danger'>Usuario no encontrado.</div>");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Modificar Usuario</title>
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


<div class="container mt-5">
  <h2>Modificar Usuario</h2>
  <form action="modificarUsuario.php" method="POST">
    <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">
    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre completo</label>
      <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
    </div>
    <div class="mb-3">
      <label for="usuario" class="form-label">Usuario</label>
      <input type="text" class="form-control" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
    </div>
    <div class="mb-3">
      <label for="clave" class="form-label">Contraseña</label>
      <input type="password" class="form-control" id="clave" name="clave" placeholder="Solo si desea cambiarla">
    </div>
    <div class="mb-3">
  <label for="rol" class="form-label">Rol</label>
  <select class="form-select" id="rol" name="rol" required>
    <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
    <option value="Supervisor" <?= $usuario['rol'] === 'Supervisor' ? 'selected' : '' ?>>Supervisor</option>
    <option value="empleado" <?= $usuario['rol'] === 'empleado' ? 'selected' : '' ?>>Empleado</option>
  </select>
</div>
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" id="estado" name="estado" <?= ((int)$usuario['estado'] === 1) ? 'checked' : '' ?>>
      <label class="form-check-label" for="estado">Activo</label>
    </div>
    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    <a href="gestionUsuarios.php" class="btn btn-secondary ms-2">⬅️ Volver</a>
  </form>
</div>

<footer class="text-white text-center p-3 mt-5" style="background-color:black;">
  <p>&copy; 2025 Grupo GRETA | Contacto: GIA.com</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
  die("<div class='alert alert-danger'>ID de usuario inválido o no especificado.</div>");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM usuarios WHERE id = $id LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  $usuario = $result->fetch_assoc();
} else {
  die("<div class='alert alert-danger'>Usuario no encontrado.</div>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Modificar Usuario</title>
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
            <i class="bi bi-person-circle fs-5"></i> ROOT
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">🔔 Notificaciones</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="index.php">🚪 Cerrar sesión</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <h2>Modificar Usuario</h2>
  <form action="modificarUsuario.php" method="POST">
    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre completo</label>
      <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $usuario['nombre'] ?>" required>
    </div>
    <div class="mb-3">
      <label for="usuario" class="form-label">Usuario</label>
      <input type="text" class="form-control" id="usuario" name="usuario" value="<?= $usuario['usuario'] ?>" required>
    </div>
    <div class="mb-3">
      <label for="clave" class="form-label">Contraseña</label>
      <input type="password" class="form-control" id="clave" name="clave" placeholder="Solo si desea cambiarla">
    </div>
    <div class="mb-3">
      <label for="rol" class="form-label">Rol</label>
      <select class="form-select" id="rol" name="rol" required>
        <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
        <option value="empleado" <?= $usuario['rol'] == 'empleado' ? 'selected' : '' ?>>Empleado</option>
      </select>
    </div>
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" id="estado" name="estado" <?= $usuario['estado'] ? 'checked' : '' ?>>
      <label class="form-check-label" for="estado">Activo</label>
    </div>
    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    <a href="gestionUsuarios.php" class="btn btn-secondary ms-2">⬅️ Volver</a>
  </form>
</div>

<footer class="text-white text-center p-3 mt-5" style="background-color:black;">
  <p>&copy; 2025 Grupo GRETA | Contacto: GIA.com</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
