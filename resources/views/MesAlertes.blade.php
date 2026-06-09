@extends('citoyen')

@section('title', 'Signaler une urgence — CongoAssist')

@push('head-styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Signaler une urgence</h1>
        <p>Choisissez le service et remplissez le formulaire — l'alerte sera transmise immédiatement.</p>
    </div>
</div>

{{-- Info banner --}}
<div class="alert alert-info anim-fade" style="margin-bottom:24px;">
    <i data-feather="info" style="width:16px;height:16px;flex-shrink:0;"></i>
    <span>Votre alerte est transmise de façon confidentielle aux services compétents. N'utilisez ce service qu'en cas de réelle urgence.</span>
</div>

{{-- Services d'urgence --}}
<div style="margin-bottom:28px;">
    <h2 style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:16px;">Choisir un service d'urgence</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;">
        @foreach($services as $service)
        <a href="{{ $service->lien }}"
           style="display:flex;align-items:center;gap:14px;padding:18px 16px;
                  background:var(--surface);border:1.5px solid var(--border);
                  border-radius:var(--radius-lg);text-decoration:none;
                  transition:border-color .15s,box-shadow .15s,transform .15s;"
           onmouseover="this.style.borderColor='#2563eb';this.style.boxShadow='var(--shadow)';this.style.transform='translateY(-2px)'"
           onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='none';this.style.transform='translateY(0)'">
            <div style="width:42px;height:42px;border-radius:10px;background:var(--accent-light);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i data-feather="alert-triangle" style="width:18px;height:18px;color:var(--accent);"></i>
            </div>
            <div>
                <div style="font-size:13.5px;font-weight:700;color:var(--text);">{{ $service->nom_service }}</div>
                <div style="font-size:12px;color:var(--text-sec);margin-top:2px;">Signaler →</div>
            </div>
        </a>
        @endforeach
    </div>
</div>

{{-- Formulaire rapide --}}
<div class="card anim-fade" style="animation-delay:.1s;margin-bottom:28px;">
    <div class="card-header" style="padding-bottom:16px;border-bottom:1px solid var(--border);">
        <span class="card-title" style="font-size:15px;">Signalement rapide depuis l'accueil</span>
        <span class="pill pill-green">
            <i data-feather="zap" style="width:11px;height:11px;"></i>
            Envoi immédiat
        </span>
    </div>
    <div class="card-body">
        <form action="{{ route('enregistrerAlerte') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Type d'urgence <span style="color:var(--red)">*</span></label>
                    <select name="type_alerte" class="form-control" required>
                        <option value="">— Sélectionnez —</option>
                        @foreach($services as $service)
                        <option value="{{ $service->nom_service }}">{{ $service->nom_service }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Localisation <span style="color:var(--red)">*</span></label>
                    <input type="text" name="localisation" class="form-control"
                           placeholder="Ex : Avenue de l'O.U.A, Bacongo" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description de la situation</label>
                <textarea name="description" rows="4" class="form-control"
                          placeholder="Décrivez brièvement ce que vous observez..."></textarea>
            </div>

            {{-- Message vocal --}}
            <div class="form-group">
                <label class="form-label">Message vocal <span style="font-weight:400;color:var(--text-muted)">(optionnel)</span></label>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <button type="button" id="recordBtn" class="btn btn-secondary">
                        <i data-feather="mic" style="width:14px;height:14px;"></i> Enregistrer
                    </button>
                    <button type="button" id="stopBtn" class="btn btn-secondary" disabled
                            style="opacity:.5;cursor:not-allowed;">
                        <i data-feather="square" style="width:14px;height:14px;"></i> Arrêter
                    </button>
                    <span id="recordingStatus" style="display:none;align-items:center;gap:6px;font-size:12.5px;color:var(--red);font-weight:600;">
                        <span style="width:7px;height:7px;border-radius:50%;background:var(--red);animation:pulse 1.4s ease-in-out infinite;display:inline-block;"></span>
                        Enregistrement…
                    </span>
                </div>
                <audio id="audioPreview" controls style="margin-top:10px;display:none;width:100%;border-radius:var(--radius);"></audio>
                <input type="file" name="audio" id="audioInput" style="display:none;" accept="audio/*">
            </div>

            <div style="display:flex;justify-content:flex-end;padding-top:8px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="send" style="width:14px;height:14px;"></i>
                    Envoyer l'alerte
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Numéros d'urgence --}}
<div class="card anim-fade" style="animation-delay:.18s;">
    <div class="card-header" style="padding-bottom:14px;border-bottom:1px solid var(--border);">
        <span class="card-title" style="font-size:15px;">Numéros d'urgence nationaux</span>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;">
            @foreach([['15','SAMU','Urgences médicales','bg-red-soft'],['18','Pompiers','Incendie & secours','bg-amber-soft'],['17','Police','Forces de l\'ordre','bg-blue-soft'],['112','Urgence univ.','Tout type','bg-green-soft']] as $num)
            <div style="padding:16px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-lg);text-align:center;">
                <div style="font-size:26px;font-weight:800;color:var(--text);line-height:1;">{{ $num[0] }}</div>
                <div style="font-size:12px;font-weight:700;color:var(--text);margin-top:4px;">{{ $num[1] }}</div>
                <div style="font-size:11px;color:var(--text-sec);margin-top:2px;">{{ $num[2] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.4)} }
</style>
@endpush

@push('scripts')
<script>
let mediaRecorder, audioChunks = [];
const recordBtn = document.getElementById('recordBtn');
const stopBtn   = document.getElementById('stopBtn');
const preview   = document.getElementById('audioPreview');
const input     = document.getElementById('audioInput');
const status    = document.getElementById('recordingStatus');

recordBtn.addEventListener('click', async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];
        mediaRecorder.addEventListener('dataavailable', e => audioChunks.push(e.data));
        mediaRecorder.addEventListener('stop', () => {
            const blob = new Blob(audioChunks, { type: 'audio/webm' });
            preview.src = URL.createObjectURL(blob);
            preview.style.display = 'block';
            const file = new File([blob], 'alerte_audio.webm', { type: 'audio/webm' });
            const dt = new DataTransfer(); dt.items.add(file);
            input.files = dt.files;
            recordBtn.disabled = false;
            stopBtn.disabled = true;
            stopBtn.style.opacity = '.5';
            status.style.display = 'none';
            feather.replace({ 'stroke-width': 1.75 });
        });
        mediaRecorder.start();
        recordBtn.disabled = true;
        stopBtn.disabled = false;
        stopBtn.style.opacity = '1';
        stopBtn.style.cursor = 'pointer';
        status.style.display = 'flex';
    } catch(e) { alert('Impossible d\'accéder au microphone.'); }
});
stopBtn.addEventListener('click', () => { if(mediaRecorder && mediaRecorder.state !== 'inactive') mediaRecorder.stop(); });
</script>
@endpush

@endsection
