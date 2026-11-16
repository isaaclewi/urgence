<?php

namespace App\Http\Controllers;

use App\Models\InscriptionVaccin;
use App\Models\MonProgrammeVaccin;
use App\Support\Notifier;

class CitoyenProgrammesController extends Controller
{
    private function citoyenId(): int
    {
        return (int) session('citoyen_id');
    }

    public function index()
    {
        $mes = InscriptionVaccin::with(['programme.service:id,nom'])
            ->where('citoyen_id', $this->citoyenId())
            ->orderByRaw("CASE statut WHEN 'en_cours' THEN 0 WHEN 'inscrit' THEN 1 WHEN 'termine' THEN 2 ELSE 3 END")
            ->orderByDesc('updated_at')
            ->get();

        $actifs = $mes->where('statut', '!=', 'abandonne')->pluck('programme_id')->all();

        // Programmes encore ouverts (non terminés) auxquels le citoyen n'est pas inscrit
        $disponibles = MonProgrammeVaccin::with('service:id,nom')
            ->where(fn ($q) => $q->whereNull('date_fin')->orWhereDate('date_fin', '>=', today()))
            ->whereNotIn('id', $actifs)
            ->orderBy('date_debut')
            ->get();

        return view('mesProgrammes', [
            'mes'         => $mes,
            'disponibles' => $disponibles,
            'prochaines'  => $mes->filter(fn ($i) => $i->prochaine_dose && $i->statut !== 'termine' && $i->statut !== 'abandonne')
                                 ->sortBy('prochaine_dose')->values(),
        ]);
    }

    public function inscrire($id)
    {
        $programme = MonProgrammeVaccin::findOrFail($id);

        if ($programme->date_fin && $programme->date_fin->lt(today())) {
            return back()->with('error', 'Ce programme est terminé : les inscriptions sont closes.');
        }

        $inscription = InscriptionVaccin::firstOrNew([
            'programme_id' => $programme->id,
            'citoyen_id'   => $this->citoyenId(),
        ]);

        if ($inscription->exists && $inscription->statut !== 'abandonne') {
            return back()->with('error', 'Vous êtes déjà inscrit à ce programme.');
        }

        $reinscription = $inscription->exists;
        $inscription->statut = $inscription->doses_recues > 0 ? 'en_cours' : 'inscrit';
        $inscription->save();

        Notifier::citoyen(
            $this->citoyenId(),
            'Inscription enregistrée',
            "Vous suivez maintenant « {$programme->nom_programme} ». Vous serez informé de l'avancement ici.",
            route('mesProgrammes', [], false),
            'info',
            'vaccin'
        );

        // Le service responsable est prévenu d'une nouvelle inscription
        $c          = \App\Models\Citoyens::find($this->citoyenId());
        $nomCitoyen = $c ? trim(($c->prenom ?? '') . ' ' . ($c->nom ?? '')) : 'Un citoyen';

        Notifier::service(
            $programme->service_id,
            'Nouvelle inscription',
            $nomCitoyen . " s'est inscrit au programme « {$programme->nom_programme} ».",
            route('services.vaccinationSuivi.index', [], false),
            'info',
            'vaccin'
        );

        return back()->with('success', $reinscription ? 'Inscription reprise.' : 'Inscription enregistrée.');
    }

    public function quitter($id)
    {
        $inscription = InscriptionVaccin::where('citoyen_id', $this->citoyenId())->findOrFail($id);

        if ($inscription->statut === 'termine') {
            return back()->with('error', 'Ce programme est terminé : il reste dans votre historique.');
        }

        if ($inscription->doses_recues > 0) {
            $inscription->update(['statut' => 'abandonne', 'prochaine_dose' => null]);   // l'historique des doses est conservé
        } else {
            $inscription->delete();
        }

        return back()->with('success', 'Vous ne suivez plus ce programme.');
    }
}
