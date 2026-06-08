<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - CongoAssist</title>
    <link rel="icon" type="image/png" href="medias/Clogo.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="{{ asset('javascript/login.js') }}" defer></script>
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

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

        .animate-slide-left {
            animation: slideInLeft 0.8s ease-out forwards;
        }

        .animate-slide-right {
            animation: slideInRight 0.8s ease-out forwards;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #10b981 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }

        .input-with-icon {
            padding-left: 40px;
        }

        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #f0fdf4 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
        <!-- Left Side - Info Section -->
        <div class="hidden lg:block animate-slide-left">
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-green-500 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-3xl">C</span>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold">
                            <span class="text-blue-800">Congo</span><span class="text-green-500">Assist</span>
                        </h1>
                        <p class="text-gray-600 text-sm">Votre sécurité, notre priorité</p>
                    </div>
                </div>
                
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Bienvenue sur CongoAssist</h2>
                <p class="text-gray-600 mb-8 text-lg">
                    Connectez-vous pour accéder à votre espace personnel et gérer les urgences de votre communauté.
                </p>

                <!-- Features -->
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-100 p-3 rounded-lg flex-shrink-0">
                            <i data-feather="zap" class="text-blue-600 w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Alertes instantanées</h3>
                            <p class="text-gray-600 text-sm">Recevez et envoyez des alertes en temps réel pour une réponse rapide</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="bg-green-100 p-3 rounded-lg flex-shrink-0">
                            <i data-feather="map" class="text-green-600 w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Carte interactive</h3>
                            <p class="text-gray-600 text-sm">Visualisez les incidents autour de vous sur une carte en direct</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="bg-purple-100 p-3 rounded-lg flex-shrink-0">
                            <i data-feather="shield" class="text-purple-600 w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Sécurité renforcée</h3>
                            <p class="text-gray-600 text-sm">Données cryptées et protection de votre vie privée</p>
                        </div>
                    </div>
                </div>

                <!-- Illustration -->
                <div class="mt-8 p-6 hero-gradient rounded-2xl text-white text-center shadow-xl">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <i data-feather="users" class="w-6 h-6"></i>
                        <p class="font-bold text-lg">+5,000 citoyens actifs</p>
                    </div>
                    <p class="text-blue-100 text-sm">Rejoignez notre communauté solidaire</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="animate-slide-right">
            <div class="glass-effect rounded-3xl shadow-2xl p-8 md:p-10">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-green-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-2xl">C</span>
                    </div>
                    <h1 class="text-2xl font-bold">
                        <span class="text-blue-800">Congo</span><span class="text-green-500">Assist</span>
                    </h1>
                </div>

                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i data-feather="log-in" class="text-blue-600 w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Connexion</h2>
                            <p class="text-gray-600">Accédez à votre espace</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('processLogin') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-feather="mail" class="w-4 h-4 inline mr-1"></i>
                            Adresse email
                        </label>
                        <div class="relative">
                   
                            <input type="email" 
                                name="email" 
                                placeholder="Ex: lombaisaac8@gmail.com" 
                                required
                                class="input-with-icon w-full px-4 py-3.5 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-feather="lock" class="w-4 h-4 inline mr-1"></i>
                            Mot de passe
                        </label>
                        <div class="relative">
                        
                            <input type="password" 
                                name="password" 
                                placeholder="Entrez votre mot de passe" 
                                required
                                class="input-with-icon w-full px-4 py-3.5 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-600">Se souvenir de moi</span>
                        </label>
                        <a href="{{ route('formulaire') }}" class="text-sm text-blue-600 hover:text-green-500 font-semibold transition">
                            S'inscrire
                        </a>
                    </div>

                    <!-- Buttons -->
                    <div class="space-y-3">
                        <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-6 rounded-lg transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <i data-feather="log-in" class="w-5 h-5"></i>
                            Se connecter
                        </button>
                        
                        <button type="reset" 
                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-4 px-6 rounded-lg transition flex items-center justify-center gap-2">
                            <i data-feather="x-circle" class="w-5 h-5"></i>
                            Réinitialiser
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">Ou</span>
                    </div>
                </div>

                <!-- Sign Up Link -->
                <div class="text-center p-4 bg-gradient-to-r from-blue-50 to-green-50 rounded-lg">
                    <p class="text-gray-700">
                        Pas encore de compte ? 
                        <a href="{{ route('formulaire') }}" class="text-blue-600 hover:text-green-500 font-bold transition">
                            Créer un compte gratuitement
                        </a>
                    </p>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <div class="flex items-center justify-center gap-2 text-gray-600 text-sm">
                        <i data-feather="mail" class="w-4 h-4"></i>
                        <a href="mailto:lombaisaac8@gmail.com" class="hover:text-blue-600 transition">
                            lombaisaac8@gmail.com
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="mt-6 flex justify-center gap-4 text-sm">
                <a href="{{ route('accueil') }}" class="text-gray-600 hover:text-blue-600 transition flex items-center gap-1">
                    <i data-feather="home" class="w-4 h-4"></i>
                    Accueil
                </a>
                <span class="text-gray-300">•</span>
                <a href="{{ route('avis') }}" class="text-gray-600 hover:text-blue-600 transition flex items-center gap-1">
                    <i data-feather="message-circle" class="w-4 h-4"></i>
                    Avis
                </a>
                <span class="text-gray-300">•</span>
                <a href="{{ route('regle') }}" class="text-gray-600 hover:text-blue-600 transition flex items-center gap-1">
                    <i data-feather="file-text" class="w-4 h-4"></i>
                    Règlement
                </a>
            </div>
        </div>
    </div>

    <script>
        feather.replace();

        const routeAccueil = "{{ route('accueil') }}";
        const routeFormulaire = "{{ route('formulaire') }}";
    </script>
</body>
</html>