<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\ShowSelectionController;
use Illuminate\Support\Facades\Route;

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

include 'moonshine.php';

Route::prefix('contact')->as('contact.')->group(function () {
    Route::get('/', [ContactController::class, 'contactForm'])->name('form');
    Route::post('/', [ContactController::class, 'createTicket'])->name('create-ticket');
});


Route::get('selection/{uniqueId}', ShowSelectionController::class)->name('selection.show');

Route::get('/terms-of-use', [DocumentController::class, 'getTermsOfUse']);
Route::get('/policy', [DocumentController::class, 'getPolicy']);


