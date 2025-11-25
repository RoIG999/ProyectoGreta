<?php 
include("conexion.php"); 

// Determinar el tipo de usuario y la página de retorno
session_start();
$pagina_retorno = "Inicio.php";

// Verificar si hay usuario logueado
if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_rol'])) {
    $rol = $_SESSION['usuario_rol'] ?? '';
    $rol = mb_strtolower($rol, 'UTF-8');
    $rol = strtr($rol, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n']);
    
    // Mapeo de roles según tu base de datos
    switch($rol) {
        case 'empleado':
            $pagina_retorno = "Panel-empleada.php";
            break;
        case 'supervisor':  // Como está en tu BD
            $pagina_retorno = "Panel-supervisora.php";
            break;
        case 'admin':       // Como está en tu BD  
            $pagina_retorno = "Panel-dueña.php";
            break;
        case 'cliente':
        default:
            $pagina_retorno = "Inicio.php";
            break;
    }
}

// Si no hay usuario en sesión (cliente sin login), queda como "inicio.php"


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

    /* HEADER */
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

    /* ENCABEZADO UNIFICADO */
    .encabezado-agenda {
      background: linear-gradient(135deg, #ffffff, #fff5f8);
      border-radius: 25px;
      padding: 40px;
      margin-bottom: 25px;
      box-shadow: var(--shadow);
      border: none;
      background-clip: padding-box;
      position: relative;
      /* REMOVER overflow: hidden PARA PERMITIR QUE EL DROPDOWN SALGA */
    }

    

    .contenido-encabezado {
      display: flex;
      align-items: center; /* Cambiado a flex-start para mejor alineación */
      gap: 40px;
      position: relative; /* Para el contexto de posicionamiento */
    }

    .seccion-izquierda {
      flex: 1;
  display: flex;
  align-items: center;
  justify-content: center; /* AGREGAR para centrar contenido */
  gap: 30px;}

    .icono-titulo {
      background: linear-gradient(135deg, #e91e63, #c2185b);
      width: 90px;
      height: 90px;
      border-radius: 25px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 8px 25px rgba(233, 30, 99, 0.4);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      margin-bottom: 20px;
    }

    .icono-titulo::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
      transform: rotate(45deg);
      transition: all 0.6s ease;
    }

    .icono-titulo:hover::before {
      transform: rotate(45deg) translate(50%, 50%);
    }

    .icono-titulo:hover {
      transform: scale(1.05) rotate(5deg);
      box-shadow: 0 12px 30px rgba(233, 30, 99, 0.5);
    }

    .icono-titulo i {
      font-size: 2.5rem;
      color: white;
      position: relative;
      z-index: 2;
    }

    .texto-encabezado h2 {
      font-family: 'Playfair Display', serif;
      font-size: 2.8rem;
      color: var(--text-dark);
      margin-bottom: 12px;
      font-weight: 700;
      background: linear-gradient(135deg, #e91e63, #c2185b);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .texto-encabezado p {
      color: var(--text-medium);
      font-size: 1.3rem;
      font-weight: 500;
      margin-bottom: 0;
    }

    /* BOTÓN DESPLEGABLE CORREGIDO - AHORA SALE DEL CONTENEDOR */
    .dropdown-container {
      position: relative;
      flex: 0 0 400px;
      /* EL DROPDOWN PODRÁ SALIR DE ESTE CONTENEDOR */
    }

    .dropdown-button {
      width: 100%;
      background: linear-gradient(135deg, #fff5f8, #ffeef5);
      border: 3px solid #f8bbd9;
      border-radius: 20px;
      padding: 20px 25px;
      font-weight: 700;
      font-size: 1.2rem;
      color: var(--text-dark);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: all 0.3s ease;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      text-align: left;
    }

    .dropdown-button:hover {
      border-color: var(--primary-color);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(233, 30, 99, 0.2);
    }

    .dropdown-button.activo {
      border-bottom-left-radius: 0;
      border-bottom-right-radius: 0;
      border-bottom: none !important;
    }

    .dropdown-button i.fa-chevron-down {
      color: var(--primary-color);
      font-size: 1.1rem;
      transition: transform 0.3s ease;
      margin-left: 10px;
    }

    .dropdown-button.activo i.fa-chevron-down {
      transform: rotate(180deg);
    }

    .servicio-seleccionado {
      display: flex;
      align-items: center;
      gap: 12px;
      flex: 1;
    }

    .servicio-seleccionado i:first-child {
      color: var(--primary-color);
      font-size: 1.3rem;
      width: 24px;
      text-align: center;
    }

    .dropdown-menu {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border: none;
      border-top: none;
      border-radius: 0 0 20px 20px;
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s ease;
      z-index: 1100; /* MAYOR Z-INDEX PARA QUE ESTÉ POR ENCIMA DE TODO */
      box-shadow: 0 15px 40px rgba(0,0,0,0.15);
      /* EL DROPDOWN AHORA PUEDE SALIR DEL CONTENEDOR PADRE */
    }

    .dropdown-menu.mostrar {
      max-height: 400px; /* Más altura para mostrar más servicios */
    }

    .dropdown-opciones {
  max-height: 350px;
  overflow-y: auto;
  overflow-x: hidden; /* Quita scroll horizontal */
  padding: 5px 0;
  word-wrap: break-word; /* Rompe palabras largas */
  white-space: normal; /* Permite que el texto haga wrap */
}

    .opcion-servicio {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px 20px;
  border-bottom: 1px solid #f0f0f0;
  cursor: pointer;
  transition: all 0.3s ease;
  background: white;
  word-wrap: break-word; /* Asegura que el texto no cause overflow */
  white-space: normal; /* Permite múltiples líneas si es necesario */
}

    .opcion-servicio:last-child {
      border-bottom: none;
    }

    .opcion-servicio:hover {
      background: var(--accent-light);
      transform: translateX(5px);
    }

    .opcion-servicio.seleccionado {
      background: linear-gradient(135deg, var(--accent-light), #fce4ec);
      border-left: 4px solid var(--primary-color);
    }

    .icono-opcion {
      width: 24px;
      text-align: center;
      color: var(--primary-color);
      font-size: 1.2rem;
    }

    .texto-opcion {
      font-weight: 600;
      color: var(--text-dark);
      font-size: 1rem;
    }

    .selector-hidden {
      display: none;
    }

    .info-seleccion-rapida {
      display: flex;
      align-items: center;
      gap: 20px;
      padding: 25px 30px;
      background: linear-gradient(135deg, var(--accent-light), #fce4ec);
      border-radius: 20px;
      border-left: 6px solid var(--accent-color);
      margin-top: 25px;
      font-size: 1.1rem;
      backdrop-filter: blur(10px);
      border: 2px solid rgba(255, 255, 255, 0.8);
      display: none;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      animation: slideIn 0.5s ease-out;
    }
    
    .info-seleccion-rapida.mostrar {
      display: flex;
    }

    .info-seleccion-rapida strong {
      color: var(--text-dark);
      font-size: 1.3rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .info-seleccion-rapida strong::before {
      content: '✨';
      font-size: 1.5rem;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* CALENDARIO */
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

    /* FULLCALENDAR */
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

    /* BOTONES */
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

    /* FORMULARIOS */
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

    /* PANTALLAS */
    .pantalla {
      display: none;
    }
    
    .pantalla.activa {
      display: block;
    }

    /* SEGUNDA PANTALLA MÁS GRANDE */
    #pantalla2 .formulario {
      max-width: 800px;
      padding: 25px;
      margin: 20px auto;
    }

    #pantalla2 h2 {
      font-size: 2.4rem;
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

    /* MODAL */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2000;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }
    
    .modal-overlay.mostrar {
      opacity: 1;
      visibility: visible;
    }
    
    .modal {
      background: white;
      border-radius: 20px;
      padding: 25px;
      max-width: 500px;
      width: 90%;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      position: relative;
      transform: translateY(20px);
      transition: transform 0.3s ease;
    }
    
    .modal-overlay.mostrar .modal {
      transform: translateY(0);
    }
    
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 2px solid #f0f0f0;
    }
    
    .modal-titulo {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      color: var(--text-dark);
      margin: 0;
    }
    
    .modal-cerrar {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--text-medium);
      transition: color 0.3s ease;
    }
    
    .modal-cerrar:hover {
      color: var(--primary-color);
    }
    
    .modal-contenido {
      margin-bottom: 25px;
      font-size: 1.1rem;
      line-height: 1.6;
    }
    
    .modal-acciones {
      display: flex;
      gap: 15px;
      justify-content: flex-end;
    }
    
    .modal-boton {
      padding: 12px 25px;
      border-radius: 12px;
      border: none;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 1rem;
    }
    
    .modal-boton.secundario {
      background: #f0f0f0;
      color: #333;
    }
    
    .modal-boton.primario {
      background: var(--gradient-primary);
      color: white;
    }
    
    .modal-boton:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Modal de detalles de turno ocupado */
    .modal-detalles {
      max-width: 500px;
    }

    .detalles-turno {
      background: linear-gradient(135deg, #fff5f8, #ffeef5);
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 20px;
      border: 2px solid var(--border-color);
    }

    .detalle-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 12px 0;
      border-bottom: 1px solid rgba(233, 30, 99, 0.1);
    }

    .detalle-item:last-child {
      border-bottom: none;
    }

    .detalle-icono {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .detalle-icono i {
      color: white;
      font-size: 1rem;
    }

    .detalle-info {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .detalle-label {
      font-size: 0.85rem;
      color: var(--text-medium);
      font-weight: 600;
      margin-bottom: 4px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .detalle-valor {
      font-size: 1rem;
      color: var(--text-dark);
      font-weight: 600;
    }

    .info-adicional {
      background: linear-gradient(135deg, #e3f2fd, #bbdefb);
      border: 1px solid #90caf9;
      border-radius: 10px;
      padding: 15px;
      font-size: 0.9rem;
      color: #1565c0;
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }

    /* Estados del turno */
    .estado-turno {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      margin-left: 10px;
    }

    .estado-confirmado {
      background: linear-gradient(135deg, #c8e6c9, #a5d6a7);
      color: #2e7d32;
    }

    .estado-pendiente {
      background: linear-gradient(135deg, #fff9c4, #fff59d);
      color: #f57f17;
    }

    .estado-cancelado {
      background: linear-gradient(135deg, #ffcdd2, #ef9a9a);
      color: #c62828;
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
      
      .contenido-encabezado {
        flex-direction: column;
        gap: 30px;
      }
      
      .dropdown-container {
        flex: 0 0 auto;
        width: 100%;
      }
      
      .icono-titulo {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px auto;
      }
      
      .icono-titulo i {
        font-size: 2.2rem;
      }
      
      .texto-encabezado {
        text-align: center;
      }
      
      .texto-encabezado h2 {
        font-size: 2.2rem;
      }
      
      .texto-encabezado p {
        font-size: 1.1rem;
      }
      
      .dropdown-button {
        padding: 15px 20px;
        font-size: 1.1rem;
      }
      
      .opcion-servicio {
        padding: 12px 15px;
      }
      
      .encabezado-agenda {
        padding: 30px 20px;
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
      
      .modal {
        padding: 20px;
      }
      
      .modal-acciones {
        flex-direction: column;
      }

      /* En móvil, el dropdown se expande hacia abajo normalmente */
      .dropdown-menu {
        position: absolute;
      }

      .detalle-item {
        padding: 10px 0;
      }

      .detalle-icono {
        width: 35px;
        height: 35px;
      }

      .estado-turno {
        font-size: 0.7rem;
        padding: 4px 8px;
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
      <!-- ENCABEZADO UNIFICADO CON DROPDOWN -->
      <div class="encabezado-agenda">
        <div class="contenido-encabezado">
          <div class="seccion-izquierda">
            <div class="icono-titulo">
              <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="texto-encabezado">
              <h2>Agenda tu Turno</h2>
              <p>Encontrá el momento perfecto para tu belleza</p>
            </div>
          </div>
          
          <!-- BOTÓN DESPLEGABLE CORREGIDO -->
          <div class="dropdown-container">
            <button class="dropdown-button" id="dropdownButton">
              <div class="servicio-seleccionado">
                <i class="fas fa-spa"></i>
                <span id="dropdownText">¿Qué te gustaría hacer hoy?</span>
              </div>
              <i class="fas fa-chevron-down"></i>
            </button>
            
            <div class="dropdown-menu" id="dropdownMenu">
              <div class="dropdown-opciones" id="opcionesServicios">
                <!-- Las opciones se cargarán dinámicamente -->
                <div class="opcion-servicio" style="padding: 20px; text-align: center; color: var(--text-medium);">
                  <i class="fas fa-spinner fa-spin"></i> Cargando servicios...
                </div>
              </div>
            </div>
          </div>
          
          <!-- Selector hidden para mantener la funcionalidad -->
          <select id="servicio" class="selector-hidden">
            <option value="">Cargando servicios...</option>
          </select>
        </div>
        
        <div class="info-seleccion-rapida" id="seleccionInfo">
          <strong>Turno seleccionado</strong>
          <span id="infoServicio"></span>
          <span id="infoFechaHora"></span>
        </div>
      </div>

      <div id="calendar"></div>
    </div>

    <!-- El resto del código se mantiene igual -->
    <!-- Pantalla 2: Datos del cliente -->
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
          <button type="button" id="botonVolver" class="boton-reservar" style="background: linear-gradient(to right, #000000, #333333);">
  <i class="fas fa-arrow-left"></i> VOLVER AL CALENDARIO</button>
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

  <!-- Modal de confirmación -->
  <div class="modal-overlay" id="modalConfirmacion">
    <div class="modal">
      <div class="modal-header">
        <h3 class="modal-titulo">Confirmar turno</h3>
        <button class="modal-cerrar" id="modalCerrar">&times;</button>
      </div>
      <div class="modal-contenido" id="modalContenido">
        <!-- Contenido dinámico -->
      </div>
      <div class="modal-acciones">
        <button class="modal-boton secundario" id="modalCancelar">Cancelar</button>
        <button class="modal-boton primario" id="modalConfirmar">Continuar</button>
      </div>
    </div>
  </div>

  <!-- Modal de detalles de turno ocupado -->
  <div class="modal-overlay" id="modalDetallesTurno">
    <div class="modal modal-detalles">
      <div class="modal-header">
        <h3 class="modal-titulo">
          <i class="fas fa-calendar-times" style="color: var(--danger-color); margin-right: 10px;"></i>
          Turno Ocupado
        </h3>
        <button class="modal-cerrar" id="modalDetallesCerrar">&times;</button>
      </div>
      <div class="modal-contenido">
        <div class="detalles-turno">
          <div class="detalle-item">
            <div class="detalle-icono">
              <i class="fas fa-user"></i>
            </div>
            <div class="detalle-info">
              <span class="detalle-label">Cliente</span>
              <span class="detalle-valor" id="detalleCliente"></span>
            </div>
          </div>
          
          <div class="detalle-item">
            <div class="detalle-icono">
              <i class="fas fa-phone"></i>
            </div>
            <div class="detalle-info">
              <span class="detalle-label">Teléfono</span>
              <span class="detalle-valor" id="detalleTelefono"></span>
            </div>
          </div>
          
          <div class="detalle-item">
            <div class="detalle-icono">
              <i class="fas fa-spa"></i>
            </div>
            <div class="detalle-info">
              <span class="detalle-label">Servicio</span>
              <span class="detalle-valor" id="detalleServicio"></span>
            </div>
          </div>
          
          <div class="detalle-item">
            <div class="detalle-icono">
              <i class="fas fa-clock"></i>
            </div>
            <div class="detalle-info">
              <span class="detalle-label">Horario</span>
              <span class="detalle-valor" id="detalleHorario"></span>
            </div>
          </div>
          
          <div class="detalle-item">
            <div class="detalle-icono">
              <i class="fas fa-calendar-day"></i>
            </div>
            <div class="detalle-info">
              <span class="detalle-label">Fecha</span>
              <span class="detalle-valor" id="detalleFecha"></span>
            </div>
          </div>
          
          <div class="detalle-item" id="detalleDniContainer" style="display: none;">
            <div class="detalle-icono">
              <i class="fas fa-id-card"></i>
            </div>
            <div class="detalle-info">
              <span class="detalle-label">DNI</span>
              <span class="detalle-valor" id="detalleDni"></span>
            </div>
          </div>
        </div>
        
        <div class="info-adicional">
          <i class="fas fa-info-circle" style="color: var(--primary-color); margin-right: 8px;"></i>
          Este horario ya está reservado. Podés elegir otro turno disponible.
        </div>
      </div>
      <div class="modal-acciones">
        <button class="modal-boton primario" id="modalDetallesAceptar">
          <i class="fas fa-check"></i> Entendido
        </button>
      </div>
    </div>
  </div>

 <script>
    // JavaScript CORREGIDO con botón desplegable mejorado
    let calendar;
    let horarioSeleccionado = null;
    let eventoSeleccionado = null;
    let servicioActual = '';
    let servicioNombre = '';
    let timeoutBusqueda = null;

    // Mapeo de iconos para cada tipo de servicio
    const iconosServicios = {
      'manicura': 'fas fa-hand-sparkles',
      'uñas': 'fas fa-hand-sparkles',
      'esculpidas': 'fas fa-gem',
      'semipermanente': 'fas fa-paint-brush',
      'pedicura': 'fas fa-foot',
      'pies': 'fas fa-foot',
      'spa': 'fas fa-spa',
      'facial': 'fas fa-user-circle',
      'depilación': 'fas fa-feather-alt',
      'cejas': 'fas fa-eye',
      'pestañas': 'fas fa-eye',
      'default': 'fas fa-spa'
    };

    document.addEventListener("DOMContentLoaded", function () {
        const calendarEl = document.getElementById("calendar");
        const botonVolver = document.getElementById('botonVolver');
        const botonReservar = document.getElementById('botonReservar');
        const buscarClienteInput = document.getElementById('buscarCliente');
        const resultadosClientes = document.getElementById('resultadosClientes');
        const pantalla1 = document.getElementById('pantalla1');
        const pantalla2 = document.getElementById('pantalla2');
        const servicioSelect = document.getElementById('servicio');
        const dropdownButton = document.getElementById('dropdownButton');
        const dropdownMenu = document.getElementById('dropdownMenu');
        const dropdownText = document.getElementById('dropdownText');
        const modalConfirmacion = document.getElementById('modalConfirmacion');
        const modalCerrar = document.getElementById('modalCerrar');
        const modalCancelar = document.getElementById('modalCancelar');
        const modalConfirmar = document.getElementById('modalConfirmar');
        const modalContenido = document.getElementById('modalContenido');
        
        // Nuevos modales
        const modalDetallesTurno = document.getElementById('modalDetallesTurno');
        const modalDetallesCerrar = document.getElementById('modalDetallesCerrar');
        const modalDetallesAceptar = document.getElementById('modalDetallesAceptar');

        // Función para alternar el dropdown
        function toggleDropdown() {
            const estaActivo = dropdownMenu.classList.contains('mostrar');
            dropdownMenu.classList.toggle('mostrar');
            dropdownButton.classList.toggle('activo');
        }

        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('mostrar');
                dropdownButton.classList.remove('activo');
            }
        });

        // Event listener para el botón dropdown
        dropdownButton.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleDropdown();
        });

        // Inicializar calendario
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
                    mostrarModalConfirmacion();
                } else if (estado === "pasado") {
                    mostrarError("No se pueden agendar turnos en horarios que ya finalizaron");
                } else if (estado === "ocupado") {
                    mostrarDetallesTurno(event);
                }
            }
        });

        calendar.render();

        // Event Listeners
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

        // Eventos del modal de confirmación
        modalCerrar.addEventListener('click', function() {
            modalConfirmacion.classList.remove('mostrar');
        });

        modalCancelar.addEventListener('click', function() {
            modalConfirmacion.classList.remove('mostrar');
        });

        modalConfirmar.addEventListener('click', function() {
            modalConfirmacion.classList.remove('mostrar');
            mostrarPantalla2();
        });

        modalConfirmacion.addEventListener('click', function(e) {
            if (e.target === modalConfirmacion) {
                modalConfirmacion.classList.remove('mostrar');
            }
        });

        // Eventos del modal de detalles
        modalDetallesCerrar.addEventListener('click', function() {
            modalDetallesTurno.classList.remove('mostrar');
        });

        modalDetallesAceptar.addEventListener('click', function() {
            modalDetallesTurno.classList.remove('mostrar');
        });

        modalDetallesTurno.addEventListener('click', function(e) {
            if (e.target === modalDetallesTurno) {
                modalDetallesTurno.classList.remove('mostrar');
            }
        });

        // Actualizaciones automáticas
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

    // Función para obtener el icono correspondiente al servicio
    function obtenerIconoServicio(nombreServicio) {
        const nombre = nombreServicio.toLowerCase();
        
        for (const [key, icono] of Object.entries(iconosServicios)) {
            if (nombre.includes(key)) {
                return icono;
            }
        }
        
        return iconosServicios.default;
    }

    // Función para crear opciones de servicio con iconos en el dropdown
    function crearOpcionesServicios(servicios) {
        const opcionesContainer = document.getElementById('opcionesServicios');
        const servicioSelect = document.getElementById('servicio');
        
        opcionesContainer.innerHTML = '';
        servicioSelect.innerHTML = '<option value="">Elegí tu servicio...</option>';
        
        servicios.forEach(servicio => {
            const icono = obtenerIconoServicio(servicio.nombre);
            
            // Crear opción para el dropdown
            const opcionDiv = document.createElement('div');
            opcionDiv.className = 'opcion-servicio';
            opcionDiv.setAttribute('data-servicio-id', servicio.ID);
            opcionDiv.setAttribute('data-servicio-nombre', servicio.nombre);
            
            opcionDiv.innerHTML = `
                <div class="icono-opcion">
                    <i class="${icono}"></i>
                </div>
                <div class="texto-opcion">${servicio.nombre}</div>
            `;
            
            opcionDiv.addEventListener('click', function() {
                // Remover selección anterior
                document.querySelectorAll('.opcion-servicio').forEach(op => {
                    op.classList.remove('seleccionado');
                });
                
                // Agregar selección actual
                this.classList.add('seleccionado');
                
                // Actualizar valores
                servicioActual = servicio.ID;
                servicioNombre = servicio.nombre;
                
                // Actualizar el texto del botón - SOLO UN ICONO
                document.getElementById('dropdownText').textContent = servicio.nombre;
                // Actualizar el select hidden
                document.getElementById('servicio').value = servicioActual;
                
                // Cerrar el dropdown
                document.getElementById('dropdownMenu').classList.remove('mostrar');
                document.getElementById('dropdownButton').classList.remove('activo');
                
                // Cargar turnos
                cargarTurnosParaSemana(true);
                resetearSeleccion();
            });
            
            opcionesContainer.appendChild(opcionDiv);
            
            // También crear opción para el select hidden
            const option = document.createElement('option');
            option.value = servicio.ID;
            option.textContent = servicio.nombre;
            servicioSelect.appendChild(option);
        });
        
        // Seleccionar el primer servicio por defecto si hay servicios
        if (servicios.length > 0) {
            const primeraOpcion = opcionesContainer.querySelector('.opcion-servicio');
            if (primeraOpcion) {
                primeraOpcion.click();
            }
        }
    }

    // FUNCIÓN MEJORADA PARA CARGAR SERVICIOS
    function cargarServicios() {
        console.log('🔍 Iniciando carga de servicios...');
        
        fetch('api/turnos.php?action=listar-servicios')
            .then(response => {
                console.log('📡 Estado de respuesta:', response.status, response.statusText);
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                console.log('📄 Respuesta cruda:', text);
                
                if (!text.trim()) {
                    console.error('❌ Respuesta vacía del servidor');
                    mostrarErrorServicios('No se pudieron cargar los servicios');
                    return;
                }
                
                try {
                    const servicios = JSON.parse(text);
                    console.log('✅ Servicios parseados:', servicios);
                    
                    if (Array.isArray(servicios)) {
                        if (servicios.length === 0) {
                            console.warn('⚠️ No hay servicios disponibles');
                            mostrarErrorServicios('No hay servicios activos disponibles');
                        } else {
                            crearOpcionesServicios(servicios);
                        }
                    } else {
                        console.error('❌ La respuesta no es un array:', servicios);
                        mostrarErrorServicios('Error en el formato de servicios');
                    }
                } catch (e) {
                    console.error('❌ Error parseando JSON:', e);
                    console.error('Texto que causó el error:', text);
                    mostrarErrorServicios('Error cargando servicios');
                }
            })
            .catch(error => {
                console.error('❌ Error en fetch:', error);
                mostrarErrorServicios('Error de conexión: ' + error.message);
            });
    }

    function mostrarErrorServicios(mensaje) {
        const opcionesContainer = document.getElementById('opcionesServicios');
        opcionesContainer.innerHTML = `
            <div class="opcion-servicio" style="padding: 20px; text-align: center; color: var(--danger-color);">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="texto-opcion">${mensaje}</div>
                <div style="font-size: 0.8rem; margin-top: 10px;">Recarga la página o contacta al administrador</div>
            </div>
        `;
    }

    function mostrarModalConfirmacion() {
        if (!horarioSeleccionado) return;
        
        const fecha = horarioSeleccionado.toLocaleDateString('es-AR', { 
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
        });
        const hora = horarioSeleccionado.toLocaleTimeString('es-AR', {hour: '2-digit', minute: '2-digit'});
        
        const contenido = `
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="font-size: 3rem; color: var(--primary-color); margin-bottom: 10px;">✨</div>
                <p style="font-size: 1.2rem; margin-bottom: 10px; font-weight: 600;">¿Confirmás tu turno?</p>
            </div>
            <div style="background: linear-gradient(135deg, #fff5f8, #ffeef5); border-radius: 15px; padding: 20px; border: 2px solid var(--border-color);">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-spa" style="color: white; font-size: 1rem;"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.9rem; color: var(--text-medium); font-weight: 600;">SERVICIO</div>
                        <div style="font-size: 1.1rem; color: var(--text-dark); font-weight: 700;">${servicioNombre}</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-calendar-day" style="color: white; font-size: 1rem;"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.9rem; color: var(--text-medium); font-weight: 600;">FECHA</div>
                        <div style="font-size: 1.1rem; color: var(--text-dark); font-weight: 700;">${fecha}</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock" style="color: white; font-size: 1rem;"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.9rem; color: var(--text-medium); font-weight: 600;">HORA</div>
                        <div style="font-size: 1.1rem; color: var(--text-dark); font-weight: 700;">${hora}</div>
                    </div>
                </div>
            </div>
            <p style="text-align: center; margin-top: 20px; color: var(--text-medium); font-size: 0.95rem;">
                Serás redirigido para completar tus datos de contacto
            </p>
        `;
        
        document.getElementById('modalContenido').innerHTML = contenido;
        document.getElementById('modalConfirmacion').classList.add('mostrar');
    }

    function mostrarDetallesTurno(event) {
        const props = event.extendedProps;
        
        // Formatear datos
        const cliente = props.cliente || 'No disponible';
        const telefono = props.telefono || 'No disponible';
        const servicio = servicioNombre || props.servicio || 'No disponible';
        const hora = event.start.toLocaleTimeString('es-AR', {hour: '2-digit', minute: '2-digit'});
        const fecha = event.start.toLocaleDateString('es-AR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        const dni = props.dni || '';
        const estadoTurno = props.estado_turno || 'Confirmado';
        
        // Actualizar contenido del modal
        document.getElementById('detalleCliente').textContent = cliente;
        document.getElementById('detalleTelefono').textContent = telefono;
        document.getElementById('detalleServicio').textContent = servicio;
        document.getElementById('detalleHorario').textContent = hora;
        document.getElementById('detalleFecha').textContent = fecha;
        
        // Mostrar/ocultar DNI según disponibilidad
        const dniContainer = document.getElementById('detalleDniContainer');
        const dniValor = document.getElementById('detalleDni');
        if (dni && dni.trim() !== '') {
            dniValor.textContent = dni;
            dniContainer.style.display = 'flex';
        } else {
            dniContainer.style.display = 'none';
        }
        
        // Agregar estado del turno al título si está disponible
        const titulo = document.querySelector('#modalDetallesTurno .modal-titulo');
        let estadoHtml = '';
        
        if (estadoTurno.toLowerCase() === 'confirmado') {
            estadoHtml = '<span class="estado-turno estado-confirmado"><i class="fas fa-check-circle"></i> Confirmado</span>';
        } else if (estadoTurno.toLowerCase() === 'pendiente') {
            estadoHtml = '<span class="estado-turno estado-pendiente"><i class="fas fa-clock"></i> Pendiente</span>';
        } else if (estadoTurno.toLowerCase() === 'cancelado') {
            estadoHtml = '<span class="estado-turno estado-cancelado"><i class="fas fa-times-circle"></i> Cancelado</span>';
        }
        
        titulo.innerHTML = `
            <i class="fas fa-calendar-times" style="color: var(--danger-color); margin-right: 10px;"></i>
            Turno Ocupado
            ${estadoHtml}
        `;
        
        // Mostrar modal
        document.getElementById('modalDetallesTurno').classList.add('mostrar');
    }

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
                        
                        const eventId = `${fecha}-${evento.start}-${servicioActual}`;
                        
                        calendar.addEvent({
                            id: eventId,
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
        
        document.getElementById('seleccionInfo').classList.remove('mostrar');
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
      <footer><php echo "Redirigiendo a: " . $pagina_retorno . " para el usuario: " . $_SESSION['tipo_usuario'];?></footer>
</body>
</html>