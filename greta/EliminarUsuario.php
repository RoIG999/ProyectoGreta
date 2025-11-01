<?php
// greta/EliminarUsuario.php
include("conexion.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header("Location: gestionUsuarios.php?error=id"); exit;
}

$sql = "UPDATE usuarios SET estado = 0 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
  $stmt->close();
  header("Location: gestionUsuarios.php?ok=desactivado"); exit;
} else {
  $stmt->close();
  header("Location: gestionUsuarios.php?error=desactivar"); exit;
}
