<?php

use App\Http\Controllers\FondsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IdentifiantsController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Kreait\Firebase\Factory;

use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', fn() =>response(['ok' =>true]));


Route::get('/dashboard', [IdentifiantsController::class, 'dashboard'])
    ->middleware(['auth', 'verified', 'otp'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/identifiants', [IdentifiantsController::class, 'create'])->name('identifiants');
    Route::get('/identifiants-create', [IdentifiantsController::class, 'identifants_create'])->name('identifiants-create');
    Route::post('/store-identifiants', [IdentifiantsController::class,'store'])->name('identifiants.store');
    Route::get('/fond-blanc', [IdentifiantsController::class, 'create'])->name('fond-blanc');
    Route::get('/fond-noir', [IdentifiantsController::class, 'creation'])->name('fond-noir');
    Route::get('/identifiants', [IdentifiantsController::class, 'showIdentifiants'])->name('identifiants');
    Route::get('/identifiants', [IdentifiantsController::class, 'afficherIdentifiants'])->name('identifiants');
    Route::get('/messages_create', [IdentifiantsController::class, 'messages_create'])->name('messages_create');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
    Route::get('/identifiants/{id}/edit', [IdentifiantsController::class, 'edit'])->name('identifiants.edit');
    Route::put('/identifiants/{id}', [IdentifiantsController::class, 'update'])->name('identifiants.update');
    Route::delete('/identifiants/{id}', [IdentifiantsController::class, 'destroy_identifiants'])->name('identifiants.destroy');
    Route::get('/identifiants/{id}/print', [IdentifiantsController::class, 'imprimer'])->name('identifiants.imprimer');
   Route::get('/exporter-identifiants', [IdentifiantsController::class, 'exporter'])->name('identifiants.exporter');
   Route::get('/extension-token', [IdentifiantsController::class, 'getToken']);
   Route::get('/identifiants/import-excel', [IdentifiantsController::class, 'showImportPage'])->name('identifiants.import.page');
   Route::post('/identifiants/import-excel', [IdentifiantsController::class, 'import'])->name('identifiants.import.excel');
   Route::get('/historique', [IdentifiantsController::class, 'index'])->name('historique.index');
 Route::get('/generateur-mot-de-passe', [IdentifiantsController::class, 'generatorindex'])->name('password.generator');
Route::post('/mot-de-passe/genere', [IdentifiantsController::class, 'motDePasseGenere'])->name('mot_de_passe.genere');
Route::get('/logs', [IdentifiantsController::class, 'index_logs'])->name('logs.index');
Route::get('/test-otp', [\App\Http\Controllers\Auth\OtpNotificationController::class, 'send']);
Route::get('/securite_test', [IdentifiantsController::class, 'securite_test'])->name('securite_test');
Route::post('/securite', [IdentifiantsController::class, 'tester'])->name('securite.test');
});


 Route::get('/identifiant', [IdentifiantsController::class, 'parSite']);



require __DIR__.'/auth.php';
