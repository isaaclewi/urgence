<?php

namespace App\Http\Controllers;

use App\Models\Admins;
use App\Models\servicesProposes;
use Illuminate\Http\Request;

class adminServicesController extends Controller
{
    // Afficher la liste des services
    public function index()
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Veuillez vous connecter.');
        }

        $admin = Admins::find(session('admin_id'));
        $services = servicesProposes::all();

        return view('admin.services', compact('admin', 'services'));
    }

    // Ajouter un nouveau service
    public function store(Request $request)
    {
        $request->validate([
            'nom_service' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'lien' => 'required|string|max:255',
        ]);

        $imageData = null;

        if ($request->hasFile('image')) {
            // Lire le fichier et le convertir en base64
            $file = $request->file('image');
            $imageData = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file));
        }

        ServicesProposes::create([
            'nom_service' => $request->nom_service,
            'description' => $request->description,
            'lien' => $request->lien,
            'image' => $imageData, // on stocke directement en base64
            'admin_id' => session('admin_id') ?? null,
        ]);

        return redirect()->route('admin.services')->with('success', 'Service créé avec succès');
    }

    // Supprimer un service
    public function destroy($id)
    {
        $service = ServicesProposes::findOrFail($id);
        $service->delete(); // plus besoin de Storage

        return redirect()->back()->with('success', 'Service supprimé avec succès.');
    }
}
