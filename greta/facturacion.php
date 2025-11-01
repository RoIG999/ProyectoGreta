<?php
// facturacion.php - Sistema de facturación legal
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Solo dueña/admin pueden facturar
$rol = $_SESSION['usuario_rol'] ?? '';
$rol_normalizado = mb_strtolower($rol, 'UTF-8');
$rol_normalizado = strtr($rol_normalizado, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n']);

if (!in_array($rol_normalizado, ['duena', 'dueña', 'admin'])) {
    header('Location: login.php?e=perm');
    exit;
}

include("conexion.php");

// Obtener grupos pagados pendientes de facturación
$sql_grupos = "SELECT gt.*, p.id as pago_id, p.metodo_pago, p.fecha_pago
               FROM grupo_turnos gt
               INNER JOIN pagos p ON gt.id = p.grupo_turnos_id
               LEFT JOIN facturas f ON gt.id = f.grupo_turnos_id
               WHERE gt.estado = 'pagado' AND f.id IS NULL
               ORDER BY p.fecha_pago DESC";
$grupos_pagados = $conn->query($sql_grupos)->fetch_all(MYSQLI_ASSOC);

// Emitir factura
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emitir_factura'])) {
    $grupo_id = $_POST['grupo_id'];
    $cliente_email = $_POST['cliente_email'];
    $cliente_direccion = $_POST['cliente_direccion'];
    
    // Aquí iría la integración con AFIP
    // Por ahora simulamos la emisión
    
    $conn->begin_transaction();
    
    try {
        // Obtener información del grupo
        $sql_grupo = "SELECT * FROM grupo_turnos WHERE id = ?";
        $stmt = $conn->prepare($sql_grupo);
        $stmt->bind_param("i", $grupo_id);
        $stmt->execute();
        $grupo = $stmt->get_result()->fetch_assoc();
        
        // Generar número de factura (en producción usar AFIP)
        $sql_ultima = "SELECT COALESCE(MAX(numero_factura), 0) + 1 as siguiente FROM facturas";
        $siguiente_numero = $conn->query($sql_ultima)->fetch_assoc()['siguiente'];
        
        // Simular CAE (en producción obtener de AFIP)
        $cae = '701' . str_pad(rand(0, 9999999999), 11, '0', STR_PAD_LEFT) . '9';
        $vencimiento_cae = date('Y-m-d', strtotime('+10 days'));
        
        // Insertar factura
        $sql_factura = "INSERT INTO facturas (grupo_turnos_id, punto_venta, numero_factura, cae, 
                                             fecha_vencimiento_cae, total, estado, cliente_nombre, 
                                             cliente_apellido, cliente_dni, cliente_direccion, cliente_email) 
                        VALUES (?, 1, ?, ?, ?, ?, 'emitida', ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql_factura);
        $stmt->bind_param("iissssssss", $grupo_id, $siguiente_numero, $cae, $vencimiento_cae, 
                         $grupo['total'], $grupo['cliente_nombre'], $grupo['cliente_apellido'],
                         $grupo['cliente_dni'], $cliente_direccion, $cliente_email);
        $stmt->execute();
        $factura_id = $conn->insert_id;
        
        // Generar PDF de la factura (simulado)
        $url_factura = "facturas/factura_" . $factura_id . ".pdf";
        file_put_contents($url_factura, "Factura simulada - En producción se genera con AFIP");
        
        // Actualizar URL de la factura
        $sql_update = "UPDATE facturas SET url_factura = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("si", $url_factura, $factura_id);
        $stmt->execute();
        
        $conn->commit();
        
        $_SESSION['success'] = "✅ Factura emitida correctamente - N° 0001-" . str_pad($siguiente_numero, 8, '0', STR_PAD_LEFT);
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "❌ Error al emitir factura: " . $e->getMessage();
    }
    
    header("Location: facturacion.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturación - GRETA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">🧾 Sistema de Facturación</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Grupos Pagados Pendientes de Facturación</h4>
            </div>
            <div class="card-body">
                <?php if (empty($grupos_pagados)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-receipt fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No hay grupos pendientes de facturación</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Método Pago</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($grupos_pagados as $grupo): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($grupo['cliente_nombre'] . ' ' . $grupo['cliente_apellido']) ?></strong><br>
                                            <small class="text-muted">DNI: <?= htmlspecialchars($grupo['cliente_dni'] ?? 'No especificado') ?></small>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($grupo['fecha'])) ?></td>
                                        <td class="fw-bold">$<?= number_format($grupo['total'], 2, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-info"><?= ucfirst($grupo['metodo_pago']) ?></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-success btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalFactura<?= $grupo['id'] ?>">
                                                <i class="bi bi-receipt"></i> Emitir Factura
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal para emitir factura -->
                                    <div class="modal fade" id="modalFactura<?= $grupo['id'] ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Emitir Factura</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Cliente</label>
                                                            <input type="text" class="form-control" 
                                                                   value="<?= htmlspecialchars($grupo['cliente_nombre'] . ' ' . $grupo['cliente_apellido']) ?>" readonly>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">DNI</label>
                                                            <input type="text" class="form-control" 
                                                                   value="<?= htmlspecialchars($grupo['cliente_dni'] ?? '') ?>" readonly>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Dirección *</label>
                                                            <input type="text" class="form-control" name="cliente_direccion" required>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Email *</label>
                                                            <input type="email" class="form-control" name="cliente_email" required>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Total a Facturar</label>
                                                            <input type="text" class="form-control" 
                                                                   value="$<?= number_format($grupo['total'], 2, ',', '.') ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" name="emitir_factura" class="btn btn-success">
                                                            <i class="bi bi-check-circle"></i> Emitir Factura
                                                        </button>
                                                    </div>
                                                </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>