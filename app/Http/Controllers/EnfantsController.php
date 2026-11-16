<?php

namespace App\Http\Controllers;

use App\Models\Enfant;
use App\Models\SuiviVaccinEnfant;
use App\Models\vaccinEnfant;
use App\Support\CalendrierEnfant;
use App\Support\Notifier;
use App\Support\SuiviSchema;
use Illuminate\Http\Request;

class EnfantsController extends Controller
{
    private const MAX_ENFANTS = 10;

    private function citoyenId(): int
    {
        return (int) session('citoyen_id');
    }

    /** Un parent n'accède qu'à ses propres enfants. */
    private function enfant($id): Enfant
    {
        SuiviSchema::assurer();

        return Enfant::where('citoyen_id', $this->citoyenId())->findOrFail($id);
    }

    public function index()
    {
        SuiviSchema::assurer();

        $enfants = Enfant::where('citoyen_id', $this->citoyenId())->orderBy('date_naissance', 'desc')->get();

        $resumes = [];
        foreach ($enfants as $e) {
            $lignes = CalendrierEnfant::pour($e);
            CalendrierEnfant::rappeler($e, $lignes);
            $resumes[$e->id] = [
                'retard'  => $lignes->where('etat', 'en_retard')->count(),
                'a_faire' => $lignes->where('etat', 'a_faire')->count(),
                'complet' => $lignes->where('etat', 'complet')->count(),
                'total'   => $lignes->count(),
            ];
        }

        return view('enfants', compact('enfants', 'resumes'));
    }

    public function store(Request $request)
    {
        SuiviSchema::assurer();

        $data = $request->validate([
            'prenom'         => ['required', 'string', 'max:80'],
            'nom'            => ['nullable', 'string', 'max:80'],
            'sexe'           => ['nullable', 'in:M,F'],
            'date_naissance' => ['required', 'date', 'before_or_equal:today', 'after:' . today()->subYears(18)->toDateString()],
        ], [
            'date_naissance.before_or_equal' => 'La date de naissance ne peut pas être dans le futur.',
            'date_naissance.after'           => 'Cet espace concerne les enfants de moins de 18 ans.',
        ]);

        if (Enfant::where('citoyen_id', $this->citoyenId())->count() >= self::MAX_ENFANTS) {
            return back()->withInput()->with('error', 'Vous avez atteint le maximum de ' . self::MAX_ENFANTS . ' enfants.');
        }

        $enfant = Enfant::create($data + ['citoyen_id' => $this->citoyenId()]);

        return redirect()->route('enfants.show', $enfant->id)->with('success', $enfant->prenom . ' a été ajouté. Voici son calendrier de vaccination.');
    }

    public function show($id)
    {
        $enfant = $this->enfant($id);
        $lignes = CalendrierEnfant::pour($enfant);
        CalendrierEnfant::rappeler($enfant, $lignes);

        return view('enfantCalendrier', compact('enfant', 'lignes'));
    }

    public function destroy($id)
    {
        $enfant = $this->enfant($id);
        $nom    = $enfant->prenom;
        $enfant->delete();   // son historique de doses est supprimé avec lui

        return redirect()->route('enfants.index')->with('success', "{$nom} a été retiré de votre espace.");
    }

    /** Le parent déclare une dose reçue. */
    public function dose(Request $request, $id, $vaccinId)
    {
        $enfant = $this->enfant($id);
        $vaccin = vaccinEnfant::findOrFail($vaccinId);

        $data = $request->validate([
            'date_dose'      => ['required', 'date', 'before_or_equal:today', 'after_or_equal:' . $enfant->date_naissance->toDateString()],
            'prochaine_dose' => ['nullable', 'date', 'after:date_dose'],
        ], [
            'date_dose.before_or_equal'  => 'La date de la dose ne peut pas être dans le futur.',
            'date_dose.after_or_equal'   => 'La dose ne peut pas précéder la naissance.',
            'prochaine_dose.after'       => 'La prochaine dose doit être après la dose enregistrée.',
        ]);

        $total = max(1, (int) ($vaccin->nombre_doses ?: 1));
        $suivi = SuiviVaccinEnfant::firstOrNew(['enfant_id' => $enfant->id, 'vaccin_id' => $vaccin->id]);

        if (($suivi->doses_recues ?? 0) >= $total) {
            return back()->with('error', 'Toutes les doses de ce vaccin sont déjà enregistrées.');
        }

        $suivi->doses_recues  = (int) ($suivi->doses_recues ?? 0) + 1;
        $suivi->derniere_dose = $data['date_dose'];
        $suivi->prochaine_dose = $suivi->doses_recues >= $total ? null : ($data['prochaine_dose'] ?? null);
        $suivi->save();

        $fini = $suivi->doses_recues >= $total;

        Notifier::citoyen(
            $this->citoyenId(),
            $fini ? 'Vaccin terminé' : 'Dose enregistrée',
            $enfant->prenom . ' : ' . $vaccin->nom_vaccin . ' — ' . ($fini ? 'toutes les doses sont faites.' : "dose {$suivi->doses_recues}/{$total} enregistrée."),
            route('enfants.show', $enfant->id, false),
            'info',
            'vaccin'
        );

        return back()->with('success', 'Dose enregistrée pour ' . $enfant->prenom . '.');
    }

    /** Corriger une erreur : retire la dernière dose enregistrée. */
    public function retirerDose($id, $vaccinId)
    {
        $enfant = $this->enfant($id);
        $suivi  = SuiviVaccinEnfant::where('enfant_id', $enfant->id)->where('vaccin_id', $vaccinId)->first();

        if (! $suivi || $suivi->doses_recues < 1) {
            return back()->with('error', 'Aucune dose à retirer.');
        }

        $suivi->doses_recues--;

        if ($suivi->doses_recues === 0) {
            $suivi->delete();
        } else {
            $suivi->prochaine_dose = null;
            $suivi->save();
        }

        return back()->with('success', 'La dernière dose a été retirée.');
    }
}
