<?php

namespace App\Http\Controllers;

use App\Exports\TableExport;
use App\Models\Agent;
use App\Models\Services;
use App\Models\StockItem;
use App\Models\Vehicule;
use App\Services\KpiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    private const TZ = KpiService::TZ;

    private function serviceId(): int
    {
        return (int) session('service_id');
    }

    private function service(): Services
    {
        return Services::findOrFail($this->serviceId());
    }

    // ═════════════════════════ Page d'accueil ═════════════════════════

    public function index()
    {
        return view('services.exports', [
            'ressourcesDispo' => Schema::hasTable('agents') && Schema::hasTable('vehicules') && Schema::hasTable('stocks'),
            'du'              => now(self::TZ)->subDays(29)->format('Y-m-d'),
            'au'              => now(self::TZ)->format('Y-m-d'),
        ]);
    }

    // ═════════════════════════ Jeux de données ═════════════════════════

    private function dataPersonnel(): array
    {
        $agents = Agent::with('equipe')
            ->where('service_id', $this->serviceId())
            ->orderBy('nom')->orderBy('prenom')
            ->get();

        return [
            'headings' => ['Matricule', 'Nom', 'Prénom', 'Fonction', 'Grade', 'Équipe', 'Téléphone', 'Email', 'Statut', "Date d'embauche", 'Compétences'],
            'rows'     => $agents->map(fn ($a) => [
                $a->matricule, $a->nom, $a->prenom, $a->fonction, $a->grade ?? '',
                $a->equipe?->nom ?? '', $a->telephone ?? '', $a->email ?? '', ucfirst($a->statut),
                $a->date_embauche?->format('d/m/Y') ?? '', $a->competences ?? '',
            ])->all(),
            // Colonnes gardées dans le PDF (le tableau complet reste dans l'Excel)
            'pdf'      => [0, 1, 2, 3, 5, 6, 8],
        ];
    }

    private function dataVehicules(): array
    {
        $vehicules = Vehicule::with('equipe')
            ->where('service_id', $this->serviceId())
            ->orderBy('immatriculation')
            ->get();

        return [
            'headings' => ['Immatriculation', 'Type', 'Marque / modèle', 'Équipe', 'Statut', 'Kilométrage', 'Prochaine maintenance'],
            'rows'     => $vehicules->map(fn ($v) => [
                $v->immatriculation, ucfirst($v->type), $v->marque_modele ?? '', $v->equipe?->nom ?? '',
                ucfirst($v->statut), $v->kilometrage ?? '', $v->prochaine_maintenance?->format('d/m/Y') ?? '',
            ])->all(),
        ];
    }

    private function dataStocks(): array
    {
        $stocks = StockItem::where('service_id', $this->serviceId())
            ->orderBy('categorie')->orderBy('designation')
            ->get();

        return [
            'headings' => ['Désignation', 'Catégorie', 'Lot', 'Quantité', 'Unité', "Seuil d'alerte", 'Expiration', 'Alerte'],
            'rows'     => $stocks->map(function ($s) {
                $alertes = [];
                if ($s->estExpire())        { $alertes[] = 'Périmé'; }
                elseif ($s->expireBientot()) { $alertes[] = 'Expire bientôt'; }
                if ($s->estSousSeuil())      { $alertes[] = 'Sous le seuil'; }

                return [
                    $s->designation, ucfirst($s->categorie), $s->lot ?? '', $s->quantite, $s->unite ?? '',
                    $s->seuil_alerte, $s->date_expiration?->format('d/m/Y') ?? '', implode(', ', $alertes),
                ];
            })->all(),
        ];
    }

    private function dataInterventions(Carbon $debutUtc, Carbon $finUtc): array
    {
        $rows = DB::table('alerte_interventions as i')
            ->join('alertes as a', 'a.id', '=', 'i.alerte_id')
            ->join('services as e', 'e.id', '=', 'i.equipe_id')
            ->where('e.parent_service_id', $this->serviceId())
            ->whereBetween('i.created_at', [$debutUtc, $finUtc])
            ->orderByDesc('i.created_at')
            ->get(['i.created_at', 'a.titre', 'a.localisation', 'e.nom as equipe', 'i.statut', 'i.commentaire']);

        return [
            'headings' => ['Date', 'Alerte', 'Lieu', 'Équipe', 'Statut', 'Commentaire'],
            'rows'     => $rows->map(fn ($r) => [
                Carbon::parse($r->created_at, 'UTC')->timezone(self::TZ)->format('d/m/Y H:i'),
                $r->titre, $r->localisation ?? '', $r->equipe, ucfirst((string) $r->statut), $r->commentaire ?? '',
            ])->all(),
        ];
    }

    // ═════════════════════════ Sorties ═════════════════════════

    public function personnel(string $format)
    {
        return $this->sortie($format, 'personnel', 'Liste du personnel', null, $this->dataPersonnel());
    }

    public function vehicules(string $format)
    {
        return $this->sortie($format, 'vehicules', 'Parc de véhicules', null, $this->dataVehicules());
    }

    public function stocks(string $format)
    {
        return $this->sortie($format, 'stocks', 'État des stocks', null, $this->dataStocks());
    }

    public function interventions(Request $request, string $format)
    {
        $request->validate([
            'du' => ['required', 'date'],
            'au' => ['required', 'date', 'after_or_equal:du'],
        ]);

        $debutUtc = Carbon::parse($request->du, self::TZ)->startOfDay()->utc();
        $finUtc   = Carbon::parse($request->au, self::TZ)->endOfDay()->utc();

        $sous = 'Période du ' . Carbon::parse($request->du)->format('d/m/Y') . ' au ' . Carbon::parse($request->au)->format('d/m/Y');

        return $this->sortie($format, 'interventions', 'Interventions des équipes', $sous, $this->dataInterventions($debutUtc, $finUtc));
    }

    /** Synthèse des indicateurs (PDF). */
    public function kpi(Request $request, KpiService $kpi)
    {
        $jours = (int) $request->input('jours', 30);
        if (! in_array($jours, KpiController::PERIODES, true)) {
            $jours = 30;
        }

        $service = $this->service();
        $data    = $kpi->calculer($service, $jours);

        return Pdf::loadView('exports.pdf.kpi', array_merge($data, $this->contextePdf($service), [
            'jours' => $jours,
        ]))->setPaper('a4', 'portrait')
           ->download('synthese-indicateurs-' . $jours . 'j-' . now(self::TZ)->format('Ymd') . '.pdf');
    }

    /** Rapport d'une intervention (PDF). */
    public function intervention(int $id)
    {
        $iv = DB::table('alerte_interventions as i')
            ->join('alertes as a', 'a.id', '=', 'i.alerte_id')
            ->join('services as e', 'e.id', '=', 'i.equipe_id')
            ->leftJoin('citoyens as c', 'c.id', '=', 'a.citoyen_id')
            ->where('e.parent_service_id', $this->serviceId())
            ->where('i.id', $id)
            ->first([
                'i.id', 'i.statut', 'i.commentaire', 'i.latitude', 'i.longitude',
                'i.created_at as iv_cree', 'i.updated_at as iv_maj',
                'a.titre', 'a.type_alerte', 'a.description', 'a.localisation', 'a.statut as alerte_statut',
                'a.created_at as alerte_cree',
                'e.nom as equipe_nom',
                'c.nom as c_nom', 'c.prenom as c_prenom', 'c.telephone as c_tel',
            ]);

        abort_if(! $iv, 404);

        $tz   = fn ($v) => Carbon::parse($v, 'UTC')->timezone(self::TZ);
        $cree = $tz($iv->alerte_cree);
        $prise = $tz($iv->iv_cree);

        return Pdf::loadView('exports.pdf.intervention', array_merge($this->contextePdf($this->service()), [
            'iv'          => $iv,
            'alerteLe'    => $cree->format('d/m/Y H:i'),
            'priseLe'     => $prise->format('d/m/Y H:i'),
            'majLe'       => $tz($iv->iv_maj)->format('d/m/Y H:i'),
            'delaiMin'    => max(0, (int) round($cree->diffInMinutes($prise, false))),
        ]))->setPaper('a4', 'portrait')
           ->download('rapport-intervention-' . $iv->id . '.pdf');
    }

    // ═════════════════════════ Helpers ═════════════════════════

    /**
     * @param array{headings:array, rows:array, pdf?:array} $d
     */
    private function sortie(string $format, string $slug, string $titre, ?string $sousTitre, array $d)
    {
        $fichier = $slug . '-' . now(self::TZ)->format('Ymd');

        if ($format === 'xlsx') {
            return Excel::download(new TableExport($d['headings'], $d['rows'], $titre), $fichier . '.xlsx');
        }

        // PDF : on peut restreindre les colonnes pour que le tableau reste lisible
        $headings = $d['headings'];
        $rows     = $d['rows'];
        if (isset($d['pdf'])) {
            $keep     = $d['pdf'];
            $headings = array_map(fn ($i) => $d['headings'][$i], $keep);
            $rows     = array_map(fn ($r) => array_map(fn ($i) => $r[$i], $keep), $d['rows']);
        }

        return Pdf::loadView('exports.pdf.table', array_merge($this->contextePdf($this->service()), [
            'titre'     => $titre,
            'sousTitre' => $sousTitre,
            'headings'  => $headings,
            'rows'      => $rows,
        ]))->setPaper('a4', 'landscape')->download($fichier . '.pdf');
    }

    /** Variables communes à tous les PDF (en-tête, logo, date). */
    private function contextePdf(Services $service): array
    {
        $logo = null;
        foreach ([public_path('medias/Clogo.jpg'), base_path('medias/Clogo.jpg')] as $chemin) {
            if (is_file($chemin)) {
                $logo = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($chemin));
                break;
            }
        }

        return [
            'service' => $service,
            'logo'    => $logo,
            'genereLe'=> now(self::TZ)->format('d/m/Y à H:i'),
        ];
    }
}
