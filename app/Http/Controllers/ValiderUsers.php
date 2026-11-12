<?php

namespace App\Http\Controllers;

use App\Models\Citoyens;
use Illuminate\Http\Request;

class ValiderUsers extends Controller
{
    /** File de vérification : les comptes non actifs d'abord, du plus ancien au plus récent. */
    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $statut = $request->query('statut', 'inactif');   // par défaut : ce qu'il reste à vérifier

        $citoyens = Citoyens::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(fn ($w) => $w->where('nom', 'like', $like)
                    ->orWhere('prenom', 'like', $like)
                    ->orWhere('matricule', 'like', $like));
            })
            ->when($statut === 'actif', fn ($query) => $query->where('etat_compte', 'actif'))
            ->when($statut === 'inactif', fn ($query) => $query->where(
                fn ($w) => $w->whereNull('etat_compte')->orWhere('etat_compte', '!=', 'actif')))
            ->orderBy('created_at', $statut === 'inactif' ? 'asc' : 'desc')
            ->paginate(10)
            ->withQueryString();

        $compteurs = [
            'inactif' => Citoyens::where(fn ($w) => $w->whereNull('etat_compte')->orWhere('etat_compte', '!=', 'actif'))->count(),
            'actif'   => Citoyens::where('etat_compte', 'actif')->count(),
        ];

        return view('admin.valider', compact('citoyens', 'q', 'statut', 'compteurs'));
    }

    public function activer($id)
    {
        Citoyens::findOrFail($id)->update(['etat_compte' => 'actif']);

        return back()->with('success', 'Le compte du citoyen a été activé avec succès.');
    }

    public function desactiver($id)
    {
        Citoyens::findOrFail($id)->update(['etat_compte' => 'inactif']);

        return back()->with('success', 'Le compte du citoyen a été désactivé avec succès.');
    }
}
