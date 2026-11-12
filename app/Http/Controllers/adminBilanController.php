<?php

namespace App\Http\Controllers;

use App\Models\bilanSante;
use App\Models\BilanJournal;
use App\Models\Citoyens;
use Illuminate\Http\Request;

/**
 * Supervision des bilans de santé côté administration (route protégée par auth.admin).
 *
 * Volontairement en LECTURE SEULE et SANS contenu médical : l'admin voit quel service a créé
 * chaque bilan et qui l'a consulté ou modifié, mais ne lit ni ne modifie les données de santé
 * (créées et chiffrées par les services de santé).
 */
class AdminBilanController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $bilans = bilanSante::with(['citoyen:id,matricule,nom,prenom', 'service:id,nom', 'modifiePar:id,nom'])
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->whereHas('citoyen', function ($c) use ($like) {
                    $c->where('nom', 'like', $like)
                      ->orWhere('prenom', 'like', $like)
                      ->orWhere('matricule', 'like', $like);
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        // Nombre d'actions tracées et dernier accès, par bilan de la page
        $acces = BilanJournal::selectRaw('bilan_id, COUNT(*) as total, MAX(created_at) as dernier')
            ->whereIn('bilan_id', $bilans->pluck('id'))
            ->groupBy('bilan_id')
            ->get()
            ->keyBy('bilan_id');

        $stats = [
            'total'      => bilanSante::count(),
            'sans_auteur' => bilanSante::whereNull('service_id')->count(),
            'sans_bilan' => Citoyens::where('etat_compte', 'actif')->whereNotIn('id', bilanSante::select('citoyen_id'))->count(),
            'acces_7j'   => BilanJournal::where('action', 'consultation')->where('created_at', '>=', now()->subDays(7))->count(),
        ];

        // Dernières actions (y compris suppressions : le bilan n'existe plus, la trace reste)
        $journal = BilanJournal::with('service:id,nom')->latest('created_at')->latest('id')->limit(30)->get();
        $citoyens = Citoyens::whereIn('id', $journal->pluck('citoyen_id')->unique())
            ->get(['id', 'matricule', 'nom', 'prenom'])->keyBy('id');

        return view('admin.bilans', compact('bilans', 'acces', 'stats', 'journal', 'citoyens', 'q'));
    }
}
