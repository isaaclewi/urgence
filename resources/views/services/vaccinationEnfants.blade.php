<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $service->nom ?? 'CongoAssist' }} — Gestion Vaccination Enfants</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        .animate-fade-in { animation: fadeIn 0.5s ease-out; }
        .animate-slide-in { animation: slideIn 0.4s ease-out; }
        .animate-pulse-slow { animation: pulse 2s ease-in-out infinite; }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        body[data-role="pompier"] { --accent-color: 239, 68, 68; --accent-light: 254, 226, 226; }
        body[data-role="police"] { --accent-color: 59, 130, 246; --accent-light: 219, 234, 254; }
        body[data-role="hopital"] { --accent-color: 16, 185, 129; --accent-light: 209, 250, 229; }

        .accent-bg { background: rgb(var(--accent-color, 16, 185, 129)); }
        .accent-text { color: rgb(var(--accent-color, 16, 185, 129)); }
        .accent-light-bg { background: rgb(var(--accent-light, 209, 250, 229)); }

        .sidebar-link {
            position: relative;
            transition: all 0.3s ease;
        }
        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: rgb(var(--accent-color, 16, 185, 129));
            transform: scaleY(0);
            transition: transform 0.3s ease;
            border-radius: 0 4px 4px 0;
        }
        .sidebar-link:hover::before,
        .sidebar-link.active::before { transform: scaleY(1); }
        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(var(--accent-color, 16, 185, 129), 0.1);
            padding-left: 20px;
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .status-indicator::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 12px;
            height: 12px;
            background: #10b981;
            border-radius: 50%;
            border: 2px solid white;
        }
    </style>
