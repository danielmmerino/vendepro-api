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
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\CuentaItemController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\FacturaElectronicaController;
use App\Http\Controllers\CxcVentaController;
use App\Http\Controllers\NotaCreditoController;
use App\Http\Controllers\Sri\SecuenciaController;
use App\Http\Controllers\Sri\FirmaController;
use App\Http\Controllers\Sri\EstablecimientoController;
use App\Http\Controllers\Sri\EstadoController;
use App\Http\Controllers\Sri\CallbackController;
use App\Http\Controllers\CajaAperturaController;
use App\Http\Controllers\CajaMovimientoController;
use App\Http\Controllers\CajaDepositoController;
use App\Http\Controllers\CajaCierreController;
use App\Http\Controllers\CajaEstadoController;
use App\Http\Controllers\PagoVentaController;
use App\Http\Controllers\Inventario\StockController;
use App\Http\Controllers\Inventario\MovimientoController;
use App\Http\Controllers\Inventario\AjusteController;
use App\Http\Controllers\Inventario\TransferenciaController;
use App\Http\Controllers\Inventario\ConteoController;
use App\Http\Controllers\Inventario\LoteController;
use App\Http\Controllers\Inventario\ProduccionController;
use App\Http\Controllers\Inventario\MermaController;
use App\Http\Controllers\Inventario\CostoController;
use App\Http\Controllers\PromocionController;
use App\Http\Controllers\PromocionReglaController;
use App\Http\Controllers\PromocionComboController;
use App\Http\Controllers\PromocionSimulacionController;
use App\Http\Controllers\CuponController;
use App\Http\Controllers\PromocionReporteController;

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
    Route::apiResource('cuentas', CuentaController::class);
    Route::post('cuentas/{cuenta}/items', [CuentaItemController::class, 'store']);
});

