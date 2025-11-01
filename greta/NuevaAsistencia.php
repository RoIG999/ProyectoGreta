<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usuario_id = $_POST['usuario_id'];
  $fecha = $_POST['fecha'];
  $asistencia = isset($_POST['asistencia']) ? 1 : 0;
  $anotaciones = $_POST['anotaciones'];

  $stmt = $conn->prepare("INSERT INTO asistencias (id_usuario, fecha, asistencia, anotaciones) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("isis", $usuario_id, $fecha, $asistencia, $anotaciones);
  $stmt->execute();

  header("Location: historial.php?agregado=1");
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nueva Asistencia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2>➕ Registrar Nueva Asistencia</h2>
  <form method="POST">
    <div class="mb-3">
      <label for="usuario_id" class="form-label">Empleado</label>
      <select class="form-select" name="usuario_id" required>
        <option value="">-- Seleccionar --</option>
        <?php
        $usuarios = $conn->query("SELECT id, nombre FROM usuarios WHERE estado = 1");
        while ($u = $usuarios->fetch_assoc()) {
          echo "<option value='{$u['id']}'>{$u['nombre']}</option>";
        }
        ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="fecha" class="form-label">Fecha</label>
      <input type="date" name="fecha" class="form-control" required>
    </div>

    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="asistencia" id="asistencia" checked>
      <label class="form-check-label" for="asistencia">Asistió</label>
    </div>

    <div class="mb-3">
      <label for="anotaciones" class="form-label">Anotaciones</label>
      <textarea name="anotaciones" class="form-control" rows="4"></textarea>
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="historial.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>

</body>
</html>
