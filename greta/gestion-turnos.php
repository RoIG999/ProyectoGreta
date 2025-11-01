<?php
// gestion-turnos.php
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

// Conexión a la base de datos
include("conexion.php");

// Obtener fecha actual o fecha seleccionada
$fecha_actual = $_GET['fecha'] ?? date('Y-m-d');
$fecha_formateada = date('d/m/Y', strtotime($fecha_actual));

// Procesar cambio de estado si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cambiar_estado'])) {
        $turno_id = $_POST['turno_id'];
        $nuevo_estado = $_POST['nuevo_estado'];
        
        // Actualizar estado del turno (SIN notas)
        $sql_update = "UPDATE turno SET ID_estado_turno_FK = ? WHERE ID = ?";
        $stmt = $conn->prepare($sql_update);
        if ($stmt) {
            $stmt->bind_param("ii", $nuevo_estado, $turno_id);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Estado del turno actualizado correctamente";
                header("Location: gestion-turnos.php?fecha=" . $fecha_actual);
                exit;
            } else {
                $_SESSION['error'] = "Error al actualizar el estado del turno: " . $stmt->error;
            }
        } else {
            $_SESSION['error'] = "Error en la consulta: " . $conn->error;
        }
    }
}

// Obtener turnos de la fecha seleccionada
$turnos_del_dia = [];
$sql_turnos_dia = "SELECT t.*, 
                   rs.nombre as servicio_nombre,
                   s.precio,
                   et.nombre as estado_nombre, 
                   et.ID as estado_id,
                   t.grupo_turnos_id
                   FROM turno t 
                   LEFT JOIN rubro_servicio rs ON t.ID_servicio_FK = rs.ID 
                   LEFT JOIN servicio s ON rs.nombre = s.nombre
                   LEFT JOIN estado_turno et ON t.ID_estado_turno_FK = et.ID
                   WHERE t.fecha = ?
                   ORDER BY t.hora ASC";

$stmt = $conn->prepare($sql_turnos_dia);
if ($stmt) {
    $stmt->bind_param("s", $fecha_actual);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $turnos_del_dia[] = $row;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = "Error en la consulta de turnos: " . $conn->error;
}

// Obtener estadísticas
$total_turnos = count($turnos_del_dia);
$turnos_confirmados = array_filter($turnos_del_dia, function($t) { return $t['estado_id'] == 1; });
$turnos_proceso = array_filter($turnos_del_dia, function($t) { return $t['estado_id'] == 4; });
$turnos_pagados = array_filter($turnos_del_dia, function($t) { return $t['estado_id'] == 7; });

