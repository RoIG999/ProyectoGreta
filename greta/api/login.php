<?php
// greta/api/login.php
header('Content-Type: application/json; charset=utf-8');
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Headers: Content-Type');
// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

if (!ini_get('date.timezone')) {
  date_default_timezone_set('America/Argentina/Cordoba');
}
session_start();

require_once __DIR__ . '/../conexion.php'; // debe exponer $conn (mysqli)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Método no permitido']); exit;
}

// Lee JSON o form-data
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) { $data = $_POST; }

$usuario = isset($data['usuario']) ? trim($data['usuario']) : '';
$clave   = isset($data['clave'])   ? (string)$data['clave']   : '';

if ($usuario === '' || $clave === '') {
  http_response_code(400);
  echo json_encode(['error' => 'Faltan credenciales']); exit;
}

// Busca usuario
$sql  = "SELECT id, nombre, usuario, clave, rol, estado FROM usuarios WHERE usuario = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) { http_response_code(500); echo json_encode(['error' => 'Error preparando consulta']); exit; }
$stmt->bind_param('s', $usuario);
$stmt->execute();
$res  = $stmt->get_result();
$user = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$user) { http_response_code(401); echo json_encode(['error' => 'Usuario o contraseña inválidos']); exit; }

// Estado (borrado lógico)
if ((int)($user['estado'] ?? 0) !== 1) {
  http_response_code(403);
  echo json_encode(['error' => 'Usuario inactivo']); exit;
}

// Verificación de contraseña (soporta legado y migra a bcrypt)
$hashActual = (string)($user['clave'] ?? '');
$esBcrypt = (substr($hashActual, 0, 4) === '$2y$');
$AUTO_MIGRAR_HASH = true;

$ok = false;
if ($esBcrypt) {
  $ok = password_verify($clave, $hashActual);
  if ($ok && password_needs_rehash($hashActual, PASSWORD_DEFAULT)) {
    $nuevo = password_hash($clave, PASSWORD_DEFAULT);
    if ($upd = $conn->prepare("UPDATE usuarios SET clave=? WHERE id=?")) {
      $upd->bind_param('si', $nuevo, $user['id']); $upd->execute(); $upd->close();
    }
  }
} else {
  // Texto plano heredado
  if (hash_equals($hashActual, $clave)) {
    $ok = true;
    if ($AUTO_MIGRAR_HASH) {
      $nuevo = password_hash($clave, PASSWORD_DEFAULT);
      if ($upd = $conn->prepare("UPDATE usuarios SET clave=? WHERE id=?")) {
        $upd->bind_param('si', $nuevo, $user['id']); $upd->execute(); $upd->close();
      }
    }
  }
}

if (!$ok) { http_response_code(401); echo json_encode(['error' => 'Usuario o contraseña inválidos']); exit; }

// Normalizar rol: minúsculas y sin tildes
$rolRaw = (string)($user['rol'] ?? '');
$rol = mb_strtolower($rolRaw, 'UTF-8');
$rol = strtr($rol, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n']); // "dueña" -> "duena"

// Guardar sesión
$_SESSION['usuario_id']    = (int)$user['id'];
$_SESSION['usuario_nombre']= (string)$user['nombre'];
$_SESSION['usuario_rol']   = $rol;

// Respuesta
echo json_encode([
  'id'     => (int)$user['id'],
  'nombre' => (string)$user['nombre'],
  'rol'    => $rol, // ya normalizado: "duena", "empleada/empleado", "supervisora"
]);
