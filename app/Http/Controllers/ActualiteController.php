<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActualiteController extends Controller
{
    public function index()
    {
        if (!session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $service    = Services::find(session('service_id'));
        $actualites = Actualite::where('service_id', $service->id)
                               ->orderBy('created_at', 'desc')
                               ->get();

        return view('services.actualite', compact('service', 'actualites'));
    }

    public function store(Request $request)
    {
        if (!session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $service = Services::find(session('service_id'));

        $request->validate([
            'contenu'   => 'required|string|max:5000',
            'url_media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4|max:51200',
        ]);

        $urlMedia  = null;
        $typeMedia = null;

        if ($request->hasFile('url_media')) {
            $file     = $request->file('url_media');
            $filename = time() . '_' . $file->getClientOriginalName();

            // ✅ Upload vers Supabase
            Storage::disk('supabase')->putFileAs('actualites', $file, $filename);

            $urlMedia  = env('SUPABASE_PUBLIC_URL') . '/actualites/' . $filename;
            $typeMedia = strtolower($file->getClientOriginalExtension()) === 'mp4' ? 'mp4' : 'image';
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

    public function update(Request $request, $id)
    {
        if (!session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $actualite = Actualite::findOrFail($id);

        $request->validate([
            'contenu'   => 'required|string|max:5000',
            'url_media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4|max:51200',
        ]);

        if ($request->hasFile('url_media')) {

            // Supprimer ancien fichier sur Supabase
            if ($actualite->url_media) {
                Storage::disk('supabase')->delete('actualites/' . basename($actualite->url_media));
            }

            $file     = $request->file('url_media');
            $filename = time() . '_' . $file->getClientOriginalName();

            //Upload vers Supabase
            Storage::disk('supabase')->putFileAs('actualites', $file, $filename);

            $actualite->url_media  = env('SUPABASE_PUBLIC_URL') . '/actualites/' . $filename;
            $actualite->type_media = strtolower($file->getClientOriginalExtension()) === 'mp4' ? 'mp4' : 'image';
        }

        $actualite->contenu = $request->contenu;
        $actualite->save();

        return redirect()->route('services.actualite')
                         ->with('success', 'Actualité modifiée avec succès.');
    }

    public function destroy($id)
    {
        if (!session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $actualite = Actualite::findOrFail($id);

        //Supprimer sur Supabase
        if ($actualite->url_media) {
            Storage::disk('supabase')->delete('actualites/' . basename($actualite->url_media));
        }

        $actualite->delete();

        return redirect()->route('services.actualite')
                         ->with('success', 'Actualité supprimée avec succès.');
    }
}
