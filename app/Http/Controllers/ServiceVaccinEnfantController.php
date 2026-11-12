<?php

namespace App\Http\Controllers;

use App\Models\MonProgrammeVaccin;
use App\Models\Services;
use App\Models\vaccinEnfant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Vaccins pour enfants du portail service.
 * Même règle que les programmes : chaque service gère uniquement ses propres vaccins.
 */
class ServiceVaccinEnfantController extends Controller
{
    public const VOIES = [
        'Intramusculaire', 'Sous-cutanée', 'Intradermique', 'Orale', 'Nasale',
    ];

    private function serviceId(): int
    {
        return (int) session('service_id');
    }

    private function visibles(): Builder
    {
        $sid = $this->serviceId();

        return vaccinEnfant::query()
            ->where(fn ($q) => $q->where('service_id', $sid)->orWhereNull('service_id'));
    }

    private function vaccinDuService($id): vaccinEnfant
    {
        return vaccinEnfant::where('id', $id)
            ->where('service_id', $this->serviceId())
            ->firstOrFail();
    }

    /** Programmes proposés dans la liste déroulante : les siens + les nationaux. */
    private function programmesDisponibles()
    {
        $sid = $this->serviceId();

        return MonProgrammeVaccin::where(fn ($q) => $q->where('service_id', $sid)->orWhereNull('service_id'))
            ->orderBy('nom_programme')->get(['id', 'nom_programme']);
    }

    private function regles(Request $request): array
    {
        $sid = $this->serviceId();

        $ageMax = ['nullable', 'integer', 'min:0'];
        if ($request->filled('age_cible_min')) {
            $ageMax[] = 'gte:age_cible_min';
        }

        return [
            'nom_vaccin'              => 'required|string|max:255',
            'fabricant'               => 'nullable|string|max:255',
            'nombre_doses'            => 'nullable|integer|min:1|max:20',
            'voie_administration'     => 'nullable|string|max:100',
            'date_fabrication'        => 'nullable|date',
            'date_expiration'         => 'nullable|date|after_or_equal:date_fabrication',
            'age_cible_min'           => 'nullable|integer|min:0',
            'age_cible_max'           => $ageMax,
            'vaccination_obligatoire' => 'nullable|boolean',
            // Le programme doit exister ET être visible par ce service
            'programme_id'            => ['nullable', 'integer', Rule::exists('mon_programmes_vaccin', 'id')
                ->where(fn ($q) => $q->where(fn ($w) => $w->where('service_id', $sid)->orWhereNull('service_id')))],
        ];
    }

    private function champs(Request $request): array
    {
        return $request->only([
            'nom_vaccin', 'fabricant', 'nombre_doses', 'voie_administration',
            'date_fabrication', 'date_expiration', 'age_cible_min', 'age_cible_max', 'programme_id',
        ]) + ['vaccination_obligatoire' => $request->boolean('vaccination_obligatoire')];
    }

    public function index(Request $request)
    {
        $service = Services::findOrFail($this->serviceId());

        $stats = [
            'total'        => $this->visibles()->count(),
            'obligatoires' => $this->visibles()->where('vaccination_obligatoire', true)->count(),
            'bientot'      => $this->visibles()->bientotPerimes()->count(),
            'perimes'      => $this->visibles()->perimes()->count(),
        ];

        $liste = $this->visibles()->with('programme:id,nom_programme');

        if ($q = mb_strtolower(trim((string) $request->query('q')))) {
            $liste->where(fn ($w) => $w
                ->whereRaw('LOWER(nom_vaccin) LIKE ?', ["%{$q}%"])
                ->orWhereRaw('LOWER(fabricant) LIKE ?', ["%{$q}%"]));
        }

        match ($request->query('obligatoire')) {
            'oui'   => $liste->where('vaccination_obligatoire', true),
            'non'   => $liste->where('vaccination_obligatoire', false),
            default => null,
        };

        match ($request->query('peremption')) {
            'perime'  => $liste->perimes(),
            'bientot' => $liste->bientotPerimes(),
            'valide'  => $liste->valides(),
            default   => null,
        };

        $vaccins = $liste->orderBy('nom_vaccin')->paginate(10)->withQueryString();

        return view('services.vaccinationEnfants', compact('vaccins', 'service', 'stats'));
    }

    public function create()
    {
        return view('services.vaccinationEnfantsCreate', [
            'vaccin'     => new vaccinEnfant(['vaccination_obligatoire' => false]),
            'programmes' => $this->programmesDisponibles(),
            'voies'      => self::VOIES,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate($this->regles($request));

        vaccinEnfant::create($this->champs($request) + ['service_id' => $this->serviceId()]);

        return redirect()->route('services.vaccinationEnfantsIndex')->with('success', 'Vaccin ajouté avec succès.');
    }

    public function edit($id)
    {
        return view('services.vaccinationEnfantsEdit', [
            'vaccin'     => $this->vaccinDuService($id),
            'programmes' => $this->programmesDisponibles(),
            'voies'      => self::VOIES,
        ]);
    }

    public function update(Request $request, $id)
    {
        $vaccin = $this->vaccinDuService($id);

        $request->validate($this->regles($request));

        // La case « obligatoire » décochée n'est pas envoyée par le navigateur :
        // champs() la lit avec boolean(), donc elle repasse bien à faux.
        $vaccin->update($this->champs($request));

        return redirect()->route('services.vaccinationEnfantsIndex')->with('success', 'Vaccin mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $this->vaccinDuService($id)->delete();

        return redirect()->route('services.vaccinationEnfantsIndex')->with('success', 'Vaccin supprimé avec succès.');
    }
}
