@extends('services.master')

@section('title', $service->nom ?? 'CongoAssist')

@section('page-title', 'Actualités')
@section('page-subtitle', 'Gérez et publiez les actualités de votre service')

@section('page-actions')
    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="btn btn-accent">
        <i data-feather="plus"></i>
        Nouvelle actualité
    </button>
@endsection

@section('sidebar')
<div class="sb-section-label">Navigation</div>
<a href="{{ route('services.compte') }}" class="sidebar-link">
    <i data-feather="home"></i><span class="sb-lbl">Tableau de bord</span>
</a>
<a href="{{ route('services.urgenceSignalee') }}" class="sidebar-link">
    <i data-feather="bell"></i><span class="sb-lbl">Urgences signalées</span>
</a>
<a href="{{ route('services.citoyens') }}" class="sidebar-link">
    <i data-feather="users"></i><span class="sb-lbl">Citoyens</span>
</a>
<a href="{{ route('services.forum.index') }}" class="sidebar-link">
    <i data-feather="message-square"></i><span class="sb-lbl">Forum</span>
</a>
<a href="{{ route('services.actualite') }}" class="sidebar-link active">
    <i data-feather="newspaper"></i><span class="sb-lbl">Actualités</span>
</a>
<a href="{{ route('services.profil') }}" class="sidebar-link">
    <i data-feather="settings"></i><span class="sb-lbl">Gestion interne</span>
</a>
<div class="sb-divider"></div>
<a href="{{ route('services.logout') }}" class="sidebar-link danger">
    <i data-feather="log-out"></i><span class="sb-lbl">Déconnexion</span>
