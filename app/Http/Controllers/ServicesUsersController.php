<?php

namespace App\Http\Controllers;

use App\Models\Citoyens;
use App\Models\Services;
use Illuminate\Http\Request;

class ServicesUsersController extends Controller
{
    /**
     * Liste des citoyens avec recherche, filtres et pagination côté serveur.
     * Les statistiques viennent de la base (avant : calculées sur les 5 lignes
     * de la page affichée, donc fausses dès qu'il y avait plus de 5 citoyens).
     */
    public function index(Request $request)
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Vous devez être connecté.');
        }

        $service = Services::find(session('service_id'));

        $requete = Citoyens::query();

        if ($q = trim((string) $request->query('q'))) {
            $requete->where(function ($w) use ($q) {
                $w->where('nom', 'like', "%{$q}%")
                  ->orWhere('prenom', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('telephone', 'like', "%{$q}%")
                  ->orWhere('matricule', 'like', "%{$q}%")
                  ->orWhere('adresse', 'like', "%{$q}%");
            });
        }

        if (in_array($request->query('sexe'), ['M', 'F'], true)) {
            $requete->where('sexe', $request->query('sexe'));
        }

        if ($request->filled('etat')) {
            $requete->where('etat_compte', $request->query('etat'));
        }

        $citoyens = $requete->orderByDesc('id')->paginate(15)->withQueryString();

        $stats = [
            'total'    => Citoyens::count(),
            'actifs'   => Citoyens::where('etat_compte', 'actif')->count(),
            'hommes'   => Citoyens::where('sexe', 'M')->count(),
            'femmes'   => Citoyens::where('sexe', 'F')->count(),
        ];

        return view('services.citoyens', compact('service', 'citoyens', 'stats'));
    }
}
