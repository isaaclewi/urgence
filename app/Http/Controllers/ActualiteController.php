<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    /** Enregistre un média sur Supabase sous un nom aléatoire ; renvoie [url, type]. */
    private function stockerMedia($fichier): array
    {
        $extension = strtolower($fichier->extension() ?: $fichier->getClientOriginalExtension());
        $nom       = Str::uuid() . '.' . $extension;

        Storage::disk('supabase')->putFileAs('actualites', $fichier, $nom);

        return [
            config('filesystems.disks.supabase.public_url') . '/actualites/' . $nom,
            $extension === 'mp4' ? 'mp4' : 'image',
        ];
    }

    private function supprimerMedia(?string $url): void
    {
        if ($url) {
            Storage::disk('supabase')->delete('actualites/' . basename($url));
        }
    }

    public function index()
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $service    = Services::findOrFail(session('service_id'));
        $actualites = Actualite::where('service_id', $service->id)
            ->orderByDesc('created_at')
            ->paginate(10);

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
            'url_media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4|max:51200',
        ]);

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
            'url_media'       => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4|max:51200',
            'supprimer_media' => 'nullable|boolean',
        ]);

        if ($request->hasFile('url_media')) {
            $this->supprimerMedia($actualite->url_media);
            [$actualite->url_media, $actualite->type_media] = $this->stockerMedia($request->file('url_media'));
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
