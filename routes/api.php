<?php

use App\Http\Controllers\ProgramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware('/register', [App\Http\Controllers\RegisterController::class, 'register']);


Route::prefix('programs')->group(function () {
    Route::get('/', [App\Http\Controllers\ProgramController::class, 'programs']);
    Route::post('/store', [App\Http\Controllers\ProgramController::class, 'store']);
    Route::get('/recommendation', [App\Http\Controllers\ProgramController::class, 'recommendation']);
    Route::get('/approval', [App\Http\Controllers\ProgramController::class, 'approval']);
    Route::delete('/destroy/{id}', [App\Http\Controllers\ProgramController::class, 'destroy']);
    Route::get('/edit/{id}', [App\Http\Controllers\ProgramController::class, 'edit']);
    Route::post('/update/{id}', [App\Http\Controllers\ProgramController::class, 'update']);
    // Route::put('/endorseRecommendation', [App\Http\Controllers\ProgramController::class, 'endorseRecommendation']);
    Route::put('/endorseRecommendation', [App\Http\Controllers\ProgramController::class, 'endorseRecommendation']);
    Route::put('/singleRecommendation', [App\Http\Controllers\ProgramController::class, 'singleRecommendation']);
    Route::put('/singleApprove', [App\Http\Controllers\ProgramController::class, 'singleApprove']);
    Route::put('/approve', [App\Http\Controllers\ProgramController::class, 'approve']);
    Route::get('/bank-panel', [App\Http\Controllers\ProgramController::class, 'bankPanels']);
    Route::get('/show/{id}', [App\Http\Controllers\ProgramController::class, 'show']);
});

Route::prefix('receipients')->group(function () {
    Route::get('/', [App\Http\Controllers\ReceipientController::class, 'index']);
    Route::post('/store', [App\Http\Controllers\ReceipientController::class, 'store']);
    Route::get('/recommendation', [App\Http\Controllers\ReceipientController::class, 'recommendation']);
    Route::get('/approval', [App\Http\Controllers\ReceipientController::class, 'approval']);
    Route::get('/programs', [App\Http\Controllers\ReceipientController::class, 'programs']);
    Route::get('/banks', [App\Http\Controllers\ReceipientController::class, 'banks']);
    Route::put('/endorse', [App\Http\Controllers\ReceipientController::class, 'endorse']);
    Route::put('/approve', [App\Http\Controllers\ReceipientController::class, 'approve']);
});

Route::prefix('bank-panel')->group(function () {
    Route::get('/', [App\Http\Controllers\BankPanelController::class, 'index']);
    Route::post('/store', [App\Http\Controllers\BankPanelController::class, 'store']);
    Route::delete('/destroy/{id}', [App\Http\Controllers\BankPanelController::class, 'destroy']);
    Route::get('/edit/{id}', [App\Http\Controllers\BankPanelController::class, 'edit']);
    Route::post('/update/{id}', [App\Http\Controllers\BankPanelController::class, 'update']);
});