// Calcular ingresos
$ingresos = 0;
foreach ($turnos_pagados as $turno) {
    $ingresos += floatval($turno['precio']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Turnos - GRETA</title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem 1rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--supervisora-border);
            transition: all 0.3s ease;
            border-left: 4px solid var(--supervisora-accent);
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stats-card h4 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
            color: var(--supervisora-dark);
        }

        .stats-card small {
            font-weight: 600;
            color: var(--supervisora-secondary);
        }

        /* Estilos para estados de turno */
        .estado-badge {
            font-size: 0.75rem;
            padding: 0.5em 0.8em;
            font-weight: 600;
            border-radius: 6px;
        }

        .estado-1 { /* Confirmado */
            background: linear-gradient(135deg, #38A169, #68D391);
            color: white;
        }

        .estado-4 { /* Disponible/En Proceso */
            background: linear-gradient(135deg, #D69E2E, #F6E05E);
            color: var(--supervisora-dark);
        }

        .estado-5 { /* Pendiente */
            background: linear-gradient(135deg, #3182CE, #63B3ED);
            color: white;
        }

        .estado-6 { /* Cancelado */
            background: linear-gradient(135deg, #718096, #A0AEC0);
            color: white;
        }

        .estado-7 { /* Pagado */
            background: linear-gradient(135deg, #805AD5, #B794F4);
            color: white;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--supervisora-secondary);
            padding: 1.2rem 0.75rem;
            background: var(--supervisora-light);
            border-bottom: 2px solid var(--supervisora-border);
        }

        .table td {
            padding: 1.2rem 0.75rem;
            vertical-align: middle;
            border-color: var(--supervisora-border);
        }

        .table tbody tr:hover {
            background-color: rgba(229, 62, 62, 0.05);
        }

        .btn-supervisora {
            background: linear-gradient(135deg, var(--supervisora-primary) 0%, var(--supervisora-secondary) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(45, 55, 72, 0.2);
        }

        .btn-supervisora:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 55, 72, 0.3);
            color: white;
        }

        .filtro-fecha {
            max-width: 300px;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--supervisora-secondary);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .header-title {
            flex: 1;
            min-width: 300px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .header-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filtro-fecha {
                max-width: 100%;
            }
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: nowrap;
        }

        .btn-group-custom {
            display: flex;
            gap: 0.5rem;
        }
        
        .precio-sin-definir {
            color: #6c757d;
            font-style: italic;
        }

        /* Dropdown Styles */
        .dropdown {
            position: static !important;
        }

        .dropdown-menu {
            border-radius: 12px;
            border: 1px solid var(--supervisora-border);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            padding: 0.5rem;
            min-width: 220px;
            position: absolute;
            z-index: 1060;
            background: white;
            margin: 0.125rem 0 0;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 0.7rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0.1rem 0;
            color: var(--supervisora-text);
            text-decoration: none;
            display: block;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, var(--supervisora-primary), var(--supervisora-secondary));
            color: white;
            transform: translateX(5px);
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-supervisora">
            <div class="container">
                <a class="navbar-brand navbar-brand-supervisora" href="panel-supervisora.php">
                    <i class="bi bi-arrow-left me-2"></i>
                    GRETA · Gestión de Turnos
                </a>
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="panel-supervisora.php">
                        <i class="bi bi-house me-1"></i> Volver al Panel
                    </a>
                </div>
            </div>
        </nav>

        <!-- Contenido principal -->
        <div class="container py-4">
            <!-- Header -->
            <div class="header-container">
                <div class="header-title">
                    <h1 class="h3 mb-1 fw-bold">Gestión de Turnos</h1>
                    <p class="text-muted mb-0">Gestiona y realiza seguimiento de todos los turnos - <?= $fecha_formateada ?></p>
                </div>
                <div class="filtro-fecha">
                    <form method="GET" class="d-flex gap-2 align-items-center">
                        <label class="form-label mb-0 fw-semibold">Fecha:</label>
                        <input type="date" name="fecha" value="<?= $fecha_actual ?>" class="form-control" onchange="this.form.submit()">
                        <button type="button" class="btn btn-supervisora" onclick="location.href='?fecha=<?= date('Y-m-d') ?>'">
                            Hoy
                        </button>
                    </form>
                </div>
            </div>

            <!-- Notificaciones -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i> <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Estadísticas -->
            <div class="stats-grid">
                <div class="stats-card">
                    <h4><?= $total_turnos ?></h4>
                    <small>Total Turnos</small>
                </div>
                <div class="stats-card">
                    <h4><?= count($turnos_confirmados) ?></h4>
                    <small>Confirmados</small>
                </div>
                <div class="stats-card">
                    <h4><?= count($turnos_proceso) ?></h4>
                    <small>En Proceso</small>
                </div>
                <div class="stats-card">
                    <h4><?= count($turnos_pagados) ?></h4>
                    <small>Pagados</small>
                </div>
                <div class="stats-card">
                    <h4>$<?= number_format($ingresos, 0, ',', '.') ?></h4>
                    <small>Ingresos</small>
                </div>
            </div>

            <!-- Tabla de Turnos -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <?php if (empty($turnos_del_dia)): ?>
                        <div class="empty-state">
                            <i class="bi bi-calendar-x"></i>
                            <h5>No hay turnos programados</h5>
                            <p class="text-muted">No hay turnos programados para el <?= $fecha_formateada ?></p>
                            <a href="calendario.php" class="btn btn-supervisora">
                                <i class="bi bi-calendar-plus me-1"></i> Agendar Turno
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Hora</th>
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>Precio</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($turnos_del_dia as $turno): ?>
                                        <tr>
                                            <td class="fw-bold"><?= date('H:i', strtotime($turno['hora'])) ?></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong><?= htmlspecialchars($turno['nombre_cliente'] . ' ' . $turno['apellido_cliente']) ?></strong>
                                                    <small class="text-muted"><?= htmlspecialchars($turno['telefono_cliente'] ?? 'Sin teléfono') ?></small>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($turno['servicio_nombre']) ?></td>
                                            <td class="fw-semibold">
                                                <?php if ($turno['precio'] > 0): ?>
                                                    $<?= number_format($turno['precio'], 0, ',', '.') ?>
                                                <?php else: ?>
                                                    <span class="precio-sin-definir">Sin definir</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge estado-badge estado-<?= $turno['estado_id'] ?>">
                                                    <?= htmlspecialchars($turno['estado_nombre']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#detalleTurnoModal<?= $turno['ID'] ?>">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-supervisora dropdown-toggle" 
                                                                type="button" data-bs-toggle="dropdown">
                                                            Acciones
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <?php
                                                            // TRANSICIONES según estados reales
                                                            $transiciones = [];
                                                            switch($turno['estado_id']) {
                                                                case 5: // Pendiente
                                                                    $transiciones = [
                                                                        [1, 'Confirmar Turno', 'success', 'bi-check-circle'],
                                                                        [6, 'Cancelar Turno', 'danger', 'bi-x-circle']
                                                                    ];
                                                                    break;
                                                                case 1: // Confirmado
                                                                    $transiciones = [
                                                                        [4, 'Marcar como En Proceso', 'warning', 'bi-play-circle'],
                                                                        [6, 'Cancelar Turno', 'danger', 'bi-x-circle']
                                                                    ];
                                                                    break;
                                                                case 4: // Disponible/En Proceso
                                                                    $transiciones = [
                                                                        [1, 'Volver a Confirmado', 'secondary', 'bi-arrow-counterclockwise'],
                                                                        [6, 'Cancelar Turno', 'danger', 'bi-x-circle']
                                                                    ];
                                                                    // SOLO para estado 4 con precio, agregar opción de pago ÚNICA
                                                                    if ($turno['precio'] > 0) {
                                                                        $transiciones[] = [888, '💳 Procesar Pago', 'success', 'bi-currency-dollar'];
                                                                    }
                                                                    break;
                                                                case 7: // Pagado
                                                                    $transiciones = [
                                                                        [4, 'Revertir a En Proceso', 'secondary', 'bi-arrow-counterclockwise']
                                                                    ];
                                                                    break;
                                                                case 6: // Cancelado
                                                                    $transiciones = [
                                                                        [5, 'Reactivar como Pendiente', 'secondary', 'bi-arrow-clockwise']
                                                                    ];
                                                                    break;
                                                            }

                                                            foreach ($transiciones as $transicion):
                                                            ?>
                                                                <li>
                                                                    <?php if ($transicion[0] == 888): ?>
                                                                        <!-- ⭐⭐ BOTÓN ÚNICO PARA PAGOS - NUEVO PANEL UNIFICADO -->
                                                                        <a class="dropdown-item text-<?= $transicion[2] ?>" 
                                                                           href="procesar-pago.php?turno_id=<?= $turno['ID'] ?>">
                                                                            <i class="bi bi-currency-dollar me-2"></i>
                                                                            <?= $transicion[1] ?>
                                                                        </a>
                                                                    <?php else: ?>
                                                                        <!-- Formulario normal para cambio de estado (SIN notas) -->
                                                                        <form method="POST" class="d-inline">
                                                                            <input type="hidden" name="turno_id" value="<?= $turno['ID'] ?>">
                                                                            <input type="hidden" name="nuevo_estado" value="<?= $transicion[0] ?>">
                                                                            <button type="submit" name="cambiar_estado" class="dropdown-item text-<?= $transicion[2] ?>">
                                                                                <i class="bi <?= $transicion[3] ?> me-1"></i> 
                                                                                <?= $transicion[1] ?>
                                                                            </button>
                                                                        </form>
                                                                    <?php endif; ?>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal para detalles del turno (SIN notas) -->
                                        <div class="modal fade" id="detalleTurnoModal<?= $turno['ID'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Detalles Completos del Turno</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6 class="fw-semibold text-primary mb-3">Información del Cliente</h6>
                                                                <p><strong>Nombre:</strong> <?= htmlspecialchars($turno['nombre_cliente'] . ' ' . $turno['apellido_cliente']) ?></p>
                                                                <p><strong>Teléfono:</strong> <?= htmlspecialchars($turno['telefono_cliente'] ?? 'No especificado') ?></p>
                                                                <?php if ($turno['grupo_turnos_id']): ?>
                                                                    <p><strong>💼 Tipo:</strong> <span class="badge bg-info">Turno en Grupo</span></p>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6 class="fw-semibold text-primary mb-3">Información del Servicio</h6>
                                                                <p><strong>Servicio:</strong> <?= htmlspecialchars($turno['servicio_nombre']) ?></p>
                                                                <?php if ($turno['precio'] > 0): ?>
                                                                    <p><strong>Precio:</strong> $<?= number_format($turno['precio'], 0, ',', '.') ?></p>
                                                                <?php else: ?>
                                                                    <p><strong>Precio:</strong> <span class="precio-sin-definir">Sin definir</span></p>
                                                                <?php endif; ?>
                                                                <p><strong>Fecha y Hora:</strong> <?= $fecha_formateada ?> a las <?= date('H:i', strtotime($turno['hora'])) ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para confirmar acciones importantes
        function confirmarAccion(mensaje) {
            return confirm(mensaje);
        }

        // Agregar confirmación a acciones de cancelar
        document.addEventListener('DOMContentLoaded', function() {
            const cancelButtons = document.querySelectorAll('button[name="cambiar_estado"]');
            cancelButtons.forEach(button => {
                if (button.textContent.includes('Cancelar')) {
                    button.addEventListener('click', function(e) {
                        if (!confirmarAccion('¿Estás segura de que deseas cancelar este turno?')) {
                            e.preventDefault();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>