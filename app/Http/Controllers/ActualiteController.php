<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Services;
use Illuminate\Http\Request;

class ActualiteController extends Controller
{
    // Affiche toutes les actualités du service connecté
    public function index()
    {
        if (!session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $service = Services::find(session('service_id'));

        $actualites = Actualite::where('service_id', $service->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('services.actualite', compact('service', 'actualites'));
    }

    // Stocker une nouvelle actualité
    public function store(Request $request)
    {
        $service = Services::find(session('service_id'));

        $request->validate([
            'contenu'   => 'required|string|max:5000',
            'url_media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4|max:51200',
        ]);

        $urlMedia = null;
        $typeMedia = null;

        if ($request->hasFile('url_media')) {
            $file = $request->file('url_media');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Upload vers /medias/stocke (racine)
            $file->move('medias/stocke', $filename);

            $urlMedia  = 'medias/stocke/' . $filename;
            $typeMedia = $file->getClientOriginalExtension() === 'mp4' ? 'mp4' : 'image';
        }

        Actualite::create([
            'auteur_nom'       => $service->nom,
            'contenu'          => $request->contenu,
            'date_publication' => now()->toDateString(),
            'service_id'       => $service->id,
            'url_media'        => $urlMedia,
            'type_media'       => $typeMedia,
        ]);

        return redirect()->route('services.actualite')
            ->with('success', 'Actualité publiée avec succès.');
    }

    // Mise à jour
    public function update(Request $request, $id)
    {
        $actualite = Actualite::findOrFail($id);

        $request->validate([
            'contenu'   => 'required|string|max:5000',
            'url_media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4|max:51200',
        ]);

        if ($request->hasFile('url_media')) {

            // Supprimer ancien fichier
            if ($actualite->url_media && file_exists($actualite->url_media)) {
                unlink($actualite->url_media);
            }

            $file = $request->file('url_media');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Upload racine
            $file->move('medias/stocke', $filename);

            $actualite->url_media  = 'medias/stocke/' . $filename;
            $actualite->type_media = $file->getClientOriginalExtension() === 'mp4' ? 'mp4' : 'image';
        }

        $actualite->contenu = $request->contenu;
        $actualite->save();

        return redirect()->route('services.actualite')
            ->with('success', 'Actualité modifiée avec succès.');
    }

    // Suppression
    public function destroy($id)
    {
        $actualite = Actualite::findOrFail($id);

        if ($actualite->url_media && file_exists($actualite->url_media)) {
            unlink($actualite->url_media);
        }

        $actualite->delete();

        return redirect()->route('services.actualite')
            ->with('success', 'Actualité supprimée avec succès.');
    }
}
