<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\HubSpotAuthController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CRMFeatureController;




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
Route::get('/hubspot/callback', [HubSpotAuthController::class, 'handleHubSpotCallback']);

# upload hubspot contact
Route::get('/upload-contact-form', [ContactController::class, 'showForm'])->name('upload-contact-form');
Route::post('/upload-contact', [ContactController::class, 'uploadContact'])->name('upload-contact');

# process webhook action
Route::post('/hubspot/contact', [WebhookController::class, 'webhookProcess']);

# associate contact with company
Route::post('/hubspot/add-association', [ContactController::class, 'ccAssociation']);

# search objects
Route::post('/hubspot/search', [CRMFeatureController::class, 'searchRequest']);

# create custom object
Route::post('/hubspot/custom', [CRMFeatureController::class, 'customObject']);



