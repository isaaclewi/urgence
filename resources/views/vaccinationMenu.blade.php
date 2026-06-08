<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination - CongoAssist</title>
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

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .animate-slide-up {
            animation: slideInUp 0.5s ease-out forwards;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
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
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }

        .option-card {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .option-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #3b82f6, #10b981);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .option-card:hover::before {
            transform: scaleX(1);
        }

        .option-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .option-card:hover img {
            transform: scale(1.1);
        }

        .gradient-pandemie {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .gradient-epidemie {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .gradient-enfant {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        .gradient-perso {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
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

        <!-- Header -->
        <header class="bg-white rounded-2xl shadow-md p-6 mb-8 animate-fade-in">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-3">
                        <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-3 rounded-xl animate-float">
                            <i data-feather="shield" class="w-8 h-8 text-white"></i>
                        </div>
                        Vaccination
                    </h1>
                    <p class="text-gray-600">Gérez vos programmes de vaccination et ceux de votre famille</p>
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
        <div class="bg-gradient-to-r from-blue-500 to-green-500 rounded-xl p-6 mb-8 text-white animate-fade-in">
            <div class="flex items-start gap-4">
                <div class="bg-white bg-opacity-20 p-3 rounded-lg flex-shrink-0">
                    <i data-feather="info" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-2">Pourquoi se faire vacciner ?</h3>
                    <p class="text-blue-100">La vaccination est l'un des moyens les plus efficaces de prévenir les maladies infectieuses. Elle protège non seulement vous et votre famille, mais contribue également à la santé publique en limitant la propagation des maladies.</p>
                </div>
            </div>
        </div>

        <!-- Options Grid -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i data-feather="grid" class="w-6 h-6 text-blue-600"></i>
                Programmes de vaccination disponibles
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
                <!-- Pandémies -->
                <div class="option-card card-hover animate-slide-up" style="animation-delay: 0s">
                    <div class="overflow-hidden">
                        <img src="medias/Screenshot 2025-02-15 220915.jpg" alt="Pandémies">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="gradient-pandemie p-3 rounded-xl">
                                <i data-feather="alert-triangle" class="w-6 h-6 text-white"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Pandémies</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Suivez les programmes de vaccination liés aux pandémies actuelles et protégez-vous contre les menaces sanitaires mondiales.</p>
                        <button onclick="window.location='{{ route('programmeVaccinController') }}'" class="w-full gradient-pandemie text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition flex items-center justify-center gap-2">
                            <i data-feather="arrow-right" class="w-5 h-5"></i>
                            Voir les détails
                        </button>
                    </div>
                </div>

                <!-- Épidémies -->
                <div class="option-card card-hover animate-slide-up" style="animation-delay: 0.1s">
                    <div class="overflow-hidden">
                        <img src="medias/Screenshot 2025-02-15 220719.jpg" alt="Épidémies">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="gradient-epidemie p-3 rounded-xl">
                                <i data-feather="alert-circle" class="w-6 h-6 text-white"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Épidémies</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Recevez les alertes et programmes contre les épidémies locales pour une protection ciblée de votre santé.</p>
                        <button onclick="window.location='{{ route('programmeVaccinEpidemieController') }}'" class="w-full gradient-epidemie text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition flex items-center justify-center gap-2">
                            <i data-feather="arrow-right" class="w-5 h-5"></i>
                            Découvrir
                        </button>
                    </div>
                </div>

                <!-- Vaccination Enfant -->
                <div class="option-card card-hover animate-slide-up" style="animation-delay: 0.2s">
                    <div class="overflow-hidden">
                        <img src="medias/Joyeux bébé dans un studio _ Photo Premium.jpeg" alt="Vaccin Enfant">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="gradient-enfant p-3 rounded-xl">
                                <i data-feather="heart" class="w-6 h-6 text-white"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Vaccination de mon enfant</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Ajoutez ou consultez le programme de vaccination de votre enfant selon le calendrier vaccinal recommandé.</p>
                        <button onclick="window.location='{{ route('programmeVaccinEnfantController') }}'" class="w-full gradient-enfant text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition flex items-center justify-center gap-2">
                            <i data-feather="arrow-right" class="w-5 h-5"></i>
                            Gérer
                        </button>
                    </div>
                </div>

                <!-- Mon Programme -->
                <div class="option-card card-hover animate-slide-up" style="animation-delay: 0.3s">
                    <div class="overflow-hidden">
                        <img src="medias/Free Photo _ Medium shot health worker with mask.jpeg" alt="Mon programme">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="gradient-perso p-3 rounded-xl">
                                <i data-feather="user-check" class="w-6 h-6 text-white"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Voir mon programme</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Accédez à votre propre calendrier de vaccination personnalisé et suivez vos rappels de vaccins.</p>
                        <button onclick="window.location=''" class="w-full gradient-perso text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition flex items-center justify-center gap-2">
                            <i data-feather="arrow-right" class="w-5 h-5"></i>
                            Consulter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-fade-in">
            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center gap-4">
                    <div class="bg-blue-100 p-4 rounded-xl">
                        <i data-feather="shield-check" class="w-8 h-8 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm font-semibold">Protection</p>
                        <p class="text-2xl font-bold text-gray-900">95%</p>
                        <p class="text-xs text-gray-500">Efficacité moyenne</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center gap-4">
                    <div class="bg-green-100 p-4 rounded-xl">
                        <i data-feather="users" class="w-8 h-8 text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm font-semibold">Couverture</p>
                        <p class="text-2xl font-bold text-gray-900">85%</p>
                        <p class="text-xs text-gray-500">Population vaccinée</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center gap-4">
                    <div class="bg-purple-100 p-4 rounded-xl">
                        <i data-feather="trending-up" class="w-8 h-8 text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm font-semibold">Impact</p>
                        <p class="text-2xl font-bold text-gray-900">3M+</p>
                        <p class="text-xs text-gray-500">Vies sauvées</p>
                    </div>
                </div>
            </div>
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
            <a href="{{ route('compteController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                <i data-feather="user" class="w-6 h-6 mx-auto mb-2 text-indigo-600"></i>
                <p class="text-sm font-semibold text-gray-800">Compte</p>
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