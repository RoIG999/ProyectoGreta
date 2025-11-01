<?php
// EliminarAsistencia.php
include("conexion.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header("Location: historial.php?error=id"); exit;
}

$stmt = $conn->prepare("DELETE FROM asistencias WHERE id = ?");
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
  $stmt->close();
  header("Location: historial.php?eliminada=1"); exit;
} else {
  $stmt->close();
  header("Location: historial.php?error=eliminar"); exit;
}
