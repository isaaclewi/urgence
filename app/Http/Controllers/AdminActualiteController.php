<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Admins;
use Illuminate\Http\Request;
use App\Support\ImageUpload;

class AdminActualiteController extends Controller
{
    public function index()
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $admin = Admins::find(session('admin_id'));
        $actualites = Actualite::with('service:id,nom,photo_profil')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(8);

        return view('admin.actualite', compact('admin', 'actualites'));
    }

    public function store(Request $request)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $admin = Admins::find(session('admin_id'));

        $request->validate([
            'contenu'   => 'required|string|max:5000',
            'url_media' => ImageUpload::REGLE_MEDIA,
        ], ImageUpload::MESSAGES);

        $urlMedia  = null;
        $typeMedia = null;

        if ($request->hasFile('url_media')) {
            [$urlMedia, $typeMedia] = ImageUpload::storeMedia($request->file('url_media'), 'actualites');
        }

        Actualite::create([
            'auteur_nom'       => $admin->nom,
            'contenu'          => $request->contenu,
            'date_publication' => now()->toDateString(),
            'url_media'        => $urlMedia,
            'type_media'       => $typeMedia,
        ]);

        return redirect()->route('admin.actualite')
            ->with('success', 'Actualité publiée avec succès.');
    }

    public function edit($id)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $admin     = Admins::find(session('admin_id'));
        $actualite = Actualite::findOrFail($id);

        return view('admin.actualite_edit', compact('admin', 'actualite'));
    }

    public function update(Request $request, $id)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $actualite = Actualite::findOrFail($id);

        $request->validate([
            'contenu'   => 'required|string|max:5000',
            'url_media' => ImageUpload::REGLE_MEDIA,
        ], ImageUpload::MESSAGES);

        if ($request->hasFile('url_media')) {
            // Nouveau média envoyé d'abord ; l'ancien n'est supprimé qu'ensuite.
            $ancien = $actualite->url_media;
            [$actualite->url_media, $actualite->type_media] = ImageUpload::storeMedia($request->file('url_media'), 'actualites');
            ImageUpload::supprimer($ancien, 'actualites');
        } elseif ($request->boolean('supprimer_media') && $actualite->url_media) {
            ImageUpload::supprimer($actualite->url_media, 'actualites');
            $actualite->url_media  = null;
            $actualite->type_media = null;
        }

        $actualite->contenu = $request->contenu;
        $actualite->save();

        return redirect()->route('admin.actualite')
            ->with('success', 'Actualité modifiée.');
    }

    public function destroy($id)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $actualite = Actualite::findOrFail($id);

        ImageUpload::supprimer($actualite->url_media, 'actualites');

        $actualite->delete();

        return redirect()->route('admin.actualite')
            ->with('success', 'Actualité supprimée.');
    }
}
