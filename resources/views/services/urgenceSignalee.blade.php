@extends('services.master')

@section('title', ($service->nom ?? 'CongoAssist') . ' — Urgences signalées')

@section('page-title', 'Urgences signalées')
@section('page-subtitle', 'Gérez et suivez toutes les alertes en temps réel')

@section('sidebar')
    <div class="space-y-1">
        <a href="{{ route('services.compte') }}"
            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
            <i data-feather="home" class="w-5 h-5"></i>
            <span>Tableau de bord</span>
        </a>
        <a href="{{ route('services.urgenceSignalee') }}"
            class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold accent-light-bg accent-text">
            <i data-feather="bell" class="w-5 h-5"></i>
            <span>Urgences signalées</span>
            @if ($alertes->where('statut', 'en attente')->count() > 0)
                <span class="ml-auto px-2 py-1 text-xs font-bold text-white bg-red-500 rounded-full">
                    {{ $alertes->where('statut', 'en attente')->count() }}
                </span>
            @endif
        </a>
        <a href="{{ route('services.citoyens') }}"
            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
            <i data-feather="users" class="w-5 h-5"></i>
            <span>Citoyens</span>
        </a>
        <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
            <i data-feather="message-square" class="w-5 h-5"></i>
            <span>Forum</span>
        </a>
        <a href="{{ route('services.actualite') }}"
            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
            <i data-feather="newspaper" class="w-5 h-5"></i>
            <span>Actualités</span>
        </a>
        <a href="{{ route('services.profil') }}"
            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
            <i data-feather="settings" class="w-5 h-5"></i>
            <span>Gestion interne</span>
        </a>
        <div class="pt-4 mt-4 border-t border-gray-200">
            <a href="{{ route('services.logout') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 font-semibold hover:bg-red-50">
                <i data-feather="log-out" class="w-5 h-5"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Success Message --}}
        @if (session('success'))
            <div
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg animate-fade-in flex items-center gap-3">
                <i data-feather="check-circle" class="w-5 h-5"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 animate-fade-in">
            <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">En attente</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $alertes->where('statut', 'en attente')->count() }}
                        </p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-xl">
                        <i data-feather="alert-circle" class="w-8 h-8 text-red-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">En cours</p>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ $alertes->where('statut', 'pris en charge')->count() }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-xl">
                        <i data-feather="clock" class="w-8 h-8 text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Terminées</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $alertes->where('statut', 'terminee')->count() }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-xl">
                        <i data-feather="check-circle" class="w-8 h-8 text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Total</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $alertes->count() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-xl">
                        <i data-feather="list" class="w-8 h-8 text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-slide-in">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="accent-light-bg p-3 rounded-xl">
                        <i data-feather="alert-triangle" class="w-6 h-6 accent-text"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Liste des urgences</h3>
                        <p class="text-sm text-gray-600">{{ $alertes->count() }} alerte(s) au total</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="accent-light-bg">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase accent-text">
                                <div class="flex items-center gap-2">
                                    <i data-feather="hash" class="w-4 h-4"></i>
                                    ID
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase accent-text">
                                <div class="flex items-center gap-2">
                                    <i data-feather="file-text" class="w-4 h-4"></i>
                                    Titre
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase accent-text">
                                <div class="flex items-center gap-2">
                                    <i data-feather="tag" class="w-4 h-4"></i>
                                    Type
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase accent-text">
                                <div class="flex items-center gap-2">
                                    <i data-feather="map-pin" class="w-4 h-4"></i>
                                    Localisation
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase accent-text">
                                <div class="flex items-center gap-2">
                                    <i data-feather="calendar" class="w-4 h-4"></i>
                                    Date
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase accent-text">
                                <div class="flex items-center justify-center gap-2">
                                    <i data-feather="user" class="w-4 h-4"></i>
                                    Citoyen
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase accent-text">
                                <div class="flex items-center justify-center gap-2">
                                    <i data-feather="info" class="w-4 h-4"></i>
                                    Statut
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase accent-text" colspan="2">
                                <div class="flex items-center justify-center gap-2">
                                    <i data-feather="settings" class="w-4 h-4"></i>
                                    Action
                                </div>
                            </th>

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($alertes as $alerte)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-900">#{{ $alerte->id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $alerte->titre }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                                        {{ $alerte->type_alerte }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 text-gray-700">
                                        <i data-feather="map-pin" class="w-4 h-4 text-red-500"></i>
                                        <span class="text-sm">{{ $alerte->localisation }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($alerte->created_at)->format('d/m/Y') }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($alerte->created_at)->format('H:i') }}
                                        </span>
                                    </div>
                                <td class="px-6 py-4 text-center">
                                    @if ($alerte->citoyen)
                                        <button
                                            onclick="showModal(
                '{{ $alerte->citoyen->nom }}',
                '{{ $alerte->citoyen->prenom }}',
                '{{ $alerte->citoyen->email }}',
                '{{ $alerte->citoyen->telephone }}',
                '{{ $alerte->citoyen->adresse ?? 'Adresse non renseignée' }}',
                '{{ asset($alerte->citoyen->photo_profil ?? 'medias/default.jpg') }}'
            )"
                                            class="px-4 py-2 accent-bg text-white rounded-lg font-semibold hover:opacity-90 transition flex items-center justify-center gap-2 mx-auto">
                                            <i data-feather="eye" class="w-4 h-4"></i>
                                            Voir
                                        </button>
                                    @else
                                        <span class="text-gray-400 italic text-sm">Anonyme</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($alerte->statut == 'en attente')
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">
                                            <i data-feather="alert-circle" class="w-3 h-3"></i>
                                            En attente
                                        </span>
                                    @elseif($alerte->statut == 'pris en charge')
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">
                                            <i data-feather="clock" class="w-3 h-3"></i>
                                            Pris en charge
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                            <i data-feather="check-circle" class="w-3 h-3"></i>
                                            Terminée
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
    <button
        onclick="showAlerteModal(
            '{{ addslashes($alerte->titre) }}',
            '{{ addslashes($alerte->description) }}',
            '{{ $alerte->media_photo ? asset($alerte->media_photo) : '' }}',
            '{{ $alerte->media_vocal ? asset($alerte->media_vocal) : '' }}'
        )"
        class="px-4 py-2 accent-bg text-white rounded-lg font-semibold hover:opacity-90 transition flex items-center justify-center gap-2 mx-auto">
        <i data-feather="eye" class="w-4 h-4"></i>
        Voir
    </button>
