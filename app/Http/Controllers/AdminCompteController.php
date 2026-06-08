<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admins;

class AdminCompteController extends Controller
{
    public function index()
    {
        // Vérifier si l’utilisateur est connecté
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Veuillez vous connecter.');
        }

        // Récupérer les infos de l’admin connecté
        $admin = Admins::find(session('admin_id'));

        return view('admin.compte', compact('admin'));
    }
}
