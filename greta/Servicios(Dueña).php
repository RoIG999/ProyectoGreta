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

$nombre = htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Dueña', ENT_QUOTES, 'UTF-8');

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
        $imagen = trim($_POST['imagen'] ?? '');
        
        // Si no se ingresó imagen, usar vacia.webp
        if (empty($imagen)) {
            $imagen = 'img/vacia.webp';
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
        $imagen = trim($_POST['imagen'] ?? '');
        
        if (!empty($nombre_servicio) && $precio > 0 && $duracion > 0) {
            // Si se ingresó una nueva imagen, actualizarla
            if (!empty($imagen)) {
                $sql = "UPDATE servicio SET nombre = ?, descripcion = ?, precio = ?, duracion = ?, imagen = ?, fecha_modificacion = NOW() 
                        WHERE ID = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("ssdisi", $nombre_servicio, $descripcion, $precio, $duracion, $imagen, $servicio_id);
                }
            } else {
                // Si no se ingresó imagen, mantener la actual
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

// Cerrar conexión
$conn->close();

// ... el resto de tu código se mantiene igual ...


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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .navbar-nav .nav-link { color: white !important; }
    .table-hover tbody tr:hover { cursor: pointer; background-color: #f5f5f5; }
    .card-service { transition: transform 0.2s; }
    .card-service:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    
    /* Estilos de la vista pública */
    .servicios-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 24px;
      padding: 0 16px 40px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .servicio-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    position: relative;
    display: flex;
    flex-direction: column;
    transition: transform .2s ease, box-shadow .2s ease;
    border: 1px solid #e9ecef;
    /* ELIMINAR padding interno que pueda estar causando espacio */
    padding: 0;
}
    

    .servicio-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 25px rgba(0,0,0,0.15);
    }
    
    .servicio-card .thumb {
    width: 100%;
    aspect-ratio: 4 / 3;
    overflow: hidden;
    background: #f8f9fa;
    /* ELIMINAR cualquier margen o padding que pueda estar causando la línea */
    margin: 0;
    padding: 0;
}

.servicio-card .thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .3s ease;
    /* Asegurar que no haya espacios */
    vertical-align: bottom;
}

    
    .servicio-card:hover .thumb img {
      transform: scale(1.05);
    }
    
    .servicio-card h3 {
    margin: 14px 16px 6px;
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
}
    .servicio-card p {
    margin: 0 16px 12px;
    color: #6c757d;
    font-size: 0.9rem;
    line-height: 1.4;
    min-height: 40px;
}
    
    .servicio-card .precio {
      margin: 0 16px 8px;
      font-weight: bold;
      color: #28a745;
      font-size: 1.2rem;
    }
    
    .servicio-card .acciones {
      margin: 0 16px 16px;
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }
    
    .servicio-card .badge {
    /* Posicionar el badge absolutamente sobre la imagen */
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 2;
    font-size: 0.75rem;
    margin: 0;
}
    
    .admin-header {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 16px;
    }

    .duracion-badge {
      background: #e9ecef;
      color: #495057;
      font-size: 0.8rem;
      padding: 0.25rem 0.5rem;
      border-radius: 0.375rem;
    }
    
    /* Prevenir envío automático de formularios */
    form button[type="submit"] {
      position: relative;
      z-index: 1;
    }
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
        <li class="nav-item"><a class="nav-link" href="Panel-dueña.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="gestionUsuarios.php">Usuarios</a></li>
        <li class="nav-item"><a class="nav-link" href="Historial.php">Asistencias</a></li>
        <li class="nav-item"><a class="nav-link active" href="Servicios(Dueña).php">Servicios</a></li>
        <li class="nav-item"><a class="nav-link" href="gestion-turnos-dueña.php">Turnos</a></li>
      </ul>
      <ul class="navbar-nav d-flex flex-row gap-3">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" title="Usuario actual">
            <i class="bi bi-person-circle fs-5"></i> <?= $nombre ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">🔔 Notificaciones</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="logout.php">🚪 Cerrar sesión</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="admin-header">
    <!-- Mensajes -->
    <?php if ($mensaje): ?>
      <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
        <?= $mensaje ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Gestión de Servicios</h2>
      <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarServicio">
          <i class="bi bi-plus-circle"></i> Agregar Servicio
        </button>
        <a href="Servicios.php" class="btn btn-outline-dark" target="_blank">
          <i class="bi bi-eye"></i> Ver vista pública
        </a>
      </div>
    </div>
  </div>

  <!-- Mostrar servicios en cards -->
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
</div>

<!-- Los modales se mantienen igual -->
<!-- Modal Agregar Servicio -->
<div class="modal fade" id="modalAgregarServicio" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Agregar Nuevo Servicio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="formAgregarServicio">
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
            <div class="col-md-6 mb-3">
              <label class="form-label">URL de Imagen</label>
              <input type="url" class="form-control" name="imagen" placeholder="https://...">
              <div class="form-text">Deja vacío para usar imagen por defecto</div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="3" maxlength="500"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="agregar_servicio" class="btn btn-primary">Guardar Servicio</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Editar Servicio -->
<div class="modal fade" id="modalEditarServicio" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Servicio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="formEditarServicio">
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
            <div class="col-md-6 mb-3">
              <label class="form-label">URL de Imagen</label>
              <input type="url" class="form-control" name="imagen" id="editar_imagen" placeholder="https://...">
              <div class="form-text">Solo completa si quieres cambiar la imagen actual</div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" id="editar_descripcion" rows="3" maxlength="500"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="editar_servicio" class="btn btn-primary">Actualizar Servicio</button>
        </div>
      </form>
    </div>
  </div>
</div>

<footer class="text-white text-center p-3 mt-5" style="background-color:black;">
  <p>&copy; 2025 Grupo GRETA | Contacto: GIA.com</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Función mejorada para prevenir envíos duplicados
function confirmarCambioEstado(form, accion) {
    const servicioNombre = form.closest('.servicio-card').querySelector('h3').textContent;
    const confirmacion = confirm(`¿Estás segura de que deseas ${accion} el servicio "${servicioNombre}"?`);
    
    if (confirmacion) {
        // Deshabilitar el botón para prevenir múltiples clics
        const boton = form.querySelector('button[type="submit"]');
        boton.disabled = true;
        boton.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';
        
        return true;
    }
    return false;
}

// Prevenir envío automático de formularios
document.addEventListener('DOMContentLoaded', function() {
    // Deshabilitar envío automático por Enter en formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && e.target.type !== 'textarea') {
                e.preventDefault();
            }
        });
        
        // Restaurar botones deshabilitados al cerrar modales
        form.addEventListener('reset', function() {
            const botones = form.querySelectorAll('button[type="submit"]');
            botones.forEach(boton => {
                boton.disabled = false;
            });
        });
    });
});
// Función para cargar datos en el modal de edición
function cargarDatosEdicion(servicio) {
    document.getElementById('editar_servicio_id').value = servicio.ID;
    document.getElementById('editar_nombre').value = servicio.nombre;
    document.getElementById('editar_descripcion').value = servicio.descripcion || '';
    document.getElementById('editar_precio').value = servicio.precio;
    document.getElementById('editar_duracion').value = servicio.duracion || 60;
    
    // Solo cargar imagen si no es la por defecto
    if (servicio.imagen && servicio.imagen !== 'img/vacia.webp') {
        document.getElementById('editar_imagen').value = servicio.imagen;
    } else {
        document.getElementById('editar_imagen').value = '';
    }
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

// Prevenir envío automático de formularios
document.addEventListener('DOMContentLoaded', function() {
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
</script>
</body>
</html>