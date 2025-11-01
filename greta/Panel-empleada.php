<?php
// panel-empleada.php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Normalizar rol: minúsculas y sin tildes
$rol = $_SESSION['usuario_rol'] ?? '';
$rol = mb_strtolower($rol, 'UTF-8');
$rol = strtr($rol, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n']);

// Solo empleados pueden acceder
if ($rol !== 'empleado' && $rol !== 'empleada') {
    header('Location: login.php?e=perm');
    exit;
}

$nombre = htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Empleada', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Empleada - GRETA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --empleada-primary: #7B5BA8;
            --empleada-secondary: #9F86C4;
            --empleada-accent: #E4C6FA;
            --empleada-light: #F8F5FC;
            --empleada-dark: #4E3A6B;
            --empleada-success: #8DD7BF;
            --empleada-warning: #FFD166;
            --empleada-text: #2D3748;
            --empleada-gray: #F0EBF5;
            --empleada-border: #E2E8F0;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--empleada-text);
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            min-height: 100vh;
            border-radius: 0;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.1);
        }

        /* Navbar estilo único */
        .navbar-empleada {
            background: linear-gradient(135deg, var(--empleada-primary) 0%, var(--empleada-dark) 100%);
            box-shadow: 0 4px 20px rgba(123, 91, 168, 0.3);
            padding: 1rem 0;
        }

        .navbar-brand-empleada {
            font-weight: 700;
            font-size: 1.4rem;
            color: white !important;
        }

        .navbar-brand-empleada img {
            height: 40px;
            width: auto;
            margin-right: 12px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Header de bienvenida */
        .welcome-header {
            background: linear-gradient(135deg, var(--empleada-accent) 0%, var(--empleada-light) 100%);
            border-radius: 20px;
            padding: 2.5rem;
            margin: 2rem 0;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--empleada-border);
        }

        .welcome-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.3;
        }

        .welcome-icon {
            font-size: 4rem;
            color: var(--empleada-primary);
            filter: drop-shadow(0 4px 8px rgba(123, 91, 168, 0.3));
        }

        .welcome-text h1 {
            font-weight: 700;
            color: var(--empleada-dark);
            margin-bottom: 0.5rem;
            font-size: 2.2rem;
        }

        .welcome-text .lead {
            color: var(--empleada-primary);
            font-weight: 500;
            font-size: 1.2rem;
        }

        /* Tarjeta de información */
        .info-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--empleada-border);
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .info-card h5 {
            color: var(--empleada-dark);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-card h5 i {
            color: var(--empleada-primary);
        }

        /* Calendario container */
        .calendar-container-empleada {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            margin-bottom: 2rem;
            border: 1px solid var(--empleada-border);
        }

        .calendar-header-empleada {
            background: linear-gradient(135deg, var(--empleada-primary) 0%, var(--empleada-secondary) 100%);
            color: white;
            padding: 1.5rem 2rem;
        }

        .calendar-header-empleada h3 {
            font-weight: 700;
            margin: 0;
            font-size: 1.5rem;
        }

        .calendar-frame-container {
            height: 600px;
            background: white;
        }

        #calendar-frame {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Footer */
        .footer-empleada {
            background: var(--empleada-dark);
            color: white;
            padding: 1.5rem 0;
            margin-top: 3rem;
        }

        /* Botones personalizados */
        .btn-empleada {
            background: linear-gradient(135deg, var(--empleada-primary) 0%, var(--empleada-secondary) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(123, 91, 168, 0.3);
        }

        .btn-empleada:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(123, 91, 168, 0.4);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .welcome-header {
                padding: 1.5rem;
                margin: 1rem 0;
            }
            
            .welcome-text h1 {
                font-size: 1.8rem;
            }
            
            .welcome-icon {
                font-size: 3rem;
            }
            
            .calendar-frame-container {
                height: 400px;
            }
            
            .calendar-header-empleada {
                padding: 1rem 1.5rem;
            }
        }

        /* Animaciones */
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

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-empleada">
            <div class="container">
                <a class="navbar-brand navbar-brand-empleada" href="#">
                    <img src="img/LogoGreta.jpeg" alt="GRETA">
                    GRETA · Empleada
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarEmpleada">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarEmpleada">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="logout.php">
                                <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Contenido principal -->
        <div class="container py-4">
            <!-- Header de bienvenida -->
            <div class="welcome-header fade-in-up">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="welcome-text">
                            <h1>¡Hola, <?= $nombre ?>! ✨</h1>
                            <p class="lead">Bienvenida a tu espacio de trabajo en GRETA</p>
                            <p class="mb-0">Gestiona tu agenda y atiende a tus clientes de manera eficiente.</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="bi bi-emoji-smile welcome-icon"></i>
                    </div>
                </div>
            </div>

            <!-- Información rápida -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="info-card fade-in-up" style="animation-delay: 0.1s">
                        <h5><i class="bi bi-info-circle"></i> Horario Actual</h5>
                        <p class="mb-0">Eres una profesional de la estética en GRETA. Tu dedicación y atención al cliente son fundamentales para nuestro éxito.</p>
                    </div>
             </div>
                
                  
                        
            
                    
            </div>

            <!-- Calendario -->
            <div class="calendar-container-empleada fade-in-up" style="animation-delay: 0.3s">
                <div class="calendar-header-empleada">
                    <h3><i class="bi bi-calendar-week me-2"></i>Mi Agenda</h3>
                </div>
                <div class="calendar-frame-container">
                    <iframe id="calendar-frame" src="calendario.php" frameborder="0"></iframe>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="row">
                <div class="col-12">
                    <div class="info-card fade-in-up" style="animation-delay: 0.4s">
                        <h5><i class="bi bi-lightbulb"></i> Recordatorio Importante</h5>
                        <p class="mb-0">Recuerda confirmar los turnos con al menos 24 horas de anticipación y mantener tu disponibilidad actualizada en el sistema.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer-empleada text-center">
            <div class="container">
                <small>© <?= date('Y'); ?> GRETA Estética · Panel de Empleada</small>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Efectos de animación al cargar
        document.addEventListener('DOMContentLoaded', function() {
            // Agregar clase de animación a los elementos
            const elements = document.querySelectorAll('.fade-in-up');
            elements.forEach((element, index) => {
                element.style.animationDelay = `${index * 0.1}s`;
            });

            // Actualizar hora en tiempo real
            function actualizarHora() {
                const ahora = new Date();
                const opciones = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };
                const fechaCompleta = ahora.toLocaleDateString('es-ES', opciones);
                
                const elementoHora = document.querySelector('.info-card:last-child p');
                if (elementoHora) {
                    elementoHora.innerHTML = `<strong>${fechaCompleta}</strong>`;
                }
            }

            // Actualizar cada segundo
            setInterval(actualizarHora, 1000);
            actualizarHora();

            // Efecto de carga suave para el iframe
            const calendarFrame = document.getElementById('calendar-frame');
            calendarFrame.onload = function() {
                calendarFrame.style.opacity = '1';
            };
        });

        // Función para recargar el calendario si es necesario
        function recargarCalendario() {
            const calendarFrame = document.getElementById('calendar-frame');
            calendarFrame.contentWindow.location.reload();
        }
    </script>
</body>
</html>