<?php

namespace App\Http\Controllers;

use App\Models\Admins;
use App\Models\Citoyens;
use Illuminate\Http\Request;

class GestionUsers extends Controller
{
    public function index()
    {
        if (! session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $citoyens = Citoyens::paginate(5);
        $admin = Admins::find(session('admin_id'));

        return view('admin.users', compact('admin', 'citoyens'));
    }

    public function destroy($id)
    {
        if (! session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $citoyen = Citoyens::find($id);

        if (! $citoyen) {
            return back()->with('error', 'Citoyen introuvable.');
        }

        // Supprime les fichiers liés s’ils existent
        if ($citoyen->piece_identite && file_exists(public_path($citoyen->piece_identite))) {
            unlink(public_path($citoyen->piece_identite));
        }
        if ($citoyen->photo_profil && file_exists(public_path($citoyen->photo_profil))) {
            unlink(public_path($citoyen->photo_profil));
        }

        // Supprimer définitivement le citoyen
        $citoyen->delete();

        return back()->with('success', 'Le compte du citoyen a été supprimé définitivement.');
    }
}
