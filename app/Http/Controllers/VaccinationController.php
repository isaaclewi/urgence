<?php

namespace App\Http\Controllers;

use App\Models\MonProgrammeVaccin;
use App\Models\Services;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Programmes de vaccination (pandémie / épidémie) du portail service.
 * Un service ne modifie et ne supprime que SES programmes ; les programmes sans
 * propriétaire (données nationales) restent visibles en lecture seule.
 */
class VaccinationController extends Controller
{
    public const CATEGORIES = ['pandemie' => 'Pandémie', 'epidemie' => 'Épidémie'];

    private function serviceId(): int
    {
        return (int) session('service_id');
    }

    /** Programmes que le service connecté peut voir : les siens + les nationaux. */
    private function visibles(): Builder
    {
        $sid = $this->serviceId();

        return MonProgrammeVaccin::query()
            ->where(fn ($q) => $q->where('service_id', $sid)->orWhereNull('service_id'));
    }

    /** Programme appartenant au service connecté — sinon 404 (jamais celui d'un autre). */
    private function programmeDuService($id): MonProgrammeVaccin
    {
        return MonProgrammeVaccin::where('id', $id)
            ->where('service_id', $this->serviceId())
            ->firstOrFail();
    }

    private function regles(): array
    {
        return [
            'nom_programme'  => 'required|string|max:255',
            'categorie'      => 'required|in:' . implode(',', array_keys(self::CATEGORIES)),
            'age_cible'      => 'nullable|string|max:50',
            'date_debut'     => 'nullable|date',
            'date_fin'       => 'nullable|date|after_or_equal:date_debut',
            'nb_vaccins'     => 'nullable|integer|min:0|max:100',
            'organisme_resp' => 'nullable|string|max:255',
        ];
    }

    private function champs(Request $request): array
    {
        return $request->only([
            'nom_programme', 'categorie', 'age_cible', 'date_debut',
            'date_fin', 'nb_vaccins', 'organisme_resp',
        ]);
    }

    public function index(Request $request)
    {
        $service = Services::findOrFail($this->serviceId());

        $stats = [
            'total'    => $this->visibles()->count(),
            'en_cours' => $this->visibles()->enCours()->count(),
            'a_venir'  => $this->visibles()->aVenir()->count(),
            'termines' => $this->visibles()->termines()->count(),
        ];

        $liste = $this->visibles()->withCount('vaccins');

        if ($q = mb_strtolower(trim((string) $request->query('q')))) {
            $liste->where(fn ($w) => $w
                ->whereRaw('LOWER(nom_programme) LIKE ?', ["%{$q}%"])
                ->orWhereRaw('LOWER(organisme_resp) LIKE ?', ["%{$q}%"]));
        }

        if (isset(self::CATEGORIES[$request->query('categorie')])) {
            $liste->where('categorie', $request->query('categorie'));
        }

        match ($request->query('statut')) {
            'a_venir'  => $liste->aVenir(),
            'en_cours' => $liste->enCours(),
            'termine'  => $liste->termines(),
            default    => null,
        };

        $programmes = $liste->orderByDesc('date_debut')->orderByDesc('id')->paginate(10)->withQueryString();

        return view('services.vaccinationIndex', [
            'programmes' => $programmes,
            'service'    => $service,
            'stats'      => $stats,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function create()
    {
        return view('services.vaccinationCreate', [
            'programme'  => new MonProgrammeVaccin(),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate($this->regles());

        MonProgrammeVaccin::create($this->champs($request) + ['service_id' => $this->serviceId()]);

        return redirect()->route('services.vaccinationIndex')->with('success', 'Programme ajouté avec succès.');
    }

    public function edit($id)
    {
        return view('services.vaccinationEdit', [
            'programme'  => $this->programmeDuService($id),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function update(Request $request, $id)
    {
        $programme = $this->programmeDuService($id);

        $request->validate($this->regles());

        $programme->update($this->champs($request));

        return redirect()->route('services.vaccinationIndex')->with('success', 'Programme mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $programme = $this->programmeDuService($id);
        $nb        = $programme->vaccins()->count();

        // Les vaccins liés ne sont pas supprimés : ils perdent seulement leur rattachement (FK nullOnDelete).
        $programme->delete();

        return redirect()->route('services.vaccinationIndex')->with(
            'success',
            $nb > 0
                ? "Programme supprimé. {$nb} vaccin(s) lié(s) n'ont plus de programme."
                : 'Programme supprimé avec succès.'
        );
    }
}
