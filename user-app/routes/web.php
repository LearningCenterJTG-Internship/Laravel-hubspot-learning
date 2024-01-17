<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\HubSpotAuthController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CRMFeatureController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\CRMCardController;
use App\Http\Controllers\timelineController;
use App\Http\Controllers\noteController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

# receive token
Route::get('/hubspot/auth', [HubSpotAuthController::class, 'redirectToHubSpot']);
Route::get('/hubspot/callback', [HubSpotAuthController::class, 'handleHubSpotCallback']);#->middleware('hubspot.auth');
Route::get('/hubspot/token', [HubSpotAuthController::class, 'getToken']);

# save authorized user
Route::get('/user-auth', [UserAuthController::class, 'showUserForm'])->name('user-auth');
Route::get('/user-save', [UserAuthController::class, 'saveUser'])->name('user-save');

# upload hubspot contact
Route::get('/upload-contact-form', [ContactController::class, 'showForm'])->name('upload-contact-form');
Route::post('/upload-contact', [ContactController::class, 'uploadContact'])->name('upload-contact');

# process webhook action
Route::post('/hubspot/webhook', [WebhookController::class, 'webhookProcess']);

# associate contact with company
Route::post('/hubspot/add-association', [ContactController::class, 'ccAssociation']);

# search objects
Route::post('/hubspot/search', [CRMFeatureController::class, 'searchRequest']);

# create custom object
Route::post('/hubspot/custom', [CRMFeatureController::class, 'customObject']);

# create CRM cards
Route::post('/hubspot/cards', [CRMFeatureController::class, 'createCard']);

# fetch CRM cards
Route::get('/hubspot/fetch-cards', [CRMCardController::class, 'fetchSample']);

# timeline template
Route::post('/template', [timelineController::class, 'createTemplate']);
Route::get('/retrieve-template', [timelineController::class, 'retrieveTemplate']);
Route::post('/template-token', [timelineController::class, 'createToken']);
Route::get('/template-token-retrieve', [timelineController::class, 'retrieveTimelineToken']);
Route::post('/event-create', [timelineController::class, 'createEvent']);

# note
Route::post('/note-create', [noteController::class, 'createNote']);

