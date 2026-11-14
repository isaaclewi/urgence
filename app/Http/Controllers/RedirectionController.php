<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\servicesProposes;
use App\Models\alertes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\AvisUser;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RedirectionController extends Controller
{
    //
    public function accueil()
    {
        $services = servicesProposes::all(); //

        return view('accueil', compact('services'));
    }

    public function accueilAdmin()
    {
        // Page publique : chiffres réels, sans jamais bloquer l'affichage si la base est lente
        try {
            $stats = [
                'alertes'    => \App\Models\alertes::count(),
                'en_attente' => \App\Models\alertes::whereIn('statut', ['ouvert', 'en attente'])->count(),
                'services'   => \App\Models\Services::where('disponible', 1)->count(),
            ];
        } catch (\Throwable $e) {
            $stats = ['alertes' => null, 'en_attente' => null, 'services' => null];
        }

        return view('admin.accueil', compact('stats'));
    }

    public function accueilService()
    {
        return view('services.accueil');
    }

    public function formulaire()
    {
        return view('formulaire');
    }

    public function login()
    {
        return view('login');
    }

    public function compte()
    {
        return view('compte');
    }

    public function actualites()
    {
        return view('actualites');
    }

    public function profil()
    {
        return view('profil');
    }

    public function bilanSante()
    {
        return view('bilanSante');
    }

    public function MesAlertes()
    {
        return view('MesAlertes');
    }

    public function vaccinationMenu()
    {
        return view('vaccinationMenu');
    }

    public function avis()
    {
        $avis = AvisUser::all();
        $totalAvis = $avis->count();
        $noteMoyenne = $avis->avg('note') ? number_format($avis->avg('note'), 1) : '0.0';
        $satisfaits = $avis->where('note', '>=', 4)->count();
        $tauxSatisfaction = $totalAvis ? round(($satisfaits / $totalAvis) * 100) : 0;

        return view('avis', compact('avis', 'totalAvis', 'noteMoyenne', 'tauxSatisfaction'));
    }

    public function regle()
    {
        return view('regle');
    }

    public function admin()
    {
        return view('admin.compte');
    }

    public function adminLogin()
    {
        return view('admin.login');
    }

    public function services()
    {
        return view('services.compte');
    }

    public function servicesLogin()
    {
        return view('services.login');
    }

    public function enregistrerAlerte(Request $request)
    {
        // Validation
        $request->validate([
            'type_alerte' => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audio' => 'nullable|file|mimes:webm,mp3,wav|max:10240', // 10MB max
        ]);

        $alerte = new alertes();
        $alerte->titre = $request->type_alerte;
        $alerte->description = $request->description;
        $alerte->date_heure = now();
        $alerte->localisation = $request->localisation;
        $alerte->type_alerte = $request->type_alerte;
        $alerte->statut = 'nouveau';
        $alerte->citoyen_id = Auth::id(); // ✅ citoyen connecté
        $alerte->services_id = null; // Si tu veux l’affecter automatiquement, adapte ici

        // Enregistrement audio
        if ($request->hasFile('audio')) {
            $path = $request->file('audio')->store('alertes_audio', 'public');
            $alerte->media_vocal = $path;
        }

        $alerte->save();

        return back()->with('success', 'Alerte envoyée avec succès !');
    }
}
