@extends('citoyen')

@section('title', 'Alerte Police — CongoAssist')

@push('head-styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Alerte Police</h1>
        <p>Signalez une situation nécessitant l'intervention des forces de l'ordre.</p>
    </div>
</div>

{{-- Numéros d'urgence --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;margin-bottom:24px;" class="anim-fade">
    @foreach([['117','Police','bg-blue-soft'],['118','Pompiers','bg-amber-soft'],['15','SAMU','bg-red-soft']] as $n)
    <div class="card" style="padding:18px;text-align:center;">
        <div style="font-size:28px;font-weight:800;color:var(--text);line-height:1;">{{ $n[0] }}</div>
        <div style="font-size:12px;font-weight:700;color:var(--text);margin-top:4px;">{{ $n[1] }}</div>
    </div>
    @endforeach
</div>

<div class="alert alert-info anim-fade" style="margin-bottom:24px;">
    <i data-feather="info" style="width:16px;height:16px;flex-shrink:0;"></i>
    <span>Cette alerte sera transmise immédiatement aux forces de l'ordre. N'utilisez ce service qu'en cas de réelle nécessité.</span>
</div>

<div class="card anim-fade">
    <div class="card-body">
        <form action="{{ route('alertePolice.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Titre</label>
                <input type="text" name="titre" value="Police" readonly class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Description de la situation <span style="color:var(--red)">*</span></label>
                <textarea name="description" rows="5" required class="form-control"
                          placeholder="Décrivez précisément la situation (type d'incident, personnes impliquées...)"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Type d'intervention</label>
                <select name="type_alerte" required class="form-control">
                    <option value="police">👮 Intervention de la Police</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Photo <span style="font-weight:400;color:var(--text-muted)">(optionnel)</span></label>
                <input type="file" name="media_photo" accept="image/*" class="form-control">
            </div>

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

            <div class="form-group">
                <label class="form-label">Localisation</label>
                <div id="adresse" style="padding:14px;background:var(--accent-light);border:1.5px solid #bfdbfe;border-radius:var(--radius);display:flex;align-items:center;gap:10px;">
                    <i data-feather="loader" style="width:16px;height:16px;color:var(--accent);animation:spin 1s linear infinite;"></i>
                    <span style="font-size:13px;color:var(--accent);font-weight:500;">Détection de votre position...</span>
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
                <button type="submit" class="btn btn-primary btn-lg"
                        onclick="return confirm('Confirmer l\'envoi de cette alerte aux forces de l\'ordre ?')">
                    <i data-feather="shield" style="width:14px;height:14px;"></i>
                    Envoyer l'alerte Police
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
        L.marker([lat, lon]).addTo(map).bindPopup('<b>Votre position</b>').openPopup();
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json&accept-language=fr`)
            .then(r => r.json()).then(data => {
                adresseDiv.innerHTML = `<i data-feather="map-pin" style="width:16px;height:16px;color:var(--green);flex-shrink:0;"></i><span style="font-size:13px;">${data.display_name}</span>`;
                locInput.value = data.display_name;
                feather.replace({ 'stroke-width': 1.75 });
            });
    }, () => { adresseDiv.innerHTML = '<span style="color:var(--red)">Géolocalisation refusée</span>'; });

    let mr, chunks = [];
    document.getElementById('record-btn').addEventListener('click', async () => {
        if (!mr || mr.state === 'inactive') {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mr = new MediaRecorder(stream); chunks = [];
            mr.ondataavailable = e => chunks.push(e.data);
            mr.onstop = () => {
                const blob = new Blob(chunks, { type: 'audio/webm' });
                const ap = document.getElementById('audio-playback');
                ap.src = URL.createObjectURL(blob); ap.style.display = 'block';
                const r = new FileReader(); r.readAsDataURL(blob);
                r.onloadend = () => { document.getElementById('media_vocal').value = r.result; };
                document.getElementById('record-status').textContent = 'Enregistrement terminé';
            };
            mr.start();
            document.getElementById('record-status').textContent = 'Enregistrement en cours…';
        } else if (mr.state === 'recording') mr.stop();
    });
});
</script>
@endpush

@endsection
