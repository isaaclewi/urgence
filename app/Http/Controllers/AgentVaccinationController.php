<?php

namespace App\Http\Controllers;

use App\Models\InscriptionVaccin;
use App\Models\MonProgrammeVaccin;
use App\Support\Notifier;
use App\Support\SuiviSchema;
use Illuminate\Http\Request;

class AgentVaccinationController extends Controller
{
    public function index(Request $request)
    {
        SuiviSchema::assurer();

        $agent = $request->attributes->get('agent');

        $mes = InscriptionVaccin::with('programme.service:id,nom')
            ->where('agent_id', $agent->id)
            ->orderByRaw("CASE statut WHEN 'en_cours' THEN 0 WHEN 'inscrit' THEN 1 WHEN 'termine' THEN 2 ELSE 3 END")
            ->orderByDesc('updated_at')
            ->get();

        $actifs = $mes->where('statut', '!=', 'abandonne')->pluck('programme_id')->all();

        // Programmes de son service et programmes nationaux, encore ouverts
        $disponibles = MonProgrammeVaccin::with('service:id,nom')
            ->where(fn ($q) => $q->whereNull('service_id')->orWhere('service_id', $agent->service_id))
            ->where(fn ($q) => $q->whereNull('date_fin')->orWhereDate('date_fin', '>=', today()))
            ->whereNotIn('id', $actifs)
            ->orderBy('date_debut')
            ->get();

        return view('agents.vaccination', compact('agent', 'mes', 'disponibles'));
    }

    public function inscrire(Request $request, $id)
    {
        SuiviSchema::assurer();

        $agent     = $request->attributes->get('agent');
        $programme = MonProgrammeVaccin::where(fn ($q) => $q->whereNull('service_id')->orWhere('service_id', $agent->service_id))
            ->findOrFail($id);

        if ($programme->date_fin && $programme->date_fin->lt(today())) {
            return back()->with('error', 'Ce programme est terminé : les inscriptions sont closes.');
        }

        $inscription = InscriptionVaccin::firstOrNew(['programme_id' => $programme->id, 'agent_id' => $agent->id]);

        if ($inscription->exists && $inscription->statut !== 'abandonne') {
            return back()->with('error', 'Vous êtes déjà inscrit à ce programme.');
        }

        $inscription->statut = $inscription->doses_recues > 0 ? 'en_cours' : 'inscrit';
        $inscription->save();

        Notifier::service(
            $agent->service_id,
            'Inscription du personnel',
            "{$agent->nom_complet} ({$agent->matricule}) s'est inscrit au programme « {$programme->nom_programme} ».",
            route('services.vaccinationSuivi.index', [], false),
            'info',
            'vaccin'
        );

        return back()->with('success', 'Inscription enregistrée.');
    }

    public function quitter(Request $request, $id)
    {
        $agent       = $request->attributes->get('agent');
        $inscription = InscriptionVaccin::where('agent_id', $agent->id)->findOrFail($id);

        if ($inscription->statut === 'termine') {
            return back()->with('error', 'Ce programme est terminé : il reste dans votre historique.');
        }

        if ($inscription->doses_recues > 0) {
            $inscription->update(['statut' => 'abandonne', 'prochaine_dose' => null]);
        } else {
            $inscription->delete();
        }

        return back()->with('success', 'Vous ne suivez plus ce programme.');
    }
}
