@extends('services.master')

@section('title', $service->nom ?? 'CongoAssist')

@section('page-title', 'Actualités')
@section('page-subtitle', 'Gérez et publiez les actualités de votre service')

@section('sidebar')
<div class="space-y-1">
    <a href="{{ route('services.compte') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="home" class="w-5 h-5"></i>
        <span>Tableau de bord</span>
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
    <a href="{{ route('services.actualite') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold accent-light-bg accent-text">
        <i data-feather="newspaper" class="w-5 h-5"></i>
        <span>Actualités</span>
    </a>
    <a href="{{ route('services.profil') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="settings" class="w-5 h-5"></i>
        <span>Gestion interne</span>
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
<div class="space-y-6">
    {{-- Header avec bouton --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 animate-fade-in">
        <div class="flex items-center gap-4">
            <div class="accent-light-bg p-3 rounded-xl">
                <i data-feather="newspaper" class="w-8 h-8 accent-text"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Fil d'actualités</h2>
                <p class="text-gray-600 text-sm">{{ $actualites->count() }} publication(s)</p>
            </div>
        </div>
        <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="accent-bg text-white px-6 py-3 rounded-xl font-semibold hover:opacity-90 transition flex items-center gap-2 card-hover">
            <i data-feather="plus" class="w-5 h-5"></i>
            Nouvelle actualité
        </button>
    </div>

    {{-- Feed d'actualités --}}
    <div class="space-y-6 animate-slide-in">
        @forelse($actualites as $actu)
        <div class="bg-white rounded-2xl overflow-hidden shadow-lg card-hover">
            {{-- Media --}}
            @if($actu->url_media)
            <div class="relative bg-gray-100 flex items-center justify-center" style="max-height: 400px; overflow: hidden;">
                @if($actu->type_media === 'mp4')
                <video controls class="w-full h-auto">
                    <source src="{{ $actu->url_media }}" type="video/mp4">
                </video>
                @else
                <img src="{{ $actu->url_media }}" alt="Actualité" class="w-full h-auto object-cover">
                @endif
            </div>
            @else
            <div class="relative bg-gradient-to-br from-blue-100 to-green-100 h-48 flex items-center justify-center">
                <i data-feather="image" class="w-16 h-16 text-gray-400"></i>
            </div>
            @endif

            {{-- Contenu --}}
            <div class="p-6">
                {{-- Header de l'actualité --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 accent-light-bg rounded-full flex items-center justify-center">
                            <i data-feather="user" class="w-5 h-5 accent-text"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $actu->auteur_nom }}</h4>
                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                <i data-feather="calendar" class="w-3 h-3"></i>
                                {{ \Carbon\Carbon::parse($actu->date_publication)->format('d M Y à H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('services.actualiteEdit', $actu->id) }}" class="w-9 h-9 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg flex items-center justify-center transition">
                            <i data-feather="edit-2" class="w-4 h-4"></i>
                        </a>
                        <form action="{{ route('services.actualiteDestroy', $actu->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-9 h-9 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition">
                                <i data-feather="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Contenu texte --}}
                <div class="prose max-w-none">
                    <p class="text-gray-700 leading-relaxed contenu-{{ $actu->id }}" data-full="{{ $actu->contenu }}" data-truncated="{{ Str::limit($actu->contenu, 200) }}">
                        {{ Str::limit($actu->contenu, 200) }}
                    </p>
                </div>

                @if(strlen($actu->contenu) > 200)
                <button onclick="toggleContent({{ $actu->id }})" class="mt-3 accent-text font-semibold text-sm hover:underline flex items-center gap-1 btn-voir-plus-{{ $actu->id }}">
                    <span>Voir plus</span>
                    <i data-feather="chevron-down" class="w-4 h-4"></i>
                </button>
                @endif

                {{-- Stats (optionnel) --}}
                <div class="flex items-center gap-6 mt-6 pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-2 text-gray-600">
                        <i data-feather="eye" class="w-4 h-4"></i>
                        <span class="text-sm font-semibold">248 vues</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <i data-feather="heart" class="w-4 h-4"></i>
                        <span class="text-sm font-semibold">32 likes</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <i data-feather="message-circle" class="w-4 h-4"></i>
                        <span class="text-sm font-semibold">12 commentaires</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-feather="newspaper" class="w-10 h-10 text-gray-400"></i>
            </div>
            <p class="text-gray-500 text-lg font-semibold mb-2">Aucune actualité disponible</p>
            <p class="text-gray-400 mb-6">Commencez par publier votre première actualité</p>
            <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="accent-bg text-white px-6 py-3 rounded-xl font-semibold hover:opacity-90 transition inline-flex items-center gap-2">
                <i data-feather="plus" class="w-5 h-5"></i>
                Créer une actualité
            </button>
        </div>
        @endforelse
    </div>
</div>

{{-- MODAL AJOUT --}}
<div id="modalAdd" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg animate-fade-in">
        {{-- Header Modal --}}
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i data-feather="newspaper" class="w-6 h-6 accent-text"></i>
                Ajouter une actualité
            </h3>
            <button onclick="document.getElementById('modalAdd').classList.add('hidden')" class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition">
                <i data-feather="x" class="w-5 h-5 text-gray-600"></i>
            </button>
        </div>

        {{-- Form --}}
        <form action="{{ route('services.actualiteStore') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf

            {{-- Auteur --}}
            <div>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                    <i data-feather="user" class="w-4 h-4 accent-text"></i>
                    Auteur
                </label>
                <input type="text" name="auteur_nom" value="{{ $service->nom }}" readonly class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none">
            </div>

            {{-- Contenu --}}
            <div>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                    <i data-feather="file-text" class="w-4 h-4 accent-text"></i>
                    Contenu
                </label>
                <textarea name="contenu" required rows="5" placeholder="Écrivez votre actualité ici..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"></textarea>
            </div>

            {{-- Media --}}
            <div>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                    <i data-feather="image" class="w-4 h-4 accent-text"></i>
                    Image ou vidéo (optionnel)
                </label>
                <div class="relative">
                    <input type="file" name="url_media" accept="image/*,video/*" id="fileInput" class="hidden" onchange="updateFileName(this)">
                    <label for="fileInput" class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl hover:border-blue-400 transition cursor-pointer flex items-center justify-center gap-2 text-gray-600 hover:text-blue-600">
                        <i data-feather="upload" class="w-5 h-5"></i>
                        <span id="fileName">Choisir un fichier</span>
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-2">Formats acceptés : JPG, PNG, MP4 (max 10MB)</p>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 accent-bg text-white px-6 py-3 rounded-xl font-semibold hover:opacity-90 transition flex items-center justify-center gap-2">
                    <i data-feather="send" class="w-5 h-5"></i>
                    Publier
                </button>
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="px-6 py-3 border-2 border-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleContent(id) {
        const p = document.querySelector(`.contenu-${id}`);
        const btn = document.querySelector(`.btn-voir-plus-${id} span`);
        const icon = document.querySelector(`.btn-voir-plus-${id} i`);

        if (btn.textContent === 'Voir plus') {
            p.textContent = p.dataset.full;
            btn.textContent = 'Voir moins';
            icon.setAttribute('data-feather', 'chevron-up');
        } else {
            p.textContent = p.dataset.truncated;
            btn.textContent = 'Voir plus';
            icon.setAttribute('data-feather', 'chevron-down');
        }
        feather.replace();
    }

    function updateFileName(input) {
        const fileName = input.files[0]?.name || 'Choisir un fichier';
        document.getElementById('fileName').textContent = fileName;
    }

    // Réinitialiser les icônes Feather après chargement
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
@endsection
