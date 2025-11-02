<?php
// greta/api/turnos.php - VERSION COMPLETA FUNCIONAL CON DURACIONES POR SERVICIO, DNI Y AUTOBUSQUEDA

header('Content-Type: application/json; charset=utf-8');

if (!ini_get('date.timezone')) {
    date_default_timezone_set('America/Argentina/Cordoba');
}

// Configuración de la base de datos
$db_host = 'localhost';
$db_name = 'abmgreta';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

class HttpErr extends Exception {
    public function __construct(int $code, string $msg) {
        parent::__construct($msg, $code);
    }
}

try {
    switch ($action) {
        case 'por-dia': {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                throw new HttpErr(405, 'Método no permitido');
            }

            $fecha = $_GET['fecha'] ?? date('Y-m-d');
            $servicioId = $_GET['servicioId'] ?? 0;

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                throw new HttpErr(400, 'Fecha inválida (YYYY-MM-DD)');
            }

            // Obtener turnos ocupados de la base de datos
            $ocupados = obtenerTurnosOcupados($pdo, $fecha, $servicioId);
            
            // Generar horarios disponibles CON DURACIÓN POR SERVICIO
            $disponibles = generarDisponibilidad($fecha, $ocupados, $servicioId);
            
            // Combinar eventos
            $eventos = combinarEventos($disponibles, $ocupados, $servicioId);

            echo json_encode($eventos);
            break;
        }

        case 'agendar': {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new HttpErr(405, 'Método no permitido');
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $nombre = trim($data['clienteNombre'] ?? '');
    $apellido = trim($data['clienteApellido'] ?? '');
    $telefono = trim($data['telefono'] ?? '');
    $dni = trim($data['dni'] ?? '');
    $servicioId = trim((string)($data['servicioId'] ?? '1'));
    $fecha = trim($data['fecha'] ?? '');
    $hora = trim($data['hora'] ?? '');
    $duracion = $data['duracion'] ?? 60;

    // Validaciones existentes...
    if ($nombre === '' || $apellido === '' || $telefono === '' || $fecha === '' || $hora === '') {
        throw new HttpErr(400, 'Faltan campos obligatorios');
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        throw new HttpErr(400, 'Formato fecha inválido (YYYY-MM-DD)');
    }

    if (!preg_match('/^\d{2}:\d{2}$/', $hora)) {
        throw new HttpErr(400, 'Formato hora inválido (HH:MM)');
    }

    // Convertir hora a formato completo (HH:MM:SS)
    $hora = $hora . ':00';

    // Validar que no esté ocupado CONSIDERANDO LA DURACIÓN
    if (existeTurnoEnHorarioConDuracion($pdo, $fecha, $hora, $servicioId, $duracion)) {
        throw new HttpErr(409, 'Ese horario ya está ocupado para este servicio');
    }

    // NUEVO: Buscar si el cliente ya tiene turnos ese día
    $grupoTurnosId = buscarGrupoTurnos($pdo, $nombre, $apellido, $telefono, $fecha);
    
    // Si no existe grupo, crear uno nuevo
    if (!$grupoTurnosId) {
        $grupoTurnosId = crearGrupoTurnos($pdo, $nombre, $apellido, $telefono, $dni, $fecha);
    }

    // Guardar en la base de datos CON GRUPO
    $idNuevo = guardarTurno($pdo, $nombre, $apellido, $telefono, $dni, $servicioId, $fecha, $hora, $grupoTurnosId);

    // Actualizar total del grupo
    $totalGrupo = actualizarTotalGrupo($pdo, $grupoTurnosId);

    echo json_encode([
        'ok' => true,
        'id' => $idNuevo,
        'grupo_turnos_id' => $grupoTurnosId,
        'total_grupo' => $totalGrupo,
        'mensaje' => 'Turno agendado exitosamente'
    ]);
    break;
}

        case 'listar-servicios': {
            $servicios = obtenerServicios($pdo);
            echo json_encode($servicios);
            break;
        }

        case 'buscar-clientes': {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                throw new HttpErr(405, 'Método no permitido');
            }

            $termino = $_GET['termino'] ?? '';
            $dni = $_GET['dni'] ?? '';
            
            $clientes = buscarClientes($pdo, $termino, $dni);
            echo json_encode($clientes);
            break;
        }

        default:
            throw new HttpErr(400, 'Action inválido');
    }
} catch (HttpErr $e) {
    http_response_code($e->getCode());
    echo json_encode(['error' => $e->getMessage()]);
} catch (Throwable $e) {
    http_response_code(500);
    error_log("Error en turnos.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
}

// FUNCIONES DE BASE DE DATOS

/**
 * Obtiene la duración en minutos de un servicio
 */
function obtenerDuracionServicio($servicioId) {
    // Definir duraciones específicas por servicio
    $duraciones = [
        5 => 120, // Uñas Esculpidas - 120min
        3 => 120,  // Microblading - 120min
        // Los demás servicios: 60min por defecto
        1 => 60,   // Bronceado
        2 => 60,   // Faciales  
        4 => 60,   // Perfilado de Cejas
        6 => 60    // Pestañas
    ];
    
    return $duraciones[$servicioId] ?? 60; // 60min por defecto
}

function obtenerTurnosOcupados(PDO $pdo, string $fecha, string $servicioId): array {
    $sql = "SELECT t.ID, t.nombre_cliente, t.apellido_cliente, t.telefono_cliente,
                   t.dni_cliente,
                   t.fecha, t.hora, 
                   s.nombre as servicio_nombre,
                   et.nombre as estado_nombre
            FROM turno t 
            LEFT JOIN servicio s ON t.ID_servicio_FK = s.ID 
            LEFT JOIN estado_turno et ON t.ID_estado_turno_FK = et.ID 
            WHERE t.fecha = :fecha 
            AND t.ID_servicio_FK = :servicio_id 
            AND et.nombre != 'Cancelado'
            ORDER BY t.hora";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':fecha' => $fecha,
        ':servicio_id' => $servicioId
    ]);
    
    $turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($turnos as &$turno) {
        $turno['inicio'] = $turno['fecha'] . 'T' . substr($turno['hora'], 0, 5);
        if (!isset($turno['servicio_nombre']) || $turno['servicio_nombre'] === null) {
            $turno['servicio_nombre'] = 'Servicio ' . $servicioId;
        }
        if (!isset($turno['estado_nombre']) || $turno['estado_nombre'] === null) {
            $turno['estado_nombre'] = 'Confirmado';
        }
    }
    
    return $turnos;
}

