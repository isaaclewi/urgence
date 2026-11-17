{{-- Selfie avec défi : consigne aléatoire (code + geste) valable 10 minutes. Inclus dans l'inscription et la correction du dossier. --}}
<div id="selfieBox">
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Selfie de vérification (visage bien visible, sans lunettes ni masque)
    </label>

    <input type="hidden" name="defi_id" id="defiId">
    <input type="hidden" name="selfie_direct" id="selfieDirect" value="0">

    <div id="defiTexte" class="hidden mb-3 p-4 bg-amber-50 border-2 border-amber-300 rounded-lg text-sm text-amber-900 font-semibold"></div>

    <video id="selfieVideo" autoplay playsinline muted class="hidden w-full rounded-lg bg-black"></video>
    <img id="selfiePreview" alt="Aperçu du selfie" class="hidden w-full rounded-lg border">
    <canvas id="selfieCanvas" class="hidden"></canvas>

    {{-- Rempli par la caméra ; devient visible si la caméra est indisponible (selfie alors marqué « importé ») --}}
    <input type="file" name="selfie" id="selfieInput" accept="image/jpeg,image/png,image/webp" capture="user"
           class="hidden w-full px-4 py-3 border-2 border-gray-200 rounded-lg">

    <div class="flex flex-wrap gap-3 mt-3">
        <button type="button" id="selfieStart" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold">
            Ouvrir la caméra et recevoir la consigne
        </button>
        <button type="button" id="selfieSnap" class="hidden bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold">
            Prendre la photo
        </button>
        <button type="button" id="selfieRetry" class="hidden bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2.5 rounded-lg text-sm font-semibold">
            Reprendre
        </button>
    </div>
    <p id="selfieInfo" class="text-xs text-gray-500 mt-2">Une consigne différente est générée à chaque fois ; elle est valable 10 minutes.</p>
</div>

<script>
(function () {
    var $ = function (id) { return document.getElementById(id); };
    var video = $('selfieVideo'), canvas = $('selfieCanvas'), preview = $('selfiePreview'),
        input = $('selfieInput'), info = $('selfieInfo'), stream = null;

    function arreter() { if (stream) { stream.getTracks().forEach(function (t) { t.stop(); }); stream = null; } }

    function recevoirDefi() {
        return fetch("{{ route('citoyen.defi') }}", { headers: { 'Accept': 'application/json' }, cache: 'no-store' })
            .then(function (r) { if (!r.ok) throw new Error('defi'); return r.json(); })
            .then(function (d) {
                $('defiId').value = d.id;
                $('defiTexte').textContent = 'Consigne : ' + d.texte;
                $('defiTexte').classList.remove('hidden');
            });
    }

    function modeSecours(msg) {
        // Caméra indisponible : l'envoi d'un fichier reste possible, mais le dossier sera marqué « importé ».
        info.textContent = msg;
        input.classList.remove('hidden');
        $('selfieDirect').value = '0';
        input.addEventListener('change', function () { $('selfieDirect').value = '0'; });
        $('selfieStart').classList.add('hidden');
    }

    $('selfieStart').addEventListener('click', function () {
        recevoirDefi().then(function () {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                return modeSecours('Caméra indisponible sur cet appareil : prenez le selfie avec la consigne ci-dessus puis choisissez le fichier.');
            }
            return navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false }).then(function (s) {
                stream = s; video.srcObject = s;
                video.classList.remove('hidden'); preview.classList.add('hidden');
                $('selfieStart').classList.add('hidden'); $('selfieSnap').classList.remove('hidden');
                info.textContent = 'Suivez la consigne, puis appuyez sur « Prendre la photo ».';
            }).catch(function () {
                modeSecours('Accès à la caméra refusé : prenez le selfie avec la consigne puis choisissez le fichier.');
            });
        }).catch(function () { info.textContent = 'Impossible de recevoir la consigne. Vérifiez votre connexion et réessayez.'; });
    });

    $('selfieSnap').addEventListener('click', function () {
        if (!video.videoWidth) return;
        canvas.width = video.videoWidth; canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        canvas.toBlob(function (blob) {
            var dt = new DataTransfer();
            dt.items.add(new File([blob], 'selfie.jpg', { type: 'image/jpeg' }));
            input.files = dt.files;
            $('selfieDirect').value = '1';
            preview.src = URL.createObjectURL(blob);
            preview.classList.remove('hidden'); video.classList.add('hidden');
            $('selfieSnap').classList.add('hidden'); $('selfieRetry').classList.remove('hidden');
            info.textContent = 'Photo prise. Vérifiez que le code et le geste sont bien visibles, sinon appuyez sur « Reprendre ».';
            arreter();
        }, 'image/jpeg', 0.9);
    });

    $('selfieRetry').addEventListener('click', function () {
        input.value = ''; $('selfieDirect').value = '0';
        $('selfieRetry').classList.add('hidden'); preview.classList.add('hidden');
        $('selfieStart').click();
    });

    window.addEventListener('beforeunload', arreter);

    // Empêche l'envoi sans selfie (le champ fichier est masqué, donc non contrôlé par le navigateur)
    var form = input.closest('form');
    if (form) form.addEventListener('submit', function (e) {
        if (!input.files || !input.files.length || !$('defiId').value) {
            e.preventDefault();
            info.textContent = 'Prenez d\'abord le selfie avec la consigne.';
            info.className = 'text-sm text-red-600 mt-2 font-semibold';
            $('selfieBox').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
})();
</script>
