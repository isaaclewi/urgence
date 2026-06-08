<?php

namespace App\Http\Controllers;

use App\Models\Admins;
use App\Models\DiscussionSpace;
use App\Models\Services;
use Illuminate\Http\Request;

class AdminDiscussionSpaceController extends Controller
{
    public function index()
    {
        $admin = Admins::find(session('admin_id'));

        $spaces = DiscussionSpace::with(['service', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        $agents = Admins::orderBy('nom')->get();
        $services = Services::orderBy('nom')->get();

        return view('admin.discussion_spaces', compact(
            'admin',
            'spaces',
            'agents',
            'services'
        ));
    }

    public function store(Request $request)
    {
        $admin = Admins::find(session('admin_id'));

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:public,prive',
            'service_id' => 'nullable|exists:services,id',
            'moderator_id' => 'nullable|exists:agents,id',
        ]);

        DiscussionSpace::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'service_id' => $request->service_id,
            'created_by' => $admin->id,
            'moderator_id' => $request->moderator_id,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.discussion.spaces')
            ->with('success', 'Espace de discussion créé avec succès.');
    }

    public function toggle($id)
    {
        $space = DiscussionSpace::findOrFail($id);
        $space->is_active = ! $space->is_active;
        $space->save();

        return back()->with('success', 'Statut de l’espace mis à jour.');
    }

    public function destroy($id)
    {
        $space = DiscussionSpace::findOrFail($id);
        $space->delete();

        return back()->with('success', 'Espace de discussion supprimé avec succès.');
    }
}
