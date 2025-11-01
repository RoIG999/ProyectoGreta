<?php 
// Detectar la página actual
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - GRETA</title>
    <link rel="stylesheet" href="css/estilos.css">
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

        nav a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        nav a:hover {
            color: #f0c0d0;
            transform: translateY(-2px);
        }

        nav a.activo {
            border-bottom: 2px solid #f0c0d0;
        }

        nav a.activo::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #f0c0d0;
            animation: pulseBorder 2s infinite;
        }

        /* ANIMACIONES MEJORADAS */
        @keyframes fadeInPage {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInContent {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes floatElement {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes pulseBorder {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(240, 192, 208, 0.3); }
            50% { box-shadow: 0 0 30px rgba(240, 192, 208, 0.6); }
        }

        @keyframes slideInFromLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes slideInFromRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }

        body {
            animation: fadeInPage 1s ease-out forwards;
        }

        .main-content {
            animation: fadeInContent 1.2s ease-out 0.3s forwards;
            opacity: 0;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .animate-element {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .animate-element.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* CONTENIDO MEJORADO */
        .contacto-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .contacto-header h1 {
            font-size: 3.5rem;
            background: linear-gradient(135deg, #000, #f0c0d0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            animation: floatElement 3s ease-in-out infinite;
        }

        .contacto-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-bottom: 50px;
        }

        .info-contacto {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            animation: slideInFromLeft 1s ease-out 0.5s forwards;
            opacity: 0;
        }

        .mapa-container {
            animation: slideInFromRight 1s ease-out 0.7s forwards;
            opacity: 0;
            position: relative;
    z-index: 10;
    transform: translateZ(0);
        }
        .mapa-container iframe {
    position: relative;
    z-index: 2;
}

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-radius: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .info-item:hover {
            transform: translateX(10px);
            background: linear-gradient(135deg, #f0c0d0, #ffffff);
            animation: glow 2s infinite;
        }

        .info-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #000, #333);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.5rem;
            color: white;
            transition: all 0.3s ease;
        }

        .info-item:hover .info-icon {
            transform: scale(1.1) rotate(10deg);
            background: linear-gradient(135deg, #f0c0d0, #e91e63);
        }

        .info-text h3 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 1.2rem;
        }

        .info-text p {
            margin: 0;
            color: #666;
            font-size: 1.1rem;
        }

        .mapa-animate {
            width: 100%;
            height: 400px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            border: 3px solid transparent;
            transition: all 0.3s ease;
            animation: bounceIn 1.2s ease-out 1s forwards;
            opacity: 0;
            position: relative;
    z-index: 11;

        }

        .mapa-animate:hover {
            border-color: #f0c0d0;
            animation: glow 2s infinite;
     transform: scale(1.02) translateZ(0); /* Mantener el transform 3D */
    z-index: 12; /* Aumentar z-index en hover */

        }

        .boton-reserva {
            display: inline-block;
            background: linear-gradient(135deg, #000, #333);
            color: white;
            padding: 18px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            animation: floatElement 3s ease-in-out infinite;
            margin-top: 30px;
        }

        .boton-reserva:hover {
            background: linear-gradient(135deg, #f0c0d0, #e91e63);
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 35px rgba(240, 192, 208, 0.4);
            animation: none;
        }

        .redes-sociales {
            text-align: center;
            margin-top: 50px;
            animation: fadeInContent 1s ease-out 1.2s forwards;
            opacity: 0;
        }

        .redes-sociales h3 {
            margin-bottom: 20px;
            color: #333;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .social-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #000, #333);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            animation: floatElement 3s ease-in-out infinite;
        }

        .social-icon:hover {
            transform: scale(1.2) rotate(10deg);
            animation: none;
        }

        .social-icon.instagram:hover {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
        }

        .social-icon.whatsapp:hover {
            background: linear-gradient(135deg, #25D366, #128C7E);
        }

        .social-icon.facebook:hover {
            background: linear-gradient(135deg, #4267B2, #3b5998);
        }

        footer {
            text-align: center;
            padding: 30px;
            background: #000;
            color: white;
            margin-top: 60px;
            animation: fadeInContent 1s ease-out 1.5s forwards;
            opacity: 0;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .contacto-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .contacto-header h1 {
                font-size: 2.5rem;
            }
            
            .info-contacto {
                padding: 25px;
            }
            
            .mapa-animate {
                height: 300px;
            }
        }
        /* SOLUCIÓN DEFINITIVA PARA EL MAPA */
.mapa-wrapper {
    position: relative;
    z-index: 10;
    border-radius: 20px;
    overflow: hidden;
    transform: translateZ(0); /* Crea un nuevo stacking context */
}

.mapa-animate {
    position: relative;
    z-index: 5 !important;
    transform: translateZ(0);
    transition: all 0.3s ease !important;
}

.mapa-animate:hover {
    transform: scale(1.02) translateZ(0) !important;
    z-index: 15 !important;
    border-color: #f0c0d0 !important;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3) !important;
}

/* Asegurar que el header no interfiera */
header {
    z-index: 1000;
    position: relative;
}

/* Remover animaciones conflictivas del mapa */
.mapa-container {
    animation: slideInFromRight 1s ease-out 0.7s forwards;
    opacity: 0;
    position: relative;
    z-index: 1;
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
            <a href="Calendario.php">
                <i class="fas fa-calendar-check"></i> Reservar Turno
            </a>
            <a href="Login.php">
                <i class="fas fa-user"></i> Ingreso
            </a>
        </nav>
    </header>

    <section class="main-content">
        <div class="contacto-header">
            <h1 class="animate-element">✨ Contáctanos</h1>
            <p class="animate-element" style="font-size: 1.3rem; color: #666; max-width: 600px; margin: 0 auto;">
                Estamos aquí para hacer realidad tu belleza. Visítanos y descubre la experiencia GRETA.
            </p>
        </div>

        <div class="contacto-grid">
            <div class="info-contacto">
                <div class="info-item animate-element">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-text">
                        <h3>📍 Nuestra Ubicación</h3>
                        <p>Virgen de la Merced 2345, Córdoba</p>
                    </div>
                </div>

                <div class="info-item animate-element">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-text">
                        <h3>📞 Teléfono</h3>
                        <p>351 733-9043</p>
                    </div>
                </div>

                <div class="info-item animate-element">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-text">
                        <h3>📧 Email</h3>
                        <p>gretasaloncba@gmail.com</p>
                    </div>
                </div>

                <div class="info-item animate-element">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-text">
                        <h3>🕒 Horarios</h3>
                        <p>Lunes a viernes: 9:00hs - 20:00hs</p>
                        <p>Sábados: 9:00hs - 17:00hs</p>
                    </div>
                </div>
            </div>

      <div class="mapa-container">
    <div class="mapa-wrapper">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3406.2376858287853!2d-64.2154292!3d-31.380009099999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x943298e11bbc8c23%3A0x7bfa3cebe63d68e0!2sSalon%20Greta%20U%C3%B1as%20y%20Pesta%C3%B1as!5e0!3m2!1ses!2sar!4v1760480182699!5m2!1ses!2sar" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</div>
        </div>

        <div style="text-align: center;">
            <a href="Calendario.php" class="boton-reserva animate-element">
                <i class="fas fa-calendar-check"></i> 📅 Reservar Turno Ahora
            </a>
        </div>

        <div class="redes-sociales">
            <h3>Síguenos en Redes Sociales</h3>
            <div class="social-icons">
                <a href="https://instagram.com/gretasaloncba" class="social-icon instagram" target="_blank">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://wa.me/543517339043" class="social-icon whatsapp" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="#" class="social-icon facebook" target="_blank">
                    <i class="fab fa-facebook-f"></i>
                </a>
            </div>
        </div>
    </section>

    <footer>
        <p>© 2025 GRETA Estética - Todos los derechos reservados</p>
    </footer>

    <script>
        // Animación mejorada para elementos
        document.addEventListener('DOMContentLoaded', function() {
            const animateElements = document.querySelectorAll('.animate-element');
            
            // Animación escalonada
            setTimeout(() => {
                animateElements.forEach((element, index) => {
                    setTimeout(() => {
                        element.classList.add('visible');
                    }, index * 200);
                });
            }, 300);

            // Efecto de scroll para animaciones
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, observerOptions);

            // Observar todos los elementos animables
            document.querySelectorAll('.animate-element').forEach(el => {
                observer.observe(el);
            });

            // Efecto de sonido al hacer hover en botones (opcional)
            const buttons = document.querySelectorAll('.info-item, .boton-reserva, .social-icon');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transition = 'all 0.3s ease';
                });
            });
        });
    </script>

</body>
</html>