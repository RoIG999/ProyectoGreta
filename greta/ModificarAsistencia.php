<?php
// ModificarAsistencia.php
include("conexion.php");

// Asegurar zona horaria (opcional)
if (!ini_get('date.timezone')) {
  date_default_timezone_set('America/Argentina/Cordoba');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $asistencia  = isset($_POST['asistencia']) ? 1 : 0;
  $anotaciones = (string)($_POST['anotaciones'] ?? '');

  if ($id <= 0) {
    header("Location: historial.php?error=id"); exit;
  }

  // (Opcional) Limite de longitud defensivo
  if (strlen($anotaciones) > 1000) { $anotaciones = substr($anotaciones, 0, 1000); }

  $stmt = $conn->prepare("UPDATE asistencias SET asistencia = ?, anotaciones = ? WHERE id = ?");
  $stmt->bind_param("isi", $asistencia, $anotaciones, $id);

  if ($stmt->execute()) {
    $stmt->close();
    header("Location: historial.php?modificado=1"); exit;
  } else {
    $stmt->close();
    header("Location: historial.php?error=update"); exit;
  }
}

// ---- Carga por GET para mostrar el form ----
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  die("<div class='alert alert-danger'>ID inválido.</div>");
}

$sql = "SELECT a.*, u.nombre FROM asistencias a 
        JOIN usuarios u ON a.id_usuario = u.id 
        WHERE a.id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$asistencia = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$asistencia) {
  die("<div class='alert alert-danger'>Asistencia no encontrada.</div>");
}

// Datos escapados para imprimir
$nombreSafe = htmlspecialchars($asistencia['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
$fechaSafe  = htmlspecialchars($asistencia['fecha'] ?? '', ENT_QUOTES, 'UTF-8');
$anotSafe   = htmlspecialchars($asistencia['anotaciones'] ?? '', ENT_QUOTES, 'UTF-8');
$checked    = ((int)$asistencia['asistencia'] === 1) ? 'checked' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Asistencia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2>✏️ Editar Asistencia de <?= $nombreSafe ?> - <?= $fechaSafe ?></h2>
  <form method="POST" action="ModificarAsistencia.php">
    <input type="hidden" name="id" value="<?= (int)$asistencia['id'] ?>">

    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="asistencia" id="asistencia" <?= $checked ?>>
      <label class="form-check-label" for="asistencia">Asistió</label>
    </div>

    <div class="mb-3">
      <label for="anotaciones" class="form-label">Anotaciones</label>
      <textarea name="anotaciones" id="anotaciones" class="form-control" rows="4"><?= $anotSafe ?></textarea>
    </div>

    <button type="submit" class="btn btn-success">Guardar Cambios</button>
    <a href="historial.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>

</body>
</html>
