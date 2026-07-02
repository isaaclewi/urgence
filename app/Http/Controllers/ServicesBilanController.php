<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Services;
use App\Models\Citoyens;
use App\Models\bilanSante;

class ServicesBilanController extends Controller
{
    //Tableau regorgeant les champs à chiffrer en  base de données
    private array $champsChiffres = [
        'allergies',
        'groupe_sanguin',
        'taille',
        'poids',
        'antecedents_familiaux',
        'mode_de_vie',
        'maladies_chroniques',
        'maladies_passees_importantes',
        'interventions_chirurgicales',
        'antecedents_hospitalisation',
        'medicaments_pris_actuellement',
        'listez_vaccins_reçus'
    ];

    // Liste et gestion sur une seule page (ajout du chiffrement et déchiffrement des champs sensibles pour la lecture)
    public function index()
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Vous devez être connecté.');
        }

        $service = Services::find(session('service_id'));
        $bilans = bilanSante::with('citoyen')->orderBy('id', 'desc')->paginate(10);

        //Déchiffrement des champs sensibles pour la lecture
        $bilans->getCollection()->transform(function ($bilan) {
            foreach ($this->champsChiffres as $champ) {
                if (!empty($bilan->$champ)) {
                    try {
                        $bilan->$champ = decrypt($bilan->$champ);
                    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                        // Gestion de l'erreur si le déchiffrement échoue
                        $bilan->$champ = 'Erreur de déchiffrement';
                    }
                }
            }
            return $bilan;
        });

        $citoyens = Citoyens::orderBy('nom')->get();

        return view('services.citoyensBilan', compact('service', 'bilans', 'citoyens'));
    }

    // Enregistrement ou mise à jour
    public function store(Request $request)
    {
        $validated = $request->validate([
            'citoyen_id' => 'required|exists:citoyens,id',
            'groupe_sanguin' => 'nullable|string|max:10',
            'taille' => 'nullable|string|max:10',
            'poids' => 'nullable|string|max:10',
            'allergies' => 'nullable|string|max:255',
            'antecedents_familiaux' => 'nullable|string|max:255',
            'mode_de_vie' => 'nullable|string|max:255',
            'maladies_chroniques' => 'nullable|string|max:255',
            'maladies_passees_importantes' => 'nullable|string|max:255',
            'interventions_chirurgicales' => 'nullable|string|max:255',
            'antecedents_hospitalisation' => 'nullable|string|max:255',
            'medicaments_pris_actuellement' => 'nullable|string|max:255',
            'listez_vaccins_reçus' => 'nullable|string|max:255',
        ]);

        // Chiffrement des champs sensibles avant l'enregistrement
        foreach ($this->champsChiffres as $champ) {
            if (!empty($validated[$champ])) {
                $validated[$champ] = encrypt($validated[$champ]);
            }
        }

        bilanSante::updateOrCreate(
            ['citoyen_id' => $validated['citoyen_id']],
            $validated
        );

        return redirect()->route('services.citoyensBilan')->with('success', 'Bilan ajouté ou mis à jour avec succès.');
    }

    // Suppression
    public function destroy($id)
    {
        $bilan = bilanSante::findOrFail($id);
        $bilan->delete();

        return redirect()->route('services.citoyensBilan')->with('success', 'Bilan supprimé avec succès.');
    }
}
