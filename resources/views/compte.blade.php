@extends('citoyen')

@section('title', 'Mon Compte — CongoAssist')

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Mon Compte</h1>
        <p>Gérez votre profil et accédez à vos services.</p>
    </div>
</div>

{{-- Profil --}}
<div class="card anim-fade" style="margin-bottom:24px;">
    <div class="card-body" style="padding:28px;">
        <div style="display:flex;gap:28px;align-items:flex-start;flex-wrap:wrap;">
            <div style="position:relative;flex-shrink:0;">
                <img src="{{ asset($citoyen->photo_profil) }}" alt="Photo"
                     style="width:96px;height:96px;border-radius:50%;object-fit:cover;border:3px solid var(--green);">
                <div style="position:absolute;bottom:0;right:0;width:26px;height:26px;border-radius:50%;
                            background:var(--green);display:flex;align-items:center;justify-content:center;border:2px solid #fff;">
                    <i data-feather="check" style="width:12px;height:12px;color:#fff;"></i>
                </div>
            </div>
            <div style="flex:1;min-width:0;">
                <h2 style="font-size:20px;font-weight:700;color:var(--text);margin-bottom:16px;">
                    {{ $citoyen->nom }} {{ $citoyen->prenom }}
                </h2>
                <div class="form-grid">
                    <div style="display:flex;align-items:center;gap:12px;padding:14px;background:var(--accent-light);border-radius:var(--radius-lg);">
                        <div class="stat-icon bg-blue-soft" style="width:38px;height:38px;">
                            <i data-feather="mail" style="width:16px;height:16px;"></i>
                        </div>
                        <div>
                            <div style="font-size:11px;color:var(--text-sec);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Email</div>
                            <div style="font-size:13.5px;font-weight:600;color:var(--text);">{{ $citoyen->email }}</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;padding:14px;background:var(--green-light);border-radius:var(--radius-lg);">
                        <div class="stat-icon bg-green-soft" style="width:38px;height:38px;">
                            <i data-feather="phone" style="width:16px;height:16px;"></i>
                        </div>
                        <div>
                            <div style="font-size:11px;color:var(--text-sec);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Téléphone</div>
                            <div style="font-size:13.5px;font-weight:600;color:var(--text);">{{ $citoyen->telephone }}</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;padding:14px;background:#f5f3ff;border-radius:var(--radius-lg);grid-column:1/-1;">
                        <div class="stat-icon bg-purple-soft" style="width:38px;height:38px;">
                            <i data-feather="map-pin" style="width:16px;height:16px;"></i>
                        </div>
                        <div>
                            <div style="font-size:11px;color:var(--text-sec);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Adresse</div>
                            <div style="font-size:13.5px;font-weight:600;color:var(--text);">{{ $citoyen->adresse }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Avis --}}
<div class="card anim-fade" style="animation-delay:.1s;">
    <div class="card-header" style="padding-bottom:14px;border-bottom:1px solid var(--border);">
        <span class="card-title" style="font-size:15px;">
            <i data-feather="message-circle" style="width:15px;height:15px;display:inline;margin-right:6px;vertical-align:middle;"></i>
            Laisser mon avis
        </span>
    </div>
    <div class="card-body">
        <p style="font-size:13px;color:var(--text-sec);margin-bottom:16px;">Partagez votre expérience avec CongoAssist.</p>
        <form action="{{ route('avisUsers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">
                    <i data-feather="edit-3" style="width:12px;height:12px;display:inline;margin-right:4px;vertical-align:middle;"></i>
                    Votre avis <span style="color:var(--red)">*</span>
                </label>
                <textarea name="description" rows="5" required class="form-control"
                          placeholder="Partagez votre expérience, vos suggestions..."></textarea>
            </div>
            <div style="display:flex;justify-content:flex-end;">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i data-feather="send" style="width:14px;height:14px;"></i>
                    Soumettre mon avis
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
