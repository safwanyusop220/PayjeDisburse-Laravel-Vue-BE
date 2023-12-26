<?php
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\BankPanelController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceipientController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('roles')->middleware('auth:sanctum')->group(function () {
    Route::get('all/permission', [RoleController::class, 'AllPermission']);
    Route::get('add/permission', [RoleController::class, 'AddPermission']);
    Route::get('getPermissionGroups', [RoleController::class, 'getPermissionGroups']);
    Route::post('/store', [RoleController::class, 'store']);
    Route::post('/storeRole', [RoleController::class, 'storeRole']);
    Route::get('all/roles', [RoleController::class, 'roles']);
    Route::get('/rolePermission', [RoleController::class, 'addRolePermission']);
    Route::get('/selectedRole/{id}', [RoleController::class, 'getSelectedPermissionRole']);
    Route::put('/update/{id}', [RoleController::class, 'updateRole']);
    Route::delete('/destroy/{id}', [RoleController::class, 'destroy'])->middleware('auth:sanctum');
});

Route::prefix('authentications')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('auth:sanctum');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/landingPage', [AuthController::class, 'login']);
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
    Route::get('/profile', [AuthController::class, 'getProfile'])->middleware('auth:sanctum');
    Route::get('/selectedUser/{id}', [AuthController::class, 'getUserRolePermission']);
    Route::delete('/destroy/{id}', [AuthController::class, 'destroy'])->middleware('auth:sanctum');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/getCurrentUser/{id}', [AuthController::class, 'getCurrentUser'])->middleware('auth:sanctum');
    Route::post('/update/{id}', [AuthController::class, 'updateUser'])->middleware('auth:sanctum');
    Route::put('/updateUserRolePermission/{id}', [AuthController::class, 'updateUserRolePermission'])->middleware('auth:sanctum');
});

// Route::post('authentications/logout', function () {
//     $user = Auth::user();
//     $user->tokens()->delete();
//     return response()->json(['success' => true]);
// })->middleware('auth:sanctum');


Route::prefix('programs')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ProgramController::class, 'programs']);
    Route::post('/store', [ProgramController::class, 'store']);
    Route::get('/recommendation', [ProgramController::class, 'recommendation']);
    Route::get('/approval', [ProgramController::class, 'approval']);
    Route::delete('/destroy/{id}', [ProgramController::class, 'destroy']);
    Route::get('/edit/{id}', [ProgramController::class, 'edit']);
    Route::post('/update/{id}', [ProgramController::class, 'update']);
    Route::put('/bulkApprove', [ProgramController::class, 'bulkApprove']);
    Route::put('/bulkReject', [ProgramController::class, 'bulkReject']);
    Route::put('/bulkApproveRecommendation', [ProgramController::class, 'bulkApproveRecommendation']);
    Route::put('/bulkRejectRecommendation', [ProgramController::class, 'bulkRejectRecommendation']);
    Route::put('/singleRecommendation', [ProgramController::class, 'singleRecommendation']);
    Route::put('/singleRejectSubmit', [ProgramController::class, 'singleRejectSubmit']);
    Route::put('/singleApprove', [ProgramController::class, 'singleApprove']);
    Route::put('/singleReject', [ProgramController::class, 'singleReject']);
    Route::get('/bank-panel', [ProgramController::class, 'bankPanels']);
    Route::get('/show/{id}', [ProgramController::class, 'show']);
});

Route::prefix('receipients')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ReceipientController::class, 'index']);
    Route::post('/store', [ReceipientController::class, 'store']);
    Route::get('/recommendation', [ReceipientController::class, 'recommendation']);
    Route::get('/approval', [ReceipientController::class, 'approval']);
    Route::get('/programs', [ReceipientController::class, 'programs']);
    Route::get('/banks', [ReceipientController::class, 'banks']);
    Route::put('/endorse', [ReceipientController::class, 'endorse']);
    Route::put('/bulkRejectRecommendation', [ReceipientController::class, 'bulkRejectRecommendation']);
    Route::put('/approve', [ReceipientController::class, 'approve']);
    Route::put('/bulkRejectApproval', [ReceipientController::class, 'bulkRejectApproval']);
    Route::get('/program/show/{id}', [ReceipientController::class, 'program']);
    Route::delete('/destroy/{id}', [ReceipientController::class, 'destroy']);
    Route::get('/show/{id}', [ReceipientController::class, 'show']);
    Route::put('/singleRecommendation', [ReceipientController::class, 'singleRecommendation']);
    Route::put('/singleRejectSubmitted', [ReceipientController::class, 'singleRejectSubmitted']);
    Route::put('/singleApprove', [ReceipientController::class, 'singleApprove']);
    Route::put('/singleRejectApproval', [ReceipientController::class, 'singleRejectApproval']);
    Route::get('/edit/{id}', [ReceipientController::class, 'edit']);
    Route::post('/update/{id}', [ReceipientController::class, 'update']);
});

Route::prefix('payment')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PaymentController::class, 'payment']);
    Route::get('/recipient-list/{id}', [PaymentController::class, 'recipientList']);
});

Route::prefix('bank-panel')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [BankPanelController::class, 'index']);
    Route::post('/store', [BankPanelController::class, 'store']);
    Route::delete('/destroy/{id}', [BankPanelController::class, 'destroy']);
    Route::get('/edit/{id}', [BankPanelController::class, 'edit']);
    Route::post('/update/{id}', [BankPanelController::class, 'update']);
    Route::get('/search', [BankPanelController::class, 'search']);
});

Route::prefix('audit-trail')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AuditTrailController::class, 'index']);
});