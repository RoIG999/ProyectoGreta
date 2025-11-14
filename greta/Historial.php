<?php
include("conexion.php");

// Verificar que el usuario esté logueado
session_start();
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

// --- Obtener estadísticas ---
$totalAsistencias = 0;
$totalPresentes = 0;
$totalAusentes = 0;
$hoy = date('Y-m-d');

// Total de registros
$sqlTotal = "SELECT COUNT(*) as total FROM asistencias";
if ($stmtTotal = $conn->prepare($sqlTotal)) {
    $stmtTotal->execute();
    $resTotal = $stmtTotal->get_result();
    if ($rowTotal = $resTotal->fetch_assoc()) {
        $totalAsistencias = $rowTotal['total'];
    }
    $stmtTotal->close();
}

// Total de presentes
$sqlPresentes = "SELECT COUNT(*) as total FROM asistencias WHERE asistencia = 1";
if ($stmtPresentes = $conn->prepare($sqlPresentes)) {
    $stmtPresentes->execute();
    $resPresentes = $stmtPresentes->get_result();
    if ($rowPresentes = $resPresentes->fetch_assoc()) {
        $totalPresentes = $rowPresentes['total'];
    }
    $stmtPresentes->close();
}

// Total de ausentes
$sqlAusentes = "SELECT COUNT(*) as total FROM asistencias WHERE asistencia = 0";
if ($stmtAusentes = $conn->prepare($sqlAusentes)) {
    $stmtAusentes->execute();
    $resAusentes = $stmtAusentes->get_result();
    if ($rowAusentes = $resAusentes->fetch_assoc()) {
        $totalAusentes = $rowAusentes['total'];
    }
    $stmtAusentes->close();
}

