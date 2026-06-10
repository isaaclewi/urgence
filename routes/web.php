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

// ═══════════════════════════════════════════════
//  CITOYEN ROUTES
// ═══════════════════════════════════════════════

Route::get('/', [RedirectionController::class, 'accueil'])->name('accueil');
Route::get('/formulaire', [RedirectionController::class, 'formulaire'])->name('formulaire');
Route::get('/compte', [RedirectionController::class, 'compte'])->name('compte');
Route::get('/profil', [RedirectionController::class, 'profil'])->name('profil');
Route::get('/actualites', [RedirectionController::class, 'actualites'])->name('actualites');
Route::get('/bilanSante', [RedirectionController::class, 'bilanSante'])->name('bilanSante');
Route::get('/MesAlertes', [RedirectionController::class, 'MesAlertes'])->name('MesAlertes');
Route::get('/vaccinationMenu', [RedirectionController::class, 'vaccinationMenu'])->name('vaccinationMenu');
Route::get('/avis', [RedirectionController::class, 'avis'])->name('avis');
Route::get('/regle', [RedirectionController::class, 'regle'])->name('regle');

Route::post('/alerte/enregistrer', [RedirectionController::class, 'enregistrerAlerte'])->name('enregistrerAlerte');
Route::post('/citoyens', [CitoyenController::class, 'store'])->name('citoyens.store');

Route::get('/compteController', [CompteController::class, 'index'])->name('compteController');
Route::get('/bilanController', [bilanController::class, 'index'])->name('bilanController');
Route::get('/actualitesController', [actualitesController::class, 'index'])->name('actualitesController');
Route::get('/vaccinationMenuController', [vaccinationMenuController::class, 'index'])->name('vaccinationMenuController');
Route::get('/MesAlertesController', [MesAlertesController::class, 'index'])->name('MesAlertesController');

Route::get('/profil', [profilController::class, 'index'])->name('profilController');
Route::put('/profil/update', [profilController::class, 'update'])->name('profil.update');

Route::get('/avisUsers', [AvisController::class, 'index'])->name('avisUsers.index');
Route::post('/avisUsers', [AvisController::class, 'store'])->name('avisUsers.store');

Route::get('/mes-alertes', [SuiviAlertesController::class, 'index'])->name('SuiviAlertesController');

// Auth citoyen
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'processLogin'])->name('processLogin');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Alertes citoyen
Route::get('/alertePolice', [AlertePoliceController::class, 'index'])->name('alertePolice.index');
Route::post('/alertePolice', [AlertePoliceController::class, 'store'])->name('alertePolice.store');

Route::get('/alertePompier', [AlertePompierController::class, 'index'])->name('alertePompier.index');
Route::post('/alertePompier', [AlertePompierController::class, 'store'])->name('alertePompier.store');

Route::get('/alerteHopital', [AlerteHopitalController::class, 'index'])->name('alerteHopital.index');
Route::post('/alerteHopital', [AlerteHopitalController::class, 'store'])->name('alerteHopital.store');

// Vaccination citoyen
Route::get('/programmes-vaccination', [ProgrammeVaccinController::class, 'index'])->name('programmeVaccinController');
Route::get('/programmes-epidemie', [ProgrammeVaccinEpidemieController::class, 'index'])->name('programmeVaccinEpidemieController');
Route::get('/programmes-vaccination-enfant', [ProgrammeVaccinEnfantController::class, 'index'])->name('programmeVaccinEnfantController');

// QR Code
Route::get('/codeqr', [CodeQRController::class, 'index'])->name('codeQR');
Route::get('/codeqr/download', [CodeQRController::class, 'download'])->name('codeQR.download');

// Forum citoyen
Route::get('/forumCitoyen', [RedirectionController::class, 'forumCitoyen'])->name('forumCitoyen');
Route::get('/groupeDiscussion/{id}', [RedirectionController::class, 'groupeDiscussion'])->name('groupeDiscussion');
Route::post('/groupeDiscussion/{id}/creer', [RedirectionController::class, 'creerGroupe'])->name('creerGroupe');
Route::delete('/forumCitoyen/message/{id}', [RedirectionController::class, 'supprimerMessage'])->name('supprimerMessage');

