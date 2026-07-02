<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectionController;
use App\Http\Controllers\UrgenceController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\EquipeDashboardController;
use App\Http\Controllers\ServiceEquipeController;
use App\Http\Controllers\ForumServicesController;
use App\Http\Controllers\CitoyenController;
use App\Http\Controllers\compteController;
use App\Http\Controllers\bilanController;
use App\Http\Controllers\actualitesController;
use App\Http\Controllers\vaccinationMenuController;
use App\Http\Controllers\MesAlertesController;
use App\Http\Controllers\profilController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlertePoliceController;
use App\Http\Controllers\AlertePompierController;
use App\Http\Controllers\AlerteHopitalController;
use App\Http\Controllers\ProgrammeVaccinController;
use App\Http\Controllers\ProgrammeVaccinEpidemieController;
use App\Http\Controllers\ProgrammeVaccinEnfantController;
use App\Http\Controllers\CodeQRController;
use App\Http\Controllers\SuiviAlertesController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminRegisterController;
use App\Http\Controllers\AdminCompteController;
use App\Http\Controllers\adminServicesController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\AdminBilanController;
use App\Http\Controllers\AdminProfilController;
use App\Http\Controllers\GestionUsers;
use App\Http\Controllers\ValiderUsers;
use App\Http\Controllers\AdminActualiteController;
use App\Http\Controllers\AdminAlertesController;
use App\Http\Controllers\AdminDiscussionSpaceController;
use App\Http\Controllers\ServicesCompteController;
use App\Http\Controllers\ServicesLoginController;
use App\Http\Controllers\ServicesProfilController;
use App\Http\Controllers\ServicesUsersController;
use App\Http\Controllers\VaccinationController;
use App\Http\Controllers\ServicesBilanController;
use App\Http\Controllers\ServiceVaccinEnfantController;
use App\Http\Controllers\ActualiteController;
use App\Http\Controllers\PieceIdentiteCitoyenController; //Dechiffrement de la pièce d'identité


// ─── PUBLIC ─────────────────────────────────────
Route::get('/', [RedirectionController::class, 'accueil'])->name('accueil');
Route::get('/formulaire', [RedirectionController::class, 'formulaire'])->name('formulaire');
Route::post('/citoyens', [CitoyenController::class, 'store'])->name('citoyens.store');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'processLogin'])->name('processLogin');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/avis', [RedirectionController::class, 'avis'])->name('avis');
Route::get('/regle', [RedirectionController::class, 'regle'])->name('regle');

Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'processLogin'])->name('admin.login.process');

Route::get('/services/accueil', [RedirectionController::class, 'accueilService'])->name('services.accueil');
Route::get('/services/login', [ServicesLoginController::class, 'showLoginForm'])->name('services.login');
Route::post('/services/login', [ServicesLoginController::class, 'processLogin'])->name('services.login.process');
Route::get('/services/logout', [ServicesLoginController::class, 'logout'])->name('services.logout');

Route::post('/alerte/enregistrer', [UrgenceController::class, 'enregistrerAlerte'])
    ->name('enregistrerAlerte');

