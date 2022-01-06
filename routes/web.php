<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeneratePlan;
use App\Http\Controllers\Planner;
use App\Http\Controllers\ProfileRecord;
use App\Http\Controllers\Resep;
use App\Http\Controllers\Pengujian;

//Login
Route::get('/',function () {
    return redirect('/login');
});
//Logout (otomatis)
// After Login
Route::get('/dashboard', function () {
    return redirect('/planner-week');
})->name('dashboard');

//Generate Start
Route::middleware(['auth:sanctum', 'verified'])->get('/generate', [GeneratePlan::class, 'generateStart']);
//Generate Menu
Route::middleware(['auth:sanctum', 'verified'])->post('/generate-menu', [GeneratePlan::class, 'generate']);
Route::get('/generate-menu',  function () {
    return redirect('/generate');
});
//Generate Summary
Route::middleware(['auth:sanctum', 'verified'])->post('/generate-summary', [GeneratePlan::class, 'generatePilih']);
Route::get('/generate-summary',function () {
    return redirect('/generate');
});
//Generate Feedback & save
Route::middleware(['auth:sanctum', 'verified'])->post('/generate-feedback', [GeneratePlan::class, 'generateSave']);
Route::get('/generate-feedback',function () {
    return redirect('/generate');
});
Route::middleware(['auth:sanctum', 'verified'])->post('/generate-feedbackSave', [GeneratePlan::class, 'generateFeedbackSave']);
Route::get('/generate-feedbackSave',function () {
    return redirect('/generate');
});

//Track
Route::middleware(['auth:sanctum', 'verified'])->get('/track',[ProfileRecord::class, 'showProfile']);
Route::post('/cariKota', [ProfileRecord::class, 'cariKota']);
//Update Profile
Route::middleware(['auth:sanctum', 'verified'])->get('/update-profile',[ProfileRecord::class, 'updateProfile']);
//Update Basic
Route::middleware(['auth:sanctum', 'verified'])->post('/update-basic',[ProfileRecord::class, 'updateBasic']);

//Cari Resep
Route::middleware(['auth:sanctum', 'verified'])->get('/cari-resep',[Resep::class, 'resep_show']);
//Cari -> View Resep
Route::middleware(['auth:sanctum', 'verified'])->get('/resep/{id}', [Resep::class, 'cari_resep']);
//Like Resep
Route::middleware(['auth:sanctum', 'verified'])->post('/likeMenu', [Resep::class, 'likeMenu']);
//Unlike Resep
Route::middleware(['auth:sanctum', 'verified'])->post('/unlikeMenu', [Resep::class, 'unlikeMenu']);
//Save Resep
Route::middleware(['auth:sanctum', 'verified'])->post('/saveMenu', [Resep::class, 'saveMenu']);
//Unsave Resep
Route::middleware(['auth:sanctum', 'verified'])->post('/unsaveMenu', [Resep::class, 'unsaveMenu']);
//Edit Cari
Route::middleware(['auth:sanctum', 'verified'])->get('/buat-resep', [Resep::class, 'swow_buatResep']);
Route::middleware(['auth:sanctum', 'verified'])->post('/saveMake_resep', [Resep::class, 'save_buatResep']);
Route::middleware(['auth:sanctum', 'verified'])->get('/saveMake_resep', [Resep::class, 'direectKeBuatResep']);
Route::middleware(['auth:sanctum', 'verified'])->get('/helth_label', [Resep::class, 'helth_label']);

//Planner Week
Route::middleware(['auth:sanctum', 'verified'])->get('/planner-week',[Planner::class, 'Plan_Week']);
Route::middleware(['auth:sanctum', 'verified'])->get('/planner-week/{dateParam}', [Planner::class, 'Plan_Week_look']);
//Planner Beberapa Hari
Route::middleware(['auth:sanctum', 'verified'])->get('/planner-days', [Planner::class, 'Plan_Days']);
Route::middleware(['auth:sanctum', 'verified'])->get('/planner-days/{dateParam}', [Planner::class, 'Plan_Days_look']);

//Planner Hari
Route::middleware(['auth:sanctum', 'verified'])->get('/planner-day', [Planner::class, 'Plan_Day']);
Route::middleware(['auth:sanctum', 'verified'])->get('/planner-day/{dateParam}', [Planner::class, 'Plan_Day_look']);


//Koleksi
Route::middleware(['auth:sanctum', 'verified'])->get('/koleksi', [Resep::class, 'koleksi']);


//Daftar Belanja
Route::middleware(['auth:sanctum', 'verified'])->get('/daftar-belanja', [Planner::class, 'Daftar_Belanja']);
Route::middleware(['auth:sanctum', 'verified'])->post('/daftar-belanja-show', [Planner::class, 'Daftar_Belanja_GetData']);
Route::middleware(['auth:sanctum', 'verified'])->get('/daftar-belanja-show', [Planner::class, 'Daftar_Belanja']);

// Route::get('/debug', [Pengujian::class, 'egosimilar_v1']);
Route::get('/debug', [GeneratePlan::class, 'egosimilar']);
