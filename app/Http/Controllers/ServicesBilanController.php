<?php

namespace App\Http\Controllers;

use App\Models\BilanJournal;
use App\Models\Citoyens;
use App\Models\Services;
use App\Models\bilanSante;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Bilans de santé des citoyens (portail service).
 *
 * Données sensibles : les champs restent chiffrés en base, la liste n'affiche que
 * l'essentiel, et chaque consultation / création / modification / suppression est
 * inscrite dans le journal (bilan_journal) avec le service concerné.
 */
class ServicesBilanController extends Controller
{
    private const GROUPES = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    private function serviceId(): int
    {
        return (int) session('service_id');
    }

    private function regles(): array
    {
        $texte = 'nullable|string|max:1000';

        return [
            'groupe_sanguin'                => ['nullable', Rule::in(self::GROUPES)],
            'taille'                        => 'nullable|numeric|between:30,250',
            'poids'                         => 'nullable|numeric|between:0.5,400',
            'allergies'                     => $texte,
            'maladies_chroniques'           => $texte,
            'maladies_passees_importantes'  => $texte,
            'interventions_chirurgicales'   => $texte,
            'antecedents_hospitalisation'   => $texte,
            'antecedents_familiaux'         => $texte,
            'medicaments_pris_actuellement' => $texte,
            'vaccins_recus'                 => $texte,
            'mode_de_vie'                   => $texte,
        ];
    }

    private function citoyenActif($id): Citoyens
    {
        return Citoyens::where('id', $id)->where('etat_compte', 'actif')->firstOrFail();
    }

    /* ───────────── Liste ───────────── */

    public function index(Request $request)
    {
        $service = Services::findOrFail($this->serviceId());

        $stats = [
            'total'   => bilanSante::count(),
            'miens'   => bilanSante::where('service_id', $service->id)->count(),
            'recents' => bilanSante::where('updated_at', '>=', now()->subDays(30))->count(),
            'sans'    => Citoyens::where('etat_compte', 'actif')->whereNotIn('id', bilanSante::select('citoyen_id'))->count(),
        ];

        $liste = bilanSante::with('citoyen:id,matricule,nom,prenom');

        if ($q = mb_strtolower(trim((string) $request->query('q')))) {
            $liste->whereHas('citoyen', fn ($c) => $c
                ->whereRaw('LOWER(nom) LIKE ?', ["%{$q}%"])
                ->orWhereRaw('LOWER(prenom) LIKE ?', ["%{$q}%"])
                ->orWhereRaw('LOWER(matricule) LIKE ?', ["%{$q}%"]));
        }

        $bilans = $liste->orderByDesc('updated_at')->paginate(10)->withQueryString();

        return view('services.citoyensBilan', compact('service', 'bilans', 'stats'));
    }

    /* ───────────── Création ───────────── */

    public function create(Request $request)
    {
        // Étape 2 : un citoyen est choisi
        if ($request->filled('citoyen_id')) {
            $citoyen = $this->citoyenActif($request->query('citoyen_id'));

            if ($existant = bilanSante::where('citoyen_id', $citoyen->id)->first()) {
                return redirect()->route('services.citoyensBilanEdit', $existant->id)
                    ->with('success', 'Ce citoyen a déjà un bilan : vous pouvez le modifier ici.');
            }

            return view('services.bilanCreate', [
                'citoyen'   => $citoyen,
                'resultats' => null,
                'valeurs'   => [],
                'groupes'   => self::GROUPES,
            ]);
        }

        // Étape 1 : recherche (aucun chargement de tous les citoyens)
        $resultats = null;
        $q = mb_strtolower(trim((string) $request->query('q')));

        if (mb_strlen($q) >= 2) {
            $resultats = Citoyens::where('etat_compte', 'actif')
                ->where(fn ($w) => $w
                    ->whereRaw('LOWER(matricule) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(nom) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(prenom) LIKE ?', ["%{$q}%"])
                    ->orWhere('telephone', 'like', "%{$q}%"))
                ->orderBy('nom')->limit(15)->get(['id', 'matricule', 'nom', 'prenom']);

            $ids = bilanSante::whereIn('citoyen_id', $resultats->pluck('id'))->pluck('id', 'citoyen_id');
            $resultats->each(fn ($c) => $c->bilan_id = $ids[$c->id] ?? null);
        }

        return view('services.bilanCreate', [
            'citoyen'   => null,
            'resultats' => $resultats,
            'valeurs'   => [],
            'groupes'   => self::GROUPES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->regles() + [
            'citoyen_id' => ['required', Rule::exists('citoyens', 'id')->where('etat_compte', 'actif')],
        ]);

        // On n'écrase jamais en silence un bilan existant
        if ($existant = bilanSante::where('citoyen_id', $data['citoyen_id'])->first()) {
            return redirect()->route('services.citoyensBilanEdit', $existant->id)
                ->with('error', 'Ce citoyen a déjà un bilan. Modifiez-le ici.');
        }

        $bilan = bilanSante::create(
            bilanSante::versColonnes($data) + [
                'citoyen_id'             => $data['citoyen_id'],
                'service_id'             => $this->serviceId(),
                'modifie_par_service_id' => $this->serviceId(),
            ]
        );

        BilanJournal::noter($bilan->citoyen_id, $bilan->id, $this->serviceId(), 'creation');

        return redirect()->route('services.citoyensBilanShow', $bilan->id)->with('success', 'Bilan enregistré.');
    }

    /* ───────────── Consultation ───────────── */

    public function show($id)
    {
        $bilan = bilanSante::with(['citoyen', 'service:id,nom', 'modifiePar:id,nom'])->findOrFail($id);

        BilanJournal::noter($bilan->citoyen_id, $bilan->id, $this->serviceId(), 'consultation');

        $journal = BilanJournal::with('service:id,nom')
            ->where('bilan_id', $bilan->id)->orderByDesc('id')->limit(15)->get();

        return view('services.bilanShow', [
            'bilan'     => $bilan,
            'valeurs'   => $bilan->valeurs(),
            'journal'   => $journal,
            'serviceId' => $this->serviceId(),
        ]);
    }

    /* ───────────── Modification ───────────── */

    public function edit($id)
    {
        $bilan = bilanSante::with('citoyen')->findOrFail($id);

        return view('services.bilanEdit', [
            'bilan'   => $bilan,
            'citoyen' => $bilan->citoyen,
            'valeurs' => $bilan->valeurs(),
            'groupes' => self::GROUPES,
        ]);
    }

    public function update(Request $request, $id)
    {
        $bilan = bilanSante::findOrFail($id);
        $data  = $request->validate($this->regles());

        $bilan->update(bilanSante::versColonnes($data) + ['modifie_par_service_id' => $this->serviceId()]);

        BilanJournal::noter($bilan->citoyen_id, $bilan->id, $this->serviceId(), 'modification');

        return redirect()->route('services.citoyensBilanShow', $bilan->id)->with('success', 'Bilan mis à jour.');
    }

    /* ───────────── Suppression ───────────── */

    public function destroy($id)
    {
        $bilan = bilanSante::findOrFail($id);

        // Seul le service qui a créé le bilan peut le supprimer
        if (! $bilan->creePar($this->serviceId())) {
            return redirect()->route('services.citoyensBilan')
                ->with('error', "Seul le service qui a créé ce bilan peut le supprimer. Vous pouvez le modifier.");
        }

        BilanJournal::noter($bilan->citoyen_id, $bilan->id, $this->serviceId(), 'suppression');
        $bilan->delete();

        return redirect()->route('services.citoyensBilan')->with('success', 'Bilan supprimé avec succès.');
    }
}
