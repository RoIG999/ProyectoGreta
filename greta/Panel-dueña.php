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

// Obtener parámetros de fecha (si existen)
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01'); // Primer día del mes por defecto
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t'); // Último día del mes por defecto

// Validar fechas
if (!strtotime($fecha_inicio) || !strtotime($fecha_fin)) {
    $fecha_inicio = date('Y-m-01');
    $fecha_fin = date('Y-m-t');
}

// Asegurar que fecha_fin sea mayor o igual a fecha_inicio
if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
    $fecha_fin = $fecha_inicio;
}

// Obtener datos reales de la base de datos
$total_empleados = 0;
$total_clientes = 0;
$asistencias_hoy = 0;
$asistencias_periodo = 0;
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

// ASISTENCIAS DEL PERIODO SELECCIONADO
$sql_asistencias_periodo = "SELECT COUNT(*) as total FROM asistencias 
                       WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' 
                       AND asistencia = 1";
if ($result = $conn->query($sql_asistencias_periodo)) {
    $row = $result->fetch_assoc();
    $asistencias_periodo = $row['total'];
} else {
    $asistencias_periodo = 0;
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

// Turnos pagados hoy e ingresos - CONSULTA CORREGIDA
$sql_pagados = "SELECT COUNT(*) as total, COALESCE(SUM(p.monto), 0) as ingresos 
                FROM turno t 
                LEFT JOIN pagos p ON t.ID = p.turno_id 
                WHERE t.fecha = '$hoy' AND t.ID_estado_turno_FK = 7
                AND p.estado = 'completado'";
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
$sql_recientes = "SELECT t.nombre_cliente, t.apellido_cliente, t.hora, s.nombre as servicio, et.nombre as estado
                  FROM turno t 
                  LEFT JOIN servicio s ON t.ID_servicio_FK = s.ID 
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

// ============================================================================
// CONSULTAS MEJORADAS PARA GRÁFICOS - COMPLETAMENTE REHECHAS
// ============================================================================

$ingresos_periodo = 0;
$clientes_nuevos_periodo = 0;
$servicios_populares = [];
$estado_turnos_periodo = [];

// 1. INGRESOS DEL PERÍODO - CONSULTA MEJORADA
$sql_ingresos_periodo = "SELECT COALESCE(SUM(p.monto), 0) as ingresos 
                    FROM pagos p 
                    INNER JOIN turno t ON p.turno_id = t.ID
                    WHERE t.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
                    AND p.estado = 'completado'
                    AND p.monto > 0";
if ($result = $conn->query($sql_ingresos_periodo)) {
    $row = $result->fetch_assoc();
    $ingresos_periodo = $row['ingresos'];
} else {
    $ingresos_periodo = 0;
}

// 2. SERVICIOS MÁS POPULARES - CONSULTA MEJORADA
$sql_servicios_populares = "SELECT 
    COALESCE(s.nombre, 'Servicio no especificado') as servicio, 
    COUNT(*) as cantidad 
    FROM turno t 
    LEFT JOIN servicio s ON t.ID_servicio_FK = s.ID 
    WHERE t.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    AND t.ID_estado_turno_FK NOT IN (6) -- Excluir cancelados
    GROUP BY s.ID, s.nombre 
    ORDER BY cantidad DESC 
    LIMIT 5";
$result_populares = $conn->query($sql_servicios_populares);
if ($result_populares) {
    while ($row = $result_populares->fetch_assoc()) {
        $servicios_populares[] = $row;
    }
}

// 3. ESTADO DE TURNOS DEL PERIODO - CONSULTA MEJORADA Y CORREGIDA
$sql_estado_turnos_periodo = "SELECT 
    COALESCE(et.nombre, 'Sin estado') as estado, 
    COUNT(*) as cantidad 
    FROM turno t 
    LEFT JOIN estado_turno et ON t.ID_estado_turno_FK = et.ID 
    WHERE t.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    GROUP BY et.ID, et.nombre
    ORDER BY et.ID";
$result_estados_periodo = $conn->query($sql_estado_turnos_periodo);

// Inicializar todas las variables a 0
$turnos_confirmados_periodo = 0;
$turnos_en_proceso_periodo = 0;
$turnos_pagados_periodo = 0;
$turnos_pendientes_periodo = 0;
$turnos_cancelados_periodo = 0;

if ($result_estados_periodo) {
    while ($row = $result_estados_periodo->fetch_assoc()) {
        $estado_turnos_periodo[] = $row;
        
        // Contar por estado específico
        switch($row['estado']) {
            case 'Confirmado': $turnos_confirmados_periodo = $row['cantidad']; break;
            case 'En Proceso': $turnos_en_proceso_periodo = $row['cantidad']; break;
            case 'Pagado': $turnos_pagados_periodo = $row['cantidad']; break;
            case 'Pendiente': $turnos_pendientes_periodo = $row['cantidad']; break;
            case 'Cancelado': $turnos_cancelados_periodo = $row['cantidad']; break;
        }
    }
}

// 4. INGRESOS HOY - CONSULTA MEJORADA
$sql_ingresos_hoy = "SELECT COALESCE(SUM(p.monto), 0) as ingresos 
                    FROM pagos p 
                    INNER JOIN turno t ON p.turno_id = t.ID
                    WHERE t.fecha = '$hoy'
                    AND p.estado = 'completado'";
if ($result = $conn->query($sql_ingresos_hoy)) {
    $row = $result->fetch_assoc();
    $ingresos_hoy = $row['ingresos'];
}

// 5. ASISTENCIAS DETALLADAS POR DÍA - CONSULTA COMPLETAMENTE NUEVA
$asistencias_por_dia = [];
$sql_asistencias_detalle = "SELECT DATE(fecha) as dia, COUNT(*) as cantidad 
                           FROM asistencias 
                           WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' 
                           AND asistencia = 1
                           GROUP BY DATE(fecha)
                           ORDER BY dia";
$result_detalle = $conn->query($sql_asistencias_detalle);
if ($result_detalle) {
    while ($row = $result_detalle->fetch_assoc()) {
        $asistencias_por_dia[] = $row;
    }
}

// 6. INGRESOS POR DÍA - CONSULTA COMPLETAMENTE NUEVA
$ingresos_por_dia = [];
$sql_ingresos_diarios = "SELECT t.fecha as dia, COALESCE(SUM(p.monto), 0) as ingresos
                        FROM turno t 
                        INNER JOIN pagos p ON t.ID = p.turno_id
                        WHERE t.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
                        AND p.estado = 'completado'
                        GROUP BY t.fecha
                        ORDER BY t.fecha";
$result_ingresos_diarios = $conn->query($sql_ingresos_diarios);
if ($result_ingresos_diarios) {
    while ($row = $result_ingresos_diarios->fetch_assoc()) {
        $ingresos_por_dia[] = $row;
    }
}

// 7. DATOS PARA GRÁFICO DE ASISTENCIAS - COMPLETAMENTE REHECHO
$labels_asistencias = [];
$data_asistencias = [];

// Generar todos los días del período
$start = new DateTime($fecha_inicio);
$end = new DateTime($fecha_fin);
$interval = new DateInterval('P1D');
$period = new DatePeriod($start, $interval, $end->modify('+1 day'));

foreach ($period as $date) {
    $current_date = $date->format('Y-m-d');
    $labels_asistencias[] = $date->format('d M');
    
    // Buscar asistencias para este día
    $cantidad = 0;
    foreach ($asistencias_por_dia as $asistencia) {
        if ($asistencia['dia'] == $current_date) {
            $cantidad = $asistencia['cantidad'];
            break;
        }
    }
    $data_asistencias[] = $cantidad;
}

// 8. DATOS PARA GRÁFICO DE INGRESOS - COMPLETAMENTE REHECHO
$labels_ingresos = [];
$data_ingresos = [];

foreach ($period as $date) {
    $current_date = $date->format('Y-m-d');
    $labels_ingresos[] = $date->format('d M');
    
    // Buscar ingresos para este día
    $ingreso = 0;
    foreach ($ingresos_por_dia as $ingreso_dia) {
        if ($ingreso_dia['dia'] == $current_date) {
            $ingreso = $ingreso_dia['ingresos'];
            break;
        }
    }
    $data_ingresos[] = $ingreso;
}

// 9. MÉTRICAS ADICIONALES PARA EL FILTRO LATERAL
$total_turnos_periodo = 0;
$turnos_completados_periodo = 0;

// Total turnos en el período
$sql_turnos_totales = "SELECT COUNT(*) as total FROM turno 
                      WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
                      AND ID_estado_turno_FK NOT IN (6)";
if ($result = $conn->query($sql_turnos_totales)) {
    $row = $result->fetch_assoc();
    $total_turnos_periodo = $row['total'];
}

// Turnos completados en el período
$sql_turnos_completados = "SELECT COUNT(*) as total FROM turno 
                          WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
                          AND ID_estado_turno_FK = 7";
if ($result = $conn->query($sql_turnos_completados)) {
    $row = $result->fetch_assoc();
    $turnos_completados_periodo = $row['total'];
}

// Calcular tasa de conversión
$tasa_conversion = 0;
if ($total_turnos_periodo > 0) {
    $tasa_conversion = round(($turnos_completados_periodo / $total_turnos_periodo) * 100, 1);
}

// Calcular eficiencia
$dias_laborables = 20;
$asistencias_esperadas = $total_empleados * $dias_laborables;
$eficiencia = $asistencias_esperadas > 0 ? round(($asistencias_periodo / $asistencias_esperadas) * 100) : 0;

// Promedio de ingresos por día
$dias_periodo = count($labels_ingresos);
$promedio_ingresos_diario = $dias_periodo > 0 ? $ingresos_periodo / $dias_periodo : 0;
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
    
    /* ENCABEZADO PRINCIPAL */
    .page-header {
      background: linear-gradient(135deg, var(--primary-main) 0%, var(--primary-dark) 100%);
      color: white;
      border-radius: 20px;
      padding: 30px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(190, 23, 23, 0.1);
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
    
    /* Sidebar principal */
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
    
    /* BARRA LATERAL DE FILTROS - NUEVO ESTILO */
    .filter-sidebar {
      background: var(--background-white);
      border-left: 1px solid var(--border-light);
      height: auto;
      position: fixed;
      top: 100px;
      right: 0;
      width: 350px;
      padding: 20px;
      z-index: 999;
      overflow-: hidden;
      box-shadow: -2px 0 10px rgba(0,0,0,0.05);
      transform: translateX(100%);
      transition: transform 0.3s ease;
    }
    
    .filter-sidebar.show {
      transform: translateX(0);
    }
    
    .main-content {
      margin-left: 280px;
      padding: 30px;
      width: calc(100% - 280px);
      min-height: calc(100vh - 76px);
      transition: all 0.3s ease;
    }
    
    .main-content.with-filter {
      width: calc(100% - 280px - 350px);
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
    
    /* Gráficos */
    .chart-container {
      position: relative;
      height: 300px;
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
    
    /* Métricas en filtro lateral */
    .metric-card {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-radius: 12px;
      padding: 15px;
      margin-bottom: 12px;
      border-left: 4px solid var(--accent-medium);
    }
    
    .metric-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary-dark);
    }
    
    .metric-label {
      font-size: 0.8rem;
      color: var(--text-light);
      font-weight: 500;
    }
    
    /* Checkbox personalizado */
    .form-check-input:checked {
      background-color: var(--accent-medium);
      border-color: var(--accent-medium);
    }
    
    /* Botón de filtrar en el dashboard - NUEVA POSICIÓN */
    .page-header-with-filter {
      position: relative;
      padding-right: 180px; /* Espacio para el botón */
    }
    
    .filter-btn-header {
      position: absolute;
      top: 30px;
      right: 30px;
      background: rgba(255, 255, 255, 0.2);
      color: white;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 12px;
      padding: 12px 20px;
      font-weight: 500;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
    }
    
    .filter-btn-header:hover {
      background: rgba(255, 255, 255, 0.3);
      border-color: rgba(255, 255, 255, 0.5);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    /* Mejoras en el panel de filtros - MÁS ESPACIADO */
    .filter-section {
      margin-bottom: 15px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--border-light);
    }
    
    .filter-section:last-child {
      border-bottom: none;
    }
    
    .filter-section h6 {
      font-weight: 600;
      color: var(--primary-dark);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      font-size: 1rem;
    }
    
    .filter-section h6 i {
      margin-right: 10px;
      color: var(--accent-medium);
      font-size: 1.1rem;
    }
    
    .form-label {
      font-weight: 500;
      margin-bottom: 8px;
      color: var(--text-medium);
    }
    
    .form-check-label {
      font-weight: 500;
      color: var(--text-medium);
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
      .filter-sidebar {
        width: 300px;
      }
      
      .main-content.with-filter {
        width: calc(100% - 280px - 300px);
      }
    }
    
    @media (max-width: 992px) {
      .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
      }
      
      .sidebar.show {
        transform: translateX(0);
      }
      
      .main-content {
        margin-left: 0;
        width: 100%;
      }
      
      .main-content.with-filter {
        width: 100%;
      }
      
      .filter-sidebar {
        width: 100%;
        transform: translateX(100%);
      }
      
      .page-header-with-filter {
        padding-right: 30px;
      }
      
      .filter-btn-header {
        position: static;
        margin-top: 15px;
        background: rgba(255, 255, 255, 0.15);
      }
    }
    
    @media (max-width: 768px) {
      .main-content {
        padding: 15px;
      }
      
      .stat-card .card-title {
        font-size: 1.75rem;
      }
      
      .chart-container {
        height: 250px;
      }
      
      .page-header {
        padding: 20px;
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
      <a class="nav-link active" href="Panel-dueña.php">
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
      <a class="nav-link" href="Servicios(Dueña).php">
        <i class="bi bi-scissors me-2"></i>Servicios
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="gestion-turnos-dueña.php">
        <i class="bi bi-calendar-check me-2"></i>Turnos
      </a>
    </li>
    <!-- NUEVO: Enlace al Calendario -->
    <li class="nav-item">
      <a class="nav-link" href="Calendario.php">
        <i class="bi bi-calendar-week me-2"></i>Calendario
      </a>
    </li>
  </ul>
  <div class="d-flex align-items-center">
    <span class="text-light me-3 d-none d-sm-block">
      <i class="bi bi-person-circle me-1"></i>Hola, <?= $nombre ?>
    </span>
    <a class="btn btn-outline-light btn-sm" href="logout.php">
      <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>
  </div>
</div>
  </nav>

  <!-- Sidebar principal -->
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
    </ul>
  </div>

  <!-- Barra lateral de filtros - COMPLETAMENTE NUEVA -->
  <div class="filter-sidebar" id="filterSidebar">
    <div class="filter-section">
      <h6><i class="bi bi-funnel"></i> Filtros Principales</h6>
      <form method="GET" action="" id="filterForm">
        <div class="mb-3">
          <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
          <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                 value="<?= $fecha_inicio ?>" max="<?= date('Y-m-d') ?>">
        </div>
        <div class="mb-3">
          <label for="fecha_fin" class="form-label">Fecha Fin</label>
          <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                 value="<?= $fecha_fin ?>" max="<?= date('Y-m-d') ?>">
        </div>
        <button type="submit" class="btn btn-primary w-100">
          <i class="bi bi-filter"></i> Aplicar Filtros
        </button>
      </form>
    </div>

    <div class="filter-section">
      <h6><i class="bi bi-graph-up"></i> Selección de Gráficos</h6>
      <div class="form-check mb-3">
        <input class="form-check-input chart-checkbox" type="checkbox" id="chartAsistencias" data-chart="asistenciasChart" checked>
        <label class="form-check-label" for="chartAsistencias">
          Gráfico de Asistencias
        </label>
      </div>
      <div class="form-check mb-3">
        <input class="form-check-input chart-checkbox" type="checkbox" id="chartIngresos" data-chart="ingresosChart" checked>
        <label class="form-check-label" for="chartIngresos">
          Gráfico de Ingresos
        </label>
      </div>
      <div class="form-check mb-3">
        <input class="form-check-input chart-checkbox" type="checkbox" id="chartTurnos" data-chart="turnosChart" checked>
        <label class="form-check-label" for="chartTurnos">
          Estado de Turnos
        </label>
      </div>
      <div class="form-check mb-3">
        <input class="form-check-input chart-checkbox" type="checkbox" id="chartServicios" data-chart="serviciosChart" checked>
        <label class="form-check-label" for="chartServicios">
          Servicios Populares
        </label>
      </div>
    </div>

    <div class="filter-section">
      <h6><i class="bi bi-info-circle"></i> Información del Período</h6>
      <div class="alert alert-info">
        <small>
          <strong>Período seleccionado:</strong><br>
          <?= date('d/m/Y', strtotime($fecha_inicio)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?>
          <br><br>
          <strong>Días analizados:</strong> <?= $dias_periodo ?> días
          <br><br>
          <strong>Vista activa:</strong> 
          <?= $dias_periodo <= 31 ? 'Detalle Diario' : 'Resumen Mensual' ?>
        </small>
      </div>
    </div>
  </div>

  <!-- Contenido principal -->
  <div class="main-content" id="mainContent" style="margin-top: 76px;">
    <div class="container-fluid">
      
      <!-- Sección Dashboard ÚNICA -->
      <div id="dashboard" class="section active">
        <!-- Encabezado con botón de filtrar integrado -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="page-header page-header-with-filter">
              <div class="row align-items-center">
                <div class="col-md-8">
                  <h1 class="h2 mb-2 fw-bold text-white">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                  </h1>
                  <p class="text-white mb-0 opacity-75">Resumen general y estadísticas de tu negocio</p>
                </div>
                <div class="col-md-4 text-end d-none d-md-block">
                  <i class="bi bi-speedometer2 display-4 opacity-25"></i>
                </div>
              </div>
              
              <!-- Botón de filtrar integrado en el header -->
              <button class="filter-btn-header" id="dashboardFilterBtn">
                <i class="bi bi-funnel me-2"></i> Filtrar
              </button>
            </div>
          </div>
        </div>

        <!-- Tarjetas de estadísticas principales -->
        <div class="row mb-4">
          <div class="col-12 col-md-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="card-subtitle mb-1">Empleados</h6>
                    <h3 class="card-title mb-0"><?= $total_empleados ?></h3>
                    <p class="small mb-0">Total activos</p>
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
                    <p class="small mb-0"><?= $asistencias_periodo ?> en el período</p>
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
                      <?= $turnos_confirmados_hoy ?> confirmados
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

        <!-- GRÁFICOS COMPLETAMENTE REHECHOS -->
        <div class="row" id="charts-container">
          <!-- Gráfico de Asistencias - NUEVO -->
          <div class="col-12 col-lg-6 mb-4 chart-item" data-chart="asistenciasChart">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-calendar-check me-2"></i>Asistencias
                </h5>
                <small class="text-muted">Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></small>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="asistenciasChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Gráfico de Ingresos - NUEVO -->
          <div class="col-12 col-lg-6 mb-4 chart-item" data-chart="ingresosChart">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-currency-dollar me-2"></i>Ingresos
                </h5>
                <small class="text-muted">Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></small>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="ingresosChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Gráfico de Estado de Turnos - CORREGIDO (AHORA USA EL FILTRO) -->
          <div class="col-12 col-lg-6 mb-4 chart-item" data-chart="turnosChart">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-clock me-2"></i>Estado de Turnos
                </h5>
                <small class="text-muted">Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></small>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="turnosChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Gráfico de Servicios Populares -->
          <div class="col-12 col-lg-6 mb-4 chart-item" data-chart="serviciosChart">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-scissors me-2"></i>Servicios Más Solicitados
                </h5>
                <small class="text-muted">Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></small>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="serviciosChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Información adicional -->
        <div class="row">
          <!-- Servicios Populares -->
          <div class="col-12 col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-trophy me-2"></i>Top 5 Servicios
                </h5>
                <small class="text-muted">Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></small>
              </div>
              <div class="card-body">
                <?php if (empty($servicios_populares)): ?>
                  <div class="text-center text-muted py-4">
                    <i class="bi bi-scissors fs-1 mb-2"></i>
                    <p>No hay datos de servicios</p>
                  </div>
                <?php else: ?>
                  <div class="list-group list-group-flush">
                    <?php foreach ($servicios_populares as $index => $servicio): ?>
                      <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-3">
                        <div class="d-flex align-items-center">
                          <span class="badge bg-primary me-3"><?= $index + 1 ?></span>
                          <div>
                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($servicio['servicio']) ?></h6>
                          </div>
                        </div>
                        <span class="badge bg-light text-dark"><?= $servicio['cantidad'] ?> turnos</span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Turnos Recientes -->
          <div class="col-12 col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                  <i class="bi bi-clock-history me-2"></i>Turnos Recientes
                </h5>
                <a href="gestion-turnos-dueña.php" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-arrow-right me-1"></i> Ver Todos
                </a>
              </div>
              <div class="card-body recent-activity">
                <?php if (empty($reservas_recientes)): ?>
                  <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
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
      </div>
    </div>
  </div>

  <footer class="text-center py-4 mt-4">
    <small>© <?= date('Y'); ?> GRETA Estética · Todos los derechos reservados</small>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Variables globales para los gráficos
    let charts = {};

    // Inicializar gráficos - COMPLETAMENTE REHECHOS
    function initCharts() {
      // Destruir gráficos existentes
      Object.values(charts).forEach(chart => {
        if (chart) chart.destroy();
      });
      charts = {};

      // Datos desde PHP - NUEVAS CONSULTAS
      const asistenciasLabels = <?= json_encode($labels_asistencias) ?>;
      const asistenciasData = <?= json_encode($data_asistencias) ?>;
      const ingresosLabels = <?= json_encode($labels_ingresos) ?>;
      const ingresosData = <?= json_encode($data_ingresos) ?>;
      const serviciosLabels = <?= json_encode(array_column($servicios_populares, 'servicio')) ?>;
      const serviciosData = <?= json_encode(array_column($servicios_populares, 'cantidad')) ?>;

      // 1. GRÁFICO DE ASISTENCIAS - COMPLETAMENTE NUEVO
      const asistenciasCtx = document.getElementById('asistenciasChart');
      if (asistenciasCtx) {
        charts.asistenciasChart = new Chart(asistenciasCtx, {
          type: 'bar',
          data: {
            labels: asistenciasLabels,
            datasets: [{
              label: 'Asistencias',
              data: asistenciasData,
              backgroundColor: 'rgba(54, 162, 235, 0.8)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 2,
              borderRadius: 6,
              borderSkipped: false,
            }]
          },
          options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
              legend: {
                display: true,
                position: 'top',
              },
              tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                callbacks: {
                  label: function(context) {
                    return `Asistencias: ${context.parsed.y}`;
                  }
                }
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                title: {
                  display: true,
                  text: 'Cantidad de Asistencias'
                },
                ticks: {
                  stepSize: 1
                }
              },
              x: {
                title: {
                  display: true,
                  text: 'Días'
                },
                ticks: {
                  maxTicksLimit: 10
                }
              }
            }
          }
        });
      }

      // 2. GRÁFICO DE INGRESOS - COMPLETAMENTE NUEVO
      const ingresosCtx = document.getElementById('ingresosChart');
      if (ingresosCtx) {
        charts.ingresosChart = new Chart(ingresosCtx, {
          type: 'line',
          data: {
            labels: ingresosLabels,
            datasets: [{
              label: 'Ingresos',
              data: ingresosData,
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              borderColor: 'rgba(75, 192, 192, 1)',
              borderWidth: 3,
              tension: 0.4,
              fill: true,
              pointBackgroundColor: 'rgba(75, 192, 192, 1)',
              pointBorderColor: '#ffffff',
              pointBorderWidth: 2,
              pointRadius: 5,
              pointHoverRadius: 7
            }]
          },
          options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
              legend: {
                display: true,
                position: 'top',
              },
              tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                callbacks: {
                  label: function(context) {
                    return `Ingresos: $${context.parsed.y.toLocaleString()}`;
                  }
                }
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                title: {
                  display: true,
                  text: 'Ingresos ($)'
                },
                ticks: {
                  callback: function(value) {
                    return '$' + value.toLocaleString();
                  }
                }
              },
              x: {
                title: {
                  display: true,
                  text: 'Días'
                },
                ticks: {
                  maxTicksLimit: 10
                }
              }
            }
          }
        });
      }

      // 3. GRÁFICO DE ESTADO DE TURNOS - CORREGIDO (AHORA USA EL PERIODO)
      const turnosData = [
        <?= $turnos_confirmados_periodo ?>, 
        <?= $turnos_en_proceso_periodo ?>, 
        <?= $turnos_pagados_periodo ?>, 
        <?= $turnos_pendientes_periodo ?>, 
        <?= $turnos_cancelados_periodo ?>
      ];

      const turnosCtx = document.getElementById('turnosChart');
      if (turnosCtx) {
        charts.turnosChart = new Chart(turnosCtx, {
          type: 'doughnut',
          data: {
            labels: ['Confirmados', 'En Proceso', 'Pagados', 'Pendientes', 'Cancelados'],
            datasets: [{
              data: turnosData,
              backgroundColor: [
                '#4299E1', '#ED8936', '#48BB78', '#9F7AEA', '#E53E3E'
              ],
              borderWidth: 3,
              borderColor: '#FFFFFF',
              hoverOffset: 15
            }]
          },
          options: {
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  padding: 20,
                  usePointStyle: true,
                  font: { size: 11 }
                }
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    const label = context.label || '';
                    const value = context.parsed || 0;
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                    return `${label}: ${value} (${percentage}%)`;
                  }
                }
              }
            }
          }
        });
      }

      // 4. GRÁFICO DE SERVICIOS POPULARES
      const serviciosCtx = document.getElementById('serviciosChart');
      if (serviciosCtx && serviciosData.length > 0) {
        charts.serviciosChart = new Chart(serviciosCtx, {
          type: 'bar',
          data: {
            labels: serviciosLabels,
            datasets: [{
              label: 'Turnos Realizados',
              data: serviciosData,
              backgroundColor: '#9C27B0',
              borderWidth: 0,
              borderRadius: 8
            }]
          },
          options: {
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
              legend: {
                display: false
              }
            },
            scales: {
              x: {
                beginAtZero: true,
                title: {
                  display: true,
                  text: 'Cantidad de Turnos'
                },
                ticks: {
                  stepSize: 1
                }
              }
            }
          }
        });
      }
    }

    // Selector de gráficos
    function setupChartSelector() {
      const checkboxes = document.querySelectorAll('.chart-checkbox');
      
      checkboxes.forEach(checkbox => {
        const chartId = checkbox.getAttribute('data-chart');
        const chartItem = document.querySelector(`.chart-item[data-chart="${chartId}"]`);
        
        // Estado inicial
        if (chartItem) {
          chartItem.style.display = checkbox.checked ? 'block' : 'none';
        }
        
        // Event listener para cambios
        checkbox.addEventListener('change', function() {
          const chartId = this.getAttribute('data-chart');
          const chartItem = document.querySelector(`.chart-item[data-chart="${chartId}"]`);
          
          if (chartItem) {
            if (this.checked) {
              chartItem.style.display = 'block';
              // Redimensionar el gráfico si existe
              if (charts[chartId]) {
                setTimeout(() => {
                  charts[chartId].resize();
                }, 100);
              }
            } else {
              chartItem.style.display = 'none';
            }
          }
        });
      });
    }

    // Toggle del filtro lateral
    function setupFilterToggle() {
      const dashboardFilterBtn = document.getElementById('dashboardFilterBtn');
      const filterSidebar = document.getElementById('filterSidebar');
      const mainContent = document.getElementById('mainContent');

      // NUEVO: Botón de filtrar en el dashboard
      dashboardFilterBtn.addEventListener('click', function() {
        filterSidebar.classList.toggle('show');
        mainContent.classList.toggle('with-filter');
      });
    }

    // Validación de fechas
    function setupDateValidation() {
      const fechaInicio = document.getElementById('fecha_inicio');
      const fechaFin = document.getElementById('fecha_fin');

      fechaInicio.addEventListener('change', function() {
        if (this.value > fechaFin.value) {
          fechaFin.value = this.value;
        }
        fechaFin.min = this.value;
      });

      fechaFin.addEventListener('change', function() {
        if (this.value < fechaInicio.value) {
          this.value = fechaInicio.value;
        }
      });
    }

    // Toggle sidebar en vista móvil
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      document.querySelector('.sidebar').classList.toggle('show');
    });

    // Inicializar la aplicación cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
      initCharts();
      setupChartSelector();
      setupFilterToggle();
      setupDateValidation();
    });

    // Redimensionar gráficos cuando cambia el tamaño de la ventana
    window.addEventListener('resize', function() {
      Object.values(charts).forEach(chart => {
        if (chart) chart.resize();
      });
    });
  </script>
</body>
</html>