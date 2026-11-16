<?php

namespace App\Http\Controllers;

use App\Models\Admins;
use App\Models\servicesProposes;
use App\Support\LiensServices;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index()
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $admin             = Admins::find(session('admin_id'));
        $servicesProposes  = servicesProposes::orderBy('created_at', 'desc')->paginate(5);

        $pages = LiensServices::options();

        return view('admin.serviceCreate', compact('admin', 'servicesProposes', 'pages'));
    }

    // Ajouter un service proposé
    public function store(Request $request)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $request->validate([
            'page'         => 'required|string',
            'nom_service'  => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'site'         => 'nullable|url|max:255',
            'lien_externe' => 'required_if:page,autre|nullable|url|max:255',
        ], [
            'page.required'            => 'Choisissez le service concerné.',
            'site.url'                 => "L'adresse du site doit commencer par http:// ou https://",
            'lien_externe.required_if' => 'Saisissez le lien complet du service externe.',
            'lien_externe.url'         => 'Le lien externe doit commencer par http:// ou https://',
        ]);

        $pages = LiensServices::options();

        if ($request->page === 'autre') {
            $lien  = $request->lien_externe;
            $titre = $request->nom_service;
            $desc  = $request->description;
        } else {
            if (! isset($pages[$request->page])) {
                return back()->withInput()->withErrors(['page' => 'Service inconnu.']);
            }
            $lien  = LiensServices::lien($request->page, $request->site);
            $titre = $request->nom_service ?: $pages[$request->page]['libelle'];
            $desc  = $request->description ?: $pages[$request->page]['description'];
        }

        if (! $titre) {
            return back()->withInput()->withErrors(['nom_service' => 'Saisissez un titre pour ce lien externe.']);
        }

        $imageData = null;
        if ($request->hasFile('image')) {
            $file      = $request->file('image');
            $imageData = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file));
        }

        servicesProposes::create([
            'nom_service' => $titre,
            'description' => $desc,
            'lien'        => $lien,
            'image'       => $imageData,
            'admin_id'    => session('admin_id'),
        ]);

        return redirect()->route('admin.serviceCreate')->with('success', 'Service proposé créé avec succès.');
    }

    // Supprimer un service proposé
    public function destroy($id)
    {
        servicesProposes::findOrFail($id)->delete();
        return redirect()->route('admin.serviceCreate')->with('success', 'Service proposé supprimé.');
    }

    // activer()/desactiver() retirées d'ici — déplacées dans adminServicesController
}
