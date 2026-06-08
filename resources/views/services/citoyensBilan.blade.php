@extends('services.master')

@section('title', ($service->nom ?? 'Service Urgences') . ' — Bilan de Santé')

@section('page-title', 'Gestion des Bilans de Santé')
@section('page-subtitle', 'Créez et consultez les bilans de santé des citoyens')

@section('sidebar')
<div class="space-y-1">
    <a href="{{ route('services.compte') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="home" class="w-5 h-5"></i>
        <span>Tableau de bord</span>
    </a>
    <a href="{{ route('services.citoyens') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="users" class="w-5 h-5"></i>
        <span>Citoyens</span>
    </a>
    <a href="{{ route('services.citoyensBilan') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold accent-light-bg accent-text">
        <i data-feather="activity" class="w-5 h-5"></i>
        <span>Bilan de Santé</span>
    </a>
    <a href="{{ route('services.vaccinationIndex') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
        <i data-feather="shield" class="w-5 h-5"></i>
        <span>Vaccination</span>
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
    {{-- Success Message --}}
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg animate-fade-in flex items-center gap-3">
        <i data-feather="check-circle" class="w-5 h-5"></i>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 animate-fade-in">
        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold mb-1">Total bilans</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $bilans->count() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i data-feather="file-text" class="w-8 h-8 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold mb-1">Citoyens</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $citoyens->count() }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <i data-feather="users" class="w-8 h-8 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold mb-1">Allergies</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $bilans->whereNotNull('allergies')->count() }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-xl">
                    <i data-feather="alert-circle" class="w-8 h-8 text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold mb-1">Chroniques</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $bilans->whereNotNull('maladies_chroniques')->count() }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-xl">
                    <i data-feather="trending-up" class="w-8 h-8 text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-lg p-6 animate-slide-in">
        <div class="flex items-center gap-3 mb-6">
            <div class="accent-light-bg p-3 rounded-xl">
                <i data-feather="file-plus" class="w-6 h-6 accent-text"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">Créer un Bilan de Santé</h3>
                <p class="text-sm text-gray-600">Remplissez les informations médicales du citoyen</p>
            </div>
        </div>

        <form action="{{ route('services.citoyensBilanStore') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Search Citoyen --}}
            <div>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                    <i data-feather="search" class="w-4 h-4 accent-text"></i>
                    Rechercher un citoyen par matricule
                </label>
                <input type="text" id="citoyenSearch" placeholder="Tapez le numéro matricule..."
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            </div>

            {{-- Select Citoyen --}}
            <div>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                    <i data-feather="user" class="w-4 h-4 accent-text"></i>
                    Citoyen <span class="text-red-500">*</span>
                </label>
                <select name="citoyen_id" id="citoyenSelect" required
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                    <option value="">-- Sélectionner un citoyen --</option>
                    @foreach($citoyens as $citoyen)
                    <option value="{{ $citoyen->id }}" data-matricule="{{ $citoyen->matricule }}">
                        {{ $citoyen->matricule }} — {{ $citoyen->nom }} {{ $citoyen->prenom }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Grid 2 columns --}}
            <div class="grid md:grid-cols-2 gap-6">
                {{-- Groupe sanguin --}}
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="droplet" class="w-4 h-4 text-red-500"></i>
                        Groupe sanguin
                    </label>
                    <input type="text" name="groupe_sanguin" placeholder="Ex: A+"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>

                {{-- Taille --}}
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="maximize-2" class="w-4 h-4 text-blue-500"></i>
                        Taille (cm)
                    </label>
                    <input type="number" name="taille" placeholder="Ex: 175"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>

                {{-- Poids --}}
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="award" class="w-4 h-4 text-green-500"></i>
                        Poids (kg)
                    </label>
                    <input type="number" name="poids" placeholder="Ex: 70"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>

                {{-- Allergies --}}
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="alert-triangle" class="w-4 h-4 text-orange-500"></i>
                        Allergies
                    </label>
                    <input type="text" name="allergies" placeholder="Ex: Arachides, Pénicilline"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>
            </div>

            {{-- Full width fields --}}
            <div class="space-y-4">
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="heart" class="w-4 h-4 text-red-500"></i>
                        Maladies chroniques
                    </label>
                    <input type="text" name="maladies_chroniques" placeholder="Ex: Diabète, Hypertension"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="clock" class="w-4 h-4 text-purple-500"></i>
                        Maladies passées importantes
                    </label>
                    <input type="text" name="maladies_passees_importantes" placeholder="Ex: Tuberculose (2020)"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="scissors" class="w-4 h-4 text-teal-500"></i>
                        Interventions chirurgicales
                    </label>
                    <textarea name="interventions_chirurgicales" rows="3" placeholder="Décrivez les opérations subies..."
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"></textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="users" class="w-4 h-4 text-indigo-500"></i>
                        Antécédents familiaux
                    </label>
                    <textarea name="antecedents_familiaux" rows="3" placeholder="Ex: Père diabétique, Mère cardiaque..."
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"></textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="home" class="w-4 h-4 text-pink-500"></i>
                        Antécédents d'hospitalisation
                    </label>
                    <textarea name="antecedents_hospitalisation" rows="3" placeholder="Listez les hospitalisations..."
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"></textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="package" class="w-4 h-4 text-cyan-500"></i>
                        Médicaments pris actuellement
                    </label>
                    <textarea name="medicaments_pris_actuellement" rows="3" placeholder="Ex: Aspirine 100mg/jour..."
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"></textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="shield" class="w-4 h-4 text-green-500"></i>
                        Liste des vaccins reçus
                    </label>
                    <textarea name="listez_vaccins_reçus" rows="3" placeholder="Ex: BCG, DTC, Hépatite B..."
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"></textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="coffee" class="w-4 h-4 text-amber-500"></i>
                        Mode de vie
                    </label>
                    <textarea name="mode_de_vie" rows="3" placeholder="Ex: Non-fumeur, Sport régulier..."
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"></textarea>
                </div>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="w-full accent-bg text-white px-6 py-4 rounded-xl font-bold text-lg hover:opacity-90 transition flex items-center justify-center gap-2 card-hover">
                <i data-feather="save" class="w-5 h-5"></i>
                Enregistrer le Bilan
            </button>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-fade-in">
        <div class="p-6 border-b border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="accent-light-bg p-3 rounded-xl">
                        <i data-feather="clipboard" class="w-6 h-6 accent-text"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Bilans enregistrés</h3>
                        <p class="text-sm text-gray-600">{{ $bilans->count() }} bilan(s) au total</p>
                    </div>
                </div>
            </div>

            <input type="text" id="tableSearch" placeholder="Rechercher dans le tableau..."
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
        </div>

        <div class="overflow-x-auto">
            <table id="bilansTable" class="w-full">
                <thead class="accent-light-bg">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase accent-text">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase accent-text">Citoyen</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase accent-text">Groupe</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase accent-text">Taille</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase accent-text">Poids</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase accent-text">Allergies</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase accent-text">Chroniques</th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase accent-text">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bilans as $bilan)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-semibold text-gray-900">#{{ $bilan->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-green-400 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($bilan->citoyen->nom ?? '', 0, 1)) }}{{ strtoupper(substr($bilan->citoyen->prenom ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $bilan->citoyen->nom ?? '' }} {{ $bilan->citoyen->prenom ?? '' }}</p>
                                    <p class="text-xs text-gray-500">{{ $bilan->citoyen->matricule ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-bold">{{ $bilan->groupe_sanguin ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $bilan->taille ?? '—' }} cm</td>
                        <td class="px-6 py-4 text-gray-700">{{ $bilan->poids ?? '—' }} kg</td>
                        <td class="px-6 py-4 text-gray-700 text-sm">{{ Str::limit($bilan->allergies ?? '—', 30) }}</td>
                        <td class="px-6 py-4 text-gray-700 text-sm">{{ Str::limit($bilan->maladies_chroniques ?? '—', 30) }}</td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('services.citoyensBilanDestroy', $bilan->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce bilan ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition mx-auto">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-feather="inbox" class="w-10 h-10 text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-semibold">Aucun bilan enregistré</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Filtre pour le select des citoyens
const citoyenSearch = document.getElementById('citoyenSearch');
const citoyenSelect = document.getElementById('citoyenSelect');

citoyenSearch.addEventListener('input', function() {
    const search = this.value.toLowerCase();
    Array.from(citoyenSelect.options).forEach(option => {
        if (option.value === "") return;
        const matricule = option.getAttribute('data-matricule')?.toLowerCase() || '';
        option.style.display = matricule.includes(search) ? '' : 'none';
    });
});

// Filtre pour le tableau
const tableSearch = document.getElementById('tableSearch');
const bilansTable = document.getElementById('bilansTable').getElementsByTagName('tbody')[0];

tableSearch.addEventListener('input', function() {
    const search = this.value.toLowerCase();
    Array.from(bilansTable.rows).forEach(row => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(search) ? '' : 'none';
    });
});

// Réinitialiser les icônes Feather
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush
@endsection