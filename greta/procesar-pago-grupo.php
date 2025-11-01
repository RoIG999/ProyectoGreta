<?php
// procesar-pago-grupo.php
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

// Obtener grupo de turnos si se proporciona
$grupo_id = $_GET['grupo_id'] ?? $_POST['grupo_id'] ?? null;

if ($grupo_id) {
    // Obtener información del grupo
    $sql_grupo = "SELECT gt.*, 
                         GROUP_CONCAT(CONCAT(t.hora, ' - ', s.nombre) SEPARATOR '; ') as servicios_detalle,
                         COUNT(t.id) as cantidad_turnos
                  FROM grupo_turnos gt
                  LEFT JOIN turno t ON gt.id = t.grupo_turnos_id
                  LEFT JOIN servicio s ON t.ID_servicio_FK = s.ID
                  WHERE gt.id = ?
                  GROUP BY gt.id";
    
    $stmt = $conn->prepare($sql_grupo);
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $grupo = $stmt->get_result()->fetch_assoc();
}

// Procesar pago del grupo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['procesar_pago_grupo'])) {
    $grupo_id = $_POST['grupo_id'];
    $metodo_pago = $_POST['metodo_pago'];
    $monto_total = $_POST['monto_total'];
    
    $conn->begin_transaction();
    
    try {
        // 1. Registrar pago principal en la tabla pagos
        $sql_pago = "INSERT INTO pagos (grupo_turnos_id, monto, metodo_pago, fecha_pago, estado, 
                                        alias_cbu, cbu, titular_cuenta, cuit_cuenta, banco) 
                     VALUES (?, ?, ?, NOW(), 'completado', 
                             'GRETA.SALON', '0170204660000008787653', 
                             'GRETA SALON DE BELLEZA S.R.L.', '30-71234567-8', 'Banco de Córdoba')";
        
        $stmt_pago = $conn->prepare($sql_pago);
        $stmt_pago->bind_param("ids", $grupo_id, $monto_total, $metodo_pago);
        $stmt_pago->execute();
        $pago_id = $conn->insert_id;
        
        // 2. Actualizar estado de todos los turnos del grupo a Pagado (estado 7)
        $sql_update_turnos = "UPDATE turno SET ID_estado_turno_FK = 7 WHERE grupo_turnos_id = ?";
        $stmt_update = $conn->prepare($sql_update_turnos);
        $stmt_update->bind_param("i", $grupo_id);
        $stmt_update->execute();
        
        // 3. Actualizar estado del grupo a pagado
        $sql_update_grupo = "UPDATE grupo_turnos SET estado = 'pagado', total = ? WHERE id = ?";
        $stmt_grupo = $conn->prepare($sql_update_grupo);
        $stmt_grupo->bind_param("di", $monto_total, $grupo_id);
        $stmt_grupo->execute();
        
        $conn->commit();
        
        $_SESSION['success'] = "✅ Pago del grupo procesado correctamente. Todos los turnos marcados como pagados.";
        header("Location: gestion-turnos" . ($rol_normalizado === 'supervisora' ? '' : '-dueña') . ".php?fecha=" . date('Y-m-d'));
        exit;
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "❌ Error al procesar el pago: " . $e->getMessage();
    }
}

