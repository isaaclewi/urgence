<?php

namespace App\Http\Controllers;

use App\Models\Citoyens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GestionUsers extends Controller
{
    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $statut = $request->query('statut', '');

        $citoyens = Citoyens::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(fn ($w) => $w->where('nom', 'like', $like)
                    ->orWhere('prenom', 'like', $like)
                    ->orWhere('matricule', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('telephone', 'like', $like));
            })
            ->when($statut === 'actif', fn ($query) => $query->where('etat_compte', 'actif'))
            ->when($statut === 'inactif', fn ($query) => $query->where(
                fn ($w) => $w->whereNull('etat_compte')->orWhere('etat_compte', '!=', 'actif')))
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users', compact('citoyens', 'q', 'statut'));
    }

    public function destroy($id)
    {
        $citoyen = Citoyens::find($id);

        if (! $citoyen) {
            return back()->with('error', 'Citoyen introuvable.');
        }

        // La pièce d'identité (chiffrée) et la photo sont sur Supabase : on les supprime aussi,
        // sinon elles resteraient stockées après la suppression du compte.
        try {
            if ($citoyen->piece_identite) {
                Storage::disk('supabase')->delete($citoyen->piece_identite);
            }
            if ($citoyen->photo_profil) {
                Storage::disk('supabase')->delete('photo/' . basename($citoyen->photo_profil));
            }
        } catch (\Throwable $e) {
            report($e);   // on ne bloque pas la suppression du compte, mais l'erreur est journalisée
        }

        // Anciennes installations : fichiers locaux éventuels
        foreach ([$citoyen->piece_identite, $citoyen->photo_profil] as $chemin) {
            if ($chemin && ! str_starts_with($chemin, 'http') && file_exists(public_path($chemin))) {
                @unlink(public_path($chemin));
            }
        }

        $citoyen->delete();

        return back()->with('success', 'Le compte du citoyen a été supprimé définitivement.');
    }
}
