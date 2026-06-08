@extends('admin.master')

@section('title', 'Actualités')
@section('actualiteActive', 'active')

@section('content')

<!-- ACTION BUTTON -->
<div style="display:flex; justify-content:flex-end; margin-bottom:20px;">
    <button class="action-btn" onclick="document.getElementById('modalAdd').style.display='flex'">
        <i class="fas fa-plus" style="margin-right:8px;"></i> Nouvelle actualité
    </button>
</div>

<!-- ACTUALITÉS FEED -->
<section class="actualites-feed">
    @forelse($actualites as $actu)
    <div class="actualite-card" style="transition:0.3s; border-radius:16px; overflow:hidden; box-shadow:0 6px 20px rgba(0,0,0,0.08); background:#fff;">
        
        <!-- Media -->
        <div class="media" style="width:100%; max-height:350px; display:flex; justify-content:center; align-items:center; overflow:hidden; background:#f0f0f0;">
            @if($actu->url_media)
                @if($actu->type_media === 'mp4')
                    <video controls style="width:100%; max-height:350px; object-fit:contain;">
                        <source src="{{ asset($actu->url_media) }}" type="video/mp4">
                    </video>
                @else
                    <img src="{{ asset($actu->url_media) }}" alt="Image" style="width:auto; max-width:100%; max-height:350px; object-fit:cover;">
                @endif
            @else
                <img src="{{ asset('medias/default_news.jpg') }}" alt="Default" style="width:auto; max-width:100%; max-height:350px; object-fit:cover;">
            @endif
        </div>

        <!-- Body -->
        <div class="actualite-body" style="padding:20px;">
            <div class="author" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <img src="{{ asset('medias/default.jpg') }}" alt="Auteur" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                    <h4 style="font-size:16px; font-weight:700; color:#0D3B66; margin:0;">{{ $actu->auteur_nom }}</h4>
                </div>
                <span class="date" style="font-size:12px; color:#888;">{{ \Carbon\Carbon::parse($actu->date_publication)->format('d/m/Y') }}</span>
            </div>

            <p class="contenu" data-full="{{ $actu->contenu }}" data-truncated="{{ Str::limit($actu->contenu,200) }}" style="font-size:14px; line-height:1.6; color:#2c3e50; margin-bottom:10px;">
                {{ Str::limit($actu->contenu,200) }}
            </p>
            <button class="btnVoirPlus" onclick="voirPlus(this)" style="background:transparent; border:none; color:#FF6F61; font-weight:600; cursor:pointer; transition:0.3s;">Voir plus</button>
        </div>

        <!-- Actions -->
        <div class="actions" style="display:flex; justify-content:flex-end; gap:10px; padding:10px 20px; border-top:1px solid rgba(0,0,0,0.05);">
            <a href="{{ route('admin.actualiteEdit', $actu->id) }}" style="color:#3498db; font-size:16px; transition:0.3s;"><i class="fa fa-pen"></i></a>
            <form action="{{ route('admin.actualiteDestroy', $actu->id) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" style="color:#e74c3c; background:transparent; border:none; font-size:16px; cursor:pointer; transition:0.3s;">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <p style="text-align:center; color:#888; margin-top:20px;">Aucune actualité disponible.</p>
    @endforelse
</section>

<!-- MODAL AJOUT -->
<div id="modalAdd" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:1000;">
    <div class="modal-content" style="background:#fff; padding:24px; border-radius:16px; width:450px; box-shadow:0 12px 28px rgba(0,0,0,0.25);">
        <h3 style="margin-bottom:15px;">Ajouter une actualité</h3>
        <form action="{{ route('admin.actualiteStore') }}" method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:10px;">
            @csrf
            <label>Auteur :</label>
            <input type="text" name="auteur_nom" value="{{ $admin->nom }}" readonly style="padding:10px; border-radius:12px; border:1px solid #ccc;">
            <label>Contenu :</label>
            <textarea name="contenu" required style="padding:10px; border-radius:12px; border:1px solid #ccc; min-height:100px;"></textarea>
            <label>Image ou vidéo :</label>
            <input type="file" name="url_media" accept="image/*,video/*">
            <button type="submit" class="publish" style="padding:12px; border:none; border-radius:12px; background:#FF6F61; color:#fff; font-weight:600; cursor:pointer;">Publier</button>
            <button type="button" class="cancel" onclick="document.getElementById('modalAdd').style.display='none'" style="padding:12px; border:1px solid #FF6F61; border-radius:12px; background:transparent; color:#FF6F61; cursor:pointer;">Annuler</button>
        </form>
    </div>
</div>

<script>
function voirPlus(btn){
    const p = btn.previousElementSibling;
    if(btn.innerText==="Voir plus"){ 
        p.innerText = p.dataset.full; 
        btn.innerText = "Voir moins"; 
    }
    else { 
        p.innerText = p.dataset.truncated; 
        btn.innerText = "Voir plus"; 
    }
}

// Fermer le modal en cliquant en dehors
document.getElementById('modalAdd').addEventListener('click', function(e) {
    if (e.target === this) {
        this.style.display = 'none';
    }
});
</script>

@endsection