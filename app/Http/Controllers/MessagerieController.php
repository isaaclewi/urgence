<?php

namespace App\Http\Controllers;

use App\Models\DiscussionMember;
use App\Models\DiscussionMessage;
use App\Models\Citoyens;
use App\Models\DiscussionSpace;
use App\Models\Services;
use App\Support\Messagerie\Actor;
use App\Support\Messagerie\Presenter;
use App\Support\Messagerie\Rules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Messagerie commune aux trois portails (admin, services, citoyen).
 * Le portail est déduit du préfixe du nom de route ; les droits sont dans App\Support\Messagerie\Rules.
 */
class MessagerieController extends Controller
{
    private const PAGE = 40;

    private function actor(Request $request): Actor
    {
        $portal = Str::before((string) $request->route()->getName(), '.');
        $actor  = Actor::fromPortal($portal);

        abort_if($actor === null, 403, 'Connexion requise.');

        return $actor;
    }

    private function space(int $id): DiscussionSpace
    {
        return DiscussionSpace::with('service')->findOrFail($id);
    }

    /** Le layout citoyen affiche le profil dans sa barre latérale. */
    private function profil(Actor $actor): ?Citoyens
    {
        return $actor->isCitoyen() ? Citoyens::find($actor->id) : null;
    }

    private function layout(Actor $actor): string
    {
        return $actor->isCitoyen() ? 'citoyen' : 'layouts.portal';
    }

    // ───────────────────────── Liste des groupes ─────────────────────────

    public function index(Request $request)
    {
        $actor = $this->actor($request);

        $adhesions = DiscussionMember::where('member_type', $actor->type)
            ->where('member_id', $actor->id)
            ->get()
            ->keyBy('discussion_space_id');

        $query = DiscussionSpace::query()->with('service')->withCount('messages');

        if ($actor->isAdmin()) {
            // tout
        } elseif ($actor->isService()) {
            $query->where('is_active', true)->where(function ($q) use ($adhesions) {
                $q->whereIn('id', $adhesions->keys())->orWhere('join_policy', 'request');
            });
        } else {
            $query->where('is_active', true)->where(function ($q) use ($adhesions) {
                $q->where('type', 'public')->orWhereIn('id', $adhesions->keys());
            });
        }

        $spaces = $query->get();

        // dernier message et non-lus, en deux requêtes pour tous les groupes
        $ids      = $spaces->pluck('id');
        $derniers = DiscussionMessage::whereIn('id', function ($q) use ($ids) {
                $q->selectRaw('MAX(id)')->from('discussion_messages')
                  ->whereIn('discussion_space_id', $ids)->groupBy('discussion_space_id');
            })->get()->keyBy('discussion_space_id');

        $auteurs = Presenter::authors($derniers);

        $spaces = $spaces->map(function ($s) use ($actor, $adhesions, $derniers, $auteurs) {
            $adh      = $adhesions->get($s->id);
            $dernier  = $derniers->get($s->id);
            $s->adhesion = $adh;
            $s->acces    = Rules::canView($actor, $s);

            $s->dernier = $dernier;
            $s->dernier_auteur = $dernier ? Presenter::author($auteurs, $dernier->sender_type, $dernier->sender_id) : null;

            $s->non_lus = 0;
            if ($s->acces && $dernier) {
                $s->non_lus = DiscussionMessage::where('discussion_space_id', $s->id)
                    ->where('id', '>', (int) ($adh->last_read_message_id ?? 0))
                    ->where(fn ($q) => $q->where('sender_type', '!=', $actor->type)->orWhere('sender_id', '!=', $actor->id))
                    ->where('is_deleted', false)
                    ->count();
            }

            return $s;
        })->sortByDesc(fn ($s) => optional($s->dernier)->created_at ?? $s->created_at)->values();

        return view('messagerie.index', [
            'layout'  => $this->layout($actor),
            'citoyen' => $this->profil($actor),
            'actor'   => $actor,
            'base'    => $actor->routePrefix(),
            'spaces'  => $spaces,
        ]);
    }

    // ───────────────────────── Un groupe ─────────────────────────

