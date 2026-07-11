@extends('admin.master')

@section('title', 'Services d\'urgence')
@section('serviceActive', 'active')

@section('content')

{{-- Messages flash --}}
@if(session('success'))
<div class="alert-success">
    <i class="fa fa-check-circle"></i> {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="alert-error">
    <ul style="margin:0;padding-left:18px;">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Formulaire d'ajout --}}
<div class="card" style="margin-bottom:28px;">
    <h2 style="color:#0D3B66;margin-bottom:20px;font-size:18px;">
        <i class="fa fa-plus-circle"></i> Ajouter un service d'urgence
    </h2>
    <form action="{{ route('admin.services.store') }}" method="POST">
        @csrf
        <div class="form-grid">
            <input type="text" name="nom" placeholder="Nom du service" required value="{{ old('nom') }}">
            <input type="text" name="adresse" placeholder="Adresse" required value="{{ old('adresse') }}">
            <select name="role" required>
                <option value="">Type de service</option>
                <option value="pompier" {{ old('role') == 'pompier' ? 'selected' : '' }}>Sapeur-pompier</option>
                <option value="police" {{ old('role') == 'police' ? 'selected' : '' }}>Police</option>
                <option value="hopital" {{ old('role') == 'hopital' ? 'selected' : '' }}>Hôpital</option>
            </select>
            <select name="disponible" required>
                <option value="1" {{ old('disponible', '1') == '1' ? 'selected' : '' }}>Disponible</option>
                <option value="0" {{ old('disponible') == '0' ? 'selected' : '' }}>Non disponible</option>
            </select>
            <input type="email" name="email" placeholder="Email (optionnel)" value="{{ old('email') }}">
            <input type="text" name="telephone" placeholder="Téléphone (optionnel)" value="{{ old('telephone') }}">
            <input type="text" name="etat_compte" placeholder="État du compte" required value="{{ old('etat_compte') }}">
            <input type="password" name="password" placeholder="Mot de passe" required>
        </div>
        <button class="btn-primary" type="submit">
            <i class="fa fa-save"></i> Enregistrer le service
        </button>
    </form>
</div>

{{-- Liste des services --}}
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <h2 style="color:#0D3B66;font-size:18px;margin:0;">
            <i class="fa fa-list"></i> Services enregistrés
        </h2>
        <span class="badge-count">{{ $services->count() }} service{{ $services->count() > 1 ? 's' : '' }}</span>
    </div>

    @if($services->isEmpty())
    <div style="text-align:center;padding:40px;color:#6b7280;">
        <i class="fa fa-inbox" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px;"></i>
        <p style="font-size:15px;font-weight:600;margin:0 0 4px;">Aucun service enregistré</p>
        <p style="font-size:13px;margin:0;">Utilisez le formulaire ci-dessus pour en ajouter un.</p>
    </div>
    @else
    <div style="overflow-x:auto;">
        <table class="services-table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Type</th>
                    <th>Adresse</th>
                    <th>Contact</th>
                    <th>Disponibilité</th>
                    <th>État compte</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="service-avatar service-avatar--{{ $service->role }}">
                                <i class="fa fa-{{ $service->role === 'pompier' ? 'fire' : ($service->role === 'police' ? 'shield' : 'hospital-o') }}"></i>
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:13.5px;color:#111827;">{{ $service->nom }}</div>
                                <div style="font-size:11.5px;color:#6b7280;">{{ $service->email ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge-role badge-role--{{ $service->role }}">
                            {{ ucfirst($service->role) }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:#374151;max-width:180px;">
                        {{ $service->adresse ?? '—' }}
                    </td>
                    <td style="font-size:13px;color:#374151;">
                        {{ $service->telephone ?? '—' }}
                    </td>
                    <td>
                        @if($service->disponible)
                            <span class="badge-status badge-status--on">
                                <span class="dot dot--green"></span> Disponible
                            </span>
                        @else
                            <span class="badge-status badge-status--off">
                                <span class="dot dot--red"></span> Indisponible
                            </span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:#374151;">
                        {{ $service->etat_compte ?? '—' }}
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex;gap:6px;justify-content:center;">
                            @if($service->disponible)
                                <a href="{{ route('admin.serviceDesactiver', $service->id) }}" class="btn-toggle" title="Désactiver">
                                    <i class="fa fa-toggle-on"></i>
                                </a>
                            @else
                                <a href="{{ route('admin.serviceActiver', $service->id) }}" class="btn-toggle" title="Activer">
                                    <i class="fa fa-toggle-off"></i>
                                </a>
                            @endif
                            <form action="{{ route('admin.services.destroy', $service->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Supprimer le service « {{ $service->nom }} » ? Cette action est irréversible.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" title="Supprimer">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<style>
/* ── Alertes ── */
.alert-success, .alert-error {
    padding: 12px 16px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 500;
}
.alert-success { background:#D4EDDA; color:#155724; border-left:4px solid #28a745; }
.alert-error   { background:#F8D7DA; color:#721C24; border-left:4px solid #dc3545; }

/* ── Card ── */
.card {
    background: #fff;
    border-radius: 20px;
    padding: 28px 30px;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
    border-left: 4px solid #FF6F61;
    max-width: 1200px;
    margin: 0 auto;
}

/* ── Formulaire ── */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 16px;
    margin-bottom: 8px;
}
.form-grid input,
.form-grid select {
    padding: 12px 14px;
    border-radius: 12px;
    border: 1.5px solid #A3D2CA;
    font-size: 14px;
    background: #FAFAFA;
    transition: border-color .15s, box-shadow .15s;
    width: 100%;
    box-sizing: border-box;
}
.form-grid input:focus,
.form-grid select:focus {
    outline: none;
    border-color: #FF6F61;
    box-shadow: 0 0 0 3px rgba(255,111,97,.15);
    background: #fff;
}
.btn-primary {
    margin-top: 20px;
    padding: 12px 28px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(90deg, #FFAB91, #FF6F61);
    color: #fff;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: opacity .15s;
}
.btn-primary:hover { opacity: .88; }

/* ── Tableau ── */
.services-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}
.services-table th {
    padding: 10px 14px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: #6b7280;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
}
.services-table td {
    padding: 14px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}
.services-table tbody tr:last-child td { border-bottom: none; }
.services-table tbody tr:hover { background: #fafafa; }

/* ── Avatar service ── */
.service-avatar {
    width: 36px; height: 36px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0;
}
.service-avatar--pompier { background: #fff7ed; color: #ea580c; }
.service-avatar--police  { background: #eff6ff; color: #2563eb; }
.service-avatar--hopital { background: #f0fdf4; color: #16a34a; }

/* ── Badges rôle ── */
.badge-role {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 99px;
    font-size: 11.5px;
    font-weight: 700;
}
.badge-role--pompier { background: #fff7ed; color: #c2410c; }
.badge-role--police  { background: #eff6ff; color: #1d4ed8; }
.badge-role--hopital { background: #f0fdf4; color: #15803d; }

/* ── Badges statut ── */
.badge-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 99px;
    font-size: 11.5px; font-weight: 600;
}
.badge-status--on  { background: #d1fae5; color: #065f46; }
.badge-status--off { background: #fee2e2; color: #991b1b; }
.dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
.dot--green { background: #10b981; }
.dot--red   { background: #ef4444; }

/* ── Bouton toggle disponibilité ── */
.btn-toggle {
    background: #eff6ff;
    border: 1.5px solid #bfdbfe;
    color: #2563eb;
    width: 34px; height: 34px;
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    text-decoration: none;
    transition: background .15s;
}
.btn-toggle:hover { background: #dbeafe; }

/* ── Bouton supprimer ── */
.btn-delete {
    background: #fef2f2;
    border: 1.5px solid #fecaca;
    color: #dc2626;
    width: 34px; height: 34px;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.btn-delete:hover { background: #fee2e2; }

/* ── Badge count ── */
.badge-count {
    background: #fff7ed;
    color: #c2410c;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 99px;
    border: 1px solid #fed7aa;
}
</style>

@endsection