/**
* Valida si existe un turno en el horario considerando la duración del servicio
 */
function existeTurnoEnHorarioConDuracion(PDO $pdo, string $fecha, string $hora, string $servicioId, int $duracion): bool {
    $horaInicio = $hora;
    $horaFin = date('H:i:s', strtotime($hora . ' +' . $duracion . ' minutes'));
    
    $sql = "SELECT COUNT(*) FROM turno t
            LEFT JOIN estado_turno et ON t.ID_estado_turno_FK = et.ID
            WHERE t.fecha = :fecha 
            AND t.ID_servicio_FK = :servicio_id
            AND et.nombre != 'Cancelado'
            AND (
                (t.hora >= :hora_inicio AND t.hora < :hora_fin) OR
                (DATE_ADD(CONCAT(t.fecha, ' ', t.hora), INTERVAL :duracion MINUTE) > CONCAT(:fecha, ' ', :hora_inicio) AND t.hora <= :hora_inicio)
            )";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':fecha' => $fecha,
        ':servicio_id' => $servicioId,
        ':hora_inicio' => $horaInicio,
        ':hora_fin' => $horaFin,
        ':duracion' => $duracion
    ]);
    
    return $stmt->fetchColumn() > 0;
}



function obtenerEstadoTurnoPorDefecto(PDO $pdo): int {
    try {
        // Primero intentar con 'Pendiente' (más lógico para nuevos turnos)
        $stmt = $pdo->query("SELECT ID FROM estado_turno WHERE nombre = 'Pendiente' LIMIT 1");
        $estado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($estado) {
            return $estado['ID'];
        }
        
        // Si no existe, intentar con 'Confirmado'
        $stmt = $pdo->query("SELECT ID FROM estado_turno WHERE nombre = 'Confirmado' LIMIT 1");
        $estado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($estado) {
            return $estado['ID'];
        }
        
        // Si no hay estados, crear los 4 básicos
        $pdo->exec("INSERT INTO estado_turno (nombre) VALUES 
                   ('Confirmado'), ('Pendiente'), ('Cancelado'), ('Disponible')");
        return 2; // Pendiente como ID 2 (porque Confirmado sería 1)
        
    } catch (Exception $e) {
        return 2; // Pendiente por defecto
    }
}

/**
 * Busca clientes por nombre, apellido, teléfono o DNI
 */
function buscarClientes(PDO $pdo, string $termino = '', string $dni = ''): array {
    if (empty($termino) && empty($dni)) {
        return [];
    }
    
    $sql = "SELECT DISTINCT 
                   nombre_cliente as nombre, 
                   apellido_cliente as apellido, 
                   telefono_cliente as telefono,
                   dni_cliente as dni
            FROM turno 
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($dni)) {
        $sql .= " AND dni_cliente LIKE :dni";
        $params[':dni'] = $dni . '%';
    } elseif (!empty($termino)) {
        $sql .= " AND (nombre_cliente LIKE :termino 
                      OR apellido_cliente LIKE :termino 
                      OR telefono_cliente LIKE :termino
                      OR dni_cliente LIKE :termino)";
        $params[':termino'] = '%' . $termino . '%';
    }
    
    $sql .= " ORDER BY apellido_cliente, nombre_cliente 
              LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Limpiar datos nulos
    foreach ($clientes as &$cliente) {
        $cliente['nombre'] = $cliente['nombre'] ?? '';
        $cliente['apellido'] = $cliente['apellido'] ?? '';
        $cliente['telefono'] = $cliente['telefono'] ?? '';
        $cliente['dni'] = $cliente['dni'] ?? '';
    }
    
    return $clientes;
}

