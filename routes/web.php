<?php

use App\Http\Controllers\CheckinController;
use App\Livewire\Location\CheckinLocation;
use App\Models\Checkin;
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

Route::get('/welcome', fn () => 'welcome');
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/qr/{qr_code}', CheckinLocation::class);
// require __DIR__ . '/auth.php';
