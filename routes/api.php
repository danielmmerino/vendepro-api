<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Tenancy\EmpresaController;
use App\Http\Controllers\Tenancy\LocalController;
use App\Http\Controllers\Tenancy\SuscripcionController;
use App\Http\Controllers\Tenancy\SuscripcionLocalController;
use App\Http\Controllers\Tenancy\SubscriptionStatusController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\ImpuestoController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\CategoriaProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\ProductoImportController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\CxpController;
use App\Http\Controllers\PagoProveedorController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PedidoController;

Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth.jwt');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth.jwt');
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth.jwt');
    Route::post('/force-invalidate', [AuthController::class, 'forceInvalidate'])->middleware('auth.jwt');
});

Route::get('/v1/menu', [MenuController::class, 'index']);

Route::prefix('v1')->middleware('idempotency')->group(function () {
    Route::apiResource('pedidos', PedidoController::class);
});

Route::prefix('v1')->middleware('auth.jwt')->group(function () {
    Route::middleware('check.subscription')->group(function () {
        Route::get('/empresas', [EmpresaController::class, 'index']);
        Route::post('/empresas', [EmpresaController::class, 'store']);
        Route::get('/empresas/{id}', [EmpresaController::class, 'show']);
        Route::put('/empresas/{id}', [EmpresaController::class, 'update']);
        Route::delete('/empresas/{id}', [EmpresaController::class, 'destroy']);

        Route::get('/locales', [LocalController::class, 'index']);
        Route::post('/locales', [LocalController::class, 'store']);
        Route::get('/locales/{id}', [LocalController::class, 'show']);
        Route::put('/locales/{id}', [LocalController::class, 'update']);
        Route::delete('/locales/{id}', [LocalController::class, 'destroy']);

        Route::get('/suscripciones', [SuscripcionController::class, 'index']);
        Route::post('/suscripciones', [SuscripcionController::class, 'store']);
        Route::get('/suscripciones/{id}', [SuscripcionController::class, 'show']);
        Route::put('/suscripciones/{id}', [SuscripcionController::class, 'update']);

        Route::get('/suscripciones-locales', [SuscripcionLocalController::class, 'index']);
        Route::post('/suscripciones-locales', [SuscripcionLocalController::class, 'store']);
        Route::get('/suscripciones-locales/{id}', [SuscripcionLocalController::class, 'show']);
        Route::put('/suscripciones-locales/{id}', [SuscripcionLocalController::class, 'update']);

        Route::get('/usuarios', [UsuarioController::class, 'index']);
        Route::post('/usuarios', [UsuarioController::class, 'store']);
        Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
        Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
        Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
        Route::post('/usuarios/{id}/roles', [UsuarioController::class, 'assignRoles']);

        Route::get('/roles', [RolController::class, 'index']);
        Route::post('/roles', [RolController::class, 'store']);
        Route::get('/roles/{id}', [RolController::class, 'show']);
        Route::put('/roles/{id}', [RolController::class, 'update']);
        Route::delete('/roles/{id}', [RolController::class, 'destroy']);

        Route::get('/permisos', [PermisoController::class, 'index']);
        Route::post('/permisos', [PermisoController::class, 'store']);
        Route::get('/permisos/{id}', [PermisoController::class, 'show']);
        Route::put('/permisos/{id}', [PermisoController::class, 'update']);
        Route::delete('/permisos/{id}', [PermisoController::class, 'destroy']);

        Route::get('/unidades', [UnidadMedidaController::class, 'index'])->middleware('can:productos.crear_editar');
        Route::post('/unidades', [UnidadMedidaController::class, 'store'])->middleware('can:productos.crear_editar');
        Route::get('/unidades/{id}', [UnidadMedidaController::class, 'show'])->middleware('can:productos.crear_editar');
        Route::put('/unidades/{id}', [UnidadMedidaController::class, 'update'])->middleware('can:productos.crear_editar');
        Route::delete('/unidades/{id}', [UnidadMedidaController::class, 'destroy'])->middleware('can:productos.crear_editar');

        Route::get('/impuestos', [ImpuestoController::class, 'index'])->middleware('can:productos.crear_editar');
        Route::post('/impuestos', [ImpuestoController::class, 'store'])->middleware('can:productos.crear_editar');
        Route::get('/impuestos/{id}', [ImpuestoController::class, 'show'])->middleware('can:productos.crear_editar');
        Route::put('/impuestos/{id}', [ImpuestoController::class, 'update'])->middleware('can:productos.crear_editar');
        Route::delete('/impuestos/{id}', [ImpuestoController::class, 'destroy'])->middleware('can:productos.crear_editar');

        Route::get('/metodos-pago', [MetodoPagoController::class, 'index'])->middleware('can:caja.cobrar');
        Route::post('/metodos-pago', [MetodoPagoController::class, 'store'])->middleware('can:config.locales.gestionar');
        Route::get('/metodos-pago/{id}', [MetodoPagoController::class, 'show'])->middleware('can:caja.cobrar');
        Route::put('/metodos-pago/{id}', [MetodoPagoController::class, 'update'])->middleware('can:config.locales.gestionar');
        Route::delete('/metodos-pago/{id}', [MetodoPagoController::class, 'destroy'])->middleware('can:config.locales.gestionar');

        Route::get('/categorias', [CategoriaProductoController::class, 'index'])->middleware('can:productos.crear_editar');
        Route::post('/categorias', [CategoriaProductoController::class, 'store'])->middleware('can:productos.crear_editar');
        Route::get('/categorias/{id}', [CategoriaProductoController::class, 'show'])->middleware('can:productos.crear_editar');
        Route::put('/categorias/{id}', [CategoriaProductoController::class, 'update'])->middleware('can:productos.crear_editar');
        Route::delete('/categorias/{id}', [CategoriaProductoController::class, 'destroy'])->middleware('can:productos.crear_editar');

        Route::get('/productos', [ProductoController::class, 'index'])->middleware('can:productos.ver');
        Route::post('/productos', [ProductoController::class, 'store'])->middleware('can:productos.crear_editar');
        Route::get('/productos/{id}', [ProductoController::class, 'show'])->middleware('can:productos.ver');
        Route::put('/productos/{id}', [ProductoController::class, 'update'])->middleware('can:productos.crear_editar');
        Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->middleware('can:productos.crear_editar');

        Route::get('/productos/{id}/receta', [RecetaController::class, 'index'])->middleware('can:productos.crear_editar');
        Route::post('/productos/{id}/receta', [RecetaController::class, 'store'])->middleware('can:productos.crear_editar');
        Route::delete('/productos/{id}/receta/{insumo_id}', [RecetaController::class, 'destroy'])->middleware('can:productos.crear_editar');

        Route::post('/productos/import', [ProductoImportController::class, 'store'])->middleware('can:productos.crear_editar');

        Route::get('/clientes', [ClienteController::class, 'index'])->middleware('can:reportes.ver');
        Route::post('/clientes', [ClienteController::class, 'store'])->middleware('can.any:ventas.facturacion,config.usuarios.gestionar');
        Route::get('/clientes/{id}', [ClienteController::class, 'show'])->middleware('can:reportes.ver');
        Route::put('/clientes/{id}', [ClienteController::class, 'update'])->middleware('can.any:ventas.facturacion,config.usuarios.gestionar');
        Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->middleware('can.any:ventas.facturacion,config.usuarios.gestionar');

        Route::get('/proveedores', [ProveedorController::class, 'index'])->middleware('can:proveedores.gestionar');
        Route::post('/proveedores', [ProveedorController::class, 'store'])->middleware('can:proveedores.gestionar');

        Route::get('/proveedores/{id}', [ProveedorController::class, 'show'])->middleware('can:proveedores.gestionar');
        Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->middleware('can:proveedores.gestionar');
        Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy'])->middleware('can:proveedores.gestionar');
        Route::get('/compras', [CompraController::class, 'index'])->middleware('can:compras.gestionar');
        Route::post('/compras', [CompraController::class, 'store'])->middleware('can:compras.gestionar');
        Route::get('/compras/{id}', [CompraController::class, 'show'])->middleware('can:compras.gestionar');
        Route::put('/compras/{id}', [CompraController::class, 'update'])->middleware('can:compras.gestionar');
        Route::delete('/compras/{id}', [CompraController::class, 'destroy'])->middleware('can:compras.gestionar');
        Route::post('/compras/{id}/aprobar', [CompraController::class, 'approve'])->middleware('can:compras.gestionar');
        Route::get('/cxp', [CxpController::class, 'index'])->middleware('can:compras.gestionar');
        Route::get('/cxp/{id}', [CxpController::class, 'show'])->middleware('can:compras.gestionar');
        Route::post('/pagos-proveedor', [PagoProveedorController::class, 'store'])->middleware('can:compras.gestionar');
        Route::get('/bodegas', [\App\Http\Controllers\BodegaController::class, 'index']);
          Route::post('/bodegas', [\App\Http\Controllers\BodegaController::class, 'store']);
          Route::get('/bodegas/{id}', [\App\Http\Controllers\BodegaController::class, 'show']);
          Route::put('/bodegas/{id}', [\App\Http\Controllers\BodegaController::class, 'update']);
          Route::delete('/bodegas/{id}', [\App\Http\Controllers\BodegaController::class, 'destroy']);
        Route::get('/mesas', [\App\Http\Controllers\MesaController::class, 'index']);
          Route::post('/mesas', [\App\Http\Controllers\MesaController::class, 'store']);
          Route::get('/mesas/{id}', [\App\Http\Controllers\MesaController::class, 'show']);
          Route::put('/mesas/{id}', [\App\Http\Controllers\MesaController::class, 'update']);
          Route::delete('/mesas/{id}', [\App\Http\Controllers\MesaController::class, 'destroy']);
    });

    Route::get('/estado-suscripcion', SubscriptionStatusController::class);
});

