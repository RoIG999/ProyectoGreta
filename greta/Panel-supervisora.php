<?php
// panel-supervisora.php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Normalizar rol: minúsculas y sin tildes
$rol = $_SESSION['usuario_rol'] ?? '';
$rol = mb_strtolower($rol, 'UTF-8');
$rol = strtr($rol, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n']);

// Solo supervisoras pueden acceder
if ($rol !== 'supervisor' && $rol !== 'supervisora') {
    header('Location: login.php?e=perm');
    exit;
}

$nombre = htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Supervisora', ENT_QUOTES, 'UTF-8');

// Conexión a la base de datos para obtener datos reales
include("conexion.php");

// Obtener datos reales
$turnos_hoy = 0;
$empleados_activos = 0;
$asistencias_hoy = 0;

// Consultas reales a la base de datos
$hoy = date('Y-m-d');

// Turnos de hoy
$sql_turnos = "SELECT COUNT(*) as total FROM turno WHERE fecha = '$hoy' AND ID_estado_turno_FK != 6"; // Excluye cancelados
if ($result = $conn->query($sql_turnos)) {
    $row = $result->fetch_assoc();
    $turnos_hoy = $row['total'];
}

// Empleados activos
$sql_empleados = "SELECT COUNT(*) as total FROM usuarios WHERE estado = 1 AND rol IN ('empleado', 'empleada')";
if ($result = $conn->query($sql_empleados)) {
    $row = $result->fetch_assoc();
    $empleados_activos = $row['total'];
}

// Asistencias de hoy
$sql_asistencias = "SELECT COUNT(*) as total FROM asistencias WHERE fecha = '$hoy' AND asistencia = 1";
if ($result = $conn->query($sql_asistencias)) {
    $row = $result->fetch_assoc();
    $asistencias_hoy = $row['total'];
}

// Obtener actividad reciente - Últimos 5 turnos agendados
$actividad_turnos = [];
$sql_actividad_turnos = "SELECT t.nombre_cliente, t.apellido_cliente, t.fecha, t.hora, rs.nombre as servicio 
                         FROM turno t 
                         LEFT JOIN rubro_servicio rs ON t.ID_servicio_FK = rs.ID 
                         ORDER BY t.fecha DESC, t.hora DESC 
                         LIMIT 3";
if ($result = $conn->query($sql_actividad_turnos)) {
    while ($row = $result->fetch_assoc()) {
        $actividad_turnos[] = $row;
    }
}

// Obtener actividad reciente - Últimas 5 asistencias
$actividad_asistencias = [];
$sql_actividad_asistencias = "SELECT a.fecha, u.nombre, a.asistencia, a.anotaciones 
                              FROM asistencias a 
                              LEFT JOIN usuarios u ON a.id_usuario = u.id 
                              ORDER BY a.fecha DESC, a.id DESC 
                              LIMIT 2";
if ($result = $conn->query($sql_actividad_asistencias)) {
    while ($row = $result->fetch_assoc()) {
        $actividad_asistencias[] = $row;
    }
}

// Combinar y ordenar actividad reciente
$actividad_reciente = [];

// Agregar turnos a la actividad
foreach ($actividad_turnos as $turno) {
    $fecha_turno = new DateTime($turno['fecha'] . ' ' . $turno['hora']);
    $actividad_reciente[] = [
        'tipo' => 'turno',
        'titulo' => 'Nuevo turno agendado',
        'descripcion' => $turno['nombre_cliente'] . ' ' . $turno['apellido_cliente'] . ' - ' . $turno['servicio'],
        'fecha' => $fecha_turno,
        'badge' => 'info',
        'icono' => 'bi-calendar-plus'
    ];
}

// Agregar asistencias a la actividad
foreach ($actividad_asistencias as $asistencia) {
    $fecha_asistencia = new DateTime($asistencia['fecha']);
    $estado = $asistencia['asistencia'] ? 'Presente' : 'Ausente';
    $badge = $asistencia['asistencia'] ? 'success' : 'danger';
    $actividad_reciente[] = [
        'tipo' => 'asistencia',
        'titulo' => 'Asistencia registrada',
        'descripcion' => $asistencia['nombre'] . ($asistencia['anotaciones'] ? ' - ' . $asistencia['anotaciones'] : ''),
        'fecha' => $fecha_asistencia,
        'badge' => $badge,
        'icono' => 'bi-clipboard-check'
    ];
}

// Ordenar actividad por fecha (más reciente primero)
usort($actividad_reciente, function($a, $b) {
    return $b['fecha'] <=> $a['fecha'];
});

// Limitar a 5 actividades más recientes
$actividad_reciente = array_slice($actividad_reciente, 0, 5);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Supervisora - GRETA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --supervisora-primary: #2D3748;
            --supervisora-secondary: #4A5568;
            --supervisora-accent: #E53E3E;
            --supervisora-light: #F7FAFC;
            --supervisora-dark: #1A202C;
            --supervisora-success: #38A169;
            --supervisora-warning: #D69E2E;
            --supervisora-info: #3182CE;
            --supervisora-text: #2D3748;
            --supervisora-gray: #E2E8F0;
            --supervisora-border: #CBD5E0;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #edf2f7 0%, #f7fafc 100%);
            min-height: 100vh;
            color: var(--supervisora-text);
        }

        .main-container {
            background: white;
            min-height: 100vh;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.08);
        }

        /* Navbar estilo supervisora */
        .navbar-supervisora {
            background: linear-gradient(135deg, var(--supervisora-primary) 0%, var(--supervisora-dark) 100%);
            box-shadow: 0 4px 20px rgba(45, 55, 72, 0.3);
            padding: 1rem 0;
        }

        .navbar-brand-supervisora {
            font-weight: 700;
            font-size: 1.4rem;
            color: white !important;
        }

        .navbar-brand-supervisora img {
            height: 40px;
            width: auto;
            margin-right: 12px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .navbar-supervisora .navbar-nav .nav-link {
            color: #ffffff !important;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            opacity: 1 !important;
            transition: all 0.3s ease;
        }

        .navbar-supervisora .navbar-nav .nav-link:hover {
            color: #ffffff !important;
            transform: translateY(-2px);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Header de bienvenida */
        .welcome-header-supervisora {
            background: linear-gradient(135deg, #fff5f5 0%, var(--supervisora-light) 100%);
            border-radius: 20px;
            padding: 2.5rem;
            margin: 2rem 0;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--supervisora-border);
            border-left: 4px solid var(--supervisora-accent);
        }

        .welcome-header-supervisora::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(229, 62, 62, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.3;
        }

        .welcome-icon-supervisora {
            font-size: 4rem;
            color: var(--supervisora-accent);
            filter: drop-shadow(0 4px 8px rgba(229, 62, 62, 0.3));
        }

        .welcome-text-supervisora h1 {
            font-weight: 700;
            color: var(--supervisora-dark);
            margin-bottom: 0.5rem;
            font-size: 2.2rem;
        }

        .welcome-text-supervisora .lead {
            color: var(--supervisora-secondary);
            font-weight: 500;
            font-size: 1.2rem;
        }

        /* Tarjetas de estadísticas */
        .stats-card-supervisora {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--supervisora-border);
            transition: all 0.3s ease;
            border-left: 4px solid var(--supervisora-accent);
        }

        .stats-card-supervisora:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stats-card-supervisora h3 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            font-weight: 800;
            color: var(--supervisora-dark);
        }

        .stats-card-supervisora p {
            color: var(--supervisora-secondary);
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .stats-card-supervisora i {
            font-size: 2.5rem;
            color: var(--supervisora-accent);
            margin-bottom: 1rem;
        }

        /* Acciones rápidas */
        .quick-actions-supervisora {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-action-supervisora {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            border: 2px solid transparent;
        }

        .quick-action-supervisora:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: var(--supervisora-accent);
            text-decoration: none;
            color: inherit;
        }

        .quick-action-icon {
            font-size: 2.5rem;
            color: var(--supervisora-accent);
            margin-bottom: 1rem;
        }

        .quick-action-text {
            font-weight: 600;
            color: var(--supervisora-dark);
            font-size: 1rem;
        }

        /* Footer */
        .footer-supervisora {
            background: var(--supervisora-dark);
            color: white;
            padding: 1.5rem 0;
            margin-top: 3rem;
        }

        /* Botones personalizados */
        .btn-supervisora {
            background: linear-gradient(135deg, var(--supervisora-primary) 0%, var(--supervisora-secondary) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(45, 55, 72, 0.3);
        }

        .btn-supervisora:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(45, 55, 72, 0.4);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .welcome-header-supervisora {
                padding: 1.5rem;
                margin: 1rem 0;
            }
            
            .welcome-text-supervisora h1 {
                font-size: 1.8rem;
            }
            
            .welcome-icon-supervisora {
                font-size: 3rem;
            }
            
            .quick-actions-supervisora {
                grid-template-columns: 1fr;
            }
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Badge de notificaciones */
        .notification-badge {
            background: linear-gradient(135deg, var(--supervisora-accent) 0%, #fc8181 100%);
            box-shadow: 0 2px 8px rgba(229, 62, 62, 0.6);
        }

        /* Sección de actividad reciente */
        .recent-activity {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            border: 1px solid var(--supervisora-border);
        }

        .activity-item {
            padding: 1rem;
            border-left: 3px solid var(--supervisora-accent);
            margin-bottom: 1rem;
            background: var(--supervisora-light);
            border-radius: 0 8px 8px 0;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: #fff;
            transform: translateX(5px);
        }

        .activity-item:last-child {
            margin-bottom: 0;
        }

        .empty-activity {
            text-align: center;
            padding: 2rem;
            color: var(--supervisora-secondary);
        }

        .empty-activity i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-supervisora">
            <div class="container">
                <a class="navbar-brand navbar-brand-supervisora" href="#">
                    <img src="img/LogoGreta.jpeg" alt="GRETA">
                    GRETA · Supervisora
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupervisora">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupervisora">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="gestionUsuarios.php">
                                <i class="bi bi-people me-1"></i> Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Historial.php">
                                <i class="bi bi-clipboard-check me-1"></i> Asistencias
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="calendario.php">
                                <i class="bi bi-calendar-week me-1"></i> Calendario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gestion-turnos.php">
                                <i class="bi bi-calendar-check me-1"></i> Turnos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Notificaciones -->
        <div class="container mt-3">
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> Estado del turno actualizado correctamente
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i> <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Contenido principal -->
        <div class="container py-4">
            <!-- Header de bienvenida -->
            <div class="welcome-header-supervisora fade-in-up">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="welcome-text-supervisora">
                            <h1>¡Bienvenida, <?= $nombre ?>! 👩‍💼</h1>
                            <p class="lead">Panel de Supervisión - GRETA Estética</p>
                            <p class="mb-0">Supervisa las operaciones, gestiona el personal y monitorea el rendimiento del establecimiento.</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="bi bi-graph-up-arrow welcome-icon-supervisora"></i>
                    </div>
                </div>
            </div>

            <!-- Estadísticas en tiempo real -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stats-card-supervisora fade-in-up" style="animation-delay: 0.1s">
                        <i class="bi bi-calendar-check"></i>
                        <h3><?= $turnos_hoy ?></h3>
                        <p>Turnos Hoy</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card-supervisora fade-in-up" style="animation-delay: 0.2s">
                        <i class="bi bi-people"></i>
                        <h3><?= $empleados_activos ?></h3>
                        <p>Empleados Activos</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card-supervisora fade-in-up" style="animation-delay: 0.3s">
                        <i class="bi bi-clipboard-check"></i>
                        <h3><?= $asistencias_hoy ?></h3>
                        <p>Asistencias Hoy</p>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="quick-actions-supervisora">
                <a href="gestionUsuarios.php" class="quick-action-supervisora fade-in-up" style="animation-delay: 0.4s">
                    <div class="quick-action-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="quick-action-text">Gestión de Usuarios</div>
                </a>
                <a href="Historial.php" class="quick-action-supervisora fade-in-up" style="animation-delay: 0.5s">
                    <div class="quick-action-icon">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <div class="quick-action-text">Control de Asistencia</div>
                </a>
                <a href="calendario.php" class="quick-action-supervisora fade-in-up" style="animation-delay: 0.6s">
                    <div class="quick-action-icon">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                    <div class="quick-action-text">Ver Calendario</div>
                </a>
                <a href="gestion-turnos.php" class="quick-action-supervisora fade-in-up" style="animation-delay: 0.7s">
                    <div class="quick-action-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="quick-action-text">Gestión de Turnos</div>
                </a>
            </div>

            <!-- Actividad reciente -->
            <div class="recent-activity fade-in-up" style="animation-delay: 0.8s">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Actividad Reciente</h5>
                    <button class="btn btn-sm btn-supervisora" onclick="recargarActividad()">
                        <i class="bi bi-arrow-clockwise"></i> Actualizar
                    </button>
                </div>
                
                <div id="actividad-container">
                    <?php if (empty($actividad_reciente)): ?>
                        <div class="empty-activity">
                            <i class="bi bi-inbox"></i>
                            <p>No hay actividad reciente para mostrar</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($actividad_reciente as $actividad): ?>
                            <div class='activity-item'>
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong><?= $actividad['titulo'] ?></strong>
                                        <p class="mb-0 text-muted"><?= $actividad['descripcion'] ?></p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-<?= $actividad['badge'] ?>">
                                            <i class="bi <?= $actividad['icono'] ?> me-1"></i>
                                            <?= $actividad['fecha']->format('H:i') ?>
                                        </span>
                                        <br>
                                        <small class="text-muted"><?= $actividad['fecha']->format('d/m/Y') ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información del sistema -->
            <div class="row">
                <div class="col-12">
                    <div class="card fade-in-up" style="animation-delay: 0.9s; background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                        <h5 class="mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Información del Sistema</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Fecha y Hora:</strong> <span id="hora-actual"><?= date('d/m/Y H:i:s') ?></span></p>
                                <p><strong>Rol Actual:</strong> Supervisora</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Última Actualización:</strong> <span id="ultima-actualizacion"><?= date('d/m/Y H:i') ?></span></p>
                                <p><strong>Estado del Sistema:</strong> <span class="badge bg-success">Operativo</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer-supervisora text-center">
            <div class="container">
                <small>© <?= date('Y'); ?> GRETA Estética · Panel de Supervisora</small>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Efectos de animación al cargar
        document.addEventListener('DOMContentLoaded', function() {
            // Agregar clase de animación a los elementos
            const elements = document.querySelectorAll('.fade-in-up');
            elements.forEach((element, index) => {
                element.style.animationDelay = `${index * 0.1}s`;
            });

            // Mostrar hora actual en tiempo real
            function actualizarHora() {
                const ahora = new Date();
                const opciones = { 
                    day: '2-digit', 
                    month: '2-digit', 
                    year: 'numeric',
                    hour: '2-digit', 
                    minute: '2-digit',
                    second: '2-digit'
                };
                const horaFormateada = ahora.toLocaleDateString('es-ES', opciones);
                document.getElementById('hora-actual').textContent = horaFormateada;
            }

            setInterval(actualizarHora, 1000);
            actualizarHora();
        });

        // Función para recargar actividad
        function recargarActividad() {
            const btn = event.target;
            const originalText = btn.innerHTML;
            
            // Mostrar loading
            btn.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Cargando...';
            btn.disabled = true;
            
            // Simular recarga (en un caso real, harías una petición AJAX)
            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        // Agregar estilo de spin para el icono de loading
        const style = document.createElement('style');
        style.textContent = `
            .spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>