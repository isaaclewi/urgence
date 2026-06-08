<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AvisUser;

class lesAvis extends Controller
{
    public function avis()
    {
        $avis = AvisUser::all();

        // Nombre total d'avis
        $totalAvis = $avis->count();

        // Note moyenne (si tu as un champ "note" dans la table, sinon adapte)
        $noteMoyenne = $avis->avg('note'); // valeur entre 0 et 5
        $noteMoyenne = $noteMoyenne ? number_format($noteMoyenne, 1) : '0.0';

        // Satisfaction (%) : on considère satisfait si note >= 4
        $satisfaits = $avis->where('note', '>=', 4)->count();
        $tauxSatisfaction = $totalAvis ? round(($satisfaits / $totalAvis) * 100) : 0;

        return view('avis', compact('avis', 'totalAvis', 'noteMoyenne', 'tauxSatisfaction'));
    }
}
