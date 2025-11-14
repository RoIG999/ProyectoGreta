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
$seccion_activa = $_GET['seccion'] ?? 'dashboard'; // Sección activa por defecto

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
$asistencias_mes = 0;
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

// ASISTENCIAS DEL MES (cambiado de semanal a mensual) - CON VALIDACIÓN
$sql_asistencias_mes = "SELECT COUNT(*) as total FROM asistencias 
                       WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' 
                       AND asistencia = 1";
if ($result = $conn->query($sql_asistencias_mes)) {
    $row = $result->fetch_assoc();
    $asistencias_mes = $row['total'];
} else {
    $asistencias_mes = 0;
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
// CONSULTAS CORREGIDAS PARA REPORTES PRECISOS
// ============================================================================

$ingresos_mensuales = 0;
$clientes_nuevos_mes = 0;
$servicios_populares = [];
$asistencias_mes_detalle = [];
$estado_turnos_hoy = [];

// 1. INGRESOS DEL PERÍODO - CORREGIDO Y OPTIMIZADO
$sql_ingresos_mes = "SELECT COALESCE(SUM(p.monto), 0) as ingresos 
                    FROM pagos p 
                    WHERE DATE(p.fecha_pago) BETWEEN '$fecha_inicio' AND '$fecha_fin'
                    AND p.estado = 'completado'
                    AND p.monto > 0"; // Solo montos positivos
if ($result = $conn->query($sql_ingresos_mes)) {
    $row = $result->fetch_assoc();
    $ingresos_mensuales = $row['ingresos'];
} else {
    $ingresos_mensuales = 0;
}

// 2. CLIENTES NUEVOS EN EL PERÍODO - CORREGIDA
$sql_clientes_nuevos = "SELECT COUNT(DISTINCT CONCAT(nombre_cliente, apellido_cliente, telefono_cliente)) as total 
                       FROM turno 
                       WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
                       AND ID_estado_turno_FK NOT IN (6)"; // Excluir cancelados
if ($result = $conn->query($sql_clientes_nuevos)) {
    $row = $result->fetch_assoc();
    $clientes_nuevos_mes = $row['total'];
}

// TOTAL CLIENTES ÚNICOS (para contexto)
$sql_total_clientes = "SELECT COUNT(DISTINCT CONCAT(nombre_cliente, apellido_cliente, telefono_cliente)) as total 
                      FROM turno 
                      WHERE ID_estado_turno_FK NOT IN (6)"; // Excluir cancelados
if ($result = $conn->query($sql_total_clientes)) {
    $row = $result->fetch_assoc();
    $total_clientes = $row['total'];
} else {
    $total_clientes = 0;
}

// 3. SERVICIOS MÁS POPULARES - CORREGIDA (relación directa con servicio)
$sql_servicios_populares = "SELECT 
    COALESCE(s.nombre, 'Servicio no especificado') as servicio, 
    COUNT(*) as cantidad 
    FROM turno t 
    LEFT JOIN servicio s ON t.ID_servicio_FK = s.ID 
    WHERE t.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    AND t.ID_estado_turno_FK NOT IN (6) -- Excluir turnos cancelados
    AND s.nombre IS NOT NULL
    GROUP BY s.ID, s.nombre 
    ORDER BY cantidad DESC 
    LIMIT 5"; // Aumentado a 10 servicios
$result_populares = $conn->query($sql_servicios_populares);
if ($result_populares) {
    while ($row = $result_populares->fetch_assoc()) {
        $servicios_populares[] = $row;
    }
}

// 4. ESTADO DE TURNOS HOY - CORREGIDA Y COMPLETA
$sql_estado_turnos = "SELECT 
    COALESCE(et.nombre, 'Sin estado') as estado, 
    COUNT(*) as cantidad 
    FROM turno t 
    LEFT JOIN estado_turno et ON t.ID_estado_turno_FK = et.ID 
    WHERE t.fecha = '$hoy'
    GROUP BY et.ID, et.nombre
    ORDER BY et.ID";
$result_estados = $conn->query($sql_estado_turnos);
$estado_turnos_hoy = [];
$turnos_confirmados_hoy = 0;
$turnos_en_proceso_hoy = 0;
$turnos_pagados_hoy = 0;
$turnos_pendientes_hoy = 0;
$turnos_cancelados_hoy = 0;

if ($result_estados) {
    while ($row = $result_estados->fetch_assoc()) {
        $estado_turnos_hoy[] = $row;
        
        // Contar por estado específico
        switch($row['estado']) {
            case 'Confirmado': $turnos_confirmados_hoy = $row['cantidad']; break;
            case 'En Proceso': $turnos_en_proceso_hoy = $row['cantidad']; break;
            case 'Pagado': $turnos_pagados_hoy = $row['cantidad']; break;
            case 'Pendiente': $turnos_pendientes_hoy = $row['cantidad']; break;
            case 'Cancelado': $turnos_cancelados_hoy = $row['cantidad']; break;
        }
    }
}

// Si no hay resultados, inicializar en 0
if (empty($estado_turnos_hoy)) {
    $estado_turnos_hoy[] = ['estado' => 'Sin turnos', 'cantidad' => 0];
}

// 5. INGRESOS HOY - CORREGIDO
$sql_ingresos_hoy = "SELECT COALESCE(SUM(p.monto), 0) as ingresos 
                    FROM pagos p 
                    WHERE DATE(p.fecha_pago) = '$hoy'
                    AND p.estado = 'completado'";
if ($result = $conn->query($sql_ingresos_hoy)) {
    $row = $result->fetch_assoc();
    $ingresos_hoy = $row['ingresos'];
}

// 6. ASISTENCIAS MES DETALLE - CORREGIDO - AHORA CON TODOS LOS DÍAS
$sql_asistencias_mes_detalle = "SELECT DATE(fecha) as dia, COUNT(*) as cantidad 
                               FROM asistencias 
                               WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' 
                               AND asistencia = 1
                               GROUP BY DATE(fecha)
                               ORDER BY dia";
$result_mes = $conn->query($sql_asistencias_mes_detalle);
$asistencias_mes_detalle = [];
if ($result_mes) {
    while ($row = $result_mes->fetch_assoc()) {
        $asistencias_mes_detalle[] = $row;
    }
}

// 7. INGRESOS MENSUALES REALES - CORREGIDO PARA MOSTRAR MESES ACTUALES
$ingresos_mensuales_detalle = [];
$sql_ingresos_mensuales = "SELECT 
    DATE_FORMAT(p.fecha_pago, '%Y-%m') as mes,
    DATE_FORMAT(p.fecha_pago, '%M') as mes_nombre,
    COALESCE(SUM(p.monto), 0) as ingresos
    FROM pagos p 
    WHERE p.fecha_pago >= DATE_FORMAT(NOW() - INTERVAL 3 MONTH, '%Y-%m-01')
    AND p.estado = 'completado'
    GROUP BY DATE_FORMAT(p.fecha_pago, '%Y-%m'), DATE_FORMAT(p.fecha_pago, '%M')
    ORDER BY mes";

$result_ingresos_mensuales = $conn->query($sql_ingresos_mensuales);
if ($result_ingresos_mensuales) {
    while ($row = $result_ingresos_mensuales->fetch_assoc()) {
        $ingresos_mensuales_detalle[] = $row;
    }
}

// NUEVO REPORTE: TASA DE CONVERSIÓN Y EFICIENCIA
$tasa_conversion = 0;
$turnos_totales_periodo = 0;
$turnos_completados_periodo = 0;

// Turnos totales en el período
$sql_turnos_totales = "SELECT COUNT(*) as total FROM turno 
                      WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
                      AND ID_estado_turno_FK NOT IN (6)"; // Excluir cancelados
if ($result = $conn->query($sql_turnos_totales)) {
    $row = $result->fetch_assoc();
    $turnos_totales_periodo = $row['total'];
}

// Turnos completados en el período
$sql_turnos_completados = "SELECT COUNT(*) as total FROM turno 
                          WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
                          AND ID_estado_turno_FK = 7"; // Solo pagados
if ($result = $conn->query($sql_turnos_completados)) {
    $row = $result->fetch_assoc();
    $turnos_completados_periodo = $row['total'];
}

// Calcular tasa de conversión
if ($turnos_totales_periodo > 0) {
    $tasa_conversion = round(($turnos_completados_periodo / $turnos_totales_periodo) * 100, 1);
}

// ============================================================================
// CONSULTAS MEJORADAS PARA GRÁFICO ADAPTABLE - CORREGIDAS
// ============================================================================

// Determinar el tipo de gráfico basado en el período seleccionado
$dias_periodo = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24);
$tipo_grafico = ($dias_periodo <= 31) ? 'diario' : 'mensual';

