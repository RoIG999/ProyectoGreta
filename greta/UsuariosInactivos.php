<?php
include("conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios Inactivos</title>
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
        <li class="nav-item"><a class="nav-link" href="gestionUsuarios.php">Usuarios</a></li>
      </ul>
      <ul class="navbar-nav d-flex flex-row gap-3">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
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

<?php if (isset($_GET['reactivado']) || (isset($_GET['ok']) && $_GET['ok']==='activado')): ?>
  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
    <div class="toast text-bg-success border-0 show" role="alert">
      <div class="d-flex">
        <div class="toast-body">✅ Usuario reactivado correctamente.</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Usuarios Inactivos</h2>
    <a href="gestionUsuarios.php" class="btn btn-secondary">⬅️ Volver</a>
  </div>

  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Rol</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "SELECT id, nombre, rol FROM usuarios WHERE estado = 0 ORDER BY id DESC";
      if ($stmt = $conn->prepare($sql)) {
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
          while ($row = $res->fetch_assoc()):
            $id     = (int)$row['id'];
            $nombre = htmlspecialchars($row['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
            $rol    = htmlspecialchars(ucfirst($row['rol'] ?? ''), ENT_QUOTES, 'UTF-8');
      ?>
      <tr>
        <td><?= $id ?></td>
        <td><?= $nombre ?></td>
        <td><?= $rol ?></td>
        <td>
          <a href="ActivarUsuario.php?id=<?= $id ?>" class="btn btn-success btn-sm"
             onclick="return confirm('¿Seguro que querés reactivar este usuario?')">✅ Activar</a>
        </td>
      </tr>
      <?php
          endwhile;
        } else {
          echo '<tr><td colspan="4" class="text-center text-muted">No hay usuarios inactivos.</td></tr>';
        }
        $stmt->close();
      } else {
        echo '<tr><td colspan="4" class="text-danger">Error al preparar la consulta.</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>

<footer class="text-white text-center p-3 mt-5" style="background-color:black;">
  <p>&copy; 2025 Grupo GRETA | Contacto: GIA.com</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
