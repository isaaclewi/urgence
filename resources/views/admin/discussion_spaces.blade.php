@extends('admin.master')

@section('title', 'Espaces de discussion')

@section('discussionSpacesActive', 'active')

@push('styles')
    <style>
        /* Styles spécifiques pour la page des espaces de discussion */
        .discussion-page {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-header-box {
            background: white;
            padding: 28px 32px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 32px;
            border-left: 5px solid;
            border-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%) 1;
        }

        .page-header-box h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .page-header-box h2 i {
            color: #667eea;
            font-size: 32px;
        }

        .page-header-box p {
            color: #64748b;
            font-size: 15px;
            margin: 0;
        }

        /* Alert Success */
        .alert-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 18px 24px;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.25);
            margin-bottom: 28px;
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

        .alert-success i {
            font-size: 22px;
        }

        .alert-success .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        /* Cards */
        .discussion-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }

        .discussion-card:hover {
            box-shadow: 0 12px 40px rgba(102, 126, 234, 0.15);
            transform: translateY(-4px);
        }

        .card-header-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px 28px;
            border: none;
        }

        .card-header-gradient h5 {
            margin: 0;
            font-size: 19px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-header-light {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            padding: 24px 28px;
            border: none;
            border-bottom: 2px solid #e2e8f0;
        }

        .card-header-light h5 {
            margin: 0;
            font-size: 19px;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .count-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            animation: pulse 2s infinite;
        }

        /* Form Styles */
        .form-floating-custom {
            position: relative;
            margin-bottom: 24px;
        }

        .form-floating-custom label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .form-floating-custom label i {
            color: #667eea;
            font-size: 16px;
        }

        .form-control-modern,
        .form-select-modern {
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 18px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8fafc;
            color: #1e293b;
            font-weight: 500;
        }

        .form-control-modern:focus,
        .form-select-modern:focus {
            background: white;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        textarea.form-control-modern {
            resize: vertical;
            min-height: 100px;
        }

        /* Button */
        .btn-create {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        }

        .btn-create:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.4);
        }

        /* Table Styles */
        .table-modern {
            margin: 0;
        }

        .table-modern thead {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
        }

        .table-modern thead th {
            border: none;
            padding: 18px 24px;
            font-weight: 700;
            color: #475569;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-modern tbody td {
            padding: 20px 24px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-modern tbody tr {
            transition: all 0.3s ease;
        }

        .table-modern tbody tr:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
            transform: translateX(4px);
        }

        .space-title {
            font-weight: 700;
            color: #1e293b;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .space-description {
            color: #64748b;
            font-size: 13px;
            margin: 0;
        }

        /* Badges */
        .badge-type {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-public {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .badge-prive {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
        }

        .badge-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 2px solid;
        }

        .badge-active {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border-color: #10b981;
        }

        .badge-inactive {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border-color: #ef4444;
        }

        /* Action Button */
        .btn-toggle {
            background: white;
            border: 2px solid #fbbf24;
            color: #f59e0b;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-toggle:hover {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(251, 191, 36, 0.3);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            display: block;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 16px;
            font-weight: 500;
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .page-header-box h2 {
                font-size: 24px;
            }

            .discussion-card {
                margin-bottom: 24px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="discussion-page">
        {{-- EN-TÊTE DE PAGE --}}
        <div class="page-header-box">
            <h2>
                <i class="fas fa-comments"></i>
                Espaces de discussion
            </h2>
            <p>Gérez et configurez les espaces de communication de votre plateforme</p>
        </div>

        {{-- MESSAGE DE SUCCÈS --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            {{-- FORMULAIRE DE CRÉATION --}}
            <div class="col-lg-5 mb-4">
                <div class="discussion-card">
                    <div class="card-header-gradient">
                        <h5>
                            <i class="fas fa-plus-circle"></i>
                            Créer un nouvel espace
                        </h5>
                    </div>
                    <div class="card-body p-4" style="padding: 20px;">
                        <form method="POST" action="{{ route('admin.discussion.space.store') }}">
                            @csrf

                            <div class="form-floating-custom">
                                <label>
                                    <i class="fas fa-heading"></i>
                                    Titre de l'espace
                                </label>
                                <input type="text" name="title" class="form-control form-control-modern"
                                    placeholder="Ex: Forum général, Support technique..." required>
                            </div>

                            <div class="form-floating-custom">
                                <label>
                                    <i class="fas fa-lock"></i>
                                    Type d'accès
                                </label>
                                <select name="type" class="form-select form-select-modern">
                                    <option value="public">🌐 Public - Accessible à tous</option>
                                    <option value="prive">🔒 Privé - Accès restreint</option>
                                </select>
                            </div>

                            <div class="form-floating-custom">
                                <label>
                                    <i class="fas fa-align-left"></i>
                                    Description
                                </label>
                                <textarea name="description" class="form-control form-control-modern"
                                    placeholder="Décrivez l'objectif et les règles de cet espace..."></textarea>
                            </div>

                            <div class="form-floating-custom">
                                <label>
                                    <i class="fas fa-building"></i>
                                    Service responsable
                                </label>
                                <select name="service_id" class="form-select form-select-modern">
                                    <option value="">-- Aucun service --</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-floating-custom">
                                <label>
                                    <i class="fas fa-user-shield"></i>
                                    Modérateur principal
                                </label>
                                <select name="moderator_id" class="form-select form-select-modern">
                                    <option value="">-- Aucun modérateur --</option>
                                    @foreach ($agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn-create">
                                <i class="fas fa-plus-circle"></i>
                                Créer l'espace de discussion
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- LISTE DES ESPACES --}}
            <div class="col-lg-7 mb-4" style="padding: 20px;">
                <div class="discussion-card">
                    <div class="card-header-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>
                                <i class="fas fa-list"></i>
                                Espaces existants
                            </h5>
                            <span class="count-badge">
                                {{ count($spaces) }} espace{{ count($spaces) > 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>
                                            <i class="fas fa-tag me-2"></i>Espace
                                        </th>
                                        <th>
                                            <i class="fas fa-lock me-2"></i>Type
                                        </th>
                                        <th>
                                            <i class="fas fa-building me-2"></i>Service
                                        </th>
                                        <th>
                                            <i class="fas fa-toggle-on me-2"></i>Statut
                                        </th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($spaces as $space)
                                        <tr>
                                            <td>
                                                <div class="space-title">{{ $space->title }}</div>
                                                @if ($space->description)
                                                    <p class="space-description">{{ Str::limit($space->description, 60) }}
                                                    </p>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge-type {{ $space->type === 'public' ? 'badge-public' : 'badge-prive' }}">
                                                    {{ $space->type === 'public' ? '🌐' : '🔒' }}
                                                    {{ strtoupper($space->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($space->service)
                                                    <strong>{{ $space->service->nom }}</strong>
                                                @else
                                                    <span style="color: #94a3b8; font-style: italic;">Non assigné</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($space->is_active)
                                                    <span class="badge-status badge-active">
                                                        <i class="fas fa-check-circle"></i>
                                                        Actif
                                                    </span>
                                                @else
                                                    <span class="badge-status badge-inactive">
                                                        <i class="fas fa-times-circle"></i>
                                                        Inactif
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center flex justify-center gap-2">
                                                <!-- Bouton activer/désactiver -->
                                                <a href="{{ route('admin.discussion.space.toggle', $space->id) }}"
                                                    class="btn-toggle"
                                                    title="{{ $space->is_active ? 'Désactiver l\'espace' : 'Activer l\'espace' }}">
                                                    <i class="fas fa-power-off"></i>
                                                </a>

                                                <!-- Bouton supprimer -->
                                                <a href="{{ route('admin.discussion.space.delete', $space->id) }}"
                                                    class="btn-toggle bg-red-500 border-red-500 hover:bg-red-600 hover:border-red-600"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet espace ?');"
                                                    title="Supprimer l'espace">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">
                                                <div class="empty-state">
                                                    <i class="fas fa-inbox"></i>
                                                    <p>Aucun espace de discussion créé pour le moment</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
