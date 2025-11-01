<?php 
// Detectar la página actual
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Servicios - GRETA</title>
  <link rel="stylesheet" href="css/estilos.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <style>
    /* Fondo elegante para toda la página */
    body {
      font-family: 'Montserrat', sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #0c0c0c 0%, #1a1a1a 50%, #0c0c0c 100%);
      min-height: 100vh;
      color: white;
    }

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

    header {
      background: rgba(0, 0, 0, 0.95);
      color: white;
      padding: 10px 40px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: relative;
      z-index: 1000;
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    header img {
      height: 55px;
    }
    
    nav a {
      color: white;
      margin-left: 20px;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      padding: 8px 0;
      position: relative;
    }
    
    nav a:hover {
      color: #f0c0d0;
    }
    
    nav a.activo {
      color: #f0c0d0;
    }
    
    nav a.activo::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 100%;
      height: 2px;
      background: #f0c0d0;
      animation: pulseBorder 2s infinite;
    }

    @keyframes pulseBorder {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }

    /* ===== CARRUSEL GRANDE Y PROFESIONAL ===== */
    .swiper-grande {
      width: 100%;
      height: 80vh;
      min-height: 600px;
      margin: 0 auto;
      position: relative;
    }
    
    .swiper-slide-grande {
      position: relative;
      width: 100%;
      height: 100%;
      overflow: hidden;
    }
    
    .slide-background {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      transition: transform 0.8s ease;
    }
    
    .swiper-slide-grande:hover .slide-background {
      transform: scale(1.05);
    }
    
    .slide-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, 
        rgba(0,0,0,0.7) 0%, 
        rgba(0,0,0,0.4) 50%, 
        rgba(0,0,0,0.7) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .slide-content {
      text-align: center;
      color: white;
      max-width: 600px;
      padding: 0 20px;
      transform: translateY(50px);
      opacity: 0;
      transition: all 0.8s ease;
    }
    
    .swiper-slide-active .slide-content {
      transform: translateY(0);
      opacity: 1;
    }
    
    .slide-icon {
      font-size: 4rem;
      margin-bottom: 20px;
      background: linear-gradient(135deg, #f0c0d0, #e91e63);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    
    .slide-title {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 20px;
      background: linear-gradient(135deg, #ffffff, #f0c0d0);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    
    .slide-description {
      font-size: 1.3rem;
      margin-bottom: 30px;
      line-height: 1.6;
      opacity: 0.9;
    }
    
    .slide-button {
      display: inline-block;
      background: linear-gradient(135deg, #f0c0d0, #e91e63);
      color: white;
      padding: 15px 35px;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      box-shadow: 0 10px 25px rgba(240, 192, 208, 0.3);
    }
    
    .slide-button:hover {
      transform: translateY(-3px) scale(1.05);
      box-shadow: 0 15px 35px rgba(240, 192, 208, 0.5);
    }
    
    /* Navegación del carrusel */
    .swiper-button-next-grande,
    .swiper-button-prev-grande {
      color: #f0c0d0;
      background: rgba(0,0,0,0.5);
      width: 60px;
      height: 60px;
      border-radius: 50%;
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
    }
    
    .swiper-button-next-grande:after,
    .swiper-button-prev-grande:after {
      font-size: 24px;
      font-weight: bold;
    }
    
    .swiper-button-next-grande:hover,
    .swiper-button-prev-grande:hover {
      background: rgba(240, 192, 208, 0.2);
      transform: scale(1.1);
    }
    
    .swiper-pagination-grande {
      bottom: 30px !important;
    }
    
    .swiper-pagination-bullet {
      width: 12px;
      height: 12px;
      background: white;
      opacity: 0.5;
      transition: all 0.3s ease;
    }
    
    .swiper-pagination-bullet-active {
      background: #f0c0d0;
      opacity: 1;
      transform: scale(1.3);
    }

    /* Sección de información adicional */
    .info-section {
      padding: 80px 20px;
      background: rgba(255, 255, 255, 0.02);
      backdrop-filter: blur(10px);
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .info-grid {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 40px;
    }
    
    .info-card {
      text-align: center;
      padding: 40px 30px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
    }
    
    .info-card:hover {
      transform: translateY(-10px);
      background: rgba(255, 255, 255, 0.08);
      box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }
    
    .info-icon {
      font-size: 3rem;
      margin-bottom: 20px;
      color: #f0c0d0;
    }
    
    .info-card h3 {
      font-size: 1.5rem;
      margin-bottom: 15px;
      color: white;
    }
    
    .info-card p {
      color: rgba(255, 255, 255, 0.8);
      line-height: 1.6;
    }

    /* Footer */
    footer {
      background: rgba(0, 0, 0, 0.95);
      color: white;
      text-align: center;
      padding: 40px 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .swiper-grande {
        height: 70vh;
        min-height: 500px;
      }
      
      .slide-title {
        font-size: 3rem;
      }
    }
    
    @media (max-width: 768px) {
      header {
        padding: 10px 20px;
        flex-direction: column;
        gap: 15px;
      }
      
      nav {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
      }
      
      nav a {
        margin-left: 0;
      }
      
      .swiper-grande {
        height: 60vh;
        min-height: 400px;
      }
      
      .slide-title {
        font-size: 2.5rem;
      }
      
      .slide-description {
        font-size: 1.1rem;
      }
      
      .slide-icon {
        font-size: 3rem;
      }
      
      .swiper-button-next-grande,
      .swiper-button-prev-grande {
        width: 50px;
        height: 50px;
      }
      
      .swiper-button-next-grande:after,
      .swiper-button-prev-grande:after {
        font-size: 20px;
      }
    }
    
    @media (max-width: 480px) {
      .swiper-grande {
        height: 50vh;
        min-height: 300px;
      }
      
      .slide-title {
        font-size: 2rem;
      }
      
      .slide-description {
        font-size: 1rem;
      }
      
      .slide-button {
        padding: 12px 25px;
        font-size: 1rem;
      }
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
    
    <nav>
      <a href="Inicio.php" <?php echo ($pagina_actual == 'Inicio.php') ? 'class="activo"' : ''; ?>>
        <i class="fas fa-home"></i> Inicio
      </a>
      <a href="Servicios.php" <?php echo ($pagina_actual == 'Servicios.php') ? 'class="activo"' : ''; ?>>
        <i class="fas fa-spa"></i> Servicios
      </a>
      <a href="Nosotros.php" <?php echo ($pagina_actual == 'Nosotros.php') ? 'class="activo"' : ''; ?>>
        <i class="fas fa-users"></i> Nosotros
      </a>
      <a href="Contacto.php" <?php echo ($pagina_actual == 'Contacto.php') ? 'class="activo"' : ''; ?>>
        <i class="fas fa-envelope"></i> Contacto
      </a>
      <a href="calendario.php">
        <i class="fas fa-calendar-check"></i> Reservar Turno
      </a>
      <a href="Login.php">
        <i class="fas fa-user"></i> Ingreso
      </a>
    </nav>
  </header>

  <!-- Carrusel Grande de Servicios -->
  <div class="swiper swiper-grande">
    <div class="swiper-wrapper">
      
      <!-- Slide 1: Bronceado -->
      <div class="swiper-slide swiper-slide-grande">
        <div class="slide-background" style="background-image: url('img/bronceado 6.jpg');"></div>
        <div class="slide-overlay">
          <div class="slide-content">
            <div class="slide-icon">☀️</div>
            <h2 class="slide-title">Bronceado Profesional</h2>
            <p class="slide-description">
              Tono dorado, uniforme y natural sin exposición al sol. 
              Tratamiento profesional que cuida tu piel mientras realza tu belleza.
            </p>
            <a href="servicios/bronceado.php" class="slide-button">Descubrir Más</a>
          </div>
        </div>
      </div>
      
      <!-- Slide 2: Uñas Esculpidas -->
      <div class="swiper-slide swiper-slide-grande">
        <div class="slide-background" style="background-image: url('img/uñas 6.png');"></div>
        <div class="slide-overlay">
          <div class="slide-content">
            <div class="slide-icon">💎</div>
            <h2 class="slide-title">Uñas Esculpidas</h2>
            <p class="slide-description">
              Transformamos tus uñas en obras de arte. Diseños exclusivos, 
              acabados impecables y durabilidad excepcional.
            </p>
            <a href="servicios/esculpidas.php" class="slide-button">Ver Servicios</a>
          </div>
        </div>
      </div>
      
      
      <!-- Slide 4: Pestañas -->
      <div class="swiper-slide swiper-slide-grande">
        <div class="slide-background" style="background-image: url('img/Pestañas.jpg');"></div>
        <div class="slide-overlay">
          <div class="slide-content">
            <div class="slide-icon">👁️</div>
            <h2 class="slide-title">Extensiones de Pestañas</h2>
            <p class="slide-description">
              Mirada intensa y expresiva. Extensiones naturales, 
              lifting y técnicas profesionales para resaltar tu belleza.
            </p>
            <a href="servicios/pestañas.php" class="slide-button">Explorar</a>
          </div>
        </div>
      </div>
      
      <!-- Slide 5: Cejas -->
      <div class="swiper-slide swiper-slide-grande">
        <div class="slide-background" style="background-image: url('img/laminado.jpg');"></div>
        <div class="slide-overlay">
          <div class="slide-content">
            <div class="slide-icon">✏️</div>
            <h2 class="slide-title">Belleza de Cejas</h2>
            <p class="slide-description">
              Laminado, microblading y diseño profesional. 
              Cejas perfectamente definidas que enmarcan tu rostro.
            </p>
            <a href="servicios/laminado.php" class="slide-button">Descubrir</a>
          </div>
        </div>
      </div>
      
      <!-- Slide 6: Microblading -->
      <div class="swiper-slide swiper-slide-grande">
        <div class="slide-background" style="background-image: url('img/microblanding 2.png');"></div>
        <div class="slide-overlay">
          <div class="slide-content">
            <div class="slide-icon">🎨</div>
            <h2 class="slide-title">Microblading</h2>
            <p class="slide-description">
              Técnica semipermanente para cejas naturales y definidas. 
              Resultados que transforman tu mirada por completo.
            </p>
            <a href="servicios/microblading.php" class="slide-button">Saber Más</a>
          </div>
        </div>
      </div>
      
    </div>

    <!-- Navegación -->
    <div class="swiper-button-next swiper-button-next-grande"></div>
    <div class="swiper-button-prev swiper-button-prev-grande"></div>
    <div class="swiper-pagination swiper-pagination-grande"></div>
  </div>

  <!-- Sección Informativa -->
  <section class="info-section">
    <div class="info-grid">
      <div class="info-card">
        <div class="info-icon"><i class="fas fa-gem"></i></div>
        <h3>Calidad Premium</h3>
        <p>Utilizamos productos de primera calidad y técnicas avanzadas para garantizar resultados excepcionales.</p>
      </div>
      
      <div class="info-card">
        <div class="info-icon"><i class="fas fa-user-md"></i></div>
        <h3>Profesionales Certificados</h3>
        <p>Nuestro equipo está compuesto por especialistas con amplia experiencia y formación continua.</p>
      </div>
      
      <div class="info-card">
        <div class="info-icon"><i class="fas fa-heart"></i></div>
        <h3>Enfoque Personalizado</h3>
        <p>Cada tratamiento es adaptado a tus necesidades específicas y objetivos de belleza.</p>
      </div>
    </div>
  </section>

  <footer>
    <p>© 2025 GRETA Estética - Todos los derechos reservados</p>
    <p style="margin-top: 10px; opacity: 0.7;">Virgen de la Merced 2345, Córdoba | +54 351 733-9043</p>
  </footer>

  <!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    // Inicializar Swiper
    const swiper = new Swiper('.swiper-grande', {
      loop: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
      speed: 1000,
      pagination: {
        el: '.swiper-pagination-grande',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next-grande',
        prevEl: '.swiper-button-prev-grande',
      },
      on: {
        init: function () {
          console.log('Carrusel de servicios inicializado');
        },
      },
    });

    // Pausar autoplay al hacer hover
    const swiperContainer = document.querySelector('.swiper-grande');
    swiperContainer.addEventListener('mouseenter', function() {
      swiper.autoplay.stop();
    });
    
    swiperContainer.addEventListener('mouseleave', function() {
      swiper.autoplay.start();
    });
  </script>

</body>
</html>