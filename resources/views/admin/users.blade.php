@extends('admin.master')

@section('title', 'Liste des Citoyens')
@section('usersActive', 'active')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
/* ===== PAGE HEADER ===== */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    background: white;
    padding: 24px 28px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
}

.page-title-section {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-title::before {
    content: '';
    width: 4px;
    height: 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 4px;
}

.page-subtitle {
    color: #64748b;
    font-size: 15px;
    margin-left: 16px;
}

.admin-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 16px 8px 8px;
    background: #f8fafc;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.admin-profile:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
}

.admin-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.admin-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 15px;
}

/* ===== ALERTS ===== */
.alert {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
    animation: slideDown 0.4s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border-left: 4px solid #10b981;
}

.alert-danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    border-left: 4px solid #ef4444;
}

.alert i {
    font-size: 20px;
}

/* ===== TABLE CONTAINER ===== */
.table-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    padding: 20px;
    transition: box-shadow 0.3s ease;
}

.table-container:hover {
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.1);
}

/* ===== TABLE ===== */
table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

th {
    color: white;
    padding: 18px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

td {
    padding: 16px;
    border-bottom: 1px solid #e2e8f0;
    color: #475569;
    font-size: 14px;
}

tbody tr {
    transition: all 0.3s ease;
}

tbody tr:hover {
    background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    transform: translateX(4px);
}

tbody tr:last-child td {
    border-bottom: none;
}

/* ===== BADGES ===== */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-active {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
}

.badge-inactif {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

/* ===== BUTTONS ===== */
.btn-action {
    border: none;
    padding: 10px 18px;
    border-radius: 10px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-activer {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-activer:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.btn-desactiver {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.btn-desactiver:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

/* ===== STATS BAR ===== */
.stats-bar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.stat-item {
    background: white;
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.15);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.stat-icon.blue {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.15) 100%);
    color: #3b82f6;
}

.stat-icon.green {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.15) 100%);
    color: #10b981;
}

.stat-icon.red {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.15) 100%);
    color: #ef4444;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 4px;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
}

/* ===== PAGINATION ===== */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 24px;
    padding: 20px 0;
}

/* ===== DATATABLE CUSTOM STYLES ===== */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    padding: 20px 0;
}

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 12px;
    margin-left: 8px;
    transition: all 0.3s ease;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    padding: 20px 0;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 8px;
    padding: 6px 12px;
    margin: 0 2px;
    background: white !important;
    border: 2px solid #e2e8f0 !important;
    color: #64748b !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    border-color: #667eea !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    border-color: #667eea !important;
}

/* ===== GENRE ICON ===== */
.genre-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 1024px) {
    .page-header {
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
    }
    
    table {
        font-size: 13px;
    }
    
    th, td {
        padding: 12px 10px;
    }
}

@media (max-width: 768px) {
    .table-container {
        overflow-x: auto;
    }
    
    table {
        min-width: 1000px;
    }
    
    .stats-bar {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-title-section">
        <h1 class="page-title">
            Liste des Citoyens
        </h1>
        <p class="page-subtitle">Gérer les comptes des citoyens inscrits</p>
    </div>
    <div class="admin-profile">
        <img src="{{ $admin->photo_profil ? asset($admin->photo_profil) : asset('medias/default-user.jpg') }}" 
             alt="Profil" 
             class="admin-avatar">
        <span class="admin-name">{{ $admin->nom ?? 'Admin' }}</span>
    </div>
</div>

<!-- STATS BAR -->
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total</div>
            <div class="stat-value">{{ $citoyens->total() }}</div>
        </div>
    </div>
    
    <div class="stat-item">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Actifs</div>
            <div class="stat-value">{{ $citoyens->where('etat_compte', 'actif')->count() }}</div>
        </div>
    </div>
    
    <div class="stat-item">
        <div class="stat-icon red">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Inactifs</div>
            <div class="stat-value">{{ $citoyens->where('etat_compte', '!=', 'actif')->count() }}</div>
        </div>
    </div>
</div>

<!-- ALERTS -->
@if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<!-- TABLE CONTAINER -->
<div class="table-container">
    <table id="usersTable" class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Noms et Prénoms</th>
                <th>Email</th>
                <th>Adresse</th>
                <th>Contact</th>
                <th>Matricule</th>
                <th>État du compte</th>
                <th>Genre</th>
                <th>Date inscription</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($citoyens as $c)
            <tr>
                <td><strong>#{{ $c->id }}</strong></td>
                <td>
                    <div style="font-weight: 600; color: #1e293b;">
                        {{ $c->nom }} {{ $c->prenom }}
                    </div>
                </td>
                <td>{{ $c->email }}</td>
                <td>{{ $c->adresse }}</td>
                <td>{{ $c->telephone }}</td>
                <td><strong>{{ $c->matricule }}</strong></td>
                <td>
                    @if ($c->etat_compte == 'actif')
                        <span class="badge badge-active">
                            <i class="fas fa-check-circle"></i> Activé
                        </span>
                    @else
                        <span class="badge badge-inactif">
                            <i class="fas fa-times-circle"></i> Désactivé
                        </span>
                    @endif
                </td>
                <td>
                    <span class="genre-badge">
                        <i class="fas fa-{{ $c->sexe == 'M' ? 'mars' : 'venus' }}" 
                           style="color: {{ $c->sexe == 'M' ? '#3b82f6' : '#ec4899' }};"></i>
                        {{ $c->sexe }}
                    </span>
                </td>
                <td>{{ $c->created_at->format('d/m/Y') }}</td>
                <td>
                    <form action="{{ route('admin.users.delete', $c->id) }}" method="POST"
                          onsubmit="return confirm('Voulez-vous vraiment supprimer ce compte ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-desactiver">
                            <i class="fas fa-trash-alt"></i>
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- PAGINATION -->
    <div class="pagination-wrapper">
        {!! $citoyens->links('pagination::bootstrap-5') !!}
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 15, 25, 50, 100],
            "language": {
                "search": "Rechercher :",
                "lengthMenu": "Afficher _MENU_ utilisateurs",
                "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                "infoEmpty": "Aucune entrée disponible",
                "infoFiltered": "(filtré de _MAX_ entrées totales)",
                "zeroRecords": "Aucun résultat trouvé",
                "paginate": {
                    "first": "Début",
                    "last": "Fin",
                    "next": "Suivant",
                    "previous": "Précédent"
                }
            },
            "order": [[0, "desc"]]
        });
    });
</script>
@endpush