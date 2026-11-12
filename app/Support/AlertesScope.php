<?php

namespace App\Support;

use App\Models\alertes;
use App\Models\Services;
use Illuminate\Database\Eloquent\Builder;

/**
 * Alertes visibles par un service : celles de son type, plus celles sans type
 * ou dont le type ne correspond à aucun service connu (même règle que la page Urgences).
 */
class AlertesScope
{
    public static function pourService(Services $service): Builder
    {
        $rolesConnus = Services::whereNotNull('role')->pluck('role')->toArray();

        return alertes::query()->where(function ($q) use ($service, $rolesConnus) {
            $q->where('type_alerte', $service->role)
              ->orWhereNull('type_alerte')
              ->orWhere('type_alerte', '')
              ->orWhereNotIn('type_alerte', $rolesConnus);
        });
    }
}
