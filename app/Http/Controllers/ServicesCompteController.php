<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\alertes;
use App\Models\Citoyens;
use Illuminate\Support\Facades\Storage;

class ServicesCompteController extends Controller
{
    public function index()
    {
        if (!session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $serviceId = session('service_id');
        $service = \App\Models\Services::find($serviceId);

        $stats = [
            'urgences_en_cours' => alertes::where('statut', 'en attente')
                ->where('services_id', $serviceId)
                ->count(),

            'urgences_resolues' => alertes::where('statut', 'resolue')
                ->where('services_id', $serviceId)
                ->count(),

            'citoyens_actifs' => Citoyens::where('etat_compte', 'actif')->count(),
        ];

        $recentAlerts = alertes::latest()->take(5)->get()->map(function ($a) {
            return [
                'title'    => $a->titre ?? 'Alerte sans titre',
                'type'     => ucfirst($a->type ?? 'inconnu'),
                'time'     => $a->created_at->diffForHumans(),
                'location' => $a->localisation ?? '—',
            ];
        });

        return view('services.compte', compact('service', 'stats', 'recentAlerts'));
    }

    /**
     * Upload d'un document lié au compte service
     */
    public function uploadPiece(Request $request)
    {
        if (!session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $request->validate([
            'piece_identite' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $serviceId = session('service_id');
        $file      = $request->file('piece_identite');

        // Nom de fichier unique
        $filename = 'service_' . $serviceId . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Upload vers Supabase Storage
        $path = Storage::disk('supabase')->putFileAs('pieces', $file, $filename);

        if (!$path) {
            return back()->with('error', 'Échec du téléversement du fichier.');
        }

        // Construction de l'URL publique
        $publicUrl = env('SUPABASE_PUBLIC_URL') . '/pieces/' . $filename;

        // Sauvegarde dans la base de données
        $service = \App\Models\Services::find($serviceId);
        $service->piece_identite = $publicUrl;
        $service->save();

        return back()->with('success', 'Document téléversé avec succès.');
    }
}
