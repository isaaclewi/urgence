@php
    // Détection du portail à partir de la session existante
    $portal = request()->is('equipe', 'equipe/*') ? 'equipe'
            : (request()->is('admin', 'admin/*') ? 'admin' : 'service');

    $identity = match ($portal) {
        'equipe' => ['nom' => session('equipe_nom', 'Équipe'),  'photo' => session('equipe_photo'),  'role' => 'Équipe terrain'],
        'admin'  => ['nom' => session('admin_nom', 'Admin'),    'photo' => session('admin_photo'),   'role' => 'Administration'],
        default  => ['nom' => session('service_nom', 'Service'),'photo' => session('service_photo'), 'role' => 'Service'],
    };
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CongoAssist') — CongoAssist</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js" defer></script>
    @stack('styles')
</head>
<body class="ds-body">
<div class="ds-shell" id="ds-shell">

    <aside class="ds-sidebar" aria-label="Navigation principale">
        <div class="ds-sidebar-stripe"></div>
        <a class="ds-brand" href="{{ url('/') }}">
            <img src="{{ asset('medias/Clogo.jpg') }}" alt="CongoAssist">
            <div>
                <div class="ds-brand-name">{{ \Illuminate\Support\Str::limit($identity['nom'], 22) }}</div>
                <span class="ds-brand-role">{{ $identity['role'] }}</span>
            </div>
        </a>

        <nav class="ds-nav">
            @include('layouts.partials.nav', ['portal' => $portal])
        </nav>

        <div class="ds-sidebar-foot">
            @php
                $logoutRoute = $portal === 'equipe' ? 'equipe.logout' : (($portal === 'admin' && \Illuminate\Support\Facades\Route::has('admin.logout')) ? 'admin.logout' : null);
            @endphp
            @if($logoutRoute)
                <form method="POST" action="{{ route($logoutRoute) }}">
                    @csrf
                    <button type="submit" class="ds-nav-link"><i data-feather="log-out" style="width:16px;height:16px"></i> Déconnexion</button>
                </form>
            @else
                <a class="ds-nav-link" href="{{ $portal === 'admin' ? route('admin.login') : route('services.logout') }}">
                    <i data-feather="log-out" style="width:16px;height:16px"></i> Déconnexion
                </a>
            @endif
        </div>
    </aside>

    <div class="ds-main-col">
        <header class="ds-topbar">
            <div style="display:flex;align-items:center;gap:8px">
                <button class="ds-burger" type="button" aria-label="Ouvrir le menu"
                        onclick="document.getElementById('ds-shell').classList.toggle('ds-open')">☰</button>
                <div class="ds-topbar-title">@yield('page-title', 'Tableau de bord')</div>
            </div>
            <div class="ds-topbar-user">
                <span>{{ $identity['nom'] }}</span>
                <div class="ds-avatar">
                    @if($identity['photo'])<img src="{{ str_starts_with($identity['photo'], 'data:') ? $identity['photo'] : asset($identity['photo']) }}" alt="">@else{{ strtoupper(substr($identity['nom'], 0, 1)) }}@endif
                </div>
            </div>
        </header>

        <main class="ds-main">
            @if(session('success'))<div class="ds-alert ds-alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="ds-alert ds-alert-error">{{ session('error') }}</div>@endif
            @if($errors->any())
                <div class="ds-alert ds-alert-error"><ul style="margin:0;padding-left:18px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            @hasSection('page-subtitle')
                <x-page-header :title="View::yieldContent('page-title', 'Tableau de bord')" :subtitle="View::yieldContent('page-subtitle')">
                    @yield('page-actions')
                </x-page-header>
            @endif

            @yield('content')
        </main>
    </div>

    <div class="ds-backdrop" onclick="document.getElementById('ds-shell').classList.remove('ds-open')"></div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', function () { if (window.feather) feather.replace(); });
</script>
@stack('scripts')
</body>
</html>
