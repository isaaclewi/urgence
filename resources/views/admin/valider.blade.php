@extends('admin.master')

@section('title', 'Validation des Citoyens')
@section('validerActive', 'active')

@push('styles')
<style>
    /* ===== PAGE HEADER ===== */
    .page-header {
        margin-bottom: 32px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
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

    .alert-error {
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

    /* ===== TABLE PHOTO ===== */
    .photo-wrapper {
        position: relative;
        display: inline-block;
    }

    img.table-photo {
        width: 70px;
        height: 70px;
        border-radius: 12px;
        cursor: pointer;
        object-fit: cover;
        transition: all 0.3s ease;
        border: 3px solid #e2e8f0;
    }

    img.table-photo:hover {
        transform: scale(1.1);
        border-color: #667eea;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .photo-zoom-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(102, 126, 234, 0.9);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .photo-wrapper:hover .photo-zoom-icon {
        opacity: 1;
    }

    /* ===== BADGES ===== */
    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-actif {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }

    .badge-inactif {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }

    /* ===== BUTTONS ===== */
    .btn-activer,
    .btn-desactiver {
        border: none;
        padding: 10px 20px;
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

    /* ===== MODAL IMAGE - CORRECTION ICI ===== */
    .modal {
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(8px);
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .modal.show {
        opacity: 1 !important;
        visibility: visible !important;
    }

    .modal-content {
        max-width: 90%;
        max-height: 90vh;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        object-fit: contain;
        transform: scale(0.8);
        transition: transform 0.3s ease;
    }

    .modal.show .modal-content {
        transform: scale(1);
    }

    .modal-close {
        position: absolute;
        top: 30px;
        right: 30px;
        color: white;
        font-size: 40px;
        font-weight: 300;
        cursor: pointer;
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .modal-close:hover {
        background: rgba(239, 68, 68, 0.8);
        transform: rotate(90deg);
        border-color: transparent;
    }

    .modal-caption {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 12px 24px;
        border-radius: 25px;
        color: #1e293b;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    /* ===== PAGINATION ===== */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 24px;
        padding: 20px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
        table {
            font-size: 13px;
        }

        th,
        td {
            padding: 12px 10px;
        }

        img.table-photo {
            width: 60px;
            height: 60px;
        }
    }

    @media (max-width: 768px) {
        .table-container {
            overflow-x: auto;
        }

        table {
            min-width: 1000px;
        }

        .modal-close {
            top: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            font-size: 30px;
        }

        .modal-caption {
            bottom: 15px;
            font-size: 12px;
            padding: 10px 20px;
        }
    }

    @media (max-width: 480px) {
        .page-title {
            font-size: 22px;
        }

        .page-title::before {
            height: 24px;
        }

        .modal-content {
            max-width: 95%;
            max-height: 85vh;
        }
    }

    /* ===== DATATABLE CUSTOM STYLES ===== */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        padding: 20px;
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
        padding: 20px;
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
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <h1 class="page-title">
        Validation des Citoyens
    </h1>
    <p class="page-subtitle">Gérer et vérifier les comptes des citoyens inscrits</p>
</div>

{{-- Alerts --}}
@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

{{-- Table Container --}}
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
                <th>Genre</th>
                <th>Date inscription</th>
                <th>Pièce d'identité</th>
                <th>Action</th>
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
                    <i class="fas fa-{{ $c->sexe == 'M' ? 'mars' : 'venus' }}"
                        style="color: {{ $c->sexe == 'M' ? '#3b82f6' : '#ec4899' }};"></i>
                    {{ $c->sexe }}
                </td>
                <td>{{ $c->created_at->format('d/m/Y') }}</td>
                <td>
                    <div class="photo-wrapper">
                        <img src="{{ route('citoyen.piece', $c->id) }}"
                            alt="Pièce d'identité"
                            class="table-photo"
                            data-name="{{ $c->nom }} {{ $c->prenom }}">
                        <div class="photo-zoom-icon">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                </td>

                <td>
                    @if ($c->etat_compte == 'actif')
                    <form action="{{ route('admin.desactiver', $c->id) }}" method="POST"
                        onsubmit="return confirm('Voulez-vous désactiver ce compte ?')">
                        @csrf
                        <button type="submit" class="btn-desactiver">
                            <i class="fas fa-ban"></i>
                            Désactiver
                        </button>
                    </form>
                    @else
                    <form action="{{ route('admin.activer', $c->id) }}" method="POST"
                        onsubmit="return confirm('Voulez-vous activer ce compte ?')">
                        @csrf
                        <button type="submit" class="btn-activer">
                            <i class="fas fa-check"></i>
                            Activer
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="pagination-wrapper">
    {!! $citoyens->links('pagination::bootstrap-5') !!}
</div>

{{-- MODAL IMAGE --}}
<div id="imageModal" class="modal">
    <span class="modal-close" id="closeModal">
        <i class="fas fa-times"></i>
    </span>
    <img class="modal-content" id="modalImage" alt="Image agrandie">
    <div class="modal-caption" id="modalCaption"></div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Datatable
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
            "order": [
                [0, "desc"]
            ]
        });

        // ===== MODAL - CORRECTION ICI =====
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        const modalCaption = document.getElementById('modalCaption');
        const closeBtn = document.getElementById('closeModal');

        // Ouvrir la modal au clic sur une image (event delegation pour DataTables)
        $('#usersTable').on('click', '.table-photo', function() {
            const src = $(this).data('src');
            const name = $(this).data('name');

            modalImg.src = src;
            modalCaption.textContent = "Pièce d'identité - " + name;
            modal.classList.add('show');

            // Empêcher le scroll du body
            document.body.style.overflow = 'hidden';
        });

        // Fermer la modal avec le bouton X
        closeBtn.addEventListener('click', function() {
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        });

        // Fermer la modal en cliquant sur le fond
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        });

        // Fermer la modal avec la touche Échap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('show')) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        });
    });
</script>
@endpush