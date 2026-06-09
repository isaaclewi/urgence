<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\alertes;

use App\Models\Services;

use App\Models\AlerteAffectation;





class UrgenceController extends Controller

{

    public function index()

    {

        if (!session()->has('service_id')) {

            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');

        }



        $serviceId = session('service_id');



        // On récupère le service connecté

        $service = Services::find($serviceId);



        // 🔹 Récupération des alertes selon le rôle

        // Exemple : si le service est police, on prend uniquement les alertes de type 'police'

        $alertes = alertes::with('citoyen')

    ->where(function ($query) use ($service) {



        // alertes du service OU non catégorisées

        $query->where('type_alerte', $service->role)

              ->orWhereNull('type_alerte')

              ->orWhere('type_alerte', '');

    })

    ->orderByDesc('created_at')

    ->get();





        return view('services.urgenceSignalee', compact('service', 'alertes'));

    }



    public function updateStatut(Request $request, $id)

    {

        $request->validate([

            'statut' => 'required|in:en attente,pris en charge,terminee',

        ]);



        $alerte = alertes::findOrFail($id);

        $alerte->statut = $request->statut;

        $alerte->save();



        return back()->with('success', 'Statut mis à jour avec succès.');

    }





    public function affecter(Request $request, $id)

{

    $request->validate([

        'service_destination_id' => 'required|exists:services,id',

        'commentaire' => 'nullable|string',

    ]);



    $alerte = alertes::findOrFail($id);



    // création affectation

    AlerteAffectation::create([

        'alerte_id' => $alerte->id,

        'service_source_id' => session('service_id'),

        'service_destination_id' => $request->service_destination_id,

        'affecte_par' => session('service_id'),

        'commentaire' => $request->commentaire,

        'statut' => 'transmise',

    ]);



    // update alerte

    $alerte->equipe_id = $request->service_destination_id;

    $alerte->statut_intervention = 'affectee';

    $alerte->save();



    return back()->with('success', 'Alerte affectée avec succès.');

}







public function interventionsJournalieres()

{

    $interventions = \DB::table('alerte_interventions')

        ->join('alertes', 'alertes.id', '=', 'alerte_interventions.alerte_id')

        ->join('services', 'services.id', '=', 'alerte_interventions.equipe_id')

        ->select(

            'alerte_interventions.*',

            'alertes.titre',

            'alertes.localisation',

            'services.nom as equipe_nom'

        )

        ->orderByDesc('alerte_interventions.created_at')

        ->get();



    return view('services.interventions_journalieres', compact('interventions'));

}

}