Route::prefix('v1')->group(function () {
    Route::apiResource('reservas', ReservaController::class);
    Route::post('reservas/{id}/confirmar', [ReservaController::class, 'confirm']);
    Route::post('reservas/{id}/cancelar', [ReservaController::class, 'cancel']);
    Route::post('reservas/{id}/no-show', [ReservaController::class, 'noShow']);
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

        Route::get('/usuarios', [UsuarioController::class, 'index'])->middleware('can:usuarios.ver');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->middleware('can:usuarios.crear');
        Route::get('/usuarios/{id}', [UsuarioController::class, 'show'])->middleware('can:usuarios.ver');
        Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->middleware('can:usuarios.editar');
        Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->middleware('can:usuarios.eliminar');
        Route::post('/usuarios/{id}/roles', [UsuarioController::class, 'assignRoles'])->middleware('can:usuarios.asignar_roles');

        Route::get('/roles', [RolController::class, 'index'])->middleware('can:roles.ver');
        Route::post('/roles', [RolController::class, 'store'])->middleware('can:roles.crear');
        Route::get('/roles/{id}', [RolController::class, 'show'])->middleware('can:roles.ver');
        Route::put('/roles/{id}', [RolController::class, 'update'])->middleware('can:roles.editar');
        Route::delete('/roles/{id}', [RolController::class, 'destroy'])->middleware('can:roles.eliminar');
        Route::post('/roles/{id}/permisos', [RolController::class, 'assignPermissions'])->middleware('can:roles.asignar_permisos');

        Route::get('/permisos', [PermisoController::class, 'index'])->middleware('can:permisos.ver');
        Route::post('/permisos', [PermisoController::class, 'store'])->middleware('can:permisos.crear');
        Route::get('/permisos/{id}', [PermisoController::class, 'show'])->middleware('can:permisos.ver');
        Route::put('/permisos/{id}', [PermisoController::class, 'update'])->middleware('can:permisos.editar');
        Route::delete('/permisos/{id}', [PermisoController::class, 'destroy'])->middleware('can:permisos.eliminar');

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

        Route::get('/ventas/facturas', [FacturaController::class,'index']);
        Route::post('/ventas/facturas', [FacturaController::class,'store'])->middleware('idempotency');
        Route::get('/ventas/facturas/{id}', [FacturaController::class,'show']);
        Route::put('/ventas/facturas/{id}', [FacturaController::class,'update']);
        Route::delete('/ventas/facturas/{id}', [FacturaController::class,'destroy']);
        Route::post('/ventas/facturas/{id}/aprobar', [FacturaController::class,'aprobar']);
        Route::post('/ventas/facturas/{id}/anular', [FacturaController::class,'anular']);

        Route::get('/cxc', [CxcVentaController::class,'index']);
        Route::get('/cxc/{id}', [CxcVentaController::class,'show']);
        Route::post('/cxc/pagos', [CxcVentaController::class,'storePago'])->middleware('idempotency');
        Route::get('/cxc/{id}/pagos', [CxcVentaController::class,'pagos']);

        Route::get('/ventas/notas-credito', [NotaCreditoController::class,'index']);
        Route::post('/ventas/notas-credito', [NotaCreditoController::class,'store'])->middleware('idempotency');
        Route::get('/ventas/notas-credito/{id}', [NotaCreditoController::class,'show']);
        Route::post('/ventas/notas-credito/{id}/aplicar', [NotaCreditoController::class,'aplicar']);
        Route::post('/ventas/notas-credito/{id}/anular', [NotaCreditoController::class,'anular']);

        // Promociones & Descuentos
        Route::get('/promociones', [PromocionController::class, 'index'])->middleware('can:promociones.ver');
        Route::post('/promociones', [PromocionController::class, 'store'])->middleware('can:promociones.crear');
        Route::get('/promociones/{id}', [PromocionController::class, 'show'])->middleware('can:promociones.ver');
        Route::put('/promociones/{id}', [PromocionController::class, 'update'])->middleware('can:promociones.editar');
        Route::delete('/promociones/{id}', [PromocionController::class, 'destroy'])->middleware('can:promociones.eliminar');
        Route::post('/promociones/{id}/activar', [PromocionController::class, 'activar'])->middleware('can:promociones.activar');
        Route::post('/promociones/{id}/desactivar', [PromocionController::class, 'desactivar'])->middleware('can:promociones.desactivar');
        Route::post('/promociones/{id}/duplicar', [PromocionController::class, 'duplicar'])->middleware('can:promociones.crear');
        Route::post('/promociones/{id}/reglas', [PromocionReglaController::class, 'store'])->middleware('can:promociones.reglas.crear');
        Route::post('/promociones/{id}/combo', [PromocionComboController::class, 'store'])->middleware('can:promociones.reglas.crear');
        Route::post('/promociones/simular', [PromocionSimulacionController::class, 'simular'])->middleware('can:promociones.simular');
        Route::post('/promociones/aplicar', [PromocionSimulacionController::class, 'aplicar'])->middleware('can:promociones.aplicar');
        Route::get('/cupones', [CuponController::class, 'index'])->middleware('can:cupones.ver');
        Route::post('/cupones', [CuponController::class, 'store'])->middleware('can:cupones.crear');
        Route::post('/cupones/generar-masivo', [CuponController::class, 'generarMasivo'])->middleware('can:cupones.generar_masivo');
        Route::post('/cupones/validar', [CuponController::class, 'validar'])->middleware('can:cupones.validar');
        Route::post('/cupones/{id}/anular', [CuponController::class, 'anular'])->middleware('can:cupones.anular');
        Route::get('/promociones/efectividad', [PromocionReporteController::class, 'efectividad'])->middleware('can:promociones.reportes.ver');

        // Inventario
        Route::get('/stock', [StockController::class, 'index'])->middleware('can:inventario.stock.ver');
        Route::get('/stock/kardex', [StockController::class, 'kardex'])->middleware('can:inventario.stock.ver');

        Route::get('/inventario/movimientos', [MovimientoController::class, 'index'])->middleware('can:inventario.movimientos.ver');
        Route::get('/inventario/movimientos/{id}', [MovimientoController::class, 'show'])->middleware('can:inventario.movimientos.ver');

        Route::post('/inventario/ajustes', [AjusteController::class, 'store'])->middleware('can:inventario.ajustes.crear');

        Route::post('/inventario/transferencias', [TransferenciaController::class, 'store'])->middleware('can:inventario.transferencias.crear');
        Route::get('/inventario/transferencias', [TransferenciaController::class, 'index'])->middleware('can:inventario.transferencias.ver');
        Route::get('/inventario/transferencias/{id}', [TransferenciaController::class, 'show'])->middleware('can:inventario.transferencias.ver');
        Route::post('/inventario/transferencias/{id}/recibir', [TransferenciaController::class, 'recibir'])->middleware('can:inventario.transferencias.recibir');
        Route::post('/inventario/transferencias/{id}/cancelar', [TransferenciaController::class, 'cancelar'])->middleware('can:inventario.transferencias.cancelar');

        Route::post('/inventario/conteos', [ConteoController::class, 'store'])->middleware('can:inventario.conteos.crear');
        Route::post('/inventario/conteos/{id}/capturas', [ConteoController::class, 'capturas'])->middleware('can:inventario.conteos.capturas');
        Route::post('/inventario/conteos/{id}/cerrar', [ConteoController::class, 'cerrar'])->middleware('can:inventario.conteos.cerrar');
        Route::get('/inventario/conteos', [ConteoController::class, 'index'])->middleware('can:inventario.conteos.ver');
        Route::get('/inventario/conteos/{id}', [ConteoController::class, 'show'])->middleware('can:inventario.conteos.ver');

        Route::get('/inventario/lotes', [LoteController::class, 'index'])->middleware('can:inventario.lotes.ver');
        Route::get('/inventario/alertas', [LoteController::class, 'alertas'])->middleware('can:inventario.alertas.ver');

        Route::post('/inventario/produccion', [ProduccionController::class, 'store'])->middleware('can:inventario.produccion.crear');
        Route::post('/inventario/mermas', [MermaController::class, 'store'])->middleware('can:inventario.mermas.crear');

        Route::get('/inventario/costos', [CostoController::class, 'show'])->middleware('can:inventario.costos.ver');
        Route::post('/inventario/recalcular-costos', [CostoController::class, 'recalcular'])->middleware('can:inventario.costos.recalcular');

        // Facturas electrónicas
        Route::get('/facturas', [FacturaElectronicaController::class,'index'])->middleware('can:facturas.ver');
        Route::post('/facturas', [FacturaElectronicaController::class,'store'])->middleware('can:facturas.crear');
        Route::get('/facturas/{id}', [FacturaElectronicaController::class,'show'])->middleware('can:facturas.ver');
        Route::put('/facturas/{id}', [FacturaElectronicaController::class,'update'])->middleware('can:facturas.editar');
        Route::delete('/facturas/{id}', [FacturaElectronicaController::class,'destroy'])->middleware('can:facturas.eliminar');
        Route::post('/facturas/{id}/emitir', [FacturaElectronicaController::class,'emitir'])->middleware('can:facturas.emitir');
        Route::post('/facturas/{id}/reintentar-envio', [FacturaElectronicaController::class,'reintentarEnvio'])->middleware('can:facturas.emitir');
        Route::get('/facturas/{id}/estado-sri', [FacturaElectronicaController::class,'estadoSri'])->middleware('can:facturas.ver');
        Route::get('/facturas/{id}/xml', [FacturaElectronicaController::class,'xml'])->middleware('can:facturas.descargar');
        Route::get('/facturas/{id}/pdf', [FacturaElectronicaController::class,'pdf'])->middleware('can:facturas.descargar');
        Route::post('/facturas/{id}/email', [FacturaElectronicaController::class,'email'])->middleware('can:facturas.enviar_email');
        Route::post('/facturas/{id}/anular', [FacturaElectronicaController::class,'anular'])->middleware('can:facturas.anular');

        // Caja
        Route::post('/caja/aperturas', [CajaAperturaController::class,'store'])->middleware('can:caja.aperturas.crear');
        Route::get('/caja/aperturas', [CajaAperturaController::class,'index'])->middleware('can:caja.aperturas.ver');
        Route::get('/caja/aperturas/{id}', [CajaAperturaController::class,'show'])->middleware('can:caja.aperturas.ver');
        Route::post('/caja/movimientos', [CajaMovimientoController::class,'store'])->middleware('can:caja.movimientos.crear');
        Route::get('/caja/movimientos', [CajaMovimientoController::class,'index'])->middleware('can:caja.aperturas.ver');
        Route::post('/caja/deposito', [CajaDepositoController::class,'store'])->middleware('can:caja.depositos.crear');
        Route::post('/caja/cierre', [CajaCierreController::class,'store'])->middleware('can:caja.cierre.crear');
        Route::get('/caja/estado', [CajaEstadoController::class,'index'])->middleware('can:caja.aperturas.ver');

        // Pagos de venta
        Route::post('/pagos-venta', [PagoVentaController::class,'store'])->middleware(['can:pagos_venta.crear','idempotency']);
        Route::get('/pagos-venta', [PagoVentaController::class,'index'])->middleware('can:pagos_venta.ver');
        Route::get('/pagos-venta/{id}', [PagoVentaController::class,'show'])->middleware('can:pagos_venta.ver');
        Route::post('/pagos-venta/{id}/anular', [PagoVentaController::class,'anular'])->middleware('can:pagos_venta.anular');
    });

    Route::get('/sri/secuencias',[SecuenciaController::class,'index'])->middleware('can:sri.secuencias.ver');
    Route::post('/sri/secuencias/next',[SecuenciaController::class,'next'])->middleware('can:sri.secuencias.next');
    Route::post('/sri/firma/configurar',[FirmaController::class,'configurar'])->middleware('can:sri.firma.configurar');
    Route::get('/sri/firma/estado',[FirmaController::class,'estado'])->middleware('can:sri.firma.ver');
    Route::get('/sri/establecimientos',[EstablecimientoController::class,'index'])->middleware('can:sri.establecimientos.ver');
    Route::post('/sri/establecimientos',[EstablecimientoController::class,'store'])->middleware('can:sri.establecimientos.crear');
    Route::get('/sri/establecimientos/{id}',[EstablecimientoController::class,'show'])->middleware('can:sri.establecimientos.ver');
    Route::put('/sri/establecimientos/{id}',[EstablecimientoController::class,'update'])->middleware('can:sri.establecimientos.editar');
    Route::delete('/sri/establecimientos/{id}',[EstablecimientoController::class,'destroy'])->middleware('can:sri.establecimientos.eliminar');
    Route::get('/sri/estados/{clave}',[EstadoController::class,'show'])->middleware('can:sri.estados.ver');
    Route::post('/sri/callback',[CallbackController::class,'receive'])->middleware('can:sri.callback.recibir');
    Route::get('/estado-suscripcion', SubscriptionStatusController::class);
});