// Preparar datos para gráfico adaptable - CORREGIDO COMPLETAMENTE
$labels_adaptable = [];
$data_adaptable = [];

if ($tipo_grafico == 'diario') {
    // Gráfico diario para períodos cortos (<= 31 días) - CORREGIDO
    $current = strtotime($fecha_inicio);
    $end = strtotime($fecha_fin);
    
    while ($current <= $end) {
        $current_date = date('Y-m-d', $current);
        $labels_adaptable[] = date('d M', $current);
        
        // Buscar datos para este día - CORREGIDO
        $cantidad = 0;
        foreach ($asistencias_mes_detalle as $asistencia) {
            if ($asistencia['dia'] == $current_date) {
                $cantidad = $asistencia['cantidad'];
                break;
            }
        }
        $data_adaptable[] = $cantidad;
        
        $current = strtotime('+1 day', $current);
    }
} else {
    // Gráfico mensual para períodos largos (> 31 días) - CORREGIDO
    $sql_asistencias_agrupadas = "SELECT 
        DATE_FORMAT(fecha, '%Y-%m') as mes,
        DATE_FORMAT(fecha, '%M %Y') as mes_nombre,
        COUNT(*) as asistencias
        FROM asistencias 
        WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
        AND asistencia = 1
        GROUP BY DATE_FORMAT(fecha, '%Y-%m'), DATE_FORMAT(fecha, '%M %Y')
        ORDER BY mes";

    $result_agrupadas = $conn->query($sql_asistencias_agrupadas);
    if ($result_agrupadas) {
        while ($row = $result_agrupadas->fetch_assoc()) {
            $labels_adaptable[] = $row['mes_nombre'];
            $data_adaptable[] = $row['asistencias'];
        }
    }
}

