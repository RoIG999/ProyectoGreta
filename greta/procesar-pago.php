<?php
// procesar-pago.php - VERSIÓN MEJORADA CON COMPROBANTE VISUAL
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Normalizar rol
$rol = $_SESSION['usuario_rol'] ?? '';
$rol_normalizado = mb_strtolower($rol, 'UTF-8');
$rol_normalizado = strtr($rol_normalizado, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n']);

// Solo dueña, admin o supervisor
if (!in_array($rol_normalizado, ['duena', 'dueña', 'supervisor', 'admin', 'supervisora'])) {
    header('Location: login.php?e=perm');
    exit;
}

include("conexion.php");

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener parámetros
$turno_id = $_GET['turno_id'] ?? $_POST['turno_id'] ?? null;
$grupo_id = $_GET['grupo_id'] ?? $_POST['grupo_id'] ?? null;
$descargar_comprobante = $_GET['descargar_comprobante'] ?? null;
$ver_comprobante = $_GET['ver_comprobante'] ?? null;

// Determinar modo basado en la estructura real
$modo = 'individual';
$titulo = "Procesar Pago";

if ($grupo_id) {
    $modo = 'grupal';
    $titulo = "Finalizar Compra";
} elseif ($turno_id) {
    // Verificar si el turno pertenece a un grupo
    $sql_grupo = "SELECT grupo_turnos_id FROM turno WHERE ID = ?";
    $stmt = $conn->prepare($sql_grupo);
    if ($stmt) {
        $stmt->bind_param("i", $turno_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $turno_data = $result->fetch_assoc();
        $stmt->close();
        
        if ($turno_data && $turno_data['grupo_turnos_id']) {
            $modo = 'grupal';
            $grupo_id = $turno_data['grupo_turnos_id'];
            $titulo = "Finalizar Compra";
        }
    }
}

// Variables para almacenar información
$grupo = null;
$turnos_grupo = [];
$turno_individual = null;
$primer_turno_id = null;

// Obtener información según el modo
if ($modo === 'grupal' && $grupo_id) {
    // Obtener información del grupo
    $sql_grupo = "SELECT gt.* FROM grupo_turnos gt WHERE gt.id = ?";
    $stmt = $conn->prepare($sql_grupo);
    if ($stmt) {
        $stmt->bind_param("i", $grupo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $grupo = $result->fetch_assoc();
        $stmt->close();
    } else {
        $_SESSION['error'] = "Error en la consulta del grupo";
        $grupo = null;
    }
    
    if ($grupo) {
        // Obtener todos los turnos del grupo con información completa
        $sql_turnos = "SELECT t.*, s.nombre as servicio_nombre, s.precio,
                              s.duracion, et.nombre as estado_nombre
                       FROM turno t
                       LEFT JOIN servicio s ON t.ID_servicio_FK = s.ID
                       LEFT JOIN estado_turno et ON t.ID_estado_turno_FK = et.ID
                       WHERE t.grupo_turnos_id = ?
                       ORDER BY t.hora ASC";
        
        $stmt_turnos = $conn->prepare($sql_turnos);
        if ($stmt_turnos) {
            $stmt_turnos->bind_param("i", $grupo_id);
            $stmt_turnos->execute();
            $result_turnos = $stmt_turnos->get_result();
            $turnos_grupo = $result_turnos->fetch_all(MYSQLI_ASSOC);
            $stmt_turnos->close();
            
            // Calcular total automáticamente desde los servicios
            $total_calculado = 0;
            foreach ($turnos_grupo as $turno) {
                $total_calculado += $turno['precio'];
            }
            $grupo['total_calculado'] = $total_calculado;
            $grupo['cantidad_turnos'] = count($turnos_grupo);
            
            // Obtener el primer turno_id para usar en el pago
            $primer_turno_id = $turnos_grupo[0]['ID'] ?? null;
        } else {
            $turnos_grupo = [];
            $grupo['total_calculado'] = 0;
            $grupo['cantidad_turnos'] = 0;
            $primer_turno_id = null;
        }
    }
    
} elseif ($modo === 'individual' && $turno_id) {
    // Obtener turno individual con información completa
    $sql_turno = "SELECT t.*, s.nombre as servicio_nombre, s.precio,
                         s.duracion, et.nombre as estado_nombre
                  FROM turno t
                  LEFT JOIN servicio s ON t.ID_servicio_FK = s.ID
                  LEFT JOIN estado_turno et ON t.ID_estado_turno_FK = et.ID
                  WHERE t.ID = ?";
    
    $stmt = $conn->prepare($sql_turno);
    if ($stmt) {
        $stmt->bind_param("i", $turno_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $turno_individual = $result->fetch_assoc();
        $stmt->close();
        
        if (!$turno_individual) {
            $_SESSION['error'] = "El turno especificado no existe";
            $turno_individual = null;
        }
    } else {
        $_SESSION['error'] = "Error en la consulta del turno individual";
        $turno_individual = null;
    }
}

// VER COMPROBANTE - Nueva funcionalidad
if ($ver_comprobante) {
    // Obtener datos del comprobante
    $sql_comprobante = "SELECT f.*, p.metodo_pago, p.fecha_pago 
                       FROM facturas f 
                       LEFT JOIN pagos p ON f.numero_factura = p.transaccion_id 
                       WHERE f.numero_factura = ?";
    $stmt = $conn->prepare($sql_comprobante);
    $stmt->bind_param("s", $ver_comprobante);
    $stmt->execute();
    $comprobante = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($comprobante) {
        // Mostrar comprobante directamente en el navegador
        $html = generarComprobanteHTML($comprobante);
        echo $html;
        exit;
    }
}

// DESCARGAR COMPROBANTE
if ($descargar_comprobante) {
    // Obtener datos del comprobante
    $sql_comprobante = "SELECT f.*, p.metodo_pago, p.fecha_pago 
                       FROM facturas f 
                       LEFT JOIN pagos p ON f.numero_factura = p.transaccion_id 
                       WHERE f.numero_factura = ?";
    $stmt = $conn->prepare($sql_comprobante);
    $stmt->bind_param("s", $descargar_comprobante);
    $stmt->execute();
    $comprobante = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($comprobante) {
        // Forzar descarga como HTML
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="comprobante_' . $comprobante['numero_factura'] . '.html"');
        $html = generarComprobanteHTML($comprobante);
        echo $html;
        exit;
    }
}

// Procesar pago - VERSIÓN SIMPLIFICADA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['procesar_pago'])) {
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    $monto_total = $_POST['monto_total'] ?? 0;
    $modo = $_POST['modo'] ?? '';
    $comprobante_data = $_POST['comprobante_data'] ?? '';
    
    if (empty($metodo_pago) || empty($monto_total)) {
        $_SESSION['error'] = "Faltan datos requeridos para procesar el pago";
    } else {
        $conn->begin_transaction();
        
        try {
            if ($modo === 'grupal') {
                $grupo_id = $_POST['grupo_id'] ?? null;
                
                if (!$grupo_id || !$primer_turno_id) {
                    throw new Exception("Datos del grupo no válidos");
                }
                
                // Insertar pago
                $sql_pago = "INSERT INTO pagos (grupo_turnos_id, turno_id, monto, metodo_pago, fecha_pago, estado) 
                             VALUES (?, ?, ?, ?, NOW(), 'completado')";
                
                $stmt_pago = $conn->prepare($sql_pago);
                if (!$stmt_pago) {
                    throw new Exception("Error preparando consulta de pago: " . $conn->error);
                }
                
                $stmt_pago->bind_param("iids", $grupo_id, $primer_turno_id, $monto_total, $metodo_pago);
                if (!$stmt_pago->execute()) {
                    throw new Exception("Error ejecutando pago grupal: " . $stmt_pago->error);
                }
                $pago_id = $conn->insert_id;
                $stmt_pago->close();
                
                // Actualizar estados
                $sql_update_turnos = "UPDATE turno SET ID_estado_turno_FK = 7 WHERE grupo_turnos_id = ?";
                $stmt_update = $conn->prepare($sql_update_turnos);
                if ($stmt_update) {
                    $stmt_update->bind_param("i", $grupo_id);
                    $stmt_update->execute();
                    $turnos_afectados = $stmt_update->affected_rows;
                    $stmt_update->close();
                }
                
                $sql_update_grupo = "UPDATE grupo_turnos SET estado = 'pagado', total = ? WHERE id = ?";
                $stmt_grupo = $conn->prepare($sql_update_grupo);
                if ($stmt_grupo) {
                    $stmt_grupo->bind_param("di", $monto_total, $grupo_id);
                    $stmt_grupo->execute();
                    $stmt_grupo->close();
                }
                
                $mensaje_exito = "✨ Pago procesado exitosamente. " . ($turnos_afectados ?? 0) . " servicios marcados como pagados.";
                
            } else {
                // MODO INDIVIDUAL
                $turno_id = $_POST['turno_id'] ?? null;
                
                if (!$turno_id) {
                    throw new Exception("ID de turno no válido");
                }
                
                // Insertar pago
                $sql_pago = "INSERT INTO pagos (turno_id, monto, metodo_pago, fecha_pago, estado) 
                             VALUES (?, ?, ?, NOW(), 'completado')";
                
                $stmt_pago = $conn->prepare($sql_pago);
                if (!$stmt_pago) {
                    throw new Exception("Error preparando consulta de pago: " . $conn->error);
                }
                
                $stmt_pago->bind_param("ids", $turno_id, $monto_total, $metodo_pago);
                if (!$stmt_pago->execute()) {
                    throw new Exception("Error ejecutando pago individual: " . $stmt_pago->error);
                }
                $pago_id = $conn->insert_id;
                $stmt_pago->close();
                
                // Actualizar estado del turno
                $sql_update_turno = "UPDATE turno SET ID_estado_turno_FK = 7 WHERE ID = ?";
                $stmt_update = $conn->prepare($sql_update_turno);
                if ($stmt_update) {
                    $stmt_update->bind_param("i", $turno_id);
                    $stmt_update->execute();
                    $stmt_update->close();
                }
                
                $mensaje_exito = "✨ Pago procesado exitosamente.";
            }
            
            // Procesar comprobante
            if (!empty($comprobante_data)) {
                $sql_comprobante = "UPDATE pagos SET comprobante_url = ? WHERE id = ?";
                $stmt_comp = $conn->prepare($sql_comprobante);
                if ($stmt_comp) {
                    $stmt_comp->bind_param("si", $comprobante_data, $pago_id);
                    $stmt_comp->execute();
                    $stmt_comp->close();
                    $mensaje_exito .= " 📄 Comprobante adjuntado.";
                }
            }
            
            // Generar comprobante automáticamente
            $numero_comprobante = "COMP-" . date('Ymd') . "-" . str_pad($pago_id, 6, '0', STR_PAD_LEFT);
            
            // Determinar datos del cliente
            if ($modo === 'grupal') {
                $cliente_nombre = $grupo['cliente_nombre'];
                $cliente_apellido = $grupo['cliente_apellido'];
                $cliente_dni = $grupo['cliente_dni'];
                $grupo_id_comprobante = $grupo_id;
            } else {
                $cliente_nombre = $turno_individual['nombre_cliente'];
                $cliente_apellido = $turno_individual['apellido_cliente'];
                $cliente_dni = $turno_individual['dni_cliente'];
                $grupo_id_comprobante = null;
            }
            
            $sql_comprobante = "INSERT INTO facturas (grupo_turnos_id, numero_factura, total, estado, cliente_nombre, cliente_apellido, cliente_dni) 
                           VALUES (?, ?, ?, 'emitida', ?, ?, ?)";
            
            $stmt_comprobante = $conn->prepare($sql_comprobante);
            if ($stmt_comprobante) {
                $stmt_comprobante->bind_param("isdsss", $grupo_id_comprobante, $numero_comprobante, $monto_total, $cliente_nombre, $cliente_apellido, $cliente_dni);
                $stmt_comprobante->execute();
                $stmt_comprobante->close();
                
                // Actualizar pago con número de comprobante
                $sql_update_pago = "UPDATE pagos SET transaccion_id = ? WHERE id = ?";
                $stmt_update_pago = $conn->prepare($sql_update_pago);
                if ($stmt_update_pago) {
                    $stmt_update_pago->bind_param("si", $numero_comprobante, $pago_id);
                    $stmt_update_pago->execute();
                    $stmt_update_pago->close();
                }
                
                $mensaje_exito .= " 🧾 Comprobante $numero_comprobante generado.";
                
                // GUARDAR DATOS DE COMPROBANTE PARA MOSTRAR
                $_SESSION['comprobante_data'] = [
                    'numero' => $numero_comprobante,
                    'cliente' => $cliente_nombre . ' ' . $cliente_apellido,
                    'total' => $monto_total,
                    'fecha' => date('d/m/Y H:i'),
                    'metodo_pago' => $metodo_pago
                ];
                
                $_SESSION['ultimo_comprobante'] = $numero_comprobante;
            }
            
            $conn->commit();
            $_SESSION['success'] = $mensaje_exito;
            
            // Redirigir a la misma página para mostrar el comprobante
            header("Location: procesar-pago.php?turno_id=" . ($modo === 'individual' ? $turno_id : '') . "&grupo_id=" . ($modo === 'grupal' ? $grupo_id : ''));
            exit;
            
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error'] = "❌ Error al procesar el pago: " . $e->getMessage();
        }
    }
}

// Función para generar HTML de comprobante MEJORADO CON LOGO
function generarComprobanteHTML($comprobante) {
    $fecha_emision = date('d/m/Y', strtotime($comprobante['fecha_emision'] ?? $comprobante['fecha_pago'] ?? 'now'));
    $hora_emision = date('H:i', strtotime($comprobante['fecha_emision'] ?? $comprobante['fecha_pago'] ?? 'now'));
    
    // Logo de Greta en base64 (puedes reemplazar esto con la URL de tu logo)
    $logo_greta = 'data:image/svg+xml;base64,' . base64_encode('
        <svg width="200" height="80" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#8B5FBF"/>
                    <stop offset="100%" stop-color="#FF6B95"/>
                </linearGradient>
            </defs>
            <rect width="200" height="80" fill="url(#grad)" rx="15"/>
            <text x="100" y="45" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="24" font-weight="bold">GRETA</text>
            <text x="100" y="65" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="12">BEAUTY SALON</text>
        </svg>
    ');
    
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Comprobante ' . $comprobante['numero_factura'] . '</title>
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body { 
                font-family: "Inter", sans-serif; 
                margin: 20px; 
                color: #333;
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                min-height: 100vh;
            }
            
            .comprobante-container {
                max-width: 800px;
                margin: 0 auto;
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(139, 95, 191, 0.15);
                overflow: hidden;
                border: 1px solid rgba(139, 95, 191, 0.1);
            }
            
            .header { 
                background: linear-gradient(135deg, #8B5FBF 0%, #6A3093 100%);
                color: white;
                padding: 30px;
                text-align: center;
                position: relative;
                overflow: hidden;
            }
            
            .header::before {
                content: "";
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
                animation: rotate 20s linear infinite;
            }
            
            @keyframes rotate {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            
            .logo-container {
                margin-bottom: 20px;
                position: relative;
                z-index: 2;
            }
            
            .logo {
                max-width: 200px;
                height: auto;
                filter: drop-shadow(0 5px 15px rgba(0,0,0,0.3));
            }
            
            .empresa-info h1 {
                font-size: 28px;
                font-weight: 800;
                margin-bottom: 5px;
                color: white;
            }
            
            .empresa-info p {
                margin: 2px 0;
                opacity: 0.9;
                font-size: 14px;
            }
            
            .comprobante-title {
                font-size: 24px;
                font-weight: 700;
                margin: 15px 0 5px 0;
                color: white;
            }
            
            .comprobante-numero {
                font-size: 18px;
                font-weight: 600;
                margin: 5px 0;
                background: rgba(255,255,255,0.2);
                padding: 8px 16px;
                border-radius: 10px;
                display: inline-block;
            }
            
            .fecha-emision {
                margin-top: 10px;
                font-weight: 500;
            }
            
            .content {
                padding: 30px;
            }
            
            .section { 
                margin-bottom: 25px; 
            }
            
            .section-title { 
                background: linear-gradient(135deg, #8B5FBF, #6A3093);
                color: white;
                padding: 12px 20px;
                font-weight: 600;
                border-radius: 10px;
                margin-bottom: 15px;
                display: flex;
                align-items: center;
            }
            
            .section-title i {
                margin-right: 10px;
                font-size: 18px;
            }
            
            .datos-grid { 
                display: grid; 
                grid-template-columns: 1fr 1fr; 
                gap: 20px; 
                margin: 15px 0; 
            }
            
            .datos-item { 
                background: #f8f9fa;
                padding: 15px;
                border-radius: 10px;
                border-left: 4px solid #8B5FBF;
            }
            
            .datos-label { 
                font-weight: 600; 
                color: #6A3093;
                display: block;
                margin-bottom: 5px;
                font-size: 14px;
            }
            
            .datos-value {
                font-weight: 500;
                font-size: 16px;
            }
            
            .tabla-productos { 
                width: 100%; 
                border-collapse: collapse; 
                margin: 20px 0; 
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }
            
            .tabla-productos th { 
                background: linear-gradient(135deg, #8B5FBF, #6A3093);
                color: white; 
                padding: 15px; 
                text-align: left; 
                font-weight: 600;
                font-size: 14px;
            }
            
            .tabla-productos td { 
                padding: 12px 15px; 
                border-bottom: 1px solid #e9ecef;
                background: white;
            }
            
            .tabla-productos tr:hover td {
                background: #f8f9fa;
            }
            
            .total-section { 
                background: linear-gradient(135deg, #48BB78, #38A169);
                color: white;
                padding: 25px;
                border-radius: 15px;
                text-align: center;
                margin-top: 25px;
                box-shadow: 0 10px 30px rgba(72, 187, 120, 0.3);
            }
            
            .total-label {
                font-size: 16px;
                font-weight: 600;
                margin-bottom: 10px;
                opacity: 0.9;
            }
            
            .total-monto { 
                font-size: 32px; 
                font-weight: 800; 
                margin-top: 5px;
                text-shadow: 0 2px 10px rgba(0,0,0,0.2);
            }
            
            .footer { 
                margin-top: 30px; 
                text-align: center; 
                font-size: 12px; 
                color: #6c757d; 
                border-top: 2px dashed #dee2e6; 
                padding-top: 20px;
            }
            
            .qr-code {
                width: 120px;
                height: 120px;
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 10px;
                margin: 20px auto;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 10px;
                color: #6c757d;
                text-align: center;
                padding: 10px;
            }
            
            .actions {
                text-align: center;
                margin: 30px 0;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 15px;
            }
            
            .btn {
                padding: 12px 25px;
                border: none;
                border-radius: 10px;
                font-weight: 600;
                cursor: pointer;
                margin: 5px;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            
            .btn-print {
                background: linear-gradient(135deg, #8B5FBF, #6A3093);
                color: white;
            }
            
            .btn-close {
                background: #6c757d;
                color: white;
            }
            
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
            
            @media print {
                body { 
                    margin: 0; 
                    background: white;
                }
                .comprobante-container {
                    box-shadow: none;
                    border: none;
                    border-radius: 0;
                }
                .no-print { 
                    display: none; 
                }
                .actions {
                    display: none;
                }
            }
            
            @media (max-width: 768px) {
                body {
                    margin: 10px;
                }
                .datos-grid {
                    grid-template-columns: 1fr;
                }
                .content {
                    padding: 20px;
                }
                .header {
                    padding: 20px;
                }
            }
        </style>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    </head>
    <body>
        <div class="comprobante-container">
            <div class="header">
                <div class="logo-container">
                    <img src="' . $logo_greta . '" alt="GRETA BEAUTY SALON" class="logo">
                </div>
                <div class="empresa-info">
                    <h1>GRETA BEAUTY SALON</h1>
                    <p>Salón de Belleza Profesional</p>
                    <p>Av. Colón 1234, Córdoba Capital</p>
                    <p>Tel: +54 351 123-4567 | www.gretabeauty.com</p>
                </div>
                
                <div class="comprobante-title">COMPROBANTE DE PAGO</div>
                <div class="comprobante-numero">N°: ' . $comprobante['numero_factura'] . '</div>
                <div class="fecha-emision">
                    <strong>Fecha de Emisión:</strong> ' . $fecha_emision . ' | ' . $hora_emision . ' hs
                </div>
            </div>
            
            <div class="content">
                <div class="section">
                    <div class="section-title">
                        <i class="bi bi-person-circle"></i>
                        DATOS DEL CLIENTE
                    </div>
                    <div class="datos-grid">
                        <div class="datos-item">
                            <span class="datos-label">Nombre Completo:</span>
                            <div class="datos-value">' . $comprobante['cliente_nombre'] . ' ' . $comprobante['cliente_apellido'] . '</div>
                        </div>
                        <div class="datos-item">
                            <span class="datos-label">Documento:</span>
                            <div class="datos-value">' . ($comprobante['cliente_dni'] ?: 'Consumidor Final') . '</div>
                        </div>
                        <div class="datos-item">
                            <span class="datos-label">Método de Pago:</span>
                            <div class="datos-value">' . ucfirst($comprobante['metodo_pago'] ?? 'Efectivo') . '</div>
                        </div>
                        <div class="datos-item">
                            <span class="datos-label">Estado:</span>
                            <div class="datos-value"><span style="color: #48BB78; font-weight: bold;">✓ PAGADO</span></div>
                        </div>
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">
                        <i class="bi bi-list-check"></i>
                        DETALLE DE SERVICIOS
                    </div>
                    <table class="tabla-productos">
                        <thead>
                            <tr>
                                <th style="width: 60%;">Descripción del Servicio</th>
                                <th style="width: 20%;">Cantidad</th>
                                <th style="width: 20%;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Servicios de Belleza Profesional</td>
                                <td>1</td>
                                <td><strong>$' . number_format($comprobante['total'], 2, ',', '.') . '</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="total-section">
                    <div class="total-label">TOTAL A PAGAR</div>
                    <div class="total-monto">
                        $' . number_format($comprobante['total'], 2, ',', '.') . '
                    </div>
                </div>
                
                <div class="qr-code">
                    <div>
                        <i class="bi bi-qr-code" style="font-size: 24px; margin-bottom: 5px;"></i>
                        <div>CÓDIGO QR</div>
                        <div style="font-size: 8px; margin-top: 5px;">' . $comprobante['numero_factura'] . '</div>
                    </div>
                </div>
                
                <div class="footer">
                    <p style="font-weight: bold; margin: 5px 0; color: #8B5FBF;">COMPROBANTE AUTORIZADO - GRETA BEAUTY SALON</p>
                    <p style="margin: 3px 0;">¡Gracias por confiar en nosotros! Su satisfacción es nuestra prioridad.</p>
                    <p style="margin: 5px 0; font-style: italic; color: #6A3093;">
                        "Transformando tu belleza, realzando tu confianza"
                    </p>
                    <p style="margin: 10px 0 0 0; font-size: 10px; color: #6c757d;">
                        Este es un comprobante digital válido sin necesidad de firma manuscrita<br>
                        GRETA BEAUTY SALON © ' . date('Y') . ' | CUIT: 30-12345678-9
                    </p>
                </div>
            </div>
        </div>
        
        <div class="actions no-print">
            <button onclick="window.print()" class="btn btn-print">
                <i class="bi bi-printer me-2"></i> Imprimir Comprobante
            </button>
            <button onclick="window.close()" class="btn btn-close">
                <i class="bi bi-x-circle me-2"></i> Cerrar Ventana
            </button>
        </div>
        
        <script>
            window.onload = function() {
                // Auto-print en algunos navegadores cuando se abre para ver
                if (window.location.search.includes("ver_comprobante")) {
                    setTimeout(function() {
                        window.print();
                    }, 1000);
                }
            };
        </script>
    </body>
    </html>';
}

// Limpiar comprobante_data cuando se cambia de cliente
if (isset($_GET['turno_id']) || isset($_GET['grupo_id'])) {
    $current_turno = $_GET['turno_id'] ?? null;
    $current_grupo = $_GET['grupo_id'] ?? null;
    
    // Si los parámetros han cambiado, limpiar el comprobante anterior
    if ((isset($_SESSION['last_turno']) && $_SESSION['last_turno'] != $current_turno) ||
        (isset($_SESSION['last_grupo']) && $_SESSION['last_grupo'] != $current_grupo)) {
        unset($_SESSION['comprobante_data']);
    }
    
    $_SESSION['last_turno'] = $current_turno;
    $_SESSION['last_grupo'] = $current_grupo;
}

// CORREGIR BOTÓN VOLVER - Determinar la página correcta
$return_url = 'gestion-turnos.php';
if ($rol_normalizado === 'duena' || $rol_normalizado === 'dueña' || $rol_normalizado === 'admin') {
    $return_url = 'gestion-turnos-dueña.php';
} elseif ($rol_normalizado === 'supervisor' || $rol_normalizado === 'supervisora') {
    $return_url = 'gestion-turnos.php';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> - GRETA</title>
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
            --success: #48BB78;
            --warning: #ED8936;
            --info: #4299E1;
            --luxury-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 20px 60px rgba(139, 95, 191, 0.15);
            --hover-shadow: 0 30px 80px rgba(139, 95, 191, 0.25);
            --neon-glow: 0 0 20px rgba(139, 95, 191, 0.5);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #2D3748;
            overflow-x: hidden;
        }

        .luxury-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* Header con efecto neón mejorado */
        .neon-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 2.5rem;
            margin-bottom: 3rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--neon-glow);
            animation: neonPulse 3s infinite alternate, float 6s ease-in-out infinite;
        }

        @keyframes neonPulse {
            0% { box-shadow: 0 0 20px rgba(139, 95, 191, 0.5); }
            50% { box-shadow: 0 0 30px rgba(139, 95, 191, 0.8), 0 0 40px rgba(139, 95, 191, 0.6); }
            100% { box-shadow: 0 0 25px rgba(139, 95, 191, 0.7), 0 0 35px rgba(139, 95, 191, 0.5); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .neon-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
            border-radius: 50%;
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .neon-title {
            font-weight: 800;
            font-size: 3rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #FFFFFF, var(--accent-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: textGlow 2s ease-in-out infinite alternate;
        }

        @keyframes textGlow {
            from { text-shadow: 0 0 20px rgba(255, 255, 255, 0.5); }
            to { text-shadow: 0 0 30px rgba(255, 255, 255, 0.8), 0 0 40px rgba(255, 255, 255, 0.6); }
        }

        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 400;
            animation: fadeIn 2s ease-in;
        }

        /* Cards con efecto holográfico mejorado */
        .holographic-card {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.25) 0%, 
                rgba(255, 255, 255, 0.1) 50%, 
                rgba(255, 255, 255, 0.25) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            animation: cardEntrance 0.8s ease-out;
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .holographic-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.4), 
                transparent);
            transition: left 0.6s;
        }

        .holographic-card:hover::before {
            left: 100%;
        }

        .holographic-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.25), var(--neon-glow);
        }

        /* NUEVO: CUADRO DE COMPROBANTE INTERACTIVO Y LLAMATIVO */
        .comprobante-showcase {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.95) 0%, 
                rgba(248, 250, 252, 0.95) 50%,
                rgba(240, 249, 255, 0.95) 100%);
            border: 3px solid transparent;
            border-radius: 25px;
            padding: 2.5rem;
            margin: 2rem 0;
            box-shadow: 
                0 25px 50px rgba(139, 95, 191, 0.2),
                0 0 0 1px rgba(255, 255, 255, 0.8),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            position: relative;
            overflow: hidden;
            animation: showcaseEntrance 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(20px);
        }

        @keyframes showcaseEntrance {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .comprobante-showcase::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                #8B5FBF, #FF6B95, #48BB78, #4299E1, #8B5FBF);
            background-size: 200% 100%;
            animation: gradientShift 3s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .comprobante-showcase::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, 
                rgba(139, 95, 191, 0.1) 0%, 
                rgba(255, 107, 149, 0.05) 30%, 
                transparent 70%);
            animation: rotate 15s linear infinite;
            pointer-events: none;
        }

        .showcase-header {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            z-index: 2;
        }

        .showcase-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            display: inline-block;
            background: linear-gradient(135deg, #8B5FBF, #FF6B95);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 5px 15px rgba(139, 95, 191, 0.3));
            animation: iconBounce 2s ease-in-out infinite;
        }

        @keyframes iconBounce {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-10px) scale(1.1); }
        }

        .showcase-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #8B5FBF, #6A3093);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            text-shadow: 0 5px 15px rgba(139, 95, 191, 0.2);
        }

        .showcase-subtitle {
            font-size: 1.1rem;
            color: #6B7280;
            font-weight: 500;
        }

        .comprobante-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 2rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--card-color-1), var(--card-color-2));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .action-card:hover::before {
            transform: scaleX(1);
        }

        .action-card.ver { --card-color-1: #4299E1; --card-color-2: #38B2AC; }
        .action-card.descargar { --card-color-1: #48BB78; --card-color-2: #38A169; }
        .action-card.imprimir { --card-color-1: #ED8936; --card-color-2: #DD6B20; }

        .action-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            display: block;
            transition: all 0.3s ease;
        }

        .action-card.ver .action-icon { color: #4299E1; }
        .action-card.descargar .action-icon { color: #48BB78; }
        .action-card.imprimir .action-icon { color: #ED8936; }

        .action-card:hover .action-icon {
            transform: scale(1.2) rotate(5deg);
            animation: none;
        }

        .action-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #2D3748;
        }

        .action-description {
            color: #6B7280;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .comprobante-details {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
            border-left: 4px solid #8B5FBF;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .detail-item {
            text-align: center;
            padding: 1rem;
        }

        .detail-label {
            font-size: 0.9rem;
            color: #6B7280;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2D3748;
        }

        .detail-value.total {
            color: #48BB78;
            font-size: 1.3rem;
        }

        /* Resto de estilos existentes... */
        .payment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .payment-option {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 2rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
            animation: bounceIn 0.6s ease-out;
        }

        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }

        .payment-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .payment-option:hover {
            transform: translateY(-12px) scale(1.08);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            border-color: var(--primary);
        }

        .payment-option.selected {
            border-color: var(--primary);
            background: linear-gradient(135deg, #ffffff, #f8f7ff);
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(139, 95, 191, 0.4);
        }

        .payment-option.selected::before {
            transform: scaleX(1);
        }

        .payment-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            display: block;
            transition: all 0.3s ease;
            animation: iconFloat 3s ease-in-out infinite;
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(5deg); }
        }

        .payment-option:hover .payment-icon {
            transform: scale(1.3) rotate(10deg);
            animation: none;
        }

        .efectivo { color: var(--success); }
        .transferencia { color: var(--info); }
        .tarjeta { color: var(--warning); }
        .mercadopago { color: #00B2FF; }

        .btn-neon {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 16px;
            padding: 1.2rem 3rem;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 15px 35px rgba(139, 95, 191, 0.4);
            position: relative;
            overflow: hidden;
            animation: buttonPulse 2s infinite;
        }

        @keyframes buttonPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .btn-neon::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.6s;
        }

        .btn-neon:hover {
            transform: translateY(-8px) scale(1.08);
            box-shadow: 0 30px 60px rgba(139, 95, 191, 0.6), var(--neon-glow);
        }

        .btn-neon:hover::before {
            left: 100%;
        }

        .service-item {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 6px solid var(--primary);
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            animation: slideInLeft 0.6s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .service-item:hover {
            transform: translateX(15px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .total-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 20px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 20px 40px rgba(139, 95, 191, 0.3);
            animation: pulse 2s infinite, glow 1.5s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { box-shadow: 0 0 20px rgba(139, 95, 191, 0.4); }
            to { box-shadow: 0 0 30px rgba(139, 95, 191, 0.8), 0 0 40px rgba(139, 95, 191, 0.6); }
        }

        .upload-zone {
            border: 3px dashed rgba(139, 95, 191, 0.3);
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            background: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out;
        }

        .upload-zone:hover {
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(139, 95, 191, 0.2);
        }

        .upload-zone.dragover {
            border-color: var(--primary);
            background: rgba(139, 95, 191, 0.1);
            transform: scale(1.05);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: floatParticle 8s ease-in-out infinite;
        }

        @keyframes floatParticle {
            0%, 100% { 
                transform: translateY(0px) translateX(0px) rotate(0deg); 
                opacity: 0;
            }
            50% { 
                transform: translateY(-100px) translateX(50px) rotate(180deg); 
                opacity: 0.8;
            }
        }

        .cliente-info {
            background: linear-gradient(135deg, #f0f4ff, #f7faff);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 6px solid var(--primary);
            animation: gradientShift 3s ease-in-out infinite alternate;
        }

        .status-badge {
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 5px 15px rgba(255, 107, 149, 0.3);
            animation: badgePulse 2s infinite;
        }

        @keyframes badgePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        .animate-slide-in {
            animation: slideInRight 0.8s ease-out 0.2s both;
        }

        .animate-bounce-in {
            animation: bounceIn 0.6s ease-out;
        }

        @media (max-width: 768px) {
            .neon-title {
                font-size: 2.2rem;
            }
            
            .neon-header {
                padding: 2rem 1.5rem;
            }
            
            .holographic-card {
                padding: 2rem 1.5rem;
            }
            
            .payment-grid {
                grid-template-columns: 1fr;
            }
            
            .btn-neon {
                padding: 1rem 2rem;
                font-size: 1rem;
            }
            
            .comprobante-actions-grid {
                grid-template-columns: 1fr;
            }
            
            .showcase-title {
                font-size: 1.8rem;
            }
            
            .showcase-icon {
                font-size: 3rem;
            }
        }

        .cliente-section {
            background: linear-gradient(135deg, rgba(240, 249, 255, 0.9), rgba(224, 242, 254, 0.9));
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .metodo-pago-section {
            background: linear-gradient(135deg, rgba(236, 253, 245, 0.9), rgba(209, 250, 229, 0.9));
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body>
    <!-- Efecto de Partículas Mejorado -->
    <div class="particles" id="particles"></div>

    <div class="luxury-container">
        <!-- Header Neon Mejorado -->
        <div class="neon-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="neon-title"><?= $titulo ?></h1>
                    <p class="subtitle mb-0">💫 Experiencia Premium • Comprobante Digital</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <!-- BOTÓN VOLVER CORREGIDO -->
                    <a href="<?= $return_url ?>" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-arrow-left me-2"></i> Volver a Gestión
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success holographic-card animate-fade-in" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-3 fs-2"></i>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">¡Éxito!</h5>
                        <p class="mb-0"><?= $_SESSION['success'] ?></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger holographic-card animate-fade-in" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-2"></i>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Error</h5>
                        <p class="mb-0"><?= $_SESSION['error'] ?></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- NUEVO: CUADRO DE COMPROBANTE INTERACTIVO Y LLAMATIVO -->
        <?php if (isset($_SESSION['comprobante_data']) && ($_SESSION['comprobante_data']['numero'] ?? '') != ''): ?>
        <div class="comprobante-showcase">
            <div class="showcase-header">
                <i class="bi bi-patch-check-fill showcase-icon"></i>
                <h1 class="showcase-title">¡Comprobante Generado!</h1>
                <p class="showcase-subtitle">Tu comprobante está listo. Elige cómo quieres utilizarlo:</p>
            </div>

            <div class="comprobante-actions-grid">
                <!-- VER COMPROBANTE -->
                <div class="action-card ver" onclick="verComprobante('<?= $_SESSION['comprobante_data']['numero'] ?>')">
                    <i class="bi bi-eye-fill action-icon"></i>
                    <h3 class="action-title">Ver Comprobante</h3>
                    <p class="action-description">
                        Abre el comprobante en una nueva pestaña para visualizarlo inmediatamente en tu navegador.
                    </p>
                </div>

                <!-- DESCARGAR COMPROBANTE -->
                <div class="action-card descargar" onclick="descargarComprobante('<?= $_SESSION['comprobante_data']['numero'] ?>')">
                    <i class="bi bi-download action-icon"></i>
                    <h3 class="action-title">Descargar</h3>
                    <p class="action-description">
                        Guarda el comprobante en tu dispositivo como archivo HTML para tenerlo siempre disponible.
                    </p>
                </div>

                <!-- IMPRIMIR COMPROBANTE -->
                <div class="action-card imprimir" onclick="imprimirComprobante('<?= $_SESSION['comprobante_data']['numero'] ?>')">
                    <i class="bi bi-printer-fill action-icon"></i>
                    <h3 class="action-title">Imprimir</h3>
                    <p class="action-description">
                        Abre e imprime automáticamente el comprobante en tu impresora configurada.
                    </p>
                </div>
            </div>

            <div class="comprobante-details">
                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">Número de Comprobante</div>
                        <div class="detail-value"><?= $_SESSION['comprobante_data']['numero'] ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Cliente</div>
                        <div class="detail-value"><?= $_SESSION['comprobante_data']['cliente'] ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Método de Pago</div>
                        <div class="detail-value"><?= ucfirst($_SESSION['comprobante_data']['metodo_pago']) ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Total</div>
                        <div class="detail-value total">$<?= number_format($_SESSION['comprobante_data']['total'], 2, ',', '.') ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Fecha y Hora</div>
                        <div class="detail-value"><?= $_SESSION['comprobante_data']['fecha'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php 
            // Limpiar datos de comprobante después de mostrarlos
            unset($_SESSION['comprobante_data']);
        endif; ?>

        <?php if (($modo === 'grupal' && isset($grupo)) || ($modo === 'individual' && isset($turno_individual))): ?>
            
            <!-- Información del Cliente -->
            <div class="holographic-card cliente-section">
                <div class="row align-items-center mb-4">
                    <div class="col-md-8">
                        <h3 class="mb-2"><i class="bi bi-person-check-fill me-2"></i> Información del Cliente</h3>
                        <div class="d-flex flex-wrap gap-3">
                            <span class="status-badge">
                                <i class="bi bi-calendar-check me-1"></i>
                                <?= $modo === 'grupal' ? $grupo['cantidad_turnos'] . ' servicios' : '1 servicio' ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="text-muted">Fecha: <?= $modo === 'grupal' ? date('d/m/Y', strtotime($grupo['fecha'])) : date('d/m/Y', strtotime($turno_individual['fecha'])) ?></div>
                    </div>
                </div>
                
                <div class="cliente-info">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="text-primary mb-3"><?= $modo === 'grupal' ? htmlspecialchars($grupo['cliente_nombre'] . ' ' . $grupo['cliente_apellido']) : htmlspecialchars($turno_individual['nombre_cliente'] . ' ' . $turno_individual['apellido_cliente']) ?></h4>
                            <p class="mb-2"><i class="bi bi-telephone me-2"></i> <?= $modo === 'grupal' ? htmlspecialchars($grupo['cliente_telefono']) : htmlspecialchars($turno_individual['telefono_cliente'] ?? 'No especificado') ?></p>
                            <p class="mb-0"><i class="bi bi-person-badge me-2"></i> <?= $modo === 'grupal' ? htmlspecialchars($grupo['cliente_dni'] ?? 'No especificado') : htmlspecialchars($turno_individual['dni_cliente'] ?? 'No especificado') ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="total-card">
                                <h6 class="opacity-90 mb-2">Total a Pagar</h6>
                                <h2 class="mb-0">$<?= number_format($modo === 'grupal' ? $grupo['total_calculado'] : $turno_individual['precio'], 2, ',', '.') ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalles de Servicios -->
                <h4 class="mb-4"><i class="bi bi-list-check me-2"></i> Detalles del Pedido</h4>
                <?php if ($modo === 'grupal'): ?>
                    <?php foreach ($turnos_grupo as $index => $turno): ?>
                    <div class="service-item" style="animation-delay: <?= $index * 0.1 ?>s">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="text-primary mb-2"><?= htmlspecialchars($turno['servicio_nombre']) ?></h5>
                                <div class="d-flex flex-wrap gap-3 text-muted">
                                    <span><i class="bi bi-clock me-1"></i> <?= date('H:i', strtotime($turno['hora'])) ?></span>
                                    <span><i class="bi bi-hourglass me-1"></i> <?= $turno['duracion'] ?>min</span>
                                    <span class="badge bg-light text-dark"><i class="bi bi-tag me-1"></i> <?= htmlspecialchars($turno['estado_nombre']) ?></span>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <h4 class="text-success mb-0">$<?= number_format($turno['precio'], 2, ',', '.') ?></h4>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                <?php else: ?>
                    <div class="service-item">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="text-primary mb-2"><?= htmlspecialchars($turno_individual['servicio_nombre']) ?></h5>
                                <div class="d-flex flex-wrap gap-3 text-muted">
                                    <span><i class="bi bi-clock me-1"></i> <?= date('H:i', strtotime($turno_individual['hora'])) ?></span>
                                    <span><i class="bi bi-hourglass me-1"></i> <?= $turno_individual['duracion'] ?>min</span>
                                    <span class="badge bg-light text-dark"><i class="bi bi-tag me-1"></i> <?= htmlspecialchars($turno_individual['estado_nombre']) ?></span>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <h4 class="text-success mb-0">$<?= number_format($turno_individual['precio'], 2, ',', '.') ?></h4>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Formulario de Pago -->
            <div class="holographic-card metodo-pago-section" style="animation-delay: 0.3s">
                <h3 class="mb-4"><i class="bi bi-credit-card-2-front me-2"></i> Método de Pago</h3>
                
                <form method="POST" id="formPago">
                    <input type="hidden" name="modo" value="<?= $modo ?>">
                    <?php if ($modo === 'grupal'): ?>
                        <input type="hidden" name="grupo_id" value="<?= $grupo_id ?>">
                    <?php else: ?>
                        <input type="hidden" name="turno_id" value="<?= $turno_id ?>">
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold fs-5">💰 Monto Total</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-primary text-white border-0 fs-4">$</span>
                            <input type="number" class="form-control border-0 fs-4" 
                                   name="monto_total" 
                                   value="<?= $modo === 'grupal' ? $grupo['total_calculado'] : $turno_individual['precio'] ?>" 
                                   step="0.01" min="0" required
                                   style="font-weight: 700; background: rgba(255,255,255,0.9);">
                        </div>
                        <div class="form-text mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            <?php if ($modo === 'grupal'): ?>
                                Total calculado automáticamente de <?= $grupo['cantidad_turnos'] ?> servicios
                            <?php else: ?>
                                Precio del servicio establecido
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold fs-5 mb-3">💳 Selecciona tu método de pago</label>
                        <div class="payment-grid">
                            <div class="payment-option" onclick="selectPaymentMethod('efectivo')">
                                <i class="bi bi-cash-coin payment-icon efectivo"></i>
                                <h5 class="fw-bold">Efectivo</h5>
                                <small class="text-muted">Pago en efectivo</small>
                            </div>
                            <div class="payment-option" onclick="selectPaymentMethod('transferencia')">
                                <i class="bi bi-bank2 payment-icon transferencia"></i>
                                <h5 class="fw-bold">Transferencia</h5>
                                <small class="text-muted">Transferencia bancaria</small>
                            </div>
                            <div class="payment-option" onclick="selectPaymentMethod('tarjeta')">
                                <i class="bi bi-credit-card payment-icon tarjeta"></i>
                                <h5 class="fw-bold">Tarjeta</h5>
                                <small class="text-muted">Débito/Crédito</small>
                            </div>
                            <div class="payment-option" onclick="selectPaymentMethod('mercadopago')">
                                <i class="bi bi-wallet2 payment-icon mercadopago"></i>
                                <h5 class="fw-bold">Mercado Pago</h5>
                                <small class="text-muted">Pago digital</small>
                            </div>
                        </div>
                        <input type="hidden" name="metodo_pago" id="metodo_pago" required>
                    </div>

                    <!-- Comprobante (Opcional) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold fs-5">📄 Comprobante (Opcional)</label>
                        <div class="upload-zone" id="uploadZone" onclick="document.getElementById('comprobanteFile').click()">
                            <i class="bi bi-cloud-arrow-up fs-1 text-primary mb-3"></i>
                            <h5>Subir Comprobante</h5>
                            <p class="text-muted">Arrastra tu archivo o haz clic aquí</p>
                            <p class="small text-muted">Formatos: JPG, PNG, PDF (Máx. 5MB)</p>
                            <input type="file" id="comprobanteFile" class="d-none" accept="image/*,.pdf">
                            <input type="hidden" name="comprobante_data" id="comprobante_data">
                        </div>

                        <div id="comprobantePreview" class="d-none mt-3 text-center">
                            <div class="holographic-card">
                                <img id="previewImage" src="" class="img-fluid rounded shadow mb-3" style="max-height: 200px;">
                                <button type="button" class="btn btn-outline-danger" onclick="removeComprobante()">
                                    <i class="bi bi-trash me-2"></i> Eliminar Comprobante
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Comprobante Preview -->
                    <div class="factura-preview">
                        <h5><i class="bi bi-receipt me-2"></i> Comprobante Automático</h5>
                        <p class="text-muted mb-3">Se generará automáticamente un comprobante de pago:</p>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check-circle text-success me-2"></i> Formato profesional</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i> Sin IVA incluido</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i> Código QR válido</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i> Número de comprobante</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" name="procesar_pago" class="btn btn-neon btn-lg">
                            <i class="bi bi-lightning-charge me-2"></i> 
                            Confirmar Pago y Generar Comprobante
                        </button>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <div class="holographic-card text-center">
                <i class="bi bi-exclamation-triangle fs-1 text-warning mb-3"></i>
                <h3>Información no encontrada</h3>
                <p class="text-muted">No se encontró la información del turno o grupo especificado.</p>
                <a href="<?= $return_url ?>" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-2"></i> Volver a la gestión
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // FUNCIONES PARA EL COMPROBANTE
        function verComprobante(comprobanteNumero) {
            if (comprobanteNumero) {
                window.open('procesar-pago.php?ver_comprobante=' + comprobanteNumero, '_blank');
            } else {
                alert('No hay comprobante disponible para ver');
            }
        }

        function descargarComprobante(comprobanteNumero) {
            if (comprobanteNumero) {
                window.location.href = 'procesar-pago.php?descargar_comprobante=' + comprobanteNumero;
            } else {
                alert('No hay comprobante disponible para descargar');
            }
        }

        function imprimirComprobante(comprobanteNumero) {
            if (comprobanteNumero) {
                const ventana = window.open('procesar-pago.php?ver_comprobante=' + comprobanteNumero, '_blank');
                // Esperar a que cargue la ventana y luego imprimir
                setTimeout(() => {
                    if (ventana && !ventana.closed) {
                        ventana.print();
                    }
                }, 1500);
            } else {
                alert('No hay comprobante disponible para imprimir');
            }
        }

        // Selección de método de pago con efectos
        function selectPaymentMethod(metodo) {
            document.getElementById('metodo_pago').value = metodo;
            
            document.querySelectorAll('.payment-option').forEach(card => {
                card.classList.remove('selected');
            });
            
            const selectedCard = event.currentTarget;
            selectedCard.classList.add('selected');
            
            // Efecto de confeti visual mejorado
            selectedCard.style.transform = 'translateY(-15px) scale(1.1)';
            setTimeout(() => {
                selectedCard.style.transform = 'translateY(-8px) scale(1.02)';
            }, 200);
        }

        // Drag & Drop mejorado para comprobante
        function setupDragDrop() {
            const uploadZone = document.getElementById('uploadZone');
            const fileInput = document.getElementById('comprobanteFile');
            
            if (!uploadZone) return;
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadZone.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                uploadZone.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                uploadZone.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                uploadZone.classList.add('dragover');
            }
            
            function unhighlight() {
                uploadZone.classList.remove('dragover');
            }
            
            uploadZone.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            }
        }

        // Manejar archivos
        function handleComprobanteUpload(event) {
            const files = event.target.files;
            handleFiles(files);
        }

        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];
                
                if (file.size > 5 * 1024 * 1024) {
                    alert('El archivo es demasiado grande. Máximo 5MB.');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('comprobante_data').value = e.target.result;
                    
                    if (file.type.startsWith('image/')) {
                        document.getElementById('previewImage').src = e.target.result;
                        document.getElementById('comprobantePreview').classList.remove('d-none');
                    } else {
                        document.getElementById('comprobantePreview').classList.remove('d-none');
                        document.getElementById('previewImage').src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2YzZjRmNiIvPjx0ZXh0IHg9IjEwMCIgeT0iMTAwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM2YzcyODAiPlBERiBGaWxlPC90ZXh0Pjwvc3ZnPg==';
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        function removeComprobante() {
            document.getElementById('comprobanteFile').value = '';
            document.getElementById('comprobante_data').value = '';
            document.getElementById('comprobantePreview').classList.add('d-none');
        }

        // Validación del formulario
        document.getElementById('formPago')?.addEventListener('submit', function(e) {
            const metodoPago = document.getElementById('metodo_pago').value;
            if (!metodoPago) {
                e.preventDefault();
                alert('Por favor selecciona un método de pago');
                return;
            }

            const monto = document.querySelector('input[name="monto_total"]').value;
            const confirmacion = confirm(`¿Confirmar pago de $${monto} via ${metodoPago}?\n\nSe generará automáticamente el comprobante.`);
            
            if (!confirmacion) {
                e.preventDefault();
            }
        });

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            selectPaymentMethod('efectivo');
            setupDragDrop();
            
            const fileInput = document.getElementById('comprobanteFile');
            if (fileInput) {
                fileInput.addEventListener('change', handleComprobanteUpload);
            }
        });
    </script>
</body>
</html>