{{-- resources/views/forum/groupe.blade.php --}}
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groupe - {{ $space->title }}</title>
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

        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #10b981 100%);
        }

        /* Mobile Sidebar */
        .mobile-sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }

        .mobile-sidebar.open {
            transform: translateX(0);
        }

        .sidebar-overlay {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-in-out;
        }

        .sidebar-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-green-50 flex">

    {{-- Overlay pour mobile --}}
    <div class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" id="sidebarOverlay"
        onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside
        class="sidebar mobile-sidebar fixed lg:static lg:flex flex-col w-64 min-h-screen lg:sticky top-0 text-white p-4 sm:p-6 shadow-xl z-50">
        {{-- Bouton fermer (mobile) --}}
        <button class="lg:hidden absolute top-4 right-4 text-white hover:text-green-300" onclick="toggleSidebar()">
            <i data-feather="x" class="w-6 h-6"></i>
        </button>

        <div class="flex items-center gap-3 mb-8 lg:mb-10">
            <div
                class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-lg sm:text-xl">C</span>
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-bold">Congo<span class="text-green-400">Assist</span></h1>
                <p class="text-xs text-blue-200">Tableau de bord</p>
            </div>
        </div>
        <nav class="flex-1 space-y-2">
            <a href="{{ route('bilanController') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition text-sm sm:text-base">
                <i data-feather="activity" class="w-5 h-5"></i><span>Mon bilan</span>
            </a>
            <a href="{{ route('vaccinationMenuController') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition text-sm sm:text-base">
                <i data-feather="shield" class="w-5 h-5"></i><span>Vaccinations</span>
            </a>
            <a href="{{ route('actualitesController') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition text-sm sm:text-base">
                <i data-feather="newspaper" class="w-5 h-5"></i><span>Actualités</span>
            </a>
            <a href="{{ route('MesAlertesController') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition text-sm sm:text-base">
                <i data-feather="bell" class="w-5 h-5"></i><span>Urgences</span>
            </a>
            <a href="{{ route('forumCitoyen') }}"
                class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:text-green-300 transition text-sm sm:text-base">
                <i data-feather="message-square" class="w-5 h-5"></i><span>Forum</span>
            </a>
        </nav>
        <a href="{{ route('accueil') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-500 hover:bg-red-600 transition text-white mt-6 text-sm sm:text-base">
            <i data-feather="log-out" class="w-5 h-5"></i><span>Déconnexion</span>
        </a>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 p-3 sm:p-4 lg:p-8 overflow-y-auto">
        {{-- Bouton menu mobile --}}
        <button class="lg:hidden mb-3 p-2 bg-blue-900 text-white rounded-lg shadow-md" onclick="toggleSidebar()">
            <i data-feather="menu" class="w-6 h-6"></i>
        </button>

        {{-- Groupe Header --}}
        <header
            class="bg-green-700 rounded-xl sm:rounded-2xl shadow-md p-4 sm:p-6 mb-6 sm:mb-8 text-white animate-fade-in">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0 flex-1 w-full sm:w-auto">
                    <a href="{{ url()->previous() }}" class="text-xl sm:text-2xl hover:opacity-80 flex-shrink-0">←</a>
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center bg-green-500 text-lg sm:text-xl flex-shrink-0">
                        {{ $space->type === 'public' ? '🌐' : '🔒' }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-base sm:text-xl font-bold truncate">{{ $space->title }}</h2>
                        <p class="text-xs sm:text-sm text-green-200 truncate">
                            {{ $space->description ?? 'Discussion de groupe' }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-2 flex-wrap w-full sm:w-auto">
                    <span
                        class="px-2 sm:px-3 py-1 text-xs font-semibold rounded-full bg-white/20">{{ strtoupper($space->type) }}</span>
                    @if ($space->service)
                        <span
                            class="px-2 sm:px-3 py-1 text-xs font-semibold rounded-full bg-white/20">{{ $space->service->nom }}</span>
                    @endif
                </div>
            </div>
        </header>

        {{-- Messages --}}
        <div class="space-y-3 sm:space-y-4 mb-4" id="messagesContainer">
            @forelse($messages as $msg)
                <div class="flex {{ $msg->sender_type === 'citoyen' ? 'justify-end' : 'justify-start' }}">
                    <div
                        class="max-w-[85%] sm:max-w-[75%] md:max-w-[65%] p-3 sm:p-4 rounded-xl shadow-sm {{ $msg->sender_type === 'citoyen' ? 'bg-green-100' : 'bg-gray-100' }} relative">
                        <div
                            class="text-xs font-semibold {{ $msg->sender_type === 'citoyen' ? 'text-green-600' : 'text-orange-500' }} mb-1">
                            {{ ucfirst($msg->sender_type) }}
                        </div>

                        @if ($msg->message_type === 'texte')
                            <p class="text-xs sm:text-sm text-gray-900 break-words">{{ $msg->message }}</p>
                        @else
                            <a href="{{ asset($msg->file_path) }}" target="_blank"
                                class="flex items-center gap-2 p-2 bg-gray-200 rounded hover:bg-gray-300 text-xs sm:text-sm">
                                <span class="flex-shrink-0">📎</span>
                                <span class="truncate">{{ $msg->file_name }}</span>
                            </a>
                        @endif

                        <div class="text-right text-xs text-gray-500 mt-1">{{ $msg->created_at->format('H:i') }}</div>

                        {{-- Bouton supprimer si c'est le citoyen connecté --}}
                        {{-- @if ($msg->sender_type === 'citoyen' && $msg->sender_id == session('citoyen_id'))
                        <form action="{{ route('supprimerMessage', $msg->id) }}" method="POST"
                            class="absolute top-1 right-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm">🗑</button>
                        </form>
                    @endif --}}

                        <div
                            class="absolute top-0 {{ $msg->sender_type === 'citoyen' ? 'right-0 border-l-8 border-t-8 border-green-100' : 'left-0 border-r-8 border-t-8 border-gray-100' }}">
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 sm:py-20 text-gray-500 px-4">
                    <div class="text-4xl sm:text-6xl mb-4 opacity-30">💬</div>
                    <p class="text-sm sm:text-base">Aucun message pour l'instant. Soyez le premier à écrire !</p>
                </div>
            @endforelse
        </div>

        {{-- Formulaire d'envoi --}}
        <div class="sticky bottom-0 left-0 right-0 bg-white p-3 sm:p-4 shadow-md mt-4 -mx-3 sm:-mx-4 lg:-mx-8">
            <form method="POST" action="{{ route('creerGroupe', $space->id) }}"
                class="flex gap-2 sm:gap-3 items-end max-w-5xl mx-auto">
                @csrf
                <textarea name="message" rows="1" placeholder="Écrire un message..."
                    class="flex-1 p-2 sm:p-3 border rounded-2xl sm:rounded-3xl resize-none focus:outline-none focus:ring focus:ring-green-300 text-sm sm:text-base"
                    onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault(); this.form.submit();}"
                    oninput="this.style.height='auto'; this.style.height=Math.min(this.scrollHeight, 120)+'px'"></textarea>
                <button type="submit"
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-green-500 text-white rounded-full flex items-center justify-center text-lg sm:text-xl shadow hover:bg-green-600 transition flex-shrink-0">➤</button>
            </form>
        </div>
    </main>

    <script>
        feather.replace();

        function toggleSidebar() {
            const sidebar = document.querySelector('.mobile-sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('messagesContainer');
            if (container) container.scrollTop = container.scrollHeight;
            feather.replace();
        });

        // Fermer la sidebar sur redimensionnement
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                const sidebar = document.querySelector('.mobile-sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            }
        });
    </script>
</body>

</html>