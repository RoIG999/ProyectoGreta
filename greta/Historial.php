<?php
include("conexion.php");

// Opcional: fijar timezone
if (!ini_get('date.timezone')) {
  date_default_timezone_set('America/Argentina/Cordoba');
}

// --- Leer filtros (GET) ---
$usuarioId = isset($_GET['usuario']) && is_numeric($_GET['usuario']) ? (int)$_GET['usuario'] : null;
$fecha     = isset($_GET['fecha']) && $_GET['fecha'] !== '' ? $_GET['fecha'] : null;

// Validar formato de fecha (YYYY-MM-DD)
if ($fecha && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
  $fecha = null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial</title>
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
  <h2>Historial de Asistencia</h2>

  <?php if (isset($_GET['modificado'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      ✅ Asistencia modificada correctamente.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  <?php elseif (isset($_GET['eliminada'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      🗑️ Registro eliminado correctamente.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  <?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      ❌ Ocurrió un error (<?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?>).
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  <?php endif; ?>

  <!-- Filtro por usuario -->
  <form method="GET" class="mb-4">
    <label for="usuario" class="form-label">Seleccionar persona:</label>
    <select name="usuario" id="usuario" class="form-select">
      <option value="">-- Seleccionar --</option>
      <?php
      // Listado de usuarios activos para el combo (prepared)
      if ($stmtU = $conn->prepare("SELECT id, nombre FROM usuarios WHERE estado = 1 ORDER BY nombre ASC")) {
        $stmtU->execute();
        $resU = $stmtU->get_result();
        while ($u = $resU->fetch_assoc()) {
          $idU = (int)$u['id'];
          $nom = htmlspecialchars($u['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
          $sel = ($usuarioId !== null && $usuarioId === $idU) ? 'selected' : '';
          echo "<option value=\"$idU\" $sel>$nom</option>";
        }
        $stmtU->close();
      }
      ?>
    </select>

    <label for="fecha" class="form-label mt-3">Seleccionar fecha:</label>
    <input type="date" id="fecha" name="fecha" class="form-control" value="<?= $fecha ? htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8') : '' ?>">

    <div class="mt-2 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Buscar</button>
      <?php if ($usuarioId !== null || $fecha): ?>
        <a href="Historial.php" class="btn btn-outline-dark">🔁 Ver todo el historial</a>
      <?php endif; ?>
      <a href="index.php" class="btn btn-secondary">⬅️ Volver</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Nombre</th>
          <th>Asistencia</th>
          <th>Anotaciones</th>
          <th style="width:150px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Armar SQL con filtros dinámicos (prepared)
        $baseSQL = "SELECT a.id, a.fecha, u.nombre, a.asistencia, a.anotaciones
                    FROM asistencias a
                    JOIN usuarios u ON a.id_usuario = u.id";
        $conds   = [];
        $types   = '';
        $params  = [];

        if ($usuarioId !== null) {
          $conds[] = "a.id_usuario = ?";
          $types  .= 'i';
          $params[] = $usuarioId;
        }
        if ($fecha) {
          $conds[] = "a.fecha = ?";
          $types  .= 's';
          $params[] = $fecha;
        }

        $where = '';
        if (count($conds) > 0) {
          $where = ' WHERE ' . implode(' AND ', $conds);
        }
        $order = ' ORDER BY a.fecha DESC, u.nombre ASC';

        $sql = $baseSQL . $where . $order;

        if ($stmt = $conn->prepare($sql)) {
          if ($types !== '') {
            // bind dinámico
            $stmt->bind_param($types, ...$params);
          }
          $stmt->execute();
          $res = $stmt->get_result();

          if ($res && $res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
              $id      = (int)$row['id'];
              $nombre  = htmlspecialchars($row['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
              $asiste  = (int)$row['asistencia'] === 1 ? '✅' : '❌';
              $anot    = htmlspecialchars($row['anotaciones'] ?? '', ENT_QUOTES, 'UTF-8');
              $fechaDb = $row['fecha'] ?? '';
              $fechaOk = $fechaDb ? date("d-m-Y", strtotime($fechaDb)) : '';

              echo "<tr>
                      <td>{$fechaOk}</td>
                      <td>{$nombre}</td>
                      <td>{$asiste}</td>
                      <td>{$anot}</td>
                      <td class=\"text-nowrap\">
                        <a href=\"ModificarAsistencia.php?id={$id}\" class=\"btn btn-warning btn-sm\">✏️</a>
                        <a href=\"EliminarAsistencia.php?id={$id}\" class=\"btn btn-danger btn-sm\" onclick=\"return confirm('¿Seguro que querés eliminar este registro?')\">🗑️</a>
                      </td>
                    </tr>";
            }
          } else {
            echo '<tr><td colspan="5" class="text-center text-muted">No se encontraron registros.</td></tr>';
          }
          $stmt->close();
        } else {
          echo '<tr><td colspan="5" class="text-danger">Error al preparar la consulta.</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>

  <button class="btn btn-secondary" onclick="window.print()">Imprimir historial</button>
</div>

<footer class="text-white text-center p-3 mt-5" style="background-color:black;">
  <p>&copy; 2025 Grupo GRETA | Contacto: GIA.com</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
