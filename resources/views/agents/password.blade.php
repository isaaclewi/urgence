@extends('layouts.portal')

@section('title', 'Mot de passe')
@section('page-title', 'Mon mot de passe')
@section('page-subtitle', 'Choisissez un mot de passe personnel (8 caractères minimum)')

@section('content')
<x-card>
    @if(session('success'))<p>{{ session('success') }}</p>@endif
    @if($errors->any())<div class="ds-alert">@foreach($errors->all() as $e){{ $e }}<br>@endforeach</div>@endif
    <form method="POST" action="{{ route('agent.password.update') }}">
        @csrf
        <div class="ds-form-grid">
            <div class="ds-field"><label class="ds-label" for="password_actuel">Mot de passe actuel (temporaire)</label>
                <input class="ds-input" type="password" id="password_actuel" name="password_actuel" required></div>
            <div class="ds-field"><label class="ds-label" for="password">Nouveau mot de passe</label>
                <input class="ds-input" type="password" id="password" name="password" required minlength="8"></div>
            <div class="ds-field"><label class="ds-label" for="password_confirmation">Confirmation</label>
                <input class="ds-input" type="password" id="password_confirmation" name="password_confirmation" required></div>
        </div>
        <button class="ds-btn ds-btn-primary" type="submit">Enregistrer</button>
    </form>
</x-card>
@endsection
