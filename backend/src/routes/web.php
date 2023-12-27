<?php

use App\Http\Controllers\ExcelController;
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

Route::get('/', function () {
    return view('welcome');
})->name('/');


Auth::routes();


Route::get('/unauthorized', function () {
    return view('unauthorized');
})->name('unauthorized');

Route::get('/confirmation', function () {
    return view('confirmation');
})->name('confirmation');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
    Route::get('/work-shift', [App\Http\Controllers\HomeController::class, 'workShift'])->name('work-shift');
    Route::get('/past-work-shift', [App\Http\Controllers\HomeController::class, 'pastWorkShift'])->name('past-work-shift');
    Route::post('/get-past-work-shift', [App\Http\Controllers\HomeController::class, 'getPastWorkShift'])->name('get-past-work-shift');
    Route::post('/post-work-data', [App\Http\Controllers\HomeController::class, 'postWorkData'])->name('post-work-data');
    Route::post('/last-shift', [App\Http\Controllers\HomeController::class, 'lastShift'])->name('last-shift');
});

Route::middleware(['auth', 'check.supervisor.privileges'])->group(function () {
    Route::get('/create-news', [App\Http\Controllers\HomeController::class, 'createNews'])->name('create-news');
    Route::get('/supervisor', [App\Http\Controllers\HomeController::class, 'supervisor'])->name('supervisor');
    Route::get('/get-work-shifts', [App\Http\Controllers\HomeController::class, 'getWorkShifts'])->name('get-work-shifts');
    Route::get('/linkTool-upload', [App\Http\Controllers\EditorjsController::class, 'linkToolUpload'])->name('linkTool-upload');
    Route::post('/editorjsJsonUpload', [\App\Http\Controllers\EditorjsController::class, 'editorjsJsonUpload'])->name('editorjsJsonUpload');
    Route::any('/editorjsImageUpload', [\App\Http\Controllers\EditorjsController::class, 'editorjsImageUpload'])->name('editorjsImageUpload');
    Route::post('/grant-privileges', [App\Http\Controllers\HomeController::class, 'grantPrivileges'])->name('grant-privileges');
    Route::post('/revoke-privileges', [App\Http\Controllers\HomeController::class, 'revokePrivileges'])->name('revoke-privileges');
});

Route::middleware(['auth', 'check.admin.privileges'])->group(function () {
    Route::get('/admin', [App\Http\Controllers\HomeController::class, 'admin'])->name('admin');
    Route::post('/grant-supervisor-privileges', [App\Http\Controllers\HomeController::class, 'grantSupervisorPrivileges'])->name('grant-supervisor-privileges');
    Route::post('/revoke-supervisor-privileges', [App\Http\Controllers\HomeController::class, 'revokeSupervisorPrivileges'])->name('revoke-supervisor-privileges');
    Route::get('/transmit-data', [ExcelController::class, 'export'])->name('transmit-data');
});

