<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PegawaiApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication
Route::post('/v1/pegawai/login', [PegawaiApiController::class, 'login']);

// Protected Employee Dashboard Endpoints
Route::middleware(['auth:sanctum', 'role:pegawai'])->prefix('v1/pegawai')->group(function () {
    
    // Auth & Profile
    Route::post('/logout', [PegawaiApiController::class, 'logout']);
    Route::get('/me', [PegawaiApiController::class, 'me']);
    
    // Dashboard States
    Route::get('/dashboard', [PegawaiApiController::class, 'dashboard']);
    Route::get('/absensi', [PegawaiApiController::class, 'attendanceIndex']);
    
    // Device Registration & Attendance Submissions
    Route::post('/absensi/device/init', [PegawaiApiController::class, 'initDevice']);
    Route::post('/absensi/submit', [PegawaiApiController::class, 'submitAttendance']);
    Route::post('/absensi/device/lookup', [PegawaiApiController::class, 'lookupDeviceOwner']);
    Route::post('/absensi/submit-exception', [PegawaiApiController::class, 'submitException']);
    Route::get('/absensi/history', [PegawaiApiController::class, 'getAttendanceHistory']);
    
    // Rosters / Roster Schedule
    Route::get('/jadwal', [PegawaiApiController::class, 'getSchedule']);
    
    // Leave Requests (Cuti / Sakit)
    Route::get('/izin', [PegawaiApiController::class, 'getLeaveRequests']);
    Route::post('/izin', [PegawaiApiController::class, 'submitLeaveRequest']);
    
    // Employee Request Submissions
    Route::post('/absensi/late-request', [PegawaiApiController::class, 'submitLateRequest']);
    Route::post('/absensi/checkout-correction', [PegawaiApiController::class, 'submitCheckoutCorrection']);
    Route::post('/absensi/overtime-request', [PegawaiApiController::class, 'submitOvertimeRequest']);
    
    // Secure Photo & Document Streams
    Route::get('/absensi/photo/{attendance}/{type}', [PegawaiApiController::class, 'streamAttendancePhoto']);
    Route::get('/izin/{leave}/doctor-note', [PegawaiApiController::class, 'streamLeaveDoctorNote']);
    Route::get('/absensi/late-request/{req}/evidence', [PegawaiApiController::class, 'streamLateEvidence']);
});
