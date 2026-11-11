<?php

namespace App\Http\Controllers;

use App\Models\Services;
use App\Models\StockItem;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RessourceController extends Controller
{
    public const TYPES_VEHICULE   = ['ambulance', 'camion pompier', 'véhicule de police', 'moto', 'autre'];
    public const STATUTS_VEHICULE = ['disponible', 'en mission', 'maintenance', 'hors service'];
    public const CATEGORIES_STOCK = ['médicament', 'vaccin', 'matériel', 'consommable'];

    private function serviceId(): int
    {
        return (int) session('service_id');
    }

    private function equipes()
    {
        return Services::where('parent_service_id', $this->serviceId())
            ->orderBy('nom')
            ->get(['id', 'nom']);
    }

    private function vehiculeOuEchec($id): Vehicule
    {
        return Vehicule::where('service_id', $this->serviceId())->findOrFail($id);
    }

    private function stockOuEchec($id): StockItem
    {
        return StockItem::where('service_id', $this->serviceId())->findOrFail($id);
    }

    // ───────────────────── Vue d'ensemble ─────────────────────

    public function index()
    {
        $sid = $this->serviceId();

        $vehicules = Vehicule::with('equipe')->where('service_id', $sid)->orderBy('immatriculation')->get();
        $stocks    = StockItem::where('service_id', $sid)
            ->orderByRaw('CASE WHEN date_expiration IS NULL THEN 1 ELSE 0 END')
            ->orderBy('date_expiration')
            ->orderBy('designation')
            ->get();

        $stats = [
            'vehicules'      => $vehicules->count(),
            'disponibles'    => $vehicules->where('statut', 'disponible')->count(),
            'maintenance'    => $vehicules->filter(fn ($v) => $v->statut === 'maintenance' || $v->maintenanceProche())->count(),
            'sous_seuil'     => $stocks->filter(fn ($s) => $s->estSousSeuil())->count(),
            'expires'        => $stocks->filter(fn ($s) => $s->estExpire())->count(),
            'expire_bientot' => $stocks->filter(fn ($s) => $s->expireBientot())->count(),
        ];

        return view('services.ressources.index', compact('vehicules', 'stocks', 'stats'));
    }

    // ─────────────────────── Véhicules ───────────────────────

    private function vehiculeRules(?Vehicule $vehicule = null): array
    {
        return [
            'type'                  => ['required', Rule::in(self::TYPES_VEHICULE)],
            'immatriculation'       => ['required', 'string', 'max:30',
                                        Rule::unique('vehicules', 'immatriculation')->ignore($vehicule?->id)],
            'marque_modele'         => ['nullable', 'string', 'max:120'],
            'statut'                => ['required', Rule::in(self::STATUTS_VEHICULE)],
            'equipe_id'             => ['nullable', Rule::exists('services', 'id')->where('parent_service_id', $this->serviceId())],
            'kilometrage'           => ['nullable', 'integer', 'min:0'],
            'prochaine_maintenance' => ['nullable', 'date'],
        ];
    }

    public function createVehicule()
    {
        return view('services.ressources.vehicule-form', [
            'vehicule' => new Vehicule(['statut' => 'disponible']),
            'equipes'  => $this->equipes(),
            'types'    => self::TYPES_VEHICULE,
            'statuts'  => self::STATUTS_VEHICULE,
        ]);
    }

    public function storeVehicule(Request $request)
    {
        $data = $request->validate($this->vehiculeRules());
        $data['service_id'] = $this->serviceId();
        Vehicule::create($data);

        return redirect()->route('services.ressources.index')->with('success', 'Véhicule ajouté.');
    }

    public function editVehicule($id)
    {
        return view('services.ressources.vehicule-form', [
            'vehicule' => $this->vehiculeOuEchec($id),
            'equipes'  => $this->equipes(),
            'types'    => self::TYPES_VEHICULE,
            'statuts'  => self::STATUTS_VEHICULE,
        ]);
    }

    public function updateVehicule(Request $request, $id)
    {
        $vehicule = $this->vehiculeOuEchec($id);
        $vehicule->update($request->validate($this->vehiculeRules($vehicule)));

        return redirect()->route('services.ressources.index')->with('success', 'Véhicule mis à jour.');
    }

    public function destroyVehicule($id)
    {
        $this->vehiculeOuEchec($id)->delete();

        return redirect()->route('services.ressources.index')->with('success', 'Véhicule supprimé.');
    }

    // ───────────────────────── Stocks ─────────────────────────

    private function stockRules(): array
    {
        return [
            'categorie'       => ['required', Rule::in(self::CATEGORIES_STOCK)],
            'designation'     => ['required', 'string', 'max:150'],
            'lot'             => ['nullable', 'string', 'max:60'],
            'quantite'        => ['required', 'integer', 'min:0'],
            'unite'           => ['nullable', 'string', 'max:20'],
            'seuil_alerte'    => ['nullable', 'integer', 'min:0'],
            'date_expiration' => ['nullable', 'date'],
        ];
    }

    public function createStock()
    {
        return view('services.ressources.stock-form', [
            'stock'      => new StockItem(['quantite' => 0, 'seuil_alerte' => 0]),
            'categories' => self::CATEGORIES_STOCK,
        ]);
    }

    public function storeStock(Request $request)
    {
        $data = $request->validate($this->stockRules());
        $data['seuil_alerte'] = $data['seuil_alerte'] ?? 0;
        $data['service_id']   = $this->serviceId();
        StockItem::create($data);

        return redirect()->route('services.ressources.index')->with('success', 'Article ajouté au stock.');
    }

    public function editStock($id)
    {
        return view('services.ressources.stock-form', [
            'stock'      => $this->stockOuEchec($id),
            'categories' => self::CATEGORIES_STOCK,
        ]);
    }

    public function updateStock(Request $request, $id)
    {
        $stock = $this->stockOuEchec($id);
        $data  = $request->validate($this->stockRules());
        $data['seuil_alerte'] = $data['seuil_alerte'] ?? 0;
        $stock->update($data);

        return redirect()->route('services.ressources.index')->with('success', 'Stock mis à jour.');
    }

    public function destroyStock($id)
    {
        $this->stockOuEchec($id)->delete();

        return redirect()->route('services.ressources.index')->with('success', 'Article supprimé.');
    }
}
