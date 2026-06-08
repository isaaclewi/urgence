<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccins Enfants - CongoAssist</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
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
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .vaccine-card {
            background: white;
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .vaccine-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .vaccine-card:hover img {
            transform: scale(1.1);
        }

        .badge-obligatoire {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .badge-optionnel {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
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
            <a href="{{ route('vaccinationMenuController') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition bg-white bg-opacity-10">
                <i data-feather="shield" class="w-5 h-5"></i>
                <span>Vaccinations</span>
            </a>
            <a href="{{ route('actualitesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="newspaper" class="w-5 h-5"></i>
                <span>Actualités</span>
            </a>
            <a href="{{ route('MesAlertesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
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

        <!-- Back Navigation -->
        <div class="mb-6 animate-fade-in">
            <a href="{{ route('vaccinationMenuController') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 transition">
                <i data-feather="arrow-left" class="w-5 h-5"></i>
                <span class="font-semibold">Retour aux Vaccinations</span>
            </a>
        </div>

        <!-- Header -->
        <header class="bg-white rounded-2xl shadow-md p-6 mb-8 animate-fade-in">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-3">
                        <div class="bg-gradient-to-br from-blue-500 to-purple-500 p-3 rounded-xl">
                            <i data-feather="shield" class="w-8 h-8 text-white"></i>
                        </div>
                        Vaccination — Enfants
                    </h1>
                    <p class="text-gray-600">Programme de vaccination pédiatrique</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 px-4 py-2 rounded-lg">
                        <p class="text-sm text-blue-600 font-semibold">Total: <span class="text-blue-800 font-bold">{{ count($vaccins) }}</span> vaccins</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Info Banner -->
        <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl p-6 mb-8 text-white animate-fade-in">
            <div class="flex items-start gap-4">
                <div class="bg-white bg-opacity-20 p-3 rounded-lg flex-shrink-0">
                    <i data-feather="info" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-2">Importance de la vaccination infantile</h3>
                    <p class="text-purple-100">La vaccination protège les enfants contre des maladies graves et potentiellement mortelles. Respectez le calendrier vaccinal pour assurer une protection optimale de votre enfant.</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-fade-in">
            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold mb-1">Vaccins obligatoires</p>
                        <p class="text-3xl font-bold text-red-600">{{ collect($vaccins)->where('vaccination_obligatoire', true)->count() }}</p>
                    </div>
                    <div class="bg-red-100 p-4 rounded-xl">
                        <i data-feather="alert-circle" class="w-8 h-8 text-red-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold mb-1">Vaccins optionnels</p>
                        <p class="text-3xl font-bold text-blue-600">{{ collect($vaccins)->where('vaccination_obligatoire', false)->count() }}</p>
                    </div>
                    <div class="bg-blue-100 p-4 rounded-xl">
                        <i data-feather="check-circle" class="w-8 h-8 text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold mb-1">Fabricants</p>
                        <p class="text-3xl font-bold text-green-600">{{ collect($vaccins)->pluck('fabricant')->unique()->count() }}</p>
                    </div>
                    <div class="bg-green-100 p-4 rounded-xl">
                        <i data-feather="package" class="w-8 h-8 text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vaccines Grid -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i data-feather="grid" class="w-6 h-6 text-blue-600"></i>
                Vaccins disponibles
            </h2>
            
            @forelse($vaccins as $index => $vaccin)
                @php
                    // Image selon la voie ou le type
                    if (str_contains(strtolower($vaccin->voie_administration), 'orale')) {
                        $img = asset('medias/Screenshot 2025-02-15 220828.jpg');
                    } elseif (str_contains(strtolower($vaccin->voie_administration), 'injection')) {
                        $img = asset('medias/Screenshot 2025-02-15 220915.jpg');
                    } else {
                        $img = asset('medias/Screenshot 2025-02-15 220719.jpg');
                    }
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" style="animation-delay: {{ $index * 0.1 }}s">
                    <div class="vaccine-card card-hover animate-slide-up">
                        <div class="overflow-hidden">
                            <img src="{{ $img }}" alt="{{ $vaccin->nom_vaccin }}">
                        </div>
                        
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-xl font-bold text-gray-900">{{ $vaccin->nom_vaccin }}</h3>
                                <span class="px-3 py-1 rounded-full text-xs font-bold text-white {{ $vaccin->vaccination_obligatoire ? 'badge-obligatoire' : 'badge-optionnel' }}">
                                    {{ $vaccin->vaccination_obligatoire ? 'Obligatoire' : 'Optionnel' }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center gap-2 text-sm">
                                    <i data-feather="briefcase" class="w-4 h-4 text-blue-600"></i>
                                    <span class="text-gray-600">Fabricant:</span>
                                    <span class="font-semibold text-gray-900">{{ $vaccin->fabricant ?? '—' }}</span>
                                </div>

                                <div class="flex items-center gap-2 text-sm">
                                    <i data-feather="layers" class="w-4 h-4 text-green-600"></i>
                                    <span class="text-gray-600">Doses:</span>
                                    <span class="font-semibold text-gray-900">{{ $vaccin->nombre_doses ?? '—' }}</span>
                                </div>

                                <div class="flex items-center gap-2 text-sm">
                                    <i data-feather="droplet" class="w-4 h-4 text-purple-600"></i>
                                    <span class="text-gray-600">Voie:</span>
                                    <span class="font-semibold text-gray-900">{{ $vaccin->voie_administration ?? '—' }}</span>
                                </div>

                                <div class="flex items-center gap-2 text-sm">
                                    <i data-feather="users" class="w-4 h-4 text-orange-600"></i>
                                    <span class="text-gray-600">Âge cible:</span>
                                    <span class="font-semibold text-gray-900">{{ $vaccin->age_cible_min }} - {{ $vaccin->age_cible_max }} mois</span>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-3 mt-4">
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span class="text-gray-600 flex items-center gap-1">
                                            <i data-feather="calendar" class="w-3 h-3"></i>
                                            Fabrication
                                        </span>
                                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($vaccin->date_fabrication)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-600 flex items-center gap-1">
                                            <i data-feather="clock" class="w-3 h-3"></i>
                                            Expiration
                                        </span>
                                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($vaccin->date_expiration)->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl shadow-lg p-12 text-center">
                    <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-feather="inbox" class="w-10 h-10 text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 text-lg font-semibold mb-2">Aucun vaccin disponible</p>
                    <p class="text-gray-400">Les vaccins pour enfants seront bientôt disponibles</p>
                </div>
            @endforelse
        </div>

        <!-- Quick Links -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mt-8">
            <a href="{{ route('bilanController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="activity" class="w-6 h-6 mx-auto mb-2 text-blue-600"></i>
                <p class="text-sm font-semibold text-gray-800">Bilan</p>
            </a>
            <a href="{{ route('vaccinationMenuController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="shield" class="w-6 h-6 mx-auto mb-2 text-green-600"></i>
                <p class="text-sm font-semibold text-gray-800">Vaccinations</p>
            </a>
            <a href="{{ route('actualitesController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="newspaper" class="w-6 h-6 mx-auto mb-2 text-purple-600"></i>
                <p class="text-sm font-semibold text-gray-800">Actualités</p>
            </a>
            <a href="{{ route('MesAlertesController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="bell" class="w-6 h-6 mx-auto mb-2 text-red-600"></i>
                <p class="text-sm font-semibold text-gray-800">Urgences</p>
            </a>
            <a href="{{ route('forumCitoyen') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="message-square" class="w-6 h-6 mx-auto mb-2 text-orange-600"></i>
                <p class="text-sm font-semibold text-gray-800">Forum</p>
            </a>
            <a href="{{ route('accueil') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="log-out" class="w-6 h-6 mx-auto mb-2 text-gray-600"></i>
                <p class="text-sm font-semibold text-gray-800">Déconnexion</p>
            </a>
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
                <a href="{{ route('vaccinationMenuController') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg text-white bg-white bg-opacity-10">
                    <i data-feather="shield" class="w-5 h-5"></i>
                    <span>Vaccinations</span>
                </a>
                <a href="{{ route('actualitesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="newspaper" class="w-5 h-5"></i>
                    <span>Actualités</span>
                </a>
                <a href="{{ route('MesAlertesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
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