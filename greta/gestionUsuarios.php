<?php
include("conexion.php");

// Obtener datos del usuario logueado
session_start();
$usuario_id = $_SESSION['usuario_id'] ?? 0;
$nombre_usuario = "ROOT"; // Valor por defecto

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
  <title>Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .navbar-nav .nav-link { color: white !important; }
    .table-hover tbody tr:hover { cursor: pointer; background-color: #f5f5f5; }
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
        <li class="nav-item"><a class="nav-link active" href="Servicios(Dueña).php">Servicios</a></li>
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
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Usuarios</h2>
    <div class="d-flex gap-2">
      <a href="registroUsuario.php" class="btn btn-primary">➕ Agregar Usuario</a>
      <a href="UsuariosInactivos.php" class="btn btn-outline-dark">🕶️ Ver usuarios inactivos</a>
    </div>
  </div>

  <table class="table table-striped table-hover">
    <thead>
      <tr><th>ID</th><th>Nombre</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
    </thead>
    <tbody id="tabla-usuarios">
      <?php
      // Prepared statement para listar activos
      $sql = "SELECT id, nombre, rol, estado FROM usuarios WHERE estado = 1 ORDER BY id DESC";
      if ($stmt = $conn->prepare($sql)) {
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
          while ($row = $res->fetch_assoc()):
            $id     = (int)$row['id'];
            $nombre = htmlspecialchars($row['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
            $rol    = htmlspecialchars(ucfirst($row['rol'] ?? ''), ENT_QUOTES, 'UTF-8');
            $estadoTxt = ((int)$row['estado'] === 1) ? "Activo" : "Inactivo";
      ?>
      <tr>
        <td><?= $id ?></td>
        <td><?= $nombre ?></td>
        <td><?= $rol ?></td>
        <td><?= $estadoTxt ?></td>
        <td>
          <a href="modificarUsuario.php?id=<?= $id ?>" class="btn btn-sm btn-warning">✏️</a>
          <a href="EliminarUsuario.php?id=<?= $id ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que querés eliminar este usuario?')">🗑️</a>
        </td>
      </tr>
      <?php
          endwhile;
        } else {
          echo '<tr><td colspan="5" class="text-center text-muted">No hay usuarios activos.</td></tr>';
        }
        $stmt->close();
      } else {
        echo '<tr><td colspan="5" class="text-danger">Error al preparar la consulta.</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>

<footer class="text-white text-center p-3 mt-5" style="background-color:black;">
  <p>&copy; 2025 Grupo GRETA | Contacto: GIA.com</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Toasts -->
<div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;"></div>
<script>
  function mostrarToast(mensaje) {
    const container = document.getElementById("toast-container");
    const toast = document.createElement("div");
    toast.className = "toast align-items-center text-white bg-success border-0 show mb-2";
    toast.role = "alert";
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${mensaje}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
  }

  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has('agregado')   || urlParams.has('ok') && urlParams.get('ok')==='creado')     mostrarToast("✅ Usuario registrado correctamente.");
  if (urlParams.has('modificado') || urlParams.has('ok') && urlParams.get('ok')==='editado')    mostrarToast("✏️ Usuario modificado con éxito.");
  if (urlParams.has('eliminado')  || urlParams.has('ok') && urlParams.get('ok')==='desactivado')mostrarToast("🗑️ Usuario eliminado correctamente.");
  if (urlParams.has('reactivado') || urlParams.has('ok') && urlParams.get('ok')==='activado')   mostrarToast("✅ Usuario reactivado correctamente.");
</script>

</body>
</html>
