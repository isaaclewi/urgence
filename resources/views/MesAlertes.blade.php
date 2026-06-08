<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion d'Alertes - CongoAssist</title>
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

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .animate-slide-up {
            animation: slideInUp 0.5s ease-out forwards;
        }

        .sidebar {
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
        }

        .sidebar-link {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: #10b981;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-link:hover::before,
        .sidebar-link.active::before {
            transform: scaleY(1);
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            padding-left: 24px;
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .service-card {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            height: 350px;
            cursor: pointer;
        }

        .service-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
            filter: brightness(0.75);
        }

        .service-card:hover img {
            transform: scale(1.1);
            filter: brightness(0.9);
        }

        .service-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.95), transparent);
            color: white;
        }

        .service-card:hover .service-overlay {
            background: linear-gradient(to top, rgba(16, 185, 129, 0.95), rgba(0, 0, 0, 0.5));
        }

        .quick-link {
            background: white;
            transition: all 0.3s ease;
        }

        .quick-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(16, 185, 129, 0.1));
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-green-50 flex">
    <!-- Sidebar -->
    <aside class="sidebar hidden lg:flex flex-col w-64 min-h-screen sticky top-0 text-white p-6 shadow-xl">
        <!-- Logo -->
        <div class="flex items-center gap-3 mb-10">
            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-xl">C</span>
            </div>
            <div>
                <h1 class="text-xl font-bold">Congo<span class="text-green-400">Assist</span></h1>
                <p class="text-xs text-blue-200">Tableau de bord</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-2">
            <a href="{{ route('bilanController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="activity" class="w-5 h-5"></i>
                <span>Mon bilan</span>
            </a>
            <a href="{{ route('vaccinationMenuController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="shield" class="w-5 h-5"></i>
                <span>Vaccinations</span>
            </a>
            <a href="{{ route('actualitesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="newspaper" class="w-5 h-5"></i>
                <span>Actualités</span>
            </a>
            <a href="{{ route('MesAlertesController') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition bg-white bg-opacity-10">
                <i data-feather="bell" class="w-5 h-5"></i>
                <span>Urgences</span>
            </a>
            <a href="{{ route('forumCitoyen') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="message-square" class="w-5 h-5"></i>
                <span>Forum</span>
            </a>
            <a href="{{ route('compteController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="user" class="w-5 h-5"></i>
                <span>Mon Compte</span>
            </a>
        </nav>

        <!-- Logout -->
        <a href="{{ route('accueil') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-500 hover:bg-red-600 transition text-white mt-6">
            <i data-feather="log-out" class="w-5 h-5"></i>
            <span>Déconnexion</span>
        </a>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-4 lg:p-8 overflow-y-auto">
        <!-- Mobile Header -->
        <div class="lg:hidden bg-white rounded-xl shadow-md p-4 mb-6 flex items-center justify-between">
            <button id="mobile-menu-btn" class="text-gray-700">
                <i data-feather="menu" class="w-6 h-6"></i>
            </button>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-green-500 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">C</span>
                </div>
                <span class="font-bold text-blue-800">Congo<span class="text-green-500">Assist</span></span>
            </div>
            <div class="w-6"></div>
        </div>

        <!-- Header -->
        <header class="bg-white rounded-2xl shadow-md p-6 mb-8 animate-fade-in">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-3">
                        <i data-feather="alert-triangle" class="w-8 h-8 text-red-500"></i>
                        Gestion d'Alertes
                    </h1>
                    <p class="text-gray-600">Sélectionnez un service d'urgence pour signaler un incident</p>
                </div>
                <div class="flex items-center gap-4 bg-gradient-to-r from-blue-50 to-green-50 px-6 py-3 rounded-xl">
                    <img src="{{ asset($citoyen->photo_profil) }}" alt="Profil" class="w-14 h-14 rounded-full border-3 border-green-500 object-cover shadow-md">
                    <div>
                        <p class="font-bold text-gray-900">{{ $citoyen->nom }} {{ $citoyen->prenom }}</p>
                        <p class="text-sm text-gray-600">Membre actif</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Info Banner -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 mb-8 text-white animate-fade-in">
            <div class="flex items-start gap-4">
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i data-feather="info" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-2">Comment signaler une urgence ?</h3>
                    <p class="text-blue-100">Choisissez le type de service d'urgence ci-dessous, puis remplissez le formulaire de signalement. Votre alerte sera immédiatement transmise aux autorités compétentes.</p>
                </div>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i data-feather="grid" class="w-6 h-6 text-blue-600"></i>
                Services d'urgence disponibles
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    use App\Models\servicesProposes;
                    $services = servicesProposes::all();
                @endphp
                @foreach ($services as $index => $service)
                <a href="{{ $service->lien }}" class="block animate-slide-up" style="animation-delay: {{ $index * 0.1 }}s">
                    <div class="service-card card-hover shadow-lg">
                        <img src="{{ $service->image ?? asset('medias/default-service.jpg') }}" alt="{{ $service->titre }}">
                        <div class="service-overlay">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-feather="alert-circle" class="w-5 h-5"></i>
                                <span class="text-xs font-semibold uppercase tracking-wider">Service d'urgence</span>
                            </div>
                            <h2 class="text-2xl font-bold mb-2">{{ $service->titre }}</h2>
                            <p class="text-sm text-gray-200">{{ $service->description }}</p>
                            <div class="mt-4 flex items-center gap-2 text-green-400">
                                <span class="font-semibold">Signaler →</span>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Quick Links -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i data-feather="zap" class="w-5 h-5 text-yellow-500"></i>
                Accès rapide
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <a href="{{ route('bilanController') }}" class="quick-link p-4 rounded-xl shadow-md text-center">
                    <i data-feather="activity" class="w-6 h-6 mx-auto mb-2 text-blue-600"></i>
                    <p class="text-sm font-semibold text-gray-800">Bilan</p>
                </a>
                <a href="{{ route('vaccinationMenuController') }}" class="quick-link p-4 rounded-xl shadow-md text-center">
                    <i data-feather="shield" class="w-6 h-6 mx-auto mb-2 text-green-600"></i>
                    <p class="text-sm font-semibold text-gray-800">Vaccinations</p>
                </a>
                <a href="{{ route('actualitesController') }}" class="quick-link p-4 rounded-xl shadow-md text-center">
                    <i data-feather="newspaper" class="w-6 h-6 mx-auto mb-2 text-purple-600"></i>
                    <p class="text-sm font-semibold text-gray-800">Actualités</p>
                </a>
                <a href="{{ route('MesAlertesController') }}" class="quick-link p-4 rounded-xl shadow-md text-center">
                    <i data-feather="bell" class="w-6 h-6 mx-auto mb-2 text-red-600"></i>
                    <p class="text-sm font-semibold text-gray-800">Urgences</p>
                </a>
                <a href="{{ route('compteController') }}" class="quick-link p-4 rounded-xl shadow-md text-center">
                    <i data-feather="user" class="w-6 h-6 mx-auto mb-2 text-indigo-600"></i>
                    <p class="text-sm font-semibold text-gray-800">Compte</p>
                </a>
                <a href="{{ route('accueil') }}" class="quick-link p-4 rounded-xl shadow-md text-center">
                    <i data-feather="log-out" class="w-6 h-6 mx-auto mb-2 text-gray-600"></i>
                    <p class="text-sm font-semibold text-gray-800">Déconnexion</p>
                </a>
            </div>
        </div>

        <!-- Emergency Tips -->
        <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
            <div class="flex items-start gap-4">
                <div class="bg-red-100 p-4 rounded-xl">
                    <i data-feather="alert-octagon" class="w-8 h-8 text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Conseils en cas d'urgence</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start gap-2">
                            <i data-feather="check-circle" class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5"></i>
                            <span>Gardez votre calme et évaluez la situation</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-feather="check-circle" class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5"></i>
                            <span>Fournissez des informations précises sur votre localisation</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-feather="check-circle" class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5"></i>
                            <span>Restez en sécurité en attendant l'intervention des secours</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-feather="check-circle" class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5"></i>
                            <span>Ne déplacez pas les personnes blessées sauf danger immédiat</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Sidebar -->
    <div id="mobile-sidebar" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden lg:hidden">
        <div class="sidebar w-64 min-h-screen p-6 transform -translate-x-full transition-transform duration-300" id="sidebar-content">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">C</span>
                    </div>
                    <h1 class="text-lg font-bold">Congo<span class="text-green-400">Assist</span></h1>
                </div>
                <button id="close-sidebar" class="text-white">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('bilanController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="activity" class="w-5 h-5"></i>
                    <span>Mon bilan</span>
                </a>
                <a href="{{ route('vaccinationMenuController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="shield" class="w-5 h-5"></i>
                    <span>Vaccinations</span>
                </a>
                <a href="{{ route('actualitesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="newspaper" class="w-5 h-5"></i>
                    <span>Actualités</span>
                </a>
                <a href="{{ route('MesAlertesController') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg text-white bg-white bg-opacity-10">
                    <i data-feather="bell" class="w-5 h-5"></i>
                    <span>Urgences</span>
                </a>
                <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="message-square" class="w-5 h-5"></i>
                    <span>Forum</span>
                </a>
                <a href="{{ route('compteController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="user" class="w-5 h-5"></i>
                    <span>Mon Compte</span>
                </a>
            </nav>

            <a href="{{ route('accueil') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-500 hover:bg-red-600 transition text-white mt-6">
                <i data-feather="log-out" class="w-5 h-5"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </div>

    <script>
        feather.replace();

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const sidebarContent = document.getElementById('sidebar-content');
        const closeSidebar = document.getElementById('close-sidebar');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileSidebar.classList.remove('hidden');
                setTimeout(() => {
                    sidebarContent.classList.remove('-translate-x-full');
                }, 10);
            });
        }

        if (closeSidebar) {
            closeSidebar.addEventListener('click', () => {
                sidebarContent.classList.add('-translate-x-full');
                setTimeout(() => {
                    mobileSidebar.classList.add('hidden');
                }, 300);
            });
        }

        if (mobileSidebar) {
            mobileSidebar.addEventListener('click', (e) => {
                if (e.target === mobileSidebar) {
                    sidebarContent.classList.add('-translate-x-full');
                    setTimeout(() => {
                        mobileSidebar.classList.add('hidden');
                    }, 300);
                }
            });
        }
    </script>
</body>
</html>