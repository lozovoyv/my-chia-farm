<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/', [PagesController::class, 'homePage'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/jobs', [PagesController::class, 'jobsPage'])->middleware(['auth', 'verified'])->name('jobs');
Route::get('/settings', [PagesController::class, 'settingsPage'])->middleware(['auth', 'verified'])->name('settings');

Route::post('/api/config', [ConfigController::class, 'set'])->middleware('auth');
Route::post('/api/get-keys', [ConfigController::class, 'keys'])->middleware('auth');

Route::post('/api/get-jobs', [JobController::class, 'get'])->middleware('auth');
Route::post('/api/new-job', [JobController::class, 'store'])->middleware('auth');
Route::post('/api/update-job', [JobController::class, 'update'])->middleware('auth');
Route::post('/api/remove-job', [JobController::class, 'remove'])->middleware('auth');

Route::post('/api/dismiss-worker', [WorkerController::class, 'destroy'])->middleware('auth');
Route::post('/api/add-worker', [WorkerController::class, 'start'])->middleware('auth');