/**
 * Genera disponibilidad considerando la duración del servicio
 */
/**
 * Genera disponibilidad considerando la duración del servicio
 */
function generarDisponibilidad(string $fecha, array $ocupados, string $servicioId): array {
    $dow = (int)date('w', strtotime($fecha));
    if ($dow === 0) return [];

    // ⚠️ CORRECCIÓN: Crear mapa de horas ocupadas CONSIDERANDO DURACIÓN
    $horasOcupadas = [];
    foreach ($ocupados as $turno) {
        $horaInicio = new DateTime($turno['fecha'] . ' ' . $turno['hora']);
        $duracionTurno = obtenerDuracionServicio($servicioId);
        $horaFin = clone $horaInicio;
        $horaFin->modify("+{$duracionTurno} minutes");
        
        // Marcar todas las horas dentro de este rango como ocupadas
        $horaActual = clone $horaInicio;
        while ($horaActual < $horaFin) {
            $horaStr = $horaActual->format('H:i');
            $horasOcupadas[$horaStr] = true;
            $horaActual->modify('+60 minutes'); // Avanzar de hora en hora
        }
    }

    $disponibles = [];
    $ahora = new DateTime();
    $hoy = $ahora->format('Y-m-d');
    
    // Obtener duración del servicio
    $duracion = obtenerDuracionServicio($servicioId);
    
    error_log("🔍 generarDisponibilidad - Fecha: $fecha, Servicio: $servicioId, Duración: {$duracion}min");
    
    // PARA SERVICIOS DE 120MIN - slots de 2 horas completas
    if ($duracion == 120) {
        // Slots de 120min que empiezan cada 2 horas
        $horasInicio = [9, 11, 13, 15, 17];
        
        foreach ($horasInicio as $hora) {
            $horaStr = str_pad($hora, 2, '0', STR_PAD_LEFT);
            $horarioInicio = "{$horaStr}:00";
            
            // Calcular hora de fin (2 horas después)
            $horaFin = $hora + 2;
            $horaFinStr = str_pad($horaFin, 2, '0', STR_PAD_LEFT);
            $horarioFin = "{$horaFinStr}:00";
            
            $fechaHoraInicio = new DateTime("{$fecha} {$horarioInicio}:00");
            $fechaHoraFin = new DateTime("{$fecha} {$horarioFin}:00");
            
            // ⚠️ CORRECCIÓN: Verificar que NO esté en horas ocupadas
            $estaOcupado = false;
            $horaCheck = clone $fechaHoraInicio;
            while ($horaCheck < $fechaHoraFin) {
                $horaCheckStr = $horaCheck->format('H:i');
                if (isset($horasOcupadas[$horaCheckStr])) {
                    $estaOcupado = true;
                    break;
                }
                $horaCheck->modify('+60 minutes');
            }
            
            if ($horaFin <= 20 && !$estaOcupado) {
                // Verificar si es pasado
                $esPasado = ($fecha < $hoy) || ($fecha === $hoy && $fechaHoraFin < $ahora);
                
                $disponibles[] = [
                    'start' => "{$fecha}T{$horarioInicio}:00",
                    'end' => "{$fecha}T{$horarioFin}:00",
                    'estado' => $esPasado ? 'pasado' : 'disponible',
                    'duracion' => $duracion
                ];
                
                error_log("⏰ Slot 120min: $horarioInicio - $horarioFin - ¿Es pasado?: " . ($esPasado ? 'SÍ' : 'NO'));
            }
        }
    } else {
        // Para servicios de 60min - slots de 1 hora
        for ($hora = 9; $hora < 20; $hora++) {
            $horaStr = str_pad($hora, 2, '0', STR_PAD_LEFT);
            $horarioInicio = "{$horaStr}:00";
            $horaFin = $hora + 1;
            $horaFinStr = str_pad($horaFin, 2, '0', STR_PAD_LEFT);
            $horarioFin = "{$horaFinStr}:00";
            
            $fechaHoraInicio = new DateTime("{$fecha} {$horarioInicio}:00");
            $fechaHoraFin = new DateTime("{$fecha} {$horarioFin}:00");
            
            // ⚠️ CORRECCIÓN: Verificar que NO esté en horas ocupadas
            if (!isset($horasOcupadas[$horarioInicio])) {
                // Verificar si es pasado
                $esPasado = ($fecha < $hoy) || ($fecha === $hoy && $fechaHoraFin < $ahora);
                
                $disponibles[] = [
                    'start' => "{$fecha}T{$horarioInicio}:00",
                    'end' => "{$fecha}T{$horarioFin}:00",
                    'estado' => $esPasado ? 'pasado' : 'disponible',
                    'duracion' => $duracion
                ];
                
                error_log("⏰ Slot 60min: $horarioInicio - $horarioFin - ¿Es pasado?: " . ($esPasado ? 'SÍ' : 'NO'));
            }
        }
    }
    
    return $disponibles;
}

