<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Services;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Complète UrgenceDemoSeeder pour pouvoir simuler tous les parcours :
 *  - équipes terrain (comptes type_compte = 'equipe') et rattachement des agents,
 *  - comptes de connexion des agents + matricules automatiques,
 *  - alertes affectées aux équipes (avec les bons statuts),
 *  - inscriptions aux programmes (citoyens et agents), enfants et suivi par enfant,
 *  - notifications, journal des bilans, membres des discussions.
 * Données fictives. Mot de passe de tous les comptes : « password ».
 */
class UrgenceSimulationSeeder extends Seeder
{
    private string $now;
    private array $equipes = [];

    public function run(): void
    {
        $this->now = now()->toDateTimeString();

        DB::transaction(function () {
            $this->programmesParService();
            $this->equipes();
            $this->agents();
            $this->alertesVersEquipes();
            $this->inscriptions();
            $this->enfants();
            $this->bilanJournal();
            $this->membresDiscussions();
            $this->notifications();
        });

        $this->command?->info('Données de simulation créées.');
    }

    private function d(array $v): array
    {
        return array_merge($v, ['created_at' => $this->now, 'updated_at' => $this->now]);
    }

    private function id(string $table, array $where, array $values): int
    {
        DB::table($table)->updateOrInsert($where, $this->d($values));
        return (int) DB::table($table)->where($where)->value('id');
    }

    private function service(string $email): int
    {
        return (int) DB::table('services')->where('email', $email)->value('id');
    }

    private function citoyen(string $mat): int
    {
        return (int) DB::table('citoyens')->where('matricule', $mat)->value('id');
    }

    private function programme(string $debutNom): int
    {
        return (int) DB::table('mon_programmes_vaccin')->where('nom_programme', 'like', $debutNom . '%')->value('id');
    }

    /** Le programme « femmes enceintes » est propre à la maternité ; les deux autres sont nationaux. */
    private function programmesParService(): void
    {
        DB::table('mon_programmes_vaccin')
            ->where('nom_programme', 'like', 'Vaccination des femmes enceintes%')
            ->update(['service_id' => $this->service('maternite@urgence.test')]);
    }

    private function equipes(): void
    {
        // Le formulaire de connexion « service » n'accepte que les services SANS parent
        // (ServicesLoginController). Les services de démonstration sont donc remis au niveau racine ;
        // seules les équipes ont un service parent.
        DB::table('services')->where('type_compte', '!=', 'equipe')->update(['parent_service_id' => null]);

        $rows = [
            ['pompiers@urgence.test',   'Équipe Pompiers A',        'equipe.pompiers.a@urgence.test', '0604000001', 'Poto-Poto, Brazzaville'],
            ['pompiers@urgence.test',   'Équipe Pompiers B',        'equipe.pompiers.b@urgence.test', '0604000002', 'Poto-Poto, Brazzaville'],
            ['police@urgence.test',     'Équipe Police Patrouille', 'equipe.police@urgence.test',     '0604000003', 'Centre-ville, Brazzaville'],
            ['hopital@urgence.test',    'Équipe SAMU Urgences',     'equipe.samu@urgence.test',       '0604000004', 'Talangaï, Brazzaville'],
            ['protection@urgence.test', 'Équipe Protection Mobile', 'equipe.protection@urgence.test', '0604000005', 'Bacongo, Brazzaville'],
        ];

        foreach ($rows as $r) {
            $parent = Services::find($this->service($r[0]));
            $id = $this->id('services', ['email' => $r[2]], [
                'nom' => $r[1], 'adresse' => $r[4], 'role' => $parent->role, 'disponible' => true,
                'email' => $r[2], 'etat_compte' => 'actif', 'telephone' => $r[3],
                'password' => Hash::make('password'), 'parent_service_id' => $parent->id,
                'type_compte' => 'equipe',
            ]);
            $this->equipes[$r[2]] = $id;
        }
    }

