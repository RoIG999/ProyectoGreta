<?php 
// Detectar la página actual
$pagina_actual = basename($_SERVER['PHP_SELF']);

// Conexión a la base de datos para obtener servicios
include("conexion.php");

// Obtener servicios activos de la base de datos
$servicios_activos = [];
$sql = "SELECT * FROM servicio WHERE estado = 1 ORDER BY nombre ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $servicios_activos[] = $row;
    }
}

// Cerrar conexión
$conn->close();

// SISTEMA MEJORADO DE IMÁGENES - POR NOMBRE ORIGINAL
$imagenes_servicios = [
    'Bronceado' => 'img/bronceado 2.png',
    'Faciales' => 'img/faciales.png',
    'Esculpidas' => 'img/uñas 11.png', 
    'Pestañas' => 'img/Pestañas.jpg',
    'Perfilado de Cejas' => 'img/laminado.jpg',
    'Microblading' => 'img/microblanding 2.png',
    'Microshading' => 'img/microblanding 2.png',
    'Semipermanente' => 'img/semipermanente.jpg',
    'Kapping' => 'img/kapping.jpg',
    'Masajes' => 'img/vacia.webp'
];

// Mapeo de íconos para cada servicio
$iconos_servicios = [
    'Bronceado' => '☀️',
    'Faciales' => '✨',
    'Esculpidas' => '💎',
    'Pestañas' => '👁️',
    'Perfilado de Cejas' => '✏️',
    'Microblading' => '🎨',
    'Microshading' => '🌸',
    'Semipermanente' => '💅',
    'Kapping' => '🔧',
    'Masajes' => '💆'
];

// Mapeo de imágenes de fondo para el carrusel
$fondos_servicios = [
    'Bronceado' => 'img/bronceado 6.jpg',
    'Faciales' => 'img/faciales 4 (2).jpg',
    'Esculpidas' => 'img/uñas 4.avif', 
    'Pestañas' => 'img/Pestañas 4.jpg',
    'Perfilado de Cejas' => 'img/laminado 3.webp',
    'Microblading' => 'img/microblading 5.avif',
    'Microshading' => 'img/microblading 6.webp',
    'Semipermanente' => 'img/semipermanente 3.jpg',
    'Kapping' => 'img/kapping 4.avif',
    'Masajes' => 'img/vacia.webp'
];

// Imágenes de fondo por defecto para servicios sin imagen específica
$fondos_default = [
    'img/bronceado 2.png',
    'img/faciales.png',
    'img/uñas 11.png',
    'img/Pestañas.jpg'
];
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

    /* ===== CARRUSEL CON IMÁGENES GRANDES PERO BIEN VISIBLES ===== */
    .swiper-grande {
      width: 100%;
      height: 60vh;
      min-height: 550px;
      margin: 0 auto;
      position: relative;
    }
    
    /* Contenedor del slide */
.swiper-slide-grande {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
  background: #0c0c0c;
}
/* Backdrop blur que rellena todo el slide */
.slide-backdrop {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: 50% 50%;
  filter: blur(20px) brightness(0.6);
  transform: scale(1.1); /* evita ver bordes del blur */
  z-index: 1;
}
    
    /* Contenedor de la imagen principal */
.slide-image-container {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 3; /* por encima del blur */
  overflow: hidden;
}
    
 /* Imagen principal: SIEMPRE visible completa */
.slide-image {
  width: 100%;
  height: 100%;
  object-fit: center; /* llena el carrusel ahora que es 16:9 */
  object-position: center;
  transition: transform 1.2s ease, filter 0.3s ease;
  filter: brightness(0.9);
}


    
    /* Hover sutil (opcional) */
