<?php

namespace App\Http\Controllers;

use App\Models\DiscussionSpace;
use App\Models\DiscussionMessage;
use Illuminate\Http\Request;

class ForumServicesController extends Controller
{
    // Liste des espaces de discussion
    public function index()
    {
        if (!session()->has('service_id')) {
            return redirect()->route('services.accueil')
                ->with('error', 'Veuillez vous connecter pour accéder au forum des services.');
        }

        $spaces = DiscussionSpace::where('is_active', true)
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('services.index', compact('spaces'));
    }

    // Affichage d’un espace de discussion
    public function show($id)
    {
        if (!session()->has('service_id')) {
            return redirect()->route('services.accueil')
                ->with('error', 'Veuillez vous connecter pour accéder au forum des services.');
        }

        $space = DiscussionSpace::with('service')->findOrFail($id);

        $messages = DiscussionMessage::where('discussion_space_id', $id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                $msg->sender_name = 'Inconnu';

                if ($msg->sender_type === 'service') {
                    $service = \App\Models\Services::find($msg->sender_id);
                    $msg->sender_name = $service ? $service->nom : 'Service';
                }

                return $msg;
            });

        $serviceId = session('service_id');
        $service = \App\Models\Services::find($serviceId);

        return view('services.group', compact('space', 'messages', 'service'));
    }

    // Envoyer un message dans un espace
    public function sendMessage(Request $request, $id)
    {
        if (!session()->has('service_id')) {
            return redirect()->route('services.accueil')
                ->with('error', 'Veuillez vous connecter pour envoyer un message.');
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $serviceId = session('service_id');

        DiscussionMessage::create([
            'discussion_space_id' => $id,
            'sender_type' => 'service',
            'sender_id' => $serviceId,
            'message_type' => 'texte',
            'message' => $request->message,
        ]);

        return redirect()->route('services.forum.group', $id);
    }
     public function deleteMessage($id)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $msg = DiscussionMessage::findOrFail($id);

        // L’admin peut supprimer ses propres messages
        if ($msg->sender_type === 'service' && $msg->sender_id == session('service_id')) {
            $msg->delete();
            return back()->with('success', 'Message supprimé avec succès.');
        }

        return back()->with('error', 'Vous ne pouvez pas supprimer ce message.');
    }
}
