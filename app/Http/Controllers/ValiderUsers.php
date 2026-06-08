<?php

namespace App\Http\Controllers;

use App\Models\Admins;
use App\Models\Citoyens;

class ValiderUsers extends Controller
{
    //
    public function index()
    {
        if (! session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        // Ici 5 par page
        $citoyens = Citoyens::paginate(5);

        $admin = Admins::find(session('admin_id'));

        return view('admin.valider', compact('admin', 'citoyens'));
    }

    public function activer($id)
    {
        $citoyen = \App\Models\Citoyens::findOrFail($id);
        $citoyen->etat_compte = 'actif';
        $citoyen->save();

        return back()->with('success', 'Le compte du citoyen a été activé avec succès.');
    }

    public function desactiver($id)
    {
        $citoyen = \App\Models\Citoyens::findOrFail($id);
        $citoyen->etat_compte = 'inactif';
        $citoyen->save();

        return back()->with('success', 'Le compte du citoyen a été désactivé avec succès.');
    }
}