function combinarEventos(array $disponibles, array $ocupados, string $servicioId): array {
    $eventos = [];
    $ahora = new DateTime();
    $hoy = $ahora->format('Y-m-d');

    // ⚠️ CORRECCIÓN: Primero agregar turnos OCUPADOS (tienen prioridad)
    foreach ($ocupados as $turno) {
        $fechaTurno = $turno['fecha'];
        $horaTurno = new DateTime($turno['fecha'] . ' ' . $turno['hora']);
        
        // Obtener duración del servicio para el turno ocupado
        $duracion = obtenerDuracionServicio($servicioId);
        $horaFinTurno = clone $horaTurno;
        $horaFinTurno->modify("+{$duracion} minutes");
        
        // Verificar si es pasado
        $esPasado = ($fechaTurno < $hoy) || ($fechaTurno === $hoy && $horaFinTurno < $ahora);
        
        $eventos[] = [
            'title' => ($esPasado ? '⏰ ' : '') . 'Ocupado - ' . $turno['nombre_cliente'],
            'start' => $turno['inicio'],
            'end' => $horaFinTurno->format('Y-m-d\TH:i:s'),
            'color' => $esPasado ? '#95a5a6' : '#f44336',
            'extendedProps' => [
                'estado' => $esPasado ? 'pasado' : 'ocupado',
                'servicio' => $turno['servicio_nombre'],
                'cliente' => $turno['nombre_cliente'] . ' ' . $turno['apellido_cliente'],
                'telefono' => $turno['telefono_cliente'],
                'dni' => $turno['dni_cliente'] ?? '',
                'turno_id' => $turno['ID'],
                'estado_turno' => $turno['estado_nombre'],
                'hora_original' => $turno['hora'],
                'duracion' => $duracion
            ]
        ];
    }

    // ⚠️ CORRECCIÓN: Luego agregar DISPONIBLES, verificando que no se solapen con ocupados
    foreach ($disponibles as $slot) {
        $slotInicio = new DateTime($slot['start']);
        $slotFin = new DateTime($slot['end']);
        
        // Verificar si este slot se solapa con algún turno ocupado
        $seSolapa = false;
        foreach ($ocupados as $turno) {
            $turnoInicio = new DateTime($turno['inicio']);
            $turnoDuracion = obtenerDuracionServicio($servicioId);
            $turnoFin = clone $turnoInicio;
            $turnoFin->modify("+{$turnoDuracion} minutes");
            
            if ($slotInicio < $turnoFin && $slotFin > $turnoInicio) {
                $seSolapa = true;
                break;
            }
        }
        
        if (!$seSolapa) {
            $esPasado = ($slot['estado'] === 'pasado');
            $duracion = $slot['duracion'] ?? 60;
            
            $eventos[] = [
                'title' => $esPasado ? 'Disponible' : 'Disponible',
                'start' => $slot['start'],
                'end' => $slot['end'],
                'color' => $esPasado ? '#95a5a6' : '#4caf50',
                'extendedProps' => [
                    'estado' => $slot['estado'],
                    'servicioId' => $servicioId,
                    'duracion' => $duracion
                ]
            ];
        }
    }

    return $eventos;
}

