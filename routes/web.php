<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileComplaintController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('main');

Route::middleware(['auth', 'checkrole:user'])->group(function () {
    // User's Dashboard
    Route::get('/dashboard', [DashboardController::class, 'showRecentComplaint'])->name('dashboard');

    // File Complaint Routes
    Route::middleware('checkrole:user')->group(function () {
        Route::get('/file_complaint', [FileComplaintController::class, 'showFileComplaintForm'])->name('file_complaint');
        Route::get('/file_complaint/success', function () {
            return view('residence/complaint/successComplaint');
        })->name('file_complaint.success');
        Route::get('/complaint/{id}', [FileComplaintController::class, 'show'])->name('complaint.show');
    });

    // My Profile Module (User role only)
    Route::middleware('checkrole:user')->group(function () {
        Route::get('/profile', [ProfileController::class, 'showProfile'])->name('myprofile');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('myprofile.destroy');
        Route::post('/user/profile/photo', [ProfileController::class, 'updateProfilePhoto'])->name('myprofile.photo');
    });
        
});

Route::middleware(['auth', 'checkrole:admin'])->group(function () {
    // Admin Dashboard (Admin role only)
    Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])
    ->name('admin.dashboard');

    // Complaint List
    Route::get('/admin/complaint', [AdminDashboardController::class, 'show'])->name('admin.complaint.show');
    Route::get('/admin/complaint/{id}', [AdminDashboardController::class, 'showDetails'])->name('admin.complaint.details');
    Route::get('admin/staff_management', [AdminDashboardController::class, 'staffManagement'])->name('admin.staff_management');
    Route::get('admin/report', [AdminDashboardController::class, 'report'])->name('admin.report');
});

Route::middleware(['auth', 'checkrole:staff'])->group(function () {
    // Staff Dashboard (Staff role only)
    Route::get('staff/dashboard', [StaffDashboardController::class, 'showAssignedCase'])
    ->name('staff.dashboard');
    // Complaint Details (Staff role only)
    Route::get('staff/complaint/{id}', [StaffDashboardController::class, 'showComplaintDetails'])
    ->name('staff.complaint.details');
});

require __DIR__.'/auth.php';
