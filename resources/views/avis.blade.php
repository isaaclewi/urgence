<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis - CongoAssist</title>
    <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#10b981">

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

        .avis-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #3b82f6;
        }

        .avis-card:hover {
            transform: translateX(8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            border-left-color: #10b981;
        }

        .avatar-placeholder {
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
                <li><a href="{{ route('avis') }}" class="nav-link hover:text-blue-600 transition text-blue-600">Avis</a></li>
                <li><a href="{{ route('regle') }}" class="nav-link hover:text-blue-600 transition">Règlement</a></li>
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
            <a href="{{ route('avis') }}" class="block text-blue-600 font-semibold">Avis</a>
            <a href="{{ route('regle') }}" class="block text-gray-700 hover:text-blue-600">Règlement</a>
            <a href="{{ route('login') }}" class="block bg-green-500 text-white text-center py-2 rounded-lg font-semibold">Connexion</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <!-- Hero Section -->
        <section class="hero-gradient text-white rounded-2xl p-12 mb-12 text-center animate-fade-in shadow-xl">
            <div class="flex justify-center mb-4">
                <i data-feather="message-circle" class="w-16 h-16"></i>
            </div>
            <h1 class="text-5xl md:text-6xl font-extrabold mb-4">
                Les Avis des Citoyens
            </h1>
            <p class="text-xl md:text-2xl text-blue-100 max-w-3xl mx-auto font-light">
                Découvrez ce que nos utilisateurs pensent de CongoAssist et partagez votre expérience
            </p>
        </section>

       <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white rounded-xl shadow-md p-6 text-center card-hover">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-feather="users" class="text-blue-600 w-8 h-8"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ $totalAvis }}</h3>
                <p class="text-gray-600">Avis partagés</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 text-center card-hover">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-feather="star" class="text-green-600 w-8 h-8"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-2">4/5</h3>
                <p class="text-gray-600">Note moyenne</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 text-center card-hover">
                <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-feather="trending-up" class="text-purple-600 w-8 h-8"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-2">98%</h3>
                <p class="text-gray-600">Satisfaction</p>
            </div>
        </section>

        <!-- Avis Section -->
        <section class="mb-12">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Tous les Avis</h2>
                <button onclick="window.location.href='{{ route('formulaire') }}'" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md hover:shadow-lg flex items-center">
                    <i data-feather="plus" class="mr-2 w-5 h-5"></i>
                    Donner mon avis
                </button>
            </div>

            <!-- Avis Grid -->
            <div class="space-y-6">
                @php 
                    use App\Models\AvisUser; 
                    use App\Models\Citoyens; 
                    $avis = AvisUser::all(); 
                    $citoyens = Citoyens::all(); 
                @endphp
                
                @forelse($avis as $index => $a)
                    <div class="avis-card animate-slide-in" style="animation-delay: {{ $index * 0.1 }}s;">
                        <div class="flex items-start gap-4">
                            <!-- Avatar -->
                            <div class="avatar-placeholder flex-shrink-0">
                                {{ strtoupper(substr($a->citoyen ? $a->citoyen->nom : 'Anonyme', 0, 1)) }}
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-xl font-bold text-gray-900">
                                        {{ $a->citoyen ? $a->citoyen->nom : 'Anonyme' }}
                                    </h3>
                                    <div class="flex items-center text-yellow-500">
                                        <i data-feather="star" class="w-4 h-4 fill-current"></i>
                                        <i data-feather="star" class="w-4 h-4 fill-current"></i>
                                        <i data-feather="star" class="w-4 h-4 fill-current"></i>
                                        <i data-feather="star" class="w-4 h-4 fill-current"></i>
                                        <i data-feather="star" class="w-4 h-4 fill-current"></i>
                                    </div>
                                </div>

                                <p class="text-gray-700 leading-relaxed mb-3">
                                    {{ $a->message }}
                                </p>

                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <i data-feather="calendar" class="w-4 h-4 mr-1"></i>
                                        {{ $a->created_at ? $a->created_at->format('d/m/Y') : 'Date inconnue' }}
                                    </span>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-feather="message-circle" class="text-gray-400 w-10 h-10"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Aucun avis pour le moment</h3>
                        <p class="text-gray-500 mb-6">Soyez le premier à partager votre expérience !</p>
                        <button class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-3 rounded-lg font-semibold transition shadow-md">
                            Donner mon avis
                        </button>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gradient-to-r from-blue-600 to-green-500 rounded-2xl p-12 text-center text-white mb-12 shadow-xl">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Votre avis compte !</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Aidez-nous à améliorer CongoAssist en partageant votre expérience et vos suggestions
            </p>
            <button onclick="window.location.href='{{ route('login') }}'"
                class="bg-white text-blue-700 px-10 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition shadow-lg hover:shadow-xl">
                Laisser un avis maintenant
            </button>
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
    <script>
if ("serviceWorker" in navigator) {
  window.addEventListener("load", () => {
    navigator.serviceWorker.register("/sw.js")
      .then(reg => console.log("Service Worker OK", reg))
      .catch(err => console.log("SW erreur", err));
  });
}
</script>

</body>
</html>