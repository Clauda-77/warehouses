<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/bill/{id}/pdf', [App\Http\Controllers\BillPdfController::class, 'generate'])->name('bill.pdf');