.swiper-slide-grande:hover .slide-image {
  transform: scale(1.02);
  filter: brightness(1);
}
    .slide-overlay {/* Overlay con textos ya estaba en 2; súbelo a 3 para ir por encima de la foto */

      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, 
        rgba(0,0,0,0.85) 0%, 
        rgba(0,0,0,0.5) 50%, 
        rgba(0,0,0,0.85) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 3;
    }
    
    .slide-content {
      text-align: center;
      color: white;
      max-width: 800px;
      padding: 0 40px;
      transform: translateY(20px);
      opacity: 0;
      transition: all 1s ease;
      z-index: 3;
    }
    
    .swiper-slide-active .slide-content {
      transform: translateY(0);
      opacity: 1;
    }
    
    .slide-icon {
      font-size: 4.5rem;
      margin-bottom: 25px;
      background: linear-gradient(135deg, #f0c0d0, #e91e63);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: float 3s ease-in-out infinite;
      text-shadow: 0 5px 15px rgba(0,0,0,0.5);
    }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-15px); }
    }
    
    .slide-title {
      font-size: 4rem;
      font-weight: 700;
      margin-bottom: 25px;
      background: linear-gradient(135deg, #ffffff, #f0c0d0);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-shadow: 0 8px 25px rgba(0,0,0,0.5);
      line-height: 1.1;
    }
    
    .slide-description {
      font-size: 1.4rem;
      margin-bottom: 20px;
      line-height: 1.6;
      opacity: 0.95;
      min-height: 70px;
      font-weight: 300;
      text-shadow: 0 2px 10px rgba(0,0,0,0.7);
    }
    
    .slide-precio {
      font-size: 2.5rem;
      font-weight: 700;
      color: #f0c0d0;
      margin-bottom: 20px;
      text-shadow: 0 4px 15px rgba(240, 192, 208, 0.3);
    }
    
    .slide-duracion {
      display: inline-block;
      background: rgba(240, 192, 208, 0.25);
      color: white;
      padding: 10px 20px;
      border-radius: 25px;
      font-size: 1.1rem;
      margin-bottom: 30px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.1);
      font-weight: 500;
    }
    
    /* BOTONES */
    .slide-buttons {
      display: flex;
      gap: 20px;
      justify-content: center;
      flex-wrap: wrap;
    }
    
    .slide-button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background: linear-gradient(135deg, #f0c0d0, #e91e63);
      color: white;
      padding: 15px 30px;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.4s ease;
      box-shadow: 0 10px 25px rgba(240, 192, 208, 0.4);
      border: none;
      cursor: pointer;
      min-width: 160px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .slide-button:before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s ease;
    }
    
    .slide-button:hover:before {
      left: 100%;
    }
    
    .slide-button:hover {
      transform: translateY(-5px) scale(1.05);
      box-shadow: 0 15px 35px rgba(240, 192, 208, 0.6);
    }
    
    .slide-button.detalles {
      background: linear-gradient(135deg, #667eea, #764ba2);
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }
    
    .slide-button.detalles:hover {
      box-shadow: 0 15px 35px rgba(102, 126, 234, 0.6);
    }
    
    .slide-button.reservar {
      background: linear-gradient(135deg, #f0c0d0, #e91e63);
    }
    
    /* Navegación del carrusel */
    .swiper-button-next-grande,
    .swiper-button-prev-grande {
      color: #f0c0d0;
      background: rgba(0,0,0,0.6);
      width: 70px;
      height: 70px;
      border-radius: 50%;
      backdrop-filter: blur(10px);
      transition: all 0.4s ease;
      z-index: 10;
      border: 2px solid rgba(240, 192, 208, 0.3);
    }
    
    .swiper-button-next-grande:after,
    .swiper-button-prev-grande:after {
      font-size: 28px;
      font-weight: bold;
    }
    
    .swiper-button-next-grande:hover,
    .swiper-button-prev-grande:hover {
      background: rgba(240, 192, 208, 0.2);
      transform: scale(1.15);
      border-color: rgba(240, 192, 208, 0.6);
    }
    
    .swiper-pagination-grande {
      bottom: 40px !important;
      z-index: 10;
    }
    
    .swiper-pagination-bullet {
      width: 14px;
      height: 14px;
      background: white;
      opacity: 0.6;
      transition: all 0.4s ease;
      border: 2px solid transparent;
    }
    
    .swiper-pagination-bullet-active {
      background: #f0c0d0;
      opacity: 1;
      transform: scale(1.4);
      border-color: rgba(255,255,255,0.5);
    }

    /* ===== SECCIÓN INFORMATIVA ===== */
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
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 40px;
    }
    
    .info-card {
      text-align: center;
      padding: 50px 30px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.4s ease;
    }
    
    .info-card:hover {
      transform: translateY(-10px);
      background: rgba(255, 255, 255, 0.08);
      box-shadow: 0 25px 50px rgba(0,0,0,0.4);
      border-color: rgba(240, 192, 208, 0.3);
    }
    
    .info-icon {
      font-size: 3.5rem;
      margin-bottom: 25px;
      color: #f0c0d0;
      transition: transform 0.4s ease;
    }
    
    .info-card:hover .info-icon {
      transform: scale(1.1) rotate(5deg);
    }
    
    .info-card h3 {
      font-size: 1.6rem;
      margin-bottom: 15px;
      color: white;
      font-weight: 600;
    }
    
    .info-card p {
      color: rgba(255, 255, 255, 0.8);
      line-height: 1.6;
      font-size: 1.05rem;
    }

    /* Footer */
    footer {
      background: rgba(0, 0, 0, 0.95);
      color: white;
      text-align: center;
      padding: 50px 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Mensaje cuando no hay servicios */
    .no-services {
      text-align: center;
      padding: 100px 20px;
    }
    
    .no-services-icon {
      font-size: 6rem;
      margin-bottom: 30px;
      color: #f0c0d0;
      animation: float 3s ease-in-out infinite;
    }
    
    .no-services h2 {
      font-size: 3rem;
      margin-bottom: 20px;
      color: white;
      background: linear-gradient(135deg, #ffffff, #f0c0d0);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .no-services p {
      font-size: 1.3rem;
      color: rgba(255, 255, 255, 0.7);
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.6;
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .swiper-grande {
        height: 65vh;
        min-height: 500px;
      }
      
      .slide-title {
        font-size: 3.2rem;
      }
      
      .slide-description {
        font-size: 1.2rem;
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
        min-height: 450px;
      }
      
      .slide-title {
        font-size: 2.8rem;
      }
      
      .slide-description {
        font-size: 1.1rem;
      }
      
      .slide-precio {
        font-size: 2rem;
      }
      
      .slide-icon {
        font-size: 3.5rem;
      }
      
      .slide-buttons {
        flex-direction: column;
        gap: 15px;
      }
      
      .slide-button {
        min-width: 100%;
        padding: 12px 25px;
      }
      
      .swiper-button-next-grande,
      .swiper-button-prev-grande {
        width: 55px;
        height: 55px;
      }
      
      .swiper-button-next-grande:after,
      .swiper-button-prev-grande:after {
        font-size: 22px;
      }
      
      .info-section {
        padding: 60px 20px;
      }
      
      .info-grid {
        grid-template-columns: 1fr;
        gap: 30px;
      }
    }
    
    @media (max-width: 480px) {
      .swiper-grande {
        height: 55vh;
        min-height: 400px;
      }
      
      .slide-title {
        font-size: 2.2rem;
      }
      
      .slide-description {
        font-size: 1rem;
      }
      
      .slide-precio {
        font-size: 1.8rem;
      }
      
      .slide-button {
        padding: 12px 20px;
        font-size: 1rem;
      }
      
      .slide-icon {
        font-size: 3rem;
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

  <!-- Carrusel con imágenes grandes y bien visibles -->
  <div class="swiper swiper-grande">
    <div class="swiper-wrapper">
      <?php if (empty($servicios_activos)): ?>
        <!-- Slide por defecto cuando no hay servicios -->
        <div class="swiper-slide swiper-slide-grande">
          <div class="slide-image-container">
            <img src="img/bronceado 6.jpg" alt="Servicios GRETA" class="slide-image">
          </div>
          <div class="slide-overlay">
            <div class="slide-content">
              <div class="slide-icon">✨</div>
              <h2 class="slide-title">Nuestros Servicios</h2>
              <p class="slide-description">
                Próximamente tendremos los mejores tratamientos de belleza para ti. 
                Estamos preparando una experiencia única.
              </p>
              <div class="slide-buttons">
                <a href="Contacto.php" class="slide-button detalles">
                  <i class="fas fa-info-circle"></i> Más Información
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php else: ?>
        <?php foreach ($servicios_activos as $index => $servicio): ?>
          <?php
            $precio = number_format($servicio['precio'], 0, ',', '.');
            $duracion = isset($servicio['duracion']) ? $servicio['duracion'] : 60;
            
            // Obtener ícono del servicio
            $icono = $iconos_servicios[$servicio['nombre']] ?? '✨';
            
            // Obtener imagen de fondo del servicio
            $fondo = $fondos_servicios[$servicio['nombre']] ?? $fondos_default[$index % count($fondos_default)] ?? 'img/bronceado 6.jpg';
            
            // DETERMINAR ARCHIVO DESTINO BASADO EN GRUPOS - CORREGIDO
            $nombre_servicio = $servicio['nombre'];
            if (in_array($nombre_servicio, ['Semipermanente', 'Kapping'])) {
                $archivo_destino = 'esculpidas';
            } elseif (in_array($nombre_servicio, ['Microshading'])) {
                $archivo_destino = 'microblading';
            } elseif ($nombre_servicio == 'Perfilado de Cejas') {
                $archivo_destino = 'laminado'; // CORREGIDO - va a laminado.php
            } else {
                $archivo_destino = strtolower(str_replace(' ', '-', $nombre_servicio));
            }
          ?>
          <div class="swiper-slide swiper-slide-grande">
            <!-- IMAGEN GRANDE Y BIEN VISIBLE -->
            <div class="slide-image-container">
              <img src="<?= $fondo ?>" alt="<?= htmlspecialchars($servicio['nombre']) ?>" class="slide-image">
            </div>
            <div class="slide-overlay">
              <div class="slide-content">
                <div class="slide-icon"><?= $icono ?></div>
                <h2 class="slide-title"><?= htmlspecialchars($servicio['nombre']) ?></h2>
                <p class="slide-description">
                  <?= htmlspecialchars($servicio['descripcion'] ?: 'Servicio profesional de alta calidad con resultados excepcionales.') ?>
                </p>
                <div class="slide-precio">$<?= $precio ?></div>
                <span class="slide-duracion">⏱️ <?= $duracion ?> minutos</span>
                <div class="slide-buttons">
                  <a href="servicios/<?= $archivo_destino ?>.php" 
                     class="slide-button detalles">
                    <i class="fas fa-info-circle"></i> Ver Detalles
                  </a>
                  <a href="calendario.php?servicio=<?= urlencode($servicio['nombre']) ?>" 
                     class="slide-button reservar">
                    <i class="fas fa-calendar-check"></i> Reservar Turno
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Navegación -->
    <div class="swiper-button-next swiper-button-next-grande"></div>
    <div class="swiper-button-prev swiper-button-prev-grande"></div>
    <div class="swiper-pagination swiper-pagination-grande"></div>
  </div>

  <!-- SECCIÓN INFORMATIVA -->
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
        delay: 6000,
        disableOnInteraction: false,
      },
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
      speed: 1200,
      pagination: {
        el: '.swiper-pagination-grande',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next-grande',
        prevEl: '.swiper-button-prev-grande',
      }
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