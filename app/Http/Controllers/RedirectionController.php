<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\servicesProposes;
use App\Models\alertes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\AvisUser;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DiscussionSpace;
use App\Models\DiscussionMessage;

class RedirectionController extends Controller
{
    //
    public function accueil()
    {
        $services = servicesProposes::all(); //

        return view('accueil', compact('services'));
    }

    public function accueilAdmin()
    {
        return view('admin.accueil');
    }

    public function accueilService()
    {
        return view('services.accueil');
    }

    public function formulaire()
    {
        return view('formulaire');
    }

    public function login()
    {
        return view('login');
    }

    public function compte()
    {
        return view('compte');
    }

    public function actualites()
    {
        return view('actualites');
    }

    public function profil()
    {
        return view('profil');
    }

    public function bilanSante()
    {
        return view('bilanSante');
    }

    public function MesAlertes()
    {
        return view('MesAlertes');
    }

    public function forumCitoyen()
    {
        // Vérifier si l’utilisateur est connecté
        if (!session()->has('citoyen_id')) {
            return redirect()->route('accueil')->with('error', 'Veuillez vous connecter.');
        }

        // Récupérer le citoyen
        $citoyen = \App\Models\Citoyens::find(session('citoyen_id'));

        // Récupérer les espaces de discussion
        $spaces = \App\Models\DiscussionSpace::with('service')->get(); // adapter le modèle exact

        return view('forum', compact('citoyen', 'spaces'));
    }

    public function supprimerMessage($id)
    {
        $msg = DiscussionMessage::findOrFail($id);

        // Vérifier que l'utilisateur connecté est bien l'auteur du message
        if ($msg->sender_type === 'citoyen' && $msg->sender_id == session('citoyen_id')) {
            $msg->delete();
            return back()->with('success', 'Message supprimé avec succès !');
        }

        return back()->with('error', 'Vous ne pouvez pas supprimer ce message.');
    }


    public function groupeDiscussion($id)
    {
        // Vérifier si l’utilisateur est connecté
        if (!session()->has('citoyen_id')) {
            return redirect()->route('accueil')->with('error', 'Veuillez vous connecter.');
        }

        $space = DiscussionSpace::with('service')->findOrFail($id);

        $messages = DiscussionMessage::where('discussion_space_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Récupérer le citoyen
        $citoyen = \App\Models\Citoyens::find(session('citoyen_id'));
        return view('group', compact('citoyen', 'space', 'messages'));
    }

    public function creerGroupe(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $senderId = session('citoyen_id');
        if (!$senderId) {
            return redirect()->route('accueil')->with('error', 'Veuillez vous connecter pour envoyer un message.');
        }

        DiscussionMessage::create([
            'discussion_space_id' => $id, // ID du groupe
            'sender_type' => 'citoyen',
            'sender_id' => $senderId,
            'message_type' => 'texte',
            'message' => $request->message,
        ]);

        return redirect()->route('groupeDiscussion', $id)->with('success', 'Message envoyé avec succès !');
    }


    public function vaccinationMenu()
    {
        return view('vaccinationMenu');
    }

    public function avis()
    {
        $avis = AvisUser::all();
        $totalAvis = $avis->count();
        $noteMoyenne = $avis->avg('note') ? number_format($avis->avg('note'), 1) : '0.0';
        $satisfaits = $avis->where('note', '>=', 4)->count();
        $tauxSatisfaction = $totalAvis ? round(($satisfaits / $totalAvis) * 100) : 0;

        return view('avis', compact('avis', 'totalAvis', 'noteMoyenne', 'tauxSatisfaction'));
    }

    public function regle()
    {
        return view('regle');
    }

    public function admin()
    {
        return view('admin.compte');
    }

    public function adminLogin()
    {
        return view('admin.login');
    }

    public function services()
    {
        return view('services.compte');
    }

    public function servicesLogin()
    {
        return view('services.login');
    }

    public function enregistrerAlerte(Request $request)
    {
        // Validation
        $request->validate([
            'type_alerte' => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audio' => 'nullable|file|mimes:webm,mp3,wav|max:10240', // 10MB max
        ]);

        $alerte = new alertes();
        $alerte->titre = $request->type_alerte;
        $alerte->description = $request->description;
        $alerte->date_heure = now();
        $alerte->localisation = $request->localisation;
        $alerte->type_alerte = $request->type_alerte;
        $alerte->statut = 'nouveau';
        $alerte->citoyen_id = Auth::id(); // ✅ citoyen connecté
        $alerte->services_id = null; // Si tu veux l’affecter automatiquement, adapte ici

        // Enregistrement audio
        if ($request->hasFile('audio')) {
            $path = $request->file('audio')->store('alertes_audio', 'public');
            $alerte->media_vocal = $path;
        }

        $alerte->save();

        return back()->with('success', 'Alerte envoyée avec succès !');
    }
}
