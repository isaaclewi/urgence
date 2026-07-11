<?php

namespace App\Http\Controllers;

use App\Models\Admins;
use App\Models\Services;
use App\Models\servicesProposes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class adminServicesController extends Controller
{
    public function index()
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Veuillez vous connecter.');
        }

        $admin            = Admins::find(session('admin_id'));
        $services         = Services::orderBy('created_at', 'desc')->get();
        $servicesProposes = servicesProposes::orderBy('created_at', 'desc')->get();

        return view('admin.services', compact('admin', 'services', 'servicesProposes'));
    }

    // Ajouter un service d'urgence
    public function store(Request $request)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Veuillez vous connecter.');
        }

        $request->validate([
            'nom'         => 'required|string|max:255',
            'adresse'     => 'required|string|max:255',
            'role'        => 'required|in:pompier,police,hopital',
            'disponible'  => 'required|boolean',
            'email'       => 'nullable|email|unique:services,email',
            'telephone'   => 'nullable|string|max:20',
            'etat_compte' => 'required|string|max:50',
            'password'    => 'required|string|min:6',
        ]);

        Services::create([
            'nom'         => $request->nom,
            'adresse'     => $request->adresse,
            'role'        => $request->role,
            'disponible'  => $request->disponible,
            'email'       => $request->email,
            'telephone'   => $request->telephone,
            'etat_compte' => $request->etat_compte,
            'password'    => Hash::make($request->password),
        ]);

        return redirect()->route('admin.services')->with('success', 'Service d\'urgence ajouté avec succès.');
    }

    // Supprimer un service d'urgence
    public function destroy($id)
    {
        Services::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Service d\'urgence supprimé avec succès.');
    }
}
