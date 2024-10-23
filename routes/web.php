<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::group(['middleware' => ['auth']], function() {

    Route::resource('users', Controllers\UserController::class);
    Route::get('/roles', [Controllers\RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [Controllers\RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [Controllers\RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{roles}/edit', [Controllers\RoleController::class, 'edit'])->name('roles.edit');
    Route::patch('/roles/{roles}', [Controllers\RoleController::class, 'update'])->name('roles.update');
    //Route::get('/roles/{roles}', [Controllers\RoleController::class, 'show'])->name('roles.show');
    Route::get('/dashboard', [Controllers\HomeController::class, 'latestDashboard'])->name('dashboard');

    //Change Password
    Route::get('/user/changePassword', [Controllers\UserController::class, 'showChangePasswordForm'])->name('changePassword');
    Route::post('/user/changePassword', [Controllers\UserController::class, 'changePassword'])->name('change.password');

    //Twilio Settings
    Route::get('/twilio', [Controllers\TwilioController::class, 'index'])->name('twilio.index');
    Route::get('/twilio/edit/{id}', [Controllers\TwilioController::class, 'editSettingsForm'])->name('twilio.showEdit');
    Route::post('/twilio/edit/{id}', [Controllers\TwilioController::class, 'updateSettings'])->name('twilio.edit');
    Route::get('/twilio/sendTestSms', [Controllers\TwilioController::class, 'sendTestTwilioSmsForm'])->name('twilio.sendTestSms');
    Route::post('/twilio/sendTestSms', [Controllers\TwilioController::class, 'sendTestTwilioSms'])->name('twilio.sendSms');

    Route::post('/receive', [Controllers\TwilioController::class, 'receive'])->name('twilio.receive');

    //Participating Event
    Route::get('/events', [Controllers\ParticipatingEventController::class, 'index'])->name('event.index');
    Route::get('/event/add', [Controllers\ParticipatingEventController::class, 'showAddForm'])->name('event.showAdd');
    Route::post('/event/add', [Controllers\ParticipatingEventController::class, 'addForm'])->name('event.add');
    Route::get('/event/status/{id}/{status}', [Controllers\ParticipatingEventController::class, 'enableDisableEvent'])->name('event.status');
    Route::get('/event/edit/{id}', [Controllers\ParticipatingEventController::class, 'showEditForm'])->name('event.showEdit');
    Route::post('/event/edit/{id}', [Controllers\ParticipatingEventController::class, 'editForm'])->name('event.edit');
    Route::get('/event/delete/{id}', [Controllers\ParticipatingEventController::class, 'deleteEvent'])->name('event.delete');

    //Participants
    Route::get('/participants/{event_id?}', [Controllers\ParticipantController::class, 'index'])->name('participant.index');
    Route::get('/participant/add', [Controllers\ParticipantController::class, 'showAddForm'])->name('participant.showAdd');
    Route::post('/participant/add', [Controllers\ParticipantController::class, 'addForm'])->name('participant.add');
    Route::get('/participant/edit/{id}', [Controllers\ParticipantController::class, 'showEditForm'])->name('participant.showEdit');
    Route::post('/participant/edit/{id}', [Controllers\ParticipantController::class, 'editForm'])->name('participant.edit');
    Route::get('/participant/delete/{id}', [Controllers\ParticipantController::class, 'deleteEvent'])->name('participant.delete');
    Route::get('/participant/import', [Controllers\ParticipantController::class, 'importParticipantsForm'])->name('participant.showImport');
    Route::post('/participant/import', [Controllers\ParticipantController::class, 'importParticipants'])->name('participant.import');

    //Couples
    Route::get('/couples/list', [Controllers\CoupleController::class, 'couplesListing'])->name('couples.list');
    Route::get('/couples/create/{event_id}', [Controllers\CoupleController::class, 'generateCouples'])->name('couples.create');

});

require __DIR__.'/auth.php';

//Clear all caches
Route::get('/cipher-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return '<h1>Cache facade value cleared</h1><br><h1>Çonfig cache cleared</h1><br><h1>View cache cleared</h1><br><h1>Route cache cleared</h1>';
});
