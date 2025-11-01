<?php
include("conexion.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("ID inválido.");
}

$id = intval($_GET['id']);
$sql = "SELECT a.*, u.nombre FROM asistencias a JOIN usuarios u ON a.id_usuario = u.id WHERE a.id = $id";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
  die("Registro no encontrado.");
}

$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $asistencia = isset($_POST['asistencia']) ? 1 : 0;
  $anotacion = $conn->real_escape_string($_POST['anotaciones']);
  $conn->query("UPDATE asistencias SET asistencia = $asistencia, anotaciones = '$anotacion' WHERE id = $id");
  header("Location: historial.php?modificada=1");
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Asistencia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2>Editar Asistencia de <?= $row['nombre'] ?> (<?= date("d-m-Y", strtotime($row['fecha'])) ?>)</h2>
  <form method="POST">
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="asistencia" id="asistencia" <?= $row['asistencia'] ? 'checked' : '' ?>>
      <label class="form-check-label" for="asistencia">Presente</label>
    </div>
    <div class="mb-3">
      <label for="anotaciones" class="form-label">Anotaciones</label>
      <textarea name="anotaciones" id="anotaciones" class="form-control" rows="3"><?= $row['anotaciones'] ?></textarea>
    </div>
    <button type="submit" class="btn btn-success">Guardar Cambios</button>
    <a href="historial.php" class="btn btn-secondary">Cancelar</a>
  </form>
</body>
</html>
