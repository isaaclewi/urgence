<?php

namespace App\Http\Controllers;

use App\Models\AvisUser;
use App\Models\Citoyens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AvisController extends Controller
{

     public function index()
    {
        if (session()->has('citoyen_id')) {
            $citoyen = Citoyens::find(session('citoyen_id'));

            // Récupérer le premier bilan santé lié au citoyen
           $avis = AvisUser::latest()->get();

            return view('avisUsers', compact('citoyen', 'avis'));
        }

        return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
    }

    // Ajouter un nouvel avis
  public function store(Request $request)
{
    $request->validate([
        'description' => 'required|string',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    $photoPath = null;

    // Si l'utilisateur a téléchargé une photo
    if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
        $photoPath = $request->file('photo')->store('avis_photos', 'public');
    } 
    // Sinon on prend sa photo de session (photo de profil)
    elseif (session()->has('citoyen_photo1')) {
        $photoPath = session('citoyen_photo1');
    }

    AvisUser::create([
        'citoyen_id' => session('citoyen_id'),
        'nom'        => session('citoyen_nom') ?? 'Utilisateur',
        'message'    => $request->description,
        'photo'      => $photoPath, // toujours une valeur ici
    ]);

    return redirect()->back()->with('success', 'Avis ajouté avec succès !');
}


}

