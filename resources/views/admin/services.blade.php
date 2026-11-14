@extends('layouts.portal')

@section('title', 'Services d\'urgence')
@section('page-title', 'Services d\'urgence')
@section('page-subtitle', 'Créez et gérez les comptes des services (pompiers, police, hôpitaux)')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('admin.serviceCreate') }}">Services proposés aux citoyens</a>
@endsection

@section('content')
<x-card title="Ajouter un service d'urgence" class="ds-section">
    <form action="{{ route('admin.services.store') }}" method="POST">
        @csrf
        <div class="ds-form-grid">
            <div class="ds-field"><label class="ds-label" for="nom">Nom du service *</label>
                <input class="ds-input" id="nom" type="text" name="nom" value="{{ old('nom') }}" required></div>
            <div class="ds-field"><label class="ds-label" for="adresse">Adresse *</label>
                <input class="ds-input" id="adresse" type="text" name="adresse" value="{{ old('adresse') }}" required></div>
            <div class="ds-field"><label class="ds-label" for="role">Type de service *</label>
                <select class="ds-select" id="role" name="role" required>
                    <option value="">Choisir…</option>
                    <option value="pompier" @selected(old('role') === 'pompier')>Sapeurs-pompiers</option>
                    <option value="police"  @selected(old('role') === 'police')>Police</option>
                    <option value="hopital" @selected(old('role') === 'hopital')>Hôpital</option>
                </select></div>
            <div class="ds-field"><label class="ds-label" for="disponible">Disponibilité *</label>
                <select class="ds-select" id="disponible" name="disponible" required>
                    <option value="1" @selected(old('disponible', '1') === '1')>Disponible</option>
                    <option value="0" @selected(old('disponible') === '0')>Non disponible</option>
                </select></div>
            <div class="ds-field"><label class="ds-label" for="email">Email (identifiant de connexion)</label>
                <input class="ds-input" id="email" type="email" name="email" value="{{ old('email') }}"></div>
            <div class="ds-field"><label class="ds-label" for="telephone">Téléphone</label>
                <input class="ds-input" id="telephone" type="text" name="telephone" value="{{ old('telephone') }}"></div>
            <div class="ds-field"><label class="ds-label" for="etat_compte">État du compte *</label>
                <select class="ds-select" id="etat_compte" name="etat_compte" required>
                    <option value="actif"   @selected(old('etat_compte', 'actif') === 'actif')>Actif</option>
                    <option value="inactif" @selected(old('etat_compte') === 'inactif')>Inactif (connexion bloquée)</option>
                </select></div>
            <div class="ds-field"><label class="ds-label" for="password">Mot de passe *</label>
                <input class="ds-input" id="password" type="password" name="password" minlength="6" autocomplete="new-password" required></div>
        </div>
        <button class="ds-btn ds-btn-primary" type="submit">Enregistrer le service</button>
    </form>
</x-card>

<x-card :flush="true" title="Services enregistrés ({{ $services->count() }})">
    @if($services->isEmpty())
        <x-empty title="Aucun service enregistré">Utilisez le formulaire ci-dessus pour en ajouter un.</x-empty>
    @else
        <x-table>
            <thead>
                <tr><th>Service</th><th>Type</th><th>Adresse</th><th>Contact</th><th>Disponibilité</th><th>Compte</th><th></th></tr>
            </thead>
            <tbody>
            @foreach($services as $service)
                <tr>
                    <td><strong>{{ $service->nom }}</strong><div class="ds-muted" style="font-size:12px">{{ $service->email ?? '—' }}</div></td>
                    <td>{{ ['pompier' => 'Sapeurs-pompiers', 'police' => 'Police', 'hopital' => 'Hôpital'][$service->role] ?? ucfirst($service->role) }}</td>
                    <td>{{ $service->adresse ?? '—' }}</td>
                    <td>{{ $service->telephone ?? '—' }}</td>
                    <td><x-badge :type="$service->disponible ? 'success' : 'danger'">{{ $service->disponible ? 'Disponible' : 'Indisponible' }}</x-badge></td>
                    <td><x-badge :type="$service->etat_compte === 'actif' ? 'success' : 'neutral'">{{ $service->etat_compte ?? '—' }}</x-badge></td>
                    <td>
                        <div class="ds-row-actions">
                            <form method="POST" action="{{ route($service->disponible ? 'admin.serviceDesactiver' : 'admin.serviceActiver', $service->id) }}">
                                @csrf
                                <button type="submit" class="ds-btn ds-btn-secondary ds-btn-sm">{{ $service->disponible ? 'Désactiver' : 'Activer' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.services.destroy', $service->id) }}"
                                  onsubmit="return confirm('Supprimer le service « {{ addslashes($service->nom) }} » ? Cette action est irréversible.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ds-btn ds-btn-danger ds-btn-sm">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </x-table>
    @endif
</x-card>
@endsection
