<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="MV6linsONY2-JJ_AcRM1kpei_58RFHjlG-jI5TYCk-w" />
    <title>CongoAssist - Signalement des risques</title>
   <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0d6efd">
<link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link rel="apple-touch-icon" href="/icons/icon-512.png">
<meta name="theme-color" content="#10b981">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .category-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 10;
        }

        .image-overlay {
            position: relative;
            overflow: hidden;
        }

        .image-overlay::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3));
        }

        .nav-link {
            position: relative;
            padding-bottom: 4px;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #10b981, #3b82f6);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #10b981 100%);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Navbar -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="container mx-auto flex justify-between items-center px-6 py-4">
            <div class="flex items-center space-x-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-blue-600 to-green-500 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">C</span>
                </div>
                <span class="text-2xl font-bold">
                    <span class="text-blue-800">Congo</span><span class="text-green-500">Assist</span>
                </span>
            </div>

            <ul class="hidden md:flex space-x-8 text-gray-700 font-medium">
                <li><a href="{{ route('accueil') }}" class="nav-link hover:text-blue-600 transition">Accueil</a></li>
                <li><a href="{{ route('avis') }}" class="nav-link hover:text-blue-600 transition">Avis</a></li>
                <li><a href="{{ route('regle') }}" class="nav-link hover:text-blue-600 transition">Règlement</a></li>
            </ul>

            <a href="{{ route('login') }}"
                class="hidden md:inline-block bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-2.5 rounded-lg font-semibold transition shadow-md hover:shadow-lg">
                Connexion
            </a>

            <button id="menu-btn" class="md:hidden text-gray-700">
                <i data-feather="menu"></i>
            </button>
        </nav>

        <div id="mobile-menu" class="hidden bg-white md:hidden px-6 py-4 space-y-4 border-t">
            <a href="{{ route('accueil') }}" class="block text-gray-700 hover:text-blue-600">Accueil</a>
            <a href="{{ route('avis') }}" class="block text-gray-700 hover:text-blue-600">Avis</a>
            <a href="{{ route('regle') }}" class="block text-gray-700 hover:text-blue-600">Règlement</a>
            <a href="{{ route('login') }}"
                class="block bg-green-500 text-white text-center py-2 rounded-lg font-semibold">Connexion</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <!-- Hero Section -->
                <section class="bg-white rounded-2xl shadow-md p-10 md:p-14 mb-12 animate-fade-in">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">

                <!-- Texte -->
                <div>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-5 leading-tight">
                        Congo<span class="text-green-600">Assist</span>
                    </h1>

                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Plateforme communautaire de signalement des urgences permettant
                        d’alerter rapidement les services compétents pour renforcer
                        la sécurité et la protection des citoyens.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold transition shadow">
                            Faire un signalement
                        </a>

                        <a href="{{ route('regle') }}"
                            class="inline-flex items-center justify-center border border-gray-300 hover:border-gray-400 text-gray-700 px-8 py-3 rounded-lg font-semibold transition">
                            En savoir plus
                        </a>
                    </div>
                </div>

                <!-- Nouvelle Illustration -->
                <div class="flex justify-center md:justify-end">
                    <img src="medias/télécharger (8).jfif" alt="Urgence et sécurité illustration"
                        class="w-full max-w-sm rounded-lg">

                </div>

            </div>
        </section>

        <!-- Featured Cards -->
        <section class="mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Large Card 1 -->
                <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden h-80 relative group">
                    <div class="image-overlay h-full">
                        <img src="medias/UE.jpeg" alt="Urgence médicale" class="w-full h-full object-cover">
                    </div>
                    <span class="category-badge bg-red-500 text-white">URGENCE MÉDICALE</span>
                    <div
                        class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/80 to-transparent z-10">
                        <div class="flex items-center mb-2 text-white text-sm">
                            <i data-feather="heart" class="w-4 h-4 mr-2"></i>
                            <span>7 janvier 2026</span>
                        </div>
                        <h3 class="text-white text-2xl font-bold mb-2">Urgence Médicale</h3>
                        <p class="text-white/90 text-sm">Signalez rapidement les situations nécessitant une ambulance ou
                            des secours</p>
                    </div>
                </div>

                <!-- Large Card 2 -->
                <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden h-80 relative group">
                    <div class="image-overlay h-full">
                        <img src="medias/dd038250-b88a-4d0a-ba6a-0b484559d8bd.jpeg" alt="Pompiers"
                            class="w-full h-full object-cover">
                    </div>
                    <span class="category-badge bg-orange-500 text-white">POMPIERS</span>
                    <div
                        class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/80 to-transparent z-10">
                        <div class="flex items-center mb-2 text-white text-sm">
                            <i data-feather="alert-triangle" class="w-4 h-4 mr-2"></i>
                            <span>7 janvier 2026</span>
                        </div>
                        <h3 class="text-white text-2xl font-bold mb-2">Pompiers</h3>
                        <p class="text-white/90 text-sm">Alertez les sapeurs-pompiers pour tout incendie ou accident
                            grave</p>
                    </div>
                </div>
            </div>

            <!-- Recent Posts -->
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Autres Services</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="card-hover bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="image-overlay h-48">
                        <img src="medias/912a6016-81c1-4f66-b02b-1639e12e9bae.jpeg" alt="Police"
                            class="w-full h-full object-cover">
                    </div>
                    <div class="p-5">
                        <span
                            class="inline-block bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded mb-3">POLICE</span>
                        <p class="text-gray-500 text-sm mb-2">7 janvier 2026</p>
                        <h3 class="text-gray-900 font-bold text-lg mb-2 hover:text-blue-600 transition">Police &
                            Sécurité</h3>
                        <p class="text-gray-600 text-sm">Signalez toute activité suspecte nécessitant l'intervention des
                            forces de l'ordre</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="card-hover bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="image-overlay h-48">
                        <img src="https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=600" alt="Routes"
                            class="w-full h-full object-cover">
                    </div>
                    <div class="p-5">
                        <span
                            class="inline-block bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded mb-3">ROUTES</span>
                        <p class="text-gray-500 text-sm mb-2">7 janvier 2026</p>
                        <h3 class="text-gray-900 font-bold text-lg mb-2 hover:text-blue-600 transition">Routes Urbaines
                        </h3>
                        <p class="text-gray-600 text-sm">Alertez sur les dangers routiers et infrastructures
                            défectueuses</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="card-hover bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="image-overlay h-48">
                        <img src="https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?w=600" alt="Déchets"
                            class="w-full h-full object-cover">
                    </div>
                    <div class="p-5">
                        <span
                            class="inline-block bg-green-500 text-white text-xs font-bold px-3 py-1 rounded mb-3">PROPRETÉ</span>
                        <p class="text-gray-500 text-sm mb-2">7 janvier 2026</p>
                        <h3 class="text-gray-900 font-bold text-lg mb-2 hover:text-blue-600 transition">Déchets &
                            Propreté</h3>
                        <p class="text-gray-600 text-sm">Signalez les problèmes d'hygiène publique et dépôts sauvages
                        </p>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="card-hover bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="image-overlay h-48">
                        <img src="https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=600"
                            alt="Électricité" class="w-full h-full object-cover">
                    </div>
                    <div class="p-5">
                        <span
                            class="inline-block bg-yellow-400 text-gray-900 text-xs font-bold px-3 py-1 rounded mb-3">ÉLECTRICITÉ</span>
                        <p class="text-gray-500 text-sm mb-2">7 janvier 2026</p>
                        <h3 class="text-gray-900 font-bold text-lg mb-2 hover:text-blue-600 transition">Panne
                            Électricité</h3>
                        <p class="text-gray-600 text-sm">Signalez les problèmes d'électricité dans votre quartier</p>
                    </div>
                </div>

                <!-- Card 5 -->
                <div class="card-hover bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="image-overlay h-48">
                        <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?w=600" alt="Carte"
                            class="w-full h-full object-cover">
                    </div>
                    <div class="p-5">
                        <span
                            class="inline-block bg-purple-500 text-white text-xs font-bold px-3 py-1 rounded mb-3">CARTE</span>
                        <p class="text-gray-500 text-sm mb-2">7 janvier 2026</p>
                        <h3 class="text-gray-900 font-bold text-lg mb-2 hover:text-blue-600 transition">Carte des
                            Urgences</h3>
                        <p class="text-gray-600 text-sm">Visualisez en temps réel les signalements dans votre ville</p>
                    </div>
                </div>

                <!-- Card 6 -->
                <div class="card-hover bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="image-overlay h-48">
                        <img src="https://images.unsplash.com/photo-1516321497487-e288fb19713f?w=600" alt="Info"
                            class="w-full h-full object-cover">
                    </div>
                    <div class="p-5">
                        <span
                            class="inline-block bg-indigo-500 text-white text-xs font-bold px-3 py-1 rounded mb-3">INFO</span>
                        <p class="text-gray-500 text-sm mb-2">7 janvier 2026</p>
                        <h3 class="text-gray-900 font-bold text-lg mb-2 hover:text-blue-600 transition">Informations
                            Utiles</h3>
                        <p class="text-gray-600 text-sm">Consultez les numéros d'urgence et consignes de sécurité</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Report Form -->
        <section class="bg-white rounded-xl shadow-lg p-8 mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Signalement Rapide</h2>
            <p class="text-gray-600 mb-8">
                Vous êtes témoin d'une urgence ? Remplissez ce formulaire pour alerter rapidement les services
                compétents.
            </p>
 @if (session('success'))
                <div
                    class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg font-semibold animate-fade-in">
                    {{ session('success') }}
                </div>
            @endif
            <form id="alertForm" class="space-y-6" enctype="multipart/form-data" method="POST"
                action="{{ route('enregistrerAlerte') }}">
                @csrf

                <!-- Type d'urgence & Localisation -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Type d'urgence</label>
                        <select name="type_alerte" required
                            class="w-full border-2 border-gray-200 rounded-lg px-4 py-3">
                            <option value="">-- Sélectionnez un type --</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->nom_service }}">{{ $service->nom_service }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Localisation</label>
                        <input type="text" name="localisation" placeholder="Ex : Avenue de l'O.U.A, Brazzaville"
                            class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            required>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4" placeholder="Décrivez brièvement la situation..."
                        class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition"></textarea>
                </div>

                <!-- Message vocal -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Message vocal (optionnel)</label>
                    <div class="flex items-center gap-4">
                        <button type="button" id="recordBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center shadow-md">
                            <i data-feather="mic" class="mr-2 w-5 h-5"></i> Enregistrer
                        </button>
                        <button type="button" id="stopBtn" disabled
                            class="bg-gray-300 text-gray-500 px-6 py-3 rounded-lg font-semibold cursor-not-allowed flex items-center">
                            <i data-feather="square" class="mr-2 w-5 h-5"></i> Arrêter
                        </button>
                    </div>
                    <audio id="audioPreview" controls class="mt-4 hidden w-full rounded-lg"></audio>

                    <!-- Champ caché pour envoyer l'audio -->
                    <input type="file" name="audio" id="audioInput" class="hidden" accept="audio/*">
                </div>

                <button type="submit"
                    class="w-full md:w-auto bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 px-10 rounded-lg transition shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i data-feather="send" class="mr-2"></i> Envoyer l'alerte
                </button>
            </form>


            <p id="alertMsg"
                class="hidden mt-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg font-semibold">
                ✅ Alerte envoyée avec succès !
            </p>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-bold mb-4 text-green-400">À propos de CongoAssist</h3>
                <p class="text-gray-300 leading-relaxed">
                    CongoAssist est une plateforme communautaire de signalement des urgences, permettant d'alerter
                    rapidement les services compétents pour sauver des vies.
                </p>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-4 text-green-400">Liens utiles</h3>
                <ul class="space-y-2 text-gray-300">
                    <li><a href="{{ route('accueil') }}" class="hover:text-green-400 transition">Accueil</a></li>
                    <li><a href="{{ route('avis') }}" class="hover:text-green-400 transition">Les avis</a></li>
                    <li><a href="{{ route('regle') }}" class="hover:text-green-400 transition">Règlement</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-4 text-green-400">Contact</h3>
                <p class="text-gray-300 flex items-center mb-2">
                    <i data-feather="mail" class="mr-2 w-4 h-4"></i> lombaisaac8@gmail.com
                </p>
                <p class="text-gray-300 flex items-center mb-2">
                    <i data-feather="phone" class="mr-2 w-4 h-4"></i> +242 05 052 0146/06 839 8249
                </p>
                <p class="text-gray-300 flex items-center">
                    <i data-feather="map-pin" class="mr-2 w-4 h-4"></i> Brazzaville, Congo
                </p>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
            &copy; 2026 CongoAssist. Tous droits réservés.
        </div>
    </footer>

    <script>
        feather.replace();

        // Audio recording
        let mediaRecorder;
        let audioChunks = [];
        const recordBtn = document.getElementById('recordBtn');
        const stopBtn = document.getElementById('stopBtn');
        const audioPreview = document.getElementById('audioPreview');
        const audioInput = document.getElementById('audioInput');
        const alertForm = document.getElementById('alertForm');

        recordBtn.addEventListener('click', async () => {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    audio: true
                });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.addEventListener('dataavailable', e => audioChunks.push(e.data));
                mediaRecorder.addEventListener('stop', () => {
                    const audioBlob = new Blob(audioChunks, {
                        type: 'audio/webm'
                    });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    audioPreview.src = audioUrl;
                    audioPreview.classList.remove('hidden');

                    // Convert Blob en File et ajoute au input hidden
                    const file = new File([audioBlob], 'alerte_audio.webm', {
                        type: 'audio/webm'
                    });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    audioInput.files = dataTransfer.files;

                    recordBtn.disabled = false;
                    stopBtn.disabled = true;
                    recordBtn.innerHTML = '<i data-feather="mic" class="mr-2 w-5 h-5"></i> Enregistrer';
                    feather.replace();
                });

                mediaRecorder.start();
                recordBtn.disabled = true;
                stopBtn.disabled = false;
                recordBtn.innerHTML = '<i data-feather="mic" class="mr-2 w-5 h-5"></i> Enregistrement...';
                feather.replace();
            } catch (err) {
                alert("Erreur : impossible d'accéder au micro.");
            }
        });

        stopBtn.addEventListener('click', () => {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
            }
        });

        // Optionnel : message success
        alertForm.addEventListener('submit', () => {
            // Le fichier audio est déjà attaché via JS
        });
    </script>
    <script>
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
{{-- < <script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(reg => console.log('SW OK', reg))
      .catch(err => console.log('SW erreur', err));
  });
}
</script> --}}
    {{-- <script>
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;

  // Affiche ton bouton ou message
  console.log("Install disponible");

  const btn = document.createElement('button');
  btn.innerText = "Installer CongoAssist";
  btn.style.position = "fixed";
  btn.style.bottom = "20px";
  btn.style.right = "20px";
  btn.style.padding = "10px 15px";
  btn.style.zIndex = "9999";

  document.body.appendChild(btn);

  btn.addEventListener('click', () => {
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then((choice) => {
      if (choice.outcome === 'accepted') {
        console.log('Installé');
      }
      deferredPrompt = null;
    });
  });
});
</script> --}}


