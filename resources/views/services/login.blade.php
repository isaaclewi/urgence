<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CongoAssist - Connexion Administrateur</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .animate-slide-in {
            animation: slideIn 0.5s ease-out;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #10b981 100%);
        }

        .input-field {
            transition: all 0.3s ease;
        }

        .input-field:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.2);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <!-- Background Decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-green-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute -bottom-20 left-1/3 w-72 h-72 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float" style="animation-delay: 2s;"></div>
    </div>

    <!-- Login Card -->
    <div class="glass-effect rounded-3xl shadow-2xl w-full max-w-md p-8 md:p-10 relative z-10 animate-fade-in">
        <!-- Logo Section -->
        <div class="text-center mb-8 animate-slide-in">
            <div class="inline-block relative mb-4">
                <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-lg animate-float">
                    <span class="text-white font-bold text-3xl">C</span>
                </div>
                <div class="absolute -top-1 -right-1 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                    <i data-feather="shield" class="w-4 h-4 text-white"></i>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Congo<span class="text-green-500">Assist</span>
            </h1>
            <p class="text-gray-600 text-sm">Plateforme de gestion des services d'urgence</p>
        </div>

        <!-- Title -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2 flex items-center justify-center gap-2">
                <i data-feather="log-in" class="w-6 h-6 text-blue-600"></i>
                Connexion Service Urgence
            </h2>
            <p class="text-gray-600 text-sm">Accédez à votre espace administrateur</p>
        </div>

        <!-- Form -->
        <form action="{{ route('services.login.process') }}" method="post" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Email Field -->
            <div>
                <label for="email" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                    <i data-feather="mail" class="w-4 h-4 text-blue-600"></i>
                    Adresse email
                </label>
                <div class="relative">
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="exemple@congoassist.com" 
                        required
                        class="input-field w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i data-feather="user" class="w-5 h-5"></i>
                    </div>
                </div>
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                    <i data-feather="lock" class="w-4 h-4 text-blue-600"></i>
                    Mot de passe
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••" 
                        required
                        class="input-field w-full px-4 py-3 pl-12 pr-12 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i data-feather="key" class="w-5 h-5"></i>
                    </div>
                    <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                        <i data-feather="eye" class="w-5 h-5" id="eye-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-gray-600">Se souvenir de moi</span>
                </label>
                <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold transition">
                    Mot de passe oublié ?
                </a>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-blue-600 to-green-500 text-white py-4 rounded-xl font-bold text-lg hover:shadow-2xl transition transform hover:scale-[1.02] flex items-center justify-center gap-2">
                <i data-feather="log-in" class="w-5 h-5"></i>
                Se connecter
            </button>

            <!-- Back Link -->
            <a href="{{ route('services.accueil') }}" class="flex items-center justify-center gap-2 text-gray-600 hover:text-blue-600 font-semibold transition mt-6">
                <i data-feather="arrow-left" class="w-5 h-5"></i>
                Retour à l'accueil
            </a>
        </form>

        <!-- Info Box -->
        <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
            <div class="flex items-start gap-3">
                <i data-feather="info" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-sm text-blue-900 font-semibold mb-1">Espace réservé aux administrateurs</p>
                    <p class="text-xs text-blue-700">Cet accès est destiné uniquement au personnel autorisé des services d'urgence.</p>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Besoin d'aide ? 
                <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold transition">Contactez le support</a>
            </p>
        </div>
    </div>

    <!-- Footer -->
    <div class="absolute bottom-4 left-0 right-0 text-center text-white text-sm">
        <p class="opacity-80">© 2025 CongoAssist - Tous droits réservés</p>
    </div>

    <script>
        feather.replace();

        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.setAttribute('data-feather', 'eye-off');
            } else {
                passwordField.type = 'password';
                eyeIcon.setAttribute('data-feather', 'eye');
            }
            feather.replace();
        }

        // Auto-focus on email field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });
    </script>
</body>
</html>