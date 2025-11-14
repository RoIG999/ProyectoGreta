<?php
session_start();
include("conexion.php");

$token = trim($_GET['token'] ?? '');
$accion = $_GET['accion'] ?? '';

if (empty($token) || empty($accion)) {
    die("Enlace inválido");
}

// Buscar el turno por token
$sql = "SELECT t.*, rs.nombre as servicio_nombre 
        FROM turno t 
        LEFT JOIN rubro_servicio rs ON t.ID_servicio_FK = rs.ID 
        WHERE t.token_confirmacion = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en el sistema. Por favor, contacta al establecimiento.");
}

$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$turno = $result->fetch_assoc();

if (!$turno) {
    die("Turno no encontrado o enlace expirado");
}

// Procesar la acción
if ($accion === 'confirmar') {
    $nuevo_estado = 1; // Confirmado
    $mensaje = "¡Turno confirmado exitosamente!";
    $icono = "✓";
    $clase = "success";
} elseif ($accion === 'cancelar') {
    $nuevo_estado = 6; // Cancelado
    $mensaje = "Turno cancelado exitosamente";
    $icono = "✓";
    $clase = "warning";
} else {
    die("Acción inválida");
}

// Actualizar el turno
$sql_update = "UPDATE turno SET 
               ID_estado_turno_FK = ?, 
               confirmado = 1, 
               fecha_confirmacion = NOW()
               WHERE ID = ?";

$stmt_update = $conn->prepare($sql_update);

if (!$stmt_update) {
    die("Error en el sistema. Por favor, contacta al establecimiento.");
}

$stmt_update->bind_param("ii", $nuevo_estado, $turno['ID']);
$exito = $stmt_update->execute();

if (!$exito) {
    $mensaje = "Error al procesar la solicitud";
    $icono = "✗";
    $clase = "danger";
}

// Cerrar conexiones
$stmt->close();
$stmt_update->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Turno - GRETA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
        }
        .confirmation-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .icon-success {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        .icon-warning {
            font-size: 4rem;
            color: #ffc107;
            margin-bottom: 20px;
        }
        .icon-error {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .btn-whatsapp {
            background: linear-gradient(135deg, #25D366, #128C7E);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
        }
        .btn-whatsapp:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
        }
    </style>
</head>
<body>
    <div class="confirmation-card">
        <?php if ($exito): ?>
            <div class="icon-<?= $clase ?>">
                <?= $icono ?>
            </div>
            <h2 class="text-<?= $clase ?>">¡Éxito!</h2>
            <h4 class="mb-4"><?= $mensaje ?></h4>
            
            <div class="card mb-4">
                <div class="card-body text-start">
                    <h5>Detalles del Turno:</h5>
                    <p><strong>Cliente:</strong> <?= htmlspecialchars($turno['nombre_cliente'] . ' ' . $turno['apellido_cliente']) ?></p>
                    <p><strong>Servicio:</strong> <?= htmlspecialchars($turno['servicio_nombre']) ?></p>
                    <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($turno['fecha'])) ?></p>
                    <p><strong>Hora:</strong> <?= date('H:i', strtotime($turno['hora'])) ?></p>
                </div>
            </div>
            
            <?php if ($accion === 'confirmar'): ?>
                <div class="alert alert-info">
                    <strong>¡Te esperamos!</strong> Por favor llega 10 minutos antes de tu turno.
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <strong>¿Necesitas reagendar?</strong> Contáctanos para coordinar un nuevo turno.
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="icon-error">✗</div>
            <h2 class="text-danger">Error</h2>
            <p class="mb-4"><?= $mensaje ?></p>
        <?php endif; ?>
        
        <a href="https://wa.me/5493517896906" class="btn-whatsapp" target="_blank">
            <i class="bi bi-whatsapp"></i> Contactar por WhatsApp
        </a>
        
        <div class="mt-3">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">Volver</a>
        </div>
    </div>
</body>
</html>