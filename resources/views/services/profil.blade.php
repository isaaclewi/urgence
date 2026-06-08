@extends('services.master')

@section('title', 'Profil — ' . ($service->nom ?? 'CongoAssist'))

@section('page-title', 'Mon Profil')
@section('page-subtitle', 'Gérez les informations de votre service')

@section('sidebar')
<div class="space-y-1">
    <a href="{{ route('services.compte') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="home" class="w-5 h-5"></i>
        <span>Tableau de bord</span>
    </a>
    <a href="{{ route('services.profil') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold accent-light-bg accent-text">
        <i data-feather="user" class="w-5 h-5"></i>
        <span>Mon profil</span>
    </a>
    <a href="{{ route('services.urgenceSignalee') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="bell" class="w-5 h-5"></i>
        <span>Urgences signalées</span>
    </a>
    <a href="{{ route('services.citoyens') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="users" class="w-5 h-5"></i>
        <span>Citoyens</span>
    </a>
    <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="message-square" class="w-5 h-5"></i>
        <span>Forum</span>
    </a>
    <a href="{{ route('services.actualite') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="newspaper" class="w-5 h-5"></i>
        <span>Actualités</span>
    </a>
    <div class="pt-4 mt-4 border-t border-gray-200">
        <a href="{{ route('services.logout') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 font-semibold hover:bg-red-50">
            <i data-feather="log-out" class="w-5 h-5"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Success Message --}}
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg animate-fade-in flex items-center gap-3">
        <i data-feather="check-circle" class="w-5 h-5"></i>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Profile Header Card --}}
    <div class="bg-white rounded-2xl shadow-lg p-8 text-center animate-fade-in">
        <div class="relative inline-block mb-6">
            <img src="{{ asset($service->photo_profil ?? 'medias/default.jpg') }}" alt="Profil" 
                 class="w-32 h-32 rounded-full border-4 accent-border object-cover shadow-lg mx-auto">
            <div class="absolute bottom-0 right-0 w-10 h-10 accent-bg rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                <i data-feather="shield" class="w-5 h-5 text-white"></i>
            </div>
        </div>
        
        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $service->nom }}</h3>
        <p class="text-gray-600 mb-4">{{ $service->email }}</p>
        
        <div class="flex items-center justify-center gap-4">
            <span class="inline-flex items-center gap-2 px-4 py-2 accent-light-bg accent-text rounded-full text-sm font-bold">
                <i data-feather="briefcase" class="w-4 h-4"></i>
                {{ ucfirst($service->role ?? 'Service') }}
            </span>
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-bold">
                <i data-feather="check-circle" class="w-4 h-4"></i>
                {{ $service->etat_compte ?? 'Actif' }}
            </span>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-slide-in">
        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold mb-1">Interventions</p>
                    <p class="text-3xl font-bold text-gray-900">248</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i data-feather="activity" class="w-8 h-8 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold mb-1">Taux de réussite</p>
                    <p class="text-3xl font-bold text-gray-900">98%</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <i data-feather="trending-up" class="w-8 h-8 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold mb-1">Temps moyen</p>
                    <p class="text-3xl font-bold text-gray-900">12min</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-xl">
                    <i data-feather="clock" class="w-8 h-8 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-lg p-8 animate-slide-in">
        <div class="flex items-center gap-3 mb-8">
            <div class="accent-light-bg p-3 rounded-xl">
                <i data-feather="edit" class="w-6 h-6 accent-text"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">Modifier les informations</h3>
                <p class="text-sm text-gray-600">Mettez à jour les détails de votre service</p>
            </div>
        </div>

        <form action="{{ route('services.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Grid 2 columns --}}
            <div class="grid md:grid-cols-2 gap-6">
                {{-- Nom --}}
                <div>
                    <label for="nom" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="briefcase" class="w-4 h-4 accent-text"></i>
                        Nom du service <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom', $service->nom) }}" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="mail" class="w-4 h-4 accent-text"></i>
                        Adresse email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $service->email) }}"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>

                {{-- Adresse --}}
                <div>
                    <label for="adresse" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="map-pin" class="w-4 h-4 accent-text"></i>
                        Adresse
                    </label>
                    <input type="text" id="adresse" name="adresse" value="{{ old('adresse', $service->adresse) }}"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>

                {{-- Téléphone --}}
                <div>
                    <label for="telephone" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="phone" class="w-4 h-4 accent-text"></i>
                        Téléphone
                    </label>
                    <input type="text" id="telephone" name="telephone" value="{{ old('telephone', $service->telephone) }}"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
            </div>

            {{-- Photo profil --}}
            <div>
                <label for="photo_profil" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                    <i data-feather="camera" class="w-4 h-4 accent-text"></i>
                    Photo de profil
                </label>
                <div class="relative">
                    <input type="file" id="photo_profil" name="photo_profil" accept="image/*" class="hidden" onchange="updateFileName(this)">
                    <label for="photo_profil" class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl hover:border-blue-400 transition cursor-pointer flex items-center justify-center gap-2 text-gray-600 hover:text-blue-600">
                        <i data-feather="upload" class="w-5 h-5"></i>
                        <span id="fileName">Choisir une photo</span>
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-2">Formats acceptés : JPG, PNG (max 2MB)</p>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 accent-bg text-white px-6 py-4 rounded-xl font-bold text-lg hover:opacity-90 transition flex items-center justify-center gap-2 card-hover">
                    <i data-feather="save" class="w-5 h-5"></i>
                    Enregistrer les modifications
                </button>
                <a href="{{ route('services.compte') }}" class="px-6 py-4 border-2 border-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-50 transition flex items-center justify-center gap-2">
                    <i data-feather="arrow-left" class="w-5 h-5"></i>
                    Retour
                </a>
            </div>
        </form>
    </div>

    {{-- Security Card --}}
    <div class="bg-white rounded-2xl shadow-lg p-8 animate-fade-in">
        <div class="flex items-center gap-3 mb-6">
            <div class="bg-red-100 p-3 rounded-xl">
                <i data-feather="shield" class="w-6 h-6 text-red-600"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">Sécurité du compte</h3>
                <p class="text-sm text-gray-600">Gérez la sécurité de votre compte</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <i data-feather="key" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Mot de passe</p>
                        <p class="text-sm text-gray-600">Dernière modification : il y a 30 jours</p>
                    </div>
                </div>
                <button class="px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition">
                    Modifier
                </button>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="bg-green-100 p-2 rounded-lg">
                        <i data-feather="smartphone" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Authentification à deux facteurs</p>
                        <p class="text-sm text-gray-600">Sécurisez votre compte avec 2FA</p>
                    </div>
                </div>
                <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Activer
                </button>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-100 p-2 rounded-lg">
                        <i data-feather="clock" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Historique de connexion</p>
                        <p class="text-sm text-gray-600">Dernière connexion : Aujourd'hui à 14:32</p>
                    </div>
                </div>
                <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Voir
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateFileName(input) {
    const fileName = input.files[0]?.name || 'Choisir une photo';
    document.getElementById('fileName').textContent = fileName;
}

// Réinitialiser les icônes Feather
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush
@endsection