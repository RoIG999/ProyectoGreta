<?php
// Servicios(Dueña).php (para panel de dueña - CON base de datos)
session_start();

// Verificar permisos
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Normalizar rol
$rol = $_SESSION['usuario_rol'] ?? '';
$rol_normalizado = mb_strtolower($rol, 'UTF-8');
$rol_normalizado = strtr($rol_normalizado, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n']);

if (!in_array($rol_normalizado, ['duena', 'dueña', 'supervisor', 'admin'])) {
    header('Location: login.php?e=perm');
    exit;
}

$nombre_usuario = htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Dueña', ENT_QUOTES, 'UTF-8');

// Conexión a la base de datos
include("conexion.php");

// VARIABLES PARA MENSAJES
$mensaje = '';
$tipo_mensaje = '';

// PROCESAR FORMULARIOS - SOLO SI HAY DATOS POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    
    $procesado = false;
    
    // AGREGAR NUEVO SERVICIO
    if (isset($_POST['agregar_servicio']) && !empty($_POST['nombre'])) {
        $nombre_servicio = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $duracion = intval($_POST['duracion']);
        
        // Procesar imagen subida
        $imagen = 'img/vacia.webp'; // Imagen por defecto
        
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $directorio_destino = 'img/servicios/';
            
            // Crear directorio si no existe
            if (!is_dir($directorio_destino)) {
                mkdir($directorio_destino, 0755, true);
            }
            
            $nombre_archivo = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $_FILES['imagen']['name']);
            $ruta_completa = $directorio_destino . $nombre_archivo;
            
            // Validar tipo de archivo
            $tipo_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
            $tipo_archivo = mime_content_type($_FILES['imagen']['tmp_name']);
            
            if (in_array($tipo_archivo, $tipo_permitidos)) {
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_completa)) {
                    $imagen = $ruta_completa;
                }
            }
        }
        
        if (!empty($nombre_servicio) && $precio > 0 && $duracion > 0) {
            $sql = "INSERT INTO servicio (nombre, descripcion, precio, duracion, imagen, estado, fecha_alta) 
                    VALUES (?, ?, ?, ?, ?, 1, NOW())";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssdis", $nombre_servicio, $descripcion, $precio, $duracion, $imagen);
                if ($stmt->execute()) {
                    $_SESSION['mensaje'] = "Servicio agregado correctamente";
                    $_SESSION['tipo_mensaje'] = 'success';
                    $procesado = true;
                } else {
                    $_SESSION['mensaje'] = "Error al agregar servicio: " . $stmt->error;
                    $_SESSION['tipo_mensaje'] = 'danger';
                    $procesado = true;
                }
                $stmt->close();
            } else {
                $_SESSION['mensaje'] = "Error en la consulta: " . $conn->error;
                $_SESSION['tipo_mensaje'] = 'danger';
                $procesado = true;
            }
        } else {
            $_SESSION['mensaje'] = "Por favor complete todos los campos requeridos correctamente";
            $_SESSION['tipo_mensaje'] = 'warning';
            $procesado = true;
        }
    }
    
    // EDITAR SERVICIO
    if (isset($_POST['editar_servicio']) && !empty($_POST['servicio_id'])) {
        $servicio_id = intval($_POST['servicio_id']);
        $nombre_servicio = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $duracion = intval($_POST['duracion']);
        
        // Procesar nueva imagen si se subió
        $nueva_imagen = null;
        
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $directorio_destino = 'img/servicios/';
            
            if (!is_dir($directorio_destino)) {
                mkdir($directorio_destino, 0755, true);
            }
            
            $nombre_archivo = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $_FILES['imagen']['name']);
            $ruta_completa = $directorio_destino . $nombre_archivo;
            
            $tipo_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
            $tipo_archivo = mime_content_type($_FILES['imagen']['tmp_name']);
            
            if (in_array($tipo_archivo, $tipo_permitidos)) {
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_completa)) {
                    $nueva_imagen = $ruta_completa;
                }
            }
        }
        
        if (!empty($nombre_servicio) && $precio > 0 && $duracion > 0) {
            if ($nueva_imagen) {
                // Actualizar con nueva imagen
                $sql = "UPDATE servicio SET nombre = ?, descripcion = ?, precio = ?, duracion = ?, imagen = ?, fecha_modificacion = NOW() 
                        WHERE ID = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("ssdisi", $nombre_servicio, $descripcion, $precio, $duracion, $nueva_imagen, $servicio_id);
                }
            } else {
                // Mantener imagen actual
                $sql = "UPDATE servicio SET nombre = ?, descripcion = ?, precio = ?, duracion = ?, fecha_modificacion = NOW() 
                        WHERE ID = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("ssdii", $nombre_servicio, $descripcion, $precio, $duracion, $servicio_id);
                }
            }
            
            if ($stmt && $stmt->execute()) {
                $_SESSION['mensaje'] = "Servicio actualizado correctamente";
                $_SESSION['tipo_mensaje'] = 'success';
                $procesado = true;
            } else {
                $_SESSION['mensaje'] = "Error al actualizar servicio: " . ($stmt ? $stmt->error : $conn->error);
                $_SESSION['tipo_mensaje'] = 'danger';
                $procesado = true;
            }
            if ($stmt) $stmt->close();
        } else {
            $_SESSION['mensaje'] = "Por favor complete todos los campos requeridos correctamente";
            $_SESSION['tipo_mensaje'] = 'warning';
            $procesado = true;
        }
    }
    
    // ELIMINAR/ACTIVAR/DESACTIVAR SERVICIO
    if (isset($_POST['cambiar_estado']) && !empty($_POST['servicio_id']) && isset($_POST['nuevo_estado'])) {
        $servicio_id = intval($_POST['servicio_id']);
        $nuevo_estado = intval($_POST['nuevo_estado']);
        
        // Validar que el estado sea 0 o 1
        if ($nuevo_estado === 0 || $nuevo_estado === 1) {
            $sql = "UPDATE servicio SET estado = ?, fecha_modificacion = NOW() WHERE ID = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ii", $nuevo_estado, $servicio_id);
                if ($stmt->execute()) {
                    $accion = $nuevo_estado == 1 ? 'activado' : 'desactivado';
                    $_SESSION['mensaje'] = "Servicio $accion correctamente";
                    $_SESSION['tipo_mensaje'] = 'success';
                    $procesado = true;
                } else {
                    $_SESSION['mensaje'] = "Error al cambiar estado: " . $stmt->error;
                    $_SESSION['tipo_mensaje'] = 'danger';
                    $procesado = true;
                }
                $stmt->close();
            } else {
                $_SESSION['mensaje'] = "Error en la consulta: " . $conn->error;
                $_SESSION['tipo_mensaje'] = 'danger';
                $procesado = true;
            }
        }
    }
    
    // REDIRIGIR después de procesar POST para evitar reenvío
    if ($procesado) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// OBTENER MENSAJES DE SESIÓN (si existen)
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'];
    // Limpiar mensajes después de mostrarlos
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
}

