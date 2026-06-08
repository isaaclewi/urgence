<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectionController;


use App\Http\Controllers\ForumController;

Route::delete('/forum/message/{id}', [ForumController::class, 'deleteMessage'])->name('forum.message.delete');




Route::get('/forum', [ForumController::class, 'index'])
    ->name('forum.index');

Route::get('/forum/groupe/{id}', [ForumController::class, 'show'])
    ->name('forum.group');

Route::post('/forum/groupe/{id}/send', [ForumController::class, 'sendMessage'])
    ->name('forum.group.send');


use App\Http\Controllers\ForumServicesController;

Route::get('/services/forum', [ForumServicesController::class, 'index'])
    ->name('services.forum.index');

Route::get('/services/forum/groupe/{id}', [ForumServicesController::class, 'show'])
    ->name('services.forum.group');

Route::post('/services/forum/groupe/{id}/send', [ForumServicesController::class, 'sendMessage'])
    ->name('services.forum.group.send');

Route::delete('/services/forum/message/{id}', [ForumServicesController::class, 'deleteMessage'])->name('services.forum.message.delete');
//CYTOYEN ROUTES

Route::get('/groupeDiscussion/{id}', [RedirectionController::class, 'groupeDiscussion'])->name('groupeDiscussion');

Route::post('/groupeDiscussion/{id}/creer', [RedirectionController::class, 'creerGroupe'])->name('creerGroupe');

Route::delete('/forumCitoyen/message/{id}', [RedirectionController::class, 'supprimerMessage'])->name('supprimerMessage');


Route::get('/forumCitoyen', [RedirectionController::class, 'forumCitoyen'])->name('forumCitoyen');

Route::post('/alerte/enregistrer', [RedirectionController::class, 'enregistrerAlerte'])->name('enregistrerAlerte');

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

use App\Http\Controllers\CitoyenController;

Route::post('/citoyens', [CitoyenController::class, 'store'])->name('citoyens.store');

use App\Http\Controllers\compteController;

Route::get('/compteController', [CompteController::class, 'index'])->name('compteController');

use App\Http\Controllers\bilanController;

Route::get('/bilanController', [bilanController::class, 'index'])->name('bilanController');

use App\Http\Controllers\actualitesController;

Route::get('/actualitesController', [actualitesController::class, 'index'])->name('actualitesController');

use App\Http\Controllers\vaccinationMenuController;

Route::get('/vaccinationMenuController', [vaccinationMenuController::class, 'index'])->name('vaccinationMenuController');

use App\Http\Controllers\MesAlertesController;

Route::get('/MesAlertesController', [MesAlertesController::class, 'index'])->name('MesAlertesController');

use App\Http\Controllers\profilController;

Route::get('/profil', [profilController::class, 'index'])->name('profilController');
Route::put('/profil/update', [profilController::class, 'update'])->name('profil.update');

use App\Http\Controllers\AvisController;

Route::get('/avisUsers', [AvisController::class, 'index'])->name('avisUsers.index');
Route::post('/avisUsers', [AvisController::class, 'store'])->name('avisUsers.store');

use App\Http\Controllers\AuthController;

// Page login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Traitement login
Route::post('/login', [AuthController::class, 'processLogin'])->name('processLogin');

// Déconnexion
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\AlertePoliceController;

Route::get('/alertePolice', [AlertePoliceController::class, 'index'])->name('alertePolice.index');
Route::post('/alertePolice', [AlertePoliceController::class, 'store'])->name('alertePolice.store');

use App\Http\Controllers\AlertePompierController;

Route::get('/alertePompier', [AlertePompierController::class, 'index'])->name('alertePompier.index');
Route::post('/alertePompier', [AlertePompierController::class, 'store'])->name('alertePompier.store');

use App\Http\Controllers\AlerteHopitalController;

Route::get('/alerteHopital', [AlerteHopitalController::class, 'index'])->name('alerteHopital.index');
Route::post('/alerteHopital', [AlerteHopitalController::class, 'store'])->name('alerteHopital.store');

use App\Http\Controllers\ProgrammeVaccinController;

Route::get('/programmes-vaccination', [ProgrammeVaccinController::class, 'index'])
    ->name('programmeVaccinController');

use App\Http\Controllers\ProgrammeVaccinEpidemieController;

Route::get('/programmes-epidemie', [ProgrammeVaccinEpidemieController::class, 'index'])
    ->name('programmeVaccinEpidemieController');

use App\Http\Controllers\ProgrammeVaccinEnfantController;

Route::get('/programmes-vaccination-enfant', [ProgrammeVaccinEnfantController::class, 'index'])
    ->name('programmeVaccinEnfantController');

use App\Http\Controllers\CodeQRController;

Route::get('/codeqr', [CodeQRController::class, 'index'])->name('codeQR');
Route::get('/codeqr/download', [CodeQRController::class, 'download'])->name('codeQR.download');