// Procesar comprobante de transferencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subir_comprobante'])) {
    $pago_id = $_POST['pago_id'];
    
    if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/comprobantes/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = 'comprobante_' . $pago_id . '_' . time() . '.jpg';
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $filePath)) {
            $sql_update = "UPDATE pagos SET comprobante_url = ? WHERE id = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("si", $fileName, $pago_id);
            $stmt->execute();
            
            $_SESSION['success'] = "✅ Comprobante subido correctamente";
        } else {
            $_SESSION['error'] = "❌ Error al subir el comprobante";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Pago Grupal - GRETA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .bank-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .bank-info h4 {
            color: white;
            margin-bottom: 1.5rem;
        }
        .info-item {
            margin-bottom: 0.8rem;
            padding: 0.8rem;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
        }
        .payment-methods .card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .payment-methods .card:hover,
        .payment-methods .card.selected {
            border-color: #667eea;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>💳 Procesar Pago Grupal</h1>
            <a href="gestion-turnos<?= $rol_normalizado === 'supervisora' ? '' : '-dueña' ?>.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if ($grupo): ?>
            <!-- Información del Grupo -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">👥 Grupo de Turnos</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Cliente:</strong> <?= htmlspecialchars($grupo['cliente_nombre'] . ' ' . $grupo['cliente_apellido']) ?></p>
                            <p><strong>Teléfono:</strong> <?= htmlspecialchars($grupo['cliente_telefono']) ?></p>
                            <p><strong>DNI:</strong> <?= htmlspecialchars($grupo['cliente_dni'] ?? 'No especificado') ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($grupo['fecha'])) ?></p>
                            <p><strong>Cantidad de Turnos:</strong> <?= $grupo['cantidad_turnos'] ?></p>
                            <p><strong>Servicios:</strong> <?= htmlspecialchars($grupo['servicios_detalle']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Bancaria para Transferencia -->
            <div class="bank-info">
                <h4>🏦 Datos para Transferencia</h4>
                <div class="info-item">
                    <strong>Alias CBU:</strong> GRETA.SALON
                </div>
                <div class="info-item">
                    <strong>CBU:</strong> 0170204660000008787653
                </div>
                <div class="info-item">
                    <strong>Titular:</strong> GRETA SALON DE BELLEZA S.R.L.
                </div>
                <div class="info-item">
                    <strong>CUIT:</strong> 30-71234567-8
                </div>
                <div class="info-item">
                    <strong>Banco:</strong> Banco de Córdoba
                </div>
            </div>

            <!-- Formulario de Pago -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">💰 Procesar Pago</h4>
                </div>
                <div class="card-body">
                    <form method="POST" id="formPago">
                        <input type="hidden" name="grupo_id" value="<?= $grupo_id ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Monto Total *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control form-control-lg" 
                                       name="monto_total" value="<?= $grupo['total'] ?>" 
                                       step="0.01" min="0" required>
                            </div>
                            <div class="form-text">
                                Total calculado: <strong>$<?= number_format($grupo['total'], 2, ',', '.') ?></strong>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Método de Pago *</label>
                            <div class="payment-methods row g-3">
                                <div class="col-md-3">
                                    <div class="card text-center p-3" onclick="selectPaymentMethod('efectivo')">
                                        <i class="bi bi-cash-coin fs-1 text-success"></i>
                                        <div class="card-body">
                                            <h6>Efectivo</h6>
                                            <small>Pago en efectivo</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center p-3" onclick="selectPaymentMethod('transferencia')">
                                        <i class="bi bi-bank fs-1 text-primary"></i>
                                        <div class="card-body">
                                            <h6>Transferencia</h6>
                                            <small>Transferencia bancaria</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center p-3" onclick="selectPaymentMethod('tarjeta')">
                                        <i class="bi bi-credit-card fs-1 text-warning"></i>
                                        <div class="card-body">
                                            <h6>Tarjeta</h6>
                                            <small>Débito/Crédito</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center p-3" onclick="selectPaymentMethod('mercadopago')">
                                        <i class="bi bi-wallet2 fs-1 text-danger"></i>
                                        <div class="card-body">
                                            <h6>Mercado Pago</h6>
                                            <small>Pago digital</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="metodo_pago" id="metodo_pago" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="procesar_pago_grupo" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Confirmar Pago del Grupo
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> No se encontró el grupo de turnos especificado.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPaymentMethod(metodo) {
            document.getElementById('metodo_pago').value = metodo;
            
            // Remover clase selected de todos
            document.querySelectorAll('.payment-methods .card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Agregar clase selected al seleccionado
            event.currentTarget.classList.add('selected');
        }

        // Validación del formulario
        document.getElementById('formPago').addEventListener('submit', function(e) {
            const metodoPago = document.getElementById('metodo_pago').value;
            if (!metodoPago) {
                e.preventDefault();
                alert('Por favor selecciona un método de pago');
                return;
            }
            
            if (!confirm('¿Confirmas el procesamiento del pago para todo el grupo de turnos?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>