// Preparar datos para el gráfico de ingresos mensuales - CORREGIDO
$meses_ingresos = [];
$ingresos_por_mes = [];

// Obtener los últimos 4 meses
for ($i = 3; $i >= 0; $i--) {
    $mes = date('Y-m', strtotime("-$i months"));
    $mes_nombre = date('F Y', strtotime("-$i months"));
    $meses_ingresos[] = $mes_nombre;
    
    // Buscar ingresos para este mes
    $ingreso_mes = 0;
    foreach ($ingresos_mensuales_detalle as $ingreso) {
        if ($ingreso['mes'] == $mes) {
            $ingreso_mes = $ingreso['ingresos'];
            break;
        }
    }
    $ingresos_por_mes[] = $ingreso_mes;
}

// Calcular eficiencia para el gráfico
$dias_laborables = 20; // Aproximadamente
$asistencias_esperadas = $total_empleados * $dias_laborables;
$eficiencia = $asistencias_esperadas > 0 ? round(($asistencias_mes / $asistencias_esperadas) * 100) : 0;
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
    
    /* ENCABEZADO PRINCIPAL - NUEVO ESTILO */
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
    
    /* Gráficos - MEJORAS APLICADAS */
    .chart-container {
      position: relative;
      height: 300px;
      padding: 20px;
    }
    
    .chart-legend {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 15px;
    }
    
    .legend-item {
      display: flex;
      align-items: center;
      margin: 0 10px 5px 0;
      font-size: 0.8rem;
    }
    
    .legend-color {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      margin-right: 5px;
    }
    
    /* Tooltips personalizados para gráficos */
    .chartjs-tooltip {
      background: rgba(0, 0, 0, 0.8) !important;
      border-radius: 8px !important;
      color: white !important;
      padding: 10px 15px !important;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
    
    /* Animaciones suaves para gráficos */
    canvas {
      transition: opacity 0.3s ease;
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
    
    .activity-item:last-child {
      margin-bottom: 0;
    }
    
    /* Secciones */
    .section {
      display: none;
      opacity: 0;
      transition: opacity 0.5s ease;
    }
    
    .section.active {
      display: block;
      opacity: 1;
    }
    
    /* Footer */
    footer {
      background: var(--background-white);
      border-top: 1px solid var(--border-light);
      color: var(--text-light);
      font-size: 0.875rem;
    }

    /* ESTILOS NUEVOS PARA REPORTES MÁS LLAMATIVOS */
    .report-card {
      border: none;
      border-radius: 20px;
      background: linear-gradient(135deg, var(--background-white) 0%, #f8f9fa 100%);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
      transition: all 0.4s ease;
      overflow: hidden;
      position: relative;
    }
    
    .report-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--accent-medium), var(--primary-main));
    }
    
    .report-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }
    
    .report-header {
      background: linear-gradient(135deg, var(--primary-main) 0%, var(--primary-dark) 100%);
      color: white;
      padding: 20px;
      border-radius: 20px 20px 0 0;
    }
    
    .report-icon {
      width: 60px;
      height: 60px;
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-bottom: 15px;
      background: rgba(255, 255, 255, 0.2);
    }
    
    .stat-badge {
      background: linear-gradient(135deg, var(--accent-pastel) 0%, var(--accent-soft) 100%);
      color: var(--primary-dark);
      padding: 8px 16px;
      border-radius: 25px;
      font-weight: 600;
      font-size: 0.9rem;
    }
    
    .trend-up {
      color: var(--success);
    }
    
    .trend-down {
      color: #E53E3E;
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
    
    .progress-report {
      height: 8px;
      border-radius: 10px;
      background: var(--border-light);
      overflow: hidden;
      margin: 10px 0;
    }
    
    .progress-fill {
      height: 100%;
      border-radius: 10px;
      background: linear-gradient(90deg, var(--accent-medium), var(--primary-main));
    }
    
    /* Nuevos estilos para el selector de fecha */
    .date-selector {
      background: var(--background-white);
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .date-selector label {
      font-weight: 600;
      color: var(--primary-dark);
      margin-bottom: 8px;
    }
    
    .periodo-info {
      background: linear-gradient(135deg, var(--accent-pastel) 0%, var(--accent-soft) 100%);
      border-radius: 10px;
      padding: 15px;
      margin-top: 10px;
    }

    /* Selector de gráficos */
    .chart-selector {
      background: var(--background-white);
      border-radius: 12px;
      padding: 15px;
      margin-bottom: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .chart-option {
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .chart-option:hover {
      background: var(--accent-pastel);
    }
    
    .chart-option input {
      margin-right: 10px;
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

    /* Servicios populares más compacto */
    .servicios-compactos .list-group-item {
      padding: 12px 15px;
      margin-bottom: 8px;
    }
    
    .servicios-compactos .badge {
      font-size: 0.75rem;
      padding: 4px 8px;
    }

    /* Numeración más grande para servicios */
    .numero-servicio {
      font-size: 1.1rem;
      font-weight: 700;
      min-width: 30px;
      text-align: center;
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
      
      .chart-container {
        height: 250px;
      }
      
      .page-header {
        padding: 20px;
      }
      
      .numero-servicio {
        font-size: 1rem;
        min-width: 25px;
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
        <a class="nav-link <?= $seccion_activa == 'dashboard' ? 'active' : '' ?>" href="#" data-target="dashboard">
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
      <li class="nav-item">
        <a class="nav-link <?= $seccion_activa == 'reportes' ? 'active' : '' ?>" href="#" data-target="reportes">
          <i class="bi bi-graph-up me-2"></i> Reportes
        </a>
      </li>
    </ul>
  </div>

  <!-- Contenido principal -->
  <div class="main-content" style="margin-top: 76px;">
    <div class="container-fluid">
      
      <!-- Sección Dashboard -->
      <div id="dashboard" class="section <?= $seccion_activa == 'dashboard' ? 'active' : '' ?>">
        <!-- Encabezado PRIMERO -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="page-header">
              <div class="row align-items-center">
                <div class="col-md-8">
                  <h1 class="h2 mb-2 fw-bold text-white">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                  </h1>
                  <p class="text-white mb-0 opacity-75">Resumen general y estadísticas de tu negocio</p>
                </div>
                <div class="col-md-4 text-end">
                  <i class="bi bi-speedometer2 display-4 opacity-25"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filtro DEBAJO del encabezado -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="date-selector">
              <form method="GET" action="" class="row g-3 align-items-end">
                <input type="hidden" name="seccion" id="seccion_actual" value="<?= $seccion_activa ?>">
                <div class="col-md-4">
                  <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                  <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                         value="<?= $fecha_inicio ?>" max="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                  <label for="fecha_fin" class="form-label">Fecha Fin</label>
                  <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                         value="<?= $fecha_fin ?>" max="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                  <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i> Aplicar Filtros
                  </button>
                </div>
              </form>
              <div class="periodo-info">
                <small class="text-dark">
                  <i class="bi bi-info-circle"></i> 
                  Mostrando datos del período: 
                  <strong><?= date('d/m/Y', strtotime($fecha_inicio)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?></strong>
                  <?php if ($tipo_grafico == 'diario'): ?>
                    <br><span class="text-success">• Vista diaria activa (período ≤ 31 días)</span>
                  <?php else: ?>
                    <br><span class="text-info">• Vista mensual activa (período > 31 días)</span>
                  <?php endif; ?>
                </small>
              </div>
            </div>
          </div>
        </div>

        <!-- Selector de Gráficos - MEJORADO Y FUNCIONAL -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="chart-selector">
              <h6 class="mb-3 fw-bold">Selecciona los gráficos a mostrar:</h6>
              <div class="row">
                <div class="col-md-3">
                  <div class="chart-option">
                    <input type="checkbox" id="chart1" class="chart-checkbox" data-chart="attendanceChart" checked>
                    <label for="chart1" class="mb-0">Asistencia por Período</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="chart-option">
                    <input type="checkbox" id="chart2" class="chart-checkbox" data-chart="turnosChart" checked>
                    <label for="chart2" class="mb-0">Estado de Turnos</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="chart-option">
                    <input type="checkbox" id="chart3" class="chart-checkbox" data-chart="servicesChart" checked>
                    <label for="chart3" class="mb-0">Servicios Populares</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="chart-option">
                    <input type="checkbox" id="chart4" class="chart-checkbox" data-chart="incomeChart" checked>
                    <label for="chart4" class="mb-0">Ingresos Mensuales</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- MANTENIENDO LAS 4 TARJETAS ORIGINALES EN DASHBOARD -->
        <div class="row mb-4">
          <div class="col-12 col-md-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="card-subtitle mb-1">Empleados</h6>
                    <h3 class="card-title mb-0"><?= $total_empleados ?></h3>
                    <p class="small mb-0">Total registrados</p>
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
                    <p class="small mb-0"><?= $asistencias_mes ?> en el período</p>
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
                      <?= $turnos_confirmados_hoy ?> confirmados · 
                      <?= $turnos_en_proceso_hoy ?> en proceso
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

        <!-- GRÁFICOS DINÁMICOS - TODOS LOS GRÁFICOS DE REPORTES -->
        <div class="row" id="charts-container">
          <!-- Gráfico de Asistencia Adaptable -->
          <div class="col-12 col-lg-6 mb-4 chart-item" data-chart="attendanceChart">
            <div class="card h-100">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                  <i class="bi bi-calendar-check me-2"></i>Asistencia por Período
                  <?php if ($tipo_grafico == 'diario'): ?>
                    <span class="badge bg-primary ms-2">Vista Diaria</span>
                  <?php else: ?>
                    <span class="badge bg-info ms-2">Vista Mensual</span>
                  <?php endif; ?>
                </h5>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="attendanceChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Gráfico: Estado de Turnos Hoy -->
          <div class="col-12 col-lg-6 mb-4 chart-item" data-chart="turnosChart">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-clock me-2"></i>Estado de Turnos - Hoy
                </h5>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="turnosChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Gráfico de distribución de servicios -->
          <div class="col-12 col-lg-6 mb-4 chart-item" data-chart="servicesChart">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-scissors me-2"></i>Servicios Más Solicitados
                </h5>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="servicesChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Gráfico de Ingresos Mensuales - EL MISMO QUE EN REPORTES -->
          <div class="col-12 col-lg-6 mb-4 chart-item" data-chart="incomeChart">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-currency-dollar me-2"></i>Evolución de Ingresos
                </h5>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="incomeChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- TURNOS RECIENTES -->
        <div class="row">
          <div class="col-12 mb-4">
            <div class="card h-100">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                  <i class="bi bi-clock-history me-2"></i>Turnos Recientes
                </h5>
                <a href="gestion-turnos-dueña.php" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-arrow-right me-1"></i> Gestionar Turnos
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

      <!-- SECCIÓN REPORTES COMPLETOS - CON LOS MISMOS GRÁFICOS QUE DASHBOARD -->
      <div id="reportes" class="section <?= $seccion_activa == 'reportes' ? 'active' : '' ?>">
        <!-- Encabezado - NUEVO DISEÑO NEGRO -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="page-header">
              <div class="row align-items-center">
                <div class="col-md-8">
                  <h1 class="h2 mb-2 fw-bold text-white">
                    <i class="bi bi-graph-up-arrow me-2"></i>Reportes Analíticos
                  </h1>
                  <p class="text-white mb-0 opacity-75">Análisis completos y métricas detalladas de tu negocio</p>
                </div>
                <div class="col-md-4 text-end">
                  <i class="bi bi-graph-up-arrow display-4 opacity-25"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filtro DEBAJO del encabezado -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="date-selector">
              <form method="GET" action="" class="row g-3 align-items-end">
                <input type="hidden" name="seccion" id="seccion_actual" value="<?= $seccion_activa ?>">
                <div class="col-md-4">
                  <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                  <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                         value="<?= $fecha_inicio ?>" max="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                  <label for="fecha_fin" class="form-label">Fecha Fin</label>
                  <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                         value="<?= $fecha_fin ?>" max="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                  <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i> Aplicar Filtros
                  </button>
                </div>
              </form>
              <div class="periodo-info">
                <small class="text-dark">
                  <i class="bi bi-info-circle"></i> 
                  Mostrando datos del período: 
                  <strong><?= date('d/m/Y', strtotime($fecha_inicio)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?></strong>
                  <?php if ($tipo_grafico == 'diario'): ?>
                    <br><span class="text-success">• Vista diaria activa (período ≤ 31 días)</span>
                  <?php else: ?>
                    <br><span class="text-info">• Vista mensual activa (período > 31 días)</span>
                  <?php endif; ?>
                </small>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjetas principales de reportes - ACTUALIZADA -->
        <div class="row mb-4">
          <!-- Reporte Financiero - MEJORADO -->
          <div class="col-12 col-lg-4 mb-4">
            <div class="report-card h-100">
              <div class="report-header">
                <div class="report-icon">
                  <i class="bi bi-currency-dollar"></i>
                </div>
                <h5 class="card-title mb-2">Rendimiento Financiero</h5>
                <p class="mb-0 opacity-75">Análisis de ingresos</p>
              </div>
              <div class="card-body p-4">
                <div class="row text-center">
                  <div class="col-6">
                    <h3 class="fw-bold text-dark">$<?= number_format($ingresos_hoy, 0, ',', '.') ?></h3>
                    <small class="text-muted">Hoy</small>
                  </div>
                  <div class="col-6">
                    <h3 class="fw-bold text-dark">$<?= number_format($ingresos_mensuales, 0, ',', '.') ?></h3>
                    <small class="text-muted">Período Seleccionado</small>
                  </div>
                </div>
                <div class="mt-3">
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Turnos Pagados Hoy</span>
                    <span class="fw-bold"><?= $turnos_pagados_hoy ?></span>
                  </div>
                  <div class="progress-report">
                    <div class="progress-fill" style="width: <?= min($turnos_pagados_hoy * 20, 100) ?>%"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Reporte Operativo - MEJORADO -->
          <div class="col-12 col-lg-4 mb-4">
            <div class="report-card h-100">
              <div class="report-header">
                <div class="report-icon">
                  <i class="bi bi-speedometer2"></i>
                </div>
                <h5 class="card-title mb-2">Eficiencia Operativa</h5>
                <p class="mb-0 opacity-75">Rendimiento del equipo y servicios</p>
              </div>
              <div class="card-body p-4">
                <div class="row text-center mb-3">
                  <div class="col-4">
                    <h4 class="fw-bold text-dark"><?= $asistencias_hoy ?></h4>
                    <small class="text-muted">Asistencias Hoy</small>
                  </div>
                  <div class="col-4">
                    <h4 class="fw-bold text-dark"><?= $reservas_hoy ?></h4>
                    <small class="text-muted">Turnos Hoy</small>
                  </div>
                  <div class="col-4">
                    <h4 class="fw-bold text-dark"><?= $servicios_activos ?></h4>
                    <small class="text-muted">Servicios</small>
                  </div>
                </div>
                <div class="stat-badge text-center">
                  Eficiencia: <?= $eficiencia ?>% (período)
                </div>
              </div>
            </div>
          </div>

          <!-- NUEVO REPORTE: Tasa de Conversión -->
          <div class="col-12 col-lg-4 mb-4">
            <div class="report-card h-100">
              <div class="report-header">
                <div class="report-icon">
                  <i class="bi bi-graph-up"></i>
                </div>
                <h5 class="card-title mb-2">Tasa de Conversión</h5>
                <p class="mb-0 opacity-75">Efectividad de turnos</p>
              </div>
              <div class="card-body p-4 text-center">
                <h1 class="display-4 fw-bold text-primary mb-2"><?= $tasa_conversion ?>%</h1>
                <p class="text-muted mb-3">Turnos Completados</p>
                <small class="text-muted">
                  <?= $turnos_completados_periodo ?> de <?= $turnos_totales_periodo ?> turnos
                </small>
              </div>
            </div>
          </div>
        </div>

        <!-- Servicios Populares - CON NUMERACIÓN MÁS GRANDE -->
        <div class="row">
          <div class="col-12 mb-4">
            <div class="report-card h-100">
              <div class="report-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-trophy me-2"></i>Servicios Más Solicitados
                </h5>
              </div>
              <div class="card-body servicios-compactos">
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
                          <span class="badge bg-primary numero-servicio me-3"><?= $index + 1 ?></span>
                          <div>
                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($servicio['servicio']) ?></h6>
                            <small class="text-muted">Servicio más solicitado</small>
                          </div>
                        </div>
                        <span class="stat-badge"><?= $servicio['cantidad'] ?> turnos</span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- GRÁFICOS EN REPORTES - LOS MISMOS QUE EN DASHBOARD -->
        <div class="row">
          <!-- Gráfico de Asistencia - EL MISMO -->
          <div class="col-12 col-lg-6 mb-4">
            <div class="report-card h-100">
              <div class="report-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                  <i class="bi bi-calendar-check me-2"></i>Asistencia por Período
                </h5>
                <?php if ($tipo_grafico == 'diario'): ?>
                  <span class="badge bg-primary">Vista Diaria</span>
                <?php else: ?>
                  <span class="badge bg-info">Vista Mensual</span>
                <?php endif; ?>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="weeklyChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Gráfico de Ingresos Mensuales - EL MISMO QUE EN DASHBOARD -->
          <div class="col-12 col-lg-6 mb-4">
            <div class="report-card h-100">
              <div class="report-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-currency-dollar me-2"></i>Evolución de Ingresos
                </h5>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="monthlyIncomeChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- GRÁFICOS ADICIONALES EN REPORTES -->
        <div class="row">
          <!-- Gráfico de Estado de Turnos -->
          <div class="col-12 col-lg-6 mb-4">
            <div class="report-card h-100">
              <div class="report-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-clock me-2"></i>Estado de Turnos - Hoy
                </h5>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="reportTurnosChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Gráfico de Servicios Populares -->
          <div class="col-12 col-lg-6 mb-4">
            <div class="report-card h-100">
              <div class="report-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-scissors me-2"></i>Servicios Más Solicitados
                </h5>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="reportServicesChart"></canvas>
                </div>
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

    // Variables globales para los gráficos
    let charts = {};

    // Inicializar gráficos - CÓDIGO COMPLETAMENTE CORREGIDO
    function initCharts() {
      // Destruir gráficos existentes
      Object.values(charts).forEach(chart => {
        if (chart) chart.destroy();
      });
      charts = {};

      // Datos desde PHP
      const attendanceLabels = <?= json_encode($labels_adaptable) ?>;
      const attendanceData = <?= json_encode($data_adaptable) ?>;
      const incomeLabels = <?= json_encode($meses_ingresos) ?>;
      const incomeData = <?= json_encode($ingresos_por_mes) ?>;
      const servicesLabels = <?= json_encode(array_column($servicios_populares, 'servicio')) ?>;
      const servicesData = <?= json_encode(array_column($servicios_populares, 'cantidad')) ?>;

      // 1. Gráfico de Asistencia - CORREGIDO
      const attendanceCtx = document.getElementById('attendanceChart');
      if (attendanceCtx && attendanceData.length > 0) {
        charts.attendanceChart = new Chart(attendanceCtx, {
          type: 'bar',
          data: {
            labels: attendanceLabels,
            datasets: [{
              label: 'Asistencias',
              data: attendanceData,
              backgroundColor: function(context) {
                const value = context.dataset.data[context.dataIndex];
                const maxValue = Math.max(...context.dataset.data);
                const minValue = Math.min(...context.dataset.data.filter(val => val > 0));
                
                if (value === 0) return '#E2E8F0';
                if (value === maxValue) return '#4CAF50';
                if (value === minValue) return '#FF9800';
                return '#2196F3';
              },
              borderColor: '#1565C0',
              borderWidth: 1,
              borderRadius: 6
            }]
          },
          options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
              legend: { display: false },
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
                title: { display: true, text: 'Cantidad de Asistencias' },
                ticks: { stepSize: 1 }
              },
              x: {
                title: { 
                  display: true, 
                  text: <?= $tipo_grafico == 'diario' ? "'Días del Período'" : "'Meses'" ?>
                }
              }
            }
          }
        });
      }

      // Gráfico de línea para reportes (ASISTENCIA)
      const weeklyCtx = document.getElementById('weeklyChart');
      if (weeklyCtx && attendanceData.length > 0) {
        charts.weeklyChart = new Chart(weeklyCtx, {
          type: 'line',
          data: {
            labels: attendanceLabels,
            datasets: [{
              label: 'Asistencias',
              data: attendanceData,
              borderColor: '#4CAF50',
              backgroundColor: 'rgba(76, 175, 80, 0.1)',
              borderWidth: 3,
              fill: true,
              tension: 0.4,
              pointBackgroundColor: '#4CAF50',
              pointBorderColor: '#ffffff',
              pointBorderWidth: 2,
              pointRadius: 6,
              pointHoverRadius: 8
            }]
          },
          options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
              y: {
                beginAtZero: true,
                title: { display: true, text: 'Cantidad de Asistencias' }
              },
              x: {
                title: { 
                  display: true, 
                  text: <?= $tipo_grafico == 'diario' ? "'Días'" : "'Meses'" ?>
                }
              }
            }
          }
        });
      }

      // 2. Gráfico de Estado de Turnos - DASHBOARD
      const turnosData = [
        <?= $turnos_confirmados_hoy ?>, 
        <?= $turnos_en_proceso_hoy ?>, 
        <?= $turnos_pagados_hoy ?>, 
        <?= $turnos_pendientes_hoy ?>, 
        <?= $turnos_cancelados_hoy ?>
      ];

      const turnosCtx = document.getElementById('turnosChart');
      if (turnosCtx && turnosData.some(val => val > 0)) {
        charts.turnosChart = new Chart(turnosCtx, {
          type: 'doughnut',
          data: {
            labels: ['Confirmados', 'En Proceso', 'Pagados', 'Pendientes', 'Cancelados'],
            datasets: [{
              data: turnosData,
              backgroundColor: ['#4299E1', '#ED8936', '#48BB78', '#9F7AEA', '#E53E3E'],
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
              }
            }
          }
        });
      }

      // Gráfico de Estado de Turnos - REPORTES
      const reportTurnosCtx = document.getElementById('reportTurnosChart');
      if (reportTurnosCtx && turnosData.some(val => val > 0)) {
        charts.reportTurnosChart = new Chart(reportTurnosCtx, {
          type: 'pie',
          data: {
            labels: ['Confirmados', 'En Proceso', 'Pagados', 'Pendientes', 'Cancelados'],
            datasets: [{
              data: turnosData,
              backgroundColor: ['#4299E1', '#ED8936', '#48BB78', '#9F7AEA', '#E53E3E'],
              borderWidth: 3,
              borderColor: '#FFFFFF',
              hoverOffset: 15
            }]
          },
          options: {
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  padding: 20,
                  usePointStyle: true,
                  font: { size: 11 }
                }
              }
            }
          }
        });
      }

      // 3. Gráfico de Servicios Populares - DASHBOARD
      const servicesCtx = document.getElementById('servicesChart');
      if (servicesCtx && servicesData.length > 0) {
        charts.servicesChart = new Chart(servicesCtx, {
          type: 'bar',
          data: {
            labels: servicesLabels,
            datasets: [{
              label: 'Turnos Realizados',
              data: servicesData,
              backgroundColor: '#4FD1C7',
              borderWidth: 0,
              borderRadius: 8
            }]
          },
          options: {
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
              x: {
                beginAtZero: true,
                title: { display: true, text: 'Cantidad de Turnos' },
                ticks: { stepSize: 1 }
              },
              y: {
                title: { display: true, text: 'Servicios' }
              }
            }
          }
        });
      }

      // Gráfico de Servicios Populares - REPORTES
      const reportServicesCtx = document.getElementById('reportServicesChart');
      if (reportServicesCtx && servicesData.length > 0) {
        charts.reportServicesChart = new Chart(reportServicesCtx, {
          type: 'bar',
          data: {
            labels: servicesLabels,
            datasets: [{
              label: 'Turnos Realizados',
              data: servicesData,
              backgroundColor: '#9C27B0',
              borderWidth: 0,
              borderRadius: 8
            }]
          },
          options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
              y: {
                beginAtZero: true,
                title: { display: true, text: 'Cantidad de Turnos' },
                ticks: { stepSize: 1 }
              },
              x: {
                title: { display: true, text: 'Servicios' }
              }
            }
          }
        });
      }

      // 4. Gráfico de Ingresos - DASHBOARD (LÍNEA)
      const incomeCtx = document.getElementById('incomeChart');
      if (incomeCtx && incomeData.length > 0) {
        charts.incomeChart = new Chart(incomeCtx, {
          type: 'line',
          data: {
            labels: incomeLabels,
            datasets: [{
              label: 'Ingresos',
              data: incomeData,
              borderColor: '#9C27B0',
              backgroundColor: 'rgba(156, 39, 176, 0.1)',
              borderWidth: 3,
              fill: true,
              tension: 0.3,
              pointBackgroundColor: '#9C27B0',
              pointBorderColor: '#ffffff',
              pointBorderWidth: 2,
              pointRadius: 6
            }]
          },
          options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  callback: function(value) {
                    return '$' + value.toLocaleString();
                  }
                }
              }
            }
          }
        });
      }

      // Gráfico de Ingresos - REPORTES (BARRAS - EL MISMO QUE DASHBOARD)
      const monthlyCtx = document.getElementById('monthlyIncomeChart');
      if (monthlyCtx && incomeData.length > 0) {
        charts.monthlyIncomeChart = new Chart(monthlyCtx, {
          type: 'line', // Mismo tipo que dashboard
          data: {
            labels: incomeLabels,
            datasets: [{
              label: 'Ingresos',
              data: incomeData,
              borderColor: '#9C27B0',
              backgroundColor: 'rgba(156, 39, 176, 0.1)',
              borderWidth: 3,
              fill: true,
              tension: 0.3,
              pointBackgroundColor: '#9C27B0',
              pointBorderColor: '#ffffff',
              pointBorderWidth: 2,
              pointRadius: 6
            }]
          },
          options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  callback: function(value) {
                    return '$' + value.toLocaleString();
                  }
                }
              }
            }
          }
        });
      }
    }

    // Selector de gráficos - COMPLETAMENTE CORREGIDO
    function setupChartSelector() {
      const checkboxes = document.querySelectorAll('.chart-checkbox');
      
      checkboxes.forEach(checkbox => {
        const chartId = checkbox.getAttribute('data-chart');
        const chartItem = document.querySelector(`.chart-item[data-chart="${chartId}"]`);
        
        // Estado inicial basado en el atributo checked
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

    // Navegación entre secciones
    document.querySelectorAll('.sidebar .nav-link[data-target]').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Actualizar navegación activa
        document.querySelectorAll('.sidebar .nav-link').forEach(nav => {
          nav.classList.remove('active');
        });
        this.classList.add('active');
        
        // Mostrar sección correspondiente
        const target = this.getAttribute('data-target');
        document.querySelectorAll('.section').forEach(section => {
          section.classList.remove('active');
        });
        document.getElementById(target).classList.add('active');
        
        // Actualizar el campo oculto para mantener la sección al aplicar filtros
        document.getElementById('seccion_actual').value = target;
        
        // Actualizar gráficos cuando se cambia de sección
        setTimeout(() => {
          Object.values(charts).forEach(chart => {
            if (chart) chart.resize();
          });
        }, 300);
      });
    });

    // Validación de fechas
    document.getElementById('fecha_inicio').addEventListener('change', function() {
      const fechaFin = document.getElementById('fecha_fin');
      if (this.value > fechaFin.value) {
        fechaFin.value = this.value;
      }
      fechaFin.min = this.value;
    });

    document.getElementById('fecha_fin').addEventListener('change', function() {
      const fechaInicio = document.getElementById('fecha_inicio');
      if (this.value < fechaInicio.value) {
        this.value = fechaInicio.value;
      }
    });

    // Inicializar la aplicación cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
      initCharts();
      setupChartSelector();
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