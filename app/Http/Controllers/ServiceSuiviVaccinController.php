<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\InscriptionVaccin;
use App\Models\MonProgrammeVaccin;
use App\Support\Notifier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Espace de gestion du service : suivi des inscriptions (citoyens ET personnel) aux
 * programmes de vaccination, enregistrement des doses, rappels et notifications.
 */
class ServiceSuiviVaccinController extends Controller
{
    private function serviceId(): int
    {
        return (int) session('service_id');
    }

    /** Inscriptions aux programmes du service + inscriptions de son propre personnel. */
    private function portee()
    {
        $sid = $this->serviceId();

        return InscriptionVaccin::where(function ($w) use ($sid) {
            $w->whereHas('programme', fn ($p) => $p->where('service_id', $sid))
              ->orWhereHas('agent', fn ($a) => $a->where('service_id', $sid));
        });
    }

    private function programmesUtilisables()
    {
        return MonProgrammeVaccin::where(fn ($q) => $q->whereNull('service_id')->orWhere('service_id', $this->serviceId()))
            ->orderBy('nom_programme')->get(['id', 'nom_programme', 'nb_vaccins', 'service_id']);
    }

    /** Notifie la personne concernée (citoyen ou agent) avec le bon lien. */
    private function prevenir(InscriptionVaccin $i, string $titre, string $message, string $niveau = 'important'): void
    {
        if ($i->agent_id) {
            Notifier::agent($i->agent_id, $titre, $message, route('agent.vaccination', [], false), $niveau, 'vaccin');
        } else {
            Notifier::citoyen($i->citoyen_id, $titre, $message, route('mesProgrammes', [], false), $niveau, 'vaccin');
        }
    }

    public function index(Request $request)
    {
        $sid   = $this->serviceId();
        $liste = $this->portee()->with(['programme', 'citoyen', 'agent']);

        if ($request->filled('programme_id')) {
            $liste->where('programme_id', (int) $request->programme_id);
        }
        if (in_array($request->statut, InscriptionVaccin::STATUTS, true)) {
            $liste->where('statut', $request->statut);
        }
        if ($request->type === 'personnel') {
            $liste->whereNotNull('agent_id');
        } elseif ($request->type === 'citoyen') {
            $liste->whereNotNull('citoyen_id');
        }
        if ($request->filled('q')) {
            $like = '%' . mb_strtolower($request->q) . '%';
            $liste->where(function ($w) use ($like) {
                $w->whereHas('citoyen', fn ($c) => $c->whereRaw('LOWER(nom) LIKE ?', [$like])->orWhereRaw('LOWER(prenom) LIKE ?', [$like]))
                  ->orWhereHas('agent', fn ($a) => $a->whereRaw('LOWER(nom) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(prenom) LIKE ?', [$like])->orWhereRaw('LOWER(matricule) LIKE ?', [$like]));
            });
        }

        $inscriptions = $liste->orderByDesc('updated_at')->paginate(15)->withQueryString();

        $stats = [
            'total'     => $this->portee()->count(),
            'en_cours'  => $this->portee()->where('statut', 'en_cours')->count(),
            'termine'   => $this->portee()->where('statut', 'termine')->count(),
            'personnel' => $this->portee()->whereNotNull('agent_id')->count(),
            'rappels'   => $this->portee()->whereIn('statut', ['inscrit', 'en_cours'])
                                ->whereDate('prochaine_dose', '<=', today()->addDays(7))->count(),
        ];

        return view('services.vaccinationSuivi', [
            'inscriptions' => $inscriptions,
            'stats'        => $stats,
            'programmes'   => $this->programmesUtilisables(),
            'agents'       => Agent::where('service_id', $sid)->where('statut', '!=', 'inactif')->orderBy('nom')->orderBy('prenom')->get(),
            'statuts'      => InscriptionVaccin::LIBELLES,
        ]);
    }

