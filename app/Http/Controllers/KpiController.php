<?php

namespace App\Http\Controllers;

use App\Models\Services;
use App\Services\KpiService;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public const PERIODES = [7, 30, 90];

    public function index(Request $request, KpiService $kpi)
    {
        $service = Services::findOrFail((int) session('service_id'));

        $jours = (int) $request->input('jours', 30);
        if (! in_array($jours, self::PERIODES, true)) {
            $jours = 30;
        }

        return view('services.kpi', array_merge(
            ['jours' => $jours, 'periodes' => self::PERIODES],
            $kpi->calculer($service, $jours)
        ));
    }
}