    private function agents(): void
    {
        // Agents supplémentaires : un compte suspendu (test) et un agent de la maternité.
        $extra = [
            ['ag-pom-004@urgence.test', 'pompiers@urgence.test',  'Ondongo', 'Rodrigue', 'Pompier', 'Soldat', 'suspendu'],
            ['ag-mat-001@urgence.test', 'maternite@urgence.test', 'Ibara',   'Judith',   'Sage-femme', 'Sage-femme', 'actif'],
        ];
        foreach ($extra as $e) {
            $sid = $this->service($e[1]);
            $this->id('agents', ['email' => $e[0]], [
                'service_id' => $sid, 'equipe_id' => null, 'matricule' => strtoupper(substr($e[0], 0, 10)),
                'nom' => $e[2], 'prenom' => $e[3], 'fonction' => $e[4], 'grade' => $e[5],
                'telephone' => '06050000' . random_int(10, 99), 'statut' => 'actif',
                'competences' => 'Démonstration', 'date_embauche' => '2025-03-01',
            ]);
        }

        // email de l'agent => équipe (null = sans équipe → « Mon espace »)
        $equipe = [
            'ag-pom-001@urgence.test' => 'equipe.pompiers.a@urgence.test',
            'ag-pom-002@urgence.test' => 'equipe.pompiers.a@urgence.test',
            'ag-pom-003@urgence.test' => 'equipe.pompiers.b@urgence.test',
            'ag-pom-004@urgence.test' => 'equipe.pompiers.b@urgence.test',
            'ag-pol-001@urgence.test' => 'equipe.police@urgence.test',
            'ag-pol-002@urgence.test' => null,
            'ag-hop-001@urgence.test' => 'equipe.samu@urgence.test',
            'ag-hop-002@urgence.test' => 'equipe.samu@urgence.test',
            'ag-pc-001@urgence.test'  => 'equipe.protection@urgence.test',
            'ag-mat-001@urgence.test' => null,
        ];

        foreach ($equipe as $email => $eq) {
            $agent = Agent::where('email', $email)->first();
            if (! $agent) {
                continue;
            }
            $values = [
                'equipe_id'        => $eq ? $this->equipes[$eq] : null,
                'password'         => Hash::make('password'),
                'compte_actif'     => $email !== 'ag-pom-004@urgence.test',
                // le médecin/infirmière AG-HOP-002 doit changer son mot de passe à la 1re connexion
                'doit_changer_mdp' => $email === 'ag-hop-002@urgence.test',
                'statut'           => 'actif',
            ];
            // Matricule automatique (ex. POM2-26-0001) si ce n'est pas déjà fait
            if (! preg_match('/^[A-Z]{3}\d+-\d{2}-\d{4}$/', $agent->matricule)) {
                $values['matricule'] = Agent::genererMatricule(Services::find($agent->service_id));
            }
            DB::table('agents')->where('id', $agent->id)->update($values + ['updated_at' => $this->now]);
        }
    }

