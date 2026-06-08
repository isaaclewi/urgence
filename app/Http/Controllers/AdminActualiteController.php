<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Admins;
use Illuminate\Http\Request;

class AdminActualiteController extends Controller
{
    public function index()
    {
        $admin = Admins::find(session('admin_id'));
        $actualites = Actualite::orderBy('created_at', 'desc')->get();

        return view('admin.actualite', compact('admin', 'actualites'));
    }

    public function store(Request $request)
    {
        $admin = Admins::find(session('admin_id'));

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
        // ❗ correction Admin -> Admins
        $admin = Admins::find(session('admin_id'));
        $actualite = Actualite::findOrFail($id);

        return view('admin.actualite_edit', compact('admin', 'actualite'));
    }

    public function update(Request $request, $id)
    {
        $actualite = Actualite::findOrFail($id);

        $request->validate([
            'contenu'   => 'required|string|max:5000',
            'url_media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4|max:51200',
        ]);

        if ($request->hasFile('url_media')) {

            // Supprimer ancien média
            if ($actualite->url_media && file_exists($actualite->url_media)) {
                unlink($actualite->url_media);
            }

            $file = $request->file('url_media');
            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move('medias/stocke', $filename);

            $actualite->url_media  = 'medias/stocke/' . $filename;
            $actualite->type_media = $file->getClientOriginalExtension() === 'mp4' ? 'mp4' : 'image';
        }

        $actualite->contenu = $request->contenu;
        $actualite->save();

        return redirect()->route('admin.actualite')
            ->with('success', 'Actualité modifiée.');
    }

    public function destroy($id)
    {
        $actualite = Actualite::findOrFail($id);

        if ($actualite->url_media && file_exists($actualite->url_media)) {
            unlink($actualite->url_media);
        }

        $actualite->delete();

        return redirect()->route('admin.actualite')
            ->with('success', 'Actualité supprimée.');
    }
}
