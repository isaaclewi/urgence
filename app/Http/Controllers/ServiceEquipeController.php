<?php

namespace App\Http\Controllers;

use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceEquipeController extends Controller
{
    /**
     * Équipe appartenant au service connecté — jamais un autre compte.
     * (Avant : Services::findOrFail($id) permettait de modifier ou supprimer
     * n'importe quel service ou équipe en changeant l'id dans l'URL.)
     */
    private function equipeDuService($id): Services
    {
        return Services::where('id', $id)
            ->where('parent_service_id', session('service_id'))
            ->where('type_compte', 'equipe')
            ->firstOrFail();
    }

    /**
     * Liste des équipes du service connecté
     */
    public function index()
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login');
        }

        $serviceId = session('service_id');

        $service = Services::findOrFail($serviceId);

        $equipes = Services::where('parent_service_id', $serviceId)->get();

        return view('services.equipes.index', compact('service', 'equipes'));
    }

    /**
     * Formulaire création équipe
     */
    public function create()
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login');
        }

        $service = Services::find(session('service_id'));

        return view('services.equipes.create', compact('service'));
    }

    /**
     * Enregistrement équipe
     */
    public function store(Request $request)
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login');
        }

        // L'email sert d'identifiant de connexion : obligatoire et unique.
        $request->validate([
            'nom'       => 'required|string|max:191',
            'adresse'   => 'nullable|string',
            'telephone' => 'nullable|string',
            'email'     => ['required', 'email', Rule::unique('services', 'email')],
        ]);

        // Mot de passe temporaire aléatoire, affiché une seule fois.
        $motDePasse = Str::random(10);

        Services::create([
            'nom'               => $request->nom,
            'adresse'           => $request->adresse,
            'telephone'         => $request->telephone,
            'email'             => $request->email,
            'role'              => 'equipe',
            'type_compte'       => 'equipe',
            'parent_service_id' => session('service_id'),
            'disponible'        => true,
            'etat_compte'       => 'actif',
            'password'          => Hash::make($motDePasse),
        ]);

        return redirect()
            ->route('services.equipes.index')
            ->with('success', "Équipe créée. Mot de passe temporaire : {$motDePasse} (à communiquer à l'équipe, il ne sera plus affiché).");
    }

    /**
     * Modifier équipe
     */
    public function edit($id)
    {
        $equipe = $this->equipeDuService($id);

        return view('services.equipes.edit', compact('equipe'));
    }

    public function update(Request $request, $id)
    {
        $equipe = $this->equipeDuService($id);

        $request->validate([
            'nom'       => 'required|string|max:191',
            'telephone' => 'nullable|string',
            'adresse'   => 'nullable|string',
            'email'     => ['required', 'email', Rule::unique('services', 'email')->ignore($equipe->id)],
            'password'  => 'nullable|string|min:8',
        ]);

        $donnees = [
            'nom'       => $request->nom,
            'telephone' => $request->telephone,
            'adresse'   => $request->adresse,
            'email'     => $request->email,
        ];

        // Nouveau mot de passe uniquement s'il est saisi
        if ($request->filled('password')) {
            $donnees['password'] = Hash::make($request->password);
        }

        $equipe->update($donnees);

        return redirect()
            ->route('services.equipes.index')
            ->with('success', $request->filled('password') ? 'Équipe modifiée, mot de passe changé.' : 'Équipe modifiée.');
    }

    /**
     * Suppression équipe
     */
    public function destroy($id)
    {
        $this->equipeDuService($id)->delete();

        return back()->with('success', 'Équipe supprimée.');
    }
}