    /** Les affectations visibles par une équipe pointent vers le compte équipe (pas le service parent). */
    private function alertesVersEquipes(): void
    {
        $parTitre = [
            'Incendie dans une habitation'       => 'equipe.pompiers.a@urgence.test',
            'Fumée dans un bâtiment'             => 'equipe.pompiers.a@urgence.test',
            'Chute d’arbre sur la chaussée'      => 'equipe.pompiers.b@urgence.test',
            'Accident de circulation'            => 'equipe.samu@urgence.test',
            'Demande d’assistance médicale'      => 'equipe.samu@urgence.test',
            'Vol à l’arraché signalé'            => 'equipe.police@urgence.test',
            'Inondation localisée'               => 'equipe.protection@urgence.test',
            'Dégradation d’un câble électrique'  => 'equipe.protection@urgence.test',
        ];

        foreach ($parTitre as $titre => $emailEquipe) {
            $alerte = DB::table('alertes')->where('titre', $titre)->first();
            if (! $alerte) {
                continue;
            }
            $eq = $this->equipes[$emailEquipe];
            $etat = match ($alerte->statut_intervention) {
                'en_cours'            => 'en_cours',
                'cloturee', 'annulee' => $alerte->statut_intervention === 'annulee' ? 'annulee' : 'terminee',
                default               => 'transmise',
            };
            $statutAlerte = match ($etat) {
                'en_cours' => 'pris en charge',
                'terminee' => 'terminee',
                default    => $alerte->statut,
            };

            DB::table('alertes')->where('id', $alerte->id)->update([
                'equipe_id' => $eq, 'statut' => $statutAlerte,
                'statut_intervention' => $etat === 'annulee' ? 'annulee' : $etat,
                'updated_at' => $this->now,
            ]);
            DB::table('alerte_affectations')->where('alerte_id', $alerte->id)->delete();
            DB::table('alerte_affectations')->insert($this->d([
                'alerte_id' => $alerte->id,
                'service_source_id' => $alerte->services_id,
                'service_destination_id' => $eq,
                'affecte_par' => DB::table('admins')->value('id'),
                'statut' => $etat,
                'commentaire' => 'Mission de démonstration',
                'date_affectation' => $alerte->date_heure,
            ]));
            DB::table('alerte_interventions')->where('alerte_id', $alerte->id)->delete();
            if (in_array($etat, ['en_cours', 'terminee'], true)) {
                DB::table('alerte_interventions')->insert($this->d([
                    'alerte_id' => $alerte->id, 'equipe_id' => $eq,
                    'commentaire' => 'Équipe sur place (démonstration).',
                    'latitude' => $alerte->latitude, 'longitude' => $alerte->longitude,
                    'statut' => $etat,
                ]));
            }
        }

        // Missions neuves à accepter par l'équipe SAMU (test de la sonnerie / prise en charge)
        foreach (['Malaise d’une personne', 'Accident moto'] as $titre) {
            $alerte = DB::table('alertes')->where('titre', $titre)->first();
            if (! $alerte) {
                continue;
            }
            $eq = $this->equipes['equipe.samu@urgence.test'];
            DB::table('alertes')->where('id', $alerte->id)->update(['equipe_id' => $eq, 'updated_at' => $this->now]);
            DB::table('alerte_affectations')->where('alerte_id', $alerte->id)->delete();
            DB::table('alerte_affectations')->insert($this->d([
                'alerte_id' => $alerte->id, 'service_source_id' => $alerte->services_id,
                'service_destination_id' => $eq, 'affecte_par' => DB::table('admins')->value('id'),
                'statut' => 'transmise', 'commentaire' => 'Nouvelle mission à accepter',
                'date_affectation' => $this->now,
            ]));
        }
    }

    private function inscriptions(): void
    {
        $routine  = $this->programme('Vaccination de routine');
        $rougeole = $this->programme('Campagne de vaccination contre la rougeole');
        $maternel = $this->programme('Vaccination des femmes enceintes');
        $j = fn (int $n) => Carbon::today()->addDays($n)->toDateString();

        $citoyens = [
            ['CIT-001', $routine,  'en_cours',  2, $j(-20), $j(3),  'Rappel prévu cette semaine'],
            ['CIT-001', $rougeole, 'inscrit',   0, null,    null,   null],
            ['CIT-002', $rougeole, 'termine',   1, $j(-60), null,   'Programme terminé'],
            ['CIT-003', $maternel, 'abandonne', 1, $j(-90), null,   'Abandon à la demande du citoyen'],
            ['CIT-004', $maternel, 'en_cours',  1, $j(-15), $j(10), null],
            ['CIT-005', $routine,  'en_cours',  1, $j(-40), $j(-5), 'Dose en retard'],
        ];
        foreach ($citoyens as $c) {
            $cid = $this->citoyen($c[0]);
            $this->id('inscriptions_vaccin', ['programme_id' => $c[1], 'citoyen_id' => $cid], [
                'programme_id' => $c[1], 'citoyen_id' => $cid, 'agent_id' => null,
                'statut' => $c[2], 'doses_recues' => $c[3], 'derniere_dose' => $c[4],
                'prochaine_dose' => $c[5], 'note' => $c[6],
            ]);
        }

        $agents = [
            ['ag-pom-001@urgence.test', $routine,  'en_cours', 1, $j(-10), $j(5)],
            ['ag-pom-002@urgence.test', $rougeole, 'inscrit',  0, null,    null],
            ['ag-hop-001@urgence.test', $rougeole, 'termine',  1, $j(-30), null],
            ['ag-mat-001@urgence.test', $maternel, 'en_cours', 1, $j(-7),  $j(14)],
        ];
        foreach ($agents as $a) {
            $aid = (int) DB::table('agents')->where('email', $a[0])->value('id');
            $this->id('inscriptions_vaccin', ['programme_id' => $a[1], 'agent_id' => $aid], [
                'programme_id' => $a[1], 'citoyen_id' => null, 'agent_id' => $aid,
                'statut' => $a[2], 'doses_recues' => $a[3], 'derniere_dose' => $a[4],
                'prochaine_dose' => $a[5], 'note' => null,
            ]);
        }
    }

