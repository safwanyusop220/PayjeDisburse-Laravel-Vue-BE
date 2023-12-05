<?php

use App\Http\Controllers\ProgramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('roles')->middleware('auth:sanctum')->group(function () {
    Route::get('all/permission', [App\Http\Controllers\RoleController::class, 'AllPermission']);
    Route::get('add/permission', [App\Http\Controllers\RoleController::class, 'AddPermission']);
    Route::get('getPermissionGroups', [App\Http\Controllers\RoleController::class, 'getPermissionGroups']);
    Route::post('/store', [App\Http\Controllers\RoleController::class, 'store']);
    Route::post('/storeRole', [App\Http\Controllers\RoleController::class, 'storeRole']);

    Route::get('all/roles', [App\Http\Controllers\RoleController::class, 'roles']);
    Route::get('/rolePermission', [App\Http\Controllers\RoleController::class, 'addRolePermission']);

    Route::get('/selectedRole/{id}', [App\Http\Controllers\RoleController::class, 'getSelectedPermissionRole']);

});

Route::prefix('authentications')->group(function () {
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->middleware('auth:sanctum');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::get('/user', [App\Http\Controllers\AuthController::class, 'user'])->middleware('auth:sanctum');
    Route::delete('/destroy/{id}', [App\Http\Controllers\AuthController::class, 'destroy']);
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/getCurrentUser/{id}', [App\Http\Controllers\AuthController::class, 'getCurrentUser'])->middleware('auth:sanctum');
    Route::post('/update/{id}', [App\Http\Controllers\AuthController::class, 'updateUser'])->middleware('auth:sanctum');

});

// Route::post('authentications/logout', function () {
//     $user = Auth::user();
//     $user->tokens()->delete();
//     return response()->json(['success' => true]);
// })->middleware('auth:sanctum');


Route::prefix('programs')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Http\Controllers\ProgramController::class, 'programs']);
    Route::post('/store', [App\Http\Controllers\ProgramController::class, 'store']);
    Route::get('/recommendation', [App\Http\Controllers\ProgramController::class, 'recommendation']);
    Route::get('/approval', [App\Http\Controllers\ProgramController::class, 'approval']);
    Route::delete('/destroy/{id}', [App\Http\Controllers\ProgramController::class, 'destroy']);
    Route::get('/edit/{id}', [App\Http\Controllers\ProgramController::class, 'edit']);
    Route::post('/update/{id}', [App\Http\Controllers\ProgramController::class, 'update']);
    Route::put('/endorseRecommendation', [App\Http\Controllers\ProgramController::class, 'endorseRecommendation']);
    Route::put('/singleRecommendation', [App\Http\Controllers\ProgramController::class, 'singleRecommendation']);
    Route::put('/singleApprove', [App\Http\Controllers\ProgramController::class, 'singleApprove']);
    Route::put('/approve', [App\Http\Controllers\ProgramController::class, 'approve']);
    Route::get('/bank-panel', [App\Http\Controllers\ProgramController::class, 'bankPanels']);
    Route::get('/show/{id}', [App\Http\Controllers\ProgramController::class, 'show']);
});

Route::prefix('receipients')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Http\Controllers\ReceipientController::class, 'index']);
    Route::post('/store', [App\Http\Controllers\ReceipientController::class, 'store']);
    Route::get('/recommendation', [App\Http\Controllers\ReceipientController::class, 'recommendation']);
    Route::get('/approval', [App\Http\Controllers\ReceipientController::class, 'approval']);
    Route::get('/programs', [App\Http\Controllers\ReceipientController::class, 'programs']);
    Route::get('/banks', [App\Http\Controllers\ReceipientController::class, 'banks']);
    Route::put('/endorse', [App\Http\Controllers\ReceipientController::class, 'endorse']);
    Route::put('/approve', [App\Http\Controllers\ReceipientController::class, 'approve']);
    Route::get('/program/show/{id}', [App\Http\Controllers\ReceipientController::class, 'program']);
    Route::delete('/destroy/{id}', [App\Http\Controllers\ReceipientController::class, 'destroy']);
    Route::get('/show/{id}', [App\Http\Controllers\ReceipientController::class, 'show']);
    Route::put('/singleRecommendation', [App\Http\Controllers\ReceipientController::class, 'singleRecommendation']);
    Route::put('/singleApprove', [App\Http\Controllers\ReceipientController::class, 'singleApprove']);
});

Route::prefix('bank-panel')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Http\Controllers\BankPanelController::class, 'index']);
    Route::post('/store', [App\Http\Controllers\BankPanelController::class, 'store']);
    Route::delete('/destroy/{id}', [App\Http\Controllers\BankPanelController::class, 'destroy']);
    Route::get('/edit/{id}', [App\Http\Controllers\BankPanelController::class, 'edit']);
    Route::post('/update/{id}', [App\Http\Controllers\BankPanelController::class, 'update']);
});

Route::prefix('audit-trail')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Http\Controllers\AuditTrailController::class, 'index']);
});
