<?php

namespace App\Console\Commands;

use App\Models\Citoyens;
use App\Support\Identite;
use App\Support\ImageUpload;
use Illuminate\Console\Command;

/**
 * Supprime les images de pièce et de selfie des dossiers vérifiés depuis N jours.
 * On garde le résultat de la vérification, l'empreinte du numéro et le journal, pas les images.
 *
 *   php artisan identite:purger              -> affiche ce qui serait supprimé
 *   php artisan identite:purger --confirmer  -> supprime vraiment
 */
class PurgerPiecesIdentite extends Command
{
    protected $signature = 'identite:purger {--jours=30 : Âge minimal de la vérification} {--confirmer : Supprime vraiment}';

    protected $description = "Supprime les images de pièces d'identité et de selfies des dossiers vérifiés";

    public function handle(): int
    {
        $limite = now()->subDays((int) $this->option('jours'));

        $citoyens = Citoyens::where('statut_verification', 'verifie')
            ->where('verifie_le', '<', $limite)
            ->where(fn ($q) => $q->whereNotNull('piece_identite')->orWhereNotNull('selfie_identite'))
            ->get();

        $this->info($citoyens->count() . ' dossier(s) concerné(s).');

        if (! $this->option('confirmer')) {
            $this->line('Simulation seulement. Ajoutez --confirmer pour supprimer.');
            return self::SUCCESS;
        }

        foreach ($citoyens as $c) {
            ImageUpload::supprimer($c->piece_identite, 'pieces_identite');
            ImageUpload::supprimer($c->selfie_identite, 'selfies_identite');
            $c->update(['piece_identite' => null, 'selfie_identite' => null]);
            Identite::journaliser($c->id, 'purge_images', 'Images supprimées après vérification');
        }

        $this->info('Terminé.');
        return self::SUCCESS;
    }
}
