<?php

namespace App\Http\Controllers;

use App\Models\alertes;
use Illuminate\Http\Request;

class AdminAlertesController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $statut = (string) $request->query('statut', '');
        $type   = (string) $request->query('type', '');

        $alertes = alertes::with(['citoyen:id,nom,prenom,matricule', 'service:id,nom'])
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(fn ($w) => $w->where('titre', 'like', $like)
                    ->orWhere('localisation', 'like', $like)
                    ->orWhereHas('citoyen', fn ($c) => $c->where('nom', 'like', $like)->orWhere('prenom', 'like', $like)));
            })
            ->when($statut !== '', fn ($query) => $query->where('statut', $statut))
            ->when($type !== '', fn ($query) => $query->where('type_alerte', $type))
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'    => alertes::count(),
            'attente'  => alertes::where('statut', 'en attente')->count(),
            'en_cours' => alertes::where('statut', 'pris en charge')->count(),
            'terminees' => alertes::where('statut', 'terminee')->count(),
        ];

        $statuts = alertes::whereNotNull('statut')->distinct()->orderBy('statut')->pluck('statut');
        $types   = alertes::whereNotNull('type_alerte')->where('type_alerte', '!=', '')->distinct()->orderBy('type_alerte')->pluck('type_alerte');

        return view('admin.alertes', compact('alertes', 'stats', 'statuts', 'types', 'q', 'statut', 'type'));
    }
}
