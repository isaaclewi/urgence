<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admins;
use App\Models\Services;

class ServicesController extends Controller
{
    // Afficher le formulaire et la liste des services
    public function index()
    {
        if (! session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        // Pagination : 5 services par page
        $services = Services::paginate(5);

        $admin = Admins::find(session('admin_id'));

        return view('admin.serviceCreate', compact('admin', 'services'));
    }

    // Enregistrer un nouveau service
    public function store(Request $request)
    {
        if (! session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'disponible' => 'required|boolean',
            'email' => 'nullable|email|max:255',
            'etat_compte' => 'required|string|max:50',
            'telephone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        Services::create([
            'nom' => $request->nom,
            'adresse' => $request->adresse,
            'role' => $request->role,
            'disponible' => $request->disponible,
            'email' => $request->email,
            'etat_compte' => $request->etat_compte,
            'telephone' => $request->telephone,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('admin.serviceCreate')->with('success', 'Service ajouté avec succès !');
    }

    // Activer un service
    public function activer($id)
    {
        $service = Services::findOrFail($id);
        $service->disponible = 1;
        $service->save();

        return back()->with('success', 'Le service est maintenant disponible.');
    }

    // Désactiver un service
    public function desactiver($id)
    {
        $service = Services::findOrFail($id);
        $service->disponible = 0;
        $service->save();

        return back()->with('success', 'Le service a été désactivé.');
    }
}