// ═══════════════════════════════════════════════
//  FORUM GENERAL
// ═══════════════════════════════════════════════

Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/groupe/{id}', [ForumController::class, 'show'])->name('forum.group');
Route::post('/forum/groupe/{id}/send', [ForumController::class, 'sendMessage'])->name('forum.group.send');
Route::delete('/forum/message/{id}', [ForumController::class, 'deleteMessage'])->name('forum.message.delete');

// ═══════════════════════════════════════════════
//  ADMIN ROUTES
// ═══════════════════════════════════════════════

// Redirection admin
Route::get('/admin/accueil', [RedirectionController::class, 'accueilAdmin'])->name('admin.accueil');
Route::get('/admin/compte', [RedirectionController::class, 'admin'])->name('adminCompte');
Route::get('/admin/login', [RedirectionController::class, 'adminLogin'])->name('adminLogin');

// Auth admin
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'processLogin'])->name('admin.login.process');

Route::get('/admin/register', [AdminRegisterController::class, 'create'])->name('admin.register');
Route::post('/admin/register', [AdminRegisterController::class, 'store'])->name('admin.register.store');

// Compte admin
Route::get('/admin/compte', [AdminCompteController::class, 'index'])->name('admin.compte');

// Profil admin
Route::get('/admin/profil', [AdminProfilController::class, 'index'])->name('admin.profil');
Route::get('/admin/profil/edit', [AdminProfilController::class, 'edit'])->name('admin.profil.edit');
Route::post('/admin/profil/update', [AdminProfilController::class, 'update'])->name('admin.profil.update');

// Gestion utilisateurs
Route::get('/admin/users', [GestionUsers::class, 'index'])->name('admin.users');
Route::delete('/admin/users/{id}', [GestionUsers::class, 'destroy'])->name('admin.users.delete');

Route::get('/admin/valider', [ValiderUsers::class, 'index'])->name('admin.valider');
Route::post('/admin/activer/{id}', [ValiderUsers::class, 'activer'])->name('admin.activer');
Route::post('/admin/desactiver/{id}', [ValiderUsers::class, 'desactiver'])->name('admin.desactiver');

// Services d'urgence (adminServicesController)
Route::get('/admin/services', [adminServicesController::class, 'index'])->name('admin.services');
Route::post('/admin/services/urgence', [adminServicesController::class, 'store'])->name('admin.services.store');
Route::delete('/admin/services/urgence/{id}', [adminServicesController::class, 'destroy'])->name('admin.services.destroy');

// Services proposés (ServicesController)
Route::get('admin/service-create', [ServicesController::class, 'index'])->name('admin.serviceCreate');
Route::post('admin/service-store', [ServicesController::class, 'store'])->name('admin.serviceStore');
Route::delete('admin/service-propose/{id}', [ServicesController::class, 'destroy'])->name('admin.serviceDestroy');
Route::get('admin/service-activer/{id}', [ServicesController::class, 'activer'])->name('admin.serviceActiver');
Route::get('admin/service-desactiver/{id}', [ServicesController::class, 'desactiver'])->name('admin.serviceDesactiver');

// Bilans admin
Route::get('/admin/bilans', [AdminBilanController::class, 'index'])->name('admin.bilanSante');
Route::get('/admin/bilan/{id}', [AdminBilanController::class, 'show'])->name('admin.bilan.show');
Route::post('/admin/bilan', [AdminBilanController::class, 'store'])->name('admin.bilan.store');
Route::put('/admin/bilan/{id}', [AdminBilanController::class, 'update'])->name('admin.bilan.update');

