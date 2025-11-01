<?php
require_once '../conexion.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $stmt = $pdo->query("SELECT id, nombre FROM servicios WHERE activo = 1");
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($servicios);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al cargar servicios: ' . $e->getMessage()]);
}
?>