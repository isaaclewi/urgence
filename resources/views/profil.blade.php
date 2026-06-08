<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - CongoAssist</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
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

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .animate-slide-in {
            animation: slideInLeft 0.5s ease-out forwards;
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

        .sidebar-link:hover::before,
        .sidebar-link.active::before {
            transform: scaleY(1);
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            padding-left: 24px;
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .input-field {
            transition: all 0.3s ease;
        }

        .input-field:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        .profile-photo-container {
            position: relative;
            width: 150px;
            height: 150px;
        }

        .profile-photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #10b981;
        }

        .change-photo-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            background: linear-gradient(135deg, #3b82f6, #10b981);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .change-photo-overlay:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-green-50 flex">
    <!-- Sidebar -->
    <aside class="sidebar hidden lg:flex flex-col w-64 min-h-screen sticky top-0 text-white p-6 shadow-xl">
        <!-- Logo -->
        <div class="flex items-center gap-3 mb-10">
            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-xl">C</span>
            </div>
            <div>
                <h1 class="text-xl font-bold">Congo<span class="text-green-400">Assist</span></h1>
                <p class="text-xs text-blue-200">Tableau de bord</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-2">
            <a href="{{ route('bilanController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="activity" class="w-5 h-5"></i>
                <span>Mon bilan</span>
            </a>
            <a href="{{ route('vaccinationMenuController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="shield" class="w-5 h-5"></i>
                <span>Vaccinations</span>
            </a>
            <a href="{{ route('actualitesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="newspaper" class="w-5 h-5"></i>
                <span>Actualités</span>
            </a>
            <a href="{{ route('MesAlertesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="bell" class="w-5 h-5"></i>
                <span>Urgences</span>
            </a>
            <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition">
                <i data-feather="message-square" class="w-5 h-5"></i>
                <span>Forum</span>
            </a>
            <a href="{{ route('compteController') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition bg-white bg-opacity-10">
                <i data-feather="user" class="w-5 h-5"></i>
                <span>Mon Profil</span>
            </a>
        </nav>

        <!-- Logout -->
        <a href="{{ route('accueil') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-500 hover:bg-red-600 transition text-white mt-6">
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
                <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-green-500 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">C</span>
                </div>
                <span class="font-bold text-blue-800">Congo<span class="text-green-500">Assist</span></span>
            </div>
            <div class="w-6"></div>
        </div>

        <!-- Back Navigation -->
        <div class="mb-6 animate-fade-in">
            <a href="{{ route('compteController') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 transition">
                <i data-feather="arrow-left" class="w-5 h-5"></i>
                <span class="font-semibold">Retour au Compte</span>
            </a>
        </div>

        <!-- Header -->
        <header class="bg-white rounded-2xl shadow-md p-6 mb-8 animate-fade-in">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-3">
                        <i data-feather="user" class="w-8 h-8 text-blue-600"></i>
                        Mon Profil
                    </h1>
                    <p class="text-gray-600">Gérez vos informations personnelles</p>
                </div>
                <div class="flex items-center gap-4 bg-gradient-to-r from-blue-50 to-green-50 px-6 py-3 rounded-xl">
                    <img src="{{ asset($citoyen->photo_profil) }}" alt="Profil" class="w-14 h-14 rounded-full border-3 border-green-500 object-cover shadow-md">
                    <div>
                        <p class="font-bold text-gray-900">{{ $citoyen->nom }} {{ $citoyen->prenom }}</p>
                        <p class="text-sm text-gray-600">Membre actif</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Messages -->
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 animate-fade-in">
            <div class="flex items-center gap-3">
                <i data-feather="check-circle" class="w-5 h-5"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 animate-fade-in">
            <div class="flex items-center gap-3">
                <i data-feather="x-circle" class="w-5 h-5"></i>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        @if ($errors->any())
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg mb-6 animate-fade-in">
            <div class="flex items-start gap-3">
                <i data-feather="alert-triangle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-semibold mb-2">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Profile Form -->
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-2xl shadow-lg p-8 card-hover animate-slide-in">
                <div class="flex items-center gap-3 mb-8 pb-6 border-b">
                    <div class="bg-blue-100 p-3 rounded-xl">
                        <i data-feather="edit" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Modifier le Profil</h2>
                </div>

                <form method="POST" enctype="multipart/form-data" action="{{ route('profil.update') }}">
                    @csrf
                    @method('PUT')

                    <!-- Photo Profile -->
                    <div class="flex justify-center mb-8">
                        <div class="profile-photo-container">
                            <img id="profilePic" src="{{ asset($citoyen->photo_profil) }}" alt="Photo de profil">
                            <label for="photoInput" class="change-photo-overlay">
                                <i data-feather="camera" class="w-5 h-5 text-white"></i>
                            </label>
                            <input type="file" id="photoInput" name="photo" accept="image/*" class="hidden"
                                onchange="if(this.files[0].size > 2 * 1024 * 1024){alert('❌ La photo ne doit pas dépasser 2 Mo');this.value='';}else{document.getElementById('profilePic').src = URL.createObjectURL(this.files[0])}">
                        </div>
                    </div>
                    <p class="text-center text-sm text-gray-600 mb-8">
                        <i data-feather="info" class="w-4 h-4 inline"></i>
                        JPG / PNG – taille maximale : 2 Mo
                    </p>

                    <!-- Form Fields -->
                    <div class="space-y-6">
                        <!-- Nom -->
                        <div>
                            <label for="nom" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                <i data-feather="user" class="w-4 h-4 text-blue-600"></i>
                                Nom complet
                            </label>
                            <input type="text" id="nom" name="nom" value="{{ $citoyen->nom }}" required
                                class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                <i data-feather="mail" class="w-4 h-4 text-blue-600"></i>
                                Adresse email
                            </label>
                            <input type="email" id="email" name="email" value="{{ $citoyen->email }}" required
                                class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                        </div>

                        <!-- Téléphone -->
                        <div>
                            <label for="telephone" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                <i data-feather="phone" class="w-4 h-4 text-blue-600"></i>
                                Numéro de téléphone
                            </label>
                            <input type="text" id="telephone" name="telephone" value="{{ $citoyen->telephone }}" required
                                class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                        </div>

                        <!-- Adresse -->
                        <div>
                            <label for="adresse" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                <i data-feather="map-pin" class="w-4 h-4 text-blue-600"></i>
                                Adresse complète
                            </label>
                            <input type="text" id="adresse" name="adresse" value="{{ $citoyen->adresse }}" required
                                class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                <i data-feather="lock" class="w-4 h-4 text-blue-600"></i>
                                Nouveau mot de passe
                            </label>
                            <input type="password" id="password" name="password" placeholder="Laisser vide pour garder l'ancien"
                                class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                            <p class="text-sm text-gray-500 mt-2 flex items-center gap-2">
                                <i data-feather="shield" class="w-4 h-4"></i>
                                Minimum 6 caractères
                            </p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex gap-4">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-green-500 text-white px-6 py-4 rounded-xl font-semibold hover:shadow-lg transition card-hover flex items-center justify-center gap-2">
                            <i data-feather="save" class="w-5 h-5"></i>
                            Enregistrer les modifications
                        </button>
                        <a href="{{ route('compteController') }}" class="px-6 py-4 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition flex items-center justify-center gap-2">
                            <i data-feather="x" class="w-5 h-5"></i>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mt-8">
                <a href="{{ route('bilanController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                    <i data-feather="activity" class="w-6 h-6 mx-auto mb-2 text-blue-600"></i>
                    <p class="text-sm font-semibold text-gray-800">Bilan</p>
                </a>
                <a href="{{ route('vaccinationMenuController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                    <i data-feather="shield" class="w-6 h-6 mx-auto mb-2 text-green-600"></i>
                    <p class="text-sm font-semibold text-gray-800">Vaccinations</p>
                </a>
                <a href="{{ route('actualitesController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                    <i data-feather="newspaper" class="w-6 h-6 mx-auto mb-2 text-purple-600"></i>
                    <p class="text-sm font-semibold text-gray-800">Actualités</p>
                </a>
                <a href="{{ route('MesAlertesController') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                    <i data-feather="bell" class="w-6 h-6 mx-auto mb-2 text-red-600"></i>
                    <p class="text-sm font-semibold text-gray-800">Urgences</p>
                </a>
                <a href="#" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                    <i data-feather="message-square" class="w-6 h-6 mx-auto mb-2 text-orange-600"></i>
                    <p class="text-sm font-semibold text-gray-800">Forum</p>
                </a>
                <a href="{{ route('accueil') }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition text-center card-hover">
                    <i data-feather="log-out" class="w-6 h-6 mx-auto mb-2 text-gray-600"></i>
                    <p class="text-sm font-semibold text-gray-800">Déconnexion</p>
                </a>
            </div>
        </div>
    </main>

    <!-- Mobile Sidebar -->
    <div id="mobile-sidebar" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden lg:hidden">
        <div class="sidebar w-64 min-h-screen p-6 transform -translate-x-full transition-transform duration-300" id="sidebar-content">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">C</span>
                    </div>
                    <h1 class="text-lg font-bold">Congo<span class="text-green-400">Assist</span></h1>
                </div>
                <button id="close-sidebar" class="text-white">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('bilanController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="activity" class="w-5 h-5"></i>
                    <span>Mon bilan</span>
                </a>
                <a href="{{ route('vaccinationMenuController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="shield" class="w-5 h-5"></i>
                    <span>Vaccinations</span>
                </a>
                <a href="{{ route('actualitesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="newspaper" class="w-5 h-5"></i>
                    <span>Actualités</span>
                </a>
                <a href="{{ route('MesAlertesController') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="bell" class="w-5 h-5"></i>
                    <span>Urgences</span>
                </a>
                <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white">
                    <i data-feather="message-square" class="w-5 h-5"></i>
                    <span>Forum</span>
                </a>
                <a href="{{ route('compteController') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg text-white bg-white bg-opacity-10">
                    <i data-feather="user" class="w-5 h-5"></i>
                    <span>Mon Profil</span>
                </a>
            </nav>

            <a href="{{ route('accueil') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-500 hover:bg-red-600 transition text-white mt-6">
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
    </script>
</body>
</html>