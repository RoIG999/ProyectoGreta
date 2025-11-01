<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faciales - GRETA</title>
    
    <!-- Fuentes e iconos -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500&display=swap" rel="stylesheet">
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
        }
        
        @media (max-width: 480px) {
            .swiper {
                width: 95%;
                height: 40vh;
                min-height: 250px;
                max-height: 300px;
            }
        }

        /* ===== Contenido ===== */
        .contenido-servicio {
            max-width: 800px;
            margin: 40px auto;
            text-align: center;
            font-size: 18px;
            line-height: 1.6;
            padding: 0 20px;
        }
        .contenido-servicio p {
            margin-bottom: 20px;
        }
        .boton {
            background: black;
            color: white;
            font-weight: bold;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            transition: background 0.3s, transform 0.2s;
            display: inline-block;
        }
        .boton:hover {
            background: #444;
            transform: scale(1.05);
        }

        footer {
            text-align: center;
            padding: 20px;
            background: #000;
            color: white;
            margin-top: 40px;
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

    <!-- Carrusel -->
    <div class="swiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="../img/faciales 2.jpg" alt="Faciales 2"></div>
            <div class="swiper-slide"><img src="../img/faciales 3.webp" alt="Faciales 3"></div>
            <div class="swiper-slide"><img src="../img/faciales 4 (2).jpg" alt="Faciales 4"></div>
            <div class="swiper-slide"><img src="../img/faciales 6.png" alt="Faciales 6"></div>
        </div>

        <!-- Botones -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>

    <!-- Contenido -->
    <section class="contenido-servicio">
        <h1>🌸 Tratamientos Faciales</h1>
        <p>
            Revitalizá tu piel con limpiezas profundas, exfoliaciones y tratamientos hidratantes de alto nivel. 
            Recuperá la frescura, suavidad y luminosidad para un cutis saludable y rejuvenecido.
        </p>
        <p><em>📍 GRETA – Spa para tu piel</em></p>

        <a href="../Calendario.php" class="boton">📅 Reservar este servicio</a>
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
        const swiper = new Swiper('.swiper', {
            loop: true,
            autoplay: { delay: 4000 },
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });
    </script>
</body>
</html>