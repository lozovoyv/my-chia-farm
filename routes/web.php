<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

use App\Http\Controllers\API\ConfigController;
use App\Http\Controllers\API\JobController;
use App\Http\Controllers\API\WorkerController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Pages\HomePageController;
use App\Http\Controllers\Pages\JobsPageController;
use App\Http\Controllers\Pages\SettingsPageController;
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

Route::get('/', [HomePageController::class, 'page'])->middleware(['auth'])->name('dashboard');
Route::get('/jobs', [JobsPageController::class, 'page'])->middleware(['auth'])->name('jobs');
Route::get('/settings', [SettingsPageController::class, 'page'])->middleware(['auth'])->name('settings');

/*
|--------------------------------------------------------------------------
| Frontend API Routes
|--------------------------------------------------------------------------
*/
Route::post('/api/config', [ConfigController::class, 'set'])->middleware('auth');
Route::post('/api/get-keys', [ConfigController::class, 'keys'])->middleware('auth');

Route::post('/api/job/all', [JobController::class, 'all'])->middleware('auth');
Route::post('/api/job/create', [JobController::class, 'create'])->middleware('auth');
Route::post('/api/job/update', [JobController::class, 'update'])->middleware('auth');
Route::post('/api/job/remove', [JobController::class, 'remove'])->middleware('auth');

Route::post('/api/worker/dismiss', [WorkerController::class, 'dismiss'])->middleware('auth');
Route::post('/api/worker/add', [WorkerController::class, 'start'])->middleware('auth');
