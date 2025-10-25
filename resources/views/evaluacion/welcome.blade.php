<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Evaluación Docente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-custom-dark { background-color: #201A2F; }
        .bg-custom-blue { background-color: #26BBFF; }
        .text-custom-blue { color: #26BBFF; }
        .border-custom-blue { border-color: #26BBFF; }
        
        /* Animaciones */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .float-animation { animation: float 3s ease-in-out infinite; }
        .fade-in-up { animation: fadeInUp 0.8s ease-out; }
        
        /* Efectos hover mejorados */
        .card-hover {
            transition: all 0.3s ease;
            transform-style: preserve-3d;
        }
        
        .card-hover:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(38, 187, 255, 0.2);
        }
        
        .btn-hover {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(38, 187, 255, 0.4);
        }
        
        .btn-hover::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-hover:hover::after {
            left: 100%;
        }
    </style>
</head>
<body class="bg-custom-dark min-h-screen flex items-center justify-center overflow-x-hidden">
    <!-- Fondo animado -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-10 -left-10 w-20 h-20 bg-custom-blue rounded-full opacity-10 float-animation"></div>
        <div class="absolute top-1/4 -right-10 w-16 h-16 bg-custom-blue rounded-full opacity-10 float-animation" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-1/3 left-1/4 w-12 h-12 bg-custom-blue rounded-full opacity-10 float-animation" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 right-1/4 w-24 h-24 bg-custom-blue rounded-full opacity-10 float-animation" style="animation-delay: 1.5s;"></div>
    </div>

    <div class="max-w-6xl mx-auto px-6 py-12 relative z-10">
        <!-- Header con animación -->
        <div class="text-center mb-16 fade-in-up">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-custom-blue rounded-full mb-6 float-animation">
                <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
            </div>
            
            <h1 class="text-6xl font-bold text-white mb-6 leading-tight">
                Bienvenido al <span class="text-custom-blue bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">Módulo de Evaluación</span>
            </h1>
            
            <p class="text-gray-300 text-xl max-w-3xl mx-auto leading-relaxed">
                Sistema especializado para la <span class="text-custom-blue font-semibold">evaluación del desempeño docente</span> mediante encuestas estudiantiles confidenciales y constructivas
            </p>
        </div>

        <!-- Cards Section con hover effects -->
        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <!-- Card 1 -->
            <div class="card-hover bg-[#111115] p-8 rounded-2xl border border-gray-800 hover:border-custom-blue relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-20 h-20 bg-custom-blue rounded-full opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <i class="fas fa-user-shield text-white text-2xl"></i>
                    </div>
                    <h3 class="text-white text-2xl font-bold mb-4">Evaluaciones Anónimas</h3>
                    <p class="text-gray-400 leading-relaxed">Los estudiantes evalúan de forma 100% confidencial y segura, garantizando total transparencia</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="card-hover bg-[#111115] p-8 rounded-2xl border border-gray-800 hover:border-custom-blue relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-20 h-20 bg-custom-blue rounded-full opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-white text-2xl font-bold mb-4">Reportes Detallados</h3>
                    <p class="text-gray-400 leading-relaxed">Los instructores reciben feedback específico y constructivo para su desarrollo profesional</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="card-hover bg-[#111115] p-8 rounded-2xl border border-gray-800 hover:border-custom-blue relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-20 h-20 bg-custom-blue rounded-full opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <i class="fas fa-rocket text-white text-2xl"></i>
                    </div>
                    <h3 class="text-white text-2xl font-bold mb-4">Mejora Continua</h3>
                    <p class="text-gray-400 leading-relaxed">Herramienta esencial para el crecimiento y desarrollo profesional docente institucional</p>
                </div>
            </div>
        </div>

        <!-- CTA Button Section CORREGIDA -->
        <div class="text-center fade-in-up" style="animation-delay: 0.5s;">
            <a href="{{ route('evaluacion.login') }}" 
               class="btn-hover inline-flex items-center px-12 py-5 bg-gradient-to-r from-blue-500 to-cyan-400 text-white font-bold rounded-2xl text-xl shadow-2xl">
                <i class="fas fa-play-circle mr-3 text-xl"></i>
                <span>Empecemos</span>
                <i class="fas fa-arrow-right ml-3 text-lg"></i>
            </a>
            
            <!-- Info adicional -->
            <div class="mt-8 flex justify-center items-center space-x-8 text-gray-400">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-shield-alt text-custom-blue"></i>
                    <span class="text-sm">100% Seguro</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clock text-custom-blue"></i>
                    <span class="text-sm">5-10 Minutos</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-chart-pie text-custom-blue"></i>
                    <span class="text-sm">Resultados Inmediatos</span>
                </div>
            </div>
        </div>

        <!-- Stats Grid Mejorado -->
        <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="text-white p-6 rounded-xl bg-[#111115] border border-gray-800 hover:border-custom-blue transition-all duration-300">
                <i class="fas fa-laptop text-custom-blue text-3xl mb-3"></i>
                <div class="text-3xl font-bold text-custom-blue">100%</div>
                <div class="text-gray-400 text-sm mt-2">Virtual</div>
            </div>
            <div class="text-white p-6 rounded-xl bg-[#111115] border border-gray-800 hover:border-custom-blue transition-all duration-300">
                <i class="fas fa-star text-custom-blue text-3xl mb-3"></i>
                <div class="text-3xl font-bold text-custom-blue">Escala</div>
                <div class="text-gray-400 text-sm mt-2">1-5 Puntos</div>
            </div>
            <div class="text-white p-6 rounded-xl bg-[#111115] border border-gray-800 hover:border-custom-blue transition-all duration-300">
                <i class="fas fa-list-alt text-custom-blue text-3xl mb-3"></i>
                <div class="text-3xl font-bold text-custom-blue">Múltiples</div>
                <div class="text-gray-400 text-sm mt-2">Criterios</div>
            </div>
            <div class="text-white p-6 rounded-xl bg-[#111115] border border-gray-800 hover:border-custom-blue transition-all duration-300">
                <i class="fas fa-bolt text-custom-blue text-3xl mb-3"></i>
                <div class="text-3xl font-bold text-custom-blue">Resultados</div>
                <div class="text-gray-400 text-sm mt-2">En Tiempo Real</div>
            </div>
        </div>
    </div>

    <!-- Footer Mejorado -->
    <div class="fixed bottom-0 left-0 right-0 text-center py-4 bg-[#111115] border-t border-gray-800">
        <div class="flex justify-center items-center space-x-6 text-gray-500 text-sm">
            <span>© 2024 Instituto de Capacitación Virtual</span>
            <span class="text-custom-blue">•</span>
            <span>Todos los derechos reservados</span>
            <span class="text-custom-blue">•</span>
            <span class="flex items-center">
                <i class="fas fa-heart text-red-500 mr-1"></i>
                Hecho con pasión
            </span>
        </div>
    </div>

    <script>
        // Efectos de scroll suave
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de elementos al hacer scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observar elementos para animación
            document.querySelectorAll('.card-hover').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>