// Actualités admin
Route::prefix('admin')->group(function () {
    Route::get('/actualites', [AdminActualiteController::class, 'index'])->name('admin.actualite');
    Route::post('/actualites', [AdminActualiteController::class, 'store'])->name('admin.actualiteStore');
    Route::get('/actualites/{id}/edit', [AdminActualiteController::class, 'edit'])->name('admin.actualiteEdit');
    Route::put('/actualites/{id}', [AdminActualiteController::class, 'update'])->name('admin.actualiteUpdate');
    Route::delete('/actualites/{id}', [AdminActualiteController::class, 'destroy'])->name('admin.actualiteDestroy');
});

// Alertes admin
Route::get('/admin/alertes', [AdminAlertesController::class, 'index'])->name('admin.alertes');

// Espaces de discussion admin
Route::get('/admin/discussion-spaces', [AdminDiscussionSpaceController::class, 'index'])->name('admin.discussion.spaces');
Route::post('/admin/discussion-spaces', [AdminDiscussionSpaceController::class, 'store'])->name('admin.discussion.space.store');
Route::get('/admin/discussion-spaces/toggle/{id}', [AdminDiscussionSpaceController::class, 'toggle'])->name('admin.discussion.space.toggle');
Route::get('/admin/discussion-space/{id}/delete', [AdminDiscussionSpaceController::class, 'destroy'])->name('admin.discussion.space.delete');

// ═══════════════════════════════════════════════
//  SERVICES ROUTES
// ═══════════════════════════════════════════════

Route::get('/services/accueil', [RedirectionController::class, 'accueilService'])->name('services.accueil');

// Auth services
Route::get('/services/login', [ServicesLoginController::class, 'showLoginForm'])->name('services.login');
Route::post('/services/login', [ServicesLoginController::class, 'processLogin'])->name('services.login.process');
Route::get('/services/logout', [ServicesLoginController::class, 'logout'])->name('services.logout');

// Compte & profil services
Route::get('/services/compte', [ServicesCompteController::class, 'index'])->name('services.compte');
Route::post('/services/compte/upload', [ServicesCompteController::class, 'uploadPiece'])->name('services.compte.upload');

Route::middleware(['web'])->group(function () {
    Route::get('/services/profil', [ServicesProfilController::class, 'index'])->name('services.profil');
    Route::post('/services/profil/update', [ServicesProfilController::class, 'update'])->name('services.profil.update');
});

// Citoyens services
Route::get('/services/citoyens', [ServicesUsersController::class, 'index'])->name('services.citoyens');

// Vaccination services
Route::prefix('services')->group(function () {
    Route::get('vaccination', [VaccinationController::class, 'index'])->name('services.vaccinationIndex');
    Route::get('vaccination/create', [VaccinationController::class, 'create'])->name('services.vaccinationCreate');
    Route::post('vaccination/store', [VaccinationController::class, 'store'])->name('services.vaccinationStore');
    Route::get('vaccination/edit/{id}', [VaccinationController::class, 'edit'])->name('services.vaccinationEdit');
    Route::put('vaccination/update/{id}', [VaccinationController::class, 'update'])->name('services.vaccinationUpdate');
    Route::delete('vaccination/destroy/{id}', [VaccinationController::class, 'destroy'])->name('services.vaccinationDestroy');
});

// Vaccination enfants services
Route::prefix('services')->group(function () {
    Route::get('/vaccination-enfants', [ServiceVaccinEnfantController::class, 'index'])->name('services.vaccinationEnfantsIndex');
    Route::get('/vaccination-enfants/create', [ServiceVaccinEnfantController::class, 'create'])->name('services.vaccinationEnfantsCreate');
    Route::post('/vaccination-enfants/store', [ServiceVaccinEnfantController::class, 'store'])->name('services.vaccinationEnfantsStore');
    Route::get('/vaccination-enfants/{id}/edit', [ServiceVaccinEnfantController::class, 'edit'])->name('services.vaccinationEnfantsEdit');
    Route::put('/vaccination-enfants/{id}', [ServiceVaccinEnfantController::class, 'update'])->name('services.vaccinationEnfantsUpdate');
    Route::delete('/vaccination-enfants/{id}', [ServiceVaccinEnfantController::class, 'destroy'])->name('services.vaccinationEnfantsDestroy');
});

