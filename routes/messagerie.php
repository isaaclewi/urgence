<?php

use App\Http\Controllers\MessagerieAdminController;
use App\Http\Controllers\MessagerieController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Messagerie — mêmes écrans, trois portails
|--------------------------------------------------------------------------
| Chargé depuis routes/web.php (require __DIR__.'/messagerie.php';).
| Le portail est reconnu par le préfixe du nom de route (admin. / services. / citoyen.).
*/

$portails = [
    'admin'    => ['prefix' => 'admin/messagerie',    'middleware' => 'auth.admin'],
    'services' => ['prefix' => 'services/messagerie', 'middleware' => 'auth.service'],
    'citoyen'  => ['prefix' => 'messagerie',          'middleware' => 'auth.citoyen'],
];

foreach ($portails as $nom => $cfg) {
    Route::middleware([$cfg['middleware'], 'no.cache'])
        ->prefix($cfg['prefix'])
        ->name($nom . '.messagerie.')
        ->group(function () use ($nom) {

            // Gestion des groupes : administration uniquement (déclarée avant /{id})
            if ($nom === 'admin') {
                Route::get('/gestion',                          [MessagerieAdminController::class, 'index'])->name('gestion');
                Route::post('/gestion',                         [MessagerieAdminController::class, 'store'])->name('gestion.store');
                Route::put('/gestion/{id}',                     [MessagerieAdminController::class, 'update'])->whereNumber('id')->name('gestion.update');
                Route::post('/gestion/{id}/archiver',           [MessagerieAdminController::class, 'toggle'])->whereNumber('id')->name('gestion.toggle');
                Route::delete('/gestion/{id}',                  [MessagerieAdminController::class, 'destroy'])->whereNumber('id')->name('gestion.destroy');
                Route::get('/gestion/{id}/membres',             [MessagerieAdminController::class, 'membres'])->whereNumber('id')->name('membres');
                Route::post('/gestion/{id}/membres',            [MessagerieAdminController::class, 'ajouterMembres'])->whereNumber('id')->name('membres.add');
                Route::put('/gestion/{id}/membres/{membreId}',  [MessagerieAdminController::class, 'modifierMembre'])->whereNumber(['id', 'membreId'])->name('membres.update');
                Route::delete('/gestion/{id}/membres/{membreId}', [MessagerieAdminController::class, 'retirerMembre'])->whereNumber(['id', 'membreId'])->name('membres.remove');
            }

            Route::get('/',                          [MessagerieController::class, 'index'])->name('index');
            Route::get('/{id}',                      [MessagerieController::class, 'show'])->whereNumber('id')->name('show');
            Route::get('/{id}/messages',             [MessagerieController::class, 'poll'])->whereNumber('id')->name('poll');
            Route::post('/{id}/messages',            [MessagerieController::class, 'send'])->whereNumber('id')->name('send');
            Route::put('/{id}/messages/{messageId}', [MessagerieController::class, 'update'])->whereNumber(['id', 'messageId'])->name('update');
            Route::delete('/{id}/messages/{messageId}', [MessagerieController::class, 'destroy'])->whereNumber(['id', 'messageId'])->name('destroy');

            if ($nom === 'services') {
                Route::post('/{id}/acces', [MessagerieController::class, 'demanderAcces'])->whereNumber('id')->name('acces');
            }
        });
}

/*
| Anciennes adresses : conservées (les anciens menus s'en servent encore) mais
| redirigées vers la nouvelle messagerie.
*/
Route::redirect('/forumCitoyen',                '/messagerie')->name('forumCitoyen');
Route::redirect('/groupeDiscussion/{id}',       '/messagerie/{id}')->name('groupeDiscussion');
Route::redirect('/admin/forum',                 '/admin/messagerie')->name('forum.index');
Route::redirect('/admin/forum/{id}',            '/admin/messagerie/{id}')->name('forum.group');
Route::redirect('/admin/discussion-spaces',     '/admin/messagerie/gestion')->name('admin.discussion.spaces');
Route::redirect('/services/forum',              '/services/messagerie')->name('services.forum.index');
Route::redirect('/services/forum/{id}',         '/services/messagerie/{id}')->name('services.forum.group');
