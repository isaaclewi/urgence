<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - CongoAssist</title>
    <link rel="icon" href="medias/Clogo.jpg" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .animate-fade-in {
            animation: fadeIn 1s ease-out;
        }

        .animate-slide-in {
            animation: slideIn 0.8s ease-out;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #10b981 100%);
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .service-card {
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .service-card:hover::before {
            left: 100%;
        }

        .nav-link {
            position: relative;
            transition: color 0.3s;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 3px;
            background: #10b981;
            transition: width 0.3s;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* RESPONSIVE GLOBAL FIX */
        html,
        body {
            overflow-x: hidden;
        }

        /* Images responsive */
        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        /* Container padding mobile */
        @media (max-width: 640px) {
            .container {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
        }

        /* HERO TEXT ADAPTATION */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem !important;
                line-height: 1.3 !important;
            }

            h2 {
                font-size: 1.8rem !important;
            }

            p {
                font-size: 1rem !important;
            }
        }

        /* BUTTON FULL WIDTH ON MOBILE */
        @media (max-width: 640px) {
            button {
                width: 100%;
            }
        }

        /* GRID FIX MOBILE */
        @media (max-width: 768px) {
            .grid {
                gap: 1.5rem !important;
            }
        }

        /* CARD FIX */
        .service-card {
            height: 100%;
        }

        /* HEADER MOBILE FIX */
        @media (max-width: 768px) {
            header nav {
                flex-wrap: wrap;
            }
        }

        /* FOOTER STACK */
        @media (max-width: 768px) {
            footer .grid {
                grid-template-columns: 1fr !important;
                text-align: center;
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-green-50">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">

                <!-- LOGO + NOM -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-lg sm:text-xl">C</span>
                    </div>

                    <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-blue-900">
                        <span class="sm:hidden">Congo</span>
                        <span class="hidden sm:inline">Congo<span class="text-green-500">Assist</span></span>
                    </h1>
                </div>

                <!-- NAV DESKTOP -->
                <div class="hidden md:flex items-center gap-6">
                    <!-- ton menu existant ici -->
                </div>

                <!-- ACTIONS -->
                <div class="flex items-center gap-2">

                    <!-- BOUTON LOGIN (RESPONSIVE) -->
                    <button onclick="window.location.href='{{ route('services.login') }}'"
                        class="hidden sm:block bg-gradient-to-r from-blue-600 to-green-500 text-white px-4 py-2 md:px-6 md:py-3 rounded-xl font-semibold text-sm md:text-base">
                        Connexion
                    </button>

                    <!-- MENU MOBILE -->
                    <button id="mobile-menu-btn" class="md:hidden text-gray-700">
                        <i data-feather="menu" class="w-6 h-6"></i>
                    </button>
                </div>

            </div>
        </nav>
        </nav>

        <!-- MENU MOBILE -->
        <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg px-6 py-4">
            <a href="#medical" class="block py-2">Médicale</a>
            <a href="#police" class="block py-2">Police</a>
            <a href="#vaccination" class="block py-2">Vaccination</a>
            <a href="#urbaine" class="block py-2">Urbaine</a>
            <a href="#electricite" class="block py-2">Électricité</a>

            <!-- LOGIN MOBILE -->
            <button onclick="window.location.href='{{ route('services.login') }}'"
                class="mt-4 w-full bg-gradient-to-r from-blue-600 to-green-500 text-white px-4 py-3 rounded-xl font-semibold">
                Se connecter
            </button>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-gradient text-white py-20 animate-fade-in">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="animate-slide-in">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                        Votre Partenaire de <span class="text-green-400">Confiance</span> en Situations d'Urgence
                    </h1>
                    <p class="text-xl text-blue-100 mb-8">
                        CongoAssist connecte les citoyens aux services d'urgence essentiels pour sauver des vies et
                        améliorer le quotidien.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button class="w-full sm:w-auto bg-white text-blue-900 px-8 py-4 rounded-xl font-bold">
                            Commencer maintenant
                        </button>
                        <button
                            class="border-2 border-white text-white px-8 py-4 rounded-xl font-bold hover:bg-white hover:text-blue-900 transition">
                            En savoir plus
                        </button>
                    </div>
                </div>
                <div class="animate-float hidden md:block">
                    <img src="../medias/UE.jpeg" class="w-full h-auto" alt="Urgence" class="rounded-2xl shadow-2xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="medical" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Nos Services d'Urgence</h2>
                <p class="text-xl text-gray-600">Des solutions rapides et efficaces pour chaque situation</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Urgence Médicale -->
                <div class="service-card bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-8 card-hover">
                    <div class="bg-red-500 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <i data-feather="heart" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Urgence Médicale</h3>
                    <p class="text-gray-600 mb-6">
                        Accès rapide aux services médicaux d'urgence. Localisez les hôpitaux les plus proches et
                        signalez une urgence en temps réel.
                    </p>
                    <button
                        class="bg-red-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-600 transition">
                        En savoir plus →
                    </button>
                </div>

                <!-- Police -->
                <div id="police"
                    class="service-card bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 card-hover">
                    <div class="bg-blue-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <i data-feather="shield" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Urgence Sécurité</h3>
                    <p class="text-gray-600 mb-6">
                        Signalez les incidents de sécurité et collaborez avec les forces de l'ordre pour un
                        environnement plus sûr.
                    </p>
                    <button
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                        En savoir plus →
                    </button>
                </div>

                <!-- Vaccination -->
                <div id="vaccination"
                    class="service-card bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 card-hover">
                    <div class="bg-purple-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <i data-feather="shield-check" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Vaccination</h3>
                    <p class="text-gray-600 mb-6">
                        Suivez le calendrier vaccinal de votre famille et accédez aux programmes de vaccination.
                    </p>
                    <button
                        class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        En savoir plus →
                    </button>
                </div>

                <!-- Urgence Urbaine -->
                <div id="urbaine"
                    class="service-card bg-gradient-to-br from-orange-50 to-yellow-50 rounded-2xl p-8 card-hover">
                    <div class="bg-orange-500 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <i data-feather="alert-triangle" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Urgence Urbaine</h3>
                    <p class="text-gray-600 mb-6">
                        Signalez les problèmes d'infrastructure urbaine pour améliorer votre cadre de vie.
                    </p>
                    <button
                        class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition">
                        En savoir plus →
                    </button>
                </div>

                <!-- Électricité -->
                <div id="electricite"
                    class="service-card bg-gradient-to-br from-yellow-50 to-green-50 rounded-2xl p-8 card-hover">
                    <div class="bg-yellow-500 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <i data-feather="zap" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Urgence Électricité</h3>
                    <p class="text-gray-600 mb-6">
                        Signalez les pannes électriques et suivez les réparations en temps réel.
                    </p>
                    <button
                        class="bg-yellow-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-yellow-600 transition">
                        En savoir plus →
                    </button>
                </div>

                <!-- Informations -->
                <div class="service-card bg-gradient-to-br from-green-50 to-teal-50 rounded-2xl p-8 card-hover">
                    <div class="bg-green-500 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <i data-feather="info" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Actualités & Alertes</h3>
                    <p class="text-gray-600 mb-6">
                        Restez informé des dernières actualités santé et des alertes importantes.
                    </p>
                    <button
                        class="bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition">
                        En savoir plus →
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-green-500 text-white">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div class="animate-fade-in">
                    <div class="text-5xl font-bold mb-2">24/7</div>
                    <div class="text-blue-100">Service disponible</div>
                </div>
                <div class="animate-fade-in">
                    <div class="text-5xl font-bold mb-2">10K+</div>
                    <div class="text-blue-100">Utilisateurs actifs</div>
                </div>
                <div class="animate-fade-in">
                    <div class="text-5xl font-bold mb-2">5</div>
                    <div class="text-blue-100">Services d'urgence</div>
                </div>
                <div class="animate-fade-in">
                    <div class="text-5xl font-bold mb-2">95%</div>
                    <div class="text-blue-100">Satisfaction client</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold text-gray-900 mb-6">Prêt à commencer ?</h2>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                Rejoignez des milliers d'utilisateurs qui font confiance à CongoAssist pour leur sécurité et leur
                bien-être.
            </p>
            <button onclick="window.location.href='{{ route('services.login') }}'"
                class="bg-gradient-to-r from-blue-600 to-green-500 text-white px-12 py-4 rounded-xl font-bold text-lg hover:shadow-2xl transition card-hover">
                Créer un compte gratuitement
            </button>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">C</span>
                        </div>
                        <h3 class="text-xl font-bold">Congo<span class="text-green-400">Assist</span></h3>
                    </div>
                    <p class="text-gray-400">Votre partenaire de confiance en situations d'urgence.</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Services</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-green-400 transition">Urgence Médicale</a></li>
                        <li><a href="#" class="hover:text-green-400 transition">Police</a></li>
                        <li><a href="#" class="hover:text-green-400 transition">Vaccination</a></li>
                        <li><a href="#" class="hover:text-green-400 transition">Urgence Urbaine</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">À propos</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-green-400 transition">Notre mission</a></li>
                        <li><a href="#" class="hover:text-green-400 transition">L'équipe</a></li>
                        <li><a href="#" class="hover:text-green-400 transition">Partenaires</a></li>
                        <li><a href="#" class="hover:text-green-400 transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Contact</h4>
                    <p class="text-gray-400 mb-2">Email: lombaisaac8@gmail.com</p>
                    <div class="flex gap-4 mt-4">
                        <a href="#"
                            class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-700 transition">
                            <i data-feather="facebook" class="w-5 h-5"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-500 transition">
                            <i data-feather="twitter" class="w-5 h-5"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-pink-600 rounded-lg flex items-center justify-center hover:bg-pink-700 transition">
                            <i data-feather="instagram" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 CongoAssist. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        feather.replace();
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
</body>

</html>