// Bilan services
Route::get('services/bilan', [ServicesBilanController::class, 'index'])->name('services.citoyensBilan');
Route::post('services/bilan/store', [ServicesBilanController::class, 'store'])->name('services.citoyensBilanStore');
Route::delete('services/bilan/destroy/{id}', [ServicesBilanController::class, 'destroy'])->name('services.citoyensBilanDestroy');

// Urgences services
Route::get('/services/urgences', [UrgenceController::class, 'index'])->name('services.urgenceSignalee');
Route::post('/services/urgences/{id}/update', [UrgenceController::class, 'updateStatut'])->name('services.urgenceSignaleeUpdate');
Route::post('/services/urgence/affecter/{id}', [UrgenceController::class, 'affecter'])->name('services.urgence.affecter');
Route::get('/services/interventions-journalieres', [UrgenceController::class, 'interventionsJournalieres'])->name('services.interventions.journalieres');

// Actualités services
Route::get('/actualites', [ActualiteController::class, 'index'])->name('services.actualite');
Route::post('/actualites', [ActualiteController::class, 'store'])->name('services.actualiteStore');
Route::get('/actualites/{id}/edit', [ActualiteController::class, 'edit'])->name('services.actualiteEdit');
Route::put('/actualites/{id}', [ActualiteController::class, 'update'])->name('services.actualiteUpdate');
Route::delete('/actualites/{id}', [ActualiteController::class, 'destroy'])->name('services.actualiteDestroy');

// Forum services
Route::get('/services/forum', [ForumServicesController::class, 'index'])->name('services.forum.index');
Route::get('/services/forum/groupe/{id}', [ForumServicesController::class, 'show'])->name('services.forum.group');
Route::post('/services/forum/groupe/{id}/send', [ForumServicesController::class, 'sendMessage'])->name('services.forum.group.send');
Route::delete('/services/forum/message/{id}', [ForumServicesController::class, 'deleteMessage'])->name('services.forum.message.delete');

// ═══════════════════════════════════════════════
//  EQUIPE ROUTES
// ═══════════════════════════════════════════════

Route::prefix('equipe')->name('equipe.')->group(function () {
    Route::get('/dashboard', [EquipeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/alertes', [EquipeDashboardController::class, 'alertes'])->name('alertes');
    Route::get('/alertes/{id}', [EquipeDashboardController::class, 'alerteDetail'])->name('alerte.detail');
    Route::post('/affectation/{id}/statut', [EquipeDashboardController::class, 'updateStatut'])->name('affectation.statut');
    Route::get('/profil', [EquipeDashboardController::class, 'profil'])->name('profil');
    Route::post('/logout', [ServicesLoginController::class, 'logout'])->name('logout');
});

// ═══════════════════════════════════════════════
//  EQUIPES (ServiceEquipeController)
// ═══════════════════════════════════════════════

Route::prefix('services/equipes')->group(function () {
    Route::get('/', [ServiceEquipeController::class, 'index'])->name('services.equipes.index');
    Route::get('/create', [ServiceEquipeController::class, 'create'])->name('services.equipes.create');
    Route::post('/', [ServiceEquipeController::class, 'store'])->name('services.equipes.store');
    Route::get('/{id}/edit', [ServiceEquipeController::class, 'edit'])->name('services.equipes.edit');
    Route::put('/{id}', [ServiceEquipeController::class, 'update'])->name('services.equipes.update');
    Route::delete('/{id}', [ServiceEquipeController::class, 'destroy'])->name('services.equipes.destroy');
});

// Dans la section SERVICES
Route::get('/services/urgences/stream', [UrgenceController::class, 'stream'])
    ->name('services.urgences.stream');

// Dans la section EQUIPE
Route::get('/equipe/affectations/stream', [UrgenceController::class, 'streamAffectations'])
    ->name('equipe.affectations.stream');
