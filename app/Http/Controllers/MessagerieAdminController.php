<?php

namespace App\Http\Controllers;

use App\Models\DiscussionMember;
use App\Models\DiscussionSpace;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Administration des groupes de discussion — réservé au portail admin
 * (les routes sont sous le middleware auth.admin).
 * Seule l'administration crée les groupes et décide quels services y ont accès.
 */
class MessagerieAdminController extends Controller
{
    /** Services « principaux » (pas les équipes terrain). */
    private function servicesDisponibles()
    {
        return Services::query()
            ->where(function ($q) {
                $q->whereNull('type_compte')->orWhere('type_compte', 'service');
            })
            ->whereNull('parent_service_id')
            ->orderBy('nom')
            ->get(['id', 'nom', 'role', 'etat_compte']);
    }

    public function index()
    {
        $spaces = DiscussionSpace::with('service')
            ->withCount([
                'messages',
                'members as services_count' => fn ($q) => $q->where('member_type', 'service')->where('status', 'active'),
                'members as demandes_count' => fn ($q) => $q->where('member_type', 'service')->where('status', 'pending'),
            ])
            ->orderByDesc('created_at')
            ->get();

        return view('messagerie.admin.gestion', [
            'spaces'   => $spaces,
            'services' => $this->servicesDisponibles(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->valider($request);

        $space = DiscussionSpace::create($data + [
            'created_by' => session('admin_id'),
            'is_active'  => true,
        ]);

        $this->ajouterServices($space, (array) $request->input('service_ids', []), 'member');

        // Le service responsable est modérateur d'office
        if ($space->service_id) {
            $this->ajouterServices($space, [$space->service_id], 'moderator');
        }

        return redirect()->route('admin.messagerie.membres', $space->id)
            ->with('success', 'Groupe créé. Ajoutez maintenant les services autorisés à y participer.');
    }

    public function update(Request $request, int $id)
    {
        $space = DiscussionSpace::findOrFail($id);
        $space->update($this->valider($request));

        if ($space->service_id) {
            $this->ajouterServices($space, [$space->service_id], 'moderator');
        }

        return back()->with('success', 'Paramètres du groupe mis à jour.');
    }

    public function toggle(int $id)
    {
        $space = DiscussionSpace::findOrFail($id);
        $space->update(['is_active' => ! $space->is_active]);

        return back()->with('success', $space->is_active ? 'Groupe réactivé.' : 'Groupe archivé : il est maintenant en lecture seule pour l\'administration et masqué aux autres.');
    }

    public function destroy(int $id)
    {
        DiscussionSpace::findOrFail($id)->delete();

        return back()->with('success', 'Groupe supprimé avec tous ses messages.');
    }

    // ───────────────────────── Membres ─────────────────────────

    public function membres(int $id)
    {
        $space = DiscussionSpace::with('service')->findOrFail($id);

        $membres = DiscussionMember::where('discussion_space_id', $space->id)
            ->where('member_type', 'service')
            ->get();

        $services = $this->servicesDisponibles();
        $parId    = $services->keyBy('id');

        return view('messagerie.admin.membres', [
            'space'      => $space,
            'membres'    => $membres->sortBy(fn ($m) => ($m->status === 'pending' ? '0' : '1') . ($parId[$m->member_id]->nom ?? ''))->values(),
            'parId'      => $parId,
            'candidats'  => $services->reject(fn ($s) => $membres->contains('member_id', $s->id))->values(),
            'nbCitoyens' => DiscussionMember::where('discussion_space_id', $space->id)->where('member_type', 'citoyen')->count(),
        ]);
    }

    public function ajouterMembres(Request $request, int $id)
    {
        $space = DiscussionSpace::findOrFail($id);

        $data = $request->validate([
            'service_ids'   => ['required', 'array', 'min:1'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'role'          => ['required', Rule::in(['member', 'moderator'])],
        ], ['service_ids.required' => 'Choisissez au moins un service à ajouter.']);

        $n = $this->ajouterServices($space, $data['service_ids'], $data['role']);

        return back()->with('success', $n . ' service(s) ajouté(s) au groupe.');
    }

    public function modifierMembre(Request $request, int $id, int $membreId)
    {
        $membre = DiscussionMember::where('discussion_space_id', $id)->findOrFail($membreId);

        $data = $request->validate([
            'role'     => ['required', Rule::in(['member', 'moderator'])],
            'status'   => ['required', Rule::in(['active', 'muted', 'pending'])],
            'can_post' => ['nullable', 'boolean'],
        ]);

        $membre->update([
            'role'     => $data['role'],
            'status'   => $data['status'],
            'can_post' => $request->boolean('can_post'),
        ]);

        return back()->with('success', 'Accès mis à jour.');
    }

    public function retirerMembre(int $id, int $membreId)
    {
        DiscussionMember::where('discussion_space_id', $id)->findOrFail($membreId)->delete();

        return back()->with('success', 'Service retiré du groupe : il n\'y a plus accès.');
    }

    // ───────────────────────── Outils ─────────────────────────

    private function valider(Request $request): array
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type'        => ['required', Rule::in(['public', 'prive'])],
            'join_policy' => ['required', Rule::in(['invite', 'request'])],
            'service_id'  => ['nullable', 'integer', 'exists:services,id'],
        ]);

        return $data + [
            'citizens_can_post' => $request->boolean('citizens_can_post'),
            'services_can_post' => $request->boolean('services_can_post'),
        ];
    }

    /** Ajoute (ou active) des services dans un groupe. Retourne le nombre traité. */
    private function ajouterServices(DiscussionSpace $space, array $ids, string $role): int
    {
        $n = 0;

        foreach (array_unique(array_map('intval', $ids)) as $serviceId) {
            $membre = DiscussionMember::firstOrNew([
                'discussion_space_id' => $space->id,
                'member_type'         => 'service',
                'member_id'           => $serviceId,
            ]);

            $nouveau = ! $membre->exists;

            // une demande en attente est validée ; un modérateur n'est jamais rétrogradé par un simple ajout
            $membre->status     = 'active';
            $membre->role       = ($membre->role === 'moderator' || $role === 'moderator') ? 'moderator' : 'member';
            $membre->can_post   = $membre->can_post ?? true;
            $membre->invited_by = $membre->invited_by ?? session('admin_id');
            $membre->save();

            $n += $nouveau || $membre->wasChanged() ? 1 : 0;
        }

        return $n;
    }
}
