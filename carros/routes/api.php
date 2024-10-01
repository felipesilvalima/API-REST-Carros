<?php declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('V1')->middleware('jwt.auth')->group(function(){
    Route::post('me',[AuthController::class, 'me'])->name('me.auth');
    Route::post('refresh',[AuthController::class, 'refresh'])->name('refresh.auth');
    Route::post('logout',[AuthController::class, 'logout'])->name('logout.auth');
    Route::apiResource('cliente','App\Http\Controllers\ClienteController');
    Route::apiResource('carro','App\Http\Controllers\CarroController');
    Route::apiResource('locacao','App\Http\Controllers\LocacaoController');
    Route::apiResource('marca','App\Http\Controllers\MarcaController');
    Route::apiResource('modelo','App\Http\Controllers\ModeloController');
});

Route::post('login',[AuthController::class, 'login'])->name('login.auth');