    private function enfants(): void
    {
        $j = fn (int $n) => Carbon::today()->addDays($n)->toDateString();
        $liste = [
            ['CIT-001', 'Naomie', 'Mabiala', 'F', Carbon::today()->subMonths(8)->toDateString()],
            ['CIT-001', 'Éden',   'Mabiala', 'M', Carbon::today()->subYears(4)->toDateString()],
            ['CIT-002', 'Joël',   'Ngoma',   'M', Carbon::today()->subMonths(14)->toDateString()],
        ];
        $ids = [];
        foreach ($liste as $e) {
            $cid = $this->citoyen($e[0]);
            $ids[$e[1]] = $this->id('enfants', ['citoyen_id' => $cid, 'prenom' => $e[1]], [
                'citoyen_id' => $cid, 'prenom' => $e[1], 'nom' => $e[2], 'sexe' => $e[3], 'date_naissance' => $e[4],
            ]);
        }

        $vaccin = fn (string $n) => (int) DB::table('vaccin_enfants')->where('nom_vaccin', $n)->value('id');
        $suivi = [
            ['Naomie', 'BCG',               1, $j(-200), null],
            ['Naomie', 'Polio (VPO)',        2, $j(-60),  $j(-10)],   // dose en retard
            ['Naomie', 'Pentavalent',        1, $j(-30),  $j(5)],
            ['Éden',   'Rougeole-Rubéole',   1, $j(-300), $j(20)],
            ['Joël',   'BCG',               1, $j(-400), null],
        ];
        foreach ($suivi as $s) {
            $this->id('suivi_vaccin_enfant', ['enfant_id' => $ids[$s[0]], 'vaccin_id' => $vaccin($s[1])], [
                'enfant_id' => $ids[$s[0]], 'vaccin_id' => $vaccin($s[1]),
                'doses_recues' => $s[2], 'derniere_dose' => $s[3], 'prochaine_dose' => $s[4],
            ]);
        }
    }

    private function bilanJournal(): void
    {
        if (DB::table('bilan_journal')->count() > 0) {
            return;
        }
        $hopital = $this->service('hopital@urgence.test');
        foreach (DB::table('bilans_sante')->get() as $b) {
            DB::table('bilan_journal')->insert([
                'bilan_id' => $b->id, 'citoyen_id' => $b->citoyen_id, 'service_id' => $hopital,
                'action' => 'creation', 'created_at' => $this->now,
            ]);
        }
        $premier = DB::table('bilans_sante')->first();
        if ($premier) {
            DB::table('bilan_journal')->insert([
                'bilan_id' => $premier->id, 'citoyen_id' => $premier->citoyen_id, 'service_id' => $hopital,
                'action' => 'modification', 'created_at' => $this->now,
            ]);
        }
    }

