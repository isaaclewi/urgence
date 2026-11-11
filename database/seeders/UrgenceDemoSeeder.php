<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

/**
 * Jeu de données de démonstration pour l'application Urgence.
 * Données entièrement fictives. Exécution répétable sans vider les tables.
 */
class UrgenceDemoSeeder extends Seeder
{
    private string $now;
    private array $services = [];
    private array $citoyens = [];
    private array $admins = [];
    private array $agents = [];
    private array $alertes = [];

    public function run(): void
    {
        $this->now = now()->toDateTimeString();

        DB::transaction(function () {
            $this->seedUsers();
            $this->seedAdmins();
            $this->seedServices();
            $this->seedCitoyens();
            $this->seedProgrammesEtVaccins();
            $this->seedAgentsEtGardes();
            $this->seedVehiculesEtStocks();
            $this->seedAlertesEtInterventions();
            $this->seedBilansSante();
            $this->seedActualitesEtServicesProposes();
            $this->seedDiscussions();
            $this->seedAvis();
        });

        $this->command?->info('Jeu de démonstration Urgence créé/mis à jour avec succès.');
        $this->command?->line('Comptes de test : admin@urgence.test / password ; citoyen@urgence.test / password');
    }

    private function dates(array $data): array
    {
        return array_merge($data, ['created_at' => $this->now, 'updated_at' => $this->now]);
    }

    private function upsertId(string $table, array $where, array $values): int
    {
        DB::table($table)->updateOrInsert($where, $this->dates($values));
        return (int) DB::table($table)->where($where)->value('id');
    }

