<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>GRETA - Estética Exclusiva</title>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    .logo-container {
      display: flex;
      align-items: center;
    }
    
    .logo-container img {
      height: 50px;
    
      transition: transform 0.3s ease;
    }

    .logo-container img:hover {
      transform: rotate(-5deg) scale(1.1);
    }
    /* ESTILOS GENERALES */
    body {
      font-family: 'Montserrat', sans-serif;
      margin: 0;
      padding: 0;
    }

    header {
      background: #000;
      color: white;
      padding: 10px 40px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    
    header img {
      height: 55px;
    }
    
    nav {
      display: flex;
      gap: 20px;
    }
    
    nav a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 0;
      position: relative;
      transition: all 0.3s ease;
    }
    
    nav a:hover {
      color: #f0c0d0;
    }
    
    nav a.activo {
      border-bottom: 2px solid #f0c0d0;
    }
  
    /* BOTONES FLOTANTES EN LADO IZQUIERDO */
    .social-floats {
      position: fixed;
      bottom: 25px;
      left: 25px;
      z-index: 1000;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .instagram-float, .whatsapp-float {
      opacity: 0;
      transform: translateY(50px);
      animation: fadeInUp 1s ease-out forwards;
    }

    .instagram-float {
      animation-delay: 1.5s;
    }

    .whatsapp-float {
      animation-delay: 2s;
    }

    .instagram-float a, .whatsapp-float a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
      transition: all 0.3s ease;
      text-decoration: none;
    }

    .instagram-float a {
      background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
      animation: pulse-instagram 2s infinite;
    }

    .whatsapp-float a {
      background: #25D366;
      animation: pulse-whatsapp 2s infinite;
    }

    .instagram-float a:hover, .whatsapp-float a:hover {
      transform: scale(1.1);
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.4);
    }

    .instagram-float a:hover {
      box-shadow: 0 6px 25px rgba(220, 39, 67, 0.5);
    }

    .whatsapp-float a:hover {
      background: #20BD5C;
      box-shadow: 0 6px 25px rgba(37, 211, 102, 0.6);
    }

    .instagram-float i, .whatsapp-float i {
      font-size: 32px;
      color: white !important;
    }

    /* Animaciones */
    @keyframes pulse-whatsapp {
      0% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
      70% { box-shadow: 0 0 0 15px rgba(37, 211, 102, 0); }
      100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
    }

    @keyframes pulse-instagram {
      0% { box-shadow: 0 0 0 0 rgba(217, 72, 253, 0.7); }
      70% { box-shadow: 0 0 0 15px rgba(37, 211, 102, 0); }
      100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
    }

    @keyframes fadeInUp {
      to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive */
    @media (max-width: 768px) {
      .social-floats {
        bottom: 20px;
        left: 20px;
      }
      
      .instagram-float a, .whatsapp-float a {
        width: 55px;
        height: 55px;
      }
      
      .instagram-float i, .whatsapp-float i {
        font-size: 28px;
      }
      
      header {
        padding: 10px 20px;
        flex-direction: column;
        gap: 15px;
      }
      
      nav {
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
      }
    }

    /* ESTILOS EXISTENTES */
    html { scroll-behavior: smooth; }
    body { animation: fadeInPage 1.2s ease-in-out; }
    @keyframes fadeInPage { from {opacity:0;} to {opacity:1;} }

    /* HERO */
    .hero {
      height: 100vh;
      background: url('https://images.unsplash.com/photo-1596462502278-27bfdc403348?q=80&w=1780&auto=format&fit=crop') center/cover no-repeat;
      background-attachment: fixed;
      position: relative;
      display: flex; align-items: center; justify-content: center;
      text-align: center; overflow: hidden;
      animation: zoomHero 20s ease-in-out infinite alternate;
    }
    
    @keyframes zoomHero { from {background-size:100%;} to {background-size:105%;} }
    
    .hero::after { 
      content:""; 
      position:absolute; 
      inset:0; 
      background:rgba(0,0,0,.55); 
    }
    
    .hero-content { 
      position:relative; 
      z-index:2; 
      color:white; 
      animation: fadeUp 1.5s ease-out forwards; 
    }
    
    .hero-content h1 { 
      font-family: 'Playfair Display', serif; 
      font-size: 3.5em; 
      margin-bottom:10px; 
    }
    
    .hero-content p { 
      font-family: 'Montserrat', sans-serif; 
      font-size: 1.3em; 
      margin-bottom:25px; 
    }
    
    .hero-content .boton {
      background:white; 
      color:black; 
      font-weight:bold; 
      padding:12px 30px;
      border-radius:30px; 
      text-decoration:none; 
      animation:pulseGlow 2.5s infinite; 
      transition:.3s;
    }
    
    .hero-content .boton:hover { 
      background:#f0f0f0; 
      transform: scale(1.05); 
    }
    
    @keyframes pulseGlow { 
      0%{box-shadow:0 0 0 rgba(255,255,255,.6);} 
      50%{box-shadow:0 0 20px rgba(255,255,255,.9);} 
      100%{box-shadow:0 0 0 rgba(255,255,255,.6);} 
    }

    /* PANEL LATERAL RESERVA */
    body.panel-abierto::before {
      content:""; 
      position:fixed; 
      inset:0; 
      background:rgba(0,0,0,.6); 
      z-index:998;
    }
    
    .panel-reserva {
      position: fixed; 
      top:0; 
      right:-100%; 
      width:40%; 
      max-width:500px; 
      height:100%;
      background:white; 
      box-shadow:-4px 0 20px rgba(0,0,0,.2);
      z-index:999; 
      transition:right .4s ease-in-out;
      display:flex; 
      flex-direction:column; 
      padding:20px;
      overflow-y: auto;
    }
    
    .panel-reserva.abierto { right:0; }
    
    .panel-header { 
      display:flex; 
      justify-content:space-between; 
      align-items:center; 
      border-bottom:1px solid #ddd; 
      padding-bottom:10px; 
    }
    
    .panel-header h2 { 
      font-family: 'Playfair Display', serif; 
      font-size: 1.8em; 
      margin:0; 
    }
    
    .cerrar-panel { 
      font-size:2em; 
      cursor:pointer; 
      color:#888; 
      transition:.3s; 
    }
    
    .cerrar-panel:hover { color:black; }

    #calendar {
      flex: 1;
      min-height: 600px;
    }

    .panel-form { 
      flex:1; 
      display:flex; 
      flex-direction:column; 
      margin-top:20px; 
    }
    
    .panel-form label { 
      font-weight:500; 
      margin-top:10px; 
    }
    
    .panel-form input, .panel-form select, .panel-form button {
      width:100%; 
      padding:10px; 
      border-radius:8px; 
      border:1px solid #ddd; 
      margin-top:5px; 
      font-family:'Montserrat', sans-serif;
    }
    
    .panel-form button { 
      margin-top:20px; 
      background:black; 
      color:white; 
      font-weight:bold; 
      border:none; 
      transition:background .3s; 
    }
    
    .panel-form button:hover { background:#444; }

    @media (max-width: 768px) { 
      .panel-reserva { width:100%; } 
    }

    /* SECCIONES DE CONTENIDO */
    .section-light { 
      background:white; 
      padding:60px 20px; 
      text-align:center; 
    }
    
    .section-light h2 { 
      font-family:'Playfair Display', serif; 
      font-size:2.2em; 
      margin-bottom:20px; 
    }
    
    .section-light p { 
      max-width:800px; 
      margin:auto; 
      font-family:'Montserrat', sans-serif; 
      font-size:1.1em; 
    }

    .section-dark { 
      background:black; 
      color:white; 
      padding:60px 20px; 
      text-align:center; 
    }
    
    .section-dark h2 { 
      font-family:'Playfair Display', serif; 
      font-size:2em; 
    }
    
    .servicios { 
      display:grid; 
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
      gap:25px; 
      margin-top:30px; 
    }
    
    .servicio { perspective:1000px; }

    .card { 
      position:relative; 
      width:100%; 
      height:340px; 
      transform-style:preserve-3d; 
      transition:transform .8s; 
    }
    
    .servicio:hover .card { transform: rotateY(180deg); }
    
    .card-front, .card-back {
      position:absolute; 
      inset:0; 
      backface-visibility:hidden; 
      border-radius:10px; 
      overflow:hidden;
    }
    
    .card-front { 
      background:white; 
      color:black; 
      display:flex; 
      flex-direction:column; 
    }
    
    .card-front .thumb { 
      height:200px; 
      background:#f4f4f4; 
      overflow:hidden; 
    }
    
    .card-front .thumb img { 
      width:100%; 
      height:100%; 
      object-fit:cover; 
      display:block; 
    }
    
    .card-front h3 { 
      padding:15px; 
      text-align:center; 
      font-family:'Montserrat', sans-serif; 
      margin:0; 
    }

    .card-back {
      background:#f5f5f5; 
      color:black; 
      transform: rotateY(180deg);
      display:flex; 
      align-items:center; 
      justify-content:center; 
      padding:20px; 
      font-family:'Montserrat', sans-serif; 
      text-align:center;
    }
    
    .card-back .descripcion { max-width:90%; }

    .animate-card { 
      opacity:0; 
      transform: translateY(50px); 
      transition: all .8s ease; 
    }
    
    .animate-card.visible { 
      opacity:1; 
      transform: translateY(0) scale(1.02); 
      box-shadow: 0 15px 25px rgba(0,0,0,.2); 
    }

    .frase { 
      font-size:1.5em; 
      font-style:italic; 
      text-align:center; 
      margin:50px auto; 
      max-width:700px; 
      font-family:'Playfair Display', serif; 
    }

    footer {
      background: #000;
      color: white;
      text-align: center;
      padding: 20px;
    }

    @keyframes fadeUp { 
      from {opacity:0; transform:translateY(40px);} 
      to {opacity:1; transform:translateY(0);} 
    }
  </style>
</head>
<body>
  <header>
    <div class="logo-container">
      <a class="brand" href="Inicio.php" aria-label="Inicio GRETA">
       <img src="img/LogoGreta.jpeg" alt="GRETA Estética">
      </a>
    </div>

    <nav id="site-nav">
      <a href="Inicio.php" class="activo"><i class="fas fa-home"></i> Inicio</a>
      <a href="Servicios.php"><i class="fas fa-spa"></i> Servicios</a>
      <a href="Nosotros.php"><i class="fas fa-users"></i> Nosotros</a>
      <a href="Contacto.php"><i class="fas fa-envelope"></i> Contacto</a>
      <a href="#" class="abrir-modal"><i class="fas fa-calendar-alt"></i> Reservar Turno</a>
      <a href="Login.php"><i class="fas fa-user"></i> Ingreso</a>
    </nav>
  </header>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-content">
      <h1>Estética Exclusiva</h1>
      <p>Donde la belleza y la elegancia se encuentran</p>
      <a href="Calendario.php" class="boton" >📅 Reservar Turno</a>
    </div>
  </section>

  <!-- PRESENTACIÓN -->
  <section class="section-light">
    <h2>Bienvenida a GRETA</h2>
    <p>En GRETA transformamos cada visita en una experiencia de lujo. Combinamos técnicas modernas, atención personalizada y productos de alta gama para que disfrutes de un cuidado único y exclusivo.</p>
  </section>

  <!-- SERVICIOS -->
  <section class="section-dark">
    <h2>Servicios Exclusivos</h2>
    <div class="servicios">
      <div class="servicio animate-card">
        <div class="card">
          <div class="card-front">
            <div class="thumb">
              <img src="img/uñas 4.avif" alt="esculpidas" loading="lazy">
            </div>
            <h3>Uñas esculpidas</h3>
          </div>
          <div class="card-back">
            <div class="descripcion">
              <p>Peeling ultrasónico, radiofrecuencia facial y máscara LED para una piel renovada y luminosa.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="servicio animate-card">
        <div class="card">
          <div class="card-front">
            <div class="thumb">
              <img src="img/bronceado 6.jpg" alt="Bronceado" loading="lazy">
            </div>
            <h3>Bronceado</h3>
          </div>
          <div class="card-back">
            <div class="descripcion">
              <p>Bronceado parejo y natural con aerógrafo para un look saludable todo el año.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="servicio animate-card">
        <div class="card">
          <div class="card-front">
            <div class="thumb">
              <img src="img/laminado 3.webp" alt="Laminado de Cejas" loading="lazy">
            </div>
            <h3>Laminado de Cejas</h3>
          </div>
          <div class="card-back">
            <div class="descripcion">
              <p>Moldea y define tus cejas para un acabado impecable que resalta tu mirada.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="frase">"La belleza es la expresión más elegante del alma."</div>

  <footer>
    <p>© 2025 GRETA Estética - Todos los derechos reservados</p>
  </footer>

  <!-- PANEL LATERAL RESERVA -->
  <div id="panelReserva" class="panel-reserva" aria-hidden="true">
    <div class="panel-header">
      <h2>📅 Reservar Turno</h2>
      <span id="cerrarPanel" class="cerrar-panel" role="button" aria-label="Cerrar">&times;</span>
    </div>
    <div id="calendar"></div>
    <form class="panel-form" id="formReserva" novalidate>
      <label for="nombre">Nombre completo:</label>
      <input type="text" id="nombre" required>
      <label for="telefono">Teléfono:</label>
      <input type="tel" id="telefono" inputmode="tel" placeholder="Ej: 3512345678" pattern="^[0-9\s()+-]{6,}$" required>
      <label for="servicio">Servicio:</label>
      <select id="servicio">
        <option value="1">Faciales</option>
        <option value="2">Bronceado</option>
        <option value="3">Laminado de Cejas</option>
        <option value="4">Microblading</option>
        <option value="5">Esculpidas</option>
        <option value="6">Pestañas</option>
      </select>
      <label for="fechaHora">Fecha y hora:</label>
      <input type="datetime-local" id="fechaHora" required>
      <button type="submit" class="boton">Confirmar Reserva</button>
    </form>
  </div>

  <!-- BOTONES FLOTANTES DE REDES SOCIALES (IZQUIERDA) -->
  <div class="social-floats">
    <div class="instagram-float">
      <a href="https://instagram.com/gretasaloncba" target="_blank" rel="noopener noreferrer" aria-label="Seguir en Instagram">
        <i class="fab fa-instagram"></i>
      </a>
    </div>
    
    <div class="whatsapp-float">
      <a href="https://wa.me/543517339043" target="_blank" rel="noopener noreferrer" aria-label="Contactar por WhatsApp">
        <i class="fab fa-whatsapp"></i>
      </a>
    </div>
  </div>

  <script>
    let calendar;

    document.addEventListener("DOMContentLoaded", () => {
      const pHero = document.querySelector(".hero-content p");
      if (pHero) maquinaDeEscribir("Donde la belleza y la elegancia se encuentran", pHero, 50);

      animarTarjetas();
      window.addEventListener("scroll", animarTarjetas);

      initPanelReserva();
    });

    function maquinaDeEscribir(texto, elemento, velocidad=50) {
      let i=0; elemento.textContent="";
      (function escribir(){ if(i < texto.length){ elemento.textContent += texto.charAt(i++); setTimeout(escribir, velocidad); } })();
    }

    function animarTarjetas() {
      const cards = document.querySelectorAll(".animate-card");
      const triggerBottom = (window.innerHeight/5) * 4;
      cards.forEach(card => {
        const cardTop = card.getBoundingClientRect().top;
        card.classList.toggle("visible", cardTop < triggerBottom);
      });
    }

    function initPanelReserva() {
      const openBtns = document.querySelectorAll(".abrir-modal");
      const closeBtn = document.getElementById("cerrarPanel");
      const panel    = document.getElementById("panelReserva");
      const servicioSelect = document.getElementById("servicio");

      if (!panel || !closeBtn || openBtns.length===0) return;

      openBtns.forEach(btn => btn.addEventListener("click", (e) => {
        e.preventDefault();
        abrirPanel();
      }));

      closeBtn.addEventListener("click", cerrarPanel);
      document.addEventListener("keydown", (e) => { if (e.key === "Escape") cerrarPanel(); });
      document.addEventListener("click", (e) => {
        if (document.body.classList.contains("panel-abierto") &&
            !panel.contains(e.target) &&
            !e.target.closest(".abrir-modal")) cerrarPanel();
      });

      if (servicioSelect) servicioSelect.addEventListener("change", () => {
        recargarEventos();
      });

      const form = document.getElementById("formReserva");
      form.addEventListener("submit", onEnviarReserva);
    }

    function abrirPanel() {
      const panel = document.getElementById("panelReserva");
      if (!panel) return;
      panel.classList.add("abierto");
      panel.setAttribute("aria-hidden","false");
      document.body.classList.add("panel-abierto");
      document.body.style.overflowY = "hidden";

      if (!calendar) {
        calendar = crearCalendario();
        calendar.render();
      } else {
        recargarEventos();
      }
    }

    function cerrarPanel() {
      const panel = document.getElementById("panelReserva");
      if (!panel) return;
      panel.classList.remove("abierto");
      panel.setAttribute("aria-hidden","true");
      document.body.classList.remove("panel-abierto");
      document.body.style.overflowY = "";
      panel.style.overflowY = "";
    }

    function crearCalendario() {
      const calendarEl = document.getElementById("calendar");
      return new FullCalendar.Calendar(calendarEl, {
        initialView: "timeGridWeek",
        locale: "es",
        slotMinTime: "09:00:00",
        slotMaxTime: "20:00:00",
        allDaySlot: false,
        hiddenDays: [0],
        headerToolbar: { left:"prev,next today", center:"title", right:"timeGridWeek,timeGridDay" },
        nowIndicator: true,
        selectable: false,
        dateClick: (info) => {
          if (info.date < new Date()) return;
          setFechaHoraInput(info.date);
        },
        eventClick: (info) => {
          const props = info.event.extendedProps || {};
          if (props.estado === "ocupado") {
            alert(`Turno ocupado por ${props.cliente}\nServicio: ${props.servicio}`);
          } else {
            setFechaHoraInput(info.event.start);
          }
        },
        events: (fetchInfo, successCallback, failureCallback) => {
          // Simulación de datos para el ejemplo
          const eventos = [
            {
              title: 'Disponible',
              start: new Date(new Date().setHours(10, 0, 0, 0)),
              color: '#4caf50',
              extendedProps: { estado: 'libre' }
            },
            {
              title: 'Ocupado',
              start: new Date(new Date().setHours(11, 0, 0, 0)),
              color: '#f44336',
              extendedProps: { estado: 'ocupado', cliente: 'María López', servicio: 'Facial' }
            }
          ];
          successCallback(eventos);
        }
      });
    }

    function setFechaHoraInput(dateObj) {
      const input = document.getElementById("fechaHora");
      if (!input) return;
      const pad = n => String(n).padStart(2,'0');
      const d = new Date(dateObj);
      const y = d.getFullYear(), m = pad(d.getMonth()+1), dd = pad(d.getDate());
      const hh = pad(d.getHours()), mm = pad(d.getMinutes());
      input.value = `${y}-${m}-${dd}T${hh}:${mm}`;
    }

    function getServicioId() {
      const s = document.getElementById("servicio");
      return s ? s.value : "1";
    }

    async function onEnviarReserva(e) {
      e.preventDefault();
      const nombre   = document.getElementById("nombre").value.trim();
      const telefono = document.getElementById("telefono").value.trim();
      const servicio = getServicioId();
      const fechaISO = document.getElementById("fechaHora").value;

      if (!nombre || !telefono || !fechaISO) {
        alert("Completá nombre, teléfono y fecha/hora."); return;
      }
      if (new Date(fechaISO) < new Date()) {
        alert("La fecha/hora debe ser futura."); return;
      }

      alert("✅ Reserva confirmada para: " + nombre);
      cerrarPanel();
      e.target.reset();
    }
  </script>
</body>
</html>