<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Services;
use App\Models\Citoyens;

class ServicesUsersController extends Controller
{
    //
    public function index()
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Vous devez être connecté.');
        }

        // Ici 5 par page
        $citoyens = Citoyens::paginate(5);

        $service = Services::find(session('service_id'));

        return view('services.citoyens', compact('service', 'citoyens'));
    }
}
