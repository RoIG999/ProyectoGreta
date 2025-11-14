<?php
include("conexion.php");

// Obtener datos del usuario logueado
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php'); 
  exit;
}

// Normalizar rol: minúsculas y sin tildes
$rol = $_SESSION['usuario_rol'] ?? '';
$rol_normalizado = mb_strtolower($rol, 'UTF-8');
$rol_normalizado = strtr($rol_normalizado, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n']);

// Solo dueña, admin o supervisor (en minúsculas)
if (!in_array($rol_normalizado, ['duena', 'dueña', 'supervisor', 'admin'])) {
  header('Location: login.php?e=perm'); 
  exit;
}

$nombre_usuario = htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario', ENT_QUOTES, 'UTF-8');

// Obtener estadísticas de usuarios
$sql_activos = "SELECT COUNT(*) as total FROM usuarios WHERE estado = 1";
$result_activos = $conn->query($sql_activos);
$total_activos = $result_activos ? $result_activos->fetch_assoc()['total'] : 0;

$sql_inactivos = "SELECT COUNT(*) as total FROM usuarios WHERE estado = 0";
$result_inactivos = $conn->query($sql_inactivos);
$total_inactivos = $result_inactivos ? $result_inactivos->fetch_assoc()['total'] : 0;

$sql_empleados = "SELECT COUNT(*) as total FROM usuarios WHERE estado = 1 AND rol = 'empleado'";
$result_empleados = $conn->query($sql_empleados);
$total_empleados = $result_empleados ? $result_empleados->fetch_assoc()['total'] : 0;

$sql_supervisores = "SELECT COUNT(*) as total FROM usuarios WHERE estado = 1 AND rol = 'supervisor'";
$result_supervisores = $conn->query($sql_supervisores);
$total_supervisores = $result_supervisores ? $result_supervisores->fetch_assoc()['total'] : 0;

$sql_duenas = "SELECT COUNT(*) as total FROM usuarios WHERE estado = 1 AND (rol = 'dueña' OR rol = 'duena' OR rol = 'admin')";
$result_duenas = $conn->query($sql_duenas);
$total_duenas = $result_duenas ? $result_duenas->fetch_assoc()['total'] : 0;

// Calcular porcentajes
$total_usuarios = $total_activos + $total_inactivos;
$porcentaje_activos = $total_usuarios > 0 ? round(($total_activos / $total_usuarios) * 100) : 0;
$porcentaje_inactivos = $total_usuarios > 0 ? round(($total_inactivos / $total_usuarios) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios - GRETA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-dark: #000000ff;
      --primary-main: #2e3033ff;
      --primary-light: #718096;
      --accent-pastel: #FED7D7;
      --accent-soft: #FEB2B2;
      --accent-medium: #FC8181;
      --background-light: #FAF5F0;
      --background-white: #FFFFFF;
      --text-dark: #2D3748;
      --text-medium: #4A5568;
      --text-light: #718096;
      --border-light: #E2E8F0;
      --success: #48BB78;
      --warning: #ED8936;
      --info: #4299E1;
      --purple: #9F7AEA;
    }
    
    body { 
      background: var(--background-light);
      color: var(--text-dark);
      font-family: 'Montserrat', sans-serif;
      line-height: 1.6;
    }
    
    .navbar-brand {
      font-weight: 600;
      letter-spacing: 0.5px;
    }
    
    .bg-greta { 
      background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-main) 100%);
    }
    
    /* Tarjetas de estadísticas */
    .stat-card {
      border: none;
      border-radius: 16px;
      background: var(--background-white);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
      transition: all 0.3s ease;
      border-left: 4px solid var(--accent-medium);
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card i {
      font-size: 2rem;
      background: linear-gradient(135deg, var(--accent-pastel) 0%, var(--accent-soft) 100%);
      padding: 15px;
      border-radius: 12px;
      color: var(--primary-main);
    }
    
    .stat-card .card-title {
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin-bottom: 0.25rem;
    }
    
    .stat-card .card-subtitle {
      font-size: 0.875rem;
      color: var(--text-light);
      font-weight: 500;
    }
    
    .stat-card .small {
      font-size: 0.75rem;
      color: var(--primary-light);
    }
    
    /* Sidebar */
    .sidebar {
      background: var(--background-white);
      border-right: 1px solid var(--border-light);
      height: 100vh;
      position: fixed;
      top: 56px;
      left: 0;
      width: 280px;
      padding: 20px 0;
      z-index: 1000;
      overflow-y: auto;
      box-shadow: 2px 0 10px rgba(0,0,0,0.05);
    }
    
    .sidebar .nav-link {
      color: var(--text-medium);
      padding: 14px 24px;
      border-left: 4px solid transparent;
      transition: all 0.3s ease;
      font-weight: 500;
      margin: 4px 12px;
      border-radius: 8px;
      cursor: pointer;
    }
    
    .sidebar .nav-link:hover {
      background-color: var(--accent-pastel);
      color: var(--primary-main);
      border-left: 4px solid var(--accent-medium);
    }
    
    .sidebar .nav-link.active {
      background-color: var(--accent-pastel);
      color: var(--primary-dark);
      font-weight: 600;
      border-left: 4px solid var(--accent-medium);
    }
    
    .sidebar .nav-link i {
      width: 20px;
      margin-right: 12px;
    }
    
    .main-content {
      margin-left: 280px;
      padding: 30px;
      width: calc(100% - 280px);
      min-height: calc(100vh - 76px);
    }
    
    /* Tarjetas de contenido */
    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
      background: var(--background-white);
    }
    
    .card-header {
      background: var(--background-white);
      border-bottom: 1px solid var(--border-light);
      padding: 20px 24px;
      border-radius: 16px 16px 0 0 !important;
    }
    
    .card-header h5 {
      font-weight: 600;
      color: var(--primary-dark);
      margin: 0;
    }
    
    /* Botones */
    .btn-primary {
      background: linear-gradient(135deg, var(--primary-main) 0%, var(--primary-dark) 100%);
      border: none;
      border-radius: 10px;
      font-weight: 500;
      padding: 10px 20px;
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(74, 85, 104, 0.3);
    }
    
    .btn-outline-primary {
      border: 2px solid var(--primary-main);
      color: var(--primary-main);
      border-radius: 10px;
      font-weight: 500;
      padding: 8px 18px;
      transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
      background: var(--primary-main);
      border-color: var(--primary-main);
      transform: translateY(-2px);
    }
    
    /* Tablas */
    .table-container {
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
    }
    
    .table {
      margin-bottom: 0;
    }
    
    .table thead th {
      background-color: var(--primary-main);
      color: white;
      font-weight: 600;
      border: none;
      padding: 16px 12px;
    }
    
    .table tbody tr {
      transition: all 0.3s ease;
    }
    
    .table tbody tr:hover {
      background-color: var(--accent-pastel);
      cursor: pointer;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }
    
    .table tbody td {
      padding: 14px 12px;
      border-bottom: 1px solid var(--border-light);
      vertical-align: middle;
    }
    
    .badge {
      font-weight: 500;
      padding: 6px 12px;
      border-radius: 20px;
    }
    
    .badge-success {
      background-color: var(--success);
    }
    
    .badge-warning {
      background-color: var(--warning);
    }
    
    .badge-secondary {
      background-color: var(--text-light);
    }
    
    .badge-danger {
      background-color: #E53E3E;
    }
    
    .bg-purple {
    background-color: var(--purple) !important;
}
    
    /* Progress bar */
    .progress {
      height: 8px;
      border-radius: 10px;
      background-color: var(--border-light);
    }
    
    .progress-bar {
      border-radius: 10px;
    }
    
    /* Footer */
    footer {
      background: var(--background-white);
      border-top: 1px solid var(--border-light);
      color: var(--text-light);
      font-size: 0.875rem;
    }

    /* Encabezado principal - CAMBIADO A COLORES EXISTENTES */
    .page-header {
      background: linear-gradient(135deg, var(--primary-main) 0%, var(--primary-dark) 100%);
      color: white;
      border-radius: 20px;
      padding: 30px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .page-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 200%;
      background: rgba(255, 255, 255, 0.1);
      transform: rotate(45deg);
    }
    
    /* Responsive */
    @media (max-width: 992px) {
      .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        width: 280px;
      }
      
      .sidebar.show {
        transform: translateX(0);
      }
      
      .main-content {
        margin-left: 0;
        width: 100%;
        padding: 20px;
      }
    }
    
    @media (max-width: 768px) {
      .main-content {
        padding: 15px;
      }
      
      .stat-card .card-title {
        font-size: 1.75rem;
      }
      
      .page-header {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <!-- Nav -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-greta fixed-top">
    <div class="container-fluid">
      <button class="btn btn-sm btn-outline-light me-2 d-lg-none" type="button" id="sidebarToggle">
        <i class="bi bi-list"></i>
      </button>
      <a class="navbar-brand" href="Panel-dueña.php">
        <img src="img/LogoGreta.jpeg" alt="GRETA" style="height: 50px; width: auto; margin-right: 12px;">
        GRETA · Gestión de Usuarios
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div id="navBar" class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="Panel-dueña.php">
              <i class="bi bi-house me-2"></i>Inicio
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="gestionUsuarios.php">
              <i class="bi bi-people me-2"></i>Usuarios
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Historial.php">
              <i class="bi bi-calendar-check me-2"></i>Asistencias
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Servicios(Dueña).php">
              <i class="bi bi-scissors me-2"></i>Servicios
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="gestion-turnos-dueña.php">
              <i class="bi bi-calendar-check me-2"></i>Turnos
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Panel-dueña.php?seccion=reportes">
              <i class="bi bi-graph-up me-2"></i>Reportes
            </a>
          </li>
        </ul>
        <div class="d-flex align-items-center">
          <span class="navbar-text text-white me-3">Hola, <?= $nombre_usuario; ?></span>
          <a class="btn btn-outline-light btn-sm" href="logout.php">
            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Sidebar -->
  <div class="sidebar d-none d-lg-block">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link" href="Panel-dueña.php">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="gestionUsuarios.php">
          <i class="bi bi-people me-2"></i> Usuarios
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="Historial.php">
          <i class="bi bi-calendar-check me-2"></i> Asistencias
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="Servicios(Dueña).php">
          <i class="bi bi-scissors me-2"></i> Servicios
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="gestion-turnos-dueña.php">
          <i class="bi bi-calendar-check me-2"></i> Gestión de Turnos
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="calendario.php">
          <i class="bi bi-calendar-week me-2"></i> Calendario
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="Panel-dueña.php?seccion=reportes">
          <i class="bi bi-graph-up me-2"></i> Reportes
        </a>
      </li>
    </ul>
  </div>

  <!-- Contenido principal -->
  <div class="main-content" style="margin-top: 76px;">
    <div class="container-fluid">
      <!-- Encabezado - CAMBIADO A COLORES EXISTENTES -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="page-header">
            <div class="row align-items-center">
              <div class="col-md-8">
                <h1 class="h2 mb-2 fw-bold text-white">
                  <i class="bi bi-people me-2"></i>Gestión de Usuarios
                </h1>
                <p class="text-white mb-0 opacity-75">Administra los usuarios del sistema</p>
              </div>
              <div class="col-md-4 text-end">
                <i class="bi bi-people display-4 opacity-25"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Estadísticas rápidas -->
      <div class="row mb-4">
        <!-- Tarjeta Dueña -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1">Dueña/Admin</h6>
                  <h3 class="card-title mb-0"><?= $total_duenas ?></h3>
                  <p class="small mb-0">Administración del sistema</p>
                  <?php if ($total_activos > 0): ?>
                    <div class="progress mt-2">
                      <div class="progress-bar bg-purple" role="progressbar" 
                           style="width: <?= round(($total_duenas / $total_activos) * 100) ?>%" 
                           aria-valuenow="<?= round(($total_duenas / $total_activos) * 100) ?>" 
                           aria-valuemin="0" 
                           aria-valuemax="100">
                      </div>
                    </div>
                    <small class="text-muted"><?= round(($total_duenas / $total_activos) * 100) ?>% de activos</small>
                  <?php else: ?>
                    <small class="text-muted">Sin datos</small>
                  <?php endif; ?>
                </div>
                <i class="bi bi-person-fill-gear" style="color: var(--purple);"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta Empleados -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1">Empleados</h6>
                  <h3 class="card-title mb-0"><?= $total_empleados ?></h3>
                  <p class="small mb-0">Personal activo</p>
                  <?php if ($total_activos > 0): ?>
                    <div class="progress mt-2">
                      <div class="progress-bar bg-primary" role="progressbar" 
                           style="width: <?= round(($total_empleados / $total_activos) * 100) ?>%" 
                           aria-valuenow="<?= round(($total_empleados / $total_activos) * 100) ?>" 
                           aria-valuemin="0" 
                           aria-valuemax="100">
                      </div>
                    </div>
                    <small class="text-muted"><?= round(($total_empleados / $total_activos) * 100) ?>% de activos</small>
                  <?php else: ?>
                    <small class="text-muted">Sin datos</small>
                  <?php endif; ?>
                </div>
                <i class="bi bi-person-badge"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta Supervisores -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1">Supervisores</h6>
                  <h3 class="card-title mb-0"><?= $total_supervisores ?></h3>
                  <p class="small mb-0">Personal administrativo</p>
                  <?php if ($total_activos > 0): ?>
                    <div class="progress mt-2">
                      <div class="progress-bar bg-info" role="progressbar" 
                           style="width: <?= round(($total_supervisores / $total_activos) * 100) ?>%" 
                           aria-valuenow="<?= round(($total_supervisores / $total_activos) * 100) ?>" 
                           aria-valuemin="0" 
                           aria-valuemax="100">
                      </div>
                    </div>
                    <small class="text-muted"><?= round(($total_supervisores / $total_activos) * 100) ?>% de activos</small>
                  <?php else: ?>
                    <small class="text-muted">Sin datos</small>
                  <?php endif; ?>
                </div>
                <i class="bi bi-person-gear"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta Estado General -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1">Usuarios Activos</h6>
                  <h3 class="card-title mb-0"><?= $total_activos ?></h3>
                  <p class="small mb-0">Total en el sistema</p>
                  <div class="progress mt-2">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: <?= $porcentaje_activos ?>%" 
                         aria-valuenow="<?= $porcentaje_activos ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                  </div>
                  <small class="text-muted"><?= $porcentaje_activos ?>% del total</small>
                </div>
                <i class="bi bi-person-check"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Botones de acción -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-0">
                <i class="bi bi-list-check me-2"></i>Usuarios Activos
              </h5>
            </div>
            <div class="d-flex gap-2">
              <a href="registroUsuario.php" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i> Agregar Usuario
              </a>
              <a href="UsuariosInactivos.php" class="btn btn-outline-primary">
                <i class="bi bi-person-x me-2"></i> Ver usuarios inactivos
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de usuarios -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">
                <i class="bi bi-table me-2"></i>Lista de Usuarios
              </h5>
            </div>
            <div class="card-body p-0">
              <div class="table-container">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th><i class="bi bi-hash me-1"></i>ID</th>
                      <th><i class="bi bi-person me-1"></i>Nombre</th>
                      <th><i class="bi bi-person-badge me-1"></i>Rol</th>
                      <th><i class="bi bi-circle-fill me-1"></i>Estado</th>
                      <th><i class="bi bi-gear me-1"></i>Acciones</th>
                    </tr>
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
                          $estadoBadge = ((int)$row['estado'] === 1) ? "success" : "secondary";
                    ?>
                    <tr>
                      <td><?= $id ?></td>
                      <td><?= $nombre ?></td>
                      <td>
                        <span class="badge 
                          <?= strtolower($rol) === 'empleado' ? 'bg-primary' : 
                             (strtolower($rol) === 'supervisor' ? 'bg-info' : 
                             (strtolower($rol) === 'dueña' || strtolower($rol) === 'duena' || strtolower($rol) === 'admin' ? 'bg-purple' : 'bg-secondary')) ?>">
                          <?= $rol ?>
                        </span>
                      </td>
                      <td>
                        <span class="badge badge-<?= $estadoBadge ?>">
                          <i class="bi bi-circle-fill me-1"></i><?= $estadoTxt ?>
                        </span>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="modificarUsuario.php?id=<?= $id ?>" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Editar
                          </a>
                          <a href="EliminarUsuario.php?id=<?= $id ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que querés eliminar este usuario?')">
                            <i class="bi bi-trash"></i> Eliminar
                          </a>
                        </div>
                      </td>
                    </tr>
                    <?php
                        endwhile;
                      } else {
                        echo '<tr><td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-people display-4 text-muted mb-3"></i><br>
                                No hay usuarios activos.
                              </td></tr>';
                      }
                      $stmt->close();
                    } else {
                      echo '<tr><td colspan="5" class="text-danger text-center py-4">
                              <i class="bi bi-exclamation-triangle me-2"></i>Error al preparar la consulta.
                            </td></tr>';
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="text-center py-4 mt-4">
    <small>© <?= date('Y'); ?> GRETA Estética · Todos los derechos reservados</small>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Toggle sidebar en vista móvil
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      document.querySelector('.sidebar').classList.toggle('show');
    });

    // Toasts para notificaciones
    const urlParams = new URLSearchParams(window.location.search);
    
    function mostrarToast(mensaje, tipo = 'success') {
      const toastContainer = document.createElement('div');
      toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
      toastContainer.style.zIndex = '9999';
      
      const icon = tipo === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
      
      const toast = document.createElement('div');
      toast.className = `toast align-items-center text-white bg-${tipo} border-0 show`;
      toast.role = 'alert';
      
      toast.innerHTML = `
        <div class="d-flex">
          <div class="toast-body">
            <i class="bi ${icon} me-2"></i>${mensaje}
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      `;
      
      toastContainer.appendChild(toast);
      document.body.appendChild(toastContainer);
      
      setTimeout(() => {
        toastContainer.remove();
      }, 3000);
    }

    // Mostrar toasts según parámetros de URL
    if (urlParams.has('agregado') || (urlParams.has('ok') && urlParams.get('ok') === 'creado')) {
      mostrarToast("Usuario registrado correctamente.");
    }
    if (urlParams.has('modificado') || (urlParams.has('ok') && urlParams.get('ok') === 'editado')) {
      mostrarToast("Usuario modificado con éxito.");
    }
    if (urlParams.has('eliminado') || (urlParams.has('ok') && urlParams.get('ok') === 'desactivado')) {
      mostrarToast("Usuario eliminado correctamente.");
    }
    if (urlParams.has('reactivado') || (urlParams.has('ok') && urlParams.get('ok') === 'activado')) {
      mostrarToast("Usuario reactivado correctamente.");
    }
  </script>
</body>
</html>