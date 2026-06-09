@extends('citoyen')

@section('title', 'Nouvelle Alerte Hôpital — CongoAssist')

@push('head-styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Nouvelle Alerte — Hôpital</h1>
        <p>Signalez une urgence hospitalière immédiatement.</p>
    </div>
</div>

<div class="alert alert-info anim-fade" style="margin-bottom:24px;">
    <i data-feather="info" style="width:16px;height:16px;flex-shrink:0;"></i>
    <span>Cette alerte sera transmise immédiatement aux services d'urgence hospitalière. Assurez-vous que les informations sont exactes.</span>
</div>

<div class="card anim-fade">
    <div class="card-body">
        <form id="formAlerte" action="{{ route('alerteHopital.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Titre</label>
                <input type="text" name="titre" value="Hopital" readonly class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Description de l'urgence <span style="color:var(--red)">*</span></label>
                <textarea name="description" rows="5" required class="form-control"
                          placeholder="Décrivez précisément la situation d'urgence..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Type d'urgence</label>
                <select name="type_alerte" required class="form-control">
                    <option value="hopital">🏥 Intervention hospitalière</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Photo <span style="font-weight:400;color:var(--text-muted)">(optionnel)</span></label>
                <input type="file" name="media_photo" accept="image/*" class="form-control">
            </div>

            {{-- Message vocal --}}
            <div class="form-group">
                <label class="form-label">Message vocal <span style="font-weight:400;color:var(--text-muted)">(optionnel)</span></label>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <button type="button" id="record-btn" class="btn btn-secondary">
                        <i data-feather="mic" style="width:14px;height:14px;"></i> Enregistrer
                    </button>
                    <span id="record-status" style="font-size:12.5px;color:var(--text-sec);">Aucun enregistrement</span>
                </div>
                <audio id="audio-playback" controls style="margin-top:10px;display:none;width:100%;border-radius:var(--radius);"></audio>
                <input type="hidden" name="media_vocal" id="media_vocal">
            </div>

            {{-- Localisation --}}
            <div class="form-group">
                <label class="form-label">Localisation</label>
                <div id="adresse" style="padding:14px;background:var(--accent-light);border:1.5px solid #bfdbfe;border-radius:var(--radius);display:flex;align-items:center;gap:10px;">
                    <i data-feather="loader" style="width:16px;height:16px;color:var(--accent);animation:spin 1s linear infinite;"></i>
                    <span style="font-size:13px;color:var(--accent);font-weight:500;">Recherche de votre position...</span>
                </div>
            </div>

            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="localisation" id="localisation">

            <div class="form-group">
                <label class="form-label">Carte interactive</label>
                <div id="map" style="width:100%;height:340px;border-radius:var(--radius-lg);border:1px solid var(--border);"></div>
            </div>

            <div style="display:flex;justify-content:flex-end;padding-top:8px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-danger btn-lg"
                        onclick="return confirm('Confirmer l\'envoi de cette alerte d\'urgence hospitalière ?')">
                    <i data-feather="send" style="width:14px;height:14px;"></i>
                    Envoyer l'alerte
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const adresseDiv = document.getElementById('adresse');
    const locInput   = document.getElementById('localisation');
    const latInput   = document.getElementById('latitude');
    const lonInput   = document.getElementById('longitude');

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude, lon = pos.coords.longitude;
            latInput.value = lat; lonInput.value = lon;

            const map = L.map('map').setView([lat, lon], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);
            L.marker([lat, lon]).addTo(map).bindPopup('<b>Votre position</b>').openPopup();

            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json&accept-language=fr`)
                .then(r => r.json()).then(data => {
                    adresseDiv.innerHTML = `<i data-feather="map-pin" style="width:16px;height:16px;color:var(--green);flex-shrink:0;"></i><span style="font-size:13px;color:var(--text);">${data.display_name ?? 'Adresse non trouvée'}</span>`;
                    locInput.value = data.display_name ?? 'Adresse non trouvée';
                    feather.replace({ 'stroke-width': 1.75 });
                }).catch(() => { adresseDiv.innerHTML = '<span>Erreur de récupération d\'adresse</span>'; });
        }, () => { adresseDiv.innerHTML = '<span style="color:var(--red)">Géolocalisation refusée</span>'; });
    }

    // Vocal
    const recordBtn    = document.getElementById('record-btn');
    const audioPreview = document.getElementById('audio-playback');
    const vocalInput   = document.getElementById('media_vocal');
    const status       = document.getElementById('record-status');
    let mr, chunks = [];

    recordBtn.addEventListener('click', async () => {
        if (!mr || mr.state === 'inactive') {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mr = new MediaRecorder(stream); chunks = [];
            mr.ondataavailable = e => chunks.push(e.data);
            mr.onstop = () => {
                const blob = new Blob(chunks, { type: 'audio/webm' });
                audioPreview.src = URL.createObjectURL(blob);
                audioPreview.style.display = 'block';
                const reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = () => { vocalInput.value = reader.result; };
                recordBtn.innerHTML = '<i data-feather="mic" style="width:14px;height:14px;"></i> Enregistrer';
                status.textContent = 'Enregistrement terminé';
                feather.replace({ 'stroke-width': 1.75 });
            };
            mr.start();
            recordBtn.innerHTML = '<i data-feather="square" style="width:14px;height:14px;"></i> Arrêter';
            status.textContent = 'Enregistrement en cours…';
            feather.replace({ 'stroke-width': 1.75 });
        } else if (mr.state === 'recording') {
            mr.stop();
        }
    });
});
</script>
@endpush

@endsection
