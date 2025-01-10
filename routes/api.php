<?php

use App\Http\Controllers\Api\ApiResidenceController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiComplaintController;
use App\Http\Controllers\Api\ApiProfileController;
use App\Http\Controllers\Api\ApiStaffController;

// Create Token
Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});

// User's Dashboard API Routes
Route::get('dashboard', [ApiComplaintController::class, 'displayComplaint'])
->name('api.dashboard');

// User's Profile API Routes
Route::get('profile', [ApiProfileController::class, 'show'])->name('api.profile.show');
Route::put('profile/update', [ApiProfileController::class, 'update'])->name('api.profile.update');
Route::delete('profile/delete', [ApiProfileController::class, 'destroy'])->name('api.profile.destroy');

// Staff API Routes
Route::get('staff', [ApiStaffController::class, 'showStaff'])->name('api.staff.show');
Route::post('staff/store', [ApiStaffController::class, 'store'])->name('api.staff.store');
Route::get('staff/{id}', [ApiStaffController::class, 'showDetails'])->name('api.staff.details');
Route::put('staff/update', [ApiStaffController::class, 'update'])->name('api.staff.update');
Route::delete('staff/delete', [ApiStaffController::class, 'destroy'])->name('api.staff.destroy');

// Complaints API Routes
Route::get('complaints', [ApiComplaintController::class, 'index'])->name('api.complaint.index');   // Show all complaints
Route::post('complaints/file', [ApiComplaintController::class, 'store'])->name('api.complaint.store');    // Store a new complaint
Route::get('complaints/{id}', [ApiComplaintController::class, 'show'])->name('api.complaint.show');   // Show a complaint
Route::put('complaints/update', [ApiComplaintController::class, 'edit'])->name('api.complaint.edit');   // Update a complaint
Route::delete('complaints/delete', [ApiComplaintController::class, 'destroy'])->name('api.complaint.destroy'); // Delete a complaint

// Residence API routes
Route::post('residences', [ApiResidenceController::class, 'store'])->name('api.residence');    // Store a new residence
Route::put('residences/{id}', [ApiResidenceController::class, 'edit'])->name('api.residence.edit');   // Update a residence
Route::delete('residences/{id}', [ApiResidenceController::class, 'destroy'])->name('api.residence.destroy'); // Delete a residence



require __DIR__.'/auth.php';