    public function show(Request $request, int $id)
    {
        $actor = $this->actor($request);
        $space = $this->space($id);

        if (! Rules::canView($actor, $space)) {
            return redirect()->route($actor->routePrefix() . 'index')
                ->with('error', "Vous n'avez pas accès à ce groupe. Contactez l'administration pour y être ajouté.");
        }

        $messages = DiscussionMessage::where('discussion_space_id', $space->id)
            ->orderByDesc('id')->limit(self::PAGE + 1)->get();

        $aPlus    = $messages->count() > self::PAGE;
        $messages = $messages->take(self::PAGE)->reverse()->values();

        [$messages, $auteurs] = Presenter::prepare($messages);

        $this->marquerLu($actor, $space, (int) $messages->max('id'));

        // Participants visibles : services autorisés + nombre d'agents / citoyens
        $membres = DiscussionMember::where('discussion_space_id', $space->id)
            ->where('member_type', 'service')->whereIn('status', ['active', 'muted'])->get();
        $services = Services::whereIn('id', $membres->pluck('member_id'))->get(['id', 'nom', 'role'])->keyBy('id');

        return view('messagerie.space', [
            'layout'   => $this->layout($actor),
            'citoyen'  => $this->profil($actor),
            'actor'    => $actor,
            'base'     => $actor->routePrefix(),
            'space'    => $space,
            'messages' => $messages,
            'auteurs'  => $auteurs,
            'aPlus'    => $aPlus,
            'membres'  => $membres,
            'services' => $services,
            'canPost'  => Rules::canPost($actor, $space),
            'isMod'    => Rules::isModerator($actor, $space),
            'dernierId'=> (int) $messages->max('id'),
            'maintenant' => now()->timestamp,
        ]);
    }

    /** Messages récents / modifiés (polling) ou plus anciens (« voir plus »). */
    public function poll(Request $request, int $id): JsonResponse
    {
        $actor = $this->actor($request);
        $space = $this->space($id);

        if (! Rules::canView($actor, $space)) {
            return response()->json(['ok' => false, 'reason' => 'access'], 403);
        }

        // Anciens messages
        if ($request->filled('before')) {
            $anciens = DiscussionMessage::where('discussion_space_id', $space->id)
                ->where('id', '<', (int) $request->before)
                ->orderByDesc('id')->limit(self::PAGE + 1)->get();

            $aPlus   = $anciens->count() > self::PAGE;
            $anciens = $anciens->take(self::PAGE)->reverse()->values();
            [$anciens, $auteurs] = Presenter::prepare($anciens);

            return response()->json([
                'ok'       => true,
                'html'     => $this->rendre($anciens, $auteurs, $actor, $space),
                'has_more' => $aPlus,
            ]);
        }

        $after = (int) $request->query('after', 0);
        $since = (int) $request->query('since', now()->timestamp);

        $nouveaux = DiscussionMessage::where('discussion_space_id', $space->id)
            ->where('id', '>', $after)->orderBy('id')->limit(100)->get();

        // messages déjà affichés mais modifiés ou supprimés depuis le dernier passage
        $modifies = DiscussionMessage::where('discussion_space_id', $space->id)
            ->where('id', '<=', $after)
            ->where('updated_at', '>', now()->setTimestamp(max(0, $since - 2)))
            ->orderBy('id')->limit(50)->get();

        [$tous, $auteurs] = Presenter::prepare($nouveaux->concat($modifies)->values());

        $nouveauxPrep = $tous->whereIn('id', $nouveaux->pluck('id'))->values();
        $modifiesPrep = $tous->whereIn('id', $modifies->pluck('id'))->values();

        if ($nouveaux->isNotEmpty()) {
            $this->marquerLu($actor, $space, (int) $nouveaux->max('id'));
        }

        return response()->json([
            'ok'       => true,
            'html'     => $this->rendre($nouveauxPrep, $auteurs, $actor, $space),
            'updates'  => $modifiesPrep->map(fn ($m) => [
                'id'   => $m->id,
                'html' => $this->rendre(collect([$m]), $auteurs, $actor, $space),
            ])->values(),
            'last_id'  => (int) ($nouveaux->max('id') ?? $after),
            'now'      => now()->timestamp,
            'can_post' => Rules::canPost($actor, $space),
            'active'   => (bool) $space->is_active,
        ]);
    }

    // ───────────────────────── Écrire / modifier / supprimer ─────────────────────────

    public function send(Request $request, int $id)
    {
        $actor = $this->actor($request);
        $space = $this->space($id);

        if (! Rules::canPost($actor, $space)) {
            return $this->refus($request, "Vous ne pouvez pas écrire dans ce groupe.", 403);
        }

        $data = $request->validate([
            'message'     => ['required', 'string', 'max:2000'],
            'reply_to_id' => ['nullable', 'integer'],
        ]);

        $texte = trim($data['message']);
        if ($texte === '') {
            return $this->refus($request, 'Le message est vide.', 422);
        }

        $cle = 'messagerie:' . $actor->type . ':' . $actor->id;
        if (RateLimiter::tooManyAttempts($cle, 20)) {
            return $this->refus($request, 'Trop de messages envoyés. Patientez quelques secondes.', 429);
        }
        RateLimiter::hit($cle, 60);

        $reponseA = null;
        if (! empty($data['reply_to_id'])) {
            $reponseA = DiscussionMessage::where('discussion_space_id', $space->id)->find($data['reply_to_id'])?->id;
        }

        $m = DiscussionMessage::create([
            'discussion_space_id' => $space->id,
            'sender_type'         => $actor->type,
            'sender_id'           => $actor->id,
            'message_type'        => 'texte',
            'message'             => $texte,
            'reply_to_id'         => $reponseA,
        ]);

        $this->marquerLu($actor, $space, $m->id);

        if ($request->expectsJson()) {
            [$prep, $auteurs] = Presenter::prepare(collect([$m]));

            return response()->json([
                'ok'   => true,
                'id'   => $m->id,
                'html' => $this->rendre($prep, $auteurs, $actor, $space),
            ]);
        }

        return redirect()->route($actor->routePrefix() . 'show', $space->id);
    }

