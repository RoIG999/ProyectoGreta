<?php 
// Detectar la página actual
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nosotros - GRETA Estética</title>
  <link rel="stylesheet" href="css/estilos.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* RESET PARA ANCHO COMPLETO */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body, html {
      margin: 0;
      padding: 0;
      width: 100%;
      overflow-x: hidden;
    }

    /* Fondo suave para toda la página */
    body {
      font-family: 'Montserrat', sans-serif;
      background: linear-gradient(135deg, #f8f5f0 0%, #f0ebe0 50%, #e8e2d6 100%);
      min-height: 100vh;
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
    
    nav a {
      color: white;
      margin-left: 20px;
      text-decoration: none;
      font-weight: 500;
    }
    
    nav a:hover {
      color: #f0c0d0;
    }
    
    nav a.activo {
      border-bottom: 2px solid #f0c0d0;
    }

    /* ANIMACIONES */
    @keyframes fadeInPage {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInContent {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    body {
        animation: fadeInPage 0.8s ease-out forwards;
    }

    .main-content {
        animation: fadeInContent 1s ease-out 0.3s forwards;
        opacity: 0;
    }

    .animate-element {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.6s ease-out;
    }

    .animate-element.visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* HERO COMPACTO - ANCHO COMPLETO CORREGIDO */
        /* HERO COMPACTO - ANCHO COMPLETO FORZADO */
        /* HERO COMPACTO - SOBREESCRIBIENDO ESTILOS EXTERNOS */
    section.hero {
      background: url('img/hero.png') center/cover no-repeat !important;
      height: 40vh !important;
      min-height: 100px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      position: relative !important;
      text-align: center !important;
      color: #fff !important;
      background-attachment: fixed !important;
      width: 100% !important;
      max-width: none !important;
      margin: 0 !important;
      padding: 0 !important;
      left: 0 !important;
      right: 0 !important;
      transform: none !important;
      animation: none !important;
    }
    
    section.hero::after {
      content: "" !important;
      position: absolute !important;
      top: 0 !important;
      left: 0 !important;
      right: 0 !important;
      bottom: 0 !important;
      background: linear-gradient(135deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.4) 100%) !important;
      z-index: 1 !important;
    }
    
    section.hero h1 {
      position: relative !important;
      font-family: 'Playfair Display', serif !important;
      font-size: 2.8em !important;
      margin: 0 !important;
      z-index: 2 !important;
      text-shadow: 0 4px 8px rgba(0,0,0,0.5) !important;
      padding: 0 !important;
      background: none !important;
      border: none !important;
      border-radius: 0 !important;
      backdrop-filter: none !important;
    }

    /* CONTENIDO PRINCIPAL */
    .content-wrapper {
      background: rgba(255, 255, 255, 0.95);
      margin: 25px auto;
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
      max-width: 1100px;
      position: relative;
      overflow: hidden;
    }

    .content-wrapper::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: 
        radial-gradient(circle at 10% 20%, rgba(240, 192, 208, 0.1) 2px, transparent 2px),
        radial-gradient(circle at 90% 80%, rgba(240, 192, 208, 0.05) 1px, transparent 1px);
      background-size: 50px 50px, 30px 30px;
      background-position: 0 0, 25px 25px;
      pointer-events: none;
    }

    /* SOBRE NOSOTROS - TEXTO IZQUIERDA, IMAGEN DERECHA */
    .nosotros {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 35px;
      align-items: center;
      padding: 30px;
      position: relative;
      z-index: 1;
    }
    
    .nosotros-imagen {
      width: 100%;
      height: 320px;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,.12);
      transition: all 0.3s ease;
      order: 2;
    }
    
    .nosotros-imagen img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }
    
    .nosotros-imagen:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(0,0,0,.2);
    }
    
    .nosotros-imagen:hover img {
      transform: scale(1.08);
    }
    
    .nosotros-texto {
      order: 1;
    }
    
    .nosotros-texto h2 {
      font-family: 'Playfair Display', serif;
      font-size: 2em;
      margin-bottom: 20px;
      color: #2D3748;
      line-height: 1.3;
      position: relative;
      padding-bottom: 12px;
      transition: all 0.3s ease;
      cursor: default;
    }
    
    .nosotros-texto h2:hover {
      color: #f0c0d0;
      transform: translateX(8px);
    }
    
    .nosotros-texto h2::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #f0c0d0, #d4a5b5);
      transition: width 0.3s ease;
    }
    
    .nosotros-texto h2:hover::after {
      width: 100%;
    }
    
    /* TEXTO CONCISO E INTERACTIVO */
    .nosotros-texto p {
      font-size: 1em;
      line-height: 1.6;
      margin-bottom: 18px;
      color: #4A5568;
      position: relative;
      padding: 12px 15px;
      border-radius: 8px;
      border-left: 3px solid transparent;
      transition: all 0.3s ease;
      cursor: default;
      background: rgba(255, 255, 255, 0.7);
    }
    
    .nosotros-texto p:hover {
      border-left-color: #f0c0d0;
      background: rgba(240, 192, 208, 0.08);
      transform: translateX(8px);
      box-shadow: 0 3px 12px rgba(0,0,0,0.08);
    }
    
    .lista-interactiva {
      list-style: none;
      padding: 0;
      margin: 22px 0;
    }
    
    .lista-interactiva li {
      padding: 14px 18px;
      margin: 8px 0;
      background: rgba(255, 255, 255, 0.8);
      border-radius: 8px;
      border-left: 3px solid transparent;
      transition: all 0.3s ease;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      font-size: 0.95em;
    }
    
    .lista-interactiva li::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 0;
      background: linear-gradient(90deg, rgba(240, 192, 208, 0.1), transparent);
      transition: width 0.3s ease;
    }
    
    .lista-interactiva li:hover {
      border-left-color: #f0c0d0;
      background: rgba(255, 255, 255, 0.95);
      transform: translateX(10px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .lista-interactiva li:hover::before {
      width: 100%;
    }
    
    .lista-interactiva li i {
      color: #f0c0d0;
      margin-right: 10px;
      transition: all 0.3s ease;
      font-size: 1em;
    }
    
    .lista-interactiva li:hover i {
      transform: scale(1.2);
      color: #d4a5b5;
    }

    /* FRASE MÁS COMPACTA */
    .frase {
      text-align: center;
      font-size: 1.3em;
      font-style: italic;
      margin: 40px auto;
      max-width: 700px;
      font-family: 'Playfair Display', serif;
      color: #2D3748;
      background: rgba(255, 255, 255, 0.9);
      padding: 22px 30px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.06);
      border-left: 3px solid #f0c0d0;
      transition: all 0.3s ease;
      cursor: default;
      position: relative;
      overflow: hidden;
    }
    
    .frase::before {
      content: '"';
      position: absolute;
      top: 5px;
      left: 15px;
      font-size: 3em;
      color: rgba(240, 192, 208, 0.2);
      font-family: serif;
    }
    
    .frase:hover {
      transform: scale(1.02);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    footer {
      background: #000;
      color: #fff;
      text-align: center;
      padding: 20px;
      margin-top: 40px;
      font-size: .85em;
    }

    /* Responsive */
    @media (max-width: 900px) {
      .nosotros {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 25px;
        padding: 25px 20px;
      }
      
      .nosotros-imagen {
        height: 280px;
        order: 2;
      }
      
      .nosotros-texto {
        order: 1;
      }
      
      
      
      
    
      .frase {
        font-size: 1.1em;
        padding: 18px 22px;
        margin: 30px 20px;
      }
      
      .content-wrapper {
        margin: 15px;
        border-radius: 12px;
      }
    }

    @media (max-width: 480px) {
      .hero h1 {
        font-size: 2em;
      }
      
      .nosotros-texto h2 {
        font-size: 1.7em;
      }
      
      .nosotros-texto p {
        font-size: 0.9em;
        padding: 10px 12px;
      }
      
      .nosotros-imagen {
        height: 220px;
      }
      
      .lista-interactiva li {
        padding: 12px 15px;
        font-size: 0.9em;
      }
      
      .frase {
        font-size: 1em;
        padding: 15px 20px;
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


    <!-- HERO COMPACTO -->
  <div class="hero-container">
    <section class="hero">
      <h1 class="animate-element">Sobre GRETA</h1>
    </section>
  </div>

  <!-- CONTENIDO -->
  <div class="content-wrapper">
    <section class="nosotros main-content">
      <!-- TEXTO IZQUIERDA -->
      <div class="nosotros-texto">
        <h2 class="animate-element">Donde la belleza encuentra elegancia</h2>
        
        <p class="animate-element">
          En GRETA realzamos tu belleza natural con tratamientos personalizados y tecnología de vanguardia. 
          Cada experiencia está diseñada para que te sientas única y especial.
        </p>
        
        <ul class="lista-interactiva animate-element">
          <li><i class="fas fa-star"></i> <strong>Productos Premium:</strong> Marcas internacionales de primera calidad</li>
          <li><i class="fas fa-heart"></i> <strong>Atención Personal:</strong> Te escuchamos y entendemos</li>
          <li><i class="fas fa-award"></i> <strong>Expertos Certificados:</strong> Siempre actualizados en nuevas técnicas</li>
          <li><i class="fas fa-shield-alt"></i> <strong>Máxima Seguridad:</strong> Protocolos de bioseguridad avanzados</li>
        </ul>
      </div>
      
      <!-- IMAGEN DERECHA -->
      <div class="nosotros-imagen animate-element">
        <img src="img/estetica-interior.png" alt="Interior GRETA - Ambiente elegante y acogedor">
      </div>
    </section>
  </div>

  <!-- FRASE INSPIRADORA -->
  <div class="frase animate-element">"Tu belleza única merece cuidado excepcional"</div>

  <footer>
    <p>© 2025 GRETA Estética - Todos los derechos reservados</p>
  </footer>

  <script>
    // Animación escalonada para elementos
    document.addEventListener('DOMContentLoaded', function() {
        const animateElements = document.querySelectorAll('.animate-element');
        
        setTimeout(() => {
            animateElements.forEach((element, index) => {
                setTimeout(() => {
                    element.classList.add('visible');
                }, index * 150);
            });
        }, 400);
    });
  </script>

</body>
</html>