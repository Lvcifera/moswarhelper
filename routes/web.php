<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Auth;
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

/**
 * конкретные действия
 */
Route::post('auth/', [MainController::class, 'authorizeTry'])->name('authorizeTry'); // попытка авторизации
Route::post('teeth/', [MainController::class, 'teethWork'])->name('teeth.work'); // покупка и открытие зубных ящиков
Route::post('gypsy/', [MainController::class, 'gypsyWork'])->name('gypsy.work'); // игра с гадалкой
Route::post('moscowpoly/', [MainController::class, 'moscowpolyWork'])->name('moscowpoly.work'); // броски кубиков москвополии
Route::post('petriks/', [MainController::class, 'petriksWork'])->name('petriks.work'); // варка петриков
Route::post('gifts/', [MainController::class, 'giftsWork'])->name('gifts.work'); // варка петриков
Route::post('licences/', [MainController::class, 'licenceAdd'])->name('licence.add'); // добавить лицензию

/**
 * страницы приложения
 */
Route::get('/', function () {
    return view('welcome');
})->name('welcome');
Route::get('auth', [MainController::class, 'authForm'])->name('auth')->middleware('auth'); // форма авторизации
Route::get('manual', [MainController::class, 'manual'])->name('manual'); // руководство пользователя
Route::get('licences', [MainController::class, 'licences'])->name('licences')->middleware('auth'); // лицензии
Route::get('teeth', [MainController::class, 'teeth'])->name('teeth')->middleware('auth');; // зубные ящики
Route::get('moscowpoly', [MainController::class, 'moscowpoly'])->name('moscowpoly')->middleware('auth');; // москвополия
Route::get('gypsy', [MainController::class, 'gypsy'])->name('gypsy')->middleware('auth');; // гадалка
Route::get('petriks', [MainController::class, 'petriks'])->name('petriks')->middleware('auth');; // петрики
Route::get('gifts', [MainController::class, 'gifts'])->name('gifts')->middleware('auth');; // подарки

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
