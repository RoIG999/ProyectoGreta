<?php
// greta/GuardarUsuario.php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: gestionUsuarios.php?error=metodo"); exit;
}

$nombre  = trim($_POST['nombre'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$clave   = (string)($_POST['clave'] ?? '');
$rol     = trim($_POST['rol'] ?? '');
$estado  = isset($_POST['estado']) ? 1 : 0;

// Validaciones mínimas
if ($nombre === '' || $usuario === '' || $clave === '' || $rol === '') {
  header("Location: RegistroUsuario.php?error=faltan_campos"); exit;
}

// Roles permitidos - según la base de datos
$rolesPermitidos = ['admin','Supervisor','empleado'];
if (!in_array($rol, $rolesPermitidos, true)) {
  header("Location: RegistroUsuario.php?error=rol_invalido"); exit;
}

// ¿Usuario duplicado?
$sql = "SELECT id FROM usuarios WHERE usuario = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $usuario);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows > 0) {
  $stmt->close();
  header("Location: RegistroUsuario.php?error=usuario_duplicado"); exit;
}
$stmt->close();

// Hash seguro (bcrypt vía PASSWORD_DEFAULT)
$hash = password_hash($clave, PASSWORD_DEFAULT);

// Insert
$sql = "INSERT INTO usuarios (nombre, usuario, clave, rol, estado) VALUES (?,?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssi', $nombre, $usuario, $hash, $rol, $estado);

if ($stmt->execute()) {
  $stmt->close();
  header("Location: gestionUsuarios.php?ok=creado"); exit;
} else {
  $stmt->close();
  header("Location: RegistroUsuario.php?error=insert"); exit;
}