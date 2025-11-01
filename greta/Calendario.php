<?php 
include("conexion.php"); 

// Determinar el tipo de usuario y la página de retorno
session_start();
$pagina_retorno = "inicio.php";

if (isset($_SESSION['tipo_usuario'])) {
    switch($_SESSION['tipo_usuario']) {
        case 'empleada':
            $pagina_retorno = "panel-empleada.php";
            break;
        case 'supervisora':
            $pagina_retorno = "panel-supervisora.php";
            break;
        case 'dueña':
            $pagina_retorno = "panel-dueña.php";
            break;
        case 'cliente':
        default:
            $pagina_retorno = "inicio.php";
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Agenda GRETA 💅</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary-color: #e91e63;
      --primary-dark: #c2185b;
      --primary-light: #f8bbd9;
      --secondary-color: #f0c0d0;
      --light-bg: #fff5f8;
      --white: #ffffff;
      --text-dark: #880e4f;
      --text-medium: #ad1457;
      --text-light: #c2185b;
      --border-color: #f8bbd9;
      --success-color: #000000ff;
      --success-dark: hsla(0, 1%, 30%, 1.00) 100%;
      --danger-color: #f44336;
      --danger-dark: #d32f2f;
      --warning-color: #ffc107;
      --warning-dark: #ff8f00;;
      --accent-color: #e91e63;
      --accent-light: #fce4ec;
      --shadow: 0 4px 15px rgba(233, 30, 99, 0.15);
      --shadow-hover: 0 6px 25px rgba(233, 30, 99, 0.25);
      --gradient-primary: linear-gradient(135deg, #000000ff 0%, hsla(0, 1%, 30%, 1.00) 100%);
      --gradient-bg: linear-gradient(135deg, #ffffffff, #f5f5f5ff);
      --gradient-accent: linear-gradient(135deg, #ff80ab 0%, #e91e63 100%);
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      font-family: 'Montserrat', sans-serif;
      background: var(--gradient-bg);
      color: var(--text-dark);
      line-height: 1.5;
      min-height: 100vh;
    }

    /* HEADER CON NUEVO DISEÑO */
    header {
      background: var(--gradient-primary);
      color: white;
      padding: 8px 25px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 20px rgba(233, 30, 99, 0.3);
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 3px solid #ff80ab;
    }
    
    .logo-container {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    
    .logo-container img {
      height: 55px;
      transition: all 0.3s ease;
      border-radius: 8px;
      padding: 0;
      background: transparent;
      object-fit: contain;
      filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
    }

    .logo-container img:hover {
      transform: scale(1.05) rotate(-2deg);
    }
    
    .logo-text {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      font-weight: 600;
      background: linear-gradient(135deg, #ffffff, #ffebee);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    nav {
      display: flex;
      gap: 12px;
      align-items: center;
    }
    
    nav a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      border-radius: 25px;
      transition: all 0.3s ease;
      font-size: 0.9rem;
      border: 2px solid transparent;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
    }
    
    nav a:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
      border-color: #ffebee;
      box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
    }

    .btn-volver {
      background: linear-gradient(135deg, #ffffff, #ffebee);
      color: var(--primary-color) !important;
      font-weight: 700;
    }

    .btn-volver:hover {
      background: linear-gradient(135deg, #ffebee, #ffffff) !important;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
    }

    /* CONTENEDOR PRINCIPAL */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 15px 20px 20px 20px;
    }

    h2 {
      text-align: center;
      margin: 15px 0 10px 0;
      font-family: 'Playfair Display', serif;
      font-size: 2.2rem;
      font-weight: 700;
      background: linear-gradient(135deg, #000000ff, #000000ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    h2:after {
      content: '';
      display: block;
      width: 100px;
      height: 4px;
      background: linear-gradient(90deg, #ffffff, #ff3b7dff);
      margin: 8px auto 0;
      border-radius: 2px;
      box-shadow: 0 2px 8px rgba(255, 255, 255, 0.3);
    }

    /* FORMULARIOS CON NUEVO DISEÑO */
    .formulario {
      max-width: 700px;
      margin: 0 auto 15px auto;
      padding: 20px;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: var(--shadow);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      backdrop-filter: blur(10px);
    }

    .formulario:before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(90deg, #ff80ab, #e91e63, #ff80ab);
      background-size: 200% 100%;
      animation: shimmer 3s ease-in-out infinite;
    }

    @keyframes shimmer {
      0%, 100% { background-position: -200% 0; }
      50% { background-position: 200% 0; }
    }

    .form-row {
      display: flex;
      gap: 15px;
      margin-bottom: 15px;
    }
    
    .form-group {
      flex: 1;
      position: relative;
    }

    .formulario label {
      font-weight: 600;
      margin-bottom: 8px;
      display: block;
      color: var(--text-dark);
      font-size: 0.95rem;
    }

    .formulario input, 
    .formulario select {
      padding: 12px 15px;
      width: 100%;
      border-radius: 12px;
      border: 2px solid var(--border-color);
      background: rgba(255, 255, 255, 0.8);
      font-size: 1rem;
      transition: all 0.3s ease;
      font-family: 'Montserrat', sans-serif;
      color: var(--text-dark);
    }

    .formulario input:focus,
    .formulario select:focus {
      outline: none;
      border-color: var(--primary-color);
      background: rgba(255, 255, 255, 0.95);
      box-shadow: 0 0 0 4px rgba(233, 30, 99, 0.1);
      transform: translateY(-2px);
    }

    /* CALENDARIO CON NUEVO DISEÑO */
    #calendar {
      max-width: 100%;
      margin: 15px auto;
      background: rgba(255, 255, 255, 0.95);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 20px;
      box-shadow: var(--shadow);
      padding: 15px;
      overflow: hidden;
      min-height: 220px;
      max-height: 600px;
      backdrop-filter: blur(10px);
    }

    /* FULLCALENDAR CON NUEVO TEMA */
    .fc {
      font-family: 'Montserrat', sans-serif;
      font-size: 0.8rem !important;
    }
    
    .fc .fc-toolbar {
      padding: 8px 0 !important;
      margin-bottom: 10px !important;
      border-bottom: 2px solid rgba(233, 30, 99, 0.1);
    }
    
    .fc .fc-toolbar-title {
      font-size: 1.4rem !important;
      font-weight: 700;
      font-family: 'Playfair Display', serif;
      background: linear-gradient(135deg, #e91e63, #c2185b);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .fc .fc-button {
      background: var(--gradient-primary);
      border: none;
      border-radius: 10px;
      padding: 8px 15px;
      font-weight: 600;
      color: white;
      transition: all 0.3s ease;
      font-size: 0.85rem;
      box-shadow: 0 2px 8px rgba(233, 30, 99, 0.3);
    }
    
    .fc .fc-button:hover {
      background: linear-gradient(135deg, #c2185b, #e91e63);
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(233, 30, 99, 0.4);
    }
    
    .fc .fc-col-header-cell {
      background: linear-gradient(135deg, #fce4ec, #f8bbd9);
      color: var(--text-dark);
      padding: 10px 0;
      font-weight: 700;
      border: 1px solid var(--border-color);
      font-size: 0.9rem;
    }
    
    .fc .fc-timegrid-slot {
      height: 40px !important;
      border-bottom: 1px solid #fce4ec;
    }
    
    .fc .fc-timegrid-slot-label {
      padding: 0 10px;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-medium);
    }
    
    .fc .fc-timegrid-axis-cushion {
      font-weight: 600;
      color: var(--text-dark);
      font-size: 0.85rem;
    }

    .fc-event {
      border: none !important;
      border-radius: 10px;
      padding: 5px 8px;
      font-size: 0.75rem;
      font-weight: 600;
      cursor: pointer;
      margin: 2px 3px !important;
      height: 38px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      transition: all 0.3s ease;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .fc-event:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .fc-event.disponible {
      background: linear-gradient(135deg, var(--success-color), var(--success-dark)) !important;
      color: white !important;
      border-left: 5px solid var(--success-dark) !important;
    }
    
    .fc-event.ocupado {
      background: linear-gradient(135deg, var(--danger-color), var(--danger-dark)) !important;
      color: white !important;
      border-left: 5px solid var(--danger-dark) !important;
      cursor: not-allowed;
    }
    
    .fc-event.seleccionado {
      background: linear-gradient(135deg, var(--warning-color), var(--warning-dark)) !important;
      border-left: 5px solid var(--warning-dark) !important;
      box-shadow: 0 4px 15px rgba(255, 128, 171, 0.4);
    }

    /* Eventos de 120min */
    .fc-event[data-duracion="120"] {
      height: 82px !important;
      min-height: 82px !important;
      padding: 8px 6px !important;
    }

    /* BOTONES MEJORADOS Y LLAMATIVOS */
    .boton-reservar {
      margin-top: 15px;
      padding: 15px 25px;
      width: 100%;
      background: var(--gradient-primary);
      color: white;
      border: none;
      border-radius: 15px;
      font-size: 1.1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 6px 20px rgba(233, 30, 99, 0.4);
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .boton-reservar:before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      transition: left 0.5s;
    }

    .boton-reservar:hover:before {
      left: 100%;
    }

    .boton-reservar:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(233, 30, 99, 0.6);
      background: linear-gradient(135deg, #c2185b, #e91e63);
    }

    .boton-reservar:active {
      transform: translateY(-1px);
    }

    .boton-reservar:disabled {
      background: #ccc;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .boton-reservar:disabled:hover:before {
      left: -100%;
    }

    /* INFORMACIÓN DE SELECCIÓN */
    .seleccion-info {
      max-width: 1000px;
      margin: 12px auto;
      padding: 15px 20px;
      background: linear-gradient(135deg, var(--accent-light), #fce4ec);
      border-radius: 15px;
      border-left: 5px solid var(--accent-color);
      display: none;
      box-shadow: var(--shadow);
      align-items: center;
      gap: 12px;
      font-size: 1rem;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.5);
    }
    
    .seleccion-info.mostrar {
      display: flex;
    }

    .seleccion-info strong {
      color: var(--text-dark);
      font-size: 1.1rem;
    }

    /* AUTOCOMPLETADO */
    .autocomplete-container {
      position: relative;
    }
    
    .autocomplete-results {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: rgba(255, 255, 255, 0.95);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      max-height: 200px;
      overflow-y: auto;
      z-index: 1000;
      box-shadow: var(--shadow);
      display: none;
      backdrop-filter: blur(10px);
    }
    
    .autocomplete-result {
      padding: 12px 15px;
      cursor: pointer;
      border-bottom: 1px solid #fce4ec;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }

    .autocomplete-result:hover {
      background: var(--accent-light);
    }

    /* MENSAJES */
    .success-message, .error-message {
      padding: 12px 15px;
      border-radius: 10px;
      margin: 12px 0;
      display: none;
      font-size: 0.95rem;
      font-weight: 600;
    }

    .success-message {
      background: linear-gradient(135deg, #d4edda, #c3e6cb);
      color: #155724;
      border-left: 5px solid var(--success-color);
    }

    .error-message {
      background: linear-gradient(135deg, #f8d7da, #f1b0b7);
      color: #721c24;
      border-left: 5px solid var(--danger-color);
    }

    /* PANTALLAS - SEGUNDA PANTALLA MÁS GRANDE */
    .pantalla {
      display: none;
    }
    
    .pantalla.activa {
      display: block;
    }

    /* SEGUNDA PANTALLA MÁS GRANDE */
    #pantalla2 .formulario {
      max-width: 800px; /* Más ancha que la primera pantalla */
      padding: 25px;
      margin: 20px auto;
    }

    #pantalla2 h2 {
      font-size: 2.4rem; /* Título más grande */
      margin: 20px 0 15px 0;
    }

    #pantalla2 .seleccion-info {
      max-width: 800px;
      padding: 18px 25px;
      font-size: 1.1rem;
    }

    #pantalla2 .boton-reservar {
      padding: 16px 30px;
      font-size: 1.2rem;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
      header {
        padding: 6px 15px;
        flex-direction: column;
        gap: 10px;
      }
      
      .logo-container img {
        height: 50px;
      }
      
      .logo-text {
        font-size: 1.5rem;
      }
      
      nav {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      h2 {
        font-size: 1.8rem;
        margin: 12px 0 8px 0;
      }
      
      .container {
        padding: 10px 15px 15px 15px;
      }
      
      #calendar {
        margin: 10px auto;
        max-height: 450px;
        padding: 10px;
      }
      
      .fc .fc-toolbar {
        flex-direction: column;
        gap: 6px;
      }
      
      .form-row {
        flex-direction: column;
        gap: 0;
      }

      #pantalla2 .formulario {
        max-width: 95%;
        padding: 20px;
      }

      #pantalla2 h2 {
        font-size: 2rem;
      }
    }

    /* ANIMACIÓN DE ENTRADA */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .pantalla.activa {
      animation: fadeInUp 0.6s ease-out;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo-container">
      <img src="img/LogoGreta.jpeg" alt="GRETA Logo" />
      <div class="logo-text">GRETA</div>
    </div>
    <nav>
      <a href="<?php echo $pagina_retorno; ?>" class="btn-volver">
        <i class="fas fa-arrow-left"></i> Volver
      </a>
    </nav>
  </header>

  <div class="container">
    <!-- Pantalla 1: Selección de servicio y calendario -->
    <div id="pantalla1" class="pantalla activa">
      <h2>Agenda tu Turno</h2>
      
      <div class="formulario">
        <div class="form-row">
          <div class="form-group">
            <label for="servicio">Servicio:</label>
            <select id="servicio">
              <option value="">Cargando servicios...</option>
            </select>
          </div>
        </div>
      </div>

      

      <div id="calendar"></div>
      <div class="seleccion-info" id="seleccionInfo">
<strong><u>📅 Turno seleccionado:</u></strong>

        
        <span id="infoServicio"></span>
        <span id="infoFechaHora"></span>
      </div>
      <div class="formulario">
        <button type="button" id="botonContinuar" class="boton-reservar" disabled>
          <i class="fas fa-arrow-right"></i> CONTINUAR
        </button>
      </div>
    </div>

    <!-- Pantalla 2: Datos del cliente - MÁS GRANDE -->
    <div id="pantalla2" class="pantalla">
      <h2>Completa tus Datos</h2>
      
      <div class="seleccion-info mostrar" id="resumenSeleccion">
<strong><u>📅 Turno seleccionado:</u></strong>
        
        <span id="resumenServicio"></span>
        <span id="resumenFechaHora"></span>
      </div>

      <div class="formulario">
        <!-- Búsqueda de cliente existente -->
        <div class="form-row">
          <div class="form-group autocomplete-container">
            <label for="buscarCliente">
              <i class="fas fa-search"></i> Buscar cliente existente:
            </label>
            <input type="text" id="buscarCliente" placeholder="Escribí nombre, apellido, teléfono o DNI..." />
            <div class="autocomplete-results" id="resultadosClientes"></div>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="nombre">Tu nombre:</label>
            <input type="text" id="nombre" placeholder="Ej: Maria" required />
          </div>
          <div class="form-group">
            <label for="apellido">Tu apellido:</label>
            <input type="text" id="apellido" placeholder="Ej: Romero" required />
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" placeholder="Ej: 351234567" required />
          </div>
          <div class="form-group">
            <label for="dni">DNI (opcional):</label>
            <input type="text" id="dni" placeholder="Ej: 12345678" />
          </div>
        </div>

        <div class="form-row">
          <button type="button" id="botonVolver" class="boton-reservar" style="background: linear-gradient(135deg, #9e9e9e, #757575);">
            <i class="fas fa-arrow-left"></i> VOLVER AL CALENDARIO
          </button>
          <button type="button" id="botonReservar" class="boton-reservar" disabled>
            <i class="fas fa-calendar-plus"></i> CONFIRMAR RESERVA
          </button>
        </div>
        
        <div class="success-message" id="successMessage"></div>
        <div class="error-message" id="errorMessage"></div>
      </div>
    </div>
  </div>

  <script>
    // ... (Todo el JavaScript permanece igual que en la versión anterior)
    // Solo se cambió el texto del botón "CONTINUAR CON DATOS DEL CLIENTE" por "CONTINUAR"
    
    let calendar;
    let horarioSeleccionado = null;
    let eventoSeleccionado = null;
    let servicioActual = '';
    let servicioNombre = '';
    let timeoutBusqueda = null;

    document.addEventListener("DOMContentLoaded", function () {
        const calendarEl = document.getElementById("calendar");
        const botonContinuar = document.getElementById('botonContinuar');
        const botonVolver = document.getElementById('botonVolver');
        const botonReservar = document.getElementById('botonReservar');
        const buscarClienteInput = document.getElementById('buscarCliente');
        const resultadosClientes = document.getElementById('resultadosClientes');
        const pantalla1 = document.getElementById('pantalla1');
        const pantalla2 = document.getElementById('pantalla2');
        const servicioSelect = document.getElementById('servicio');

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: "timeGridWeek",
            initialDate: new Date(),
            locale: "es",
            slotMinTime: "09:00:00",
            slotMaxTime: "19:00:00",
            allDaySlot: false,
            nowIndicator: true,
            slotDuration: '01:00:00',
            slotLabelInterval: '01:00:00',
            slotEventOverlap: false,
            eventOverlap: false,
            forceEventDuration: false,
            height: 'auto',
            contentHeight: 'auto',
            
            slotLabelFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
                meridiem: false
            },
            
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "timeGridWeek,timeGridDay"
            },
            
            buttonText: {
                today: "Hoy",
                week: "Semana",
                day: "Día"
            },
            
            eventDidMount: function(info) {
                const duracion = info.event.extendedProps.duracion;
                if (duracion === 120) {
                    const eventEl = info.el;
                    eventEl.style.height = '82px';
                    eventEl.setAttribute('data-duracion', '120');
                }
            },
            
            eventContent: function(arg) {
                const title = arg.event.title.split(' - ')[0];
                const duracion = arg.event.extendedProps.duracion || 60;
                return {
                    html: `<div class="fc-event-time">${arg.timeText}</div>
                           <div class="fc-event-title">${title}</div>
                           <div class="fc-event-duracion">${duracion}min</div>`
                };
            },
            
            eventClassNames: function(arg) {
                const estado = arg.event.extendedProps.estado;
                const classes = [];
                if (estado === "ocupado") classes.push("ocupado");
                if (estado === "disponible") classes.push("disponible");
                if (estado === "pasado") classes.push("pasado");
                if (arg.event === eventoSeleccionado) classes.push("seleccionado");
                return classes;
            },
            
            datesSet: function(info) {
                if (servicioActual) {
                    cargarTurnosParaSemana(true);
                }
                resetearSeleccion();
            },
            
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                const estado = info.event.extendedProps.estado;
                const event = info.event;
                const ahora = new Date();
                const inicioEvento = info.event.start;
                const finEvento = info.event.end;
                
                if (estado === "disponible") {
                    seleccionarHorario(event);
                } else if (estado === "pasado") {
                    mostrarError("No se pueden agendar turnos en horarios que ya finalizaron");
                } else if (estado === "ocupado") {
                    mostrarDetallesTurno(event);
                }
            }
        });

        calendar.render();

        botonContinuar.addEventListener('click', function() {
            if (servicioActual && horarioSeleccionado) {
                mostrarPantalla2();
            }
        });

        botonVolver.addEventListener('click', function() {
            mostrarPantalla1();
        });

        servicioSelect.addEventListener('change', function() {
            servicioActual = this.value;
            servicioNombre = this.options[this.selectedIndex].text;
            cargarTurnosParaSemana(true);
            resetearSeleccion();
        });

        buscarClienteInput.addEventListener('input', function(e) {
            const termino = e.target.value.trim();
            
            if (timeoutBusqueda) {
                clearTimeout(timeoutBusqueda);
            }
            
            timeoutBusqueda = setTimeout(() => {
                if (termino.length >= 2) {
                    buscarClientes(termino);
                } else {
                    resultadosClientes.style.display = 'none';
                }
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (!buscarClienteInput.contains(e.target) && !resultadosClientes.contains(e.target)) {
                resultadosClientes.style.display = 'none';
            }
        });

        setInterval(function() {
            if (servicioActual) {
                cargarTurnosParaSemana(false);
            }
        }, 60000);

        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && servicioActual) {
                cargarTurnosParaSemana(false);
            }
        });
        
        botonReservar.addEventListener('click', function() {
            if (horarioSeleccionado) {
                reservarTurno();
            }
        });

        cargarServicios();
    });

    function mostrarPantalla2() {
        document.getElementById('pantalla1').classList.remove('activa');
        document.getElementById('pantalla2').classList.add('activa');
        
        const resumenFechaHora = document.getElementById('resumenFechaHora');
        const resumenServicio = document.getElementById('resumenServicio');
        
        if (horarioSeleccionado) {
            const fecha = horarioSeleccionado.toLocaleDateString('es-AR', { 
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
            });
            const hora = horarioSeleccionado.toLocaleTimeString('es-AR', {hour: '2-digit', minute: '2-digit'});
            resumenFechaHora.innerHTML = `<strong>Fecha:</strong> ${fecha} a las ${hora}`;
resumenServicio.innerHTML = ` <strong>Servicio:</strong> ${servicioNombre}`;
        }
        
        validarFormularioCliente();
    }

    function mostrarPantalla1() {
        document.getElementById('pantalla2').classList.remove('activa');
        document.getElementById('pantalla1').classList.add('activa');
    }

    function buscarClientes(termino) {
        fetch(`api/turnos.php?action=buscar-clientes&termino=${encodeURIComponent(termino)}`)
            .then(response => response.json())
            .then(clientes => {
                const resultadosClientes = document.getElementById('resultadosClientes');
                resultadosClientes.innerHTML = '';
                
                if (clientes.length > 0) {
                    clientes.forEach(cliente => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-result';
                        div.innerHTML = `
                            <strong>${cliente.nombre} ${cliente.apellido}</strong>
                            <span class="cliente-info">📞 ${cliente.telefono} | 🆔 ${cliente.dni || 'Sin DNI'}</span>
                        `;
                        div.addEventListener('click', function() {
                            cargarDatosCliente(cliente);
                            resultadosClientes.style.display = 'none';
                        });
                        resultadosClientes.appendChild(div);
                    });
                    resultadosClientes.style.display = 'block';
                } else {
                    const div = document.createElement('div');
                    div.className = 'autocomplete-result';
                    div.textContent = 'No se encontraron clientes';
                    resultadosClientes.appendChild(div);
                    resultadosClientes.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error buscando clientes:', error);
            });
    }

    function cargarDatosCliente(cliente) {
        document.getElementById('nombre').value = cliente.nombre || '';
        document.getElementById('apellido').value = cliente.apellido || '';
        document.getElementById('telefono').value = cliente.telefono || '';
        document.getElementById('dni').value = cliente.dni || '';
        document.getElementById('buscarCliente').value = '';
        
        validarFormularioCliente();
        mostrarExito(`✅ Datos de ${cliente.nombre} ${cliente.apellido} cargados automáticamente`);
    }

    function validarFormularioCliente() {
        const nombre = document.getElementById('nombre').value.trim();
        const apellido = document.getElementById('apellido').value.trim();
        const telefono = document.getElementById('telefono').value.trim();
        
        const botonReservar = document.getElementById('botonReservar');
        botonReservar.disabled = !(nombre && apellido && telefono);
    }

    document.getElementById('nombre').addEventListener('input', validarFormularioCliente);
    document.getElementById('apellido').addEventListener('input', validarFormularioCliente);
    document.getElementById('telefono').addEventListener('input', validarFormularioCliente);

    function determinarEstadoEvento(evento, fecha) {
    const ahora = new Date();
    const inicioEvento = new Date(evento.start);
    const finEvento = new Date(evento.end);
    
    if (finEvento < ahora) {
        return "pasado";
    }
    
    if (evento.title.includes('Ocupado') || evento.title.includes('ocupado')) {
        return "ocupado";
    }
    
    if ((inicioEvento <= ahora && finEvento >= ahora) || inicioEvento > ahora) {
        return "disponible";
    }
    
    return "disponible";
}

    function cargarServicios() {
        fetch('api/turnos.php?action=listar-servicios')
            .then(response => response.text())
            .then(text => {
                try {
                    const servicios = JSON.parse(text);
                    const select = document.getElementById('servicio');
                    select.innerHTML = '<option value="">Seleccionar servicio</option>';
                    
                    servicios.forEach(servicio => {
                        const option = document.createElement('option');
                        option.value = servicio.ID;
                        option.textContent = servicio.nombre;
                        select.appendChild(option);
                    });
                    
                    if (servicios.length > 0) {
                        servicioActual = servicios[0].ID;
                        servicioNombre = servicios[0].nombre;
                        select.value = servicioActual;
                        cargarTurnosParaSemana(true);
                    }
                    
                } catch (e) {
                    console.error('Error cargando servicios:', e);
                    mostrarError('Error cargando servicios');
                }
            })
            .catch(error => {
                console.error('Error cargando servicios:', error);
                mostrarError('Error de conexión');
            });
    }

    function cargarTurnosParaSemana(mostrarLoading = true) {
    if (!servicioActual) return;
    
    const start = calendar.view.currentStart;
    const end = calendar.view.currentEnd;
    
    calendar.getEvents().forEach(evento => evento.remove());
    
    let loadingDiv;
    if (mostrarLoading) {
        loadingDiv = document.createElement('div');
        loadingDiv.className = 'loading';
        loadingDiv.innerHTML = '✨ Cargando disponibilidad...';
        document.getElementById('calendar').appendChild(loadingDiv);
    }
    
    const fechaActual = new Date(start);
    const promesas = [];
    
    while (fechaActual < end) {
        const fecha = fechaActual.toISOString().split('T')[0];
        const diaSemana = fechaActual.getDay();
        
        if (diaSemana >= 1 && diaSemana <= 6) {
            promesas.push(cargarTurnosParaDia(fecha));
        }
        fechaActual.setDate(fechaActual.getDate() + 1);
    }
    
    Promise.all(promesas)
        .finally(() => {
            if (loadingDiv) {
                loadingDiv.remove();
            }
        });
}

    function cargarTurnosParaDia(fecha) {
    return fetch(`api/turnos.php?action=por-dia&fecha=${fecha}&servicioId=${servicioActual}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(eventos => {
            eventos.forEach(evento => {
                try {
                    const estado = determinarEstadoEvento(evento, fecha);
                    
                    // ⬇️⬇️⬇️ AGREGÁ UN ID ÚNICO ⬇️⬇️⬇️
                    const eventId = `${fecha}-${evento.start}-${servicioActual}`;
                    
                    calendar.addEvent({
                        id: eventId, // ← ESTO EVITA DUPLICADOS
                        title: evento.title,
                        start: evento.start,
                        end: evento.end,
                        color: evento.color,
                        extendedProps: {
                            duracion: evento.duracion || evento.extendedProps?.duracion || 60,
                            estado: estado,
                            cliente: evento.extendedProps?.cliente || '',
                            telefono: evento.extendedProps?.telefono || ''
                        }
                    });
                    
                } catch (e) {
                    console.error('Error agregando evento:', e);
                }
            });
        })
        .catch(error => {
            console.error('Error cargando turnos para día:', fecha, error);
        });
}

    function seleccionarHorario(event) {
        const ahora = new Date();
        const inicioEvento = event.start;
        const finEvento = event.end;
        
        if (finEvento < ahora) {
            mostrarError("Este turno ya finalizó. Por favor seleccioná otro horario.");
            return;
        }
        
        if (eventoSeleccionado) {
            eventoSeleccionado.setProp('classNames', 
                eventoSeleccionado.extendedProps.estado === 'disponible' ? ['disponible'] : ['ocupado']);
        }
        
        eventoSeleccionado = event;
        horarioSeleccionado = event.start;
        
        const clases = ['seleccionado'];
        if (event.extendedProps.estado === 'disponible') {
            clases.push('disponible');
        }
        event.setProp('classNames', clases);
        
        actualizarInfoSeleccion();
        
        const botonContinuar = document.getElementById('botonContinuar');
        botonContinuar.disabled = false;
    }

    function actualizarInfoSeleccion() {
        const infoPanel = document.getElementById('seleccionInfo');
        const infoFechaHora = document.getElementById('infoFechaHora');
        const infoServicio = document.getElementById('infoServicio');
        
        if (horarioSeleccionado) {
            const fecha = horarioSeleccionado.toLocaleDateString('es-AR', { 
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
            });
            const hora = horarioSeleccionado.toLocaleTimeString('es-AR', {hour: '2-digit', minute: '2-digit'});
            
infoFechaHora.innerHTML = `<strong>Fecha:</strong> ${fecha} a las ${hora}`;
infoServicio.innerHTML = ` <strong>Servicio:</strong> ${servicioNombre} `;

            infoPanel.classList.add('mostrar');
        } else {
            infoPanel.classList.remove('mostrar');
        }
    }

    function resetearSeleccion() {
        if (eventoSeleccionado) {
            eventoSeleccionado.setProp('classNames', 
                eventoSeleccionado.extendedProps.estado === 'disponible' ? ['disponible'] : ['ocupado']);
        }
        
        horarioSeleccionado = null;
        eventoSeleccionado = null;
        
        const botonContinuar = document.getElementById('botonContinuar');
        botonContinuar.disabled = true;
        
        document.getElementById('seleccionInfo').classList.remove('mostrar');
    }

    function mostrarDetallesTurno(event) {
        const props = event.extendedProps;
        
        const mensaje = `📋 Turno Ocupado
    --------------------
    👤 Cliente: ${props.cliente || 'No disponible'}
    📞 Teléfono: ${props.telefono || 'No disponible'}
    💅 Servicio: ${servicioNombre || props.servicio || 'No disponible'}
    ⏰ Hora: ${event.start.toLocaleTimeString('es-AR', {hour: '2-digit', minute: '2-digit'})}
    📅 Fecha: ${event.start.toLocaleDateString('es-AR')}`.trim();
            
        alert(mensaje);
    }

    function reservarTurno() {
        const nombre = document.getElementById("nombre").value.trim();
        const apellido = document.getElementById("apellido").value.trim();
        const telefono = document.getElementById("telefono").value.trim();
        const dni = document.getElementById("dni").value.trim();

        if (!nombre || !apellido || !telefono) {
            mostrarError("Por favor completá tu nombre, apellido y teléfono.");
            return;
        }

        if (!horarioSeleccionado || !eventoSeleccionado) {
            mostrarError("Por favor seleccioná un horario disponible.");
            return;
        }

        const ahora = new Date();
        const finEvento = new Date(eventoSeleccionado.end);
        
        if (finEvento < ahora) {
            mostrarError("Este turno ya finalizó. Por favor seleccioná otro horario.");
            resetearSeleccion();
            cargarTurnosParaSemana(false);
            return;
        }

        const duracion = eventoSeleccionado.extendedProps.duracion || 60;
        
        const turnoData = {
            clienteNombre: nombre,
            clienteApellido: apellido,
            telefono: telefono,
            dni: dni,
            servicioId: servicioActual,
            fecha: horarioSeleccionado.toISOString().split('T')[0],
            hora: horarioSeleccionado.toTimeString().split(' ')[0].substring(0, 5),
            duracion: duracion
        };

        const botonReservar = document.getElementById('botonReservar');
        const botonOriginal = botonReservar.innerHTML;
        botonReservar.disabled = true;
        botonReservar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> RESERVANDO...';

        fetch("api/turnos.php?action=agendar", {
            method: "POST",
            headers: { 
                "Content-Type": "application/json"
            },
            body: JSON.stringify(turnoData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                mostrarExito("✅ " + data.mensaje);
                crearConfeti(30);
                resetearSeleccion();
                cargarTurnosParaSemana();
                limpiarFormulario();
                mostrarPantalla1();
            } else {
                throw new Error(data.error || 'Error en la reserva');
            }
        })
        .catch(error => {
            mostrarError("❌ " + error.message);
        })
        .finally(() => {
            botonReservar.disabled = false;
            botonReservar.innerHTML = botonOriginal;
        });
    }

    function mostrarExito(mensaje) {
        const successDiv = document.getElementById('successMessage');
        successDiv.textContent = mensaje;
        successDiv.style.display = 'block';
        
        setTimeout(() => {
            successDiv.style.display = 'none';
        }, 4000);
    }

    function mostrarError(mensaje) {
        const errorDiv = document.getElementById('errorMessage');
        errorDiv.textContent = mensaje;
        errorDiv.style.display = 'block';
        
        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 4000);
    }

    function limpiarFormulario() {
        document.getElementById("nombre").value = '';
        document.getElementById("apellido").value = '';
        document.getElementById("telefono").value = '';
        document.getElementById("dni").value = '';
        document.getElementById("buscarCliente").value = '';
    }

    function crearConfeti(cantidad) {
        const colors = ['#e91e63', '#c2185b', '#ff80ab', '#fce4ec', '#ffffff'];
        
        for (let i = 0; i < cantidad; i++) {
            const confetti = document.createElement('div');
            confetti.style.position = 'fixed';
            confetti.style.width = Math.random() * 12 + 6 + 'px';
            confetti.style.height = Math.random() * 12 + 6 + 'px';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.top = '-10px';
            confetti.style.pointerEvents = 'none';
            confetti.style.zIndex = '1000';
            confetti.style.borderRadius = '50%';
            confetti.style.animation = `confettiFall ${Math.random() * 2 + 1}s linear forwards`;
            
            document.body.appendChild(confetti);
            
            setTimeout(() => {
                confetti.remove();
            }, 3000);
        }
    }

    const confettiStyle = document.createElement('style');
    confettiStyle.textContent = `
        @keyframes confettiFall {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(confettiStyle);
  </script>
</body>
</html>