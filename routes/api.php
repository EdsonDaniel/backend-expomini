<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Articulos;
use App\Http\Controllers\ArticulosController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/art', [ArticulosController::class, 'index']);
Route::get('/buscar', [ArticulosController::class, 'search']);
Route::get('/prm',[ArticulosController::class, 'promociones']);

Route::post('/generarpedido', [ArticulosController::class, 'generaPedido']);

Route::get('/busca_cliente', [ArticulosController::class, 'buscaCliente']);
Route::get('/generarpedido2', [ArticulosController::class, 'generaPedido2']);
Route::get('/busca_pedido', [ArticulosController::class, 'buscaPedido']);

Route::get('/prueba', [ArticulosController::class, 'prueba']);

Route::post('/registarse', [ArticulosController::class, 'registar']);

Route::get('/sinc_ped_manual', [ArticulosController::class, 'sinc_manual']);

Route::get('/promocionados', [ArticulosController::class, 'productosPromocionados']);