// ─── CITOYEN (connecté) ─────────────────────────
Route::middleware(['auth.citoyen', 'no.cache'])->group(function () {
    Route::get('/compte', [compteController::class, 'index'])->name('compte');
    Route::get('/compteController', [compteController::class, 'index'])->name('compteController');
    Route::get('/profil', [profilController::class, 'index'])->name('profilController');
    Route::put('/profil/update', [profilController::class, 'update'])->name('profil.update');
    Route::get('/bilanSante', [bilanController::class, 'index'])->name('bilanController');
    Route::get('/actualites', [actualitesController::class, 'index'])->name('actualitesController');
    Route::get('/MesAlertes', [MesAlertesController::class, 'index'])->name('MesAlertesController');
    Route::get('/mes-alertes', [SuiviAlertesController::class, 'index'])->name('SuiviAlertesController');
    Route::get('/vaccinationMenu', [vaccinationMenuController::class, 'index'])->name('vaccinationMenuController');
    Route::get('/avisUsers', [AvisController::class, 'index'])->name('avisUsers.index');
    Route::post('/avisUsers', [AvisController::class, 'store'])->name('avisUsers.store');
    Route::get('/codeqr', [CodeQRController::class, 'index'])->name('codeQR');
    Route::get('/codeqr/download', [CodeQRController::class, 'download'])->name('codeQR.download');
    Route::get('/alertePolice', [AlertePoliceController::class, 'index'])->name('alertePolice.index');
    Route::post('/alertePolice', [AlertePoliceController::class, 'store'])->name('alertePolice.store');
    Route::get('/alertePompier', [AlertePompierController::class, 'index'])->name('alertePompier.index');
    Route::post('/alertePompier', [AlertePompierController::class, 'store'])->name('alertePompier.store');
    Route::get('/alerteHopital', [AlerteHopitalController::class, 'index'])->name('alerteHopital.index');
    Route::post('/alerteHopital', [AlerteHopitalController::class, 'store'])->name('alerteHopital.store');
    Route::get('/programmes-vaccination', [ProgrammeVaccinController::class, 'index'])->name('programmeVaccinController');
    Route::get('/programmes-epidemie', [ProgrammeVaccinEpidemieController::class, 'index'])->name('programmeVaccinEpidemieController');
    Route::get('/programmes-vaccination-enfant', [ProgrammeVaccinEnfantController::class, 'index'])->name('programmeVaccinEnfantController');
    Route::get('/forumCitoyen', [RedirectionController::class, 'forumCitoyen'])->name('forumCitoyen');
    Route::get('/groupeDiscussion/{id}', [RedirectionController::class, 'groupeDiscussion'])->name('groupeDiscussion');
    Route::post('/groupeDiscussion/{id}/creer', [RedirectionController::class, 'creerGroupe'])->name('creerGroupe');
    Route::delete('/forumCitoyen/message/{id}', [RedirectionController::class, 'supprimerMessage'])->name('supprimerMessage');
});

// ─── ADMIN (connecté) ───────────────────────────
Route::middleware(['auth.admin', 'no.cache'])->prefix('admin')->group(function () {
    Route::get('/accueil', [RedirectionController::class, 'accueilAdmin'])->name('admin.accueil');
    Route::get('/compte', [AdminCompteController::class, 'index'])->name('admin.compte');
    Route::get('/profil', [AdminProfilController::class, 'index'])->name('admin.profil');
    Route::get('/profil/edit', [AdminProfilController::class, 'edit'])->name('admin.profil.edit');
    Route::post('/profil/update', [AdminProfilController::class, 'update'])->name('admin.profil.update');
    Route::get('/users', [GestionUsers::class, 'index'])->name('admin.users');
    Route::delete('/users/{id}', [GestionUsers::class, 'destroy'])->name('admin.users.delete');
    Route::get('/valider', [ValiderUsers::class, 'index'])->name('admin.valider');
    Route::post('/activer/{id}', [ValiderUsers::class, 'activer'])->name('admin.activer');
    Route::post('/desactiver/{id}', [ValiderUsers::class, 'desactiver'])->name('admin.desactiver');
    Route::get('/services', [adminServicesController::class, 'index'])->name('admin.services');
    Route::post('/services/urgence', [adminServicesController::class, 'store'])->name('admin.services.store');
    Route::delete('/services/urgence/{id}', [adminServicesController::class, 'destroy'])->name('admin.services.destroy');
    Route::get('/service-create', [ServicesController::class, 'index'])->name('admin.serviceCreate');
    Route::post('/service-store', [ServicesController::class, 'store'])->name('admin.serviceStore');
    Route::delete('/service-propose/{id}', [ServicesController::class, 'destroy'])->name('admin.serviceDestroy');
    Route::get('/service-activer/{id}', [ServicesController::class, 'activer'])->name('admin.serviceActiver');
    Route::get('/service-desactiver/{id}', [ServicesController::class, 'desactiver'])->name('admin.serviceDesactiver');
    Route::get('/bilans', [AdminBilanController::class, 'index'])->name('admin.bilanSante');
    Route::get('/bilan/{id}', [AdminBilanController::class, 'show'])->name('admin.bilan.show');
    Route::post('/bilan', [AdminBilanController::class, 'store'])->name('admin.bilan.store');
    Route::put('/bilan/{id}', [AdminBilanController::class, 'update'])->name('admin.bilan.update');
    Route::get('/actualites', [AdminActualiteController::class, 'index'])->name('admin.actualite');
    Route::post('/actualites', [AdminActualiteController::class, 'store'])->name('admin.actualiteStore');
    Route::get('/actualites/{id}/edit', [AdminActualiteController::class, 'edit'])->name('admin.actualiteEdit');
    Route::put('/actualites/{id}', [AdminActualiteController::class, 'update'])->name('admin.actualiteUpdate');
    Route::delete('/actualites/{id}', [AdminActualiteController::class, 'destroy'])->name('admin.actualiteDestroy');
    Route::get('/alertes', [AdminAlertesController::class, 'index'])->name('admin.alertes');
    Route::get('/discussion-spaces', [AdminDiscussionSpaceController::class, 'index'])->name('admin.discussion.spaces');
    Route::post('/discussion-spaces', [AdminDiscussionSpaceController::class, 'store'])->name('admin.discussion.space.store');
    Route::get('/discussion-spaces/toggle/{id}', [AdminDiscussionSpaceController::class, 'toggle'])->name('admin.discussion.space.toggle');
    Route::get('/discussion-space/{id}/delete', [AdminDiscussionSpaceController::class, 'destroy'])->name('admin.discussion.space.delete');
    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/{id}', [ForumController::class, 'show'])->name('forum.group');
Route::post('/forum/{id}/message', [ForumController::class, 'sendMessage'])->name('forum.group.send');
Route::delete('/forum/message/{id}', [ForumController::class, 'deleteMessage'])->name('forum.message.delete');

//Mon commentaire (Déchiffrement de la pièce d'identité)
Route::get('/citoyen/piece/{id}', [PieceIdentiteCitoyenController::class, 'show'])->name('citoyen.piece');


});

