<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\RessourceController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ServiceEquipeController;
use App\Http\Controllers\ServicesProfilController;
use App\Http\Controllers\ServicesBilanController;

/*
|--------------------------------------------------------------------------
| Modules ERP — portail Services
|--------------------------------------------------------------------------
| Chargé depuis routes/web.php (require __DIR__.'/erp.php';) donc dans le
| groupe « web » (session, CSRF).
*/
Route::middleware(['auth.service', 'no.cache'])->prefix('services')->name('services.')->group(function () {

    // Changement de mot de passe du compte service
    Route::post('/profil/password', [ServicesProfilController::class, 'password'])->name('profil.password');

    // Bilans de santé : création, consultation (journalisée), modification
    // (la liste, l'enregistrement et la suppression restent dans routes/web.php)
    Route::get('/citoyens/bilan/nouveau',        [ServicesBilanController::class, 'create'])->name('citoyensBilanCreate');
    Route::get('/citoyens/bilan/{id}',           [ServicesBilanController::class, 'show'])->whereNumber('id')->name('citoyensBilanShow');
    Route::get('/citoyens/bilan/{id}/modifier',  [ServicesBilanController::class, 'edit'])->whereNumber('id')->name('citoyensBilanEdit');
    Route::put('/citoyens/bilan/{id}',           [ServicesBilanController::class, 'update'])->whereNumber('id')->name('citoyensBilanUpdate');

    // Tableau de bord KPI
    Route::get('/indicateurs', [KpiController::class, 'index'])->name('kpi');

    // Rapports & exports (PDF / Excel)
    Route::prefix('exports')->name('exports.')->group(function () {
        Route::get('/', [ExportController::class, 'index'])->name('index');
        Route::get('/personnel/{format}',     [ExportController::class, 'personnel'])->whereIn('format', ['pdf', 'xlsx'])->name('personnel');
        Route::get('/vehicules/{format}',     [ExportController::class, 'vehicules'])->whereIn('format', ['pdf', 'xlsx'])->name('vehicules');
        Route::get('/stocks/{format}',        [ExportController::class, 'stocks'])->whereIn('format', ['pdf', 'xlsx'])->name('stocks');
        Route::get('/interventions/{format}', [ExportController::class, 'interventions'])->whereIn('format', ['pdf', 'xlsx'])->name('interventions');
        Route::get('/kpi',                    [ExportController::class, 'kpi'])->name('kpi');
        Route::get('/intervention/{id}',      [ExportController::class, 'intervention'])->whereNumber('id')->name('intervention');
    });

    // Équipes : désactiver / réactiver (les autres routes équipes sont dans web.php)
    Route::post('/equipes/{id}/statut', [ServiceEquipeController::class, 'statut'])->whereNumber('id')->name('equipes.statut');

    // Personnel
    Route::get('/personnel',               [PersonnelController::class, 'index'])->name('personnel.index');
    Route::get('/personnel/create',        [PersonnelController::class, 'create'])->name('personnel.create');
    Route::post('/personnel',              [PersonnelController::class, 'store'])->name('personnel.store');
    Route::get('/personnel/{id}/edit',     [PersonnelController::class, 'edit'])->whereNumber('id')->name('personnel.edit');
    Route::put('/personnel/{id}',          [PersonnelController::class, 'update'])->whereNumber('id')->name('personnel.update');
    Route::delete('/personnel/{id}',       [PersonnelController::class, 'destroy'])->whereNumber('id')->name('personnel.destroy');

    // Planning des gardes
    Route::get('/personnel/gardes',        [PersonnelController::class, 'gardes'])->name('personnel.gardes');
    Route::post('/personnel/gardes',       [PersonnelController::class, 'storeGarde'])->name('personnel.gardes.store');
    Route::delete('/personnel/gardes/{id}',[PersonnelController::class, 'destroyGarde'])->whereNumber('id')->name('personnel.gardes.destroy');

    // Ressources : tableau de bord + véhicules + stocks
    Route::get('/ressources',                       [RessourceController::class, 'index'])->name('ressources.index');

    Route::get('/ressources/vehicules/create',      [RessourceController::class, 'createVehicule'])->name('ressources.vehicules.create');
    Route::post('/ressources/vehicules',            [RessourceController::class, 'storeVehicule'])->name('ressources.vehicules.store');
    Route::get('/ressources/vehicules/{id}/edit',   [RessourceController::class, 'editVehicule'])->whereNumber('id')->name('ressources.vehicules.edit');
    Route::put('/ressources/vehicules/{id}',        [RessourceController::class, 'updateVehicule'])->whereNumber('id')->name('ressources.vehicules.update');
    Route::delete('/ressources/vehicules/{id}',     [RessourceController::class, 'destroyVehicule'])->whereNumber('id')->name('ressources.vehicules.destroy');

    Route::get('/ressources/stocks/create',         [RessourceController::class, 'createStock'])->name('ressources.stocks.create');
    Route::post('/ressources/stocks',               [RessourceController::class, 'storeStock'])->name('ressources.stocks.store');
    Route::get('/ressources/stocks/{id}/edit',      [RessourceController::class, 'editStock'])->whereNumber('id')->name('ressources.stocks.edit');
    Route::put('/ressources/stocks/{id}',           [RessourceController::class, 'updateStock'])->whereNumber('id')->name('ressources.stocks.update');
    Route::delete('/ressources/stocks/{id}',        [RessourceController::class, 'destroyStock'])->whereNumber('id')->name('ressources.stocks.destroy');
});