    public function update(Request $request, int $id, int $messageId)
    {
        $actor = $this->actor($request);
        $space = $this->space($id);
        $m     = DiscussionMessage::where('discussion_space_id', $space->id)->findOrFail($messageId);

        if (! Rules::canView($actor, $space) || ! $space->is_active) {
            return $this->refus($request, 'Groupe indisponible.', 403);
        }

        if (! Rules::canEdit($actor, $m)) {
            return $this->refus($request, 'Ce message ne peut plus être modifié (délai de '
                . Rules::EDIT_WINDOW_MINUTES . ' minutes dépassé).', 403);
        }

        $data = $request->validate(['message' => ['required', 'string', 'max:2000']]);
        $texte = trim($data['message']);

        if ($texte === '') {
            return $this->refus($request, 'Le message est vide.', 422);
        }

        if ($texte !== $m->message) {
            $m->update(['message' => $texte, 'edited_at' => now()]);
        }

        [$prep, $auteurs] = Presenter::prepare(collect([$m->fresh()]));

        return response()->json(['ok' => true, 'html' => $this->rendre($prep, $auteurs, $actor, $space)]);
    }

    public function destroy(Request $request, int $id, int $messageId)
    {
        $actor = $this->actor($request);
        $space = $this->space($id);
        $m     = DiscussionMessage::where('discussion_space_id', $space->id)->findOrFail($messageId);

        if (! Rules::canView($actor, $space) || ! Rules::canDelete($actor, $space, $m)) {
            return $this->refus($request, 'Vous ne pouvez pas supprimer ce message.', 403);
        }

        // Suppression « pour tous », conservée en base pour la traçabilité (jamais réaffichée)
        $m->update([
            'is_deleted'      => true,
            'deleted_at'      => now(),
            'deleted_by_type' => $actor->type,
            'deleted_by_id'   => $actor->id,
        ]);

        [$prep, $auteurs] = Presenter::prepare(collect([$m->fresh()]));

        return response()->json(['ok' => true, 'html' => $this->rendre($prep, $auteurs, $actor, $space)]);
    }

    // ───────────────────────── Demande d'accès (services) ─────────────────────────

    public function demanderAcces(Request $request, int $id)
    {
        $actor = $this->actor($request);
        $space = $this->space($id);

        abort_unless($actor->isService() && $space->is_active && $space->join_policy === 'request', 403);

        $existant = Rules::member($actor, $space);

        if ($existant) {
            $msg = $existant->status === 'pending'
                ? 'Votre demande est déjà en attente de validation.'
                : 'Vous avez déjà accès à ce groupe.';

            return back()->with('error', $msg);
        }

        DiscussionMember::create([
            'discussion_space_id' => $space->id,
            'member_type'         => $actor->type,
            'member_id'           => $actor->id,
            'role'                => 'member',
            'status'              => 'pending',
        ]);

        return back()->with('success', "Demande envoyée. L'administration doit la valider avant que vous puissiez accéder au groupe.");
    }

    // ───────────────────────── Outils internes ─────────────────────────

    private function rendre($messages, array $auteurs, Actor $actor, DiscussionSpace $space): string
    {
        return $messages->map(fn ($m) => view('messagerie._message', [
            'm' => $m, 'auteurs' => $auteurs, 'actor' => $actor, 'space' => $space,
        ])->render())->implode('');
    }

    /** Enregistre la dernière lecture (crée la ligne de suivi au besoin pour l'admin et les citoyens). */
    private function marquerLu(Actor $actor, DiscussionSpace $space, int $dernierId): void
    {
        if ($dernierId <= 0) {
            return;
        }

        $membre = Rules::member($actor, $space);

        if ($membre) {
            if ((int) $membre->last_read_message_id < $dernierId) {
                $membre->update(['last_read_message_id' => $dernierId]);
            }
            return;
        }

        // Pas de ligne : seuls l'admin et les citoyens (groupes publics) y accèdent sans être listés
        if ($actor->isService()) {
            return;
        }

        $membre = DiscussionMember::create([
            'discussion_space_id'  => $space->id,
            'member_type'          => $actor->type,
            'member_id'            => $actor->id,
            'role'                 => 'member',
            'status'               => 'active',
            'last_read_message_id' => $dernierId,
        ]);

        Rules::forget();
    }

    private function refus(Request $request, string $message, int $code)
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => false, 'message' => $message], $code);
        }

        return back()->with('error', $message);
    }
}
