<?php
// usuariosInactivos.php
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

// Obtener usuarios inactivos
$usuarios_inactivos = [];
$sql = "SELECT id, nombre, rol FROM usuarios WHERE estado = 0 ORDER BY id DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $usuarios_inactivos[] = $row;
    }
}

// Contar usuarios activos para contexto
$sql_activos = "SELECT COUNT(*) as total FROM usuarios WHERE estado = 1";
$result_activos = $conn->query($sql_activos);
$total_activos = $result_activos ? $result_activos->fetch_assoc()['total'] : 0;

// Calcular porcentaje de inactivos
$total_usuarios = $total_activos + count($usuarios_inactivos);
$porcentaje_inactivos = $total_usuarios > 0 ? round((count($usuarios_inactivos) / $total_usuarios) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios Inactivos - GRETA</title>
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

    .featured-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 20px;
      padding: 30px;
      position: relative;
      overflow: hidden;
    }
    
    .featured-card::before {
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
        GRETA · Panel Dueña
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
      <!-- Encabezado -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="featured-card">
            <div class="row align-items-center">
              <div class="col-md-8">
                <h1 class="h2 mb-2 fw-bold text-white">
                  <i class="bi bi-person-x me-2"></i>Usuarios Inactivos
                </h1>
                <p class="text-white mb-0">Gestión de usuarios desactivados en el sistema</p>
              </div>
              <div class="col-md-4 text-end">
                <i class="bi bi-person-x display-4 opacity-50"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Estadísticas rápidas -->
      <div class="row mb-4">
        <div class="col-12 col-md-6 mb-3">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1">Usuarios Inactivos</h6>
                  <h3 class="card-title mb-0"><?= count($usuarios_inactivos) ?></h3>
                  <p class="small mb-0">Total desactivados en el sistema</p>
                  <div class="progress mt-2">
                    <div class="progress-bar bg-warning" role="progressbar" 
                         style="width: <?= $porcentaje_inactivos ?>%" 
                         aria-valuenow="<?= $porcentaje_inactivos ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                  </div>
                  <small class="text-muted"><?= $porcentaje_inactivos ?>% del total de usuarios</small>
                </div>
                <i class="bi bi-person-x"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6 mb-3">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1">Usuarios Activos</h6>
                  <h3 class="card-title mb-0"><?= $total_activos ?></h3>
                  <p class="small mb-0">Con acceso al sistema</p>
                  <div class="progress mt-2">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: <?= 100 - $porcentaje_inactivos ?>%" 
                         aria-valuenow="<?= 100 - $porcentaje_inactivos ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                  </div>
                  <small class="text-muted"><?= 100 - $porcentaje_inactivos ?>% del total de usuarios</small>
                </div>
                <i class="bi bi-person-check"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de usuarios inactivos -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">
                <i class="bi bi-list-ul me-2"></i>Lista de Usuarios Inactivos
              </h5>
              <div>
                <a href="gestionUsuarios.php" class="btn btn-outline-primary me-2">
                  <i class="bi bi-arrow-left me-1"></i> Volver a Usuarios
                </a>
                <a href="Panel-dueña.php" class="btn btn-primary">
                  <i class="bi bi-house me-1"></i> Ir al Dashboard
                </a>
              </div>
            </div>
            <div class="card-body p-0">
              <?php if (isset($_GET['reactivado']) || (isset($_GET['ok']) && $_GET['ok']==='activado')): ?>
                <div class="alert alert-success m-3" role="alert">
                  <i class="bi bi-check-circle-fill me-2"></i>Usuario reactivado correctamente.
                </div>
              <?php endif; ?>
              
              <div class="table-container">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Rol</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($usuarios_inactivos)): ?>
                      <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                          <i class="bi bi-person-check display-4 text-muted mb-3"></i>
                          <p>No hay usuarios inactivos en el sistema</p>
                          <a href="gestionUsuarios.php" class="btn btn-primary mt-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver a Gestión de Usuarios
                          </a>
                        </td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($usuarios_inactivos as $usuario): ?>
                        <tr>
                          <td><?= $usuario['id'] ?></td>
                          <td>
                            <div class="d-flex align-items-center">
                              <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-person text-muted"></i>
                              </div>
                              <div>
                                <strong><?= htmlspecialchars($usuario['nombre']) ?></strong>
                              </div>
                            </div>
                          </td>
                          <td>
                            <span class="badge 
                              <?= strtolower($usuario['rol']) === 'empleado' ? 'bg-primary' : 
                                 (strtolower($usuario['rol']) === 'supervisor' ? 'bg-warning' : 'bg-secondary') ?>">
                              <?= ucfirst($usuario['rol']) ?>
                            </span>
                          </td>
                          <td>
                            <span class="badge bg-danger">
                              <i class="bi bi-x-circle me-1"></i> Inactivo
                            </span>
                          </td>
                          <td>
                            <a href="ActivarUsuario.php?id=<?= $usuario['id'] ?>" 
                               class="btn btn-success btn-sm"
                               onclick="return confirm('¿Seguro que querés reactivar a <?= htmlspecialchars($usuario['nombre']) ?>?')">
                              <i class="bi bi-check-lg me-1"></i> Activar
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
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

    // Auto-ocultar alertas después de 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        setTimeout(() => {
          alert.style.opacity = '0';
          setTimeout(() => alert.remove(), 300);
        }, 5000);
      });
    });
  </script>
</body>
</html>