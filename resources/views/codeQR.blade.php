@extends('citoyen')

@section('title', 'Mon Code QR — CongoAssist')

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Mon Code QR Santé</h1>
        <p>Votre identifiant médical numérique.</p>
    </div>
    <a href="{{ route('compteController') }}" class="btn btn-secondary">
        <i data-feather="arrow-left" style="width:14px;height:14px;"></i> Retour
    </a>
</div>

{{-- QR Code --}}
<div style="max-width:680px;margin:0 auto;">
    <div class="card anim-fade" style="margin-bottom:24px;">
        <div class="card-body" style="text-align:center;padding:40px 28px;">
            <div style="display:inline-flex;align-items:center;gap:10px;background:linear-gradient(135deg,var(--accent),var(--green));
                        color:#fff;padding:10px 22px;border-radius:99px;margin-bottom:28px;font-weight:700;font-size:15px;">
                <i data-feather="user-check" style="width:16px;height:16px;"></i>
                {{ $citoyen->nom }} {{ $citoyen->prenom }}
            </div>
            <div style="display:inline-block;padding:24px;background:var(--surface2);border-radius:var(--radius-lg);
                        border:1px solid var(--border);margin-bottom:24px;">
                <img src="data:image/png;base64,{{ $qrImage }}" alt="QR Code"
                     style="width:220px;height:220px;border-radius:var(--radius);display:block;">
            </div>
            <div class="alert alert-info" style="text-align:left;margin-bottom:24px;">
                <i data-feather="info" style="width:16px;height:16px;flex-shrink:0;"></i>
                <span>Présentez ce code QR lors de vos consultations médicales pour un accès rapide à votre dossier de santé.</span>
            </div>
            <div class="form-grid">
                <a href="{{ route('codeQR.download') }}" class="btn btn-success btn-lg btn-full">
                    <i data-feather="download" style="width:14px;height:14px;"></i>
                    Télécharger
                </a>
                <button onclick="window.print()" class="btn btn-secondary btn-lg btn-full">
                    <i data-feather="printer" style="width:14px;height:14px;"></i>
                    Imprimer
                </button>
            </div>
        </div>
    </div>

    {{-- Infos --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;" class="anim-fade" style="animation-delay:.1s;">
        @foreach([
            ['shield-check','bg-green-soft','Sécurisé','Données cryptées et protégées'],
            ['zap','bg-blue-soft','Rapide','Accès instantané à votre dossier'],
            ['smartphone','bg-purple-soft','Mobile','Accessible depuis n\'importe où'],
        ] as $item)
        <div class="card" style="padding:20px;">
            <div class="stat-icon {{ $item[1] }}" style="margin-bottom:12px;">
                <i data-feather="{{ $item[0] }}" style="width:18px;height:18px;"></i>
            </div>
            <div style="font-size:13.5px;font-weight:700;color:var(--text);margin-bottom:4px;">{{ $item[2] }}</div>
            <div style="font-size:12px;color:var(--text-sec);">{{ $item[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

@endsection
