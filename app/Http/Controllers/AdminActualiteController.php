<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Admins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminActualiteController extends Controller
{
    public function index()
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $admin = Admins::find(session('admin_id'));
        $actualites = Actualite::latest()->get();

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
            'url_media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4|max:51200',
        ]);

        $urlMedia  = null;
        $typeMedia = null;

        if ($request->hasFile('url_media')) {
            $file     = $request->file('url_media');
            $ext      = strtolower($file->getClientOriginalExtension());
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());

            Storage::disk('supabase')->putFileAs('actualites', $file, $filename);

            $urlMedia  = env('SUPABASE_PUBLIC_URL') . '/actualites/' . $filename;
            $typeMedia = $ext === 'mp4' ? 'mp4' : 'image';
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
            'url_media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4|max:51200',
        ]);

        if ($request->hasFile('url_media')) {
            if ($actualite->url_media) {
                $oldFilename = basename($actualite->url_media);
                Storage::disk('supabase')->delete('actualites/' . $oldFilename);
            }

            $file     = $request->file('url_media');
            $ext      = strtolower($file->getClientOriginalExtension());
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());

            Storage::disk('supabase')->putFileAs('actualites', $file, $filename);

            $actualite->url_media  = env('SUPABASE_PUBLIC_URL') . '/actualites/' . $filename;
            $actualite->type_media = $ext === 'mp4' ? 'mp4' : 'image';
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

        if ($actualite->url_media) {
            $oldFilename = basename($actualite->url_media);
            Storage::disk('supabase')->delete('actualites/' . $oldFilename);
        }

        $actualite->delete();

        return redirect()->route('admin.actualite')
            ->with('success', 'Actualité supprimée.');
    }
}
