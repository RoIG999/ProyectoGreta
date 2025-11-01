<?php
// GuardarAsistencia.php
include("conexion.php");

// Asegurar zona horaria (opcional, pero recomendado)
if (!ini_get('date.timezone')) {
  date_default_timezone_set('America/Argentina/Cordoba');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: index.php?error=metodo"); exit;
}

$fecha = date("Y-m-d");

// Validación básica
if (!isset($_POST['asistencia']) || !is_array($_POST['asistencia'])) {
  header("Location: index.php?error=sin_datos"); exit;
}

// Preparar statements reutilizables
$checkUsuarioActivo = $conn->prepare("SELECT 1 FROM usuarios WHERE id = ? AND estado = 1");
$checkExistente     = $conn->prepare("SELECT id FROM asistencias WHERE id_usuario = ? AND fecha = ? LIMIT 1");
$doInsert           = $conn->prepare("INSERT INTO asistencias (id_usuario, fecha, asistencia, anotaciones) VALUES (?, ?, 1, ?)");
$doUpdate           = $conn->prepare("UPDATE asistencias SET asistencia = 1, anotaciones = ? WHERE id = ?");

$conn->begin_transaction();

try {
  foreach ($_POST['asistencia'] as $idUsuario => $valor) {
    $idUsuario = (int)$idUsuario;
    if ($idUsuario <= 0) { continue; }

    // Anotación asociada a ese usuario (puede venir o no)
    $anotacion = isset($_POST['anotaciones'][$idUsuario]) ? trim((string)$_POST['anotaciones'][$idUsuario]) : '';
    // Limite defensivo de longitud (opcional)
    if (strlen($anotacion) > 1000) { $anotacion = substr($anotacion, 0, 1000); }

    // Verificar que el usuario esté ACTIVO
    $checkUsuarioActivo->bind_param('i', $idUsuario);
    $checkUsuarioActivo->execute();
    $resActivo = $checkUsuarioActivo->get_result();
    if (!$resActivo || $resActivo->num_rows === 0) {
      // Usuario inactivo o inexistente → saltar
      continue;
    }

    // ¿Ya existe asistencia para hoy?
    $checkExistente->bind_param('is', $idUsuario, $fecha);
    $checkExistente->execute();
    $res = $checkExistente->get_result();
    $fila = $res ? $res->fetch_assoc() : null;

    if ($fila) {
      // UPDATE existente
      $idAsistencia = (int)$fila['id'];
      $doUpdate->bind_param('si', $anotacion, $idAsistencia);
      $doUpdate->execute();
    } else {
      // INSERT nueva
      $doInsert->bind_param('iss', $idUsuario, $fecha, $anotacion);
      $doInsert->execute();
    }
  }

  $conn->commit();
  header("Location: index.php?asistenciaGuardada=1"); exit;

} catch (Throwable $e) {
  $conn->rollback();
  // En dev: echo "Error: ".$e->getMessage();
  header("Location: index.php?error=guardar_asistencia"); exit;
} finally {
  if ($checkUsuarioActivo) $checkUsuarioActivo->close();
  if ($checkExistente)     $checkExistente->close();
  if ($doInsert)           $doInsert->close();
  if ($doUpdate)           $doUpdate->close();
  $conn->close();
}

