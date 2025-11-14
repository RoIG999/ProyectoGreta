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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Asistencia - GRETA</title>
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
    
    .table tbody tr.present {
      background-color: #d1e7dd !important;
    }
    
    .table tbody tr.present:hover {
      background-color: #b8dfce !important;
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
    
    .badge-primary {
      background-color: var(--primary-main);
    }
    
    /* Formularios */
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
      
      .btn-group-responsive {
        flex-direction: column;
        gap: 8px;
      }
      
      .btn-group-responsive .btn {
        width: 100%;
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
        GRETA · Registro de Asistencia
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
            <a class="nav-link active" href="index.php">
              <i class="bi bi-calendar-check me-2"></i>Asistencia
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="gestionUsuarios.php">
              <i class="bi bi-people me-2"></i>Usuarios
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Historial.php">
              <i class="bi bi-clock-history me-2"></i>Historial
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
        <a class="nav-link active" href="index.php">
          <i class="bi bi-calendar-check me-2"></i> Asistencia
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="Historial.php">
          <i class="bi bi-clock-history me-2"></i> Historial
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
          <h1 class="h3 mb-2 fw-bold text-dark">
            <i class="bi bi-calendar-check me-2"></i>Registro de Asistencia
          </h1>
          <p class="text-muted">Registra la asistencia diaria del personal</p>
        </div>
      </div>

      <!-- Botones de acción -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-0">
                <i class="bi bi-list-check me-2"></i>Registro Diario
              </h5>
            </div>
            <div class="d-flex gap-2">
              <a href="Historial.php" class="btn btn-outline-primary">
                <i class="bi bi-clock-history me-2"></i> Ver Historial
              </a>
              <button type="button" class="btn btn-info" onclick="window.print()">
                <i class="bi bi-printer me-2"></i> Imprimir
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Búsqueda rápida por ID -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">
                <i class="bi bi-search me-2"></i>Búsqueda Rápida por ID
              </h5>
            </div>
            <div class="card-body">
              <div class="row align-items-end">
                <div class="col-md-6">
                  <label for="input-id" class="form-label">Ingresar ID de empleado</label>
                  <input id="input-id" type="text" class="form-control" 
                         placeholder="Ingrese el ID y presione Enter...">
                  <div class="form-text">Presiona Enter para marcar automáticamente la asistencia</div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex gap-2 flex-wrap btn-group-responsive">
                    <button type="button" class="btn btn-outline-primary" onclick="marcarTodos()">
                      <i class="bi bi-check-all me-2"></i> Marcar Todos
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="desmarcarTodos()">
                      <i class="bi bi-x-circle me-2"></i> Desmarcar Todos
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de asistencia - Mismo diseño que gestión de usuarios -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">
                <i class="bi bi-table me-2"></i>Lista de Empleados
              </h5>
            </div>
            <div class="card-body p-0">
              <form action="guardarAsistencia.php" method="POST">
                <div class="table-container">
                  <table class="table table-striped table-hover" id="tabla-asistencia">
                    <thead>
                      <tr>
                        <th><i class="bi bi-hash me-1"></i>ID</th>
                        <th><i class="bi bi-person me-1"></i>Nombre</th>
                        <th class="text-center"><i class="bi bi-check-circle me-1"></i>Asistencia</th>
                        <th><i class="bi bi-chat-text me-1"></i>Anotaciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT id, nombre FROM usuarios WHERE estado = 1";
                      $result = $conn->query($sql);
                      $totalEmpleados = 0;
                      
                      if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()):
                          $totalEmpleados++;
                      ?>
                      <tr data-id="<?= $row['id'] ?>">
                        <td class="fw-bold"><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td class="text-center">
                          <div class="form-check form-check-inline d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" 
                                   name="asistencia[<?= $row['id'] ?>]" value="1"
                                   onchange="actualizarContador()">
                          </div>
                        </td>
                        <td>
                          <textarea class="form-control form-textarea" 
                                    name="anotaciones[<?= $row['id'] ?>]" 
                                    rows="1" 
                                    placeholder="Observaciones..."></textarea>
                        </td>
                      </tr>
                      <?php 
                        endwhile;
                      } else {
                        echo '<tr><td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-people display-4 text-muted mb-3"></i><br>
                                No hay empleados activos.
                              </td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>

                <div class="card-footer bg-transparent border-0 p-4">
                  <div class="d-flex gap-2 justify-content-between align-items-center">
                    <button type="submit" class="btn btn-success px-4">
                      <i class="bi bi-check-circle me-2"></i> Guardar Asistencia
                    </button>
                    <div class="text-muted small">
                      <span class="badge bg-primary" id="contador-asistencia">0/<?= $totalEmpleados ?> presentes</span>
                    </div>
                  </div>
                </div>
              </form>
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
    // Inicializar fecha en el título
    window.addEventListener("DOMContentLoaded", () => {
      const hoy = new Date();
      const dia = String(hoy.getDate()).padStart(2, '0');
      const mes = String(hoy.getMonth() + 1).padStart(2, '0');
      const anio = hoy.getFullYear();
      const fechaFormateada = `${dia}-${mes}-${anio}`;
      document.querySelector("h1").innerHTML = `<i class="bi bi-calendar-check me-2"></i>Registro de Asistencia - ${fechaFormateada}`;
      
      // Inicializar contador
      actualizarContador();
    });

    // Búsqueda por ID
    document.getElementById("input-id").addEventListener("keypress", function(e) {
      if (e.key === "Enter") {
        e.preventDefault();
        const id = this.value.trim();
        const fila = document.querySelector(`tr[data-id='${id}']`);
        if (fila) {
          fila.classList.add("present");
          const checkbox = fila.querySelector("input[type='checkbox']");
          checkbox.checked = true;
          checkbox.dispatchEvent(new Event('change'));
          this.value = "";
          
          // Efecto visual
          fila.style.transition = 'all 0.3s ease';
          fila.style.transform = 'scale(1.02)';
          setTimeout(() => {
            fila.style.transform = 'scale(1)';
          }, 300);
        } else {
          alert("❌ ID no encontrado. Verifique el número e intente nuevamente.");
        }
      }
    });

    // Actualizar contador de asistencia
    function actualizarContador() {
      const checkboxes = document.querySelectorAll('input[type="checkbox"]');
      const total = checkboxes.length;
      const marcados = Array.from(checkboxes).filter(cb => cb.checked).length;
      document.getElementById('contador-asistencia').textContent = `${marcados}/${total} presentes`;
    }

    // Marcar todos los empleados
    function marcarTodos() {
      const checkboxes = document.querySelectorAll('input[type="checkbox"]');
      const filas = document.querySelectorAll('tr[data-id]');
      
      checkboxes.forEach(checkbox => {
        checkbox.checked = true;
      });
      
      filas.forEach(fila => {
        fila.classList.add('present');
      });
      
      actualizarContador();
    }

    // Desmarcar todos los empleados
    function desmarcarTodos() {
      const checkboxes = document.querySelectorAll('input[type="checkbox"]');
      const filas = document.querySelectorAll('tr[data-id]');
      
      checkboxes.forEach(checkbox => {
        checkbox.checked = false;
      });
      
      filas.forEach(fila => {
        fila.classList.remove('present');
      });
      
      actualizarContador();
    }

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
    if (urlParams.has('asistenciaGuardada')) {
      mostrarToast("Asistencia guardada correctamente.");
    }
    if (urlParams.has('mensaje')) {
      mostrarToast(urlParams.get('mensaje'));
    }
  </script>
</body>
</html>