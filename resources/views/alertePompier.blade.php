@extends('citoyen')

@section('title', 'Alerte Pompiers — CongoAssist')

@push('head-styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Alerte Pompiers</h1>
        <p>Signalez un incendie ou une situation d'urgence grave.</p>
    </div>
</div>

{{-- Numéros --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;margin-bottom:24px;" class="anim-fade">
    @foreach([['118','Pompiers'],['117','Police'],['15','SAMU']] as $n)
    <div class="card" style="padding:18px;text-align:center;">
        <div style="font-size:28px;font-weight:800;color:var(--text);line-height:1;">{{ $n[0] }}</div>
        <div style="font-size:12px;font-weight:700;color:var(--text);margin-top:4px;">{{ $n[1] }}</div>
    </div>
    @endforeach
</div>

<div class="alert alert-warning anim-fade" style="margin-bottom:24px;">
    <i data-feather="alert-octagon" style="width:16px;height:16px;flex-shrink:0;"></i>
    <span>Alerte prioritaire — N'utilisez ce service qu'en cas d'incendie, accident grave, fuite de gaz ou urgence nécessitant les pompiers.</span>
</div>

<div class="card anim-fade">
    <div class="card-body">
        <form action="{{ route('alertePompier.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Titre</label>
                <input type="text" name="titre" value="Pompier" readonly class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Description de l'urgence <span style="color:var(--red)">*</span></label>
                <textarea name="description" rows="5" required class="form-control"
                          placeholder="Décrivez précisément : incendie, fuite de gaz, accident... Type, ampleur, personnes en danger."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Type d'intervention</label>
                <select name="type_alerte" required class="form-control">
                    <option value="pompier">🚒 Intervention des Pompiers</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Photo <span style="font-weight:400;color:var(--text-muted)">(optionnel)</span></label>
                <input type="file" name="media_photo" accept="image/*" class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Message vocal <span style="font-weight:400;color:var(--text-muted)">(optionnel)</span></label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <button type="button" id="startRecording" class="btn btn-secondary">
                        <i data-feather="mic" style="width:14px;height:14px;"></i> Démarrer
                    </button>
                    <button type="button" id="stopRecording" class="btn btn-danger" disabled style="opacity:.5;">
                        <i data-feather="square" style="width:14px;height:14px;"></i> Arrêter
                    </button>
                </div>
                <audio id="audioPlayback" controls style="margin-top:10px;display:none;width:100%;border-radius:var(--radius);"></audio>
                <input type="hidden" name="media_vocal" id="media_vocal">
            </div>

            <div class="form-group">
                <label class="form-label">Localisation</label>
                <div id="adresse" style="padding:14px;background:#fff7ed;border:1.5px solid #fed7aa;border-radius:var(--radius);display:flex;align-items:center;gap:10px;">
                    <i data-feather="loader" style="width:16px;height:16px;color:#ea580c;animation:spin 1s linear infinite;"></i>
                    <span style="font-size:13px;color:#ea580c;font-weight:500;">Détection de votre position...</span>
                </div>
            </div>

            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="localisation" id="localisation">

            <div class="form-group">
                <label class="form-label">Carte de localisation</label>
                <div id="map" style="width:100%;height:340px;border-radius:var(--radius-lg);border:1px solid var(--border);"></div>
            </div>

            <div style="display:flex;justify-content:flex-end;padding-top:8px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-danger btn-lg"
                        onclick="return confirm('🚨 Confirmer l\'envoi de l\'alerte aux pompiers ?')">
                    <i data-feather="alert-triangle" style="width:14px;height:14px;"></i>
                    Envoyer l'alerte Pompiers
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const adresseDiv = document.getElementById('adresse');
    const locInput = document.getElementById('localisation');
    const latInput = document.getElementById('latitude');
    const lonInput = document.getElementById('longitude');

    navigator.geolocation?.getCurrentPosition(pos => {
        const lat = pos.coords.latitude, lon = pos.coords.longitude;
        latInput.value = lat; lonInput.value = lon;
        const map = L.map('map').setView([lat, lon], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
        L.marker([lat, lon]).addTo(map).bindPopup('<b>🚒 Votre position</b>').openPopup();
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json&accept-language=fr`)
            .then(r => r.json()).then(data => {
                adresseDiv.innerHTML = `<i data-feather="map-pin" style="width:16px;height:16px;color:var(--green);flex-shrink:0;"></i><span style="font-size:13px;">${data.display_name}</span>`;
                locInput.value = data.display_name;
                feather.replace({ 'stroke-width': 1.75 });
            });
    }, () => { adresseDiv.innerHTML = '<span style="color:var(--red)">Géolocalisation refusée</span>'; });

    let mr, chunks = [];
    const startBtn = document.getElementById('startRecording');
    const stopBtn  = document.getElementById('stopRecording');

    startBtn.addEventListener('click', async () => {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mr = new MediaRecorder(stream); chunks = [];
        mr.ondataavailable = e => chunks.push(e.data);
        mr.onstop = () => {
            const blob = new Blob(chunks, { type: 'audio/webm' });
            const ap = document.getElementById('audioPlayback');
            const r = new FileReader(); r.readAsDataURL(blob);
            r.onloadend = () => {
                document.getElementById('media_vocal').value = r.result;
                ap.src = r.result; ap.style.display = 'block';
            };
        };
        mr.start();
        startBtn.disabled = true; stopBtn.disabled = false; stopBtn.style.opacity = '1';
    });
    stopBtn.addEventListener('click', () => {
        mr.stop(); startBtn.disabled = false; stopBtn.disabled = true; stopBtn.style.opacity = '.5';
    });
});
</script>
@endpush

@endsection
