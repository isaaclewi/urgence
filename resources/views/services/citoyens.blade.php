@extends('services.master')

@section('title', 'Citoyens — ' . ($service->nom ?? 'CongoAssist'))

@section('page-title', 'Citoyens actifs')
@section('page-subtitle', 'Liste des utilisateurs inscrits sur la plateforme')

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
    <a href="{{ route('services.citoyens') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold accent-light-bg accent-text">
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
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 animate-fade-in">
        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold mb-1">Total citoyens</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $citoyens->total() }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <i data-feather="users" class="w-8 h-8 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold mb-1">Comptes actifs</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $citoyens->where('etat_compte', 'actif')->count() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i data-feather="user-check" class="w-8 h-8 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold mb-1">Hommes</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $citoyens->where('sexe', 'M')->count() }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-xl">
                    <i data-feather="user" class="w-8 h-8 text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg card-hover border-l-4 border-pink-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold mb-1">Femmes</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $citoyens->where('sexe', 'F')->count() }}</p>
                </div>
                <div class="bg-pink-100 p-3 rounded-xl">
                    <i data-feather="user" class="w-8 h-8 text-pink-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-slide-in">
        {{-- Table Header --}}
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="accent-light-bg p-3 rounded-xl">
                    <i data-feather="users" class="w-6 h-6 accent-text"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Liste des citoyens</h3>
                    <p class="text-sm text-gray-600">{{ $citoyens->where('etat_compte', 'actif')->count() }} comptes actifs</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition flex items-center gap-2">
                    <i data-feather="filter" class="w-4 h-4"></i>
                    Filtrer
                </button>
                <button class="px-4 py-2 accent-bg text-white rounded-xl font-semibold hover:opacity-90 transition flex items-center gap-2">
                    <i data-feather="download" class="w-4 h-4"></i>
                    Exporter
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table id="citoyensTable" class="w-full">
                <thead class="accent-light-bg">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider accent-text">
                            <div class="flex items-center gap-2">
                                <i data-feather="hash" class="w-4 h-4"></i>
                                ID
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider accent-text">
                            <div class="flex items-center gap-2">
                                <i data-feather="user" class="w-4 h-4"></i>
                                Noms et Prénoms
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider accent-text">
                            <div class="flex items-center gap-2">
                                <i data-feather="mail" class="w-4 h-4"></i>
                                Email
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider accent-text">
                            <div class="flex items-center gap-2">
                                <i data-feather="map-pin" class="w-4 h-4"></i>
                                Adresse
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider accent-text">
                            <div class="flex items-center gap-2">
                                <i data-feather="phone" class="w-4 h-4"></i>
                                Contact
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider accent-text">
                            <div class="flex items-center gap-2">
                                <i data-feather="credit-card" class="w-4 h-4"></i>
                                Matricule
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider accent-text">
                            <div class="flex items-center justify-center gap-2">
                                <i data-feather="check-circle" class="w-4 h-4"></i>
                                État
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider accent-text">
                            <div class="flex items-center justify-center gap-2">
                                <i data-feather="users" class="w-4 h-4"></i>
                                Genre
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider accent-text">
                            <div class="flex items-center justify-center gap-2">
                                <i data-feather="calendar" class="w-4 h-4"></i>
                                Inscription
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($citoyens->where('etat_compte', 'actif') as $c)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold text-gray-900">#{{ $c->id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-green-400 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($c->nom, 0, 1)) }}{{ strtoupper(substr($c->prenom, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $c->nom }} {{ $c->prenom }}</p>
                                    <p class="text-xs text-gray-500">Citoyen</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-gray-700">
                                <i data-feather="mail" class="w-4 h-4 text-gray-400"></i>
                                <span class="text-sm">{{ $c->email }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-gray-700">
                                <i data-feather="map-pin" class="w-4 h-4 text-gray-400"></i>
                                <span class="text-sm">{{ $c->adresse }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-gray-700">
                                <i data-feather="phone" class="w-4 h-4 text-gray-400"></i>
                                <span class="text-sm">{{ $c->telephone }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm font-mono">{{ $c->matricule }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                <i data-feather="check" class="w-3 h-3"></i>
                                Activé
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($c->sexe === 'M')
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                                <i data-feather="user" class="w-3 h-3"></i>
                                M
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-xs font-bold">
                                <i data-feather="user" class="w-3 h-3"></i>
                                F
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-semibold text-gray-900">{{ $c->created_at->format('d/m/Y') }}</span>
                                <span class="text-xs text-gray-500">{{ $c->created_at->format('H:i') }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-6 border-t border-gray-100">
            {!! $citoyens->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#citoyensTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 15, 25, 50],
        "responsive": true,
        "autoWidth": false,
        "order": [[0, 'desc']],
        "language": {
            "search": "Rechercher :",
            "lengthMenu": "Afficher _MENU_ citoyens",
            "paginate": {
                "first": "Début",
                "last": "Fin",
                "next": "Suivant",
                "previous": "Précédent"
            },
            "info": "Affichage _START_ à _END_ sur _TOTAL_ citoyens",
            "infoEmpty": "Aucun citoyen",
            "infoFiltered": "(filtré depuis _MAX_ total)",
            "zeroRecords": "Aucun citoyen trouvé"
        },
        "columnDefs": [
            { "orderable": false, "targets": [6] }
        ],
        "drawCallback": function() {
            feather.replace();
        }
    });

    // Réinitialiser les icônes après le chargement
    feather.replace();
});
</script>
@endpush
@endsection