</a>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- Feed d'actualités --}}
    <div style="display:flex; flex-direction:column; gap:20px;" class="anim-slide">
        @forelse($actualites as $actu)
        <div class="content-card" style="overflow:hidden;">

            {{-- Média --}}
            @if($actu->url_media)
                @if($actu->type_media === 'mp4')
                <video controls style="width:100%; max-height:400px; display:block; background:#000;">
                    <source src="{{ $actu->url_media }}" type="video/mp4">
                </video>
                @else
                <img src="{{ $actu->url_media }}" alt="Actualité"
                     style="width:100%; max-height:400px; object-fit:cover; display:block;">
                @endif
            @else
            <div style="height:160px; background:linear-gradient(135deg,var(--surface2),var(--border));
                        display:flex; align-items:center; justify-content:center;">
                <i data-feather="image" style="width:48px;height:48px;color:var(--border-mid);"></i>
            </div>
            @endif

            <div style="padding:20px 22px;">
                {{-- Header de l'actu --}}
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="avatar-initials">
                            <i data-feather="user" style="width:15px;height:15px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:600; color:var(--text); font-size:13.5px;">{{ $actu->auteur_nom }}</div>
                            <div style="font-size:11.5px; color:var(--text-muted); display:flex; align-items:center; gap:4px; margin-top:2px;">
                                <i data-feather="calendar" style="width:11px;height:11px;"></i>
                                {{ \Carbon\Carbon::parse($actu->date_publication)->format('d M Y à H:i') }}
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; gap:6px;">
                        <a href="{{ route('services.actualiteEdit', $actu->id) }}"
                           class="btn btn-outline btn-sm btn-icon" title="Modifier">
                            <i data-feather="edit-2"></i>
                        </a>
                        <form action="{{ route('services.actualiteDestroy', $actu->id) }}" method="POST"
                              style="display:inline;"
                              onsubmit="return confirm('Supprimer cette actualité ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Supprimer">
                                <i data-feather="trash-2"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Texte --}}
                <p class="contenu-{{ $actu->id }}"
                   data-full="{{ $actu->contenu }}"
                   data-truncated="{{ Str::limit($actu->contenu, 200) }}"
                   style="font-size:13.5px; color:var(--text-sec); line-height:1.7;">
                    {{ Str::limit($actu->contenu, 200) }}
                </p>

                @if(strlen($actu->contenu) > 200)
                <button onclick="toggleContent({{ $actu->id }})"
                        class="btn-voir-plus-{{ $actu->id }}"
                        style="margin-top:10px; background:none; border:none; cursor:pointer;
                               font-size:12.5px; font-weight:600; color:var(--accent);
                               display:flex; align-items:center; gap:4px; padding:0;">
                    <span>Voir plus</span>
                    <i data-feather="chevron-down" style="width:14px;height:14px;"></i>
                </button>
                @endif

                {{-- Stats --}}
                <div style="display:flex; gap:20px; margin-top:16px; padding-top:14px;
                             border-top:1px solid var(--border);">
                    <div style="display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--text-muted);">
                        <i data-feather="eye" style="width:14px;height:14px;"></i> 248 vues
                    </div>
                    <div style="display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--text-muted);">
                        <i data-feather="heart" style="width:14px;height:14px;"></i> 32 likes
                    </div>
                    <div style="display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--text-muted);">
                        <i data-feather="message-circle" style="width:14px;height:14px;"></i> 12 commentaires
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="content-card">
            <div class="cc-body" style="text-align:center; padding:60px 20px;">
                <div style="width:64px;height:64px;border-radius:50%;background:var(--surface2);
                             display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i data-feather="newspaper" style="width:28px;height:28px;color:var(--border-mid);"></i>
                </div>
                <div style="font-size:15px; font-weight:700; color:var(--text); margin-bottom:6px;">Aucune actualité</div>
                <div style="font-size:13px; color:var(--text-muted); margin-bottom:20px;">Publiez votre première actualité</div>
                <button onclick="document.getElementById('modalAdd').classList.remove('hidden')"
                        class="btn btn-accent">
                    <i data-feather="plus"></i> Créer une actualité
                </button>
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- MODAL AJOUT --}}
<div id="modalAdd" style="display:none; position:fixed; inset:0; z-index:80;
     background:rgba(0,0,0,.4); backdrop-filter:blur(4px);
     align-items:center; justify-content:center; padding:16px;"
     class="modal-overlay-custom">

    <div class="modal-box anim-fade" style="max-width:520px;">
        <div class="modal-header">
            <div class="modal-title">
                <i data-feather="newspaper" style="color:var(--accent);width:18px;height:18px;"></i>
                Ajouter une actualité
            </div>
            <button onclick="document.getElementById('modalAdd').style.display='none'"
                    class="btn btn-outline btn-sm btn-icon">
                <i data-feather="x"></i>
            </button>
        </div>

        <form action="{{ route('services.actualiteStore') }}" method="POST"
              enctype="multipart/form-data">
            @csrf
            <div class="modal-body" style="display:flex; flex-direction:column; gap:14px;">

                <div>
                    <label class="form-label"><i data-feather="user" style="width:13px;height:13px;color:var(--accent);"></i> Auteur</label>
                    <input type="text" name="auteur_nom" value="{{ $service->nom }}" readonly class="form-input" style="background:var(--surface2);">
                </div>

                <div>
                    <label class="form-label"><i data-feather="file-text" style="width:13px;height:13px;color:var(--accent);"></i> Contenu</label>
                    <textarea name="contenu" required rows="5"
                              placeholder="Écrivez votre actualité ici…"
                              class="form-input" style="resize:none;"></textarea>
                </div>

                <div>
                    <label class="form-label"><i data-feather="image" style="width:13px;height:13px;color:var(--accent);"></i> Image ou vidéo (optionnel)</label>
                    <input type="file" name="url_media" accept="image/*,video/*" id="fileInput" style="display:none;" onchange="updateFileName(this)">
                    <label for="fileInput" class="form-input"
                           style="display:flex;align-items:center;gap:8px;cursor:pointer;
                                  border-style:dashed; color:var(--text-muted); justify-content:center;">
                        <i data-feather="upload" style="width:15px;height:15px;"></i>
                        <span id="fileName">Choisir un fichier</span>
                    </label>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:5px;">JPG, PNG, MP4 — max 10 Mo</div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        onclick="document.getElementById('modalAdd').style.display='none'"
                        class="btn btn-outline">Annuler</button>
                <button type="submit" class="btn btn-accent">
                    <i data-feather="send"></i> Publier
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .modal-overlay-custom { display: none; }
    .modal-overlay-custom.open { display: flex; }
</style>
@endpush

@push('scripts')
<script>
    /* Ouvrir/fermer la modale */
    function openModal()  { const m = document.getElementById('modalAdd'); m.style.display='flex'; }
    function closeModal() { const m = document.getElementById('modalAdd'); m.style.display='none'; }
    document.getElementById('modalAdd').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    document.querySelectorAll('[onclick*="modalAdd"]').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.getAttribute('onclick').includes('remove')) {
                document.getElementById('modalAdd').style.display = 'flex';
            }
        });
    });

    function toggleContent(id) {
        const p   = document.querySelector('.contenu-' + id);
        const btn = document.querySelector('.btn-voir-plus-' + id + ' span');
        const ico = document.querySelector('.btn-voir-plus-' + id + ' i');
        if (btn.textContent === 'Voir plus') {
            p.textContent = p.dataset.full;
            btn.textContent = 'Voir moins';
            ico.setAttribute('data-feather', 'chevron-up');
        } else {
            p.textContent = p.dataset.truncated;
            btn.textContent = 'Voir plus';
            ico.setAttribute('data-feather', 'chevron-down');
        }
        feather.replace({ width: 16, height: 16 });
    }

    function updateFileName(input) {
        document.getElementById('fileName').textContent = input.files[0]?.name || 'Choisir un fichier';
    }

    document.addEventListener('DOMContentLoaded', () => feather.replace({ width: 16, height: 16 }));
</script>
@endpush
@endsection