use App\Http\Controllers\SuiviAlertesController;

Route::get('/mes-alertes', [SuiviAlertesController::class, 'index'])->name('SuiviAlertesController');





//ADMIN ROUTES

Route::get('/admin/accueil', [RedirectionController::class, 'accueilAdmin'])->name('admin.accueil');

Route::get('/admin/compte', [RedirectionController::class, 'admin'])->name('adminCompte');

Route::get('/admin/login', [RedirectionController::class, 'adminLogin'])->name('adminLogin');

use App\Http\Controllers\AdminLoginController;

Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'processLogin'])->name('admin.login.process');

use App\Http\Controllers\AdminRegisterController;

Route::get('/admin/register', [AdminRegisterController::class, 'create'])->name('admin.register');
Route::post('/admin/register', [AdminRegisterController::class, 'store'])->name('admin.register.store');

use App\Http\Controllers\AdminCompteController;

Route::get('/admin/compte', [AdminCompteController::class, 'index'])->name('admin.compte');

use App\Http\Controllers\adminServicesController;

Route::get('/admin/services', [adminServicesController::class, 'index'])->name('admin.services');
Route::post('/admin/services', [adminServicesController::class, 'store'])->name('admin.services.store');
Route::delete('/admin/services/{id}/delete', [adminServicesController::class, 'destroy'])->name('admin.services.destroy');

use App\Http\Controllers\AdminBilanController;

// Liste des bilans
Route::get('/admin/bilans', [AdminBilanController::class, 'index'])->name('admin.bilanSante');

// Afficher le formulaire de bilan d’un citoyen
Route::get('/admin/bilan/{id}', [AdminBilanController::class, 'show'])->name('admin.bilan.show');

// Enregistrer un nouveau bilan
Route::post('/admin/bilan', [AdminBilanController::class, 'store'])->name('admin.bilan.store');

// Mettre à jour un bilan existant
Route::put('/admin/bilan/{id}', [AdminBilanController::class, 'update'])->name('admin.bilan.update');

use App\Http\Controllers\AdminProfilController;

// Page profil (affichage)
Route::get('/admin/profil', [AdminProfilController::class, 'index'])->name('admin.profil');

// Page modification profil (edit)
Route::get('/admin/profil/edit', [AdminProfilController::class, 'edit'])->name('admin.profil.edit');
Route::post('/admin/profil/update', [AdminProfilController::class, 'update'])->name('admin.profil.update');

use App\Http\Controllers\GestionUsers;

Route::get('/admin/users', [GestionUsers::class, 'index'])->name('admin.users');
// Suppression d’un citoyen par l’administrateur
Route::delete('/admin/users/{id}', [App\Http\Controllers\GestionUsers::class, 'destroy'])
    ->name('admin.users.delete');


use App\Http\Controllers\ValiderUsers;

Route::get('/admin/valider', [ValiderUsers::class, 'index'])->name('admin.valider');
Route::post('/admin/activer/{id}', [ValiderUsers::class, 'activer'])->name('admin.activer');
Route::post('/admin/desactiver/{id}', [ValiderUsers::class, 'desactiver'])->name('admin.desactiver');

use App\Http\Controllers\ServicesController;

Route::get('admin/service-create', [ServicesController::class, 'index'])->name('admin.serviceCreate');
Route::post('admin/service-store', [ServicesController::class, 'store'])->name('admin.serviceStore');
Route::get('admin/service-activer/{id}', [ServicesController::class, 'activer'])->name('admin.serviceActiver');
Route::get('admin/service-desactiver/{id}', [ServicesController::class, 'desactiver'])->name('admin.serviceDesactiver');

use App\Http\Controllers\AdminActualiteController;

Route::prefix('admin')->group(function () {
    Route::get('/actualites', [AdminActualiteController::class,'index'])->name('admin.actualite');
    Route::post('/actualites', [AdminActualiteController::class,'store'])->name('admin.actualiteStore');
    Route::get('/actualites/{id}/edit', [AdminActualiteController::class,'edit'])->name('admin.actualiteEdit');
    Route::put('/actualites/{id}', [AdminActualiteController::class,'update'])->name('admin.actualiteUpdate');
    Route::delete('/actualites/{id}', [AdminActualiteController::class,'destroy'])->name('admin.actualiteDestroy');
});

use App\Http\Controllers\AdminAlertesController;

Route::get('/admin/alertes', [AdminAlertesController::class, 'index'])->name('admin.alertes');
use App\Http\Controllers\AdminDiscussionSpaceController;

Route::get('/admin/discussion-spaces', [AdminDiscussionSpaceController::class, 'index'])
    ->name('admin.discussion.spaces');

Route::post('/admin/discussion-spaces', [AdminDiscussionSpaceController::class, 'store'])
    ->name('admin.discussion.space.store');