// OBTENER SERVICIOS DE LA BASE DE DATOS
$servicios = [];
$sql = "SELECT * FROM servicio ORDER BY estado DESC, nombre ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $servicios[] = $row;
    }
}

// Obtener estadísticas para las tarjetas
$totalServicios = count($servicios);
$serviciosActivos = 0;
$serviciosInactivos = 0;

foreach ($servicios as $servicio) {
    if ($servicio['estado'] == 1) {
        $serviciosActivos++;
    } else {
        $serviciosInactivos++;
    }
}

// Cerrar conexión
$conn->close();

// SISTEMA MEJORADO DE IMÁGENES - POR NOMBRE ORIGINAL
$imagenes_servicios = [
    // Por nombre (para servicios que no han cambiado de nombre)
    'Bronceado' => 'img/bronceado 2.png',
    'Faciales' => 'img/faciales.png',
    'Esculpidas' => 'img/uñas 11.png', 
    'Pestañas' => 'img/Pestañas.jpg',
    'Perfilado de Cejas' => 'img/laminado.jpg',
    'Microblading' => 'img/microblanding 2.png',
    'Semipermanente' => 'img/semipermanente.jpg',
    'Kapping' => 'img/kapping.jpg',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Servicios - GRETA</title>
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
      --warning: #f72617ff;
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
    
    /* Tarjetas de estadísticas - IGUAL AL PANEL DE USUARIOS */
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
    
    .btn-danger {
      background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
      border: none;
      border-radius: 10px;
      font-weight: 500;
      padding: 10px 20px;
      transition: all 0.3s ease;
    }
    
    .btn-danger:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
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
    
    /* Alertas */
    .alert {
      border-radius: 12px;
      border: none;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      padding: 16px 20px;
    }
    
    /* Grid de servicios */
    .servicios-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 24px;
      padding: 0;
    }

    .servicio-card {
      background: var(--background-white);
      border-radius: 16px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
      overflow: hidden;
      position: relative;
      display: flex;
      flex-direction: column;
      transition: all 0.3s ease;
      border: 1px solid var(--border-light);
      padding: 0;
      height: 100%;
    }
    
    .servicio-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .servicio-card .thumb {
      width: 100%;
      aspect-ratio: 4 / 3;
      overflow: hidden;
      background: var(--background-light);
      margin: 0;
      padding: 0;
    }

    .servicio-card .thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform 0.3s ease;
      vertical-align: bottom;
    }
    
    .servicio-card:hover .thumb img {
      transform: scale(1.05);
    }
    
    .servicio-card h3 {
      margin: 14px 16px 6px;
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--text-dark);
    }
    
    .servicio-card p {
      margin: 0 16px 12px;
      color: var(--text-light);
      font-size: 0.9rem;
      line-height: 1.4;
      flex-grow: 1;
    }
    
    .servicio-card .precio {
      margin: 0 16px 8px;
      font-weight: bold;
      color: var(--success);
      font-size: 1.2rem;
    }
    
    .servicio-card .acciones {
      margin: 0 16px 16px;
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }
    
    .servicio-card .badge {
      position: absolute;
      top: 12px;
      left: 12px;
      z-index: 2;
      font-size: 0.75rem;
      margin: 0;
    }
    
    .duracion-badge {
      background: var(--border-light);
      color: var(--text-medium);
      font-size: 0.8rem;
      padding: 0.25rem 0.5rem;
      border-radius: 0.375rem;
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
    
    /* Upload de imagen MEJORADO Y MÁS COMPACTO */
    .upload-area {
      border: 2px dashed var(--border-light);
      border-radius: 12px;
      padding: 1.5rem;
      text-align: center;
      transition: all 0.3s ease;
      background: var(--background-light);
      cursor: pointer;
    }
    
    .upload-area:hover, .upload-area.dragover {
      border-color: var(--accent-medium);
      background: var(--accent-pastel);
    }
    
    .upload-area i {
      font-size: 2rem;
      color: var(--primary-light);
      margin-bottom: 0.75rem;
    }
    
    .upload-area.dragover i {
      color: var(--accent-medium);
    }
    
    .image-preview {
      max-width: 100%;
      max-height: 150px;
      border-radius: 8px;
      display: none;
      margin: 0 auto;
    }
    
    .upload-text {
      color: var(--text-medium);
      margin-bottom: 0.25rem;
      font-size: 0.9rem;
    }
    
    .upload-hint {
      color: var(--text-light);
      font-size: 0.8rem;
    }
    
    /* Modales más compactos */
    .modal-content {
      border-radius: 16px;
      border: none;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .modal-header {
      border-bottom: 1px solid var(--border-light);
      padding: 1.25rem 1.5rem;
      border-radius: 16px 16px 0 0;
    }
    
    .modal-body {
      padding: 1.5rem;
    }
    
    .modal-footer {
      border-top: 1px solid var(--border-light);
      padding: 1.25rem 1.5rem;
      border-radius: 0 0 16px 16px;
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
    }
    
    @media (max-width: 768px) {
      .main-content {
        padding: 15px;
      }
      
      .servicios-grid {
        grid-template-columns: 1fr;
        gap: 16px;
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
      
      .upload-area {
        padding: 1rem;
      }
      
      .modal-body {
        padding: 1.25rem;
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
      
      .modal-header,
      .modal-footer {
        padding: 1rem 1.25rem;
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
        GRETA · Gestión de Servicios
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
            <a class="nav-link" href="Historial.php">
              <i class="bi bi-calendar-check me-2"></i>Asistencias
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="Servicios(Dueña).php">
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
        <a class="nav-link" href="Historial.php">
          <i class="bi bi-calendar-check me-2"></i> Asistencias
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="Servicios(Dueña).php">
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
                  <i class="bi bi-scissors me-2"></i>Gestión de Servicios
                </h1>
                <p class="text-white mb-0 opacity-75">Administra los servicios disponibles en el establecimiento</p>
              </div>
              <div class="col-md-4 text-end">
                <i class="bi bi-scissors display-4 opacity-25"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tarjetas de estadísticas - 3 TARJETAS DISTRIBUIDAS -->
      <div class="row mb-4">
        <!-- Tarjeta Total Servicios -->
        <div class="col-12 col-md-4 mb-3">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1">Total Servicios</h6>
                  <h3 class="card-title mb-0"><?= $totalServicios ?></h3>
                  <p class="small mb-0">Todos los servicios registrados</p>
                </div>
                <i class="bi bi-scissors"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta Servicios Activos -->
        <div class="col-12 col-md-4 mb-3">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1">Servicios Activos</h6>
                  <h3 class="card-title mb-0"><?= $serviciosActivos ?></h3>
                  <p class="small mb-0">Disponibles para turnos</p>
                  <?php if ($totalServicios > 0): ?>
                    <div class="progress mt-2">
                      <div class="progress-bar bg-success" role="progressbar" 
                           style="width: <?= round(($serviciosActivos / $totalServicios) * 100) ?>%" 
                           aria-valuenow="<?= round(($serviciosActivos / $totalServicios) * 100) ?>" 
                           aria-valuemin="0" 
                           aria-valuemax="100">
                      </div>
                    </div>
                    <small class="text-muted"><?= round(($serviciosActivos / $totalServicios) * 100) ?>% del total</small>
                  <?php endif; ?>
                </div>
<i class="bi bi-check-circle" style="color: var(--success);"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta Servicios Inactivos -->
        <div class="col-12 col-md-4 mb-3">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1">Servicios Inactivos</h6>
                  <h3 class="card-title mb-0"><?= $serviciosInactivos ?></h3>
                  <p class="small mb-0">No disponibles actualmente</p>
                  <?php if ($totalServicios > 0): ?>
                    <div class="progress mt-2">
                      <div class="progress-bar bg-warning" role="progressbar" 
                           style="width: <?= round(($serviciosInactivos / $totalServicios) * 100) ?>%" 
                           aria-valuenow="<?= round(($serviciosInactivos / $totalServicios) * 100) ?>" 
                           aria-valuemin="0" 
                           aria-valuemax="100">
                      </div>
                    </div>
                    <small class="text-muted"><?= round(($serviciosInactivos / $totalServicios) * 100) ?>% del total</small>
                  <?php endif; ?>
                </div>
                <i class="bi bi-eye-slash" style="color: var(--warning);"></i></i>
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
                <i class="bi bi-list-check me-2"></i>Servicios Activos
              </h5>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarServicio">
                <i class="bi bi-plus-circle me-2"></i> Agregar Servicio
              </button>
              <a href="Servicios.php" class="btn btn-outline-primary" target="_blank">
                <i class="bi bi-eye me-2"></i> Ver vista pública
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Alertas -->
      <?php if ($mensaje): ?>
        <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
          <i class="bi bi-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
          <?= $mensaje ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Grid de servicios -->
      <div class="row">
        <div class="col-12">
          <?php if (empty($servicios)): ?>
            <div class="text-center py-5">
              <i class="bi bi-scissors display-1 text-muted mb-3"></i>
              <h4 class="text-muted">No hay servicios registrados</h4>
              <p class="text-muted">Comienza agregando tu primer servicio</p>
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarServicio">
                <i class="bi bi-plus-circle me-2"></i> Agregar Primer Servicio
              </button>
            </div>
          <?php else: ?>
            <div class="servicios-grid" id="servicios-container">
              <?php foreach ($servicios as $servicio): ?>
                <?php
                  $precio = number_format($servicio['precio'], 0, ',', '.');
                  $estadoClass = $servicio['estado'] == 1 ? 'success' : 'secondary';
                  $estadoTexto = $servicio['estado'] == 1 ? 'Activo' : 'Inactivo';
                  // Asignar duración por defecto si no existe en la BD
                  $duracion = isset($servicio['duracion']) ? $servicio['duracion'] : 60;
                  
                  // SISTEMA MEJORADO DE IMÁGENES
                  $imagen_final = 'img/vacia.webp'; // Imagen por defecto
                  
                  // Estrategia 1: Imagen desde la base de datos (si no es vacia.webp)
                  if (!empty($servicio['imagen']) && $servicio['imagen'] !== 'img/vacia.webp') {
                      $imagen_final = $servicio['imagen'];
                  } 
                  // Estrategia 2: Imagen por nombre del servicio (para servicios existentes)
                  elseif (isset($imagenes_servicios[$servicio['nombre']])) {
                      $imagen_final = $imagenes_servicios[$servicio['nombre']];
                  }
                ?>
                <div class="servicio-card">
                  <span class="badge bg-<?= $estadoClass ?>"><?= $estadoTexto ?></span>
                  <div class="thumb">
                    <img src="<?= $imagen_final ?>" 
                         alt="<?= htmlspecialchars($servicio['nombre']) ?>" 
                         loading="lazy" 
                         decoding="async"
                         onerror="this.src='img/vacia.webp'">
                  </div>
                  <h3><?= htmlspecialchars($servicio['nombre']) ?></h3>
                  <p><?= htmlspecialchars($servicio['descripcion'] ?: 'Sin descripción') ?></p>
                  <div class="precio">$<?= $precio ?></div>
                  <div class="d-flex justify-content-between align-items-center px-3 mb-2">
                    <span class="duracion-badge">⏱️ <?= $duracion ?> min</span>
                  </div>
                  <div class="acciones">
                    <button class="btn btn-sm btn-warning" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalEditarServicio"
                            onclick="cargarDatosEdicion(<?= htmlspecialchars(json_encode($servicio)) ?>)">
                      <i class="bi bi-pencil"></i> Editar
                    </button>
                    <?php if ($servicio['estado'] == 1): ?>
                      <form method="POST" class="d-inline" onsubmit="return confirmarCambioEstado(this, 'desactivar')">
                        <input type="hidden" name="servicio_id" value="<?= $servicio['ID'] ?>">
                        <input type="hidden" name="nuevo_estado" value="0">
                        <button type="submit" name="cambiar_estado" class="btn btn-sm btn-danger">
                          <i class="bi bi-eye-slash"></i> Desactivar
                        </button>
                      </form>
                    <?php else: ?>
                      <form method="POST" class="d-inline" onsubmit="return confirmarCambioEstado(this, 'activar')">
                        <input type="hidden" name="servicio_id" value="<?= $servicio['ID'] ?>">
                        <input type="hidden" name="nuevo_estado" value="1">
                        <button type="submit" name="cambiar_estado" class="btn btn-sm btn-success">
                          <i class="bi bi-eye"></i> Activar
                        </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <footer class="text-center py-4 mt-4">
    <small>© <?= date('Y'); ?> GRETA Estética · Todos los derechos reservados</small>
  </footer>

  <!-- Modal Agregar Servicio - MÁS COMPACTO -->
  <div class="modal fade" id="modalAgregarServicio" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-plus-circle me-2"></i>Agregar Nuevo Servicio
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" id="formAgregarServicio" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Nombre del Servicio *</label>
                <input type="text" class="form-control" name="nombre" required maxlength="100">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Precio ($) *</label>
                <input type="number" class="form-control" name="precio" step="0.01" min="0" required>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Duración (minutos) *</label>
                <input type="number" class="form-control" name="duracion" min="1" value="60" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Imagen del Servicio</label>
              <div class="upload-area" id="uploadAreaAgregar">
                <i class="bi bi-cloud-upload"></i>
                <p class="upload-text">Arrastra una imagen o haz clic para subir</p>
                <p class="upload-hint">Formatos: JPG, PNG, WEBP, GIF (Máx. 5MB)</p>
                <input type="file" name="imagen" id="imagenInputAgregar" accept="image/*" style="display: none;">
                <img id="imagePreviewAgregar" class="image-preview">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea class="form-control" name="descripcion" rows="2" maxlength="500"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" name="agregar_servicio" class="btn btn-primary">Guardar Servicio</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Editar Servicio - MÁS COMPACTO -->
  <div class="modal fade" id="modalEditarServicio" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-pencil me-2"></i>Editar Servicio
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" id="formEditarServicio" enctype="multipart/form-data">
          <input type="hidden" name="servicio_id" id="editar_servicio_id">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Nombre del Servicio *</label>
                <input type="text" class="form-control" name="nombre" id="editar_nombre" required maxlength="100">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Precio ($) *</label>
                <input type="number" class="form-control" name="precio" id="editar_precio" step="0.01" min="0" required>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Duración (minutos) *</label>
                <input type="number" class="form-control" name="duracion" id="editar_duracion" min="1" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Imagen del Servicio</label>
              <div class="upload-area" id="uploadAreaEditar">
                <i class="bi bi-cloud-upload"></i>
                <p class="upload-text">Arrastra una imagen o haz clic para cambiar</p>
                <p class="upload-hint">Formatos: JPG, PNG, WEBP, GIF (Máx. 5MB)</p>
                <input type="file" name="imagen" id="imagenInputEditar" accept="image/*" style="display: none;">
                <img id="imagePreviewEditar" class="image-preview">
              </div>
              <div class="form-text">Deja vacío para mantener la imagen actual</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea class="form-control" name="descripcion" id="editar_descripcion" rows="2" maxlength="500"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" name="editar_servicio" class="btn btn-primary">Actualizar Servicio</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Toggle sidebar en vista móvil
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      document.querySelector('.sidebar').classList.toggle('show');
    });

    // Función para cargar datos en el modal de edición
    function cargarDatosEdicion(servicio) {
      document.getElementById('editar_servicio_id').value = servicio.ID;
      document.getElementById('editar_nombre').value = servicio.nombre;
      document.getElementById('editar_descripcion').value = servicio.descripcion || '';
      document.getElementById('editar_precio').value = servicio.precio;
      document.getElementById('editar_duracion').value = servicio.duracion || 60;
      
      // Limpiar preview de imagen al editar
      document.getElementById('imagePreviewEditar').style.display = 'none';
      document.getElementById('imagenInputEditar').value = '';
    }

    // Función mejorada para confirmar cambio de estado
    function confirmarCambioEstado(form, accion) {
      const servicioNombre = form.closest('.servicio-card').querySelector('h3').textContent;
      const confirmacion = confirm(`¿Estás segura de que deseas ${accion} el servicio "${servicioNombre}"?`);
      
      if (confirmacion) {
        // Agregar un timestamp único para prevenir envíos duplicados
        const timestamp = new Date().getTime();
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'timestamp';
        hiddenInput.value = timestamp;
        form.appendChild(hiddenInput);
        
        return true;
      }
      return false;
    }

    // Sistema de upload de imágenes con drag & drop
    function initializeImageUpload(uploadAreaId, fileInputId, previewId) {
      const uploadArea = document.getElementById(uploadAreaId);
      const fileInput = document.getElementById(fileInputId);
      const imagePreview = document.getElementById(previewId);

      // Click en el área de upload
      uploadArea.addEventListener('click', () => {
        fileInput.click();
      });

      // Cambio en el input de archivo
      fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
          const file = e.target.files[0];
          previewImage(file, imagePreview, uploadArea);
        }
      });

      // Drag & drop
      uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
      });

      uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
      });

      uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        if (e.dataTransfer.files.length > 0) {
          const file = e.dataTransfer.files[0];
          fileInput.files = e.dataTransfer.files;
          previewImage(file, imagePreview, uploadArea);
        }
      });
    }

    // Función para previsualizar imagen
    function previewImage(file, imagePreview, uploadArea) {
      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        
        reader.onload = (e) => {
          imagePreview.src = e.target.result;
          imagePreview.style.display = 'block';
          
          // Ocultar elementos del upload area
          const uploadIcon = uploadArea.querySelector('i');
          const uploadText = uploadArea.querySelector('.upload-text');
          const uploadHint = uploadArea.querySelector('.upload-hint');
          
          if (uploadIcon) uploadIcon.style.display = 'none';
          if (uploadText) uploadText.style.display = 'none';
          if (uploadHint) uploadHint.style.display = 'none';
        };
        
        reader.readAsDataURL(file);
      } else {
        alert('Por favor, selecciona un archivo de imagen válido.');
      }
    }

    // Inicializar upload de imágenes para ambos modales
    document.addEventListener('DOMContentLoaded', function() {
      initializeImageUpload('uploadAreaAgregar', 'imagenInputAgregar', 'imagePreviewAgregar');
      initializeImageUpload('uploadAreaEditar', 'imagenInputEditar', 'imagePreviewEditar');

      // Deshabilitar envío automático por Enter en formularios
      const forms = document.querySelectorAll('form');
      forms.forEach(form => {
        form.addEventListener('keypress', function(e) {
          if (e.key === 'Enter' && e.target.type !== 'textarea') {
            e.preventDefault();
          }
        });
      });
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
    if (urlParams.has('mensaje')) {
      mostrarToast(urlParams.get('mensaje'));
    }
  </script>
</body>
</html>