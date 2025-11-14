<?php
// ModificarAsistencia.php
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

// Asegurar zona horaria (opcional)
if (!ini_get('date.timezone')) {
  date_default_timezone_set('America/Argentina/Cordoba');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $asistencia  = isset($_POST['asistencia']) ? 1 : 0;
  $anotaciones = (string)($_POST['anotaciones'] ?? '');

  if ($id <= 0) {
    header("Location: historial.php?error=id"); exit;
  }

  // (Opcional) Limite de longitud defensivo
  if (strlen($anotaciones) > 1000) { $anotaciones = substr($anotaciones, 0, 1000); }

  $stmt = $conn->prepare("UPDATE asistencias SET asistencia = ?, anotaciones = ? WHERE id = ?");
  $stmt->bind_param("isi", $asistencia, $anotaciones, $id);

  if ($stmt->execute()) {
    $stmt->close();
    header("Location: historial.php?modificado=1"); exit;
  } else {
    $stmt->close();
    header("Location: historial.php?error=update"); exit;
  }
}

// ---- Carga por GET para mostrar el form ----
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  die("<div class='alert alert-danger'>ID inválido.</div>");
}

$sql = "SELECT a.*, u.nombre FROM asistencias a 
        JOIN usuarios u ON a.id_usuario = u.id 
        WHERE a.id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$asistencia = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$asistencia) {
  die("<div class='alert alert-danger'>Asistencia no encontrada.</div>");
}

