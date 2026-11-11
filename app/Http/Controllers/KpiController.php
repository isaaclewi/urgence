<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\alertes;
use App\Models\Services;
use App\Models\StockItem;
use App\Models\Vehicule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KpiController extends Controller
{
    private const PERIODES = [7, 30, 90];
    /** Fuseau d'affichage (la base stocke en UTC). */
    private const TZ = 'Africa/Brazzaville';

    public function index(Request $request)
    {
        $sid     = (int) session('service_id');
        $service = Services::findOrFail($sid);

        $jours = (int) $request->input('jours', 30);
        if (! in_array($jours, self::PERIODES, true)) {
            $jours = 30;
        }

        $debutLocal = now(self::TZ)->subDays($jours - 1)->startOfDay();
        $debutUtc   = $debutLocal->copy()->utc();

        // ── Alertes visibles par ce service (même règle que la page Urgences) ──
        $rolesConnus = Services::whereNotNull('role')->pluck('role')->toArray();

        $alertes = alertes::query()
            ->where('created_at', '>=', $debutUtc)
            ->where(function ($q) use ($service, $rolesConnus) {
                $q->where('type_alerte', $service->role)
                  ->orWhereNull('type_alerte')
                  ->orWhere('type_alerte', '')
                  ->orWhereNotIn('type_alerte', $rolesConnus);
            })
            ->get(['id', 'type_alerte', 'statut', 'localisation', 'created_at']);

        $total = $alertes->count();

        $groupe = fn ($s) => in_array($s, ['terminee', 'resolue'], true) ? 'terminees'
                 : ($s === 'pris en charge' ? 'en_cours' : 'ouvertes');
        $parStatut = $alertes->groupBy(fn ($a) => $groupe($a->statut))->map->count();

        $terminees = (int) ($parStatut['terminees'] ?? 0);
        $tauxResolution = $total > 0 ? round($terminees * 100 / $total) : null;

        // ── Courbe : alertes par jour ──
        $parJour = $alertes->groupBy(fn ($a) => $a->created_at->copy()->timezone(self::TZ)->format('Y-m-d'))->map->count();
        $jourLabels = [];
        $jourValeurs = [];
        for ($d = $debutLocal->copy(); $d->lte(now(self::TZ)); $d->addDay()) {
            $jourLabels[]  = $d->format('d/m');
            $jourValeurs[] = (int) ($parJour[$d->format('Y-m-d')] ?? 0);
        }

        // ── Alertes par heure (heure locale) ──
        $parHeure = $alertes->groupBy(fn ($a) => (int) $a->created_at->copy()->timezone(self::TZ)->format('G'))->map->count();
        $heureValeurs = [];
        for ($h = 0; $h < 24; $h++) {
            $heureValeurs[] = (int) ($parHeure[$h] ?? 0);
        }

        // ── Alertes par type ──
        $parType = $alertes->groupBy(fn ($a) => $a->type_alerte ?: 'non précisé')->map->count()->sortDesc();

        // ── Zones les plus touchées ──
        $zones = $alertes->filter(fn ($a) => filled($a->localisation))
            ->groupBy(fn ($a) => mb_strtolower(trim($a->localisation)))
            ->map(fn ($g) => ['lieu' => trim($g->first()->localisation), 'n' => $g->count()])
            ->sortByDesc('n')->take(5)->values();

        // ── Affectations envoyées par ce service (délais + charge équipes) ──
        $rows = DB::table('alerte_affectations as af')
            ->join('alertes as al', 'al.id', '=', 'af.alerte_id')
            ->where('af.service_source_id', $sid)
            ->where('al.created_at', '>=', $debutUtc)
            ->get([
                'af.alerte_id', 'af.service_destination_id', 'af.statut',
                'af.created_at as aff_created', 'af.updated_at as aff_updated',
                'al.created_at as al_created',
            ]);

        $parse = fn ($v) => Carbon::parse($v, 'UTC');

        // Délai d'affectation : création de l'alerte → première affectation
        $delais = $rows->groupBy('alerte_id')->map(function ($g) use ($parse) {
            $premiere = $g->map(fn ($r) => $parse($r->aff_created))->min();
            return max(0, $parse($g->first()->al_created)->diffInMinutes($premiere, false));
        })->values();

        // Durée d'intervention : affectation → dernier changement de statut « terminée »
        $durees = $rows->where('statut', 'terminee')
            ->map(fn ($r) => max(0, $parse($r->aff_created)->diffInMinutes($parse($r->aff_updated), false)))
            ->values();

        // Charge par équipe
        $equipes = Services::where('parent_service_id', $sid)->orderBy('nom')->get(['id', 'nom']);
        $charge = [
            'labels'    => $equipes->pluck('nom')->all(),
            'transmise' => $equipes->map(fn ($e) => $rows->where('service_destination_id', $e->id)->where('statut', 'transmise')->count())->all(),
            'en_cours'  => $equipes->map(fn ($e) => $rows->where('service_destination_id', $e->id)->where('statut', 'en_cours')->count())->all(),
            'terminee'  => $equipes->map(fn ($e) => $rows->where('service_destination_id', $e->id)->where('statut', 'terminee')->count())->all(),
        ];

        // ── Ressources (module étape 3, si les tables existent) ──
        $ressources = null;
        if (Schema::hasTable('agents') && Schema::hasTable('vehicules') && Schema::hasTable('stocks')) {
            $stocks = StockItem::where('service_id', $sid)->get();
            $ressources = [
                'agents_actifs'  => Agent::where('service_id', $sid)->where('statut', 'actif')->count(),
                'vehicules_dispo'=> Vehicule::where('service_id', $sid)->where('statut', 'disponible')->count(),
                'vehicules'      => Vehicule::where('service_id', $sid)->count(),
                'stocks_alerte'  => $stocks->filter(fn ($s) => $s->estSousSeuil() || $s->estExpire() || $s->expireBientot())->count(),
            ];
        }

        return view('services.kpi', [
            'jours'          => $jours,
            'periodes'       => self::PERIODES,
            'total'          => $total,
            'parStatut'      => $parStatut,
            'tauxResolution' => $tauxResolution,
            'delaiMoyen'     => $this->formatDuree($delais->avg()),
            'delaiMedian'    => $this->formatDuree($this->mediane($delais)),
            'dureeMoyenne'   => $this->formatDuree($durees->avg()),
            'nbInterventions'=> $durees->count(),
            'zones'          => $zones,
            'ressources'     => $ressources,
            'charts'         => [
                'jours'  => ['labels' => $jourLabels, 'values' => $jourValeurs],
                'heures' => ['labels' => array_map(fn ($h) => $h . 'h', range(0, 23)), 'values' => $heureValeurs],
                'types'  => ['labels' => $parType->keys()->map(fn ($k) => ucfirst($k))->all(), 'values' => $parType->values()->all()],
                'charge' => $charge,
            ],
        ]);
    }

    private function mediane($valeurs): ?float
    {
        $tri = $valeurs->sort()->values();
        $n   = $tri->count();
        if ($n === 0) {
            return null;
        }
        return $n % 2 ? $tri[intdiv($n, 2)] : ($tri[$n / 2 - 1] + $tri[$n / 2]) / 2;
    }

    private function formatDuree(?float $minutes): string
    {
        if ($minutes === null) {
            return '—';
        }
        $m = (int) round($minutes);
        if ($m < 60) {
            return $m . ' min';
        }
        if ($m < 1440) {
            return intdiv($m, 60) . ' h ' . str_pad((string) ($m % 60), 2, '0', STR_PAD_LEFT);
        }
        return intdiv($m, 1440) . ' j ' . intdiv($m % 1440, 60) . ' h';
    }
}
