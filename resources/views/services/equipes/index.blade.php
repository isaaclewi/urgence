@extends('services.master') {{-- adapte si ton master a un autre nom --}}

@section('page-title', 'Gestion des équipes')
@section('page-subtitle', 'Création et administration des équipes terrain')

@section('page-actions')
<a href="{{ route('services.equipes.create') }}" class="btn btn-accent">
    <i data-feather="plus"></i> Nouvelle équipe
</a>
@endsection

@section('sidebar')
<a href="{{ route('services.compte') }}" class="sidebar-link">
    <i data-feather="home"></i> Dashboard
</a>

<a href="{{ route('services.equipes.index') }}" class="sidebar-link active">
    <i data-feather="users"></i> Équipes
</a>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:15px;">
    <i data-feather="check-circle"></i>
    {{ session('success') }}
</div>
@endif

<div class="content-card">
    <div class="cc-header">
        <div>
            <div class="cc-title">
                <i data-feather="users"></i>
                Liste des équipes
            </div>
            <div class="cc-subtitle">
                Toutes les équipes créées par votre service
            </div>
        </div>
    </div>

    <div class="cc-body table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Statut</th>
                    <th style="width:160px;">Actions</th>
                </tr>
            </thead>

            <tbody>
            @forelse($equipes as $equipe)
                <tr>
                    <td>
                        <strong>{{ $equipe->nom }}</strong>
                    </td>

                    <td>{{ $equipe->email ?? '—' }}</td>

                    <td>{{ $equipe->telephone ?? '—' }}</td>

                    <td>
                        <span class="pill pill-green">
                            Actif
                        </span>
                    </td>

                    <td>
                        <a href="{{ route('services.equipes.edit', $equipe->id) }}"
                           class="btn btn-sm btn-outline">
                            <i data-feather="edit"></i>
                        </a>

                        <form action="{{ route('services.equipes.destroy', $equipe->id) }}"
                              method="POST"
                              style="display:inline-block;">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Supprimer cette équipe ?')">
                                <i data-feather="trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:20px;">
                        Aucune équipe trouvée
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
