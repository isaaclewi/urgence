@extends('services.master')

@section('title', 'Espaces de discussion')

@section('page-title', 'Forum')
@section('page-subtitle', 'Espaces de discussion et échanges communautaires')


@section('sidebar')
<div class="space-y-1">
    <a href="{{ route('services.compte') }}" class="sidebar-link {{ request()->routeIs('services.compte') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
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
    {{-- Stats Card --}}
    <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 accent-border animate-fade-in">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="accent-light-bg p-4 rounded-xl">
                    <i data-feather="message-square" class="w-8 h-8 accent-text"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Espaces de discussion</h3>
                    <p class="text-sm text-gray-600 mt-1">Rejoignez la conversation et échangez avec la communauté</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-gray-900">{{ count($spaces) }}</p>
                <p class="text-sm text-gray-600">{{ count($spaces) > 1 ? 'Espaces' : 'Espace' }}</p>
            </div>
        </div>
    </div>

    {{-- Liste des espaces --}}
    <div class="grid grid-cols-1 gap-4 animate-slide-in">
        @forelse($spaces as $space)
            <a href="{{ route('services.forum.group', $space->id) }}" class="block group">
                <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border border-gray-100 transition-all">
                    <div class="flex items-center gap-4">
                        
                        {{-- Avatar --}}
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl {{ $space->type === 'public' ? 'bg-gradient-to-br from-green-400 to-green-600' : 'bg-gradient-to-br from-blue-400 to-blue-600' }} shadow-lg">
                                {{ $space->type === 'public' ? '🌐' : '🔒' }}
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-lg font-bold text-gray-900 group-hover:accent-text transition-colors">
                                    {{ $space->title }}
                                </h4>
                                <i data-feather="arrow-right" class="w-5 h-5 text-gray-400 group-hover:accent-text group-hover:translate-x-1 transition-all"></i>
                            </div>
                            
                            <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                                {{ \Illuminate\Support\Str::limit($space->description, 120) }}
                            </p>

                            {{-- Badges --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-semibold">
                                    <i data-feather="{{ $space->type === 'public' ? 'globe' : 'lock' }}" class="w-3.5 h-3.5"></i>
                                    {{ strtoupper($space->type) }}
                                </span>

                                @if($space->service)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold">
                                        <i data-feather="briefcase" class="w-3.5 h-3.5"></i>
                                        {{ $space->service->nom }}
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <div class="bg-gray-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-feather="message-square" class="w-12 h-12 text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun espace disponible</h3>
                <p class="text-gray-600">Les espaces de discussion seront bientôt disponibles.</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    // Initialiser les icônes Feather
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
</script>
@endpush
@endsection
