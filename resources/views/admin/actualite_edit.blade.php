@extends('layouts.portal')

@section('title', 'Modifier une actualité')
@section('page-title', 'Modifier une actualité')
@section('page-subtitle', 'Publiée le ' . \Carbon\Carbon::parse($actualite->date_publication)->format('d/m/Y'))

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('admin.actualite') }}">← Retour</a>
@endsection

@section('content')
<x-card>
    <form method="POST" action="{{ route('admin.actualiteUpdate', $actualite->id) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="ds-field">
            <label class="ds-label" for="contenu">Contenu *</label>
            <textarea class="ds-textarea" id="contenu" name="contenu" rows="8" maxlength="5000" required>{{ old('contenu', $actualite->contenu) }}</textarea>
        </div>

        @if($actualite->url_media)
            <div class="ds-field">
                <label class="ds-label">Média actuel</label>
                @if($actualite->type_media === 'mp4')
                    <video controls preload="metadata" style="max-width:420px;width:100%;background:#000"><source src="{{ $actualite->url_media }}" type="video/mp4"></video>
                @else
                    <img src="{{ $actualite->url_media }}" alt="Média actuel" style="max-width:420px;width:100%;border-radius:8px">
                @endif
                <label style="display:flex;gap:8px;align-items:center;font-size:13px;margin-top:6px">
                    <input type="checkbox" name="supprimer_media" value="1"> Retirer ce média
                </label>
            </div>
        @endif

        <div class="ds-field">
            <label class="ds-label" for="url_media">{{ $actualite->url_media ? 'Remplacer le média' : 'Ajouter un média' }} (facultatif)</label>
            <input class="ds-input" type="file" id="url_media" name="url_media" accept=".jpg,.jpeg,.png,.gif,.mp4">
        </div>

        <button class="ds-btn ds-btn-primary" type="submit">Enregistrer</button>
    </form>
</x-card>
@endsection