// ─── SERVICE (connecté) ─────────────────────────
Route::middleware(['auth.service', 'no.cache'])->group(function () {
    Route::get('/services/compte', [ServicesCompteController::class, 'index'])->name('services.compte');
    Route::post('/services/compte/upload', [ServicesCompteController::class, 'uploadPiece'])->name('services.compte.upload');
    Route::get('/services/profil', [ServicesProfilController::class, 'index'])->name('services.profil');
    Route::post('/services/profil/update', [ServicesProfilController::class, 'update'])->name('services.profil.update');
    Route::get('/services/citoyens', [ServicesUsersController::class, 'index'])->name('services.citoyens');
    Route::get('/services/urgences', [UrgenceController::class, 'index'])->name('services.urgenceSignalee');
    Route::post('/services/urgences/{id}/update', [UrgenceController::class, 'updateStatut'])->name('services.urgenceSignaleeUpdate');
    Route::post('/services/urgence/affecter/{id}', [UrgenceController::class, 'affecter'])->name('services.urgence.affecter');
    Route::get('/services/interventions-journalieres', [UrgenceController::class, 'interventionsJournalieres'])->name('services.interventions.journalieres');
    Route::get('/services/urgences/stream', [UrgenceController::class, 'stream'])->name('services.urgences.stream');

    Route::get('/services/equipes', [ServiceEquipeController::class, 'index'])->name('services.equipes.index');
Route::get('/services/equipes/create', [ServiceEquipeController::class, 'create'])->name('services.equipes.create');
Route::post('/services/equipes', [ServiceEquipeController::class, 'store'])->name('services.equipes.store');
Route::get('/services/equipes/{id}/edit', [ServiceEquipeController::class, 'edit'])->name('services.equipes.edit');
Route::put('/services/equipes/{id}', [ServiceEquipeController::class, 'update'])->name('services.equipes.update');
Route::delete('/services/equipes/{id}', [ServiceEquipeController::class, 'destroy'])->name('services.equipes.destroy');





// Actualités
Route::get('/services/actualites', [ActualiteController::class, 'index'])->name('services.actualite');
Route::post('/services/actualites', [ActualiteController::class, 'store'])->name('services.actualiteStore');
Route::get('/services/actualites/{id}/edit', [ActualiteController::class, 'edit'])->name('services.actualiteEdit');
Route::put('/services/actualites/{id}', [ActualiteController::class, 'update'])->name('services.actualiteUpdate');
Route::delete('/services/actualites/{id}', [ActualiteController::class, 'destroy'])->name('services.actualiteDestroy');
// Forum
Route::get('/services/forum', [ForumServicesController::class, 'index'])->name('services.forum.index');
Route::get('/services/forum/{id}', [ForumServicesController::class, 'show'])->name('services.forum.group');
Route::post('/services/forum/{id}/message', [ForumServicesController::class, 'sendMessage'])->name('services.forum.group.send');
Route::delete('/services/forum/message/{id}', [ForumServicesController::class, 'deleteMessage'])->name('services.forum.message.delete');
    // vaccination, bilan, forum, equipes, actualites services… (même groupe)
Route::get('/services/citoyens/bilan', [ServicesBilanController::class, 'index'])->name('services.citoyensBilan');
Route::post('/services/citoyens/bilan', [ServicesBilanController::class, 'store'])->name('services.citoyensBilanStore');
Route::delete('/services/citoyens/bilan/{id}', [ServicesBilanController::class, 'destroy'])->name('services.citoyensBilanDestroy');

    Route::get('/services/vaccination', [VaccinationController::class, 'index'])->name('services.vaccinationIndex');
Route::get('/services/vaccination/create', [VaccinationController::class, 'create'])->name('services.vaccinationCreate');
Route::post('/services/vaccination', [VaccinationController::class, 'store'])->name('services.vaccinationStore');
Route::get('/services/vaccination/{id}/edit', [VaccinationController::class, 'edit'])->name('services.vaccinationEdit');
Route::put('/services/vaccination/{id}', [VaccinationController::class, 'update'])->name('services.vaccinationUpdate');
Route::delete('/services/vaccination/{id}', [VaccinationController::class, 'destroy'])->name('services.vaccinationDestroy');

Route::get('/services/vaccination-enfants', [ServiceVaccinEnfantController::class, 'index'])->name('services.vaccinationEnfantsIndex');
Route::get('/services/vaccination-enfants/create', [ServiceVaccinEnfantController::class, 'create'])->name('services.vaccinationEnfantsCreate');
Route::post('/services/vaccination-enfants', [ServiceVaccinEnfantController::class, 'store'])->name('services.vaccinationEnfantsStore');
Route::get('/services/vaccination-enfants/{id}/edit', [ServiceVaccinEnfantController::class, 'edit'])->name('services.vaccinationEnfantsEdit');
Route::put('/services/vaccination-enfants/{id}', [ServiceVaccinEnfantController::class, 'update'])->name('services.vaccinationEnfantsUpdate');
Route::delete('/services/vaccination-enfants/{id}', [ServiceVaccinEnfantController::class, 'destroy'])->name('services.vaccinationEnfantsDestroy');

Route::get('/services/bilans/chiffrer-anciens', [ServicesBilanController::class, 'chiffrerAnciens']);
});

// ─── ÉQUIPE (connectée) ─────────────────────────
Route::middleware(['auth.equipe', 'no.cache'])->prefix('equipe')->name('equipe.')->group(function () {
    Route::get('/dashboard', [EquipeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/alertes', [EquipeDashboardController::class, 'alertes'])->name('alertes');
    Route::get('/alertes/{id}', [EquipeDashboardController::class, 'alerteDetail'])->name('alerte.detail');
    Route::post('/affectation/{id}/statut', [EquipeDashboardController::class, 'updateStatut'])->name('affectation.statut');
    Route::get('/profil', [EquipeDashboardController::class, 'profil'])->name('profil');
    Route::get('/affectations/stream', [UrgenceController::class, 'streamAffectations'])->name('affectations.stream');
    Route::post('/logout', [ServicesLoginController::class, 'logout'])->name('logout');
});
