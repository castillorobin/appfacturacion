<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteWebController;
use App\Http\Controllers\ProductoWebController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\AjusteInventarioController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DTEController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\NotaCreditoController;
use App\Http\Controllers\ContingenciaDTEController;
use App\Http\Controllers\KardexController;
/*
Route::get('/', function () {
    return view('welcome');
});
*/
Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

   
     Route::get('/clientes', [ClienteWebController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/create', [ClienteWebController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteWebController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}/edit', [ClienteWebController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClienteWebController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteWebController::class, 'destroy'])->name('clientes.destroy');
    Route::get('/clientes/{cliente}', [ClienteWebController::class, 'show'])->name('clientes.show');



    Route::get('/productos', [ProductoWebController::class, 'index'])->name('productos.index');
    Route::get('/productos/create', [ProductoWebController::class, 'create'])->name('productos.create');
    Route::post('/productos', [ProductoWebController::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}/edit', [ProductoWebController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}', [ProductoWebController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoWebController::class, 'destroy'])->name('productos.destroy');


    Route::resource('categorias', CategoriaController::class)->except('show');
    Route::get('ajustes/create', [AjusteInventarioController::class, 'create'])->name('ajustes.create');
    Route::post('ajustes', [AjusteInventarioController::class, 'store'])->name('ajustes.store');

Route::resource('facturas', FacturaController::class);
//    Route::resource('facturas', FacturaController::class)->only(['index', 'create', 'store']);

Route::resource('compras', CompraController::class)->only(['index', 'create', 'store']);

Route::get('dtes/sujeto', [DTEController::class, 'creandosujeto'])->name('admin.crearsujeto');
Route::post('/facturas/sujeto/store', [CompraController::class, 'storeSujetoExcluido'])->name('compras.storeSujetoExcluido');


Route::post('/facturas/sujeto-excluido', [FacturaController::class, 'generarSujetoExcluido'])->name('facturas.sujeto_excluido.generar');

//Admin DTE's
Route::get('/dtes', [DTEController::class, 'index'])->name('dtes.index');
Route::get('/dtes/{id}/json', [DTEController::class, 'descargarJson'])->name('dtes.descargarJson');
Route::get('/dtes/{id}/pdf', [DTEController::class, 'verPdf'])->name('dtes.verPdf');
Route::get('/dtes/descargar-json', [\App\Http\Controllers\DTEController::class, 'descargarJsonLote'])
     ->name('dtes.descargarJsonLote');

Route::post('/dtes/{dte}/anular', [App\Http\Controllers\DTEController::class, 'anular'])->name('dtes.anular');
//Route::post('/dtes/{dte}/nota-credito', [NotaCreditoController::class, 'emitirDesdeDTE'])->name('dtes.emitirNotaCredito');

Route::get('/notas-credito/emitir/{dte}', [NotaCreditoController::class, 'formEmitir'])->name('notas-credito.formEmitir');
Route::post('/notas-credito/emitir/{dte}', [NotaCreditoController::class, 'emitirDesdeDTE'])->name('notas-credito.emitirDesdeDTE');

//Contingencia
Route::get('/dtes/emitirEnContingencia/{id}', [ContingenciaController::class, 'emitirEnContingencia'])->name('dtes.emitirEnContingencia');


//Proveedores
Route::resource('proveedores', ProveedorController::class)->parameters([
    'proveedores' => 'proveedor'
]);

});

//Administrar Cajas
Route::resource('cajas', CajaController::class)->only(['index', 'create', 'store']);
Route::get('cajas/{caja}/movimientos', [CajaController::class, 'movimientos'])->name('cajas.movimientos');
Route::post('/cajas/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('cajas.cerrar');

//Contingencia DTE
Route::prefix('contingencia')->group(function () {
    Route::get('/', [ContingenciaDTEController::class, 'index'])->name('contingencia.index');
    Route::post('/reportar/{id}', [ContingenciaDTEController::class, 'reportar'])->name('contingencia.reportar');
    Route::post('/enviar/{id}', [ContingenciaDTEController::class, 'enviar'])->name('contingencia.enviar');
    Route::get('/crearcontingencia', [ContingenciaDTEController::class, 'crearcontingencia'])->name('contingencia.crear');
    Route::post('/guardarcontingencia', [ContingenciaDTEController::class, 'store'])->name('contingencia.store');
});

//Kardex
Route::get('/kardex', [KardexController::class, 'index'])->name('kardex.index');
Route::get('/kardex/{producto}', [KardexController::class, 'show'])->name('kardex.detalle');

require __DIR__.'/auth.php';
