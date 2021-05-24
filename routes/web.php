<?php

use App\Http\Controllers\BotFunctionController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ModuleController;
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

Route::get('test', [MainController::class, 'test'])->name('test')->middleware('auth'); // тестирование

/**
 * общие действия
 */
Route::post('auth/', [MainController::class, 'authorizeTry'])->name('authorizeTry'); // попытка авторизации
Route::post('licences/', [MainController::class, 'licenceAdd'])->name('licence.add'); // добавить лицензию

/**
 * модули
 */
Route::post('teeth/', [ModuleController::class, 'teethWork'])->name('teeth.work'); // покупка и открытие зубных ящиков
Route::post('gypsy/', [ModuleController::class, 'gypsyWork'])->name('gypsy.work'); // игра с гадалкой
Route::post('moscowpoly/', [ModuleController::class, 'moscowpolyWork'])->name('moscowpoly.work'); // броски кубиков москвополии
Route::post('petriks/', [ModuleController::class, 'petriksWork'])->name('petriks.work'); // варка петриков
Route::post('gifts/', [ModuleController::class, 'giftsWork'])->name('gifts.work'); // дарение подарков

/**
 * функции бота
 */
Route::post('botFunctions/patrol/create', [BotFunctionController::class, 'patrolCreate'])->name('patrol.create'); // создание задачи патруля
Route::get('botFunctions/patroldel/{id}', [BotFunctionController::class, 'patrolDelete'])->name('patrol.delete'); // удаление задачи патруля
Route::post('botFunctions/shaurburgers/create', [BotFunctionController::class, 'shaurburgersCreate'])->name('shaurburgers.create'); // создание задачи шаурбургерса
Route::get('botFunctions/shaurburgers/{id}', [BotFunctionController::class, 'shaurburgersDelete'])->name('shaurburgers.delete'); // удаление задачи шаурбургерса

/**
 * страницы приложения
 */
Route::get('/', function () {
    return view('welcome');
})->name('welcome');
Route::get('auth', [MainController::class, 'authForm'])->name('auth')->middleware('auth'); // форма авторизации
Route::get('manual', [MainController::class, 'manual'])->name('manual'); // руководство пользователя
Route::get('licences', [MainController::class, 'licences'])->name('licences')->middleware('auth'); // лицензии
Route::get('teeth', [ModuleController::class, 'teeth'])->name('teeth')->middleware('auth');; // зубные ящики
Route::get('moscowpoly', [ModuleController::class, 'moscowpoly'])->name('moscowpoly')->middleware('auth');; // москвополия
Route::get('gypsy', [ModuleController::class, 'gypsy'])->name('gypsy')->middleware('auth');; // гадалка
Route::get('petriks', [ModuleController::class, 'petriks'])->name('petriks')->middleware('auth');; // петрики
Route::get('gifts', [ModuleController::class, 'gifts'])->name('gifts')->middleware('auth');; // подарки
Route::get('botFunctions', [BotFunctionController::class, 'botFunctions'])->name('botFunctions')->middleware('auth');; // функции бота

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
