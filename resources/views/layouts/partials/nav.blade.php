@php
    /*
     | Menu unique des 3 portails. Un lien n'apparaît que si sa route existe :
     | les futurs modules ERP (personnel, ressources, KPI…) s'afficheront
     | automatiquement dès que leurs routes seront déclarées.
     | Format : [route, libellé, icône Feather, motif d'activation]
     */
    $menus = [
        'service' => [
            'Pilotage' => [
                ['services.compte',                 'Tableau de bord', 'home',     'services.compte'],
                ['services.urgenceSignalee',        'Urgences',        'alert-triangle', 'services.urgenceSignalee*'],
                ['services.interventions.journalieres', 'Interventions', 'activity', 'services.interventions.*'],
            ],
            'Organisation' => [
                ['services.equipes.index',          'Équipes',         'users',    'services.equipes.*'],
                ['services.personnel.index',        'Personnel',       'user-check', 'services.personnel.*'],
                ['services.ressources.index',       'Ressources',      'truck',    'services.ressources.*'],
                ['services.kpi',                    'Indicateurs',     'bar-chart-2', 'services.kpi*'],
            ],
            'Santé & citoyens' => [
                ['services.citoyens',               'Citoyens',        'user',     'services.citoyens'],
                ['services.citoyensBilan',          'Bilans de santé', 'heart',    'services.citoyensBilan*'],
                ['services.vaccinationIndex',       'Vaccination',     'shield',   'services.vaccination*'],
            ],
            'Communication' => [
                ['services.actualite',              'Actualités',      'file-text','services.actualite*'],
                ['services.forum.index',            'Forum',           'message-circle', 'services.forum.*'],
            ],
        ],
        'admin' => [
            'Administration' => [
                ['admin.compte',                    'Tableau de bord', 'home',     'admin.compte'],
                ['admin.users',                     'Citoyens',        'users',    'admin.users*'],
                ['admin.valider',                   'Vérifications',   'check-circle', 'admin.valider'],
                ['admin.services',                  'Services',        'briefcase','admin.service*'],
                ['admin.alertes',                   'Urgences',        'bell',     'admin.alertes'],
            ],
            'Contenu' => [
                ['admin.actualite',                 'Actualités',      'file-text','admin.actualite*'],
                ['admin.discussion.spaces',         'Espaces discussion','message-square','admin.discussion.*'],
                ['forum.index',                     'Forum',           'message-circle', 'forum.*'],
            ],
        ],
        'equipe' => [
            'Terrain' => [
                ['equipe.dashboard',                'Tableau de bord', 'home',     'equipe.dashboard'],
                ['equipe.alertes',                  'Mes alertes',     'alert-triangle', 'equipe.alerte*'],
                ['equipe.profil',                   'Profil',          'user',     'equipe.profil'],
            ],
        ],
    ];
@endphp

@foreach($menus[$portal] as $section => $items)
    @php $visibles = array_filter($items, fn($i) => \Illuminate\Support\Facades\Route::has($i[0])); @endphp
    @if(count($visibles))
        <div class="ds-nav-label">{{ $section }}</div>
        @foreach($visibles as [$route, $label, $icon, $pattern])
            <a href="{{ route($route) }}" class="ds-nav-link {{ request()->routeIs($pattern) ? 'active' : '' }}">
                <i data-feather="{{ $icon }}" class="ds-nav-icon" style="width:16px;height:16px"></i> {{ $label }}
            </a>
        @endforeach
    @endif
@endforeach