</head>
<body data-role="{{ $service->role ?? 'hopital' }}" class="bg-gradient-to-br from-gray-50 to-blue-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="hidden lg:flex flex-col w-72 bg-white shadow-xl sticky top-0 h-screen">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center gap-4 mb-4">
                    <div class="relative">
                        <img src="{{ asset('medias/Clogo.jpg') }}" alt="CongoAssist" class="w-14 h-14 rounded-xl object-cover border-2 border-gray-100">
                        <div class="absolute -top-1 -right-1 w-5 h-5 accent-bg rounded-full flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-xs"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">{{ $service->nom ?? 'CongoAssist' }}</h1>
                        <span class="inline-block px-3 py-1 text-xs font-bold accent-text accent-light-bg rounded-full">
                            {{ ucfirst($service->role ?? 'Hôpital') }}
                        </span>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4 overflow-y-auto custom-scrollbar">
                <a href="{{ route('services.compte') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 mb-2">
                    <i class="fas fa-home w-5"></i>
                    <span class="font-medium">Tableau de bord</span>
                </a>
                <a href="{{ route('services.urgenceSignalee') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 mb-2">
                    <i class="fas fa-bell w-5"></i>
                    <span class="font-medium">Urgences signalées</span>
                </a>
                <a href="{{ route('services.citoyens') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 mb-2">
                    <i class="fas fa-users w-5"></i>
                    <span class="font-medium">Citoyens</span>
                </a>
                <a href="#" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 mb-2 bg-green-50">
                    <i class="fas fa-syringe w-5 text-green-600"></i>
                    <span class="font-medium text-green-600">Vaccination Enfants</span>
                </a>
                <div class="my-4 border-t border-gray-200"></div>
                <a href="{{ route('services.profil') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 mb-2">
                    <i class="fas fa-cog w-5"></i>
                    <span class="font-medium">Gestion interne</span>
                </a>
                <a href="{{ route('services.logout') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-red-600 mb-2">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span class="font-medium">Déconnexion</span>
                </a>
            </nav>

            <div class="p-4 border-t border-gray-100">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition cursor-pointer">
                    <div class="relative status-indicator">
                        <img src="{{ asset($service->photo_profil ?? 'medias/default.jpg') }}" alt="Profil" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $service->nom ?? 'Service' }}</p>
                        <p class="text-xs text-green-600 flex items-center gap-1">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse-slow"></span>
                            En ligne
                        </p>
                    </div>
                    <i class="fas fa-cog text-gray-400"></i>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Header -->
            <header class="bg-white shadow-sm p-6 lg:p-8 animate-fade-in">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestion des vaccins enfants</h1>
                        <p class="text-gray-600">Suivez et gérez le calendrier vaccinal pédiatrique</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <button data-bs-toggle="modal" data-bs-target="#addVaccinModal" class="flex items-center gap-2 px-6 py-3 accent-bg text-white rounded-xl font-semibold hover:opacity-90 transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-plus"></i>
                            Ajouter un vaccin
                        </button>
                    </div>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="p-6 lg:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-syringe text-blue-600 text-xl"></i>
                            </div>
                            <span class="text-2xl font-bold text-blue-600">{{ $vaccins->total() }}</span>
                        </div>
                        <h3 class="text-gray-600 text-sm mb-1">Total vaccins</h3>
                        <p class="text-xs text-gray-500">Catalogue complet</p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <span class="text-2xl font-bold text-green-600">{{ $vaccins->where('vaccination_obligatoire', 1)->count() }}</span>
                        </div>
                        <h3 class="text-gray-600 text-sm mb-1">Obligatoires</h3>
                        <p class="text-xs text-gray-500">Vaccins requis</p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-vials text-purple-600 text-xl"></i>
                            </div>
                            <span class="text-2xl font-bold text-purple-600">{{ $vaccins->sum('nombre_doses') }}</span>
                        </div>
                        <h3 class="text-gray-600 text-sm mb-1">Doses totales</h3>
                        <p class="text-xs text-gray-500">Toutes administrations</p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-star text-orange-600 text-xl"></i>
                            </div>
                            <span class="text-2xl font-bold text-orange-600">{{ $vaccins->where('vaccination_obligatoire', 0)->count() }}</span>
                        </div>
                        <h3 class="text-gray-600 text-sm mb-1">Recommandés</h3>
                        <p class="text-xs text-gray-500">Optionnels</p>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="bg-white rounded-xl p-6 shadow-lg mb-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <i class="fas fa-search text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                                <input type="text" id="searchInput" placeholder="Rechercher un vaccin, fabricant..." class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                        </div>
                        <select id="filterObligatoire" class="px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">Tous les vaccins</option>
                            <option value="1">Obligatoires</option>
                            <option value="0">Recommandés</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="accent-bg text-white">
                                    <th class="px-6 py-4 text-left font-semibold">Vaccin</th>
                                    <th class="px-6 py-4 text-left font-semibold">Fabricant</th>
                                    <th class="px-6 py-4 text-left font-semibold">Doses</th>
                                    <th class="px-6 py-4 text-left font-semibold">Voie</th>
                                    <th class="px-6 py-4 text-left font-semibold">Âge (mois)</th>
                                    <th class="px-6 py-4 text-left font-semibold">Statut</th>
                                    <th class="px-6 py-4 text-left font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @foreach ($vaccins as $v)
                                <tr id="row-{{ $v->id }}" class="border-b border-gray-200 hover:bg-green-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-vial text-white"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $v->nom_vaccin }}</p>
                                                <p class="text-xs text-gray-500">ID: #{{ $v->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-sm">{{ $v->fabricant ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                            {{ $v->nombre_doses ?? 0 }} dose{{ ($v->nombre_doses ?? 0) > 1 ? 's' : '' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold">
                                            {{ $v->voie_administration ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-sm">
                                        {{ $v->age_cible_min ?? '0' }} - {{ $v->age_cible_max ?? '0' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($v->vaccination_obligatoire)
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold flex items-center gap-1 w-fit">
                                                <i class="fas fa-check-circle"></i> Obligatoire
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold flex items-center gap-1 w-fit">
                                                <i class="fas fa-star"></i> Recommandé
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            <form action="{{ route('services.vaccinationEnfantsDestroy', $v->id) }}" method="POST" class="deleteForm" data-id="{{ $v->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between p-6 border-t border-gray-200">
                        <p class="text-sm text-gray-600">Affichage de <span class="font-semibold">{{ $vaccins->count() }}</span> sur <span class="font-semibold">{{ $vaccins->total() }}</span> vaccins</p>
                        <div>
                            {!! $vaccins->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addVaccinModal" tabindex="-1" aria-labelledby="addVaccinModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-2xl shadow-2xl border-0">
                <div class="modal-header border-0 p-6">
                    <h2 class="text-2xl font-bold text-gray-900">Ajouter un vaccin</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="addVaccinForm">
                    @csrf
                    <div class="modal-body p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom du vaccin *</label>
                                <input type="text" name="nom_vaccin" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Ex: BCG, DTC, Polio...">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Fabricant</label>
                                <input type="text" name="fabricant" placeholder="Ex: Sanofi, GSK" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre de doses</label>
                                <input type="number" name="nombre_doses" placeholder="0" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Voie d'administration</label>
                                <select name="voie_administration" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="">Sélectionner...</option>
                                    <option value="Intramusculaire">Intramusculaire</option>
                                    <option value="Sous-cutanée">Sous-cutanée</option>
                                    <option value="Orale">Orale</option>
                                    <option value="Intradermique">Intradermique</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Vaccination obligatoire</label>
                                <select name="vaccination_obligatoire" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="0">Non - Recommandé</option>
                                    <option value="1">Oui - Obligatoire</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de fabrication</label>
                                <input type="date" name="date_fabrication" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date d'expiration</label>
                                <input type="date" name="date_expiration" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Âge cible min (mois)</label>
                                <input type="number" name="age_cible_min" placeholder="0" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Âge cible max (mois)</label>
                                <input type="number" name="age_cible_max" placeholder="0" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-6 flex gap-4">
                        <button type="button" data-bs-dismiss="modal" class="flex-1 px-6 py-3 border border-gray-300 rounded-xl font-semibold hover:bg-gray-50 transition">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 px-6 py-3 accent-bg text-white rounded-xl font-semibold hover:opacity-90 transition">
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
            // Search functionality
            $('#searchInput').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('#tableBody tr').each(function() {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(searchTerm));
                });
            });

            // Filter by obligatoire
            $('#filterObligatoire').on('change', function() {
                const filter = $(this).val();
                $('#tableBody tr').each(function() {
                    if (filter === '') {
                        $(this).show();
                    } else {
                        const isObligatoire = $(this).find('td:nth-child(6)').text().includes('Obligatoire');
                        $(this).toggle((filter === '1' && isObligatoire) || (filter === '0' && !isObligatoire));
                    }
                });
            });

            // Add vaccine AJAX
            $('#addVaccinForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('services.vaccinationEnfantsStore') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function() {
                        $('#addVaccinModal').modal('hide');
                        location.reload();
                    },
                    error: function() {
                        alert('Erreur lors de l\'enregistrement');
                    }
                });
            });

            // Delete vaccine AJAX
            $('.deleteForm').submit(function(e) {
                e.preventDefault();
                if (confirm('Supprimer ce vaccin ?')) {
                    const id = $(this).data('id');
                    const url = $(this).attr('action');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function() {
                            $('#row-' + id).fadeOut(300, function() {
                                $(this).remove();
                            });
                        },
                        error: function() {
                            alert('Erreur lors de la suppression');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>