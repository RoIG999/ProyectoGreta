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

$nombre_usuario = htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Dueña', ENT_QUOTES, 'UTF-8');

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
            // Actualizar estado del turno
$sql_update = "UPDATE turno SET ID_estado_turno_FK = ? WHERE ID = ?";
$stmt = $conn->prepare($sql_update);
if ($stmt) {
    $stmt->bind_param("ii", $nuevo_estado, $turno_id);
    
    if ($stmt->execute()) {
            
            // TODO EL CÓDIGO DE WHATSAPP AQUÍ
            
        } else {
            header("Location: gestion-turnos-dueña.php?fecha=" . $fecha_actual);
            exit();
        }
    }
            
            if ($stmt->execute()) {
    $_SESSION['success'] = "Estado del turno actualizado correctamente";
    
    // Si el nuevo estado es "Confirmado" (ID 1), enviar recordatorio por WhatsApp
    // Si el nuevo estado es "Confirmado" (ID 1), enviar recordatorio por WhatsApp
if ($nuevo_estado == 1) {

    // 1. Obtener información del turno
    $sql_turno = "SELECT t.*, rs.nombre as servicio_nombre, t.telefono_cliente 
                 FROM turno t 
                 LEFT JOIN rubro_servicio rs ON t.ID_servicio_FK = rs.ID 
                 WHERE t.ID = ?";
    $stmt_turno = $conn->prepare($sql_turno);
    
    if ($stmt_turno) {
        $stmt_turno->bind_param("i", $turno_id);
        $stmt_turno->execute();
        $result_turno = $stmt_turno->get_result();
        $turno_info = $result_turno->fetch_assoc();
        $stmt_turno->close();
        
        if ($turno_info) {

            
            // 2. Generar token (MÉTODO MÁS SEGURO)
            $token_confirmacion = "greta_" . time() . "_" . $turno_id . "_" . bin2hex(random_bytes(8));
   
            
            // 3. Guardar token (MÉTODO DIRECTO)
            $sql_save_token = "UPDATE turno SET token_confirmacion = ? WHERE ID = ?";
            $stmt_save = $conn->prepare($sql_save_token);
            
            if ($stmt_save) {
                $stmt_save->bind_param("si", $token_confirmacion, $turno_id);
                if ($stmt_save->execute()) {
                    
                    // VERIFICACIÓN INMEDIATA
                    $sql_verify = "SELECT token_confirmacion FROM turno WHERE ID = ?";
                    $stmt_verify = $conn->prepare($sql_verify);
                    $stmt_verify->bind_param("i", $turno_id);
                    $stmt_verify->execute();
                    $result_verify = $stmt_verify->get_result();
                    $verified = $result_verify->fetch_assoc();
                    $stmt_verify->close();
                    

                    
                    if ($verified['token_confirmacion'] === $token_confirmacion) {
                        
                        // 4. Preparar mensaje WhatsApp
                        if (!empty($turno_info['telefono_cliente'])) {
                            $telefono = preg_replace('/[^0-9]/', '', $turno_info['telefono_cliente']);
                            $nombre_cliente = $turno_info['nombre_cliente'] . ' ' . $turno_info['apellido_cliente'];
                            $servicio = $turno_info['servicio_nombre'];
                            $fecha_turno = date('d/m/Y', strtotime($turno_info['fecha']));
                            $hora_turno = date('H:i', strtotime($turno_info['hora']));
                            
                            $base_url = "https://nakisha-unbroadcast-gale.ngrok-free.dev/greta";
                            $url_confirmar = $base_url . "/confirmar-turno.php?token=" . $token_confirmacion . "&accion=confirmar";
                            $url_cancelar = $base_url . "/confirmar-turno.php?token=" . $token_confirmacion . "&accion=cancelar";
                            
                            
                            
                            $mensaje_whatsapp = "¡Hola $nombre_cliente! \u{1F60A}\n\nTu turno en *GRETA Estética* está *PENDIENTE*:\n\n\u{1F4C5} *Fecha:* $fecha_turno\n\u{23F0} *Hora:* $hora_turno\n\u{1F485} *Servicio:* $servicio\n\n\u{1F4CD} *Dirección:* Virgen de la Merced 2345 - Córdoba\n\u{1F4DE} *Teléfono:* 3517896906\n\n\u{26A0}\u{FE0F} *POR FAVOR CONFIRMA TU ASISTENCIA:*\n\n\u{2705} CONFIRMAR: $url_confirmar\n\u{274C} CANCELAR: $url_cancelar\n\nTu confirmación nos ayuda a organizar mejor la agenda. ¡Gracias! \u{2728}";
                            
                            $mensaje_codificado = rawurlencode($mensaje_whatsapp);
                            $whatsapp_url = "https://wa.me/$telefono?text=$mensaje_codificado";
                            
                            $_SESSION['whatsapp_url'] = $whatsapp_url;
                            $_SESSION['whatsapp_cliente'] = $nombre_cliente;
                            $_SESSION['whatsapp_telefono'] = $telefono;
                            
                            
                            
                        } else {
                            echo "❌ No hay teléfono del cliente<br>";
                        }
                    } else {
                        echo "❌ ERROR: Token no coincide en verificación<br>";
                    }
                } else {
                    echo "❌ Error ejecutando save: " . $stmt_save->error . "<br>";
                }
                $stmt_save->close();
            } else {
                echo "❌ Error preparando save: " . $conn->error . "<br>";
            }
        } else {
            echo "❌ No se encontró turno info<br>";
        }
    } else {
        echo "❌ Error preparando consulta turno<br>";
    }
    
    echo "</div>";
} else {
    header("Location: gestion-turnos-dueña.php?fecha=" . $fecha_actual);
    exit();
}
}
}
}// Procesar solicitud de confirmación
    if (isset($_POST['solicitar_confirmacion'])) {
        $turno_id = $_POST['turno_id'];
        
        // Obtener información del turno
        $sql_turno = "SELECT t.*, rs.nombre as servicio_nombre 
                     FROM turno t 
                     LEFT JOIN rubro_servicio rs ON t.ID_servicio_FK = rs.ID 
                     WHERE t.ID = ?";
        $stmt = $conn->prepare($sql_turno);
        $stmt->bind_param("i", $turno_id);
        $stmt->execute();
        $turno_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($turno_info && !empty($turno_info['telefono_cliente'])) {
            $telefono = preg_replace('/[^0-9]/', '', $turno_info['telefono_cliente']);
            $nombre_cliente = $turno_info['nombre_cliente'] . ' ' . $turno_info['apellido_cliente'];
            $servicio = $turno_info['servicio_nombre'];
            $fecha_turno = date('d/m/Y', strtotime($turno_info['fecha']));
            $hora_turno = date('H:i', strtotime($turno_info['hora']));
            
            // Mensaje CON EMOJIS Y NEGRITAS
            $mensaje_whatsapp = "¡Hola $nombre_cliente! 😊\n\nTe recordamos tu turno en *GRETA Estética*:\n\n📅 *Fecha:* $fecha_turno\n⏰ *Hora:* $hora_turno\n💅 *Servicio:* $servicio\n\n📍 *Dirección:* Virgen de la Merced 2345 - Córdoba\n\n⚠️ *POR FAVOR CONFIRMA TU ASISTENCIA:*\n\n✅ *CONFIRMAR* - Respondé \"SI\" o \"CONFIRMO\"\n❌ *CANCELAR* - Respondé \"NO\" o \"CANCELO\"\n\nTu respuesta es muy importante para nosotros. ¡Gracias! ✨";
            
            $mensaje_codificado = rawurlencode($mensaje_whatsapp);
            $whatsapp_url = "https://wa.me/$telefono?text=$mensaje_codificado";
            
            // Redirigir directamente a WhatsApp
            header("Location: $whatsapp_url");
            exit();
        } else {
            $_SESSION['error'] = "No se pudo enviar la solicitud de confirmación (falta teléfono)";
            header("Location: gestion-turnos-dueña.php?fecha=" . $fecha_actual);
            exit();
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
            --danger: #F56565;
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
            height: 100%;
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
        
        /* Tablas MEJORADAS */
        .table-container {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
        }
        
        .table {
            margin-bottom: 0;
            font-size: 0.9rem;
            width: 100%;
            table-layout: fixed;
        }
        
        .table thead th {
            background-color: var(--primary-main);
            color: white;
            font-weight: 600;
            border: none;
            padding: 14px 8px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            vertical-align: middle;
            text-align: center;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background-color: var(--accent-pastel);
            cursor: pointer;
        }
        
        .table tbody td {
            padding: 12px 8px;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            text-align: center;
            word-wrap: break-word;
        }
        
        .badge {
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
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
        
        .badge-info {
            background-color: var(--info);
        }
        
        .badge-danger {
            background-color: var(--danger);
        }
        
        /* Formularios */
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
        
        /* Alertas */
        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 16px 20px;
        }
        
        /* Footer */
        footer {
            background: var(--background-white);
            border-top: 1px solid var(--border-light);
            color: var(--text-light);
            font-size: 0.875rem;
        }

        /* Estados de turnos */
        .status-badge {
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            min-width: 100px;
        }

        .status-confirmed { 
            background-color: var(--success);
            color: white;
        }

        .status-process { 
            background-color: var(--warning);
            color: white;
        }

        .status-pending { 
            background-color: var(--info);
            color: white;
        }

        .status-cancelled { 
            background-color: var(--danger);
            color: white;
        }

        .status-paid { 
            background-color: var(--primary-main);
            color: white;
        }

        .client-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-main), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .price-highlight {
            font-weight: 700;
            color: var(--success);
            font-size: 0.85rem;
        }

        .action-buttons {
            display: flex;
            gap: 4px;
            flex-wrap: nowrap;
            justify-content: center;
            align-items: center;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.3s ease;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .btn-view {
            background: linear-gradient(135deg, var(--info), #63B3ED);
            color: white;
        }

        .btn-view:hover {
            transform: scale(1.1);
            color: white;
        }

        /* Dropdown Styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-menu {
            border-radius: 12px;
            border: 1px solid var(--border-light);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            padding: 0.5rem;
            min-width: 180px;
            z-index: 1060;
            background: white;
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 2px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 0.6rem 0.8rem;
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
            font-size: 0.8rem;
        }

        .dropdown-item:hover {
            background-color: var(--accent-pastel);
            color: var(--primary-dark);
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
            color: var(--primary-main);
        }

        /* WhatsApp Button */
        .btn-whatsapp {
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            padding: 6px 10px;
            transition: all 0.3s ease;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .btn-whatsapp:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
            color: white;
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
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }
            
            .stat-card .card-title {
                font-size: 1.75rem;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 3px;
            }
            
            .dropdown-menu {
                position: fixed;
                left: 50% !important;
                transform: translateX(-50%);
                top: 50% !important;
                width: 90%;
                max-width: 280px;
            }
            
            .page-header {
                padding: 20px;
            }
        }

        /* Grid de estadísticas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Botón flotante WhatsApp */
        .whatsapp-float {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 1000;
        }

        .whatsapp-float a {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: white;
            text-decoration: none;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Cliente info compacto */
        .client-info-compact {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }
        
        .client-text {
            min-width: 0;
            flex: 1;
        }
        
        .client-name {
            font-weight: 600;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .client-phone {
            font-size: 0.75rem;
            color: var(--text-light);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                GRETA · Gestión de Turnos
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
                        <a class="nav-link" href="Servicios(Dueña).php">
                            <i class="bi bi-scissors me-2"></i>Servicios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gestion-turnos-dueña.php">
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
                <a class="nav-link" href="Servicios(Dueña).php">
                    <i class="bi bi-scissors me-2"></i> Servicios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="gestion-turnos-dueña.php">
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
    <div class="main-content" style="margin-top:75px;">
        <div class="container-fluid">
            <!-- ENCABEZADO PRINCIPAL - NUEVO ESTILO -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="h2 mb-2 fw-bold text-white">
                                    <i class="bi bi-calendar-check me-2"></i>Gestión de Turnos
                                </h1>
                                <p class="text-white mb-0 opacity-75">Control de agenda y servicios - <?= $fecha_formateada ?></p>
                            </div>
                            <div class="col-md-4 text-end">
                                <i class="bi bi-calendar-check display-4 opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtro de fecha -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-funnel me-2"></i>Filtro por Fecha
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="row g-3 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label">Seleccionar fecha:</label>
                                    <div class="input-group">
                                        <input type="date" name="fecha" value="<?= $fecha_actual ?>" 
                                               class="form-control" 
                                               onchange="this.form.submit()">
                                        <button type="button" class="btn btn-outline-primary" 
                                                onclick="window.location.href='gestion-turnos-dueña.php?fecha=<?= date('Y-m-d') ?>'">
                                            <i class="bi bi-calendar-day me-2"></i> Hoy
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="Calendario.php" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-2"></i> Nuevo Turno
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <!-- ✅✅✅ NUEVA ALERTA PARA WHATSAPP -->
<?php if (isset($_SESSION['whatsapp_url'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <h4 class="alert-heading">
        <i class="bi bi-whatsapp me-2"></i>¡Recordatorio listo para enviar!
    </h4>
    <p class="mb-3">
        Se ha generado el recordatorio para <strong><?= $_SESSION['whatsapp_cliente'] ?></strong>. 
        Haz clic en el botón para abrir WhatsApp:
    </p>
    
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <a href="<?= $_SESSION['whatsapp_url'] ?>" 
           target="_blank" 
           class="btn btn-success btn-lg">
            <i class="bi bi-whatsapp me-2"></i>
            Enviar WhatsApp a <?= $_SESSION['whatsapp_cliente'] ?>
        </a>
        
        <a href="gestion-turnos-dueña.php?fecha=<?= $fecha_actual ?>" 
           class="btn btn-outline-secondary">
            Continuar sin enviar
        </a>
    </div>
    
    <hr>
    <p class="mb-0 small">
        <strong>Teléfono:</strong> <?= $_SESSION['whatsapp_telefono'] ?><br>
        <strong>Nota:</strong> El mensaje incluye enlaces para que el cliente confirme o cancele el turno.
    </p>
    
    <button type="button" class="btn-close" data-bs-dismiss="alert" 
            onclick="limpiarWhatsAppSession()"></button>
</div>

<script>
function limpiarWhatsAppSession() {
    // Opcional: limpiar la sesión via AJAX
    fetch('limpiar-session.php?tipo=whatsapp')
        .then(response => response.json())
        .then(data => console.log('Session limpiada'));
}
</script>

<?php 
// Limpiar la sesión después de mostrar
unset($_SESSION['whatsapp_url']);
unset($_SESSION['whatsapp_cliente']); 
unset($_SESSION['whatsapp_telefono']);
?>
<?php endif; ?>

            <!-- Tarjetas de estadísticas -->
            <div class="stats-grid">
                <div class="stat-card card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">Total Turnos</h6>
                                <h3 class="card-title mb-0"><?= $total_turnos ?></h3>
                                <p class="small mb-0">Programados para hoy</p>
                            </div>
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">Confirmados</h6>
                                <h3 class="card-title mb-0"><?= count($turnos_confirmados) ?></h3>
                                <p class="small mb-0">Turnos confirmados</p>
                                <?php if ($total_turnos > 0): ?>
                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: <?= round((count($turnos_confirmados) / $total_turnos) * 100) ?>%" 
                                             aria-valuenow="<?= round((count($turnos_confirmados) / $total_turnos) * 100) ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted"><?= round((count($turnos_confirmados) / $total_turnos) * 100) ?>% del total</small>
                                <?php endif; ?>
                            </div>
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">En Proceso</h6>
                                <h3 class="card-title mb-0"><?= count($turnos_proceso) ?></h3>
                                <p class="small mb-0">Servicios en curso</p>
                                <?php if ($total_turnos > 0): ?>
                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: <?= round((count($turnos_proceso) / $total_turnos) * 100) ?>%" 
                                             aria-valuenow="<?= round((count($turnos_proceso) / $total_turnos) * 100) ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted"><?= round((count($turnos_proceso) / $total_turnos) * 100) ?>% del total</small>
                                <?php endif; ?>
                            </div>
                            <i class="bi bi-clock"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">Pagados</h6>
                                <h3 class="card-title mb-0"><?= count($turnos_pagados) ?></h3>
                                <p class="small mb-0">Servicios finalizados</p>
                                <?php if ($total_turnos > 0): ?>
                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                             style="width: <?= round((count($turnos_pagados) / $total_turnos) * 100) ?>%" 
                                             aria-valuenow="<?= round((count($turnos_pagados) / $total_turnos) * 100) ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted"><?= round((count($turnos_pagados) / $total_turnos) * 100) ?>% del total</small>
                                <?php endif; ?>
                            </div>
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-1">Ingresos</h6>
                                <h3 class="card-title mb-0">$<?= number_format($ingresos, 0, ',', '.') ?></h3>
                                <p class="small mb-0">Total del día</p>
                            </div>
                            <i class="bi bi-graph-up"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de turnos -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-table me-2"></i>Turnos del Día - <?= $fecha_formateada ?>
                            </h5>
                            <div>
                                <span class="badge bg-primary me-2"><?= $total_turnos ?> total</span>
                                <span class="badge bg-success"><?= count($turnos_confirmados) ?> confirmados</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($turnos_del_dia)): ?>
                                <div class="empty-state">
                                    <i class="bi bi-calendar-x"></i>
                                    <h4 class="text-dark">No hay turnos programados</h4>
                                    <p class="text-muted mb-3">No hay turnos agendados para el <?= $fecha_formateada ?></p>
                                    <a href="Calendario.php" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i> Agendar Nuevo Turno
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th width="10%" class="text-center">Hora</th>
                                                <th width="22%" class="text-center">Cliente</th>
                                                <th width="18%" class="text-center">Servicio</th>
                                                <th width="12%" class="text-center">Precio</th>
                                                <th width="15%" class="text-center">Estado</th>
                                                <th width="23%" class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($turnos_del_dia as $turno): ?>
                                                <tr>
                                                    <td class="fw-bold text-center"><?= date('H:i', strtotime($turno['hora'])) ?></td>
                                                    <td>
                                                        <div class="client-info-compact">
                                                            <div class="client-avatar">
                                                                <?= strtoupper(substr($turno['nombre_cliente'], 0, 1)) ?>
                                                            </div>
                                                            <div class="client-text">
                                                                <div class="client-name"><?= htmlspecialchars($turno['nombre_cliente'] . ' ' . $turno['apellido_cliente']) ?></div>
                                                                <div class="client-phone"><?= htmlspecialchars($turno['telefono_cliente'] ?? 'Sin teléfono') ?></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="fw-semibold text-center"><?= htmlspecialchars($turno['servicio_nombre']) ?></td>
                                                    <td class="text-center">
                                                        <?php if ($turno['precio'] > 0): ?>
                                                            <span class="price-highlight">$<?= number_format($turno['precio'], 0, ',', '.') ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted fst-italic">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php
                                                        $statusClass = '';
                                                        $statusIcon = '';
                                                        switch($turno['estado_id']) {
                                                            case 1: 
                                                                $statusClass = 'status-confirmed'; 
                                                                $statusIcon = 'bi-check-circle';
                                                                break;
                                                            case 4: 
                                                                $statusClass = 'status-process'; 
                                                                $statusIcon = 'bi-play-circle';
                                                                break;
                                                            case 5: 
                                                                $statusClass = 'status-pending'; 
                                                                $statusIcon = 'bi-clock';
                                                                break;
                                                            case 6: 
                                                                $statusClass = 'status-cancelled'; 
                                                                $statusIcon = 'bi-x-circle';
                                                                break;
                                                            case 7: 
                                                                $statusClass = 'status-paid'; 
                                                                $statusIcon = 'bi-currency-dollar';
                                                                break;
                                                            default: 
                                                                $statusClass = 'status-pending';
                                                                $statusIcon = 'bi-question-circle';
                                                        }
                                                        ?>
                                                        <span class="status-badge <?= $statusClass ?>">
                                                            <i class="bi <?= $statusIcon ?> me-1"></i>
                                                            <?= htmlspecialchars($turno['estado_nombre']) ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="action-buttons">
                                                            <!-- Botón Ver -->
                                                            <button type="button" class="btn-action btn-view" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#detalleTurnoModal<?= $turno['ID'] ?>"
                                                                    title="Ver detalles">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                            
                                                            <!-- Botón WhatsApp Básico -->
                                                            <?php if (!empty($turno['telefono_cliente'])): ?>
                                                                <?php
                                                                $telefono = preg_replace('/[^0-9]/', '', $turno['telefono_cliente']);
                                                                $nombre_cliente = $turno['nombre_cliente'] . ' ' . $turno['apellido_cliente'];
                                                                $servicio = $turno['servicio_nombre'];
                                                                $fecha_turno = date('d/m/Y', strtotime($turno['fecha']));
                                                                $hora_turno = date('H:i', strtotime($turno['hora']));
                                                                
                                                                $mensaje_whatsapp_basico = rawurlencode("¡Hola $nombre_cliente! 😊\n\nInformación de tu turno en *GRETA Estética*:\n\n📅 *Fecha:* $fecha_turno\n⏰ *Hora:* $hora_turno\n💅 *Servicio:* $servicio\n\n📍 *Dirección:* Virgen de la Merced 2345 - Córdoba\n📞 *Teléfono:* 3517896906\n\n¡Te esperamos! ✨");
                                                                ?>
                                                                <a href="https://wa.me/<?= $telefono ?>?text=<?= $mensaje_whatsapp_basico ?>" 
                                                                   target="_blank" 
                                                                   class="btn btn-whatsapp"
                                                                   title="Contactar por WhatsApp">
                                                                    <i class="bi bi-whatsapp"></i>
                                                                </a>
                                                            <?php endif; ?>

                                                            <!-- Dropdown de Acciones -->
                                                            <div class="dropdown">
                                                                <button class="btn btn-primary btn-sm dropdown-toggle" 
                                                                        type="button" 
                                                                        data-bs-toggle="dropdown"
                                                                        aria-expanded="false">
                                                                    <i class="bi bi-gear"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <!-- Solicitar Confirmación -->
                                                                    <?php if (!empty($turno['telefono_cliente']) && in_array($turno['estado_id'], [1, 5])): ?>
                                                                        <li>
                                                                            <form method="POST" action="" style="display: inline; width: 100%;">
                                                                                <input type="hidden" name="turno_id" value="<?= $turno['ID'] ?>">
                                                                                <button type="submit" name="solicitar_confirmacion" value="1" 
                                                                                        class="dropdown-item text-info w-100 text-start border-0 bg-transparent"
                                                                                        onclick="return confirm('¿Solicitar confirmación a <?= htmlspecialchars($turno['nombre_cliente']) ?>?')">
                                                                                    <i class="bi bi-whatsapp me-2"></i>
                                                                                    Solicitar Confirmación
                                                                                </button>
                                                                            </form>
                                                                        </li>
                                                                        <li><hr class="dropdown-divider"></li>
                                                                    <?php endif; ?>

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
                                                                                [4, 'Marcar como En Proceso', 'primary'],
                                                                                [6, 'Cancelar Turno', 'danger']
                                                                            ];
                                                                            break;
                                                                        case 4:
                                                                            $transiciones = [
                                                                                [1, 'Volver a Confirmado', 'info'],
                                                                                [6, 'Cancelar Turno', 'danger']
                                                                            ];
                                                                            if ($turno['precio'] > 0) {
                                                                                $transiciones[] = [888, 'Procesar Pago', 'success'];
                                                                            }
                                                                            break;
                                                                        case 7:
                                                                            $transiciones = [
                                                                                [4, 'Revertir a En Proceso', 'info']
                                                                            ];
                                                                            break;
                                                                        case 6:
                                                                            $transiciones = [
                                                                                [5, 'Reactivar como Pendiente', 'info']
                                                                            ];
                                                                            break;
                                                                    }

                                                                    foreach ($transiciones as $transicion):
                                                                    ?>
                                                                        <li>
                                                                            <?php if ($transicion[0] == 888): ?>
                                                                                <a class="dropdown-item text-success" 
                                                                                   href="procesar-pago.php?turno_id=<?= $turno['ID'] ?>">
                                                                                    <i class="bi bi-currency-dollar me-2"></i>
                                                                                    <?= $transicion[1] ?>
                                                                                </a>
                                                                            <?php else: ?>
                                                                                <form method="POST" action="" style="display: inline; width: 100%;">
                                                                                    <input type="hidden" name="turno_id" value="<?= $turno['ID'] ?>">
                                                                                    <input type="hidden" name="nuevo_estado" value="<?= $transicion[0] ?>">
                                                                                    <button type="submit" name="cambiar_estado" value="1" 
                                                                                            class="dropdown-item text-<?= $transicion[2] ?> w-100 text-start border-0 bg-transparent"
                                                                                            onclick="return confirm('¿<?= $transicion[1] ?> para <?= htmlspecialchars($turno['nombre_cliente']) ?>?')">
                                                                                        <i class="bi <?= 
                                                                                            $transicion[0] == 1 ? 'bi-check-circle' : 
                                                                                            ($transicion[0] == 4 ? 'bi-play-circle' : 
                                                                                            ($transicion[0] == 6 ? 'bi-x-circle' : 
                                                                                            ($transicion[0] == 5 ? 'bi-arrow-clockwise' : 'bi-arrow-counterclockwise')))
                                                                                        ?> me-2"></i>
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
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 mt-4">
        <small>© <?= date('Y'); ?> GRETA Estética · Todos los derechos reservados</small>
    </footer>

    <!-- Botón flotante de WhatsApp -->
    <div class="whatsapp-float">
        <a href="https://wa.me/5493517896906?text=Hola%20GRETA%20Estética,%20me%20gustaría%20sacar%20un%20turno" 
           target="_blank" 
           class="btn btn-success btn-lg rounded-circle shadow"
           title="Contactar por WhatsApp">
            <i class="bi bi-whatsapp"></i>
        </a>
    </div>

    <!-- MODALES - Solo modal de detalles -->
    <?php foreach ($turnos_del_dia as $turno): ?>
        <!-- Modal para detalles del turno -->
        <div class="modal fade" id="detalleTurnoModal<?= $turno['ID'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-info-circle me-2"></i>Detalles del Turno
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-semibold text-dark mb-3">
                                    <i class="bi bi-person me-2"></i>Información del Cliente
                                </h6>
                                <p><strong>Nombre:</strong> <?= htmlspecialchars($turno['nombre_cliente'] . ' ' . $turno['apellido_cliente']) ?></p>
                                <p><strong>Teléfono:</strong> <?= htmlspecialchars($turno['telefono_cliente'] ?? 'No especificado') ?></p>
                                <?php if ($turno['grupo_turnos_id']): ?>
                                    <p><strong>Tipo:</strong> <span class="badge bg-info">Turno en Grupo</span></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-semibold text-dark mb-3">
                                    <i class="bi bi-scissors me-2"></i>Información del Servicio
                                </h6>
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
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle sidebar en vista móvil
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('show');
    });

    // Prevenir que los dropdowns se cierren al hacer clic dentro de ellos
    document.addEventListener('DOMContentLoaded', function() {
        var dropdowns = document.querySelectorAll('.dropdown-menu');
        dropdowns.forEach(function(dropdown) {
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    });
</script>
</body>
</html>