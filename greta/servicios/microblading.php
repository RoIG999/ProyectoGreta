<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Microblading & Microshading - GRETA</title>
  
  <!-- Fuentes e iconos -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <style>
    /* ESTILOS BARRA DE NAVEGACIÓN (IGUAL QUE INICIO.PHP) */
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

    body {
      font-family: 'Montserrat', sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      min-height: 100vh;
    }

    header {
      background: #000;
      color: white;
      padding: 10px 40px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: relative;
      z-index: 1000;
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

    /* BOTONES FLOTANTES (IGUAL QUE INICIO.PHP) */
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

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
      40% {transform: translateY(-10px);}
      60% {transform: translateY(-5px);}
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

    /* ===== BOTÓN DE RESERVA FIJO ===== */
    .reserva-fija {
      position: fixed;
      bottom: 30px;
      right: 30px;
      z-index: 1000;
      animation: bounce 2s infinite;
    }
    
    .reserva-fija .boton {
      background: linear-gradient(135deg, #f0c0d0, #e91e63);
      color: white;
      padding: 18px 35px;
      border-radius: 50px;
      font-weight: bold;
      font-size: 1.1rem;
      box-shadow: 0 10px 30px rgba(240, 192, 208, 0.4);
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .reserva-fija .boton:hover {
      transform: translateY(-5px) scale(1.05);
      box-shadow: 0 15px 40px rgba(240, 192, 208, 0.6);
    }

    /* ===== Carrusel que ocupa todo el espacio ===== */
    .swiper {
      width: 40%;
      max-width: 1000px;
      height: 60vh;
      min-height: 400px;
      max-height: 600px;
      margin: 40px auto;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(0,0,0,0.25);
      background: #000; /* Fondo negro para los espacios */
    }
    
    .swiper-wrapper {
      width: 100%;
      height: 100%;
    }
    
    .swiper-slide {
      position: relative;
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .swiper-slide img {
      width: 100%;
      height: 100%;
      object-fit: cover; /* Ocupa todo el espacio recortando si es necesario */
      object-position: center;
      display: block;
    }
    
    .swiper-button-next,
    .swiper-button-prev {
      color: #fff;
      background: rgba(0,0,0,0.5);
      width: 50px;
      height: 50px;
      border-radius: 50%;
      transition: all 0.3s ease;
    }
    
    .swiper-button-next:after,
    .swiper-button-prev:after {
      font-size: 20px;
      font-weight: bold;
    }
    
    .swiper-button-next:hover,
    .swiper-button-prev:hover {
      background: rgba(0,0,0,0.8);
      transform: scale(1.1);
    }
    
    .swiper-pagination-bullet {
      width: 12px;
      height: 12px;
      opacity: 0.7;
      background: #fff;
    }
    
    .swiper-pagination-bullet-active {
      background: #f0c0d0;
      opacity: 1;
      transform: scale(1.2);
    }

    /* ===== Media Queries para Responsive ===== */
    @media (max-width: 1024px) {
      .swiper {
        width: 80%;
        height: 60vh;
        min-height: 350px;
        max-height: 500px;
      }
    }
    
    @media (max-width: 768px) {
      .swiper {
        width: 90%;
        height: 50vh;
        min-height: 300px;
        max-height: 400px;
        border-radius: 10px;
        margin: 20px auto;
      }
      
      .swiper-button-next,
      .swiper-button-prev {
        width: 40px;
        height: 40px;
      }
      
      .swiper-button-next:after,
      .swiper-button-prev:after {
        font-size: 16px;
      }

      .reserva-fija {
        bottom: 20px;
        right: 20px;
      }
      
      .reserva-fija .boton {
        padding: 15px 25px;
        font-size: 1rem;
      }
    }
    
    @media (max-width: 480px) {
      .swiper {
        width: 95%;
        height: 40vh;
        min-height: 250px;
        max-height: 300px;
      }

      .reserva-fija {
        bottom: 15px;
        right: 15px;
        left: 15px;
        text-align: center;
      }
      
      .reserva-fija .boton {
        width: 100%;
        justify-content: center;
      }
    }

    /* ===== NUEVOS ESTILOS PARA SUBSERVICIOS ===== */
    .servicios-container {
      max-width: 1200px;
      margin: 60px auto;
      padding: 0 20px;
    }
    
    .servicios-header {
      text-align: center;
      margin-bottom: 50px;
    }
    
    .servicios-header h1 {
      font-size: 3.5rem;
      background: linear-gradient(135deg, #000, #f0c0d0);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 20px;
      animation: float 3s ease-in-out infinite;
    }
    
    .servicios-header p {
      font-size: 1.2rem;
      color: #666;
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.6;
    }
    
    .servicios-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 30px;
      margin-top: 40px;
    }
    
    .servicio-card {
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      transition: all 0.4s ease;
      cursor: pointer;
      position: relative;
    }
    
    .servicio-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .servicio-imagen {
      height: 250px;
      overflow: hidden;
    }
    
    .servicio-imagen img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    
    .servicio-card:hover .servicio-imagen img {
      transform: scale(1.1);
    }
    
    .servicio-contenido {
      padding: 25px;
    }
    
    .servicio-contenido h3 {
      font-size: 1.5rem;
      margin: 0 0 15px 0;
      color: #333;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .servicio-contenido p {
      color: #666;
      line-height: 1.6;
      margin-bottom: 20px;
    }
    
    .servicio-details {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 15px;
      margin-top: 15px;
      display: none;
      animation: fadeIn 0.5s ease;
    }
    
    .servicio-details.active {
      display: block;
    }
    
    .servicio-details h4 {
      color: #333;
      margin-top: 0;
      margin-bottom: 10px;
      font-size: 1.1rem;
    }
    
    .servicio-details ul {
      padding-left: 20px;
      margin: 0;
    }
    
    .servicio-details li {
      margin-bottom: 8px;
      color: #555;
    }
    
    .servicio-precio {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid #eee;
    }
    
    .precio-container {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }
    
    .precio {
      font-weight: bold;
      font-size: 1.8rem;
      color: #28a745;
      font-weight: 700;
    }
    
    .precio-promo {
      font-size: 0.9rem;
      color: #666;
    }
    
    .servicio-icono {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #f0c0d0, #e91e63);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.2rem;
    }
    
    .servicio-toggle {
      background: none;
      border: none;
      color: #e91e63;
      font-weight: bold;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: 15px;
      padding: 8px 16px;
      background: #f8f9fa;
      border-radius: 25px;
      transition: all 0.3s ease;
      font-size: 0.9rem;
    }
    
    .servicio-toggle:hover {
      background: #e91e63;
      color: white;
      transform: translateY(-2px);
    }
    
    .servicio-toggle i {
      transition: transform 0.3s ease;
    }
    
    .servicio-toggle.active i {
      transform: rotate(180deg);
    }
    
    .beneficios-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }
    
    .beneficio-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 15px;
      background: #f8f9fa;
      border-radius: 10px;
      transition: all 0.3s ease;
    }
    
    .beneficio-item:hover {
      background: #e9ecef;
      transform: translateX(5px);
    }
    
    .beneficio-icono {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #000, #333);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      flex-shrink: 0;
    }
    
    .beneficio-texto h4 {
      margin: 0 0 5px 0;
      color: #333;
      font-size: 1rem;
    }
    
    .beneficio-texto p {
      margin: 0;
      color: #666;
      font-size: 0.9rem;
    }
    
    .info-adicional {
      background: #e8f5e8;
      padding: 15px;
      border-radius: 10px;
      margin-top: 15px;
      font-size: 0.9rem;
      color: #2d5016;
      border-left: 4px solid #28a745;
    }
    
    .cta-section {
      text-align: center;
      margin: 60px 0;
      padding: 40px;
      background: linear-gradient(135deg, #000, #333);
      color: white;
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    
    .cta-section h2 {
      font-size: 2.5rem;
      margin-bottom: 20px;
    }
    
    .cta-section p {
      font-size: 1.2rem;
      margin-bottom: 30px;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
      line-height: 1.6;
    }

    footer {
      text-align: center;
      padding: 30px;
      background: #000;
      color: white;
      margin-top: 60px;
    }
  </style>
</head>
<body>

  <!-- Header (IGUAL QUE INICIO.PHP) -->
  <header>
    <div class="logo-container">
      <a class="brand" href="../Inicio.php" aria-label="Inicio GRETA">
        <img src="../img/LogoGreta.jpeg" alt="GRETA Estética">
      </a>
    </div>

    <nav id="site-nav">
      <a href="../Inicio.php"><i class="fas fa-home"></i> Inicio</a>
      <a href="../Servicios.php" class="activo"><i class="fas fa-spa"></i> Servicios</a>
      <a href="../Nosotros.php"><i class="fas fa-users"></i> Nosotros</a>
      <a href="../Contacto.php"><i class="fas fa-envelope"></i> Contacto</a>
      <a href="../Calendario.php"><i class="fas fa-calendar-alt"></i> Reservar Turno</a>
      <a href="../Login.php"><i class="fas fa-user"></i> Ingreso</a>
    </nav>
  </header>

  <!-- BOTÓN DE RESERVA FIJO -->
  <div class="reserva-fija">
    <a href="../Calendario.php" class="boton">
      <i class="fas fa-calendar-check"></i> Reservar Turno
    </a>
  </div>

  <!-- Carrusel -->
  <div class="swiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide"><img src="../img/microblanding 2.png" alt="Microblading profesional"></div>
      <div class="swiper-slide"><img src="../img/microblading 3.jpg" alt="Resultado microblading natural"></div>
      <div class="swiper-slide"><img src="../img/microblading 4.webp" alt="Técnica de microblading"></div>
      <div class="swiper-slide"><img src="../img/microblading 6.webp" alt="Cejas perfectas con microblading"></div>
    </div>

    <!-- Botones -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
  </div>

  <!-- Sección de Servicios -->
  <section class="servicios-container">
    <div class="servicios-header">
      <h1>🎯 Microblading & Microshading</h1>
      <p>Transforma tu mirada con nuestras técnicas especializadas de micropigmentación. Descubre la opción perfecta para cejas naturales y definidas.</p>
    </div>

    <div class="servicios-grid">
      <!-- Servicio: Microblading Tradicional -->
      <div class="servicio-card">
        <div class="servicio-imagen">
          <img src="../img/microblading 3.jpg" alt="Microblading tradicional">
        </div>
        <div class="servicio-contenido">
          <h3><span class="servicio-icono">✏️</span> Microblading Tradicional</h3>
          <p>Maquillaje semipermanente que imita los pelitos naturales. Ideal para rellenar, dar forma o crear cejas totalmente nuevas.</p>
          
          <button class="servicio-toggle">Ver detalles <i class="fas fa-chevron-down"></i></button>
          
          <div class="servicio-details">
            <h4>¿Qué incluye?</h4>
            <ul>
              <li>Sesión inicial de 2 horas</li>
              <li>Diseño personalizado de cejas</li>
              <li>Aplicación con técnica microblading</li>
              <li>Retoque obligatorio a los 20 días</li>
              <li>Pigmentos naturales hipoalergénicos</li>
              <li>Duración: 12-18 meses</li>
            </ul>
            
            <div class="beneficios-grid">
              <div class="beneficio-item">
                <div class="beneficio-icono"><i class="fas fa-pencil-alt"></i></div>
                <div class="beneficio-texto">
                  <h4>Trazos Naturales</h4>
                  <p>Imitan vello real</p>
                </div>
              </div>
              <div class="beneficio-item">
                <div class="beneficio-icono"><i class="fas fa-calendar-alt"></i></div>
                <div class="beneficio-texto">
                  <h4>Durabilidad</h4>
                  <p>Hasta 18 meses</p>
                </div>
              </div>
              <div class="beneficio-item">
                <div class="beneficio-icono"><i class="fas fa-user-check"></i></div>
                <div class="beneficio-texto">
                  <h4>Personalizado</h4>
                  <p>Según tu rostro</p>
                </div>
              </div>
            </div>

            <div class="info-adicional">
              <strong>💡 Tercera sesión:</strong> $95.000 si se realiza dentro de los 2 meses posteriores (efectivo)
            </div>
          </div>
          
          <div class="servicio-precio">
            <div class="precio-container">
              <span class="precio">$154.000</span>
              <span class="precio-promo">Efectivo o transferencia</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Servicio: Microshading -->
      <div class="servicio-card">
        <div class="servicio-imagen">
          <img src="../img/microblading 4.webp" alt="Microshading efecto polvo">
        </div>
        <div class="servicio-contenido">
          <h3><span class="servicio-icono"><i class="fas fa-fill-drip"></i></span> Microshading</h3>
          <p>Efecto polvo en cejas - técnica suave que crea cejas maquilladas con sombra, ideal para forma y definición.</p>
          
          <button class="servicio-toggle">Ver detalles <i class="fas fa-chevron-down"></i></button>
          
          <div class="servicio-details">
            <h4>¿Qué incluye?</h4>
            <ul>
              <li>Sesión inicial completa</li>
              <li>Evaluación facial personalizada</li>
              <li>Técnica microshading profesional</li>
              <li>Retoque obligatorio en primer mes</li>
              <li>Pigmentos de alta calidad</li>
              <li>Duración: 1 año aprox.</li>
            </ul>
            
            <div class="beneficios-grid">
              <div class="beneficio-item">
                <div class="beneficio-icono"><i class="fas fa-paint-brush"></i></div>
                <div class="beneficio-texto">
                  <h4>Efecto Polvo</h4>
                  <p>Como maquillada</p>
                </div>
              </div>
              <div class="beneficio-item">
                <div class="beneficio-icono"><i class="fas fa-cloud"></i></div>
                <div class="beneficio-texto">
                  <h4>Acabado Suave</h4>
                  <p>Look natural</p>
                </div>
              </div>
              <div class="beneficio-item">
                <div class="beneficio-icono"><i class="fas fa-shield-alt"></i></div>
                <div class="beneficio-texto">
                  <h4>Mínima Invasión</h4>
                  <p>Técnica segura</p>
                </div>
              </div>
            </div>

            <div class="info-adicional">
              <strong>✨ Perfecto para:</strong> Quienes buscan un look suave y definido, como tener las cejas maquilladas permanentemente.
            </div>
          </div>
          
          <div class="servicio-precio">
            <div class="precio-container">
              <span class="precio">$159.300</span>
              <span class="precio-promo">Efectivo o transferencia</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Sección CTA -->
  <section class="servicios-container">
    <div class="cta-section">
      <h2>✨ Transforma Tu Mirada Hoy</h2>
      <p>Despierta cada mañana con cejas perfectas. Elige la técnica que mejor se adapte a tu estilo y disfruta de resultados naturales y duraderos.</p>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>© 2025 GRETA Estética - Todos los derechos reservados</p>
  </footer>

  <!-- BOTONES FLOTANTES DE REDES SOCIALES (IGUAL QUE INICIO.PHP) -->
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

  <!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    // Inicializar Swiper
    const swiper = new Swiper('.swiper', {
      loop: true,
      autoplay: { delay: 4000 },
      pagination: { el: '.swiper-pagination', clickable: true },
      navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    });

    // Funcionalidad para mostrar/ocultar detalles de servicios
    document.querySelectorAll('.servicio-toggle').forEach(button => {
      button.addEventListener('click', function() {
        const details = this.nextElementSibling;
        const isActive = details.classList.contains('active');
        
        // Cerrar todos los detalles abiertos
        document.querySelectorAll('.servicio-details').forEach(d => {
          d.classList.remove('active');
        });
        document.querySelectorAll('.servicio-toggle').forEach(b => {
          b.classList.remove('active');
        });
        
        // Abrir el actual si no estaba activo
        if (!isActive) {
          details.classList.add('active');
          this.classList.add('active');
        }
      });
    });

    // Animación de entrada para las tarjetas
    document.addEventListener('DOMContentLoaded', function() {
      const cards = document.querySelectorAll('.servicio-card');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
          card.style.transition = 'all 0.6s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, 300 + (index * 200));
      });
    });
  </script>
</body>
</html>