    /** Enregistre une dose reçue et prévient la personne. */
    public function dose(Request $request, $id)
    {
        $i = $this->portee()->with(['programme', 'citoyen', 'agent'])->findOrFail($id);

        if (in_array($i->statut, ['termine', 'abandonne'], true)) {
            return back()->with('error', 'Ce suivi est ' . ($i->statut === 'termine' ? 'terminé' : 'abandonné') . ' : aucune dose à ajouter.');
        }

        $data = $request->validate([
            'prochaine_dose' => ['nullable', 'date', 'after:today'],
            'note'           => ['nullable', 'string', 'max:255'],
        ]);

        $total   = (int) ($i->programme->nb_vaccins ?? 0);
        $doses   = $i->doses_recues + 1;
        $termine = $total > 0 && $doses >= $total;

        $i->update([
            'doses_recues'   => $doses,
            'derniere_dose'  => today(),
            'prochaine_dose' => $termine ? null : ($data['prochaine_dose'] ?? null),
            'statut'         => $termine ? 'termine' : 'en_cours',
            'note'           => $data['note'] ?? $i->note,
        ]);

        $nomService = session('service_nom', 'Votre service de santé');
        $avancement = $total > 0 ? "{$doses}/{$total}" : (string) $doses;

        $message = "Dose {$avancement} enregistrée pour « {$i->programme->nom_programme} » par {$nomService}.";
        if ($termine) {
            $message .= ' Le programme est terminé, félicitations !';
        } elseif (! empty($data['prochaine_dose'])) {
            $message .= ' Prochaine dose : ' . \Carbon\Carbon::parse($data['prochaine_dose'])->format('d/m/Y') . '.';
        }

        $this->prevenir($i, $termine ? 'Programme terminé' : 'Nouvelle dose enregistrée', $message);

        return back()->with('success', "Dose enregistrée pour {$i->sujetNom()} ({$avancement}). La personne a été notifiée.");
    }

    public function statut(Request $request, $id)
    {
        $i    = $this->portee()->with('programme')->findOrFail($id);
        $data = $request->validate(['statut' => ['required', Rule::in(InscriptionVaccin::STATUTS)]]);

        $i->update(['statut' => $data['statut']] + (in_array($data['statut'], ['termine', 'abandonne'], true) ? ['prochaine_dose' => null] : []));

        $this->prevenir($i, 'Suivi mis à jour', "Votre suivi du programme « {$i->programme->nom_programme} » est maintenant : " . $i->libelleStatut() . '.', 'info');

        return back()->with('success', 'Statut mis à jour.');
    }

    public function rappel($id)
    {
        $i = $this->portee()->with('programme')->findOrFail($id);

        if (in_array($i->statut, ['termine', 'abandonne'], true)) {
            return back()->with('error', 'Aucun rappel à envoyer pour ce suivi.');
        }

        $message = $i->prochaine_dose
            ? "Rappel : votre prochaine dose du programme « {$i->programme->nom_programme} » est prévue le " . $i->prochaine_dose->format('d/m/Y') . '.'
            : "Rappel : pensez à vous présenter pour votre prochaine dose du programme « {$i->programme->nom_programme} ».";

        $this->prevenir($i, 'Rappel de vaccination', $message, 'important');

        return back()->with('success', 'Rappel envoyé.');
    }

    /** Inscrit une sélection (ou tout) du personnel actif à un programme. */
    public function inscrirePersonnel(Request $request)
    {
        $sid = $this->serviceId();

        $data = $request->validate([
            'programme_id' => ['required', Rule::exists('mon_programmes_vaccin', 'id')
                                ->where(fn ($q) => $q->whereNull('service_id')->orWhere('service_id', $sid))],
            'agent_ids'    => ['nullable', 'array'],
            'agent_ids.*'  => [Rule::exists('agents', 'id')->where('service_id', $sid)],
            'tous'         => ['nullable', 'boolean'],
        ]);

        $programme = MonProgrammeVaccin::findOrFail($data['programme_id']);

        if ($programme->date_fin && $programme->date_fin->lt(today())) {
            return back()->with('error', 'Ce programme est terminé : impossible d\'y inscrire du personnel.');
        }

        $agents = Agent::where('service_id', $sid)->where('statut', '!=', 'inactif')
            ->when(! $request->boolean('tous'), fn ($q) => $q->whereIn('id', $data['agent_ids'] ?? []))
            ->get();

        if ($agents->isEmpty()) {
            return back()->with('error', 'Sélectionnez au moins un agent (ou cochez « tout le personnel »).');
        }

        $ajoutes = 0;
        foreach ($agents as $agent) {
            $i = InscriptionVaccin::firstOrNew(['programme_id' => $programme->id, 'agent_id' => $agent->id]);

            if ($i->exists && $i->statut !== 'abandonne') {
                continue;     // déjà suivi
            }

            $i->statut = $i->doses_recues > 0 ? 'en_cours' : 'inscrit';
            $i->save();
            $ajoutes++;

            Notifier::agent(
                $agent->id,
                'Programme de vaccination',
                session('service_nom', 'Votre service') . " vous a inscrit au programme « {$programme->nom_programme} ». Suivez votre avancement dans « Mes vaccinations ».",
                route('agent.vaccination', [], false),
                'important',
                'vaccin'
            );
        }

        return back()->with('success', $ajoutes
            ? "{$ajoutes} agent(s) inscrit(s) à « {$programme->nom_programme} » et notifié(s)."
            : 'Tous les agents sélectionnés étaient déjà inscrits.');
    }
}