    private function membresDiscussions(): void
    {
        $spaces = DB::table('discussion_spaces')->pluck('id', 'title');
        $public = [$spaces['Informations officielles'] ?? null, $spaces['Prévention et sécurité'] ?? null];
        foreach (array_filter($public) as $sid) {
            foreach (['CIT-001', 'CIT-002', 'CIT-003'] as $m) {
                $this->membre($sid, 'citoyen', $this->citoyen($m), 'member');
            }
        }
        $coord = $spaces['Coordination des interventions'] ?? null;
        if ($coord) {
            foreach (['pompiers', 'police', 'hopital', 'protection'] as $k) {
                $this->membre($coord, 'service', $this->service($k . '@urgence.test'), 'member');
            }
            $this->membre($coord, 'service', $this->service('coordination@urgence.test'), 'moderator');
        }
    }

    private function membre(int $space, string $type, int $id, string $role): void
    {
        $this->id('discussion_members', ['discussion_space_id' => $space, 'member_type' => $type, 'member_id' => $id], [
            'role' => $role, 'can_post' => true, 'status' => 'active',
        ]);
    }

    private function notifications(): void
    {
        if (DB::table('notifications_app')->count() > 0) {
            return;
        }
        $n = function (string $type, int $id, string $cat, string $niveau, string $titre, string $msg, ?string $lien = null, bool $lu = false) {
            DB::table('notifications_app')->insert($this->d([
                'destinataire_type' => $type, 'destinataire_id' => $id, 'categorie' => $cat, 'niveau' => $niveau,
                'titre' => $titre, 'message' => $msg, 'lien' => $lien, 'lu_at' => $lu ? $this->now : null,
            ]));
        };

        $c1 = $this->citoyen('CIT-001');
        $n('citoyen', $c1, 'vaccin', 'important', 'Rappel de dose', 'Votre prochaine dose du programme de routine est prévue dans 3 jours.', '/mon-programme');
        $n('citoyen', $c1, 'vaccin', 'info', 'Dose enregistrée', 'Une dose a été enregistrée dans votre suivi.', '/mon-programme', true);
        $n('citoyen', $c1, 'bilan', 'info', 'Bilan de santé mis à jour', 'Votre bilan de santé a été modifié par le centre hospitalier.');
        $n('citoyen', $this->citoyen('CIT-005'), 'vaccin', 'important', 'Dose en retard', 'Votre dose est en retard. Merci de vous rendre dans un centre.');

        foreach (['pompiers', 'police', 'hopital', 'protection'] as $k) {
            $sid = $this->service($k . '@urgence.test');
            $n('service', $sid, 'alerte', 'urgent', 'Nouvelle urgence', 'Une nouvelle alerte vient d’être signalée dans votre zone.');
        }
        $n('service', $this->service('hopital@urgence.test'), 'vaccin', 'info', 'Nouvelle inscription', 'Un citoyen vient de s’inscrire à un programme de vaccination.');
        $n('service', $this->service('maternite@urgence.test'), 'vaccin', 'info', 'Nouvelle inscription', 'Une patiente suit le programme femmes enceintes.');

        $n('service', $this->equipes['equipe.samu@urgence.test'], 'mission', 'urgent', 'Nouvelle mission', 'Une mission vous a été affectée. Acceptez-la depuis votre tableau de bord.', '/equipe/dashboard');
        $n('service', $this->equipes['equipe.pompiers.a@urgence.test'], 'mission', 'important', 'Mission en cours', 'Votre intervention est en cours.');

        $ag = fn (string $e) => (int) DB::table('agents')->where('email', $e)->value('id');
        $n('agent', $ag('ag-pom-001@urgence.test'), 'garde', 'important', 'Garde planifiée', 'Une garde vous a été planifiée.');
        $n('agent', $ag('ag-pom-001@urgence.test'), 'vaccin', 'info', 'Inscription à un programme', 'Votre service vous a inscrit à un programme de vaccination.');
        $n('agent', $ag('ag-pol-002@urgence.test'), 'systeme', 'info', 'Bienvenue', 'Votre espace personnel est prêt.');
    }
}
