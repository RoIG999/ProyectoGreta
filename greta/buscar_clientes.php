<?php
// buscar_clientes.php
header('Content-Type: application/json');

// Conexión a la base de datos
include("conexion.php");

$termino = $_GET['termino'] ?? '';
$dni = $_GET['dni'] ?? '';

$clientes = [];

if (!empty($termino)) {
    // Buscar por nombre, apellido o DNI
    $sql = "SELECT DISTINCT 
                nombre_cliente as nombre, 
                apellido_cliente as apellido, 
                telefono_cliente as telefono,
                dni_cliente as dni
            FROM turno 
            WHERE (nombre_cliente LIKE ? OR apellido_cliente LIKE ? OR dni_cliente LIKE ?)
            AND dni_cliente IS NOT NULL
            ORDER BY nombre_cliente, apellido_cliente 
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $likeTermino = "%$termino%";
    $stmt->bind_param("sss", $likeTermino, $likeTermino, $likeTermino);
    
} elseif (!empty($dni)) {
    // Buscar específicamente por DNI
    $sql = "SELECT DISTINCT 
                nombre_cliente as nombre, 
                apellido_cliente as apellido, 
                telefono_cliente as telefono,
                dni_cliente as dni
            FROM turno 
            WHERE dni_cliente = ?
            ORDER BY fecha DESC 
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dni);
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
}

$conn->close();
echo json_encode($clientes);
?>