<?php
// activarServicio.php
include("conexion.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header("Location: servicios.php?error=id_invalido");
  exit;
}

// Activar servicio
$sql = "UPDATE servicios SET estado = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
  $stmt->close();
  header("Location: servicios.php?ok=activado");
  exit;
} else {
  $stmt->close();
  header("Location: servicios.php?error=activar");
  exit;
}