// Asistencias de hoy
$sqlHoy = "SELECT COUNT(*) as total FROM asistencias WHERE fecha = ?";
if ($stmtHoy = $conn->prepare($sqlHoy)) {
    $stmtHoy->bind_param('s', $hoy);
    $stmtHoy->execute();
    $resHoy = $stmtHoy->get_result();
    $totalHoy = 0;
    if ($rowHoy = $resHoy->fetch_assoc()) {
        $totalHoy = $rowHoy['total'];
    }
    $stmtHoy->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Asistencias - GRETA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-dark: #000000ff;
      --primary-main: #22262cff;
      --primary-light: #718096;
      --accent-pastel: #FED7D7;
      --accent-soft: #FEB2B2;
      --accent-medium: #FC8181;
      --background-light: #FAF5F0;
      --background-white: #FFFFFF;
      --text-dark: #2D3748;
      --text-medium: #454f61ff;
      --text-light: #718096;
      --border-light: #E2E8F0;
      --success: #07db00ff;
      --warning: rgba(255, 2, 2, 1);
      --info: #4299E1;
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
    .bg-purple {
    background-color: var(--purple) !important;
}

    
    /* Tarjetas de estadísticas MEJORADAS */
    .stat-card {
      border: none;
      border-radius: 16px;
      background: var(--background-white);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
      transition: all 0.3s ease;
      /* QUITAMOS el border-left que causaba la franja roja */
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
    
    .btn-warning {
      background: linear-gradient(135deg, var(--warning) 0%, #dd6b20 100%);
      border: none;
      border-radius: 10px;
      font-weight: 500;
      padding: 10px 20px;
      transition: all 0.3s ease;
    }
    
    .btn-warning:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(237, 137, 54, 0.3);
    }
    
    .btn-info {
      background: linear-gradient(135deg, var(--info) 0%, #3182ce 100%);
      border: none;
      border-radius: 10px;
      font-weight: 500;
      padding: 8px 18px;
      transition: all 0.3s ease;
    }
    
    .btn-info:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(66, 153, 225, 0.3);
    }
    
    /* Tablas MEJORADAS */
    .table-container {
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
    }
    
    .table {
      margin-bottom: 0;
      width: 100%;
    }
    
    .table thead th {
      background-color: var(--primary-main);
      color: white;
      font-weight: 600;
      border: none;
      padding: 16px 12px;
      white-space: nowrap;
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
    
    /* Columnas específicas para mejor distribución */
    .table th:nth-child(1), .table td:nth-child(1) { width: 8%; } /* ID */
    .table th:nth-child(2), .table td:nth-child(2) { width: 12%; } /* Fecha */
    .table th:nth-child(3), .table td:nth-child(3) { width: 20%; } /* Nombre */
    .table th:nth-child(4), .table td:nth-child(4) { width: 15%; } /* Asistencia */
    .table th:nth-child(5), .table td:nth-child(5) { width: 25%; } /* Anotaciones */
    .table th:nth-child(6), .table td:nth-child(6) { width: 20%; min-width: 150px; } /* Acciones */
    
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
    
    .badge-primary {
      background-color: var(--primary-main);
    }
    
    
    /* Formularios */
    .form-control, .form-select {
      border-radius: 10px;
      border: 2px solid var(--border-light);
      padding: 12px 15px;
      transition: all 0.3s ease;
      font-size: 0.95rem;
    }
    
    .form-control:focus, .form-select:focus {
      border-color: var(--accent-medium);
      box-shadow: 0 0 0 3px rgba(252, 129, 129, 0.1);
    }
    
    .form-label {
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 8px;
    }
    
    /* Alertas */
    .alert {
      border-radius: 12px;
      border: none;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      padding: 16px 20px;
    }
    
    /* Footer */
    footer {
      background: var(--background-white);
      border-top: 1px solid var(--border-light);
      color: var(--text-light);
      font-size: 0.875rem;
    }

    /* Encabezado principal */
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
    
    /* Progress bar para estadísticas */
    .progress {
      height: 8px;
      border-radius: 10px;
      background-color: var(--border-light);
      margin-top: 8px;
    }
    
    .progress-bar {
      border-radius: 10px;
    }
    
    /* Responsive MEJORADO */
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
      
      /* Ajustes de tabla en móviles */
      .table-container {
        overflow-x: auto;
      }
      
      .table {
        min-width: 800px;
      }
    }
    
    @media (max-width: 768px) {
      .main-content {
        padding: 15px;
      }
      
      .btn-group-responsive {
        flex-direction: column;
        gap: 8px;
      }
      
      .btn-group-responsive .btn {
        width: 100%;
      }
      
      .stat-card .card-title {
        font-size: 1.75rem;
      }
      
      .page-header {
        padding: 20px;
      }
      
      /* Tarjetas en móviles */
      .stat-card {
        margin-bottom: 1rem;
      }
    }
    
    @media (max-width: 576px) {
      .stat-card .card-body {
        padding: 1.25rem;
      }
      
      .stat-card i {
        font-size: 1.5rem;
        padding: 12px;
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
        GRETA · Historial de Asistencias
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
            <a class="nav-link" href="gestionUsuarios.php">
              <i class="bi bi-people me-2"></i>Usuarios
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="Historial.php">
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
        <a class="nav-link" href="gestionUsuarios.php">
          <i class="bi bi-people me-2"></i> Usuarios
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="Historial.php">
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
      <!-- Encabezado -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="page-header">
            <div class="row align-items-center">
              <div class="col-md-8">
                <h1 class="h2 mb-2 fw-bold text-white">
                  <i class="bi bi-calendar-check me-2"></i>Historial de Asistencias
                </h1>
                <p class="text-white mb-0 opacity-75">Consulta y gestiona el registro de asistencias del personal</p>
              </div>
              <div class="col-md-4 text-end">
                <i class="bi bi-calendar-check display-4 opacity-25"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

            <!-- Tarjetas de estadísticas - DISEÑO MEJORADO -->
      <div class="row mb-4">
        <!-- Tarjeta Total Registros -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1">Total Registros</h6>
                  <h3 class="card-title mb-0"><?= $totalAsistencias ?></h3>
                  <p class="small mb-0">Todos los registros</p>
                </div>
                <i class="bi bi-calendar-check" style="color: var(--primary-main);"></i>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Tarjeta Presentes -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1">Presentes</h6>
                  <h3 class="card-title mb-0"><?= $totalPresentes ?></h3>
                  <p class="small mb-0">Asistencias confirmadas</p>
                  <?php if ($totalAsistencias > 0): ?>
                    <div class="progress mt-2">
                      <div class="progress-bar bg-success" role="progressbar" 
                           style="width: <?= round(($totalPresentes / $totalAsistencias) * 100) ?>%" 
                           aria-valuenow="<?= round(($totalPresentes / $totalAsistencias) * 100) ?>" 
                           aria-valuemin="0" 
                           aria-valuemax="100">
                      </div>
                    </div>
                    <small class="text-muted"><?= round(($totalPresentes / $totalAsistencias) * 100) ?>% del total</small>
                  <?php endif; ?>
                </div>
                <i class="bi bi-check-circle" style="color: var(--success);"></i>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Tarjeta Ausentes -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1">Ausentes</h6>
                  <h3 class="card-title mb-0"><?= $totalAusentes ?></h3>
                  <p class="small mb-0">Inasistencias registradas</p>
                  <?php if ($totalAsistencias > 0): ?>
                    <div class="progress mt-2">
                      <div class="progress-bar bg-warning" role="progressbar" 
                           style="width: <?= round(($totalAusentes / $totalAsistencias) * 100) ?>%" 
                           aria-valuenow="<?= round(($totalAusentes / $totalAsistencias) * 100) ?>" 
                           aria-valuemin="0" 
                           aria-valuemax="100">
                      </div>
                    </div>
                    <small class="text-muted"><?= round(($totalAusentes / $totalAsistencias) * 100) ?>% del total</small>
                  <?php endif; ?>
                </div>
                <i class="bi bi-x-circle" style="color: var(--warning);"></i>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Tarjeta Registros Hoy -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1">Registros Hoy</h6>
                  <h3 class="card-title mb-0"><?= $totalHoy ?></h3>
                  <p class="small mb-0"><?= date('d/m/Y') ?></p>
                </div>
                <i class="bi bi-clock-history" style="color: var(--info);"></i>
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
                <i class="bi bi-list-check me-2"></i>Registros de Asistencia
              </h5>
            </div>
            <div class="d-flex gap-2">
              <a href="index.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i> Nuevo Registro
              </a>
              <button type="button" class="btn btn-info" onclick="window.print()">
                <i class="bi bi-printer me-2"></i> Imprimir
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Alertas -->
      <?php if (isset($_GET['modificado'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check-circle me-2"></i> Asistencia modificada correctamente.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
      <?php elseif (isset($_GET['eliminada'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-trash me-2"></i> Registro eliminado correctamente.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
      <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i> Ocurrió un error (<?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?>).
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
      <?php endif; ?>

      <!-- Filtros -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filtros de Búsqueda
              </h5>
            </div>
            <div class="card-body">
              <form method="GET" class="row g-3">
                <div class="col-md-6">
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
                </div>
                
                <div class="col-md-6">
                  <label for="fecha" class="form-label">Seleccionar fecha:</label>
                  <input type="date" id="fecha" name="fecha" class="form-control" 
                         value="<?= $fecha ? htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8') : '' ?>">
                </div>
                
                <div class="col-12">
                  <div class="d-flex gap-2 flex-wrap btn-group-responsive">
                    <button type="submit" class="btn btn-primary">
                      <i class="bi bi-search me-2"></i> Buscar
                    </button>
                    <?php if ($usuarioId !== null || $fecha): ?>
                      <a href="Historial.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-clockwise me-2"></i> Ver todo el historial
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de asistencias MEJORADA -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">
                <i class="bi bi-table me-2"></i>Lista de Asistencias
              </h5>
            </div>
            <div class="card-body p-0">
              <div class="table-container">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th><i class="bi bi-hash me-1"></i>ID</th>
                      <th><i class="bi bi-calendar me-1"></i>Fecha</th>
                      <th><i class="bi bi-person me-1"></i>Nombre</th>
                      <th><i class="bi bi-check-circle me-1"></i>Asistencia</th>
                      <th><i class="bi bi-chat-text me-1"></i>Anotaciones</th>
                      <th><i class="bi bi-gear me-1"></i>Acciones</th>
                    </tr>
                  </thead>
                  <tbody id="tabla-asistencias">
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
                          $asiste  = (int)$row['asistencia'] === 1;
                          $anot    = htmlspecialchars($row['anotaciones'] ?? '', ENT_QUOTES, 'UTF-8');
                          $fechaDb = $row['fecha'] ?? '';
                          $fechaOk = $fechaDb ? date("d-m-Y", strtotime($fechaDb)) : '';
                          $estadoTxt = $asiste ? "Presente" : "Ausente";
                          $estadoBadge = $asiste ? "success" : "warning";

                    ?>
                    <tr>
                      <td><?= $id ?></td>
                      <td><?= $fechaOk ?></td>
                      <td><?= $nombre ?></td>
                      <td>
                        <span class="badge badge-<?= $estadoBadge ?>">
                          <i class="bi bi-circle-fill me-1"></i><?= $estadoTxt ?>
                        </span>
                      </td>
                      <td class="text-truncate" style="max-width: 200px;" title="<?= $anot ?>">
                        <?= $anot ?>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm" role="group">
                          <a href="ModificarAsistencia.php?id=<?= $id ?>" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Editar
                          </a>
                          <a href="EliminarAsistencia.php?id=<?= $id ?>" class="btn btn-danger" onclick="return confirm('¿Seguro que querés eliminar este registro?')">
                            <i class="bi bi-trash"></i> Eliminar
                          </a>
                        </div>
                      </td>
                    </tr>
                    <?php
                        }
                      } else {
                        echo '<tr><td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-calendar-x display-4 text-muted mb-3"></i><br>
                                No se encontraron registros de asistencia.
                              </td></tr>';
                      }
                      $stmt->close();
                    } else {
                      echo '<tr><td colspan="6" class="text-danger text-center py-4">
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
    if (urlParams.has('modificado')) {
      mostrarToast("Asistencia modificada correctamente.");
    }
    if (urlParams.has('eliminada')) {
      mostrarToast("Registro eliminado correctamente.");
    }
  </script>
</body>
</html>