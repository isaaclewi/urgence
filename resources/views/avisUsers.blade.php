<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte - CongoAssist</title>
    <link rel="icon" href="medias/Clogo.jpg" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
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

        .sidebar-link:hover::before {
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
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-green-50 flex">
    <!-- Sidebar -->
    <aside class="sidebar hidden lg:flex flex-col w-64 min-h-screen sticky top-0 text-white p-6 shadow-xl">
        <div class="flex items-center gap-3 mb-10">
            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-xl">C</span>
            </div>
            <div>
                <h1 class="text-xl font-bold">Congo<span class="text-green-400">Assist</span></h1>
                <p class="text-xs text-blue-200">Mon Compte</p>
            </div>
        </div>

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
            <a href="{{ route('MesAlertesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="bell" class="w-5 h-5"></i>
                <span>Urgences</span>
            </a>
            <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="message-square" class="w-5 h-5"></i>
                <span>Forum</span>
            </a>
        </nav>

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
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Mon Compte</h1>
                <div class="flex items-center gap-4 bg-gradient-to-r from-blue-50 to-green-50 px-6 py-3 rounded-xl">
                    <img src="{{ asset($citoyen->photo_profil) }}" alt="Profil" class="w-12 h-12 rounded-full border-2 border-green-500 object-cover shadow-md">
                    <div>
                        <p class="font-bold text-gray-900">{{ $citoyen->nom }} {{ $citoyen->prenom }}</p>
                        <p class="text-sm text-gray-600">Membre actif</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Profile Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 card-hover animate-fade-in">
            <div class="flex flex-col md:flex-row gap-8 items-center md:items-start">
                <div class="relative">
                    <img src="{{ asset($citoyen->photo_profil) }}" alt="Photo de profil" class="w-32 h-32 rounded-full border-4 border-green-500 object-cover shadow-xl">
                    <div class="absolute bottom-0 right-0 bg-green-500 rounded-full p-2 shadow-lg">
                        <i data-feather="check" class="text-white w-5 h-5"></i>
                    </div>
                </div>
                
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $citoyen->nom }} {{ $citoyen->prenom }}</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 bg-blue-50 p-4 rounded-lg">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <i data-feather="mail" class="text-blue-600 w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold">Email</p>
                                <p class="text-sm text-gray-900 font-medium">{{ $citoyen->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 bg-green-50 p-4 rounded-lg">
                            <div class="bg-green-100 p-2 rounded-lg">
                                <i data-feather="phone" class="text-green-600 w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold">Téléphone</p>
                                <p class="text-sm text-gray-900 font-medium">{{ $citoyen->telephone }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 bg-purple-50 p-4 rounded-lg md:col-span-2">
                            <div class="bg-purple-100 p-2 rounded-lg">
                                <i data-feather="map-pin" class="text-purple-600 w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold">Adresse</p>
                                <p class="text-sm text-gray-900 font-medium">{{ $citoyen->adresse }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <a href="{{ route('profilController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="user" class="text-blue-600 w-8 h-8 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Profil</p>
            </a>
            <a href="{{ route('codeQR') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="grid" class="text-purple-600 w-8 h-8 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Code QR</p>
            </a>
            <a href="{{ route('actualitesController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="newspaper" class="text-green-600 w-8 h-8 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Actualités</p>
            </a>
            <a href="{{ route('MesAlertesController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="bell" class="text-red-600 w-8 h-8 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Urgences</p>
            </a>
            <a href="{{ route('compteController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="layout" class="text-orange-600 w-8 h-8 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Dashboard</p>
            </a>
            <a href="{{ route('accueil') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="log-out" class="text-gray-600 w-8 h-8 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Déconnexion</p>
            </a>
        </div>

        <!-- Avis Form -->
        <div class="bg-white rounded-2xl shadow-lg p-8 animate-fade-in">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i data-feather="message-circle" class="text-blue-600 w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Laisser mon avis</h3>
                    <p class="text-sm text-gray-600">Partagez votre expérience avec CongoAssist</p>
                </div>
            </div>

            <form action="{{ route('avisUsers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="edit-3" class="w-4 h-4 inline mr-1"></i>
                        Votre avis *
                    </label>
                    <textarea name="description" 
                              rows="5" 
                              required
                              placeholder="Partagez votre expérience, vos suggestions ou vos commentaires..."
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"></textarea>
                    <p class="text-xs text-gray-500 mt-2">
                        <i data-feather="info" class="w-3 h-3 inline mr-1"></i>
                        Votre avis nous aide à améliorer nos services
                    </p>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-bold py-4 px-6 rounded-lg transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    <i data-feather="send" class="w-5 h-5"></i>
                    <span>Soumettre mon avis</span>
                </button>
            </form>
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
                <a href="{{ route('MesAlertesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="bell" class="w-5 h-5"></i>
                    <span>Urgences</span>
                </a>
                <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="message-square" class="w-5 h-5"></i>
                    <span>Forum</span>
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