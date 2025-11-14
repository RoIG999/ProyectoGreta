<?php
// greta/modificarUsuario.php
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
  <title>Modificar Usuario - GRETA</title>
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
    
    /* Formularios */
    .form-container {
      max-width: 720px;
      margin: 0 auto;
    }
    
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
    
    .form-text {
      color: var(--text-light);
      font-size: 0.85rem;
    }
    
    .input-group .btn {
      border-radius: 0 10px 10px 0;
      border: 2px solid var(--border-light);
      border-left: none;
    }
    
    .input-group .form-control {
      border-radius: 10px 0 0 10px;
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
    
    .badge-primary {
      background-color: var(--primary-main);
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
      
      .form-container {
        max-width: 100%;
      }
      
      .header-actions {
        flex-direction: column;
        gap: 15px;
      }
      
      .header-actions .btn {
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
        GRETA · Modificar Usuario
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
      <div class="form-container">
        <!-- Encabezado con botón al lado -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center header-actions">
              <div>
                <h1 class="h3 mb-2 fw-bold text-dark">
                  <i class="bi bi-person-gear me-2"></i>Modificar Usuario
                </h1>
                <p class="text-muted">Actualiza los datos del usuario seleccionado</p>
              </div>
              <a href="gestionUsuarios.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i> Volver a Usuarios
              </a>
            </div>
          </div>
        </div>

        <!-- Formulario -->
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">
              <i class="bi bi-person-gear me-2"></i>Editar Datos del Usuario
            </h5>
          </div>
          <div class="card-body">
            <form action="modificarUsuario.php" method="POST">
              <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">
              
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label for="nombre" class="form-label">Nombre completo</label>
                  <input type="text" class="form-control" id="nombre" name="nombre" 
                         value="<?= htmlspecialchars($usuario['nombre']) ?>" required
                         placeholder="Ingresa el nombre completo del usuario">
                </div>

                <div class="col-md-6 mb-3">
                  <label for="usuario" class="form-label">Usuario</label>
                  <input type="text" class="form-control" id="usuario" name="usuario" 
                         value="<?= htmlspecialchars($usuario['usuario']) ?>" required
                         placeholder="Nombre de usuario único">
                </div>

                <div class="col-md-6 mb-3">
                  <label for="clave" class="form-label">Contraseña</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="clave" name="clave" 
                           placeholder="Solo si desea cambiarla">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePass()">
                      <i class="bi bi-eye" id="eye-icon"></i>
                    </button>
                  </div>
                  <div class="form-text">Dejar vacío para mantener la contraseña actual</div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="rol" class="form-label">Rol</label>
                  <select class="form-select" id="rol" name="rol" required>
                    <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    <option value="Supervisor" <?= $usuario['rol'] === 'Supervisor' ? 'selected' : '' ?>>Supervisor</option>
                    <option value="empleado" <?= $usuario['rol'] === 'empleado' ? 'selected' : '' ?>>Empleado</option>
                  </select>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="form-check h-100 d-flex align-items-center">
                    <input class="form-check-input me-2" type="checkbox" id="estado" name="estado" 
                           <?= ((int)$usuario['estado'] === 1) ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="estado">
                      Usuario Activo
                    </label>
                  </div>
                  <div class="form-text">Desmarcar para desactivar el usuario</div>
                </div>

                <div class="col-12 mt-4">
                  <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning flex-fill py-2">
                      <i class="bi bi-check-circle me-2"></i> Guardar Cambios
                    </button>
                    <a href="gestionUsuarios.php" class="btn btn-outline-secondary py-2">
                      <i class="bi bi-x-circle me-2"></i> Cancelar
                    </a>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Información del usuario -->
        <div class="card mt-4">
          <div class="card-header">
            <h6 class="card-title mb-0">
              <i class="bi bi-info-circle me-2"></i>Información del Usuario
            </h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <p class="mb-1"><strong>ID:</strong> <?= htmlspecialchars($usuario['id']) ?></p>
                <p class="mb-1"><strong>Estado actual:</strong> 
                  <span class="badge <?= ((int)$usuario['estado'] === 1) ? 'bg-success' : 'bg-secondary' ?>">
                    <?= ((int)$usuario['estado'] === 1) ? 'Activo' : 'Inactivo' ?>
                  </span>
                </p>
              </div>
              <div class="col-md-6">
                <p class="mb-1"><strong>Rol actual:</strong> 
                  <span class="badge bg-primary"><?= htmlspecialchars(ucfirst($usuario['rol'])) ?></span>
                </p>
                <p class="mb-0"><strong>Usuario:</strong> <?= htmlspecialchars($usuario['usuario']) ?></p>
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

    // Mostrar/ocultar contraseña
    function togglePass() {
      const input = document.getElementById('clave');
      const icon = document.getElementById('eye-icon');
      
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    }

    // Validación del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
      const nombre = document.getElementById('nombre').value.trim();
      const usuario = document.getElementById('usuario').value.trim();
      const rol = document.getElementById('rol').value;

      if (!nombre || !usuario || !rol) {
        e.preventDefault();
        alert('Por favor, completa todos los campos obligatorios.');
        return false;
      }
    });

    // Efecto de enfoque en campos del formulario
    document.querySelectorAll('.form-control, .form-select').forEach(element => {
      element.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
      });
      
      element.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
      });
    });
  </script>
</body>
</html>