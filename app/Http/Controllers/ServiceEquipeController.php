<?php

namespace App\Http\Controllers;

use App\Models\Services;
use Illuminate\Http\Request;

class ServiceEquipeController extends Controller
{
    /**
     * Liste des équipes du service connecté
     */
    public function index()
    {

        if (! session()->has('service_id')) {

            return redirect()
                ->route('services.login');

        }

        $serviceId = session('service_id');

        $service = Services::findOrFail($serviceId);

        $equipes = Services::where(
            'parent_service_id',
            $serviceId
        )->get();

        return view(
            'services.equipes.index',
            compact(
                'service',
                'equipes'
            )
        );

    }

    /**
     * Formulaire création équipe
     */
    public function create()
    {

        if (! session()->has('service_id')) {

            return redirect()
                ->route('services.login');

        }

        $service = Services::find(
            session('service_id')
        );

        return view(
            'services.equipes.create',
            compact('service')
        );

    }

    /**
     * Enregistrement équipe
     */
    public function store(Request $request)
    {

        if (! session()->has('service_id')) {

            return redirect()
                ->route('services.login');

        }

        $request->validate([

            'nom' => 'required|string|max:191',

            'adresse' => 'nullable|string',

            'telephone' => 'nullable|string',

            'email' => 'nullable|email',

        ]);

        Services::create([

            'nom' => $request->nom,

            'adresse' => $request->adresse,

            'telephone' => $request->telephone,

            'email' => $request->email,

            'role' => 'equipe',

            'type_compte' => 'equipe',

            'parent_service_id' => session('service_id'),

            'disponible' => true,

            'etat_compte' => 'actif',

            'password' => bcrypt('12345678'),

        ]);

        return redirect()
            ->route('services.equipes.index')
            ->with(
                'success',
                'Équipe créée avec succès.'
            );

    }

    /**
     * Modifier équipe
     */
    public function edit($id)
    {

        $equipe = Services::findOrFail($id);

        return view(

            'services.equipes.edit',

            compact('equipe')

        );

    }

    public function update(Request $request, $id)
    {

        $equipe = Services::findOrFail($id);

        $request->validate([

            'nom' => 'required',

            'telephone' => 'nullable',

            'email' => 'nullable|email',

        ]);

        $equipe->update([

            'nom' => $request->nom,

            'telephone' => $request->telephone,

            'email' => $request->email,

        ]);

        return redirect()
            ->route('services.equipes.index')
            ->with(

                'success',

                'Équipe modifiée.'

            );

    }

    /**
     * Suppression équipe
     */
    public function destroy($id)
    {

        $equipe = Services::findOrFail($id);

        $equipe->delete();

        return back()
            ->with(

                'success',

                'Équipe supprimée.'

            );

    }
}
