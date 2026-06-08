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
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .animate-slide-in {
            animation: slideInLeft 0.5s ease-out forwards;
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
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .action-card {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(16, 185, 129, 0.1));
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .action-card:hover {
            border-color: #10b981;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(16, 185, 129, 0.2));
            transform: translateY(-5px);
        }

        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #10b981 100%);
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
            <a href="{{ route('MesAlertesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="bell" class="w-5 h-5"></i>
                <span>Urgences</span>
            </a>
            <a href="{{ route('forumCitoyen') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="message-square" class="w-5 h-5"></i>
                <span>Forum</span>
            </a>
            <a href="{{ route('codeQR') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="grid" class="w-5 h-5"></i>
                <span>Code QR</span>
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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Mon Compte</h1>
                    <p class="text-gray-600">Gérez votre profil et vos paramètres</p>
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

        <!-- Profile Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 card-hover animate-slide-in">
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

        <!-- Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="action-card p-6 rounded-xl cursor-pointer" onclick="window.location='{{ route('profilController') }}'">
                <div class="flex items-center gap-4">
                    <div class="bg-blue-100 p-4 rounded-xl">
                        <i data-feather="user" class="text-blue-600 w-8 h-8"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Mon profil</h3>
                        <p class="text-sm text-gray-600">Modifier mes informations</p>
                    </div>
                </div>
            </div>

            <div class="action-card p-6 rounded-xl cursor-pointer" onclick="window.location='{{ route('avisUsers.index') }}'">
                <div class="flex items-center gap-4">
                    <div class="bg-green-100 p-4 rounded-xl">
                        <i data-feather="message-circle" class="text-green-600 w-8 h-8"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Mon avis</h3>
                        <p class="text-sm text-gray-600">Partager mon expérience</p>
                    </div>
                </div>
            </div>

            <div class="action-card p-6 rounded-xl cursor-pointer" onclick="window.location='{{ route('codeQR') }}'">
                <div class="flex items-center gap-4">
                    <div class="bg-purple-100 p-4 rounded-xl">
                        <i data-feather="grid" class="text-purple-600 w-8 h-8"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Code QR</h3>
                        <p class="text-sm text-gray-600">Mon QR personnel</p>
                    </div>
                </div>
            </div>

            <div class="action-card p-6 rounded-xl cursor-pointer" onclick="window.location='{{ route('MesAlertesController') }}'">
                <div class="flex items-center gap-4">
                    <div class="bg-red-100 p-4 rounded-xl">
                        <i data-feather="bell" class="text-red-600 w-8 h-8"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Urgences</h3>
                        <p class="text-sm text-gray-600">Mes alertes actives</p>
                    </div>
                </div>
            </div>

            <div class="action-card p-6 rounded-xl cursor-pointer" onclick="window.location='{{ route('forumCitoyen') }}'">
                <div class="flex items-center gap-4">
                    <div class="bg-orange-100 p-4 rounded-xl">
                        <i data-feather="users" class="text-orange-600 w-8 h-8"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Forum</h3>
                        <p class="text-sm text-gray-600">Rejoindre les discussions</p>
                    </div>
                </div>
            </div>

            <div class="action-card p-6 rounded-xl cursor-pointer" onclick="window.location='{{ route('bilanController') }}'">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-100 p-4 rounded-xl">
                        <i data-feather="activity" class="text-indigo-600 w-8 h-8"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Mon bilan</h3>
                        <p class="text-sm text-gray-600">Suivi de santé</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="bg-white rounded-2xl shadow-lg p-8 card-hover">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i data-feather="map" class="text-blue-600 w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Ma Position & Urgences proches</h3>
                    <p class="text-sm text-gray-600">Visualisez les alertes en temps réel autour de vous</p>
                </div>
            </div>

            <div class="rounded-xl overflow-hidden shadow-lg mb-4">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.246183956974!2d15.258879!3d-4.259204!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1a6a63f00cf56721%3A0x2de5d3fa47bb3c21!2sBrazzaville%2C%20Congo!5e0!3m2!1sfr!2scg!4v1695822730456!5m2!1sfr!2scg"
                    width="100%" 
                    height="400" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start gap-3">
                    <i data-feather="info" class="text-blue-600 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                    <p class="text-sm text-blue-800">
                        Les alertes d'urgence à proximité sont affichées sur la carte en temps réel. Activez votre géolocalisation pour une meilleure précision.
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Links Footer -->
        <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <a href="{{ route('bilanController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="activity" class="text-blue-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Bilan</p>
            </a>
            <a href="{{ route('vaccinationMenuController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="shield" class="text-green-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Vaccinations</p>
            </a>
            <a href="{{ route('actualitesController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="newspaper" class="text-purple-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Actualités</p>
            </a>
            <a href="{{ route('MesAlertesController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="bell" class="text-red-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Urgences</p>
            </a>
            <a href="{{ route('accueil') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="home" class="text-orange-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Accueil</p>
            </a>
            <a href="{{ route('accueil') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="log-out" class="text-gray-600 w-6 h-6 mx-auto mb-2"></i>
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
                <a href="{{ route('forumCitoyen') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="message-square" class="w-5 h-5"></i>
                    <span>Forum</span>
                </a>
                <a href="{{ route('codeQR') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="grid" class="w-5 h-5"></i>
                    <span>Code QR</span>
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