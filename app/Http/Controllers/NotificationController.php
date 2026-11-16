<?php

namespace App\Http\Controllers;

use App\Support\Notifier;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private function exigerConnexion(): void
    {
        abort_if(empty(Notifier::identites()), 403);
    }

    /** Interrogé toutes les quelques secondes par la cloche : nouvelles notifications non lues. */
    public function flux(Request $request)
    {
        $this->exigerConnexion();

        $apres   = (int) $request->query('apres', -1);
        $dernier = (int) (Notifier::requete()->max('id') ?? 0);
        $nonLues = Notifier::requete()->whereNull('lu_at')->count();

        $items = [];
        if ($apres >= 0) {
            $items = Notifier::requete()->whereNull('lu_at')->where('id', '>', $apres)
                ->orderBy('id')->limit(20)->get()
                ->map(fn ($n) => [
                    'id'        => $n->id,
                    'titre'     => $n->titre,
                    'message'   => $n->message,
                    'niveau'    => $n->niveau,
                    'categorie' => $n->categorie,
                ])->values();
        }

        return response()->json([
            'non_lues'   => $nonLues,
            'dernier_id' => $dernier,
            'items'      => $items,
        ])->header('Cache-Control', 'no-store');
    }

    public function index()
    {
        $this->exigerConnexion();

        return view('notifications.index', [
            'notifications' => Notifier::requete()->orderByDesc('id')->limit(80)->get(),
            'nonLues'       => Notifier::requete()->whereNull('lu_at')->count(),
            'retour'        => Notifier::accueil(),
        ]);
    }

    /** Marque comme lue puis ouvre le lien de la notification. */
    public function ouvrir($id)
    {
        $this->exigerConnexion();

        $n = Notifier::requete()->where('id', $id)->firstOrFail();

        if (! $n->lu_at) {
            $n->update(['lu_at' => now()]);
        }

        $lien = (string) $n->lien;

        // Liens internes uniquement (jamais de redirection vers un autre site)
        if ($lien !== '' && str_starts_with($lien, '/') && ! str_starts_with($lien, '//')) {
            return redirect($lien);
        }

        return redirect()->route('notifications.index');
    }

    public function toutesLues()
    {
        $this->exigerConnexion();

        Notifier::requete()->whereNull('lu_at')->update(['lu_at' => now()]);

        return back()->with('success', 'Toutes les notifications sont marquées comme lues.');
    }
}
