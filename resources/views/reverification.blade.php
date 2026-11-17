<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corriger mon dossier - CongoAssist</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen py-8">
<div class="container mx-auto px-4 max-w-xl">
    <h1 class="text-2xl font-bold mb-2"><span class="text-blue-800">Congo</span><span class="text-green-500">Assist</span></h1>
    <h2 class="text-xl font-semibold mb-4">Corriger mon dossier</h2>

    @if($citoyen->motif_rejet)
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded mb-6 text-sm text-red-800">
            <strong>Motif du rejet :</strong> {{ $citoyen->motif_rejet }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded mb-6">
            <ul class="text-sm text-red-700 list-disc pl-5">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow space-y-5">
        @csrf
        @php $champ = 'w-full px-4 py-3 border-2 border-gray-200 rounded-lg'; @endphp

        <div>
            <label class="block text-sm font-semibold mb-1">Date de naissance</label>
            <input type="date" name="date_naissance" required class="{{ $champ }}"
                   max="{{ now()->subYears(18)->toDateString() }}"
                   value="{{ old('date_naissance', $citoyen->date_naissance?->toDateString()) }}">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Type de pièce</label>
            <select name="type_piece" required class="{{ $champ }} bg-white">
                @foreach(\App\Support\Identite::TYPES as $val => $lib)
                    <option value="{{ $val }}" @selected(old('type_piece', $citoyen->type_piece) === $val)>{{ $lib }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Numéro de la pièce</label>
                <input type="text" name="numero_piece" required class="{{ $champ }}" value="{{ old('numero_piece') }}" autocomplete="off">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Date d’expiration</label>
                <input type="date" name="date_expiration_piece" required class="{{ $champ }}"
                       min="{{ now()->addDay()->toDateString() }}" value="{{ old('date_expiration_piece') }}">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Photo de la pièce (recto, nette, en entier)</label>
            <input type="file" name="piece_identite" required accept="image/jpeg,image/png,image/webp" class="{{ $champ }}">
        </div>

        @include('partials.selfie-defi')

        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg">Renvoyer mon dossier</button>
    </form>
</div>
</body>
</html>
