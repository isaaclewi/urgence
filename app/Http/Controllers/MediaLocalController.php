<?php

namespace App\Http\Controllers;

use App\Support\ImageUpload;
use Illuminate\Support\Facades\Storage;

/**
 * Sert les images enregistrées en local (quand Supabase n'est pas configuré).
 * Seuls les dossiers d'images publiques sont accessibles : jamais les pièces d'identité ni la messagerie.
 */
class MediaLocalController extends Controller
{
    public function show(string $chemin)
    {
        $chemin = ltrim($chemin, '/');

        abort_if(! ImageUpload::local() || str_contains($chemin, '..'), 404);
        abort_unless(preg_match('#^(photo|admins|services|actualites|avis_photos|alertes|diagnostic)/#', $chemin), 404);

        $disque = Storage::disk('supabase');
        abort_unless($disque->exists($chemin), 404);

        return response()->file($disque->path($chemin), ['Cache-Control' => 'public, max-age=86400']);
    }
}
