<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - CongoAssist</title>
    <link rel="icon" href="medias/Clogo.jpg" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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
            animation: fadeIn 0.6s ease-out forwards;
        }

        .animate-slide-right {
            animation: slideInRight 0.8s ease-out forwards;
        }

        .input-group {
            position: relative;
        }

        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label,
        .input-group select:focus + label {
            transform: translateY(-28px) scale(0.85);
            color: #10b981;
        }

        .input-group label {
            position: absolute;
            left: 12px;
            top: 12px;
            transition: all 0.3s ease;
            pointer-events: none;
            color: #6b7280;
            font-weight: 500;
        }

        .floating-label input,
        .floating-label select {
            padding-top: 20px;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #10b981 100%);
        }

        .form-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-green-50 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8 animate-fade-in">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-green-500 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-2xl">C</span>
                </div>
                <h1 class="text-3xl font-bold">
                    <span class="text-blue-800">Congo</span><span class="text-green-500">Assist</span>
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('accueil') }}" class="hidden md:flex items-center gap-2 text-gray-700 hover:text-blue-600 transition font-medium">
                    <i data-feather="home" class="w-5 h-5"></i>
                    <span>Accueil</span>
                </a>
               
            </div>
        </header>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Form Section -->
            <div class="form-card p-8 animate-fade-in">
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i data-feather="user-plus" class="text-blue-600 w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Inscrivez-vous</h2>
                            <p class="text-gray-600">Rejoignez notre communauté aujourd'hui</p>
                        </div>
                    </div>
                </div>

                {{-- MESSAGE DE SUCCÈS --}}
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6 flex items-start gap-3 animate-fade-in">
                        <i data-feather="check-circle" class="text-green-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-green-800 font-semibold">Succès !</p>
                            <p class="text-green-700 text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                {{-- ERREURS DE VALIDATION --}}
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6 animate-fade-in">
                        <div class="flex items-start gap-3">
                            <i data-feather="alert-circle" class="text-red-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-red-800 font-semibold mb-2">Erreurs détectées :</p>
                                <ul class="space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li class="text-red-700 text-sm flex items-start gap-2">
                                            <span class="text-red-500">•</span>
                                            <span>{{ $error }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('citoyens.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Nom et Prénom -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i data-feather="user" class="w-4 h-4 inline mr-1"></i>
                                Nom
                            </label>
                            <input type="text" name="nom" placeholder="Ex: Lomba" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i data-feather="user" class="w-4 h-4 inline mr-1"></i>
                                Prénom
                            </label>
                            <input type="text" name="prenom" placeholder="Ex: Isaac" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <!-- Age et Sexe -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i data-feather="calendar" class="w-4 h-4 inline mr-1"></i>
                                Âge
                            </label>
                            <input type="number" name="age" placeholder="Ex: 20" required min="18"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i data-feather="users" class="w-4 h-4 inline mr-1"></i>
                                Sexe
                            </label>
                            <select name="sexe" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition bg-white">
                                <option value="M">Homme</option>
                                <option value="F">Femme</option>
                            </select>
                        </div>
                    </div>

                    <!-- Adresse et Téléphone -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i data-feather="map-pin" class="w-4 h-4 inline mr-1"></i>
                                Adresse
                            </label>
                            <input type="text" name="adresse" placeholder="Ex: 47 rue Owando, Ouenzé" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i data-feather="phone" class="w-4 h-4 inline mr-1"></i>
                                Téléphone
                            </label>
                            <input type="text" name="telephone" placeholder="Ex: +242 06 777 88 90" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-feather="mail" class="w-4 h-4 inline mr-1"></i>
                            Email
                        </label>
                        <input type="email" name="email" placeholder="Ex: lombaisaac8@gmail.com" required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    </div>

                    <!-- Pièce d'identité -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-feather="credit-card" class="w-4 h-4 inline mr-1"></i>
                            Pièce d'identité
                        </label>
                        <div class="relative">
                            <input type="file" name="piece_identite" accept="image/*" 
                                onchange="if(this.files[0] && this.files[0].size>5*1024*1024){alert('Fichier trop gros (max 5MB)'); this.value='';} else if(this.files[0]){document.getElementById('file-name').textContent = this.files[0].name;}"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-2">
                                <i data-feather="info" class="w-3 h-3 inline mr-1"></i>
                                Format accepté: JPG, PNG (max 5MB)
                            </p>
                        </div>
                        <span id="file-name" class="text-sm text-green-600 mt-2 block"></span>
                    </div>

                    <!-- Mot de passe -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i data-feather="lock" class="w-4 h-4 inline mr-1"></i>
                                Mot de passe
                            </label>
                            <input type="password" name="password" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                            <p class="text-xs text-gray-500 mt-2">
                                <i data-feather="shield" class="w-3 h-3 inline mr-1"></i>
                                Minimum 6 caractères
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i data-feather="lock" class="w-4 h-4 inline mr-1"></i>
                                Confirmer
                            </label>
                            <input type="password" name="password_confirmation" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                        <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 px-6 rounded-lg transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <i data-feather="check" class="w-5 h-5"></i>
                            S'inscrire
                        </button>
                        <button type="reset" 
                            class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-4 px-6 rounded-lg transition flex items-center justify-center gap-2">
                            <i data-feather="x" class="w-5 h-5"></i>
                            Réinitialiser
                        </button>
                    </div>
                </form>

                <!-- Login Link -->
                <div class="mt-8 text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-600">
                        Vous avez déjà un compte ? 
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-green-500 font-semibold transition">
                            Se connecter
                        </a>
                    </p>
                </div>
            </div>

            <!-- Image/Info Section -->
            <div class="hidden lg:flex flex-col justify-center items-center animate-slide-right">
                <div class="glass-effect rounded-3xl p-8 shadow-xl">
                    <div class="mb-8 text-center">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Pourquoi nous rejoindre ?</h3>
                        <div class="space-y-4">
                            <div class="flex items-start gap-4 text-left">
                                <div class="bg-blue-100 p-3 rounded-full flex-shrink-0">
                                    <i data-feather="bell" class="text-blue-600 w-6 h-6"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Alertes en temps réel</h4>
                                    <p class="text-gray-600 text-sm">Recevez des notifications instantanées pour les urgences dans votre quartier</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 text-left">
                                <div class="bg-green-100 p-3 rounded-full flex-shrink-0">
                                    <i data-feather="shield" class="text-green-600 w-6 h-6"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Sécurité renforcée</h4>
                                    <p class="text-gray-600 text-sm">Contribuez à la sécurité de votre communauté</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 text-left">
                                <div class="bg-purple-100 p-3 rounded-full flex-shrink-0">
                                    <i data-feather="users" class="text-purple-600 w-6 h-6"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Communauté solidaire</h4>
                                    <p class="text-gray-600 text-sm">Rejoignez un réseau d'entraide active</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 text-left">
                                <div class="bg-orange-100 p-3 rounded-full flex-shrink-0">
                                    <i data-feather="map" class="text-orange-600 w-6 h-6"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Carte interactive</h4>
                                    <p class="text-gray-600 text-sm">Visualisez les incidents en direct sur la carte</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <img src="medias/Premium Vector _ Flat isometric illustration concept man working on computer.jpg" 
                            alt="Illustration" 
                            class="w-full rounded-2xl shadow-lg">
                    </div>

                    <div class="mt-8 p-6 bg-gradient-to-r from-blue-500 to-green-500 rounded-2xl text-white text-center">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <i data-feather="check-circle" class="w-6 h-6"></i>
                            <p class="font-bold text-lg">Inscription gratuite</p>
                        </div>
                        <p class="text-blue-100 text-sm">Abonnement annuel : 2000 FCFA seulement</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-12 pt-8 border-t border-gray-200 flex justify-between items-center flex-wrap gap-4 animate-fade-in">
            <div class="flex items-center gap-2 text-gray-600">
                <i data-feather="mail" class="w-4 h-4"></i>
                <a href="mailto:lombaisaac8@gmail.com" class="hover:text-blue-600 transition font-medium">
                    lombaisaac8@gmail.com
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="#" class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center hover:scale-110 transition shadow-md">
                    <img src="medias/youtub.jpg" alt="Youtube" class="w-full h-full rounded-full object-cover">
                </a>
                <a href="#" class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center hover:scale-110 transition shadow-md">
                    <img src="medias/facebook.jpg" alt="Facebook" class="w-full h-full rounded-full object-cover">
                </a>
                <a href="#" class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center hover:scale-110 transition shadow-md">
                    <img src="medias/whatsapp.jpg" alt="WhatsApp" class="w-full h-full rounded-full object-cover">
                </a>
            </div>
        </footer>
    </div>

    <script>
        feather.replace();

        const routeAccueil = "{{ route('accueil') }}";
        const routeLogin = "{{ route('login') }}";
    </script>
</body>
</html>