function obtenerServicios(PDO $pdo): array {
    try {
        $sql = "SELECT ID, nombre FROM rubro_servicio ORDER BY nombre";
        $stmt = $pdo->query($sql);
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $servicios;
        
    } catch (Exception $e) {
        error_log("Error en obtenerServicios: " . $e->getMessage());
        return [];
    }
}
// FUNCIONES PARA AGRUPAMIENTO DE TURNOS
function buscarGrupoTurnos($pdo, $nombre, $apellido, $telefono, $fecha) {
    $sql = "SELECT id FROM grupo_turnos 
            WHERE cliente_nombre = :nombre 
            AND cliente_apellido = :apellido 
            AND cliente_telefono = :telefono 
            AND fecha = :fecha 
            AND estado = 'pendiente' 
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':telefono' => $telefono,
        ':fecha' => $fecha
    ]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : null;
}

function crearGrupoTurnos($pdo, $nombre, $apellido, $telefono, $dni, $fecha) {
    $sql = "INSERT INTO grupo_turnos (cliente_nombre, cliente_apellido, cliente_telefono, cliente_dni, fecha, total) 
            VALUES (:nombre, :apellido, :telefono, :dni, :fecha, 0)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':telefono' => $telefono,
        ':dni' => $dni,
        ':fecha' => $fecha
    ]);
    
    return $pdo->lastInsertId();
}

function actualizarTotalGrupo($pdo, $grupoTurnosId) {
    // Obtener precios de los servicios y calcular total
    $sql = "SELECT SUM(s.precio) as total 
            FROM turno t 
            JOIN servicio s ON t.ID_servicio_FK = s.ID 
            WHERE t.grupo_turnos_id = :grupo_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':grupo_id' => $grupoTurnosId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $total = $result['total'] ?? 0;
    
    // Actualizar total en grupo_turnos
    $sql = "UPDATE grupo_turnos SET total = :total WHERE id = :grupo_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':total' => $total,
        ':grupo_id' => $grupoTurnosId
    ]);
    
    return $total;
}

// Modificar la función guardarTurno existente para aceptar grupo_turnos_id
function guardarTurno(PDO $pdo, string $nombre, string $apellido, string $telefono, 
                     string $dni, string $servicioId, string $fecha, string $hora, $grupoTurnosId = null): int {
    
    $estadoId = obtenerEstadoTurnoPorDefecto($pdo);
    
    $sql = "INSERT INTO turno (nombre_cliente, apellido_cliente, telefono_cliente, 
                               dni_cliente, 
                               ID_servicio_FK, ID_estado_turno_FK, fecha, hora, recordatorio_enviado, grupo_turnos_id) 
            VALUES (:nombre, :apellido, :telefono, :dni, :servicio_id, :estado_id, :fecha, :hora, 0, :grupo_id)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':telefono' => $telefono,
        ':dni' => $dni,
        ':servicio_id' => $servicioId,
        ':estado_id' => $estadoId,
        ':fecha' => $fecha,
        ':hora' => $hora,
        ':grupo_id' => $grupoTurnosId
    ]);
    
    return $pdo->lastInsertId();
}
?>