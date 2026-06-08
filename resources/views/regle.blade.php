<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Règlement - CongoAssist</title>
    <link rel="icon" href="medias/Clogo.jpg" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
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

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .animate-slide-in {
            animation: slideInLeft 0.5s ease-out forwards;
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }

        .nav-link {
            position: relative;
            padding-bottom: 4px;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #10b981, #3b82f6);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #10b981 100%);
        }

        .rule-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #3b82f6;
            margin-bottom: 16px;
        }

        .rule-card:hover {
            transform: translateX(8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            border-left-color: #10b981;
        }

        .rule-number {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #10b981);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .accordion-content.active {
            max-height: 1000px;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navbar -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="container mx-auto flex justify-between items-center px-6 py-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-green-500 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">C</span>
                </div>
                <span class="text-2xl font-bold">
                    <span class="text-blue-800">Congo</span><span class="text-green-500">Assist</span>
                </span>
            </div>

            <ul class="hidden md:flex space-x-8 text-gray-700 font-medium">
                <li><a href="{{ route('accueil') }}" class="nav-link hover:text-blue-600 transition">Accueil</a></li>
                <li><a href="{{ route('avis') }}" class="nav-link hover:text-blue-600 transition">Avis</a></li>
                <li><a href="{{ route('regle') }}" class="nav-link hover:text-blue-600 transition text-blue-600">Règlement</a></li>
            </ul>

            <a href="{{ route('login') }}" class="hidden md:inline-block bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-2.5 rounded-lg font-semibold transition shadow-md hover:shadow-lg">
                Connexion
            </a>

            <button id="menu-btn" class="md:hidden text-gray-700">
                <i data-feather="menu"></i>
            </button>
        </nav>

        <div id="mobile-menu" class="hidden bg-white md:hidden px-6 py-4 space-y-4 border-t">
            <a href="{{ route('accueil') }}" class="block text-gray-700 hover:text-blue-600">Accueil</a>
            <a href="{{ route('avis') }}" class="block text-gray-700 hover:text-blue-600">Avis</a>
            <a href="{{ route('regle') }}" class="block text-blue-600 font-semibold">Règlement</a>
            <a href="{{ route('login') }}" class="block bg-green-500 text-white text-center py-2 rounded-lg font-semibold">Connexion</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <!-- Hero Section -->
        <section class="hero-gradient text-white rounded-2xl p-12 mb-12 text-center animate-fade-in shadow-xl">
            <div class="flex justify-center mb-4">
                <i data-feather="file-text" class="w-16 h-16"></i>
            </div>
            <h1 class="text-5xl md:text-6xl font-extrabold mb-4">
                Règlement d'Utilisation
            </h1>
            <p class="text-xl md:text-2xl text-blue-100 max-w-3xl mx-auto font-light">
                Règles et conditions d'utilisation de la plateforme CongoAssist
            </p>
        </section>

        <!-- Important Notice -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 mb-8 shadow-md">
            <div class="flex items-start gap-4">
                <div class="bg-blue-500 rounded-full p-2 flex-shrink-0">
                    <i data-feather="info" class="text-white w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Information importante</h3>
                    <p class="text-blue-800">
                        En utilisant CongoAssist, vous acceptez de respecter l'ensemble des règles énoncées ci-dessous. 
                        Nous vous recommandons de lire attentivement ce document avant de créer votre compte.
                    </p>
                </div>
            </div>
        </div>

        <!-- Rules Section -->
        <section class="mb-12">
            <div class="space-y-4">
                <!-- Rule 1 -->
                <div class="rule-card animate-slide-in" style="animation-delay: 0s;">
                    <div class="flex items-start gap-4">
                        <div class="rule-number">1</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Objet de la plateforme</h3>
                            <p class="text-gray-700 leading-relaxed">
                                La plateforme CongoAssist est un service de gestion d'urgence communautaire, permettant aux habitants d'un même quartier de signaler, recevoir et coordonner des alertes en temps réel. Son but est de renforcer la sécurité et l'entraide dans votre environnement.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Rule 2 -->
                <div class="rule-card animate-slide-in" style="animation-delay: 0.1s;">
                    <div class="flex items-start gap-4">
                        <div class="rule-number">2</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Conditions d'inscription</h3>
                            <div class="space-y-3">
                                <p class="text-gray-700 leading-relaxed">
                                    Pour vous inscrire sur CongoAssist, vous devez :
                                </p>
                                <ul class="space-y-2 ml-4">
                                    <li class="flex items-start gap-2 text-gray-700">
                                        <i data-feather="check-circle" class="text-green-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                                        <span>Fournir une photo claire et lisible de votre pièce d'identité officielle</span>
                                    </li>
                                    <li class="flex items-start gap-2 text-gray-700">
                                        <i data-feather="check-circle" class="text-green-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                                        <span>Utiliser un nom complet correspondant à votre pièce d'identité</span>
                                    </li>
                                    <li class="flex items-start gap-2 text-gray-700">
                                        <i data-feather="check-circle" class="text-green-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                                        <span>Fournir un numéro de téléphone valide associé à votre compte Money</span>
                                    </li>
                                    <li class="flex items-start gap-2 text-gray-700">
                                        <i data-feather="check-circle" class="text-green-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                                        <span>Avoir au moins 18 ans</span>
                                    </li>
                                </ul>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
                                    <p class="text-green-800 font-semibold mb-2">💡 Pourquoi s'inscrire ?</p>
                                    <p class="text-green-700 text-sm">
                                        En tant que citoyen, votre inscription vous permet de recevoir des alertes en temps réel, de signaler rapidement tout incident et de suivre l'évolution des interventions dans votre zone. Vos données sont protégées.
                                    </p>
                                </div>
                                <p class="text-gray-600 text-sm italic mt-3">
                                    ⏱️ L'activation et la validation du compte prend 24 heures.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rule 3 -->
                <div class="rule-card animate-slide-in" style="animation-delay: 0.2s;">
                    <div class="flex items-start gap-4">
                        <div class="rule-number">3</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Abonnement et facturation</h3>
                            <div class="space-y-3">
                                <p class="text-gray-700 leading-relaxed">
                                    L'inscription est gratuite, mais le service est soumis à un abonnement annuel :
                                </p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <p class="text-yellow-900 font-bold text-lg mb-2">💰 2000 francs CFA / ans</p>
                                    <p class="text-yellow-800 text-sm">
                                        Prélèvement automatique sur votre compte Airtel/MTN Money chaque fin d'année
                                    </p>
                                </div>
                                <ul class="space-y-2 ml-4">
                                    <li class="flex items-start gap-2 text-gray-700">
                                        <i data-feather="alert-circle" class="text-orange-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                                        <span>En cas de solde insuffisant, votre compte pourra être suspendu jusqu'au règlement</span>
                                    </li>
                                    <li class="flex items-start gap-2 text-gray-700">
                                        <i data-feather="alert-circle" class="text-orange-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                                        <span>Aucun remboursement pour les mois entamés</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rule 4 -->
                <div class="rule-card animate-slide-in" style="animation-delay: 0.3s;">
                    <div class="flex items-start gap-4">
                        <div class="rule-number">4</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Utilisation responsable</h3>
                            <div class="space-y-3">
                                <p class="text-gray-700 leading-relaxed font-semibold">
                                    Vous vous engagez à :
                                </p>
                                <ul class="space-y-2 ml-4">
                                    <li class="flex items-start gap-2 text-gray-700">
                                        <i data-feather="shield" class="text-blue-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                                        <span>N'utiliser la plateforme que pour des urgences réelles ou des informations fiables</span>
                                    </li>
                                    <li class="flex items-start gap-2 text-gray-700">
                                        <i data-feather="x-circle" class="text-red-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                                        <span>Ne pas publier de fausses alertes</span>
                                    </li>
                                    <li class="flex items-start gap-2 text-gray-700">
                                        <i data-feather="x-circle" class="text-red-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                                        <span>Éviter les propos injurieux, discriminatoires ou diffamatoires</span>
                                    </li>
                                </ul>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mt-4">
                                    <p class="text-red-800 font-semibold flex items-center gap-2">
                                        <i data-feather="alert-triangle" class="w-5 h-5"></i>
                                        Attention
                                    </p>
                                    <p class="text-red-700 text-sm mt-2">
                                        Toute tentative de fraude ou d'usurpation d'identité entraînera la suspension immédiate du compte.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rule 5 -->
                <div class="rule-card animate-slide-in" style="animation-delay: 0.4s;">
                    <div class="flex items-start gap-4">
                        <div class="rule-number">5</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Confidentialité et données</h3>
                            <ul class="space-y-3">
                                <li class="flex items-start gap-2 text-gray-700">
                                    <i data-feather="lock" class="text-purple-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                                    <span>Les informations personnelles et pièces d'identité sont stockées de manière sécurisée et utilisées uniquement pour la vérification de votre profil</span>
                                </li>
                                <li class="flex items-start gap-2 text-gray-700">
                                    <i data-feather="map-pin" class="text-purple-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                                    <span>Les données de localisation ne sont partagées qu'avec vos voisins enregistrés et uniquement en cas d'alerte</span>
                                </li>
                                <li class="flex items-start gap-2 text-gray-700">
                                    <i data-feather="shield-off" class="text-purple-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                                    <span>Nous ne revendons pas vos données à des tiers</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Rule 6 -->
                <div class="rule-card animate-slide-in" style="animation-delay: 0.5s;">
                    <div class="flex items-start gap-4">
                        <div class="rule-number">6</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Sanctions</h3>
                            <div class="space-y-3">
                                <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
                                    <p class="text-orange-900 font-semibold">Fausse alerte</p>
                                    <p class="text-orange-700 text-sm">→ Suspension de 1 mois</p>
                                </div>
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                                    <p class="text-red-900 font-semibold">Usurpation d'identité</p>
                                    <p class="text-red-700 text-sm">→ Suppression définitive du compte</p>
                                </div>
                                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                                    <p class="text-yellow-900 font-semibold">Impayés récurrents</p>
                                    <p class="text-yellow-700 text-sm">→ Suspension du service jusqu'au règlement</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rule 7 -->
                <div class="rule-card animate-slide-in" style="animation-delay: 0.6s;">
                    <div class="flex items-start gap-4">
                        <div class="rule-number">7</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Respect de la Vie Privée</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Les données personnelles des utilisateurs sont strictement confidentielles. Toute tentative de collecte ou d'utilisation abusive de ces données est interdite.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Rule 8 -->
                <div class="rule-card animate-slide-in" style="animation-delay: 0.7s;">
                    <div class="flex items-start gap-4">
                        <div class="rule-number">8</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Engagement Communautaire</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Chaque membre doit contribuer à la sécurité et à la solidarité de la communauté en signalant rapidement toute urgence et en apportant assistance si possible.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gradient-to-r from-blue-600 to-green-500 rounded-2xl p-12 text-center text-white mb-12 shadow-xl">
            <div class="flex justify-center mb-4">
                <i data-feather="check-circle" class="w-12 h-12"></i>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Prêt à rejoindre CongoAssist ?</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                En acceptant ces règles, vous contribuez à créer une communauté plus sûre et solidaire
            </p>
            <a href="{{ route('formulaire') }}" class="inline-block bg-white text-blue-700 px-10 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition shadow-lg hover:shadow-xl">
                Créer mon compte
            </a>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-bold mb-4 text-green-400">À propos de CongoAssist</h3>
                <p class="text-gray-300 leading-relaxed">
                    CongoAssist est une plateforme communautaire de signalement des urgences, permettant d'alerter rapidement les services compétents pour sauver des vies.
                </p>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-4 text-green-400">Liens utiles</h3>
                <ul class="space-y-2 text-gray-300">
                    <li><a href="{{ route('accueil') }}" class="hover:text-green-400 transition">Accueil</a></li>
                    <li><a href="{{ route('avis') }}" class="hover:text-green-400 transition">Les avis</a></li>
                    <li><a href="{{ route('regle') }}" class="hover:text-green-400 transition">Règlement</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-4 text-green-400">Contact</h3>
                <p class="text-gray-300 flex items-center mb-2">
                    <i data-feather="mail" class="mr-2 w-4 h-4"></i> lombaisaac8@gmail.com
                </p>
               <p class="text-gray-300 flex items-center mb-2">
                    <i data-feather="phone" class="mr-2 w-4 h-4"></i> +242 05 052 0146/06 839 8249
                </p>
                <p class="text-gray-300 flex items-center">
                    <i data-feather="map-pin" class="mr-2 w-4 h-4"></i> Brazzaville, Congo
                </p>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
            &copy; 2026 CongoAssist. Tous droits réservés.
        </div>
    </footer>

    <script>
        feather.replace();

        // Menu mobile
        document.getElementById('menu-btn').addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>