    private function seedUsers(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@urgence.test'],
            $this->dates([
                'name' => 'Administrateur Urgence',
                'email_verified_at' => $this->now,
                'password' => Hash::make('password'),
            ])
        );
    }

    private function seedAdmins(): void
    {
        $rows = [
            ['matricule'=>'ADM-001','nom'=>'Loubaki','prenom'=>'Jean','sexe'=>'M','adresse'=>'Centre-ville, Brazzaville','email'=>'admin@urgence.test','telephone'=>'0600000001','etat_compte'=>'actif','mot_passe'=>Hash::make('password'),'role'=>'admin'],
            ['matricule'=>'ADM-002','nom'=>'Mabiala','prenom'=>'Clarisse','sexe'=>'F','adresse'=>'Moungali, Brazzaville','email'=>'supervision@urgence.test','telephone'=>'0600000002','etat_compte'=>'actif','mot_passe'=>Hash::make('password'),'role'=>'super_admin'],
        ];
        foreach ($rows as $row) {
            $id = $this->upsertId('admins', ['matricule'=>$row['matricule']], $row);
            $this->admins[$row['matricule']] = $id;
        }
    }

    private function seedServices(): void
    {
        $rows = [
            ['key'=>'central','nom'=>'Centre national de coordination des urgences','adresse'=>'Centre-ville, Brazzaville','role'=>'coordination','type_compte'=>'central','parent_service_id'=>null,'telephone'=>'0601000000','email'=>'coordination@urgence.test'],
            ['key'=>'pompiers','nom'=>'Sapeurs-pompiers de Brazzaville','adresse'=>'Poto-Poto, Brazzaville','role'=>'pompier','type_compte'=>'service','parent'=>'central','telephone'=>'0601000001','email'=>'pompiers@urgence.test'],
            ['key'=>'police','nom'=>'Commissariat central de Brazzaville','adresse'=>'Centre-ville, Brazzaville','role'=>'police','type_compte'=>'service','parent'=>'central','telephone'=>'0601000002','email'=>'police@urgence.test'],
            ['key'=>'hopital','nom'=>'Centre hospitalier de référence','adresse'=>'Talangaï, Brazzaville','role'=>'hopital','type_compte'=>'service','parent'=>'central','telephone'=>'0601000003','email'=>'hopital@urgence.test'],
            ['key'=>'protection','nom'=>'Protection civile – Brazzaville','adresse'=>'Bacongo, Brazzaville','role'=>'protection_civile','type_compte'=>'service','parent'=>'central','telephone'=>'0601000004','email'=>'protection@urgence.test'],
            ['key'=>'maternite','nom'=>'Maternité municipale','adresse'=>'Moungali, Brazzaville','role'=>'hopital','type_compte'=>'service','parent'=>'hopital','telephone'=>'0601000005','email'=>'maternite@urgence.test'],
            ['key'=>'pompiers_pn','nom'=>'Unité de secours de Pointe-Noire','adresse'=>'Centre-ville, Pointe-Noire','role'=>'pompier','type_compte'=>'service','parent'=>'central','telephone'=>'0601000006','email'=>'secours.pn@urgence.test'],
        ];
        foreach ($rows as $row) {
            $parent = isset($row['parent']) ? ($this->services[$row['parent']] ?? null) : null;
            $values = [
                'nom'=>$row['nom'],'adresse'=>$row['adresse'],'role'=>$row['role'],
                'disponible'=>true,'email'=>$row['email'],'etat_compte'=>'actif',
                'telephone'=>$row['telephone'],'password'=>Hash::make('password'),
                'parent_service_id'=>$parent,'type_compte'=>$row['type_compte'],
            ];
            $id = $this->upsertId('services', ['nom'=>$row['nom'],'role'=>$row['role']], $values);
            $this->services[$row['key']] = $id;
        }
    }

    private function seedCitoyens(): void
    {
        $people = [
            ['CIT-001','Mabiala','Patrick','M','Bacongo','0602000001'],
            ['CIT-002','Ngoma','Estelle','F','Moungali','0602000002'],
            ['CIT-003','Okemba','Fabrice','M','Poto-Poto','0602000003'],
            ['CIT-004','Bissiki','Grâce','F','Talangaï','0602000004'],
            ['CIT-005','Mouanda','Armel','M','Makélékélé','0602000005'],
            ['CIT-006','Tchibinda','Nadia','F','Ouenzé','0602000006'],
            ['CIT-007','Massamba','Junior','M','Djiri','0602000007'],
            ['CIT-008','Mokoko','Prisca','F','Bacongo','0602000008'],
            ['CIT-009','Itoua','Gervais','M','Mfilou','0602000009'],
            ['CIT-010','Bokilo','Sandrine','F','Pointe-Noire','0602000010'],
            ['CIT-011','Nsimba','Christian','M','Pointe-Noire','0602000011'],
            ['CIT-012','Mabondzo','Chantal','F','Talangaï','0602000012'],
        ];
        foreach ($people as $p) {
            $email = strtolower($p[0]).'@citoyen.test';
            $id = $this->upsertId('citoyens', ['matricule'=>$p[0]], [
                'matricule'=>$p[0],'nom'=>$p[1],'prenom'=>$p[2],'sexe'=>$p[3],
                'adresse'=>$p[4].', Congo','email'=>$email,'telephone'=>$p[5],
                'pseudo'=>strtolower($p[2]).$p[0],'etat_compte'=>'actif',
                'piece_identite'=>null,'password'=>Hash::make('password'),
            ]);
            $this->citoyens[$p[0]] = $id;
        }
        $this->citoyens['demo'] = $this->citoyens['CIT-001'];
        DB::table('citoyens')->updateOrInsert(['matricule'=>'CIT-001'], $this->dates([
            'nom'=>'Mabiala','prenom'=>'Patrick','sexe'=>'M','adresse'=>'Bacongo, Congo',
            'email'=>'citoyen@urgence.test','telephone'=>'0602000001','pseudo'=>'patrick',
            'etat_compte'=>'actif','piece_identite'=>null,'password'=>Hash::make('password'),
        ]));
    }

    private function seedProgrammesEtVaccins(): void
    {
        $programmes = [
            ['nom_programme'=>'Vaccination de routine des nourrissons','age_cible'=>'0 à 11 mois','date_debut'=>'2026-01-01','date_fin'=>'2026-12-31','nb_vaccins'=>6,'organisme_resp'=>'Programme élargi de vaccination','categorie'=>'routine'],
            ['nom_programme'=>'Campagne de vaccination contre la rougeole','age_cible'=>'9 mois à 5 ans','date_debut'=>'2026-03-01','date_fin'=>'2026-11-30','nb_vaccins'=>1,'organisme_resp'=>'Direction départementale de la santé','categorie'=>'campagne'],
            ['nom_programme'=>'Vaccination des femmes enceintes','age_cible'=>'Femmes enceintes','date_debut'=>'2026-02-01','date_fin'=>'2026-12-31','nb_vaccins'=>2,'organisme_resp'=>'Centres de santé partenaires','categorie'=>'maternel'],
        ];
        $ids=[];
        foreach ($programmes as $p) $ids[$p['nom_programme']] = $this->upsertId('mon_programmes_vaccin',['nom_programme'=>$p['nom_programme']],$p);
        $vaccins = [
            ['BCG','Serum Institute','1','Intradermique',0,1,true,'Vaccination de routine des nourrissons'],
            ['Polio (VPO)','Bio Farma','4','Orale',0,5,true,'Vaccination de routine des nourrissons'],
            ['Pentavalent','Biological E','3','Intramusculaire',2,5,true,'Vaccination de routine des nourrissons'],
            ['Pneumocoque','Pfizer','3','Intramusculaire',2,5,true,'Vaccination de routine des nourrissons'],
            ['Rougeole-Rubéole','Serum Institute','2','Sous-cutanée',9,60,true,'Campagne de vaccination contre la rougeole'],
            ['Tétanos (Td)','Sanofi','2','Intramusculaire',12,60,false,'Vaccination des femmes enceintes'],
        ];
        foreach ($vaccins as $v) {
            $this->upsertId('vaccin_enfants',['nom_vaccin'=>$v[0]],[
                'nom_vaccin'=>$v[0],'fabricant'=>$v[1],'nombre_doses'=>(int)$v[2],
                'voie_administration'=>$v[3],'date_fabrication'=>'2025-01-01','date_expiration'=>'2027-12-31',
                'age_cible_min'=>$v[4],'age_cible_max'=>$v[5],'vaccination_obligatoire'=>$v[6],
                'programme_id'=>$ids[$v[7]],
            ]);
        }
    }

    private function seedAgentsEtGardes(): void
    {
        $rows = [
            ['AG-POM-001','pompiers','Equipe A','Mabiala','Serge','Chef d’équipe','Capitaine','0603000001','Incendie, sauvetage'],
            ['AG-POM-002','pompiers','Equipe A','Nkounkou','David','Pompier','Sergent','0603000002','Incendie, premiers secours'],
            ['AG-POM-003','pompiers','Equipe B','Bemba','Luc','Conducteur','Caporal','0603000003','Conduite poids lourd'],
            ['AG-POL-001','police','Equipe police','Mouyabi','Alain','Officier de police','Lieutenant','0603000004','Sécurisation, circulation'],
            ['AG-POL-002','police','Equipe police','Ngassaki','Mireille','Agente de police','Brigadière','0603000005','Accueil, prévention'],
            ['AG-HOP-001','hopital','Urgences médicales','Kita','Emmanuel','Médecin urgentiste','Médecin','0603000006','Urgences adultes'],
            ['AG-HOP-002','hopital','Urgences médicales','Mabiala','Odette','Infirmière','Infirmière diplômée','0603000007','Soins d’urgence'],
            ['AG-PC-001','protection','Equipe mobile','Moukala','Boris','Agent de protection civile','Agent','0603000008','Évaluation des risques'],
        ];
        foreach ($rows as $r) {
            $serviceId=$this->services[$r[1]];
            $equipeId=$this->services[$r[1]];
            $id=$this->upsertId('agents',['service_id'=>$serviceId,'matricule'=>$r[0]],[
                'service_id'=>$serviceId,'equipe_id'=>$equipeId,'matricule'=>$r[0],
                'nom'=>$r[3],'prenom'=>$r[4],'fonction'=>$r[5],'grade'=>$r[6],
                'telephone'=>$r[7],'email'=>strtolower($r[0]).'@urgence.test','statut'=>'actif',
                'competences'=>$r[8],'date_embauche'=>'2024-01-15',
            ]);
            $this->agents[$r[0]]=$id;
        }
        $gardes = [
            ['AG-POM-001','pompiers','jour','2026-11-11 07:00:00','2026-11-11 19:00:00','Garde de jour – équipe A'],
            ['AG-POM-002','pompiers','jour','2026-11-11 07:00:00','2026-11-11 19:00:00','Appui intervention'],
            ['AG-POM-003','pompiers','nuit','2026-11-11 19:00:00','2026-11-12 07:00:00','Garde de nuit – équipe B'],
            ['AG-POL-001','police','jour','2026-11-11 07:00:00','2026-11-11 19:00:00','Patrouille secteur centre'],
            ['AG-HOP-001','hopital','jour','2026-11-11 08:00:00','2026-11-11 20:00:00','Urgences médicales'],
            ['AG-HOP-002','hopital','nuit','2026-11-11 20:00:00','2026-11-12 08:00:00','Soins continus'],
        ];
        foreach ($gardes as $g) {
            $this->upsertId('gardes',['agent_id'=>$this->agents[$g[0]],'date_debut'=>$g[3]],[
                'service_id'=>$this->services[$g[1]],'agent_id'=>$this->agents[$g[0]],'type'=>$g[2],
                'date_debut'=>$g[3],'date_fin'=>$g[4],'note'=>$g[5],
            ]);
        }
    }

    private function seedVehiculesEtStocks(): void
    {
        $vehicles = [
            ['pompiers','VSAV','BZ-001-AA','Toyota Hiace – ambulance','disponible',84200,'2027-02-15'],
            ['pompiers','FOURgon','BZ-002-AA','Mercedes Atego – incendie','en_intervention',125400,'2027-01-20'],
            ['pompiers','VLI','BZ-003-AA','Pick-up secours rapide','maintenance',98300,'2026-12-01'],
            ['police','PICKUP','BZ-010-PO','Toyota Hilux – patrouille','disponible',76500,'2027-03-10'],
            ['hopital','AMBULANCE','BZ-020-HO','Hyundai H1 – ambulance','disponible',112000,'2027-01-30'],
            ['protection','UTILITAIRE','BZ-030-PC','Mitsubishi L200 – logistique','disponible',55400,'2027-04-12'],
            ['pompiers_pn','VSAV','PN-001-AA','Toyota Hiace – secours','disponible',43100,'2027-02-01'],
        ];
        foreach ($vehicles as $v) {
            $sid=$this->services[$v[0]];
            $this->upsertId('vehicules',['immatriculation'=>$v[2]],[
                'service_id'=>$sid,'equipe_id'=>$sid,'type'=>$v[1],'immatriculation'=>$v[2],
                'marque_modele'=>$v[3],'statut'=>$v[4],'kilometrage'=>$v[5],'prochaine_maintenance'=>$v[6],
            ]);
        }
        $stocks = [
            ['pompiers','équipement','Extincteur poudre 6 kg','LOT-EXT-26',24,'unité',5,null],
            ['pompiers','protection','Combinaison anti-feu','LOT-COM-12',8,'unité',3,null],
            ['pompiers','médical','Trousse de premiers secours','LOT-SEC-08',14,'kit',5,'2027-08-31'],
            ['hopital','médical','Gants médicaux','LOT-GAN-11',120,'boîte',30,'2027-05-31'],
            ['hopital','médical','Masques chirurgicaux','LOT-MAS-04',18,'boîte',20,'2027-06-30'],
            ['hopital','médical','Compresses stériles','LOT-COM-19',45,'paquet',15,'2027-03-31'],
            ['protection','logistique','Bouteilles d’eau','LOT-EAU-01',60,'carton',10,'2027-01-31'],
            ['police','équipement','Gilets réfléchissants','LOT-GIL-05',3,'unité',5,null],
        ];
        foreach ($stocks as $s) {
            $this->upsertId('stocks',['service_id'=>$this->services[$s[0]],'designation'=>$s[2],'lot'=>$s[3]],[
                'service_id'=>$this->services[$s[0]],'categorie'=>$s[1],'designation'=>$s[2],
                'lot'=>$s[3],'quantite'=>$s[4],'unite'=>$s[5],'seuil_alerte'=>$s[6],'date_expiration'=>$s[7],
            ]);
        }
    }

    private function seedAlertesEtInterventions(): void
    {
        $rows = [
            ['AL-001','Incendie dans une habitation','Départ de feu signalé dans une cuisine.','2026-11-11 08:15:00','Bacongo, Brazzaville','incendie','en_cours','AG-POM-001','pompiers',-4.2780,15.2510],
            ['AL-002','Accident de circulation','Collision entre deux véhicules, circulation ralentie.','2026-11-11 08:42:00','Avenue de la Paix, Moungali','accident','en_cours','AG-HOP-001','hopital',-4.2630,15.2840],
            ['AL-003','Malaise d’une personne','Personne âgée présentant un malaise.','2026-11-11 09:05:00','Poto-Poto, Brazzaville','medical','nouvelle',null,'hopital',-4.2590,15.2780],
            ['AL-004','Inondation localisée','Eau accumulée dans plusieurs concessions après la pluie.','2026-11-10 18:20:00','Talangaï, Brazzaville','inondation','affectee','AG-PC-001','protection',-4.2150,15.2850],
            ['AL-005','Vol à l’arraché signalé','Signalement d’un vol à proximité du marché.','2026-11-10 16:30:00','Marché Total, Bacongo','securite','cloturee','AG-POL-001','police',-4.2860,15.2600],
            ['AL-006','Chute d’arbre sur la chaussée','Un arbre bloque partiellement la voie.','2026-11-10 14:10:00','Ouenzé, Brazzaville','incident','cloturee','AG-POM-003','pompiers',-4.2450,15.2760],
            ['AL-007','Demande d’assistance médicale','Blessure légère nécessitant une évaluation.','2026-11-09 11:35:00','Makélékélé, Brazzaville','medical','cloturee','AG-HOP-002','hopital',-4.3020,15.2450],
            ['AL-008','Fumée dans un bâtiment','Fumée observée dans un local commercial.','2026-11-09 20:45:00','Centre-ville, Brazzaville','incendie','annulee','AG-POM-001','pompiers',-4.2670,15.2830],
            ['AL-009','Accident moto','Motocycliste au sol, besoin d’assistance.','2026-11-11 10:10:00','Djiri, Brazzaville','accident','nouvelle',null,'hopital',-4.1900,15.3000],
            ['AL-010','Dégradation d’un câble électrique','Câble tombé au sol, zone à sécuriser.','2026-11-08 07:50:00','Mfilou, Brazzaville','incident','affectee','AG-PC-001','protection',-4.3150,15.2200],
            ['AL-011','Alerte sanitaire – Pointe-Noire','Demande d’évaluation sanitaire dans un quartier.','2026-11-11 07:40:00','Centre-ville, Pointe-Noire','medical','nouvelle',null,'hopital',-4.7690,11.8660],
            ['AL-012','Début d’incendie de végétation','Fumée observée en périphérie.','2026-11-07 13:25:00','Périphérie de Pointe-Noire','incendie','cloturee','AG-POM-003','pompiers_pn',-4.7900,11.8500],
        ];
        foreach ($rows as $r) {
            $citoyenId=$this->citoyens['CIT-'.str_pad((string)(((int)substr($r[0],3)-1)%12+1),3,'0',STR_PAD_LEFT)];
            $serviceId=$this->services[$r[8]];
            $agentId=$r[7] ? $this->agents[$r[7]] : null;
            $id=$this->upsertId('alertes',['titre'=>$r[1],'date_heure'=>$r[3]],[
                'titre'=>$r[1],'description'=>$r[2],'date_heure'=>$r[3],'localisation'=>$r[4],
                'media_photo'=>null,'media_vocal'=>null,'statut'=>$r[6],'type_alerte'=>$r[5],
                'citoyen_id'=>$citoyenId,'services_id'=>$serviceId,
                'latitude'=>$r[9],'longitude'=>$r[10],
                'statut_intervention'=>$r[6],'equipe_id'=>$serviceId,
            ]);
            $this->alertes[$r[0]]=$id;
            if ($r[7]) {
                $this->upsertId('alerte_affectations',['alerte_id'=>$id,'service_destination_id'=>$serviceId],[
                    'alerte_id'=>$id,'service_source_id'=>$this->services['central'],
                    'service_destination_id'=>$serviceId,'affecte_par'=>$this->admins['ADM-001'],
                    'statut'=>in_array($r[6],['cloturee','annulee']) ? 'terminee' : 'transmise',
                    'commentaire'=>'Affectation de démonstration à l’équipe '.$r[7],
                    'date_affectation'=>$r[3],
                ]);
                if (in_array($r[6],['en_cours','cloturee'])) {
                    $this->upsertId('alerte_interventions',['alerte_id'=>$id,'equipe_id'=>$serviceId],[
                        'alerte_id'=>$id,'equipe_id'=>$serviceId,
                        'commentaire'=>'Équipe '.$r[7].' mobilisée. Compte rendu de démonstration.',
                        'latitude'=>$r[9],'longitude'=>$r[10],
                        'statut'=>$r[6],
                    ]);
                }
            }
        }
    }

    private function seedBilansSante(): void
    {
        $data = [
            ['CIT-001','Aucun antécédent allergique connu','O+','1,72 m','72 kg','Hypertension dans la famille','Activité physique régulière','Aucune maladie chronique connue'],
            ['CIT-002','Pollen (exemple fictif)','A+','1,64 m','60 kg','Diabète dans la famille','Marche régulière','Aucune maladie chronique connue'],
            ['CIT-003',null,'B+','1,78 m','80 kg','Non renseigné','Non renseigné','Non renseigné'],
            ['CIT-004','Aucune allergie déclarée','O-','1,58 m','55 kg','Asthme dans la famille','Activité modérée','Aucune maladie chronique connue'],
            ['CIT-005',null,'AB+','1,81 m','83 kg','Non renseigné','Non renseigné','Non renseigné'],
            ['CIT-006','Arachides (exemple fictif)','A-','1,67 m','63 kg','Non renseigné','Marche régulière','Aucune maladie chronique connue'],
        ];
        foreach ($data as $b) {
            $this->upsertId('bilans_sante',['citoyen_id'=>$this->citoyens[$b[0]]],[
                'citoyen_id'=>$this->citoyens[$b[0]],'allergies'=>$b[1],
                'groupe_sanguin'=>$b[2],'taille'=>$b[3],'poids'=>$b[4],
                'antecedents_familiaux'=>$b[5],'mode_de_vie'=>$b[6],
                'maladies_chroniques'=>$b[7],
                'maladies_passees_importantes'=>null,'interventions_chirurgicales'=>null,
                'antecedents_hospitalisation'=>null,'medicaments_pris_actuellement'=>null,
                'listez_vaccins_reçus'=>'Données fictives de démonstration – à ne pas utiliser pour un soin.',
            ]);
        }
    }

    private function seedActualitesEtServicesProposes(): void
    {
        $news = [
            ['Centre de coordination','Exercice de simulation des secours','Un exercice de coordination entre les équipes de secours est programmé cette semaine.','2026-11-10','Centre de coordination','texte',null,null],
            ['Direction sanitaire','Rappel sur la prévention du paludisme','Quelques gestes de prévention : éliminer les eaux stagnantes et utiliser une moustiquaire.','2026-11-08','Direction sanitaire','texte',null,null],
            ['Protection civile','Conseils en cas d’incendie domestique','Évacuez les lieux, alertez les secours et ne retournez pas dans un bâtiment enfumé.','2026-11-05','Protection civile','texte',null,null],
        ];
        foreach ($news as $n) {
            $this->upsertId('actualites',['auteur_nom'=>$n[0],'date_publication'=>$n[3]],[
                'auteur_nom'=>$n[0],'contenu'=>$n[2],'date_publication'=>$n[3],
                'source'=>$n[4],'type_media'=>$n[5],'url_media'=>$n[6],
                'agent_id'=>$this->admins['ADM-001'],'service_id'=>$this->services['central'],
            ]);
        }
        $proposals = [
            ['Carte des services de secours','Localiser les services de secours et leurs coordonnées.','/services'],
            ['Conseils de prévention','Consulter les consignes de prévention et les gestes qui sauvent.','/conseils'],
            ['Suivi des alertes','Consulter l’évolution des signalements transmis.','/alertes'],
            ['Programmes de vaccination','Découvrir les campagnes et programmes de vaccination.','/vaccination'],
        ];
        foreach ($proposals as $p) {
            $this->upsertId('services_proposes',['nom_service'=>$p[0]],[
                'nom_service'=>$p[0],'description'=>$p[1],'lien'=>$p[2],
                'image'=>null,'admin_id'=>$this->admins['ADM-001'],
            ]);
        }
    }

    private function seedDiscussions(): void
    {
        $spaces = [
            ['Informations officielles','Annonces et informations validées par la coordination.','public',true,true,true,true,'ADM-001','central'],
            ['Prévention et sécurité','Échanges autour des bons réflexes et de la prévention.','public',true,true,true,true,'ADM-001','protection'],
            ['Coordination des interventions','Espace interne de coordination entre services.','prive',true,false,true,true,'ADM-002','central'],
        ];
        foreach ($spaces as $s) {
            $sid=$this->upsertId('discussion_spaces',['title'=>$s[0]],[
                'title'=>$s[0],'description'=>$s[1],'type'=>$s[2],'is_active'=>$s[3],
                'citizens_can_post'=>$s[4],'moderators_can_post'=>$s[5],'agents_can_post'=>$s[6],
                'created_by'=>$this->admins[$s[7]],'service_id'=>$this->services[$s[8]],
            ]);
            $messages = [
                ['agent',$this->admins['ADM-001'],'Bienvenue sur cet espace de démonstration. Merci de partager uniquement des informations utiles et vérifiées.'],
                ['citoyen',$this->citoyens['CIT-001'],'Merci pour les informations. Comment signaler une situation urgente ?'],
                ['service',$this->services[$s[8]],'En cas de danger immédiat, utilisez le formulaire de signalement ou contactez directement les secours.'],
            ];
            foreach ($messages as $i=>$m) {
                $this->upsertId('discussion_messages',['discussion_space_id'=>$sid,'sender_type'=>$m[0],'sender_id'=>$m[1],'message'=>$m[2]],[
                    'discussion_space_id'=>$sid,'sender_id'=>$m[1],'sender_type'=>$m[0],
                    'message_type'=>'texte','message'=>$m[2],'file_path'=>null,'file_name'=>null,
                    'file_mime'=>null,'file_size'=>null,'is_deleted'=>false,
                ]);
            }
        }
    }

    private function seedAvis(): void
    {
        $reviews = [
            ['CIT-001','Patrick Mabiala','Interface facile à comprendre.'],
            ['CIT-002','Estelle Ngoma','Les informations des services sont utiles.'],
            ['CIT-003','Fabrice Okemba','Je souhaite pouvoir suivre mes signalements.'],
            ['CIT-004','Grâce Bissiki','Bonne initiative pour centraliser les alertes.'],
        ];
        foreach ($reviews as $r) {
            DB::table('avis_users')->updateOrInsert(
                ['citoyen_id'=>(string)$this->citoyens[$r[0]],'nom'=>$r[1]],
                $this->dates(['message'=>$r[2]])
            );
        }
    }
}
