<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerte Police - CongoAssist</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .animate-pulse-slow {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .sidebar {
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
        }

        .sidebar-link {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: #10b981;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-link:hover::before {
            transform: scaleY(1);
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            padding-left: 24px;
        }

        #map {
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .leaflet-popup-content-wrapper {
            border-radius: 12px;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-green-50 flex">
    <!-- Sidebar -->
    <aside class="sidebar hidden lg:flex flex-col w-64 min-h-screen sticky top-0 text-white p-6 shadow-xl">
        <!-- Logo -->
        <div class="flex items-center gap-3 mb-10">
            <div
                class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-xl">C</span>
            </div>
            <div>
                <h1 class="text-xl font-bold">Congo<span class="text-green-400">Assist</span></h1>
                <p class="text-xs text-blue-200">Alerte Police</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-2">
            <a href="{{ route('bilanController') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="activity" class="w-5 h-5"></i>
                <span>Mon bilan</span>
            </a>
            <a href="{{ route('vaccinationMenuController') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="shield" class="w-5 h-5"></i>
                <span>Vaccinations</span>
            </a>
            <a href="{{ route('actualitesController') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="newspaper" class="w-5 h-5"></i>
                <span>Actualités</span>
            </a>
            <a href="{{ route('MesAlertesController') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="bell" class="w-5 h-5"></i>
                <span>Urgences</span>
            </a>
            <a href="{{ route('forumCitoyen') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="message-square" class="w-5 h-5"></i>
                <span>Forum</span>
            </a>
        </nav>

        <!-- Logout -->
        <a href="{{ route('accueil') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-500 hover:bg-red-600 transition text-white mt-6">
            <i data-feather="log-out" class="w-5 h-5"></i>
            <span>Déconnexion</span>
        </a>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-4 lg:p-8 overflow-y-auto">
        <!-- Mobile Header -->
        <div class="lg:hidden bg-white rounded-xl shadow-md p-4 mb-6 flex items-center justify-between">
            <button id="mobile-menu-btn" class="text-gray-700">
                <i data-feather="menu" class="w-6 h-6"></i>
            </button>
            <div class="flex items-center gap-2">
                <div
                    class="w-8 h-8 bg-gradient-to-br from-blue-600 to-green-500 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">C</span>
                </div>
                <span class="font-bold text-blue-800">Congo<span class="text-green-500">Assist</span></span>
            </div>
            <div class="w-6"></div>
        </div>

        <!-- Header -->
        <header class="bg-white rounded-2xl shadow-md p-6 mb-8 animate-fade-in">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="bg-gradient-to-br from-blue-500 to-blue-700 p-4 rounded-xl shadow-lg animate-pulse-slow">
                        <i data-feather="shield" class="text-white w-8 h-8"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Alerte Police</h1>
                        <p class="text-gray-600">Signalez une situation nécessitant l'intervention des forces de l'ordre
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4 bg-gradient-to-r from-blue-50 to-green-50 px-6 py-3 rounded-xl">
                    <img src="{{ $citoyen->photo_profil ?? asset('medias/default.png') }}" alt="Profil"
                        class="w-12 h-12 rounded-full border-2 border-green-500 object-cover shadow-md">
                    <div>
                        <p class="font-bold text-gray-900">{{ $citoyen->nom ?? 'Utilisateur' }}
                            {{ $citoyen->prenom ?? '' }}</p>
                        <p class="text-sm text-gray-600">Citoyen actif</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Alert Messages -->
        @if (session('success'))
            <div
                class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 mb-6 flex items-start gap-3 animate-fade-in">
                <i data-feather="check-circle" class="text-green-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-green-800 font-semibold">Succès !</p>
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 mb-6 flex items-start gap-3 animate-fade-in">
                <i data-feather="x-circle" class="text-red-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-red-800 font-semibold">Erreur !</p>
                    <p class="text-red-700 text-sm">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Alert Info Banner -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 mb-8 animate-fade-in">
            <div class="flex items-start gap-4">
                <div class="bg-blue-100 p-3 rounded-lg flex-shrink-0">
                    <i data-feather="info" class="text-blue-600 w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Information importante</h3>
                    <p class="text-blue-800 text-sm leading-relaxed">
                        Cette alerte sera transmise immédiatement aux forces de l'ordre. Veuillez fournir des
                        informations précises et n'utilisez ce service qu'en cas de réelle nécessité d'intervention
                        policière.
                    </p>
                </div>
            </div>
        </div>

        <!-- Emergency Numbers -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center gap-3 mb-2">
                    <i data-feather="phone" class="w-6 h-6"></i>
                    <h3 class="font-bold text-lg">Urgences Police</h3>
                </div>
                <p class="text-3xl font-bold">117</p>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center gap-3 mb-2">
                    <i data-feather="phone" class="w-6 h-6"></i>
                    <h3 class="font-bold text-lg">Pompiers</h3>
                </div>
                <p class="text-3xl font-bold">118</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center gap-3 mb-2">
                    <i data-feather="phone" class="w-6 h-6"></i>
                    <h3 class="font-bold text-lg">SAMU</h3>
                </div>
                <p class="text-3xl font-bold">15</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8 animate-fade-in">
            <form id="formAlerte" action="{{ route('alertePolice.store') }}" method="POST"
                enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Titre -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="tag" class="w-4 h-4 inline mr-1"></i>
                        Titre
                    </label>
                    <input type="text" name="titre" value="Police" readonly
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-600 font-semibold">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="file-text" class="w-4 h-4 inline mr-1"></i>
                        Description de la situation *
                    </label>
                    <textarea name="description" rows="5" required
                        placeholder="Décrivez précisément la situation (type d'incident, personnes impliquées, menaces éventuelles...)
Ex: Agression en cours, cambriolage, trouble à l'ordre public, accident de la circulation..."
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"></textarea>
                    <p class="text-xs text-gray-500 mt-2">Soyez précis : nature de l'incident, nombre de personnes,
                        urgence de la situation</p>
                </div>

                <!-- Type d'alerte -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                        Type d'intervention
                    </label>
                    <select name="type_alerte" required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                        <option value="police">👮 Intervention de la Police</option>
                    </select>
                </div>

                <!-- Photo -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="camera" class="w-4 h-4 inline mr-1"></i>
                        Photo (optionnel)
                    </label>
                    <div class="relative">
                        <input type="file" name="media_photo" accept="image/*" id="photo-input"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-2">
                            <i data-feather="info" class="w-3 h-3 inline mr-1"></i>
                            Ajoutez une photo si possible pour documenter la situation
                        </p>
                    </div>
                </div>

                <!-- Enregistrement vocal -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="mic" class="w-4 h-4 inline mr-1"></i>
                        Message vocal (optionnel)
                    </label>
                    <div class="flex items-center gap-4">
                        <button type="button" id="record-btn"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                            <i data-feather="mic" class="w-5 h-5"></i>
                            <span>🎤 Commencer l'enregistrement</span>
                        </button>
                        <span id="record-status" class="text-sm text-gray-600">Aucun enregistrement</span>
                    </div>
                    <input type="hidden" name="media_vocal" id="media_vocal">
                </div>


                <!-- Localisation -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-feather="map-pin" class="w-4 h-4 inline mr-1"></i>
                        Localisation
                    </label>
                    <div class="bg-gradient-to-r from-blue-50 to-green-50 border-2 border-blue-200 rounded-lg p-4"
                        id="adresse">
                        <div class="flex items-center gap-3">
                            <div class="animate-spin">
                                <i data-feather="loader" class="text-blue-600 w-5 h-5"></i>
                            </div>
                            <p class="text-blue-800 font-medium">Détection de votre position en cours...</p>
                        </div>
                    </div>
                </div>

                <!-- Hidden fields -->
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <input type="hidden" name="localisation" id="localisation">

                <!-- Map -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i data-feather="map" class="w-4 h-4 inline mr-1"></i>
                        Carte de localisation
                    </label>
                    <div id="map" class="w-full h-96 rounded-xl"></div>
                    <p class="text-xs text-gray-500 mt-2">
                        <i data-feather="navigation" class="w-3 h-3 inline mr-1"></i>
                        Votre position précise sera transmise aux forces de l'ordre
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit"
                        onclick="return confirm('⚠️ Confirmer l\'envoi de cette alerte ? La police sera immédiatement notifiée de votre signalement.')"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-6 rounded-lg transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <i data-feather="shield" class="w-5 h-5"></i>
                        <span>Envoyer l'alerte aux forces de l'ordre</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Links Footer -->
        <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <a href="{{ route('bilanController') }}"
                class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <i data-feather="activity" class="text-blue-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Bilan</p>
            </a>
            <a href="{{ route('vaccinationMenuController') }}"
                class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <i data-feather="shield" class="text-green-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Vaccinations</p>
            </a>
            <a href="{{ route('actualitesController') }}"
                class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <i data-feather="newspaper" class="text-purple-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Actualités</p>
            </a>
            <a href="{{ route('MesAlertesController') }}"
                class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <i data-feather="bell" class="text-red-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Urgences</p>
            </a>
            <a href="{{ route('forumCitoyen') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <i data-feather="message-square" class="text-orange-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Forum</p>
            </a>
            <a href="{{ route('accueil') }}"
                class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <i data-feather="log-out" class="text-gray-600 w-6 h-6 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-800">Déconnexion</p>
            </a>
        </div>
    </main>

    <!-- Mobile Sidebar -->
    <div id="mobile-sidebar" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden lg:hidden">
        <div class="sidebar w-64 min-h-screen p-6 transform -translate-x-full transition-transform duration-300"
            id="sidebar-content">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-2">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">C</span>
                    </div>
                    <h1 class="text-lg font-bold">Congo<span class="text-green-400">Assist</span></h1>
                </div>
                <button id="close-sidebar" class="text-white">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('bilanController') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="activity" class="w-5 h-5"></i>
                    <span>Mon bilan</span>
                </a>
                <a href="{{ route('vaccinationMenuController') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="shield" class="w-5 h-5"></i>
                    <span>Vaccinations</span>
                </a>
                <a href="{{ route('actualitesController') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="newspaper" class="w-5 h-5"></i>
                    <span>Actualités</span>
                </a>
                <a href="{{ route('MesAlertesController') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="bell" class="w-5 h-5"></i>
                    <span>Urgences</span>
                </a>
                <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="message-square" class="w-5 h-5"></i>
                    <span>Forum</span>
                </a>
            </nav>

            <a href="{{ route('accueil') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-500 hover:bg-red-600 transition text-white mt-6">
                <i data-feather="log-out" class="w-5 h-5"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </div>

    <script>
        feather.replace();

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const sidebarContent = document.getElementById('sidebar-content');
        const closeSidebar = document.getElementById('close-sidebar');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileSidebar.classList.remove('hidden');
                setTimeout(() => {
                    sidebarContent.classList.remove('-translate-x-full');
                }, 10);
            });
        }

        if (closeSidebar) {
            closeSidebar.addEventListener('click', () => {
                sidebarContent.classList.add('-translate-x-full');
                setTimeout(() => {
                    mobileSidebar.classList.add('hidden');
                }, 300);
            });
        }

        if (mobileSidebar) {
            mobileSidebar.addEventListener('click', (e) => {
                if (e.target === mobileSidebar) {
                    sidebarContent.classList.add('-translate-x-full');
                    setTimeout(() => {
                        mobileSidebar.classList.add('hidden');
                    }, 300);
                }
            });
        }

        // Geolocation & Map
        document.addEventListener("DOMContentLoaded", () => {
            const adresseDiv = document.getElementById('adresse');
            const localisationInput = document.getElementById('localisation');
            const latInput = document.getElementById('latitude');
            const lonInput = document.getElementById('longitude');

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError, {
                    enableHighAccuracy: true
                });
            } else {
                adresseDiv.innerHTML =
                    '<div class="flex items-center gap-3"><i data-feather="x-circle" class="text-red-600 w-5 h-5"></i><p class="text-red-800 font-medium">La géolocalisation n\'est pas supportée par ce navigateur.</p></div>';
                feather.replace();
            }

            function showPosition(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                latInput.value = lat;
                lonInput.value = lon;

                // Initialize map
                const map = L.map('map').setView([lat, lon], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Add marker
                const marker = L.marker([lat, lon]).addTo(map);
                marker.bindPopup("<b>Votre position actuelle</b><br>Les forces de l'ordre seront dirigées ici")
                    .openPopup();

                // Fetch address
                fetch(
                        `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json&accept-language=fr`
                        )
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.display_name) {
                            adresseDiv.innerHTML = `
                                <div class="flex items-start gap-3">
                                    <i data-feather="check-circle" class="text-green-600 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                                    <div>
                                        <p class="text-green-800 font-bold mb-1">Position détectée avec succès</p>
                                        <p class="text-gray-700 text-sm">${data.display_name}</p>
                                    </div>
                                </div>
                            `;
                            localisationInput.value = data.display_name;
                        } else {
                            adresseDiv.innerHTML =
                                '<div class="flex items-center gap-3"><i data-feather="alert-circle" class="text-orange-600 w-5 h-5"></i><p class="text-orange-800 font-medium">Adresse non trouvée.</p></div>';
                            localisationInput.value = "Adresse non trouvée";
                        }
                        feather.replace();
                    })
                    .catch(() => {
                        adresseDiv.innerHTML =
                            '<div class="flex items-center gap-3"><i data-feather="alert-circle" class="text-red-600 w-5 h-5"></i><p class="text-red-800 font-medium">Erreur de récupération d\'adresse.</p></div>';
                        localisationInput.value = "Erreur récupération adresse";
                        feather.replace();
                    });
            }

            function showError(error) {
                let message = "";
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        message = "Permission de géolocalisation refusée.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = "Position non disponible.";
                        break;
                    case error.TIMEOUT:
                        message = "Délai de géolocalisation dépassé.";
                        break;
                    default:
                        message = "Erreur de géolocalisation inconnue.";
                }
                adresseDiv.innerHTML =
                    `<div class="flex items-center gap-3"><i data-feather="alert-circle" class="text-red-600 w-5 h-5"></i><p class="text-red-800 font-medium">${message}</p></div>`;
                localisationInput.value = message;
                feather.replace();
            }
        });
    </script>
    <script>
        let mediaRecorder;
        let audioChunks = [];
        const recordBtn = document.getElementById('record-btn');
        const recordStatus = document.getElementById('record-status');
        const mediaVocalInput = document.getElementById('media_vocal');

        recordBtn.addEventListener('click', async () => {
            if (mediaRecorder && mediaRecorder.state === "recording") {
                // Stop recording
                mediaRecorder.stop();
                recordBtn.innerHTML =
                `<i data-feather="mic" class="w-5 h-5"></i> 🎤 Commencer l'enregistrement`;
                recordStatus.textContent = "Enregistrement terminé";
                return;
            }

            // Demande la permission micro
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    audio: true
                });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.ondataavailable = e => {
                    audioChunks.push(e.data);
                };

                mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, {
                        type: 'audio/webm'
                    });
                    const reader = new FileReader();
                    reader.readAsDataURL(audioBlob);
                    reader.onloadend = () => {
                        mediaVocalInput.value = reader.result; // Base64
                    };
                };

                mediaRecorder.start();
                recordBtn.innerHTML = `<i data-feather="square" class="w-5 h-5"></i> ⏹️ Arrêter`;
                recordStatus.textContent = "Enregistrement en cours...";
            } catch (err) {
                alert("Impossible d'accéder au microphone : " + err.message);
            }
        });
    </script>

</body>

</html>
