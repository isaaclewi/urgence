<?php

namespace App\Http\Controllers;

use App\Models\AvisUser;
use App\Models\Citoyens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Support\ImageUpload;

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
            'photo'       => ImageUpload::REGLE_IMAGE,
        ], ImageUpload::MESSAGES);

        // Photo choisie pour l'avis, sinon la photo de profil du citoyen (clé de session : citoyen_photo)
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $photo = ImageUpload::storeImage($request->file('photo'), 'avis_photos', 'photo');
        } else {
            $photo = session('citoyen_photo');
        }

        AvisUser::create([
            'citoyen_id'   => session('citoyen_id'),
            'nom'          => session('citoyen_nom') ?? 'Utilisateur',
            'message'      => $request->description,
            'photo_profil' => $photo,
        ]);

        return redirect()->back()->with('success', 'Avis ajouté avec succès !');
    }
}
