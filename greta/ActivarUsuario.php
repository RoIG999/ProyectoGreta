<?php
// greta/ActivarUsuario.php
include("conexion.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header("Location: UsuariosInactivos.php?error=id"); exit;
}

$sql = "UPDATE usuarios SET estado = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
  $stmt->close();
  // Asegurate que el nombre del archivo coincida (UsuariosInactivos.php vs usuariosInactivos.php)
  header("Location: UsuariosInactivos.php?ok=activado"); exit;
} else {
  $stmt->close();
  header("Location: UsuariosInactivos.php?error=activar"); exit;
}
