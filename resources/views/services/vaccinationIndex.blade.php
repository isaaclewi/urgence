{{-- resources/views/services/vaccinationIndex.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $service->nom ?? 'CongoAssist' }} — Gestion Vaccination</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .animate-fade-in { animation: fadeIn 0.6s ease-out; }
        .animate-slide-in { animation: slideIn 0.5s ease-out; }
        .animate-scale-in { animation: scaleIn 0.4s ease-out; }
        .animate-pulse-slow { animation: pulse 2s ease-in-out infinite; }
        .animate-float { animation: float 3s ease-in-out infinite; }

        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #10b981, #059669); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #059669, #047857); }

        body[data-role="pompier"] { --accent-color: 239, 68, 68; --accent-light: 254, 226, 226; --accent-dark: 185, 28, 28; }
        body[data-role="police"] { --accent-color: 59, 130, 246; --accent-light: 219, 234, 254; --accent-dark: 29, 78, 216; }
        body[data-role="hopital"] { --accent-color: 16, 185, 129; --accent-light: 209, 250, 229; --accent-dark: 5, 150, 105; }

        .accent-bg { background: linear-gradient(135deg, rgb(var(--accent-color)), rgb(var(--accent-dark))); }
        .accent-text { color: rgb(var(--accent-color)); }
        .accent-light-bg { background: rgb(var(--accent-light)); }
        .accent-border { border-color: rgb(var(--accent-color)); }

        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .sidebar-link {
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(180deg, rgb(var(--accent-color)), rgb(var(--accent-dark)));
            transform: scaleY(0);
            transition: transform 0.3s ease;
            border-radius: 0 4px 4px 0;
        }
        .sidebar-link:hover::before,
        .sidebar-link.active::before { transform: scaleY(1); }
        .sidebar-link:hover,
        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(var(--accent-color), 0.1), transparent);
            padding-left: 20px;
            transform: translateX(4px);
        }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .card-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        .card-hover:hover::before { left: 100%; }
        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .status-indicator::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 14px;
            height: 14px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
        }

        .progress-ring { transform: rotate(-90deg); }
        .progress-ring-circle { transition: stroke-dashoffset 0.5s cubic-bezier(0.4, 0, 0.2, 1); }

        .badge-glow {
            box-shadow: 0 0 20px rgba(var(--accent-color), 0.3);
        }

        .table-row {
            transition: all 0.3s ease;
        }
        .table-row:hover {
            background: linear-gradient(90deg, rgba(var(--accent-color), 0.05), transparent);
            transform: translateX(4px);
        }

        .btn-action {
            transition: all 0.2s ease;
        }
        .btn-action:hover {
            transform: scale(1.15) rotate(5deg);
        }

        .gradient-text {
            background: linear-gradient(135deg, rgb(var(--accent-color)), rgb(var(--accent-dark)));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .modal-backdrop.show {
            backdrop-filter: blur(8px);
        }

        .stat-number {
            background: linear-gradient(135deg, rgb(var(--accent-color)), rgb(var(--accent-dark)));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @media (max-width: 1024px) {
            .sidebar-mobile {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar-mobile.open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body data-role="{{ $service->role ?? 'hopital' }}" class="bg-gradient-to-br from-gray-50 via-blue-50 to-green-50">
    <!-- Mobile Menu Button -->
    <button id="mobile-menu-btn" class="lg:hidden fixed top-4 left-4 z-50 p-3 bg-white rounded-xl shadow-lg">
        <i class="fas fa-bars text-xl text-gray-700"></i>
    </button>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-mobile lg:translate-x-0 fixed lg:sticky top-0 h-screen z-40 w-72 glass-effect shadow-2xl flex flex-col">
            <!-- Close button for mobile -->
            <button id="close-sidebar" class="lg:hidden absolute top-4 right-4 p-2 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>

            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center gap-4 mb-4 animate-scale-in">
                    <div class="relative animate-float">
                        <img src="{{ asset('medias/Clogo.jpg') }}" alt="CongoAssist" class="w-16 h-16 rounded-2xl object-cover border-2 border-white shadow-xl">
                        <div class="absolute -top-1 -right-1 w-6 h-6 accent-bg rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-shield-alt text-white text-xs"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold gradient-text">{{ $service->nom ?? 'CongoAssist' }}</h1>
                        <span class="inline-block px-3 py-1 text-xs font-bold accent-text accent-light-bg rounded-full mt-1 badge-glow">
                            {{ ucfirst($service->role ?? 'Hôpital') }}
                        </span>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4 overflow-y-auto custom-scrollbar">
                <a href="{{ route('services.compte') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 mb-2">
                    <i class="fas fa-home w-5 text-lg"></i>
                    <span class="font-semibold">Tableau de bord</span>
                </a>
                <a href="{{ route('services.urgenceSignalee') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 mb-2">
                    <i class="fas fa-bell w-5 text-lg"></i>
                    <span class="font-semibold">Urgences signalées</span>
                    <span class="ml-auto px-2 py-1 bg-red-500 text-white text-xs rounded-full">3</span>
                </a>
                <a href="{{ route('services.citoyens') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 mb-2">
                    <i class="fas fa-users w-5 text-lg"></i>
                    <span class="font-semibold">Citoyens</span>
                </a>
                <a href="#" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 mb-2 accent-light-bg">
                    <i class="fas fa-calendar-alt w-5 text-lg accent-text"></i>
                    <span class="font-semibold accent-text">Programmes Vaccination</span>
                </a>
                <div class="my-4 border-t border-gray-200"></div>
                <a href="{{ route('services.profil') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 mb-2">
                    <i class="fas fa-cog w-5 text-lg"></i>
                    <span class="font-semibold">Gestion interne</span>
                </a>
                <a href="{{ route('services.logout') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 mb-2 hover:bg-red-50">
                    <i class="fas fa-sign-out-alt w-5 text-lg"></i>
                    <span class="font-semibold">Déconnexion</span>
                </a>
            </nav>

            <div class="p-4 border-t border-gray-100">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gradient-to-r from-gray-50 to-white hover:from-white hover:to-gray-50 transition cursor-pointer shadow-sm">
                    <div class="relative status-indicator">
                        <img src="{{ asset($service->photo_profil ?? 'medias/default.jpg') }}" alt="Profil" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-lg">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900 text-sm truncate">{{ $service->nom ?? 'Service' }}</p>
                        <p class="text-xs text-green-600 flex items-center gap-1 font-semibold">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse-slow"></span>
                            En ligne
                        </p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                </div>
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
            <!-- Header -->
            <header class="glass-effect shadow-lg p-6 lg:p-8 animate-fade-in sticky top-0 z-20">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-4xl font-extrabold gradient-text mb-2">Programmes de vaccination</h1>
                        <p class="text-gray-600 font-medium">Gérez et suivez vos campagnes de vaccination en temps réel</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <button class="hidden md:flex items-center gap-2 px-4 py-2 bg-white border-2 accent-border rounded-xl font-semibold accent-text hover:accent-light-bg transition shadow-md">
                            <i class="fas fa-download"></i>
                            Exporter
                        </button>
                        <button data-bs-toggle="modal" data-bs-target="#addVaccinationModal" class="flex items-center gap-2 px-6 py-3 accent-bg text-white rounded-xl font-bold hover:opacity-90 transition shadow-xl hover:shadow-2xl transform hover:scale-105">
                            <i class="fas fa-plus-circle"></i>
                            Nouveau programme
                        </button>
                    </div>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="p-6 lg:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-2xl p-6 shadow-xl card-hover border-l-4 border-blue-500 animate-slide-in" style="animation-delay: 0.1s">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-calendar-alt text-white text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <span class="text-3xl font-black stat-number">{{ $programmes->total() }}</span>
                                <p class="text-xs text-gray-500 font-semibold mt-1">PROGRAMMES</p>
                            </div>
                        </div>
                        <h3 class="text-gray-700 font-bold mb-1">Total programmes</h3>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full font-semibold">Actifs</span>
                            <span class="text-gray-500">Gestion globale</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-xl card-hover border-l-4 border-red-500 animate-slide-in" style="animation-delay: 0.2s">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-red-400 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-virus text-white text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <span class="text-3xl font-black text-red-600">{{ $programmes->where('categorie', 'pandemie')->count() }}</span>
                                <p class="text-xs text-gray-500 font-semibold mt-1">URGENTS</p>
                            </div>
                        </div>
                        <h3 class="text-gray-700 font-bold mb-1">Pandémie</h3>
                        <div class="flex items-center gap-2 text-xs">
                            <i class="fas fa-arrow-up text-red-500"></i>
                            <span class="text-red-600 font-bold">Haute priorité</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-xl card-hover border-l-4 border-purple-500 animate-slide-in" style="animation-delay: 0.3s">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-syringe text-white text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <span class="text-3xl font-black text-purple-600">{{ $programmes->sum('nb_vaccins') }}</span>
                                <p class="text-xs text-gray-500 font-semibold mt-1">VACCINS</p>
                            </div>
                        </div>
                        <h3 class="text-gray-700 font-bold mb-1">Total vaccins</h3>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full font-semibold">Distribution</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-xl card-hover border-l-4 border-orange-500 animate-slide-in" style="animation-delay: 0.4s">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-disease text-white text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <span class="text-3xl font-black text-orange-600">{{ $programmes->where('categorie', 'epidemie')->count() }}</span>
                                <p class="text-xs text-gray-500 font-semibold mt-1">ÉPIDÉMIES</p>
                            </div>
                        </div>
                        <h3 class="text-gray-700 font-bold mb-1">Épidémie</h3>
                        <div class="flex items-center gap-2 text-xs">
                            <i class="fas fa-shield-virus text-orange-500"></i>
                            <span class="text-orange-600 font-bold">Surveillance active</span>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="glass-effect rounded-2xl p-6 shadow-xl mb-6 animate-fade-in">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <i class="fas fa-search text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2 text-lg"></i>
                                <input type="text" id="searchInput" placeholder="Rechercher un programme, organisme..." class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-500 transition font-medium">
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <select id="categoryFilter" class="px-6 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-500 font-semibold bg-white min-w-[180px]">
                                <option value="">📋 Toutes catégories</option>
                                <option value="pandemie">🦠 Pandémie</option>
                                <option value="epidemie">⚠️ Épidémie</option>
                            </select>
                            <button class="hidden lg:flex items-center gap-2 px-6 py-4 bg-gradient-to-r from-gray-100 to-gray-200 rounded-xl font-bold text-gray-700 hover:from-gray-200 hover:to-gray-300 transition shadow-md">
                                <i class="fas fa-filter"></i>
                                Filtres avancés
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-2xl shadow-2xl overflow-hidden animate-fade-in">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="accent-bg text-white">
                                    <th class="px-6 py-5 text-left font-bold text-sm uppercase tracking-wider">Programme</th>
                                    <th class="px-6 py-5 text-left font-bold text-sm uppercase tracking-wider">Période</th>
                                    <th class="px-6 py-5 text-left font-bold text-sm uppercase tracking-wider">Âge cible</th>
                                    <th class="px-6 py-5 text-left font-bold text-sm uppercase tracking-wider">Vaccins</th>
                                    <th class="px-6 py-5 text-left font-bold text-sm uppercase tracking-wider">Organisme</th>
                                    <th class="px-6 py-5 text-left font-bold text-sm uppercase tracking-wider">Catégorie</th>
                                    <th class="px-6 py-5 text-center font-bold text-sm uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @foreach ($programmes as $p)
                                <tr id="row-{{ $p->id }}" class="table-row border-b border-gray-100 hover:shadow-lg transition-all">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg transform hover:scale-110 transition">
                                                <i class="fas fa-calendar-check text-white text-lg"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 text-base">{{ $p->nom_programme }}</p>
                                                <p class="text-xs text-gray-500 font-semibold">ID: #{{ $p->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm">
                                            <p class="text-gray-900 font-bold flex items-center gap-2">
                                                <i class="fas fa-play-circle text-green-500 text-xs"></i>
                                                {{ \Carbon\Carbon::parse($p->date_debut)->format('d M Y') }}
                                            </p>
                                            <p class="text-gray-500 font-medium flex items-center gap-2 mt-1">
                                                <i class="fas fa-stop-circle text-red-500 text-xs"></i>
                                                {{ \Carbon\Carbon::parse($p->date_fin)->format('d M Y') }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="px-4 py-2 bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 rounded-xl text-sm font-bold shadow-sm">
                                            {{ $p->age_cible ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-2">
                                            <span class="px-4 py-2 bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 rounded-xl text-sm font-bold shadow-sm">
                                                <i class="fas fa-syringe mr-1"></i>
                                                {{ $p->nb_vaccins ?? 0 }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-building text-gray-400"></i>
                                            <span class="text-gray-700 font-semibold text-sm">{{ $p->organisme_resp ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        @if($p->categorie == 'pandemie')
                                            <span class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl text-xs font-black uppercase shadow-lg flex items-center gap-2 w-fit">
                                                <i class="fas fa-virus"></i> Pandémie
                                            </span>
                                        @elseif($p->categorie == 'epidemie')
                                            <span class="px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl text-xs font-black uppercase shadow-lg flex items-center gap-2 w-fit">
                                                <i class="fas fa-exclamation-triangle"></i> Épidémie
                                            </span>
                                        @else
                                            <span class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl text-xs font-bold shadow-sm">{{ $p->categorie ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex justify-center gap-3">
                                            <button onclick="viewDetails({{ $p->id }})" class="btn-action p-3 bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition shadow-md" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <form action="{{ route('services.vaccinationDestroy', $p->id) }}" method="POST" class="deleteForm" data-id="{{ $p->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action p-3 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-xl transition shadow-md" title="Supprimer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col md:flex-row items-center justify-between p-6 border-t-2 border-gray-100 bg-gray-50">
                        <p class="text-sm text-gray-600 font-semibold mb-4 md:mb-0">
                            Affichage de <span class="font-black accent-text">{{ $programmes->count() }}</span> sur 
                            <span class="font-black accent-text">{{ $programmes->total() }}</span> programmes
                        </p>
                        <div>
                            {!! $programmes->links('pagination::tailwind') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addVaccinationModal" tabindex="-1" aria-labelledby="addVaccinationLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-3xl shadow-2xl border-0 overflow-hidden">
                <div class="modal-header border-0 p-8 accent-bg">
                    <div>
                        <h2 class="text-3xl font-black text-white mb-1">Nouveau programme</h2>
                        <p class="text-white/80 font-medium">Créez un programme de vaccination</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="addVaccinationForm">
                    @csrf
                    <div class="modal-body p-8 bg-gradient-to-br from-gray-50 to-white">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-clipboard-list accent-text"></i>
                                    Nom du programme *
                                </label>
                                <input type="text" name="nom_programme" required class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-500 transition font-semibold" placeholder="Ex: Vaccination COVID-19">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-child text-purple-500"></i>
                                    Âge cible
                                </label>
                                <input type="text" name="age_cible" placeholder="Ex: 0-5 ans" class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-500 transition font-semibold">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-syringe text-blue-500"></i>
                                    Nombre de vaccins
                                </label>
                                <input type="number" name="nb_vaccins" placeholder="0" class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-500 transition font-semibold">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-calendar-plus text-green-500"></i>
                                    Date début *
                                </label>
                                <input type="date" name="date_debut" required class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-500 transition font-semibold">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-calendar-times text-red-500"></i>
                                    Date fin *
                                </label>
                                <input type="date" name="date_fin" required class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-500 transition font-semibold">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-building text-gray-500"></i>
                                    Organisme responsable
                                </label>
                                <input type="text" name="organisme_resp" placeholder="Ex: OMS, Ministère Santé" class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-500 transition font-semibold">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-tag text-orange-500"></i>
                                    Catégorie *
                                </label>
                                <select name="categorie" required class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-500 transition font-bold bg-white">
                                    <option value="">Sélectionner...</option>
                                    <option value="pandemie">🦠 Pandémie</option>
                                    <option value="epidemie">⚠️ Épidémie</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-8 bg-gray-50 flex gap-4">
                        <button type="button" data-bs-dismiss="modal" class="flex-1 px-8 py-4 border-2 border-gray-300 rounded-xl font-black text-gray-700 hover:bg-gray-100 transition shadow-md text-lg">
                            <i class="fas fa-times mr-2"></i>
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 px-8 py-4 accent-bg text-white rounded-xl font-black hover:opacity-90 transition shadow-xl text-lg">
                            <i class="fas fa-check mr-2"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Mobile menu
            $('#mobile-menu-btn').click(function() {
                $('#sidebar').addClass('open');
                $('#sidebar-overlay').removeClass('hidden');
            });

            $('#close-sidebar, #sidebar-overlay').click(function() {
                $('#sidebar').removeClass('open');
                $('#sidebar-overlay').addClass('hidden');
            });

            // Search functionality
            $('#searchInput').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('#tableBody tr').each(function() {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(searchTerm));
                });
            });

            // Category filter
            $('#categoryFilter').on('change', function() {
                const category = $(this).val().toLowerCase();
                $('#tableBody tr').each(function() {
                    if (category === '') {
                        $(this).show();
                    } else {
                        const rowCategory = $(this).find('td:nth-child(6)').text().toLowerCase();
                        $(this).toggle(rowCategory.includes(category));
                    }
                });
            });

            // Add program AJAX
            $('#addVaccinationForm').submit(function(e) {
                e.preventDefault();
                const btn = $(this).find('button[type="submit"]');
                btn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...').prop('disabled', true);

                $.ajax({
                    url: "{{ route('services.vaccinationStore') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#addVaccinationModal').modal('hide');
                        location.reload();
                    },
                    error: function(err) {
                        alert("❌ Erreur lors de l'ajout du programme");
                        btn.html('<i class="fas fa-check mr-2"></i>Enregistrer').prop('disabled', false);
                    }
                });
            });

            // Delete program AJAX
            $('.deleteForm').submit(function(e) {
                e.preventDefault();
                if (confirm("⚠️ Êtes-vous sûr de vouloir supprimer ce programme ?\n\nCette action est irréversible.")) {
                    const id = $(this).data('id');
                    const url = $(this).attr('action');
                    const row = $('#row-' + id);
                    
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(res) {
                            row.css('background', 'linear-gradient(90deg, #fee2e2, white)');
                            row.fadeOut(500, function() {
                                $(this).remove();
                            });
                        },
                        error: function(err) {
                            alert("❌ Erreur lors de la suppression");
                        }
                    });
                }
            });

            // View details function
            window.viewDetails = function(id) {
                const row = $('#row-' + id);
                const nom = row.find('td:first p:first').text();
                const dates = row.find('td:nth-child(2)').text();
                const age = row.find('td:nth-child(3)').text();
                const vaccins = row.find('td:nth-child(4)').text();
                const organisme = row.find('td:nth-child(5)').text();
                
                alert(`📋 DÉTAILS DU PROGRAMME\n\n` +
                      `Programme: ${nom}\n` +
                      `Période: ${dates}\n` +
                      `Âge cible: ${age}\n` +
                      `Vaccins: ${vaccins}\n` +
                      `Organisme: ${organisme}`);
            };
        });
    </script>
</body>
</html>