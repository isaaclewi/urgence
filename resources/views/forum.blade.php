{{-- resources/views/forum/index.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espaces de discussion - CongoAssist</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        /* Animations */
        @keyframes fadeIn { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }
        @keyframes slideInLeft { from {opacity:0; transform:translateX(-30px);} to {opacity:1; transform:translateX(0);} }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.8; } }
        
        .animate-fade-in { animation: fadeIn 0.6s ease-out forwards; }
        .animate-slide-in { animation: slideInLeft 0.5s ease-out forwards; }
        .animate-pulse-slow { animation: pulse 3s ease-in-out infinite; }

        /* Cards */
        .card-hover { 
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            position: relative;
            overflow: hidden;
        }
        .card-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #059669);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        .card-hover:hover::before { transform: scaleX(1); }
        .card-hover:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.15); 
        }

        /* Sidebar */
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
            background: linear-gradient(135deg, #10b981, #059669);
            transform: scaleY(0); 
            transition: transform 0.3s ease; 
        }
        .sidebar-link:hover::before, 
        .sidebar-link.active::before { transform: scaleY(1); }
        .sidebar-link:hover { 
            background: rgba(16, 185, 129, 0.1); 
            padding-left: 24px; 
        }
        .sidebar-link.active {
            background: rgba(16, 185, 129, 0.15);
            padding-left: 24px;
        }

        /* Mobile menu */
        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .mobile-menu.open {
            transform: translateX(0);
        }

        /* Overlay */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 40;
            backdrop-filter: blur(4px);
        }
        .overlay.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { 
            background: linear-gradient(135deg, #10b981, #059669); 
            border-radius: 4px; 
        }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(135deg, #059669, #10b981); }

        /* Badge pulse */
        .badge-new {
            position: relative;
        }
        .badge-new::after {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-green-50 flex overflow-hidden">

    <!-- Overlay pour mobile -->
    <div id="overlay" class="overlay lg:hidden" onclick="toggleMobileMenu()"></div>

    <!-- Sidebar Desktop -->
    <aside class="sidebar hidden lg:flex flex-col w-72 min-h-screen sticky top-0 text-white shadow-2xl bg-gradient-to-b from-blue-800 via-blue-900 to-blue-950">
        <div class="p-6">
            <!-- Logo -->
            <div class="flex items-center gap-3 mb-10 group cursor-pointer">
                <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <span class="text-white font-bold text-2xl">C</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Congo<span class="text-green-400">Assist</span></h1>
                    <p class="text-xs text-blue-200">Portail Citoyen</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="space-y-2">
                <a href="{{ route('bilanController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white hover:text-green-300 transition">
                    <i data-feather="activity" class="w-5 h-5"></i>
                    <span class="font-semibold">Mon bilan</span>
                </a>
                <a href="{{ route('vaccinationMenuController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white hover:text-green-300 transition">
                    <i data-feather="shield" class="w-5 h-5"></i>
                    <span class="font-semibold">Vaccinations</span>
                </a>
                <a href="{{ route('actualitesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white hover:text-green-300 transition">
                    <i data-feather="newspaper" class="w-5 h-5"></i>
                    <span class="font-semibold">Actualités</span>
                </a>
                <a href="{{ route('MesAlertesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white hover:text-green-300 transition">
                    <i data-feather="bell" class="w-5 h-5"></i>
                    <span class="font-semibold">Urgences</span>
                </a>
                <a href="{{ route('forumCitoyen') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-xl text-white transition">
                    <i data-feather="message-square" class="w-5 h-5"></i>
                    <span class="font-semibold">Forum</span>
                </a>
                <a href="{{ route('codeQR') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white hover:text-green-300 transition">
                    <i data-feather="grid" class="w-5 h-5"></i>
                    <span class="font-semibold">Code QR</span>
                </a>
            </nav>
        </div>

        <!-- Footer -->
        <div class="mt-auto p-6 border-t border-blue-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
                    <i data-feather="user" class="w-5 h-5 text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm truncate">{{ auth()->user()->nom ?? 'Citoyen' }}</p>
                    <p class="text-xs text-blue-200 truncate">En ligne</p>
                </div>
            </div>
            <a href="{{ route('logout') }}" class="flex items-center gap-2 text-sm text-red-300 hover:text-red-200 transition">
                <i data-feather="log-out" class="w-4 h-4"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </aside>

    <!-- Sidebar Mobile -->
    <aside id="mobileSidebar" class="mobile-menu lg:hidden fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-blue-800 via-blue-900 to-blue-950 text-white shadow-2xl">
        <div class="p-6 h-full flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xl">C</span>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold">Congo<span class="text-green-400">Assist</span></h1>
                        <p class="text-xs text-blue-200">Portail Citoyen</p>
                    </div>
                </div>
                <button onclick="toggleMobileMenu()" class="p-2 hover:bg-blue-700 rounded-lg transition">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="space-y-2 flex-1 overflow-y-auto">
                <a href="{{ route('bilanController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white hover:text-green-300 transition">
                    <i data-feather="activity" class="w-5 h-5"></i>
                    <span class="font-semibold">Mon bilan</span>
                </a>
                <a href="{{ route('vaccinationMenuController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white hover:text-green-300 transition">
                    <i data-feather="shield" class="w-5 h-5"></i>
                    <span class="font-semibold">Vaccinations</span>
                </a>
                <a href="{{ route('actualitesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white hover:text-green-300 transition">
                    <i data-feather="newspaper" class="w-5 h-5"></i>
                    <span class="font-semibold">Actualités</span>
                </a>
                <a href="{{ route('MesAlertesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white hover:text-green-300 transition">
                    <i data-feather="bell" class="w-5 h-5"></i>
                    <span class="font-semibold">Urgences</span>
                </a>
                <a href="{{ route('forumCitoyen') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-xl text-white transition">
                    <i data-feather="message-square" class="w-5 h-5"></i>
                    <span class="font-semibold">Forum</span>
                </a>
                <a href="{{ route('codeQR') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white hover:text-green-300 transition">
                    <i data-feather="grid" class="w-5 h-5"></i>
                    <span class="font-semibold">Code QR</span>
                </a>
            </nav>

            <!-- Footer Mobile -->
            <div class="border-t border-blue-700 pt-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
                        <i data-feather="user" class="w-5 h-5 text-white"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm truncate">{{ auth()->user()->nom ?? 'Citoyen' }}</p>
                        <p class="text-xs text-blue-200">En ligne</p>
                    </div>
                </div>
                <a href="{{ route('logout') }}" class="flex items-center gap-2 text-sm text-red-300 hover:text-red-200 transition">
                    <i data-feather="log-out" class="w-4 h-4"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
        <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto">
            
            {{-- Header Mobile --}}
            <div class="lg:hidden bg-white rounded-2xl shadow-md p-4 mb-6 flex items-center justify-between animate-fade-in">
                <button onclick="toggleMobileMenu()" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <i data-feather="menu" class="w-6 h-6 text-gray-700"></i>
                </button>
                <h1 class="text-lg font-bold text-gray-900">Forum</h1>
                <div class="w-10"></div>
            </div>

            {{-- Header Desktop --}}
            <header class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 mb-8 animate-fade-in border-l-4 border-green-500">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center shadow-lg animate-pulse-slow">
                            <i data-feather="message-square" class="w-7 h-7 text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Espaces de discussion</h1>
                            <p class="text-sm text-gray-600 mt-1">Rejoignez les conversations de la communauté</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-gradient-to-br from-green-400 to-green-600 text-white px-4 py-2 rounded-xl shadow-md">
                        <i data-feather="grid" class="w-4 h-4"></i>
                        <span class="font-bold">{{ count($spaces) }}</span>
                        <span class="text-sm">{{ count($spaces) > 1 ? 'espaces' : 'espace' }}</span>
                    </div>
                </div>
            </header>

            {{-- Spaces Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                @forelse($spaces as $index => $space)
                    <a href="{{ route('groupeDiscussion', $space->id) }}" 
                       class="card-hover block bg-white rounded-2xl shadow-md p-5 sm:p-6 transition hover:bg-gray-50 animate-slide-in"
                       style="animation-delay: {{ $index * 0.1 }}s">
                        
                        <div class="flex items-start gap-4">
                            <!-- Avatar -->
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center text-2xl sm:text-3xl shadow-lg flex-shrink-0"
                                 style="background: {{ $space->type === 'public' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #3b82f6, #2563eb)' }}">
                                {{ $space->type === 'public' ? '🌐' : '🔒' }}
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-gray-900 text-base sm:text-lg line-clamp-2">{{ $space->title }}</h4>
                                    <i data-feather="arrow-right" class="w-5 h-5 text-gray-400 flex-shrink-0 ml-2"></i>
                                </div>
                                
                                <p class="text-gray-600 text-sm line-clamp-2 mb-3">
                                    {{ $space->description ?? 'Aucune description disponible' }}
                                </p>

                                <!-- Badges -->
                                <div class="flex gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2.5 py-1 rounded-lg text-xs font-bold">
                                        <i data-feather="{{ $space->type === 'public' ? 'globe' : 'lock' }}" class="w-3 h-3"></i>
                                        {{ strtoupper($space->type) }}
                                    </span>
                                    @if($space->service)
                                        <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-2.5 py-1 rounded-lg text-xs font-bold">
                                            <i data-feather="briefcase" class="w-3 h-3"></i>
                                            {{ $space->service->nom }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full bg-white rounded-2xl shadow-lg p-12 sm:p-16 text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i data-feather="message-square" class="w-12 h-12 text-gray-400"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 text-xl mb-2">Aucun espace disponible</h3>
                        <p class="text-gray-500 mb-6">Les espaces de discussion seront bientôt disponibles.</p>
                        <button class="px-6 py-3 bg-gradient-to-br from-green-400 to-green-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition">
                            <i data-feather="refresh-cw" class="w-4 h-4 inline mr-2"></i>
                            Actualiser
                        </button>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <script>
        // Initialiser Feather Icons
        feather.replace();

        // Toggle Mobile Menu
        function toggleMobileMenu() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('overlay');
            
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
            
            // Empêcher le scroll du body quand le menu est ouvert
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
        }

        // Fermer le menu mobile lors du clic sur un lien
        document.querySelectorAll('#mobileSidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleMobileMenu();
                }
            });
        });
    </script>
</body>
</html>