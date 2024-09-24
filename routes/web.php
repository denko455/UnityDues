<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
use Filament\Http\Middleware\Authenticate;



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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware([Authenticate::class])->group(function () {
    Route::get("/pdf-payments/{filter}", [PDFController::class, "pdfPayments"])->name("pdfPayments");
    Route::get("/pdf-project-payments/{id}", [PDFController::class, "pdfProjectPayments"])->name("pdfProjectPayments");
    Route::get("/pdf-members/{filter}", [PDFController::class, "pdfMembers"])->name("pdfMembers");
    Route::get("/pdf-member-payments/{id}", [PDFController::class, "pdfMemberPayments"])->name("pdfMemberPayments");

});