</td>


                                <td class="px-6 py-4">
                                    <form method="POST"
                                        action="{{ route('services.urgenceSignaleeUpdate', $alerte->id) }}">
                                        @csrf
                                        <select name="statut" onchange="this.form.submit()"
                                            class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition text-sm font-semibold">
                                            <option value="en attente"
                                                {{ $alerte->statut == 'en attente' ? 'selected' : '' }}>En attente</option>
                                            <option value="pris en charge"
                                                {{ $alerte->statut == 'pris en charge' ? 'selected' : '' }}>Pris en charge
                                            </option>
                                            <option value="terminee"
                                                {{ $alerte->statut == 'terminee' ? 'selected' : '' }}>Terminée</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div
                                        class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i data-feather="inbox" class="w-10 h-10 text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 font-semibold mb-2">Aucune alerte signalée</p>
                                    <p class="text-gray-400 text-sm">Les nouvelles urgences apparaîtront ici</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Citoyen --}}
    <div id="citoyenModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md animate-fade-in">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i data-feather="user" class="w-6 h-6 accent-text"></i>
                    Informations Citoyen
                </h3>
                <button onclick="closeModal()"
                    class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition">
                    <i data-feather="x" class="w-5 h-5 text-gray-600"></i>
                </button>
            </div>

            <div class="p-6 text-center">
                <div class="relative inline-block mb-4">
                    <img id="photoCitoyen" src="" alt="Photo citoyen"
                        class="w-24 h-24 rounded-full border-4 border-blue-500 object-cover shadow-lg mx-auto">
                </div>

                <h4 id="nomComplet" class="text-2xl font-bold text-gray-900 mb-6"></h4>

                <div class="space-y-3 text-left">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <i data-feather="mail" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold">Email</p>
                            <p id="emailCitoyen" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <i data-feather="phone" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold">Téléphone</p>
                            <p id="telCitoyen" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="bg-purple-100 p-2 rounded-lg">
                            <i data-feather="map-pin" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold">Adresse</p>
                            <p id="adresseCitoyen" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-100">
                <button onclick="closeModal()"
                    class="w-full accent-bg text-white px-6 py-3 rounded-xl font-bold hover:opacity-90 transition">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Alerte --}}
    <div id="alerteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg animate-fade-in">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i data-feather="alert-triangle" class="w-6 h-6 accent-text"></i>
                Informations de l'alerte
            </h3>
            <button onclick="closeAlerteModal()"
                class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition">
                <i data-feather="x" class="w-5 h-5 text-gray-600"></i>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <h4 id="alerteTitre" class="text-2xl font-bold text-gray-900"></h4>
            <p id="alerteDescription" class="text-gray-700 text-sm"></p>

            <div id="alertePhotoContainer" class="w-full flex justify-center">
                <img id="alertePhoto" src="" alt="Photo alerte" class="rounded-xl max-h-64 object-cover hidden">
            </div>

            <div id="alerteVocalContainer" class="w-full flex justify-center">
                <audio id="alerteVocal" controls class="w-full mt-2 hidden">
                    <source id="alerteVocalSource" src="" type="audio/webm">
                    Votre navigateur ne supporte pas l'audio.
                </audio>
            </div>
        </div>

        <div class="p-6 border-t border-gray-100">
            <button onclick="closeAlerteModal()"
                class="w-full accent-bg text-white px-6 py-3 rounded-xl font-bold hover:opacity-90 transition">
                Fermer
            </button>
        </div>
    </div>
