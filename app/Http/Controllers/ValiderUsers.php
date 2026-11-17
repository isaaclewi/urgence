<?php

namespace App\Http\Controllers;

use App\Models\Citoyens;
use App\Support\Identite;
use App\Support\Notifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class ValiderUsers extends Controller
{
    /** File de vérification : dossiers en attente d'abord, du plus ancien au plus récent. */
    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $statut = $request->query('statut', 'en_attente');

        $citoyens = Citoyens::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(fn ($w) => $w->where('nom', 'like', $like)
                    ->orWhere('prenom', 'like', $like)
                    ->orWhere('matricule', 'like', $like));
            })
            ->when(in_array($statut, ['en_attente', 'verifie', 'rejete'], true),
                fn ($query) => $query->where('statut_verification', $statut))
            ->orderBy('created_at', $statut === 'en_attente' ? 'asc' : 'desc')
            ->paginate(10)
            ->withQueryString();

        $compteurs = [
            'en_attente' => Citoyens::where('statut_verification', 'en_attente')->count(),
            'verifie'    => Citoyens::where('statut_verification', 'verifie')->count(),
            'rejete'     => Citoyens::where('statut_verification', 'rejete')->count(),
        ];

        return view('admin.valider', compact('citoyens', 'q', 'statut', 'compteurs'));
    }

    /** Page de contrôle d'un dossier : pièce, selfie, données saisies, cases à cocher, journal. */
    public function show($id)
    {
        $citoyen = Citoyens::findOrFail($id);

        Identite::journaliser($citoyen->id, 'consultation_dossier');

        $controles = Identite::controlesPour($citoyen);

        $journal = DB::table('verification_journal')
            ->where('citoyen_id', $citoyen->id)
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        return view('admin.verification', compact('citoyen', 'controles', 'journal'));
    }

    /** Valide le dossier : toutes les cases doivent être cochées. */
    public function verifier(Request $request, $id)
    {
        $citoyen   = Citoyens::findOrFail($id);
        $controles = Identite::controlesPour($citoyen);

        if (! $citoyen->piece_identite) {
            return back()->with('error', "Ce dossier n'a pas de pièce d'identité : rejetez-le avec un motif.");
        }

        $regles = [];
        $messages = [];
        foreach ($controles as $cle => $libelle) {
            $regles[$cle] = 'accepted';
            $messages["$cle.accepted"] = "Contrôle non confirmé : « $libelle ». Si ce n'est pas conforme, rejetez le dossier avec un motif.";
        }
        $request->validate($regles, $messages);

        $citoyen->update([
            'statut_verification' => 'verifie',
            'etat_compte'         => 'actif',
            'motif_rejet'         => null,
            'verifie_par'         => session('admin_id'),
            'verifie_le'          => now(),
        ]);

        Identite::journaliser($citoyen->id, 'valide', 'Contrôles : ' . implode(', ', array_keys($controles)));

        Notifier::citoyen($citoyen->id, 'Identité vérifiée', 'Votre pièce d’identité a été vérifiée. Votre compte est actif.');
        $this->envoyerMail($citoyen, 'Votre compte CongoAssist est activé', "
            <p>Bonjour <strong>{$citoyen->prenom}</strong>,</p>
            <p>Votre identité a été vérifiée et votre compte est maintenant <strong>actif</strong>.
            Vous pouvez vous connecter.</p>
            <p>Cordialement,<br><strong>L'équipe CongoAssist</strong></p>");

        return redirect()->route('admin.valider')->with('success', "Dossier de {$citoyen->prenom} {$citoyen->nom} validé : compte activé.");
    }

    /** Rejette le dossier avec un motif ; le citoyen reçoit un lien pour renvoyer ses documents. */
    public function rejeter(Request $request, $id)
    {
        $citoyen = Citoyens::findOrFail($id);

        $data = $request->validate(
            ['motif' => 'required|string|min:10|max:500'],
            ['motif.required' => 'Le motif est obligatoire.', 'motif.min' => 'Précisez le motif (au moins 10 caractères) : le citoyen le lira.']
        );

        $citoyen->update([
            'statut_verification' => 'rejete',
            'etat_compte'         => 'desactif',
            'motif_rejet'         => $data['motif'],
            'verifie_par'         => session('admin_id'),
            'verifie_le'          => now(),
        ]);

        Identite::journaliser($citoyen->id, 'rejete', $data['motif']);

        $lien  = URL::temporarySignedRoute('citoyen.reverif.form', now()->addDays(7), ['id' => $citoyen->id]);
        $motif = e($data['motif']);

        $this->envoyerMail($citoyen, 'Votre dossier CongoAssist doit être corrigé', "
            <p>Bonjour <strong>{$citoyen->prenom}</strong>,</p>
            <p>Nous n'avons pas pu valider votre pièce d'identité. Motif :</p>
            <p><em>{$motif}</em></p>
            <p><a href=\"{$lien}\">Cliquez ici pour renvoyer vos documents</a> (lien valable 7 jours).</p>
            <p>Cordialement,<br><strong>L'équipe CongoAssist</strong></p>");

        return redirect()->route('admin.valider')->with('success', "Dossier de {$citoyen->prenom} {$citoyen->nom} rejeté : le citoyen a reçu le motif par email.");
    }

    /** Réactive un compte déjà vérifié. Un dossier non vérifié passe obligatoirement par le contrôle. */
    public function activer($id)
    {
        $citoyen = Citoyens::findOrFail($id);

        if ($citoyen->statut_verification !== 'verifie') {
            return redirect()->route('admin.valider.show', $citoyen->id)
                ->with('error', "Contrôlez d'abord le dossier : un compte ne s'active qu'après vérification.");
        }

        $citoyen->update(['etat_compte' => 'actif']);
        Identite::journaliser($citoyen->id, 'reactive');

        return back()->with('success', 'Le compte du citoyen a été activé avec succès.');
    }

    public function desactiver($id)
    {
        $citoyen = Citoyens::findOrFail($id);
        $citoyen->update(['etat_compte' => 'inactif']);
        Identite::journaliser($citoyen->id, 'desactive');

        return back()->with('success', 'Le compte du citoyen a été désactivé avec succès.');
    }

    private function envoyerMail(Citoyens $citoyen, string $sujet, string $html): void
    {
        try {
            Mail::send([], [], fn ($m) => $m->to($citoyen->email)->subject($sujet)->html($html));
        } catch (\Throwable $e) {
            \Log::error('Erreur envoi mail vérification : ' . $e->getMessage());
        }
    }
}
