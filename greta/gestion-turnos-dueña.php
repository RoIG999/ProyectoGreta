<?php
// gestion-turnos-dueña.php
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

$nombre = htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Dueña', ENT_QUOTES, 'UTF-8');

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
                header("Location: gestion-turnos-dueña.php?fecha=" . $fecha_actual);
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
    <title>Gestión de Turnos - GRETA Beauty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #8B5FBF;
            --primary-dark: #6A3093;
            --primary-light: #B39DDB;
            --accent: #FF6B95;
            --accent-light: #FFA8C2;
            --gold: #FFD700;
            --silver: #E8E8E8;
            --text-dark: #2D3748;
            --text-light: #718096;
            --bg-light: #FAF7FF;
            --success: #48BB78;
            --warning: #ED8936;
            --danger: #F56565;
            --info: #4299E1;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--bg-light) 0%, #FFFFFF 100%);
            min-height: 100vh;
            color: var(--text-dark);
        }

        .luxury-navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(139, 95, 191, 0.3);
            padding: 1rem 0;
            border-bottom: 3px solid var(--gold);
        }

        .luxury-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: linear-gradient(135deg, #FFFFFF, var(--accent-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .badge-luxury {
            background: linear-gradient(135deg, var(--gold), #FFA500);
            color: var(--primary-dark);
            font-weight: 800;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            border: 2px solid rgba(255,255,255,0.3);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }

        .header-section {
            background: linear-gradient(135deg, rgba(139, 95, 191, 0.1) 0%, rgba(255, 107, 149, 0.1) 100%);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(139, 95, 191, 0.2);
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, var(--primary-light) 0%, transparent 70%);
            opacity: 0.1;
        }

        .luxury-title {
            font-weight: 800;
            font-size: 2.2rem;
            background: linear-gradient(135deg, var(--primary-dark), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .luxury-subtitle {
            color: var(--text-light);
            font-size: 1.1rem;
            font-weight: 500;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.8rem 1.2rem;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(139, 95, 191, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(139, 95, 191, 0.15);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.3rem;
        }

        .stat-total { background: linear-gradient(135deg, var(--primary-light), var(--primary)); color: white; }
        .stat-confirmed { background: linear-gradient(135deg, var(--success), #68D391); color: white; }
        .stat-process { background: linear-gradient(135deg, var(--warning), #F6AD55); color: white; }
        .stat-paid { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; }
        .stat-income { background: linear-gradient(135deg, var(--gold), #FFA500); color: var(--primary-dark); }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .stat-label {
            font-weight: 600;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .table-luxury {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(139, 95, 191, 0.1);
        }

        .table-luxury thead th {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            font-weight: 600;
            padding: 1.5rem 1rem;
            border: none;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-luxury tbody td {
            padding: 1.3rem 1rem;
            vertical-align: middle;
            border-color: rgba(139, 95, 191, 0.1);
            font-weight: 500;
        }

        .table-luxury tbody tr {
            transition: all 0.3s ease;
        }

        .table-luxury tbody tr:hover {
            background: linear-gradient(135deg, rgba(139, 95, 191, 0.05) 0%, rgba(255, 107, 149, 0.05) 100%);
            transform: scale(1.002);
        }

        .status-badge {
            padding: 0.6rem 1rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.8rem;
            border: 2px solid;
        }

        .status-confirmed { 
            background: linear-gradient(135deg, #C6F6D5, #9AE6B4); 
            color: #22543D;
            border-color: #48BB78;
        }

        .status-process { 
            background: linear-gradient(135deg, #FEEBC8, #FBD38D); 
            color: #744210;
            border-color: #ED8936;
        }

        .status-pending { 
            background: linear-gradient(135deg, #BEE3F8, #90CDF4); 
            color: #1A365D;
            border-color: #4299E1;
        }

        .status-cancelled { 
            background: linear-gradient(135deg, #FED7D7, #FEB2B2); 
            color: #742A2A;
            border-color: #F56565;
        }

        .status-paid { 
            background: linear-gradient(135deg, var(--primary-light), var(--primary)); 
            color: white;
            border-color: var(--primary-dark);
        }

        .btn-luxury {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(139, 95, 191, 0.3);
        }

        .btn-luxury:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 95, 191, 0.4);
            color: white;
        }

        .btn-luxury-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
            border-radius: 12px;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-luxury-outline:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-view {
            background: linear-gradient(135deg, var(--info), #63B3ED);
            color: white;
        }

        .btn-view:hover {
            transform: scale(1.1);
            color: white;
        }

        /* Dropdown Styles FIXED */
        .dropdown {
            position: static !important;
        }

        .dropdown-menu {
            border-radius: 12px;
            border: 1px solid rgba(139, 95, 191, 0.2);
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
            color: var(--text-dark);
            text-decoration: none;
            display: block;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            transform: translateX(5px);
        }

        .table-responsive {
            position: relative;
            overflow-x: auto;
        }

        .table-luxury,
        .container,
        body {
            overflow: visible !important;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-light);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            opacity: 0.5;
            color: var(--primary);
        }

        .modal-luxury .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .modal-luxury .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 20px 20px 0 0;
            border: none;
            padding: 1.5rem 2rem;
        }

        .modal-luxury .modal-title {
            font-weight: 700;
        }

        .date-filter {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(139, 95, 191, 0.1);
        }

        .client-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .price-highlight {
            font-weight: 700;
            color: var(--primary-dark);
            background: linear-gradient(135deg, var(--gold), #e29300ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: #2D3748
        }

        input[type="date"] {
            position: relative;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            color: transparent;
            background: transparent;
            cursor: pointer;
        }

        input[type="date"]::-webkit-inner-spin-button,
        input[type="date"]::-webkit-clear-button {
            display: none;
            -webkit-appearance: none;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .luxury-title {
                font-size: 1.8rem;
            }
            
            .header-section {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Luxury Navbar -->
    <nav class="navbar navbar-expand-lg luxury-navbar">
        <div class="container">
            <a class="navbar-brand luxury-brand" href="panel-dueña.php">
                ✨ GRETA Beauty
            </a>
            <div class="navbar-nav ms-auto">
                <span class="badge-luxury">
                    <i class="bi bi-gem me-1"></i> Dueña
                </span>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Luxury Header -->
        <div class="header-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="luxury-title">Gestión de Turnos</h1>
                    <p class="luxury-subtitle">Control exclusivo de agenda y servicios - <?= $fecha_formateada ?></p>
                </div>
                <div class="col-md-4">
                    <div class="date-filter">
                        <form method="GET" class="d-flex gap-2 align-items-center">
                            <label class="form-label mb-0 fw-semibold text-primary">📅 Fecha:</label>
                            <div class="position-relative">
                                <input type="date" name="fecha" value="<?= $fecha_actual ?>" 
                                       class="form-control border-primary pe-4" 
                                       style="appearance: none; -webkit-appearance: none; padding-right: 2.5rem;"
                                       onchange="this.form.submit()"
                                       id="fechaInput">
                                <i class="bi bi-calendar3 position-absolute" 
                                   style="right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--primary);"></i>
                            </div>
                            <button type="button" class="btn btn-luxury" 
                                    onclick="window.location.href='gestion-turnos-dueña.php?fecha=<?= date('Y-m-d') ?>'">
                                Hoy
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div class="flex-grow-1"><?= $_SESSION['success'] ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div class="flex-grow-1"><?= $_SESSION['error'] ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Luxury Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-total">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-number"><?= $total_turnos ?></div>
                <div class="stat-label">Total Turnos</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-confirmed">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-number"><?= count($turnos_confirmados) ?></div>
                <div class="stat-label">Confirmados</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-process">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stat-number"><?= count($turnos_proceso) ?></div>
                <div class="stat-label">En Proceso</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-paid">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-number"><?= count($turnos_pagados) ?></div>
                <div class="stat-label">Pagados</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-income">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="stat-number">$<?= number_format($ingresos, 0, ',', '.') ?></div>
                <div class="stat-label">Ingresos del Día</div>
            </div>
        </div>

        <!-- Luxury Table -->
        <div class="table-luxury">
            <?php if (empty($turnos_del_dia)): ?>
                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <h4 class="text-primary">No hay turnos programados</h4>
                    <p class="text-muted mb-3">No hay turnos agendados para el <?= $fecha_formateada ?></p>
                    <a href="calendario.php" class="btn btn-luxury">
                        <i class="bi bi-plus-circle me-2"></i> Agendar Nuevo Turno
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
                                    <td class="fw-bold fs-6"><?= date('H:i', strtotime($turno['hora'])) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="client-avatar">
                                                <?= strtoupper(substr($turno['nombre_cliente'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?= htmlspecialchars($turno['nombre_cliente'] . ' ' . $turno['apellido_cliente']) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($turno['telefono_cliente'] ?? 'Sin teléfono') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-semibold"><?= htmlspecialchars($turno['servicio_nombre']) ?></td>
                                    <td>
                                        <?php if ($turno['precio'] > 0): ?>
                                            <span class="price-highlight">$<?= number_format($turno['precio'], 0, ',', '.') ?></span>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Sin definir</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        switch($turno['estado_id']) {
                                            case 1: $statusClass = 'status-confirmed'; break;
                                            case 4: $statusClass = 'status-process'; break;
                                            case 5: $statusClass = 'status-pending'; break;
                                            case 6: $statusClass = 'status-cancelled'; break;
                                            case 7: $statusClass = 'status-paid'; break;
                                            default: $statusClass = 'status-pending';
                                        }
                                        ?>
                                        <span class="status-badge <?= $statusClass ?>">
                                            <?= htmlspecialchars($turno['estado_nombre']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <!-- Botón Ver -->
                                            <button type="button" class="btn-action btn-view" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detalleTurnoModal<?= $turno['ID'] ?>"
                                                    title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            
                                            <!-- Dropdown de Acciones -->
                                            <div class="dropdown">
                                                <button class="btn btn-luxury btn-sm dropdown-toggle px-3" 
                                                        type="button" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="bi bi-gear me-1"></i> Acciones
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php
                                                    $transiciones = [];
                                                    switch($turno['estado_id']) {
                                                        case 5:
                                                            $transiciones = [
                                                                [1, 'Confirmar Turno', 'success'],
                                                                [6, 'Cancelar Turno', 'danger']
                                                            ];
                                                            break;
                                                        case 1:
                                                            $transiciones = [
                                                                [4, 'Marcar como En Proceso', 'warning'],
                                                                [6, 'Cancelar Turno', 'danger']
                                                            ];
                                                            break;
                                                        case 4:
                                                            $transiciones = [
                                                                [1, 'Volver a Confirmado', 'secondary'],
                                                                [6, 'Cancelar Turno', 'danger']
                                                            ];
                                                            // SOLO para estado 4 con precio, agregar opción de pago ÚNICA
                                                            if ($turno['precio'] > 0) {
                                                                $transiciones[] = [888, '💳 Procesar Pago', 'success'];
                                                            }
                                                            break;
                                                        case 7:
                                                            $transiciones = [
                                                                [4, 'Revertir a En Proceso', 'secondary']
                                                            ];
                                                            break;
                                                        case 6:
                                                            $transiciones = [
                                                                [5, 'Reactivar como Pendiente', 'secondary']
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
                                                                <!-- Otras transiciones normales -->
                                                                <a class="dropdown-item text-<?= $transicion[2] ?>" 
                                                                   href="#"
                                                                   onclick="event.preventDefault(); document.getElementById('formEstado<?= $turno['ID'] ?>_<?= $transicion[0] ?>').submit();">
                                                                    <i class="bi <?= 
                                                                        $transicion[0] == 1 ? 'bi-check-circle' : 
                                                                        ($transicion[0] == 4 ? 'bi-play-circle' : 
                                                                        ($transicion[0] == 6 ? 'bi-x-circle' : 
                                                                        ($transicion[0] == 5 ? 'bi-arrow-clockwise' : 'bi-arrow-counterclockwise')))
                                                                    ?> me-2"></i>
                                                                    <?= $transicion[1] ?>
                                                                </a>
                                                                <!-- Formulario hidden para cada acción -->
                                                                <form id="formEstado<?= $turno['ID'] ?>_<?= $transicion[0] ?>" 
                                                                      method="POST" style="display: none;">
                                                                    <input type="hidden" name="turno_id" value="<?= $turno['ID'] ?>">
                                                                    <input type="hidden" name="nuevo_estado" value="<?= $transicion[0] ?>">
                                                                    <input type="hidden" name="cambiar_estado" value="1">
                                                                </form>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- MODALES - Solo modal de detalles (sin modales de pago viejos) -->
        <?php foreach ($turnos_del_dia as $turno): ?>
            <!-- Modal para detalles del turno -->
            <div class="modal fade modal-luxury" id="detalleTurnoModal<?= $turno['ID'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">✨ Detalles del Turno</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-semibold text-primary mb-3">👤 Información del Cliente</h6>
                                    <p><strong>Nombre:</strong> <?= htmlspecialchars($turno['nombre_cliente'] . ' ' . $turno['apellido_cliente']) ?></p>
                                    <p><strong>Teléfono:</strong> <?= htmlspecialchars($turno['telefono_cliente'] ?? 'No especificado') ?></p>
                                    <?php if ($turno['grupo_turnos_id']): ?>
                                        <p><strong>💼 Tipo:</strong> <span class="badge bg-info">Turno en Grupo</span></p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-semibold text-primary mb-3">💅 Información del Servicio</h6>
                                    <p><strong>Servicio:</strong> <?= htmlspecialchars($turno['servicio_nombre']) ?></p>
                                    <?php if ($turno['precio'] > 0): ?>
                                        <p><strong>Precio:</strong> <span class="price-highlight">$<?= number_format($turno['precio'], 0, ',', '.') ?></span></p>
                                    <?php else: ?>
                                        <p><strong>Precio:</strong> <span class="text-muted">Sin definir</span></p>
                                    <?php endif; ?>
                                    <p><strong>Fecha y Hora:</strong> <?= $fecha_formateada ?> a las <?= date('H:i', strtotime($turno['hora'])) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-luxury-outline" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Luxury confirmations
        function confirmarAccion(mensaje) {
            return confirm(`✨ ${mensaje}`);
        }

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

        // Función para cambiar estado del turno
        function cambiarEstado(turnoId, nuevoEstado, accion) {
            if (confirm(`¿Estás segura de que deseas ${accion.toLowerCase()} este turno?`)) {
                // Crear formulario dinámico
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const inputTurnoId = document.createElement('input');
                inputTurnoId.name = 'turno_id';
                inputTurnoId.value = turnoId;
                
                const inputNuevoEstado = document.createElement('input');
                inputNuevoEstado.name = 'nuevo_estado';
                inputNuevoEstado.value = nuevoEstado;
                
                const inputSubmit = document.createElement('input');
                inputSubmit.name = 'cambiar_estado';
                inputSubmit.type = 'submit';
                
                form.appendChild(inputTurnoId);
                form.appendChild(inputNuevoEstado);
                form.appendChild(inputSubmit);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Inicializar tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>