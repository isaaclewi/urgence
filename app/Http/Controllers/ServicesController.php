<?php

namespace App\Http\Controllers;

use App\Models\Admins;
use App\Models\Services;
use App\Models\servicesProposes;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index()
{
    if (!session()->has('admin_id')) {
        return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
    }

    $admin    = Admins::find(session('admin_id'));
    $services = servicesProposes::orderBy('created_at', 'desc')->paginate(5); // ✅ bonne table

    return view('admin.serviceCreate', compact('admin', 'services'));
}

    // Ajouter un service proposé
    public function store(Request $request)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $request->validate([
            'nom_service' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'lien'        => 'required|string|max:255',
        ]);

        $imageData = null;
        if ($request->hasFile('image')) {
            $file      = $request->file('image');
            $imageData = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file));
        }

        servicesProposes::create([
            'nom_service' => $request->nom_service,
            'description' => $request->description,
            'lien'        => $request->lien,
            'image'       => $imageData,
            'admin_id'    => session('admin_id'),
        ]);

        return redirect()->route('admin.services')->with('success', 'Service proposé créé avec succès.');
    }

    // Supprimer un service proposé
    public function destroy($id)
    {
        servicesProposes::findOrFail($id)->delete();
        return redirect()->route('admin.services')->with('success', 'Service proposé supprimé.');
    }

    public function activer($id)
    {
        Services::findOrFail($id)->update(['disponible' => 1]);
        return back()->with('success', 'Le service est maintenant disponible.');
    }

    public function desactiver($id)
    {
        Services::findOrFail($id)->update(['disponible' => 0]);
        return back()->with('success', 'Le service a été désactivé.');
    }
}
