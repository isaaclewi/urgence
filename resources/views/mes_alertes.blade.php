<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de mes alertes - CongoAssist</title>
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

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .status-en-attente {
            background: #fef3c7;
            color: #92400e;
        }

        .status-en-cours {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-resolue {
            background: #d1fae5;
            color: #065f46;
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }
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
            <a href="{{ route('SuiviAlertesController') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition bg-white bg-opacity-10">
                <i data-feather="list" class="w-5 h-5"></i>
                <span>Suivi Alertes</span>
            </a>
            <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="message-square" class="w-5 h-5"></i>
                <span>Forum</span>
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
                        <i data-feather="list" class="w-8 h-8 text-blue-600"></i>
                        Suivi de mes alertes
                    </h1>
                    <p class="text-gray-600">Historique et état de vos alertes d'urgence</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 px-4 py-2 rounded-lg">
                        <p class="text-sm text-blue-600 font-semibold">Total: <span class="text-blue-800 font-bold">{{ $alertes->count() }}</span></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-slide-in">
            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold mb-1">En attente</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $alertes->where('statut', 'En attente')->count() }}</p>
                    </div>
                    <div class="bg-yellow-100 p-4 rounded-xl">
                        <i data-feather="clock" class="w-8 h-8 text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold mb-1">En cours</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $alertes->where('statut', 'En cours')->count() }}</p>
                    </div>
                    <div class="bg-blue-100 p-4 rounded-xl">
                        <i data-feather="alert-circle" class="w-8 h-8 text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold mb-1">Résolues</p>
                        <p class="text-3xl font-bold text-green-600">{{ $alertes->where('statut', 'Résolue')->count() }}</p>
                    </div>
                    <div class="bg-green-100 p-4 rounded-xl">
                        <i data-feather="check-circle" class="w-8 h-8 text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-fade-in">
            <div class="p-6 bg-gradient-to-r from-blue-500 to-green-500">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i data-feather="list" class="w-5 h-5"></i>
                    Historique des alertes
                </h2>
            </div>

            @if($alertes->isEmpty())
                <div class="p-12 text-center">
                    <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-feather="inbox" class="w-10 h-10 text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 text-lg font-semibold mb-2">Aucune alerte enregistrée</p>
                    <p class="text-gray-400">Vous n'avez pas encore créé d'alerte d'urgence</p>
                    <a href="{{ route('MesAlertesController') }}" class="inline-block mt-4 px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Créer une alerte
                    </a>
                </div>
            @else
                <div class="table-container overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <i data-feather="file-text" class="w-4 h-4 inline mr-2"></i>Titre
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <i data-feather="tag" class="w-4 h-4 inline mr-2"></i>Type
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <i data-feather="map-pin" class="w-4 h-4 inline mr-2"></i>Localisation
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <i data-feather="calendar" class="w-4 h-4 inline mr-2"></i>Date
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <i data-feather="info" class="w-4 h-4 inline mr-2"></i>Statut
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($alertes as $alerte)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $alerte->titre }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                            {{ $alerte->type_alerte }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-600 flex items-center gap-1">
                                        <i data-feather="map-pin" class="w-4 h-4 text-red-500"></i>
                                        {{ $alerte->localisation }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-600 text-sm">
                                        {{ $alerte->created_at->format('d/m/Y') }}
                                        <div class="text-xs text-gray-400">{{ $alerte->created_at->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="status-badge 
                                        @if($alerte->statut == 'En attente') status-en-attente 
                                        @elseif($alerte->statut == 'En cours') status-en-cours 
                                        @elseif($alerte->statut == 'Résolue') status-resolue 
                                        @endif">
                                        {{ $alerte->statut }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Action Button -->
        <div class="mt-8 flex justify-center">
            <a href="{{ route('MesAlertesController') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-green-500 text-white rounded-xl font-semibold hover:shadow-lg transition card-hover">
                <i data-feather="plus-circle" class="w-5 h-5"></i>
                Créer une nouvelle alerte
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
                <a href="{{ route('SuiviAlertesController') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg text-white bg-white bg-opacity-10">
                    <i data-feather="list" class="w-5 h-5"></i>
                    <span>Suivi Alertes</span>
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