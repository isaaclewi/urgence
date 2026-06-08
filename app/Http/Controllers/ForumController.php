<?php

namespace App\Http\Controllers;

use App\Models\DiscussionSpace;
use App\Models\DiscussionMessage;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    /**
     * Liste des espaces de discussion (ADMIN)
     */
    public function index()
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Veuillez vous connecter.');
        }

        $spaces = DiscussionSpace::where('is_active', true)
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('forum.index', compact('spaces'));
    }

    /**
     * Affichage d’un espace de discussion (ADMIN)
     */
    public function show($id)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Veuillez vous connecter.');
        }

        $space = DiscussionSpace::with('service')->findOrFail($id);

        $messages = DiscussionMessage::where('discussion_space_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('forum.group', compact('space', 'messages'));
    }

    /**
     * Envoi de message (ADMIN)
     */
    public function sendMessage(Request $request, $id)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Veuillez vous connecter.');
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        DiscussionMessage::create([
            'discussion_space_id' => $id,
            'sender_type' => 'agent',
            'sender_id'   => session('admin_id'),
            'message_type' => 'texte',
            'message' => $request->message,
        ]);

        return redirect()->route('forum.group', $id);
    }

    /**
     * Suppression d’un message (ADMIN)
     */
    public function deleteMessage($id)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Veuillez vous connecter.');
        }

        $msg = DiscussionMessage::findOrFail($id);

        // L’admin peut supprimer ses propres messages
        if ($msg->sender_type === 'admin' && $msg->sender_id == session('admin_id')) {
            $msg->delete();
            return back()->with('success', 'Message supprimé avec succès.');
        }

        return back()->with('error', 'Vous ne pouvez pas supprimer ce message.');
    }
}
