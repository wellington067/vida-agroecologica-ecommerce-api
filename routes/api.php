<?php

use App\Http\Controllers\Api\BairroController;
use App\Http\Controllers\Api\BancaController;
use App\Http\Controllers\Api\ConsumidorController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\ProdutorController;
use App\Http\Controllers\Api\VendaController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Auth\Api\LoginController as LoginControllerWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [LoginControllerWeb::class, 'logout']);

    Route::controller(BairroController::class)->group(function () {
        Route::get('bairros', 'index');
    });

    //produtor
    Route::middleware('check.produtor')->group(function () {
        Route::apiResource('/produtores', ProdutorController::class, ['parameters' => ['produtores' => 'produtor']])->except('store');

        Route::delete('/bancas/imagens', [BancaController::class, 'deleteImagem']);
        Route::apiResource('/bancas', BancaController::class);
        Route::apiResource('banca/produtos', ProdutoController::class);

        Route::post('/vendas/{id}/confirmar', [VendaController::class, 'confirmarVenda']);
        Route::post('/vendas/{id}/enviar', [VendaController::class, 'marcarEnviado']);
    });
    //consumidor
    Route::middleware('check.consumidor')->group(function () {
        Route::apiResource('/consumidores', ConsumidorController::class, ['parameters' => ['consumidores' => 'consumidor']])->except('store');

        Route::post('/vendas/{id}/comprovante', [VendaController::class, 'anexarComprovante']);
        Route::post('/vendas/{id}/entregar', [VendaController::class, 'marcarEntregue']);
        Route::post('/vendas', [VendaController::class, 'store']);
    });
    //fora dos middlewares
    Route::get('/vendas/{id}/comprovante', [VendaController::class, 'verComprovante']);
    Route::post('/vendas/{id}/cancelar', [VendaController::class, 'cancelarCompra']);
    Route::apiResource('/vendas', VendaController::class)->except('store', 'destroy', 'update');
    Route::get('/categorias', function () {
        return response()->json(['categorias' => App\Models\ProdutoTabelado::distinct()->pluck('categoria')]);
    });
    Route::controller(ProdutoController::class)->group(function () {
        Route::post('/busca', 'buscar');
        Route::get('/categorias/{categoria}/produtos', 'buscarCategoria');
    });
    Route::get('/produtos', function () {
        $produtos = App\Models\ProdutoTabelado::all();
        return response()->json(['produtos' => $produtos]);
    });
    Route::get('/imagens/bancas/{banca}', [BancaController::class, 'getImagem']);
    Route::get('/produtores/{produtorId}/bancas', [ProdutorController::class, 'getBanca']);
});

Route::post('/produtores', [ProdutorController::class, 'store']);

Route::post('/consumidores', [ConsumidorController::class, 'store']);

Route::get('/login', fn () => response()->json(['error' => 'Login necessário'], 401))->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/token', [LoginController::class, 'token']);

Route::get('/email/verify', function () {
    return response()->json(['error' => 'O usuário não está verificado!', 'email' => Auth::user()->email], 403);
})->middleware('auth:sanctum')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [LoginController::class, 'verificarEmail'])->middleware('signed')->name('verification.verify');
Route::post('/email/verification-notification', [LoginController::class, 'reenviarEmail'])->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

// Rota para solicitar o email de redefinição de senha
Route::post('/forgot-password', [ResetPasswordController::class, 'sendResetEmail'])->name('password.email');

Route::get('/imagens/produtos/{id}', [ProdutoController::class, 'getImagem']);


// Parte do gesão web


//Route::post('login', [App\Http\Controllers\Auth\Api\LoginController::class, 'login']); //conflito

Route::middleware(['auth:sanctum', 'type.admin'])->group(function () {
    // Usuario
    Route::post('cadastro', [UserController::class, 'store']);
    Route::post('atualizar/usuario', [UserController::class, 'update']);
    Route::get('users', [UserController::class, 'index']);

    // Associacao
    Route::get('associacoes', [\App\Http\Controllers\Auth\Api\AssociacaoController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'type.admin.presidente'])->group(function () {
    //Associacao
    Route::post('cadastrar/associacao', [\App\Http\Controllers\Auth\Api\AssociacaoController::class, 'store']);
    Route::post('atualizar/associacao', [\App\Http\Controllers\Auth\Api\AssociacaoController::class, 'update']);

    // OCS
    Route::post('/organizacaoControleSocial/store', [App\Http\Controllers\Api\OrganizacaoControleSocialController::class, 'store']);
    Route::post('/organizacaoControleSocial/update', [App\Http\Controllers\Api\OrganizacaoControleSocialController::class, 'update']);
    Route::get('/associacao/{associacao_id}/organizacaoControleSocial', [App\Http\Controllers\Api\OrganizacaoControleSocialController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'type.presidente'])->group(function () {
    //minhas associações
});

Route::middleware(['auth:sanctum', 'type.agricultor'])->group(function () {

    // Propriedade
    Route::post('/propriedade/store', [App\Http\Controllers\Api\PropriedadeController::class, 'store']);
    Route::post('/propriedade/update', [App\Http\Controllers\Api\PropriedadeController::class, 'update']);
    Route::get('/usuario/{user_id}/propriedades', [App\Http\Controllers\Api\PropriedadeController::class, 'index']);
});

Route::post('/verifica', [UserController::class, 'verificaUsuario']);