</div>



    @push('scripts')
        <script>
function showAlerteModal(titre, description, photo, vocal) {
    // Texte
    document.getElementById('alerteTitre').textContent = titre;
    document.getElementById('alerteDescription').textContent = description;

    // Image
    const photoEl = document.getElementById('alertePhoto');
    if(photo) {
        photoEl.src = photo;
        photoEl.classList.remove('hidden');
    } else {
        photoEl.classList.add('hidden');
    }

    // Audio
    const vocalEl = document.getElementById('alerteVocal');
    const vocalSource = document.getElementById('alerteVocalSource');
    if(vocal) {
        vocalSource.src = vocal;
        vocalEl.load(); // recharge la source
        vocalEl.classList.remove('hidden');
    } else {
        vocalEl.classList.add('hidden');
    }

    // Afficher modal
    const modal = document.getElementById('alerteModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    feather.replace();
}

function closeAlerteModal() {
    const modal = document.getElementById('alerteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');

    const vocalEl = document.getElementById('alerteVocal');
    vocalEl.pause();
    vocalEl.currentTime = 0;
}

function showModal(nom, prenom, email, telephone, adresse, photo) {
    document.getElementById('nomComplet').textContent = nom + ' ' + prenom;
    document.getElementById('emailCitoyen').textContent = email;
    document.getElementById('telCitoyen').textContent = telephone;
    document.getElementById('adresseCitoyen').textContent = adresse;
    document.getElementById('photoCitoyen').src = photo;

    const modal = document.getElementById('citoyenModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    feather.replace(); // Pour les icônes Feather
}

function closeModal() {
    const modal = document.getElementById('citoyenModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

</script>

    @endpush
@endsection
