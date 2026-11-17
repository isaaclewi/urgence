<?php

namespace App\Http\Controllers;

use App\Models\Citoyens;
use App\Support\Identite;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

/** Affiche (déchiffrée, à la demande) la pièce d'identité ou le selfie d'un citoyen. Réservé aux admins. */
class PieceIdentiteCitoyenController extends Controller
{
    public function show($id)
    {
        return $this->servir($id, 'piece_identite', 'consultation_piece');
    }

    public function selfie($id)
    {
        return $this->servir($id, 'selfie_identite', 'consultation_selfie');
    }

    private function servir($id, string $champ, string $action)
    {
        $citoyen = Citoyens::findOrFail($id);
        $chemin  = $citoyen->{$champ};

        if (! $chemin) {
            abort(404, 'Fichier introuvable');
        }

        try {
            $contenu = Crypt::decrypt(Storage::disk('supabase')->get($chemin));
        } catch (\Throwable $e) {
            report($e);
            abort(404, 'Fichier introuvable ou illisible');
        }

        // Chaque consultation est tracée : qui, quand, depuis quelle adresse.
        Identite::journaliser($citoyen->id, $action);

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contenu) ?: 'application/octet-stream';
        if (! str_starts_with($mime, 'image/')) {
            $mime = 'application/octet-stream';
        }

        return response($contenu)
            ->header('Content-Type', $mime)
            ->header('Cache-Control', 'no-store, private')
            ->header('X-Content-Type-Options', 'nosniff');
    }
}