// Datos escapados para imprimir
$nombreSafe = htmlspecialchars($asistencia['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
$fechaSafe  = htmlspecialchars($asistencia['fecha'] ?? '', ENT_QUOTES, 'UTF-8');
$anotSafe   = htmlspecialchars($asistencia['anotaciones'] ?? '', ENT_QUOTES, 'UTF-8');
$checked    = ((int)$asistencia['asistencia'] === 1) ? 'checked' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Asistencia - GRETA</title>
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
    
    .btn-success {
      background: linear-gradient(135deg, var(--success) 0%, #38a169 100%);
      border: none;
      border-radius: 10px;
      font-weight: 500;
      padding: 10px 20px;
      transition: all 0.3s ease;
    }
    
    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
    }
    
    /* Formularios */
    .form-container {
      max-width: 720px;
      margin: 0 auto;
    }
    
    .form-control, .form-select, .form-textarea {
      border-radius: 10px;
      border: 2px solid var(--border-light);
      padding: 12px 15px;
      transition: all 0.3s ease;
      font-size: 0.95rem;
    }
    
    .form-control:focus, .form-select:focus, .form-textarea:focus {
      border-color: var(--accent-medium);
      box-shadow: 0 0 0 3px rgba(252, 129, 129, 0.1);
    }
    
    .form-label {
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 8px;
    }
    
    .form-text {
      color: var(--text-light);
      font-size: 0.85rem;
    }
    
    /* Checkbox personalizado */
    .form-check-input {
      border-radius: 6px;
      border: 2px solid var(--border-light);
      width: 1.2em;
      height: 1.2em;
      margin-top: 0.2em;
    }
    
    .form-check-input:checked {
      background-color: var(--accent-medium);
      border-color: var(--accent-medium);
    }
    
    .form-check-input:focus {
      box-shadow: 0 0 0 3px rgba(252, 129, 129, 0.1);
    }
    
    .form-check-label {
      font-weight: 500;
      color: var(--text-dark);
    }
    
    /* Badges */
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
      
      .form-container {
        max-width: 100%;
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
        GRETA · Editar Asistencia
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div id="navBar" class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="Panel-dueña.php">Inicio</a></li>
          <li class="nav-item"><a class="nav-link" href="gestionUsuarios.php">Usuarios</a></li>
          <li class="nav-item"><a class="nav-link active" href="Historial.php">Asistencias</a></li>
          <li class="nav-item"><a class="nav-link" href="Servicios(Dueña).php">Servicios</a></li>
          <li class="nav-item"><a class="nav-link" href="gestion-turnos-dueña.php">Turnos</a></li>
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
      <div class="form-container">
        <!-- Encabezado -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h1 class="h3 mb-2 fw-bold text-dark">Editar Asistencia</h1>
                <p class="text-muted">Modifica el registro de asistencia del empleado</p>
              </div>
              <a href="historial.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i> Volver al Historial
              </a>
            </div>
          </div>
        </div>

        <!-- Información del registro -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0">
              <i class="bi bi-info-circle me-2"></i>Información del Registro
            </h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <p class="mb-2"><strong>Empleado:</strong> <?= $nombreSafe ?></p>
                <p class="mb-2"><strong>Fecha:</strong> <?= $fechaSafe ?></p>
              </div>
              <div class="col-md-6">
                <p class="mb-2"><strong>Estado actual:</strong> 
                  <span class="badge <?= ((int)$asistencia['asistencia'] === 1) ? 'badge-success' : 'badge-warning' ?>">
                    <?= ((int)$asistencia['asistencia'] === 1) ? '✅ Presente' : '❌ Ausente' ?>
                  </span>
                </p>
                <p class="mb-0"><strong>ID del registro:</strong> <?= (int)$asistencia['id'] ?></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Formulario de edición -->
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">
              <i class="bi bi-pencil-square me-2"></i>Editar Datos de Asistencia
            </h5>
          </div>
          <div class="card-body">
            <form method="POST" action="ModificarAsistencia.php">
              <input type="hidden" name="id" value="<?= (int)$asistencia['id'] ?>">

              <div class="row">
                <div class="col-md-12 mb-4">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="asistencia" id="asistencia" <?= $checked ?> 
                           style="width: 3em; height: 1.5em;">
                    <label class="form-check-label fw-semibold" for="asistencia">
                      <i class="bi bi-check-circle me-2"></i>Marcar como presente
                    </label>
                  </div>
                  <div class="form-text">
                    Activa esta opción si el empleado asistió en la fecha indicada
                  </div>
                </div>

                <div class="col-md-12 mb-4">
                  <label for="anotaciones" class="form-label">Anotaciones</label>
                  <textarea name="anotaciones" id="anotaciones" class="form-control form-textarea" 
                            rows="5" placeholder="Agrega observaciones o comentarios sobre la asistencia..."><?= $anotSafe ?></textarea>
                  <div class="form-text">
                    Máximo 1000 caracteres. Información adicional sobre la asistencia.
                  </div>
                </div>

                <div class="col-12 mt-4">
                  <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success flex-fill py-2">
                      <i class="bi bi-check-circle me-2"></i> Guardar Cambios
                    </button>
                    <a href="historial.php" class="btn btn-outline-secondary py-2">
                      <i class="bi bi-x-circle me-2"></i> Cancelar
                    </a>
                  </div>
                </div>
              </div>
            </form>
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

    // Actualizar estado visual del checkbox
    const checkbox = document.getElementById('asistencia');
    const estadoBadge = document.querySelector('.badge');
    
    checkbox.addEventListener('change', function() {
      if (this.checked) {
        estadoBadge.className = 'badge badge-success';
        estadoBadge.textContent = '✅ Presente';
      } else {
        estadoBadge.className = 'badge badge-warning';
        estadoBadge.textContent = '❌ Ausente';
      }
    });

    // Contador de caracteres para anotaciones
    const textarea = document.getElementById('anotaciones');
    const charCount = document.createElement('div');
    charCount.className = 'form-text text-end mt-1';
    textarea.parentNode.appendChild(charCount);

    function updateCharCount() {
      const length = textarea.value.length;
      charCount.textContent = `${length}/1000 caracteres`;
      
      if (length > 900) {
        charCount.className = 'form-text text-end mt-1 text-warning';
      } else {
        charCount.className = 'form-text text-end mt-1';
      }
    }

    textarea.addEventListener('input', updateCharCount);
    updateCharCount(); // Inicializar contador

    // Validación del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
      const anotaciones = textarea.value;
      
      if (anotaciones.length > 1000) {
        e.preventDefault();
        alert('Las anotaciones no pueden exceder los 1000 caracteres.');
        textarea.focus();
        return false;
      }
    });
  </script>
</body>
</html>