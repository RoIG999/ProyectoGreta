<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laminado de Cejas - GRETA</title>
  
  <!-- Fuentes e iconos -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <style>
    /* ESTILOS BARRA DE NAVEGACIÓN */
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

    /* BOTONES FLOTANTES - RESTAURADOS COMO ESTABAN */
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

    /* ===== CARRUSEL ===== */
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
      background: #000;
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
      object-fit: cover;
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

    /* ===== DISEÑO MEJORADO Y MÁS INTERACTIVO ===== */
    .servicio-container {
      max-width: 1200px;
      margin: 60px auto;
      padding: 0 20px;
    }
    
    .servicio-header {
      text-align: center;
      margin-bottom: 50px;
    }
    
    .servicio-header h1 {
      font-size: 3.5rem;
      background: linear-gradient(135deg, #000, #f0c0d0);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 20px;
      animation: float 3s ease-in-out infinite;
    }
    
    .servicio-header p {
      font-size: 1.3rem;
      color: #666;
      max-width: 700px;
      margin: 0 auto;
      line-height: 1.6;
      font-weight: 500;
    }

    /* ===== GRID INTERACTIVO MEJORADO ===== */
    .servicio-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 30px;
      margin: 60px 0;
    }
    
    .feature-card {
      background: white;
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      text-align: center;
      transition: all 0.4s ease;
      position: relative;
      overflow: hidden;
      border: 2px solid transparent;
    }
    
    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
      border-color: #f0c0d0;
    }
    
    .feature-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(240, 192, 208, 0.1), transparent);
      transition: left 0.6s ease;
    }
    
    .feature-card:hover::before {
      left: 100%;
    }
    
    .feature-icon {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, #000, #333);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 25px;
      font-size: 2rem;
      color: white;
      transition: all 0.3s ease;
    }
    
    .feature-card:hover .feature-icon {
      background: linear-gradient(135deg, #f0c0d0, #e91e63);
      transform: scale(1.1) rotate(5deg);
    }
    
    .feature-card h3 {
      font-size: 1.5rem;
      margin-bottom: 15px;
      color: #333;
    }
    
    .feature-card p {
      color: #666;
      line-height: 1.6;
      margin: 0;
    }

    /* ===== ACORDEÓN INTERACTIVO ===== */
    .acordeon-container {
      max-width: 800px;
      margin: 60px auto;
    }
    
    .acordeon-item {
      background: white;
      margin-bottom: 15px;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }
    
    .acordeon-item:hover {
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .acordeon-header {
      padding: 25px 30px;
      background: linear-gradient(135deg, #000, #333);
      color: white;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: all 0.3s ease;
    }
    
    .acordeon-header:hover {
      background: linear-gradient(135deg, #f0c0d0, #e91e63);
    }
    
    .acordeon-header h3 {
      margin: 0;
      font-size: 1.3rem;
      display: flex;
      align-items: center;
      gap: 15px;
    }
    
    .acordeon-icon {
      font-size: 1.5rem;
      transition: transform 0.3s ease;
    }
    
    .acordeon-content {
      padding: 0;
      max-height: 0;
      overflow: hidden;
      transition: all 0.4s ease;
      background: white;
    }
    
    .acordeon-item.active .acordeon-content {
      padding: 30px;
      max-height: 500px;
    }
    
    .acordeon-item.active .acordeon-icon {
      transform: rotate(180deg);
    }
    
    .acordeon-content p {
      color: #666;
      line-height: 1.6;
      margin: 0;
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

    /* ===== CTA FINAL ELEGANTE ===== */
    .cta-final {
      text-align: center;
      margin: 80px 0 40px;
      padding: 60px 40px;
      background: linear-gradient(135deg, #000, #333);
      color: white;
      border-radius: 25px;
      position: relative;
      overflow: hidden;
    }
    
    .cta-final::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(240, 192, 208, 0.1) 0%, transparent 70%);
      animation: rotate 20s linear infinite;
    }
    
    @keyframes rotate {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .cta-content {
      position: relative;
      z-index: 2;
    }
    
    .cta-final h2 {
      font-size: 2.8rem;
      margin-bottom: 20px;
      background: linear-gradient(135deg, #f0c0d0, #ffffff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .cta-final p {
      font-size: 1.3rem;
      margin-bottom: 30px;
      opacity: 0.9;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
      line-height: 1.6;
    }

    .precio-destacado {
      font-size: 2.5rem;
      font-weight: bold;
      color: #f0c0d0;
      margin: 20px 0;
      text-shadow: 0 2px 10px rgba(240, 192, 208, 0.3);
    }

    .boton-reserva {
      background: linear-gradient(135deg, #f0c0d0, #e91e63);
      color: white;
      padding: 15px 30px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: bold;
      display: inline-block;
      margin-top: 20px;
      transition: all 0.3s ease;
    }

    .boton-reserva:hover {
      transform: translateY(-3px) scale(1.05);
      box-shadow: 0 10px 25px rgba(240, 192, 208, 0.4);
    }

    .duracion-info {
      font-size: 1.1rem;
      color: #f0c0d0;
      margin: 10px 0;
    }

    /* ===== Media Queries ===== */
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
      
      .servicio-header h1 {
        font-size: 2.5rem;
      }
      
      .servicio-header p {
        font-size: 1.1rem;
      }
      
      .servicio-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }
      
      .feature-card {
        padding: 30px 20px;
      }
      
      .reserva-fija {
        bottom: 20px;
        right: 20px;
      }
      
      .reserva-fija .boton {
        padding: 15px 25px;
        font-size: 1rem;
      }
      
      .cta-final {
        padding: 40px 20px;
        margin: 60px 0 30px;
      }
      
      .cta-final h2 {
        font-size: 2.2rem;
      }
      
      .cta-final p {
        font-size: 1.1rem;
      }
      
      .precio-destacado {
        font-size: 2rem;
      }
    }
    
    @media (max-width: 480px) {
      .swiper {
        width: 95%;
        height: 40vh;
        min-height: 250px;
        max-height: 300px;
      }
      
      .servicio-header h1 {
        font-size: 2rem;
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

  <!-- Header -->
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
      <div class="swiper-slide"><img src="../img/laminado 2.jpg" alt="Laminado de cejas profesional"></div>
      <div class="swiper-slide"><img src="../img/laminado 3.webp" alt="Resultado laminado de cejas"></div>
      <div class="swiper-slide"><img src="../img/laminado 4.webp" alt="Cejas perfectamente definidas"></div>
      <div class="swiper-slide"><img src="../img/laminado 5.webp" alt="Look natural con laminado"></div>
    </div>

    <!-- Botones -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
  </div>

  <!-- Sección de Servicio Mejorada -->
  <section class="servicio-container">
    <div class="servicio-header">
      <h1>✨ Laminado de Cejas</h1>
      <p>Transforma tus cejas en obras de arte perfectamente definidas. Nuestro laminado profesional peina, fija y realza el volumen natural de tus cejas para un look impecable que dura semanas.</p>
    </div>

    <!-- Grid de Características Interactivas -->
    <div class="servicio-grid">
      <div class="feature-card">
        <div class="feature-icon">💎</div>
        <h3>Definición Perfecta</h3>
        <p>Cejas perfectamente peinadas y estructuradas que enmarcan tu rostro con elegancia y sofisticación.</p>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">⏱️</div>
        <h3>Duración Prolongada</h3>
        <p>Disfruta de cejas perfectas durante 4 a 6 semanas, olvidándote del gel fijador todos los días.</p>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">🌟</div>
        <h3>Brillo Natural</h3>
        <p>Acabado sedoso y brillante que realza la belleza natural de tus cejas sin aspecto artificial.</p>
      </div>
    </div>

    <!-- Acordeón Interactivo -->
    <div class="acordeon-container">
      <div class="acordeon-item">
        <div class="acordeon-header">
          <h3><i class="fas fa-magic"></i> ¿En qué consiste el laminado de cejas?</h3>
          <span class="acordeon-icon"><i class="fas fa-chevron-down"></i></span>
        </div>
        <div class="acordeon-content">
          <p>El laminado de cejas es un tratamiento de belleza semipermanente que redefine la forma y dirección de los vellos de las cejas. A través de productos especializados, se peinan y fijan los vellos en la dirección deseada, creando un efecto de cejas más llenas, definidas y perfectamente estructuradas.</p>
        </div>
      </div>
      
      <div class="acordeon-item">
        <div class="acordeon-header">
          <h3><i class="fas fa-clock"></i> ¿Cuánto dura el procedimiento y los resultados?</h3>
          <span class="acordeon-icon"><i class="fas fa-chevron-down"></i></span>
        </div>
        <div class="acordeon-content">
          <p>El procedimiento completo dura aproximadamente 45-60 minutos. Los resultados son inmediatos y se mantienen entre 4 y 6 semanas, dependiendo del ciclo de crecimiento natural de tus cejas. Es perfecto para quienes buscan cejas perfectas sin maquillaje diario.</p>
        </div>
      </div>
      
      <div class="acordeon-item">
        <div class="acordeon-header">
          <h3><i class="fas fa-heart"></i> ¿Es adecuado para todo tipo de cejas?</h3>
          <span class="acordeon-icon"><i class="fas fa-chevron-down"></i></span>
        </div>
        <div class="acordeon-content">
          <p>¡Absolutamente! El laminado funciona excelente en todo tipo de cejas: cejas escasas, rebeldes, gruesas o finas. El tratamiento se personaliza según las características de tus cejas, realzando su forma natural y corrigiendo direcciones no deseadas.</p>
        </div>
      </div>
      
      <div class="acordeon-item">
        <div class="acordeon-header">
          <h3><i class="fas fa-star"></i> ¿Qué incluye la sesión completa?</h3>
          <span class="acordeon-icon"><i class="fas fa-chevron-down"></i></span>
        </div>
        <div class="acordeon-content">
          <p>Cada sesión incluye: análisis personalizado de tus cejas, diseño de forma ideal, proceso de laminado con productos premium, fijación profesional, nutrición intensiva, y recomendaciones específicas para el cuidado posterior. El resultado son cejas perfectas que enmarcan tu mirada.</p>
        </div>
      </div>
    </div>

    <!-- CTA Final Elegante -->
    <div class="cta-final">
      <div class="cta-content">
        <h2>¿Lista para Cejas de Impacto?</h2>
        <p>Despierta cada mañana con cejas perfectamente definidas. Olvida el gel y el maquillaje, y disfruta de un look impecable que dura semanas.</p>
        
        <!-- PRECIO DESDE BASE DE DATOS -->
        <?php
        // Conexión a la base de datos
        $servername = "127.0.0.1";
        $username = "root"; // Cambiar por tu usuario de BD
        $password = ""; // Cambiar por tu contraseña de BD
        $dbname = "abmgreta";

        // Crear conexión
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar conexión
        if ($conn->connect_error) {
            // En caso de error, usar valor por defecto
            $precio_laminado = "$27.500";
            $duracion_laminado = "60 minutos";
        } else {
            // Consulta para obtener precio y duración del laminado
            $sql = "SELECT precio, duracion FROM servicio WHERE nombre = 'Perfilado de Cejas' AND estado = 1";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $precio = $row['precio'];
                $duracion = $row['duracion'];
                
                // Formatear el precio como moneda
                $precio_laminado = "$" . number_format($precio, 0, ',', '.');
                $duracion_laminado = $duracion . " minutos";
            } else {
                // Valores por defecto si no encuentra el servicio
                $precio_laminado = "$27.500";
                $duracion_laminado = "60 minutos";
            }
            
            // Cerrar conexión
            $conn->close();
        }
        ?>
        
        <div class="precio-destacado"><?php echo $precio_laminado; ?></div>
        <div class="duracion-info">⏰ Duración: <?php echo $duracion_laminado; ?></div>
        <p><em>Incluye laminado profesional + diseño personalizado</em></p>
        
        <!-- Botón de reserva adicional -->
        <a href="../Calendario.php" class="boton-reserva">
          <i class="fas fa-calendar-plus"></i> Reservar Mi Sesión
        </a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>© 2025 GRETA Estética - Todos los derechos reservados</p>
  </footer>

  <!-- BOTONES FLOTANTES DE REDES SOCIALES -->
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

    // Acordeón Interactivo
    document.querySelectorAll('.acordeon-header').forEach(header => {
      header.addEventListener('click', () => {
        const item = header.parentElement;
        const isActive = item.classList.contains('active');
        
        // Cerrar todos los acordeones
        document.querySelectorAll('.acordeon-item').forEach(acc => {
          acc.classList.remove('active');
        });
        
        // Abrir el actual si no estaba activo
        if (!isActive) {
          item.classList.add('active');
        }
      });
    });

    // Animación de entrada para elementos
    document.addEventListener('DOMContentLoaded', function() {
      const elementos = document.querySelectorAll('.feature-card, .acordeon-item');
      
      elementos.forEach((elemento, index) => {
        elemento.style.opacity = '0';
        elemento.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
          elemento.style.transition = 'all 0.6s ease';
          elemento.style.opacity = '1';
          elemento.style.transform = 'translateY(0)';
        }, 300 + (index * 100));
      });
    });
  </script>
</body>
</html>