{{-- ══ PWA install button ══ --}}
<button id="pwa-fab" style="
    display:none; position:fixed; bottom:max(1.5rem,env(safe-area-inset-bottom));
    right:1.5rem; z-index:9990; align-items:center; gap:.6rem;
    background:linear-gradient(135deg,#10b981,#059669); color:#fff;
    font-size:.72rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
    padding:.75rem 1.4rem; border-radius:100px; border:none;
    box-shadow:0 8px 28px rgba(16,185,129,.45); cursor:pointer;
    transition:transform .2s,box-shadow .2s;" aria-label="Installer l'application">
  ⬇ Installer l'app
</button>

{{-- ══ PWA modal guide ══ --}}
<div id="pwa-modal-overlay" style="
    display:none; position:fixed; inset:0; z-index:9995;
    background:rgba(0,0,0,.55); backdrop-filter:blur(6px);
    align-items:flex-end; justify-content:center; padding:1rem;">
  <div style="
      background:#fff; border-radius:24px 24px 0 0;
      padding:2rem 1.8rem max(2rem,env(safe-area-inset-bottom));
      width:100%; max-width:480px;">
    <h3 id="pwa-modal-title" style="font-size:1rem;font-weight:800;color:#0f172a;margin-bottom:.75rem;"></h3>
    <p id="pwa-modal-body" style="font-size:.85rem;color:#64748b;line-height:1.7;"></p>
    <button id="pwa-modal-close" style="
        display:block; width:100%; margin-top:1.5rem;
        background:linear-gradient(135deg,#10b981,#059669); color:#fff;
        font-weight:700; font-size:.78rem; letter-spacing:.08em; text-transform:uppercase;
        padding:.85rem; border-radius:100px; border:none; cursor:pointer;">
      Fermer
    </button>
  </div>
</div>

<script>
// Service Worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js', { scope: '/' })
      .then(reg => console.log('SW OK', reg.scope))
      .catch(err => console.warn('SW erreur:', err));
  });
}