Route::get('/admin/discussion-spaces/toggle/{id}', [AdminDiscussionSpaceController::class, 'toggle'])
    ->name('admin.discussion.space.toggle');
Route::get('/admin/discussion-space/{id}/delete', [AdminDiscussionSpaceController::class, 'destroy'])
    ->name('admin.discussion.space.delete');






//SERVICES ROUTES

Route::get('/services/accueil', [RedirectionController::class, 'accueilService'])->name('services.accueil');

use App\Http\Controllers\ServicesCompteController;

Route::get('/services/compte', [ServicesCompteController::class, 'index'])->name('services.compte');

use App\Http\Controllers\ServicesLoginController;
// Formulaire de connexion
Route::get('/services/login', [ServicesLoginController::class, 'showLoginForm'])
    ->name('services.login');

// Traitement du formulaire
Route::post('/services/login', [ServicesLoginController::class, 'processLogin'])
    ->name('services.login.process');

// Déconnexion
Route::get('/services/logout', [ServicesLoginController::class, 'logout'])
    ->name('services.logout');

use App\Http\Controllers\ServicesProfilController;

Route::middleware(['web'])->group(function () {
    Route::get('/services/profil', [ServicesProfilController::class, 'index'])->name('services.profil');
    Route::post('/services/profil/update', [ServicesProfilController::class, 'update'])->name('services.profil.update');
});

use App\Http\Controllers\ServicesUsersController;

Route::get('/services/citoyens', [ServicesUsersController::class, 'index'])
    ->name('services.citoyens');

    Route::post('/services/compte/upload', [ServicesCompteController::class, 'uploadPiece'])
    ->name('services.compte.upload');

use App\Http\Controllers\VaccinationController;

Route::prefix('services')->group(function () {
    Route::get('vaccination', [VaccinationController::class, 'index'])->name('services.vaccinationIndex');
    Route::get('vaccination/create', [VaccinationController::class, 'create'])->name('services.vaccinationCreate');
    Route::post('vaccination/store', [VaccinationController::class, 'store'])->name('services.vaccinationStore');
    Route::get('vaccination/edit/{id}', [VaccinationController::class, 'edit'])->name('services.vaccinationEdit');
    Route::put('vaccination/update/{id}', [VaccinationController::class, 'update'])->name('services.vaccinationUpdate');
    Route::delete('vaccination/destroy/{id}', [VaccinationController::class, 'destroy'])->name('services.vaccinationDestroy');
});

use App\Http\Controllers\ServicesBilanController;

Route::get('services/bilan', [ServicesBilanController::class, 'index'])->name('services.citoyensBilan');
Route::post('services/bilan/store', [ServicesBilanController::class, 'store'])->name('services.citoyensBilanStore');
Route::delete('services/bilan/destroy/{id}', [ServicesBilanController::class, 'destroy'])->name('services.citoyensBilanDestroy');

use App\Http\Controllers\ServiceVaccinEnfantController;

Route::prefix('services')->group(function () {
    Route::get('/vaccination-enfants', [ServiceVaccinEnfantController::class, 'index'])->name('services.vaccinationEnfantsIndex');
    Route::get('/vaccination-enfants/create', [ServiceVaccinEnfantController::class, 'create'])->name('services.vaccinationEnfantsCreate');
    Route::post('/vaccination-enfants/store', [ServiceVaccinEnfantController::class, 'store'])->name('services.vaccinationEnfantsStore');
    Route::get('/vaccination-enfants/{id}/edit', [ServiceVaccinEnfantController::class, 'edit'])->name('services.vaccinationEnfantsEdit');
    Route::put('/vaccination-enfants/{id}', [ServiceVaccinEnfantController::class, 'update'])->name('services.vaccinationEnfantsUpdate');
    Route::delete('/vaccination-enfants/{id}', [ServiceVaccinEnfantController::class, 'destroy'])->name('services.vaccinationEnfantsDestroy');
});


use App\Http\Controllers\UrgenceController;

Route::get('/services/urgences', [UrgenceController::class, 'index'])->name('services.urgenceSignalee');
Route::post('/services/urgences/{id}/update', [UrgenceController::class, 'updateStatut'])->name('services.urgenceSignaleeUpdate');


use App\Http\Controllers\ActualiteController;

Route::get('/actualites', [ActualiteController::class, 'index'])->name('services.actualite');
Route::post('/actualites', [ActualiteController::class, 'store'])->name('services.actualiteStore');
Route::get('/actualites/{id}/edit', [ActualiteController::class, 'edit'])->name('services.actualiteEdit');
Route::put('/actualites/{id}', [ActualiteController::class, 'update'])->name('services.actualiteUpdate');
Route::delete('/actualites/{id}', [ActualiteController::class, 'destroy'])->name('services.actualiteDestroy');
