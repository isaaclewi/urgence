<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Services;
use Illuminate\Http\Request;
use App\Support\ImageUpload;

class ActualiteController extends Controller
{
    /**
     * Actualité appartenant au service connecté — jamais celle d'un autre service.
     * (Avant : Actualite::findOrFail($id) permettait de modifier ou supprimer
     * les actualités de n'importe quel service en changeant l'id dans l'URL.)
     */
    private function actualiteDuService($id): Actualite
    {
        return Actualite::where('id', $id)
            ->where('service_id', session('service_id'))
            ->firstOrFail();
    }

    /** Enregistre un média sur Supabase (image réduite automatiquement) ; renvoie [url, type]. */
    private function stockerMedia($fichier): array
    {
        return ImageUpload::storeMedia($fichier, 'actualites', 'url_media');
    }

    private function supprimerMedia(?string $url): void
    {
        ImageUpload::supprimer($url, 'actualites');
    }

    public function index()
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $service    = Services::findOrFail(session('service_id'));
        $actualites = Actualite::with('service:id,nom,photo_profil')
            ->where('service_id', $service->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(8);

        return view('services.actualite', compact('service', 'actualites'));
    }

    public function store(Request $request)
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $service = Services::findOrFail(session('service_id'));

        $request->validate([
            'contenu'   => 'required|string|max:5000',
            'url_media' => ImageUpload::REGLE_MEDIA,
        ], ImageUpload::MESSAGES);

        $urlMedia = $typeMedia = null;

        if ($request->hasFile('url_media')) {
            [$urlMedia, $typeMedia] = $this->stockerMedia($request->file('url_media'));
        }

        Actualite::create([
            'auteur_nom'       => $service->nom,
            'contenu'          => $request->contenu,
            'date_publication' => now()->toDateString(),
            'service_id'       => $service->id,
            'url_media'        => $urlMedia,
            'type_media'       => $typeMedia,
        ]);

        return redirect()->route('services.actualite')->with('success', 'Actualité publiée avec succès.');
    }

    public function edit($id)
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $actualite = $this->actualiteDuService($id);

        return view('services.actualite-edit', compact('actualite'));
    }

    public function update(Request $request, $id)
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $actualite = $this->actualiteDuService($id);

        $request->validate([
            'contenu'         => 'required|string|max:5000',
            'url_media'       => ImageUpload::REGLE_MEDIA,
            'supprimer_media' => 'nullable|boolean',
        ], ImageUpload::MESSAGES);

        if ($request->hasFile('url_media')) {
            // Nouveau média envoyé d'abord ; l'ancien n'est supprimé qu'ensuite.
            $ancien = $actualite->url_media;
            [$actualite->url_media, $actualite->type_media] = $this->stockerMedia($request->file('url_media'));
            $this->supprimerMedia($ancien);
        } elseif ($request->boolean('supprimer_media')) {
            $this->supprimerMedia($actualite->url_media);
            $actualite->url_media  = null;
            $actualite->type_media = null;
        }

        $actualite->contenu = $request->contenu;
        $actualite->save();

        return redirect()->route('services.actualite')->with('success', 'Actualité modifiée avec succès.');
    }

    public function destroy($id)
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $actualite = $this->actualiteDuService($id);

        $this->supprimerMedia($actualite->url_media);
        $actualite->delete();

        return redirect()->route('services.actualite')->with('success', 'Actualité supprimée avec succès.');
    }
}