// PWA Install
(function () {
  const fab     = document.getElementById('pwa-fab');
  const overlay = document.getElementById('pwa-modal-overlay');
  const title   = document.getElementById('pwa-modal-title');
  const body    = document.getElementById('pwa-modal-body');
  const close   = document.getElementById('pwa-modal-close');

  const ua         = navigator.userAgent.toLowerCase();
  const isIOS      = /iphone|ipad|ipod/.test(ua);
  const isAndroid  = /android/.test(ua);
  const isSamsung  = /samsungbrowser/.test(ua);
  const isStandalone = window.navigator.standalone === true
    || window.matchMedia('(display-mode: standalone)').matches;

  if (isStandalone) return;

  let deferredPrompt = null;

  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    fab.style.display = 'flex';
  });

  // iOS : pas de beforeinstallprompt, on affiche quand même
  if (isIOS) fab.style.display = 'flex';

  window.addEventListener('appinstalled', () => {
    fab.style.display = 'none';
    deferredPrompt = null;
  });

  fab.addEventListener('mouseenter', () => {
    fab.style.transform = 'translateY(-3px)';
    fab.style.boxShadow = '0 14px 36px rgba(16,185,129,.55)';
  });
  fab.addEventListener('mouseleave', () => {
    fab.style.transform = '';
    fab.style.boxShadow = '0 8px 28px rgba(16,185,129,.45)';
  });

  fab.addEventListener('click', async () => {
    if (deferredPrompt) {
      try {
        await deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        if (outcome === 'accepted') fab.style.display = 'none';
      } catch (err) { showGuide(); }
      deferredPrompt = null;
      return;
    }
    showGuide();
  });

  function showGuide() {
    if (isIOS) {
      title.textContent = 'Installer sur iPhone / iPad';
      body.innerHTML = `Dans <strong>Safari</strong>, appuyez sur <strong>⎙ Partager</strong> en bas, puis <strong>Sur l'écran d'accueil</strong>.`;
    } else if (isSamsung) {
      title.textContent = 'Installer sur Samsung';
      body.innerHTML = `Dans <strong>Samsung Internet</strong>, menu <strong>⋮</strong> → <strong>Ajouter page à</strong> → <strong>Écran d'accueil</strong>.`;
    } else if (isAndroid) {
      title.textContent = 'Installer sur Android';
      body.innerHTML = `Dans <strong>Chrome</strong>, menu <strong>⋮</strong> en haut à droite → <strong>Installer l'application</strong>.`;
    } else {
      title.textContent = 'Installer CongoAssist';
      body.innerHTML = `Dans <strong>Chrome</strong> ou <strong>Edge</strong>, menu <strong>⋮</strong> → <strong>Installer l'application</strong>.`;
    }
    overlay.style.display = 'flex';
  }

  close.addEventListener('click', () => overlay.style.display = 'none');
  overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.style.display = 'none'; });
})();
</script>
</body>

</html>
