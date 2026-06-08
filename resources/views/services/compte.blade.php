@extends('services.master')

@section('title', $service->nom ?? 'CongoAssist')

@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Vue d\'ensemble de votre activité')

@section('sidebar')
<div class="space-y-1">
    <a href="#" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="home" class="w-5 h-5"></i>
        <span>Tableau de bord</span>
    </a>
    <a href="{{ route('services.urgenceSignalee') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="bell" class="w-5 h-5"></i>
        <span>Urgences signalées</span>
        @if(($stats['urgences_en_cours'] ?? 0) > 0)
        <span class="ml-auto px-2 py-1 text-xs font-bold text-white accent-bg rounded-full">{{ $stats['urgences_en_cours'] }}</span>
        @endif
    </a>
    <a href="{{ route('services.citoyens') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="users" class="w-5 h-5"></i>
        <span>Citoyens</span>
    </a>
    <a href="{{ route('services.forum.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="message-square" class="w-5 h-5"></i>
        <span>Forum</span>
    </a>
    <a href="{{ route('services.actualite') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="newspaper" class="w-5 h-5"></i>
        <span>Actualités</span>
    </a>
    <a href="{{ route('services.profil') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="settings" class="w-5 h-5"></i>
        <span>Gestion interne</span>
    </a>

    @if(($service->role ?? '') === 'hopital')
    <div class="pt-4 mt-4 border-t border-gray-200">
        <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Services Médicaux</p>
        <a href="{{ route('services.vaccinationIndex') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
            <i data-feather="shield" class="w-5 h-5"></i>
            <span>Vaccinations</span>
        </a>
        <a href="{{ route('services.citoyensBilan') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
            <i data-feather="activity" class="w-5 h-5"></i>
            <span>Bilan Santé</span>
        </a>
    </div>
    @endif

    <div class="pt-4 mt-4 border-t border-gray-200">
        <a href="{{ route('services.logout') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 font-semibold hover:bg-red-50">
            <i data-feather="log-out" class="w-5 h-5"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in">
        <!-- Urgences en cours -->
        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 accent-border">
            <div class="flex items-center justify-between mb-4">
                <div class="accent-light-bg p-3 rounded-xl">
                    <i data-feather="alert-triangle" class="w-8 h-8 accent-text"></i>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['urgences_en_cours'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600 mt-1">Urgences en cours</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm">
                <span class="text-green-600 font-semibold flex items-center gap-1">
                    <i data-feather="trending-up" class="w-4 h-4"></i>
                    +12%
                </span>
                <span class="text-gray-500">vs hier</span>
            </div>
        </div>

        <!-- Urgences résolues -->
        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-100 p-3 rounded-xl">
                    <i data-feather="check-circle" class="w-8 h-8 text-green-600"></i>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['urgences_resolues'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600 mt-1">Urgences résolues</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm">
                <span class="text-green-600 font-semibold flex items-center gap-1">
                    <i data-feather="check" class="w-4 h-4"></i>
                    95%
                </span>
                <span class="text-gray-500">taux de résolution</span>
            </div>
        </div>

        <!-- Citoyens actifs -->
        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i data-feather="users" class="w-8 h-8 text-blue-600"></i>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['citoyens_actifs'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600 mt-1">Citoyens actifs</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm">
                <span class="text-blue-600 font-semibold flex items-center gap-1">
                    <i data-feather="user-plus" class="w-4 h-4"></i>
                    +8
                </span>
                <span class="text-gray-500">cette semaine</span>
            </div>
        </div>

        <!-- Temps de réponse -->
        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-100 p-3 rounded-xl">
                    <i data-feather="clock" class="w-8 h-8 text-purple-600"></i>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['temps_reponse'] ?? '—' }}</p>
                    <p class="text-sm text-gray-600 mt-1">Minutes (moy.)</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm">
                <span class="text-green-600 font-semibold flex items-center gap-1">
                    <i data-feather="trending-down" class="w-4 h-4"></i>
                    -2 min
                </span>
                <span class="text-gray-500">amélioration</span>
            </div>
        </div>
    </div>

    {{-- Main Grid: Alertes + Map --}}
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Derniers signalements -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6 animate-slide-in">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <i data-feather="bell" class="w-6 h-6 accent-text"></i>
                        Derniers signalements
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Activité en temps réel</p>
                </div>
                <button class="px-4 py-2 accent-light-bg accent-text rounded-xl font-semibold hover:opacity-80 transition flex items-center gap-2">
                    <i data-feather="filter" class="w-4 h-4"></i>
                    Filtrer
                </button>
            </div>

            <div class="space-y-3">
                @forelse($recentAlerts ?? [] as $alert)
                <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 hover:bg-gray-100 transition border-l-4 accent-border">
                    <div class="accent-light-bg p-3 rounded-xl">
                        <i data-feather="alert-circle" class="w-6 h-6 accent-text"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ $alert['title'] }}</p>
                        <div class="flex items-center gap-2 mt-1 text-sm text-gray-600">
                            <span class="flex items-center gap-1">
                                <i data-feather="tag" class="w-3 h-3"></i>
                                {{ $alert['type'] }}
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                <i data-feather="clock" class="w-3 h-3"></i>
                                {{ $alert['time'] }}
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                <i data-feather="map-pin" class="w-3 h-3"></i>
                                {{ $alert['location'] }}
                            </span>
                        </div>
                    </div>
                    <button class="px-4 py-2 accent-bg text-white rounded-lg font-semibold hover:opacity-90 transition">
                        Traiter
                    </button>
                </div>
                @empty
                <div class="text-center py-12">
                    <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-feather="inbox" class="w-10 h-10 text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-semibold mb-2">Aucun signalement récent</p>
                    <p class="text-gray-400 text-sm">Les nouvelles alertes apparaîtront ici</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Carte & Actions -->
        <div class="bg-white rounded-2xl shadow-lg p-6 animate-slide-in space-y-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                    <i data-feather="map" class="w-6 h-6 text-blue-600"></i>
                    Carte & Localisation
                </h3>
                
                <div class="h-48 bg-gradient-to-br from-blue-100 to-green-100 rounded-xl flex items-center justify-center text-gray-600">
                    <div class="text-center">
                        <i data-feather="map-pin" class="w-12 h-12 mx-auto mb-2 text-blue-500"></i>
                        <p class="font-semibold">Carte interactive</p>
                        <p class="text-sm text-gray-500">Leaflet / Google Maps</p>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-600 flex items-center gap-2">
                        <i data-feather="refresh-cw" class="w-3 h-3"></i>
                        Dernière mise à jour : {{ now()->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="space-y-3">
                <button onclick="location.reload()" class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                    <i data-feather="refresh-cw" class="w-5 h-5"></i>
                    Rafraîchir les données
                </button>

                @if(($service->role ?? '') === 'hopital')
                <button onclick="location.href='{{ route('services.vaccinationEnfantsIndex') }}'" class="w-full px-4 py-3 accent-bg text-white rounded-xl font-semibold hover:opacity-90 transition flex items-center justify-center gap-2">
                    <i data-feather="shield" class="w-5 h-5"></i>
                    Vaccination Enfants
                </button>
                @endif
            </div>

            <!-- Quick Stats -->
            <div class="border-t border-gray-200 pt-4">
                <p class="text-sm font-semibold text-gray-700 mb-3">Statistiques rapides</p>
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Interventions aujourd'hui</span>
                        <span class="font-bold text-gray-900">24</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Temps moyen</span>
                        <span class="font-bold text-gray-900">{{ $stats['temps_reponse'] ?? '—' }} min</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Taux de satisfaction</span>
                        <span class="font-bold text-green-600">98%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Chart (Optional) --}}
    <div class="bg-white rounded-2xl shadow-lg p-6 animate-fade-in">
        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-6">
            <i data-feather="trending-up" class="w-6 h-6 text-purple-600"></i>
            Activité des derniers jours
        </h3>
        <div class="h-64 bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl flex items-center justify-center text-gray-600">
            <div class="text-center">
                <i data-feather="bar-chart-2" class="w-12 h-12 mx-auto mb-2 text-purple-500"></i>
                <p class="font-semibold">Graphique d'activité</p>
                <p class="text-sm text-gray-500">Chart.js / Recharts</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Rafraîchissement automatique toutes les 30 secondes
    setInterval(() => {
        console.log('Rafraîchissement des données...');
        // Implémenter le rafraîchissement AJAX ici
    }, 30000);
</script>
@endpush
@endsection