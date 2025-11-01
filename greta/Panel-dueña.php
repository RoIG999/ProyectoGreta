<?php
// Panel-dueña.php
// Asegurate de guardar en UTF-8 (sin BOM)
session_start();

if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php'); exit;
}

// Normalizar rol: minúsculas y sin tildes
$rol = $_SESSION['usuario_rol'] ?? '';
$rol_normalizado = mb_strtolower($rol, 'UTF-8');
$rol_normalizado = strtr($rol_normalizado, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n']);

// Solo dueña, admin o supervisor (en minúsculas)
if (!in_array($rol_normalizado, ['duena', 'dueña', 'supervisor', 'admin'])) {
  header('Location: login.php?e=perm'); exit;
}

$nombre = htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario', ENT_QUOTES, 'UTF-8');

// Conexión a la base de datos para obtener datos reales
include("conexion.php");

// Obtener datos reales de la base de datos
$total_empleados = 0;
$total_clientes = 0;
$asistencias_hoy = 0;
$asistencias_semana = 0;
$servicios_activos = 0;
$reservas_hoy = 0;

// Total empleados activos
$sql_empleados = "SELECT COUNT(*) as total FROM usuarios WHERE estado = 1 AND rol = 'empleado'";
if ($result = $conn->query($sql_empleados)) {
    $row = $result->fetch_assoc();
    $total_empleados = $row['total'];
}

// Asistencias de hoy
$hoy = date('Y-m-d');
$sql_asistencias_hoy = "SELECT COUNT(*) as total FROM asistencias WHERE fecha = '$hoy' AND asistencia = 1";
if ($result = $conn->query($sql_asistencias_hoy)) {
    $row = $result->fetch_assoc();
    $asistencias_hoy = $row['total'];
}

// Asistencias de la semana (lunes a sábado)
$inicio_semana = date('Y-m-d', strtotime('monday this week'));
$fin_semana = date('Y-m-d', strtotime('saturday this week'));
$sql_asistencias_semana = "SELECT COUNT(*) as total FROM asistencias WHERE fecha BETWEEN '$inicio_semana' AND '$fin_semana' AND asistencia = 1";
if ($result = $conn->query($sql_asistencias_semana)) {
    $row = $result->fetch_assoc();
    $asistencias_semana = $row['total'];
}

// Reservas de hoy
$sql_reservas_hoy = "SELECT COUNT(*) as total FROM turno WHERE fecha = '$hoy' AND ID_estado_turno_FK IN (1,5)";
if ($result = $conn->query($sql_reservas_hoy)) {
    $row = $result->fetch_assoc();
    $reservas_hoy = $row['total'];
}

// NUEVAS ESTADÍSTICAS DE TURNOS
$turnos_confirmados_hoy = 0;
$turnos_en_proceso_hoy = 0;
$turnos_pagados_hoy = 0;
$ingresos_hoy = 0;

// Turnos confirmados hoy
$sql_confirmados = "SELECT COUNT(*) as total FROM turno WHERE fecha = '$hoy' AND ID_estado_turno_FK = 1";
if ($result = $conn->query($sql_confirmados)) {
    $row = $result->fetch_assoc();
    $turnos_confirmados_hoy = $row['total'];
}

// Turnos en proceso hoy
$sql_proceso = "SELECT COUNT(*) as total FROM turno WHERE fecha = '$hoy' AND ID_estado_turno_FK = 4";
if ($result = $conn->query($sql_proceso)) {
    $row = $result->fetch_assoc();
    $turnos_en_proceso_hoy = $row['total'];
}

// Turnos pagados hoy e ingresos
$sql_pagados = "SELECT COUNT(*) as total, COALESCE(SUM(s.precio), 0) as ingresos 
                FROM turno t 
                LEFT JOIN rubro_servicio rs ON t.ID_servicio_FK = rs.ID 
                LEFT JOIN servicio s ON rs.nombre = s.nombre 
                WHERE t.fecha = '$hoy' AND t.ID_estado_turno_FK = 7";
if ($result = $conn->query($sql_pagados)) {
    $row = $result->fetch_assoc();
    $turnos_pagados_hoy = $row['total'];
    $ingresos_hoy = $row['ingresos'];
}

// Servicios activos
$sql_servicios = "SELECT COUNT(*) as total FROM servicio WHERE estado = 1";
if ($result = $conn->query($sql_servicios)) {
    $row = $result->fetch_assoc();
    $servicios_activos = $row['total'];
}

// Obtener reservas recientes (últimos 5 turnos)
$reservas_recientes = [];
$sql_recientes = "SELECT t.nombre_cliente, t.apellido_cliente, t.hora, rs.nombre as servicio, et.nombre as estado
                  FROM turno t 
                  LEFT JOIN rubro_servicio rs ON t.ID_servicio_FK = rs.ID 
                  LEFT JOIN estado_turno et ON t.ID_estado_turno_FK = et.ID 
                  WHERE t.fecha >= '$hoy' 
                  ORDER BY t.fecha DESC, t.hora DESC 
                  LIMIT 5";
$result_recientes = $conn->query($sql_recientes);
if ($result_recientes) {
    while ($row = $result_recientes->fetch_assoc()) {
        $reservas_recientes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Dueña - GRETA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --primary-dark: #2D3748;
      --primary-main: #4A5568;
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
    
    /* Accesos rápidos */
    .quick-access-card {
      border: none;
      border-radius: 12px;
      background: var(--background-white);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
      transition: all 0.3s ease;
      padding: 20px;
      text-align: center;
    }
    
    .quick-access-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      background: linear-gradient(135deg, var(--accent-pastel) 0%, var(--background-white) 100%);
      text-decoration: none;
    }
    
    .quick-access-card i {
      font-size: 2.5rem;
      background: linear-gradient(135deg, var(--accent-pastel) 0%, var(--accent-soft) 100%);
      padding: 20px;
      border-radius: 16px;
      color: var(--primary-main);
      margin-bottom: 12px;
    }
    
    .quick-access-card h6 {
      font-weight: 600;
      color: var(--primary-dark);
      margin: 0;
    }
    
    /* Gráficos */
    .chart-container {
      position: relative;
      height: 250px;
      padding: 20px;
    }
    
    /* Actividad reciente */
    .recent-activity {
      max-height: 350px;
      overflow-y: auto;
      padding: 0 20px 20px;
    }
    
    .activity-item {
      border-left: 3px solid var(--accent-medium);
      padding: 12px 0 12px 16px;
      margin-bottom: 16px;
      background: var(--background-white);
      border-radius: 0 8px 8px 0;
    }
    
    .activity-item:last-child {
      margin-bottom: 0;
    }
    
    /* Secciones */
    .section {
      display: none;
      opacity: 0;
      transition: opacity 0.5s ease;
    }
    
    .section.active {
      display: block;
      opacity: 1;
    }
    
    /* Footer */
    footer {
      background: var(--background-white);
      border-top: 1px solid var(--border-light);
      color: var(--text-light);
      font-size: 0.875rem;
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
      <a class="navbar-brand" href="#">
        <img src="img/LogoGreta.jpeg" alt="GRETA" style="height: 50px; width: auto; margin-right: 12px;">
        GRETA · Panel Dueña
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div id="navBar" class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link active" href="Panel-dueña.php">Inicio</a></li>
          <li class="nav-item"><a class="nav-link" href="gestionUsuarios.php">Usuarios</a></li>
          <li class="nav-item"><a class="nav-link" href="Historial.php">Asistencias</a></li>
          <li class="nav-item"><a class="nav-link" href="Servicios(Dueña).php">Servicios</a></li>
          <li class="nav-item"><a class="nav-link" href="gestion-turnos-dueña.php">Turnos</a></li>
        </ul>
        <div class="d-flex align-items-center">
          <span class="navbar-text text-white me-3">Hola, <?= $nombre; ?></span>
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
        <a class="nav-link active" href="#" data-target="dashboard">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="gestionUsuarios.php">
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
        <a class="nav-link" href="#">
          <i class="bi bi-graph-up me-2"></i> Reportes
        </a>
      </li>
    </ul>
  </div>

  <!-- Contenido principal -->
  <div class="main-content" style="margin-top: 76px;">
    <div class="container-fluid">
      <!-- Sección Dashboard -->
      <div id="dashboard" class="section active">
        <!-- Encabezado -->
        <div class="row mb-4">
          <div class="col-12">
            <h1 class="h3 mb-2 fw-bold text-dark">Dashboard</h1>
            <p class="text-muted">Resumen general y estadísticas de tu negocio</p>
          </div>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="row mb-4">
          <div class="col-12 col-md-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="card-subtitle mb-1">Empleados</h6>
                    <h3 class="card-title mb-0"><?= $total_empleados ?></h3>
                    <p class="small mb-0">Total registrados</p>
                  </div>
                  <i class="bi bi-people-fill"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="card-subtitle mb-1">Asistencias Hoy</h6>
                    <h3 class="card-title mb-0"><?= $asistencias_hoy ?></h3>
                    <p class="small mb-0"><?= $asistencias_semana ?> esta semana</p>
                  </div>
                  <i class="bi bi-calendar-check"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="card-subtitle mb-1">Turnos Hoy</h6>
                    <h3 class="card-title mb-0"><?= $reservas_hoy ?></h3>
                    <p class="small mb-0">
                      <?= $turnos_confirmados_hoy ?> confirmados · 
                      <?= $turnos_en_proceso_hoy ?> en proceso
                    </p>
                  </div>
                  <i class="bi bi-clock"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="card-subtitle mb-1">Ingresos Hoy</h6>
                    <h3 class="card-title mb-0">$<?= number_format($ingresos_hoy, 0, ',', '.') ?></h3>
                    <p class="small mb-0"><?= $turnos_pagados_hoy ?> turnos pagados</p>
                  </div>
                  <i class="bi bi-currency-dollar"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Gráficos y actividad reciente -->
        <div class="row">
          <!-- Gráfico de asistencias -->
          <div class="col-12 col-lg-8 mb-4">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-0">Asistencias semanales</h5>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="attendanceChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Turnos recientes -->
          <div class="col-12 col-lg-4 mb-4">
            <div class="card h-100">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Turnos Recientes</h5>
                <a href="gestion-turnos-dueña.php" class="btn btn-sm btn-outline-primary">
                  Gestionar
                </a>
              </div>
              <div class="card-body recent-activity">
                <?php if (empty($reservas_recientes)): ?>
                  <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x fs-1 mb-2"></i>
                    <p>No hay turnos recientes</p>
                  </div>
                <?php else: ?>
                  <?php foreach ($reservas_recientes as $reserva): ?>
                    <div class="activity-item">
                      <div class="d-flex justify-content-between align-items-start">
                        <div>
                          <strong><?= htmlspecialchars($reserva['nombre_cliente'] . ' ' . $reserva['apellido_cliente']) ?></strong>
                          <p class="mb-1 text-muted small"><?= htmlspecialchars($reserva['servicio']) ?></p>
                          <small class="text-muted"><?= date('H:i', strtotime($reserva['hora'])) ?></small>
                        </div>
                        <span class="badge bg-light text-dark">
                          <?= htmlspecialchars($reserva['estado']) ?>
                        </span>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Accesos rápidos -->
        <div class="row mb-4">
          <div class="col-12">
            <h5 class="mb-3 fw-bold text-dark">Accesos Rápidos</h5>
          </div>
          
          <div class="col-6 col-md-3 col-lg-2 mb-3">
            <a href="gestionUsuarios.php" class="text-decoration-none">
              <div class="quick-access-card">
                <i class="bi bi-person-plus"></i>
                <h6>Nuevo Usuario</h6>
              </div>
            </a>
          </div>
          
          <div class="col-6 col-md-3 col-lg-2 mb-3">
            <a href="Historial.php" class="text-decoration-none">
              <div class="quick-access-card">
                <i class="bi bi-clipboard-check"></i>
                <h6>Registrar Asistencia</h6>
              </div>
            </a>
          </div>
          
          <div class="col-6 col-md-3 col-lg-2 mb-3">
            <a href="Servicios(Dueña).php" class="text-decoration-none">
              <div class="quick-access-card">
                <i class="bi bi-plus-circle"></i>
                <h6>Agregar Servicio</h6>
              </div>
            </a>
          </div>
          
          <div class="col-6 col-md-3 col-lg-2 mb-3">
            <a href="calendario.php" class="text-decoration-none">
              <div class="quick-access-card">
                <i class="bi bi-calendar2-plus"></i>
                <h6>Ver Calendario</h6>
              </div>
            </a>
          </div>

          <div class="col-6 col-md-3 col-lg-2 mb-3">
            <a href="gestion-turnos-dueña.php" class="text-decoration-none">
              <div class="quick-access-card">
                <i class="bi bi-calendar-check"></i>
                <h6>Gestión de Turnos</h6>
              </div>
            </a>
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

    // Gráfico de asistencias
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
        datasets: [{
          label: 'Asistencias',
          data: [0, 0, 0, 0, 0, 0],
          backgroundColor: '#FC8181',
          borderColor: '#F56565',
          borderWidth: 1,
          borderRadius: 6
        }]
      },
      options: {
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 5
            },
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });

    // Navegación entre secciones
    document.querySelectorAll('[data-target]').forEach(item => {
      item.addEventListener('click', function(e) {
        if (this.getAttribute('href') === '#') {
          e.preventDefault();
        }
        
        const targetId = this.getAttribute('data-target');
        showSection(targetId);
        
        // Actualizar navegación activa en sidebar
        if (this.closest('.sidebar')) {
          document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.classList.remove('active');
          });
          this.classList.add('active');
        }
      });
    });

    // Función para mostrar sección
    function showSection(sectionId) {
      document.querySelectorAll('.section').forEach(section => {
        section.classList.remove('active');
      });
      document.getElementById(sectionId).classList.add('active');
    }
  </script>
</body>
</html>