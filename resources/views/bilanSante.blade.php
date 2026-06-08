<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilan de Santé - CongoAssist</title>
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
                <p class="text-xs text-blue-200">Bilan Santé</p>
            </div>
        </div>

        <nav class="flex-1 space-y-2">
            <a href="{{ route('bilanController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white bg-white bg-opacity-10">
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
                <div class="flex items-center gap-4">
                    <div class="bg-gradient-to-br from-red-500 to-pink-600 p-4 rounded-xl shadow-lg">
                        <i data-feather="heart" class="text-white w-8 h-8"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Mon Bilan de Santé</h1>
                        <p class="text-gray-600">Informations médicales personnelles</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 bg-gradient-to-r from-blue-50 to-green-50 px-6 py-3 rounded-xl">
                    <img src="{{ asset($citoyen->photo_profil) }}" alt="Profil" class="w-12 h-12 rounded-full border-2 border-green-500 object-cover shadow-md">
                    <div>
                        <p class="font-bold text-gray-900">{{ $citoyen->nom }} {{ $citoyen->prenom }}</p>
                        <p class="text-sm text-gray-600">Dossier médical</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Bilan Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8 animate-fade-in">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Allergies -->
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="alert-circle" class="w-4 h-4 text-red-500"></i>
                        Allergies
                    </label>
                    <input type="text" 
                           value="{{ $bilan->allergies ?? 'Aucune' }}" 
                           readonly 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-medium">
                </div>

                <!-- Groupe sanguin -->
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="droplet" class="w-4 h-4 text-red-600"></i>
                        Groupe sanguin
                    </label>
                    <input type="text" 
                           value="{{ $bilan->groupe_sanguin ?? 'Non renseigné' }}" 
                           readonly 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-medium">
                </div>

                <!-- Taille -->
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="maximize-2" class="w-4 h-4 text-blue-600"></i>
                        Taille
                    </label>
                    <input type="text" 
                           value="{{ $bilan->taille ?? 'Non renseigné' }}" 
                           readonly 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-medium">
                </div>

                <!-- Poids -->
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="activity" class="w-4 h-4 text-green-600"></i>
                        Poids
                    </label>
                    <input type="text" 
                           value="{{ $bilan->poids ?? 'Non renseigné' }}" 
                           readonly 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-medium">
                </div>

                <!-- Antécédents familiaux -->
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="users" class="w-4 h-4 text-purple-600"></i>
                        Antécédents familiaux
                    </label>
                    <input type="text" 
                           value="{{ $bilan->antecedents_familiaux ?? 'Aucun' }}" 
                           readonly 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-medium">
                </div>

                <!-- Mode de vie -->
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="smile" class="w-4 h-4 text-yellow-600"></i>
                        Mode de vie
                    </label>
                    <input type="text" 
                           value="{{ $bilan->mode_de_vie ?? 'Non renseigné' }}" 
                           readonly 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-medium">
                </div>

                <!-- Maladies chroniques -->
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="clipboard" class="w-4 h-4 text-orange-600"></i>
                        Maladies chroniques
                    </label>
                    <input type="text" 
                           value="{{ $bilan->maladies_chroniques ?? 'Aucune' }}" 
                           readonly 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-medium">
                </div>

                <!-- Maladies passées -->
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="file-text" class="w-4 h-4 text-indigo-600"></i>
                        Maladies passées importantes
                    </label>
                    <input type="text" 
                           value="{{ $bilan->maladies_passees_importantes ?? 'Aucune' }}" 
                           readonly 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-medium">
                </div>

                <!-- Interventions chirurgicales -->
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="crosshair" class="w-4 h-4 text-red-700"></i>
                        Interventions chirurgicales
                    </label>
                    <input type="text" 
                           value="{{ $bilan->interventions_chirurgicales ?? 'Aucune' }}" 
                           readonly 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-medium">
                </div>

                <!-- Antécédents d'hospitalisation -->
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="home" class="w-4 h-4 text-teal-600"></i>
                        Antécédents d'hospitalisation
                    </label>
                    <input type="text" 
                           value="{{ $bilan->antecedents_hospitalisation ?? 'Aucun' }}" 
                           readonly 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-medium">
                </div>

                <!-- Médicaments -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="package" class="w-4 h-4 text-blue-700"></i>
                        Médicaments pris actuellement
                    </label>
                    <input type="text" 
                           value="{{ $bilan->medicaments_pris_actuellement ?? 'Aucun' }}" 
                           readonly 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-medium">
                </div>

                <!-- Vaccins -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="shield" class="w-4 h-4 text-green-700"></i>
                        Vaccins reçus
                    </label>
                    <textarea rows="4" 
                              readonly 
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-medium resize-none">{{ $bilan->listez_vaccins_reçus ?? 'Aucun' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Quick Links Footer -->
        <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <a href="{{ route('bilanController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <i data-feather="activity" class="text-blue-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Bilan</p>
            </a>
            <a href="{{ route('vaccinationMenuController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <i data-feather="shield" class="text-green-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Vaccinations</p>
            </a>
            <a href="{{ route('actualitesController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <i data-feather="newspaper" class="text-purple-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Actualités</p>
            </a>
            <a href="{{ route('MesAlertesController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <i data-feather="bell" class="text-red-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Urgences</p>
            </a>
            <a href="{{ route('compteController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <i data-feather="layout" class="text-orange-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Dashboard</p>
            </a>
            <a href="{{ route('accueil') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
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
                <a href="{{ route('bilanController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white bg-white bg-opacity-10">
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
                setTimeout(() => sidebarContent.classList.remove('-translate-x-full'), 10);
            });
        }

        if (closeSidebar) {
            closeSidebar.addEventListener('click', () => {
                sidebarContent.classList.add('-translate-x-full');
                setTimeout(() => mobileSidebar.classList.add('hidden'), 300);
            });
        }

        if (mobileSidebar) {
            mobileSidebar.addEventListener('click', (e) => {
                if (e.target === mobileSidebar) {
                    sidebarContent.classList.add('-translate-x-full');
                    setTimeout(() => mobileSidebar.classList.add('hidden'), 300);
                }
            });
        }
    </script>
</body>
</html>