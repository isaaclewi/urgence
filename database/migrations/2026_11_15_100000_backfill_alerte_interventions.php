<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jusqu'ici aucune interface ne créait de ligne dans alerte_interventions : la page
     * « Interventions », les rapports PDF et les exports restaient vides. Cette migration crée
     * les lignes manquantes pour les affectations déjà démarrées ou terminées.
     */
    public function up(): void
    {
        DB::table('alerte_affectations as af')
            ->join('alertes as al', 'al.id', '=', 'af.alerte_id')
            ->whereIn('af.statut', ['en_cours', 'terminee'])
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))->from('alerte_interventions as i')
                    ->whereColumn('i.alerte_id', 'af.alerte_id')
                    ->whereColumn('i.equipe_id', 'af.service_destination_id');
            })
            ->orderBy('af.id')
            ->select('af.id', 'af.alerte_id', 'af.service_destination_id', 'af.statut', 'af.commentaire',
                     'af.created_at', 'af.updated_at', 'al.latitude', 'al.longitude')
            ->chunkById(200, function ($lignes) {
                $now = now();

                DB::table('alerte_interventions')->insert($lignes->map(fn ($l) => [
                    'alerte_id'   => $l->alerte_id,
                    'equipe_id'   => $l->service_destination_id,
                    'commentaire' => $l->commentaire,
                    'latitude'    => $l->latitude,
                    'longitude'   => $l->longitude,
                    'statut'      => $l->statut,
                    'created_at'  => $l->updated_at ?? $l->created_at ?? $now,
                    'updated_at'  => $l->updated_at ?? $now,
                ])->all());
            }, 'af.id', 'id');
    }

    public function down(): void
    {
        // Données reconstituées : rien à annuler.
    }
};
