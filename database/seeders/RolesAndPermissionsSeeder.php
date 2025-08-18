<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = [
            ['codigo' => 'SUPER_ADMIN',   'nombre' => 'Super Administrador'],
            ['codigo' => 'ADMIN_LOCAL',   'nombre' => 'Administrador local'],
            ['codigo' => 'FINANZAS',      'nombre' => 'Finanzas'],
            ['codigo' => 'MESERO',        'nombre' => 'Mesero'],
            ['codigo' => 'CAJERO',        'nombre' => 'Cajero'],
            ['codigo' => 'BODEGA',        'nombre' => 'Bodega'],
            ['codigo' => 'COCINA',        'nombre' => 'Cocina'],
            ['codigo' => 'MARKETING',    'nombre' => 'Marketing'],
        ];

        foreach ($roles as $r) {
            DB::table('roles')->updateOrInsert(
                ['codigo' => $r['codigo']],
                ['nombre' => $r['nombre'], 'descripcion' => null]
            );
        }

        // Permisos mínimos (expande según tu UI)
        $permisos = [
            // Usuarios
            ['codigo' => 'usuarios.ver',                'nombre' => 'Ver usuarios'],
            ['codigo' => 'usuarios.crear',              'nombre' => 'Crear usuarios'],
            ['codigo' => 'usuarios.editar',             'nombre' => 'Editar usuarios'],
            ['codigo' => 'usuarios.eliminar',           'nombre' => 'Eliminar usuarios'],
            ['codigo' => 'usuarios.asignar_roles',      'nombre' => 'Asignar roles a usuarios'],
            // Roles
            ['codigo' => 'roles.ver',                   'nombre' => 'Ver roles'],
            ['codigo' => 'roles.crear',                 'nombre' => 'Crear roles'],
            ['codigo' => 'roles.editar',                'nombre' => 'Editar roles'],
            ['codigo' => 'roles.eliminar',              'nombre' => 'Eliminar roles'],
            ['codigo' => 'roles.asignar_permisos',      'nombre' => 'Asignar permisos a roles'],
            // Permisos
            ['codigo' => 'permisos.ver',                'nombre' => 'Ver permisos'],
            ['codigo' => 'permisos.crear',              'nombre' => 'Crear permisos'],
            ['codigo' => 'permisos.editar',             'nombre' => 'Editar permisos'],
            ['codigo' => 'permisos.eliminar',           'nombre' => 'Eliminar permisos'],
            // Configuración
            ['codigo' => 'config.locales.gestionar',     'nombre' => 'Gestionar locales'],
            ['codigo' => 'config.usuarios.gestionar',    'nombre' => 'Gestionar usuarios'],
            ['codigo' => 'config.roles.permisos',        'nombre' => 'Gestionar roles y permisos'],
            ['codigo' => 'config.ver',                   'nombre' => 'Ver configuración empresa'],
            ['codigo' => 'config.editar',                'nombre' => 'Editar configuración empresa'],
            ['codigo' => 'locales.config.ver',           'nombre' => 'Ver configuración local'],
            ['codigo' => 'locales.config.editar',        'nombre' => 'Editar configuración local'],
            // Productos / Inventario
            ['codigo' => 'productos.ver',                'nombre' => 'Ver productos'],
            ['codigo' => 'productos.crear_editar',       'nombre' => 'Crear/editar productos'],
            ['codigo' => 'inventario.movimientos',       'nombre' => 'Movimientos de inventario'],
            ['codigo' => 'inventario.stock.ver',         'nombre' => 'Ver stock'],
            ['codigo' => 'inventario.movimientos.ver',   'nombre' => 'Ver movimientos de inventario'],
            ['codigo' => 'inventario.ajustes.crear',     'nombre' => 'Crear ajustes de inventario'],
            ['codigo' => 'inventario.transferencias.crear', 'nombre' => 'Crear transferencias'],
            ['codigo' => 'inventario.transferencias.ver', 'nombre' => 'Ver transferencias'],
            ['codigo' => 'inventario.transferencias.recibir', 'nombre' => 'Recibir transferencias'],
            ['codigo' => 'inventario.transferencias.cancelar', 'nombre' => 'Cancelar transferencias'],
            ['codigo' => 'inventario.conteos.crear',     'nombre' => 'Crear conteos'],
            ['codigo' => 'inventario.conteos.capturas',  'nombre' => 'Registrar capturas de conteo'],
            ['codigo' => 'inventario.conteos.cerrar',    'nombre' => 'Cerrar conteos'],
            ['codigo' => 'inventario.conteos.ver',       'nombre' => 'Ver conteos'],
            ['codigo' => 'inventario.lotes.ver',         'nombre' => 'Ver lotes'],
            ['codigo' => 'inventario.alertas.ver',       'nombre' => 'Ver alertas de inventario'],
            ['codigo' => 'inventario.produccion.crear',  'nombre' => 'Registrar producción'],
            ['codigo' => 'inventario.mermas.crear',      'nombre' => 'Registrar mermas'],
            ['codigo' => 'inventario.costos.ver',        'nombre' => 'Ver costos de inventario'],
            ['codigo' => 'inventario.costos.recalcular', 'nombre' => 'Recalcular costos de inventario'],
            ['codigo' => 'proveedores.gestionar',        'nombre' => 'Gestionar proveedores'],
            // Pedidos / Cocina
            ['codigo' => 'pedidos.crear',                'nombre' => 'Crear pedidos'],
            ['codigo' => 'cocina.ver',                   'nombre' => 'Ver pantalla de cocina'],
            // Caja / Ventas
            ['codigo' => 'caja.cobrar',                  'nombre' => 'Cobrar'],
            ['codigo' => 'caja.cierres',                 'nombre' => 'Cierres de caja'],
            ['codigo' => 'ventas.facturacion',           'nombre' => 'Emitir facturas'],
            // Reportes
            ['codigo' => 'reportes.ver',                 'nombre' => 'Ver reportes'],
            ['codigo' => 'analytics.dashboard.ver',      'nombre' => 'Ver dashboard'],
            ['codigo' => 'reportes.ventas.ver',          'nombre' => 'Ver reportes de ventas'],
            ['codigo' => 'reportes.productos.ver',       'nombre' => 'Ver reportes de productos'],
            ['codigo' => 'reportes.inventario.ver',      'nombre' => 'Ver reportes de inventario'],
            ['codigo' => 'reportes.caja.ver',            'nombre' => 'Ver reportes de caja'],
            ['codigo' => 'reportes.cxc.ver',             'nombre' => 'Ver reportes de CxC'],
            ['codigo' => 'reportes.cxp.ver',             'nombre' => 'Ver reportes de CxP'],
            ['codigo' => 'reportes.kds.ver',             'nombre' => 'Ver reportes KDS'],
            ['codigo' => 'reportes.exportar',            'nombre' => 'Exportar reportes'],
            ['codigo' => 'analytics.query.ejecutar',     'nombre' => 'Ejecutar consultas analíticas'],
            // Reservas / Cotizaciones
            ['codigo' => 'reservas.gestionar',           'nombre' => 'Gestionar reservas'],
            ['codigo' => 'cotizaciones.gestionar',       'nombre' => 'Gestionar cotizaciones'],
            // Caja avanzadas
            ['codigo' => 'caja.aperturas.crear',         'nombre' => 'Abrir caja'],
            ['codigo' => 'caja.aperturas.ver',           'nombre' => 'Ver aperturas'],
            ['codigo' => 'caja.movimientos.crear',       'nombre' => 'Crear movimientos de caja'],
            ['codigo' => 'caja.depositos.crear',         'nombre' => 'Crear depósitos de caja'],
            ['codigo' => 'caja.cierre.crear',            'nombre' => 'Cerrar caja'],
            ['codigo' => 'pagos_venta.crear',            'nombre' => 'Registrar pagos de venta'],
            ['codigo' => 'pagos_venta.ver',              'nombre' => 'Ver pagos de venta'],
            ['codigo' => 'pagos_venta.anular',           'nombre' => 'Anular pagos de venta'],
            // Facturas electrónicas
            ['codigo' => 'facturas.ver',                'nombre' => 'Ver facturas'],
            ['codigo' => 'facturas.crear',              'nombre' => 'Crear facturas'],
            ['codigo' => 'facturas.editar',             'nombre' => 'Editar facturas'],
            ['codigo' => 'facturas.eliminar',           'nombre' => 'Eliminar facturas'],
            ['codigo' => 'facturas.emitir',             'nombre' => 'Emitir facturas'],
            ['codigo' => 'facturas.descargar',          'nombre' => 'Descargar facturas'],
            ['codigo' => 'facturas.enviar_email',       'nombre' => 'Enviar facturas por email'],
            ['codigo' => 'facturas.anular',             'nombre' => 'Anular facturas'],
            // SRI
            ['codigo' => 'sri.firma.configurar',        'nombre' => 'Configurar firma'],
            ['codigo' => 'sri.firma.ver',               'nombre' => 'Ver estado firma'],
            ['codigo' => 'sri.secuencias.ver',          'nombre' => 'Ver secuencias'],
            ['codigo' => 'sri.secuencias.next',         'nombre' => 'Generar secuencia'],
            ['codigo' => 'sri.establecimientos.ver',    'nombre' => 'Ver establecimientos'],
            ['codigo' => 'sri.establecimientos.crear',  'nombre' => 'Crear establecimientos'],
            ['codigo' => 'sri.establecimientos.editar', 'nombre' => 'Editar establecimientos'],
            ['codigo' => 'sri.establecimientos.eliminar','nombre' => 'Eliminar establecimientos'],
            ['codigo' => 'sri.estados.ver',             'nombre' => 'Consultar estado SRI'],
            ['codigo' => 'sri.callback.recibir',        'nombre' => 'Recibir callback SRI'],
            // Promociones & Descuentos
            ['codigo' => 'promociones.ver',             'nombre' => 'Ver promociones'],
            ['codigo' => 'promociones.crear',           'nombre' => 'Crear promociones'],
            ['codigo' => 'promociones.editar',          'nombre' => 'Editar promociones'],
            ['codigo' => 'promociones.eliminar',        'nombre' => 'Eliminar promociones'],
            ['codigo' => 'promociones.activar',         'nombre' => 'Activar promociones'],
            ['codigo' => 'promociones.desactivar',      'nombre' => 'Desactivar promociones'],
            ['codigo' => 'promociones.simular',         'nombre' => 'Simular promociones'],
            ['codigo' => 'promociones.aplicar',         'nombre' => 'Aplicar promociones'],
            ['codigo' => 'promociones.reglas.crear',    'nombre' => 'Gestionar reglas de promociones'],
            ['codigo' => 'cupones.ver',                 'nombre' => 'Ver cupones'],
            ['codigo' => 'cupones.crear',               'nombre' => 'Crear cupones'],
            ['codigo' => 'cupones.validar',             'nombre' => 'Validar cupones'],
            ['codigo' => 'cupones.anular',              'nombre' => 'Anular cupones'],
            ['codigo' => 'cupones.generar_masivo',      'nombre' => 'Generar cupones masivos'],
            ['codigo' => 'promociones.reportes.ver',    'nombre' => 'Ver reportes de promociones'],
            // CxC
            ['codigo' => 'cxc.ver',                    'nombre' => 'Ver CxC'],
            ['codigo' => 'cxc.pagar',                  'nombre' => 'Registrar pagos CxC'],
            ['codigo' => 'cxc.anular_pago',            'nombre' => 'Anular pagos CxC'],
            ['codigo' => 'cxc.ajustar',                'nombre' => 'Ajustar CxC'],
            ['codigo' => 'cxc.castigar',               'nombre' => 'Castigar CxC'],
            ['codigo' => 'cxc.planes',                 'nombre' => 'Gestionar planes CxC'],
            ['codigo' => 'cxc.aging',                  'nombre' => 'Reporte aging CxC'],
            ['codigo' => 'cxc.estado_cuenta',          'nombre' => 'Estado de cuenta CxC'],
            ['codigo' => 'cxc.dunning.ver',            'nombre' => 'Ver reglas dunning'],
            ['codigo' => 'cxc.dunning.crear',          'nombre' => 'Configurar dunning'],
            ['codigo' => 'cxc.dunning.enviar',         'nombre' => 'Enviar dunning'],
            // CxP
            ['codigo' => 'cxp.ver',                    'nombre' => 'Ver CxP'],
            ['codigo' => 'cxp.pagar',                  'nombre' => 'Registrar pagos CxP'],
            ['codigo' => 'cxp.anular_pago',            'nombre' => 'Anular pagos CxP'],
            ['codigo' => 'cxp.lotes_pago',             'nombre' => 'Gestionar lotes de pago'],
            ['codigo' => 'cxp.aging',                  'nombre' => 'Reporte aging CxP'],
            ['codigo' => 'cxp.estado_cuenta',          'nombre' => 'Estado de cuenta CxP'],
            // Tesorería
            ['codigo' => 'tesoreria.bancos.ver',       'nombre' => 'Ver cuentas bancarias'],
            ['codigo' => 'tesoreria.bancos.editar',    'nombre' => 'Editar cuentas bancarias'],
            ['codigo' => 'tesoreria.conciliaciones.ver',   'nombre' => 'Ver conciliaciones'],
            ['codigo' => 'tesoreria.conciliaciones.match', 'nombre' => 'Confirmar conciliaciones'],
            ['codigo' => 'tesoreria.conciliaciones.cerrar','nombre' => 'Cerrar conciliaciones'],
            ['codigo' => 'tesoreria.tarjetas.ver',     'nombre' => 'Ver settlements tarjetas'],
            ['codigo' => 'tesoreria.tarjetas.conciliar','nombre' => 'Conciliar tarjetas'],
        ];

        foreach ($permisos as $p) {
            DB::table('permisos')->updateOrInsert(
                ['codigo' => $p['codigo']],
                ['nombre' => $p['nombre']]
            );
        }

        // Asignación mínima de permisos por rol (ajusta a tu gusto)
        $map = [
            'SUPER_ADMIN' => array_column($permisos, 'codigo'),
            'ADMIN_LOCAL' => [
                'usuarios.ver','usuarios.editar','roles.ver','permisos.ver',
                'config.usuarios.gestionar','config.ver','config.editar','locales.config.ver','locales.config.editar',
                'productos.ver','productos.crear_editar','inventario.movimientos',
                'inventario.stock.ver','inventario.movimientos.ver','inventario.ajustes.crear',
                'inventario.transferencias.crear','inventario.transferencias.ver','inventario.transferencias.recibir',
                'inventario.transferencias.cancelar','inventario.conteos.crear','inventario.conteos.capturas',
                'inventario.conteos.cerrar','inventario.conteos.ver','inventario.lotes.ver','inventario.alertas.ver',
                'inventario.produccion.crear','inventario.mermas.crear','inventario.costos.ver','inventario.costos.recalcular',
                'proveedores.gestionar','pedidos.crear','cocina.ver','caja.cobrar','caja.cierres',
                'ventas.facturacion','reportes.ver','reservas.gestionar','cotizaciones.gestionar',
                'caja.aperturas.crear','caja.aperturas.ver','caja.movimientos.crear','caja.depositos.crear',
                'caja.cierre.crear','pagos_venta.crear','pagos_venta.ver','pagos_venta.anular',
                'facturas.ver','facturas.crear','facturas.editar','facturas.eliminar','facturas.emitir',
                'facturas.descargar','facturas.enviar_email','facturas.anular',
                'sri.firma.configurar','sri.firma.ver','sri.secuencias.ver','sri.secuencias.next',
                'sri.establecimientos.ver','sri.establecimientos.crear','sri.establecimientos.editar','sri.establecimientos.eliminar',
                'sri.estados.ver','sri.callback.recibir',
                'promociones.ver','promociones.crear','promociones.editar','promociones.eliminar','promociones.activar','promociones.desactivar','promociones.simular','promociones.aplicar','promociones.reglas.crear',
                'cupones.ver','cupones.crear','cupones.validar','cupones.anular','cupones.generar_masivo','promociones.reportes.ver',
                'analytics.dashboard.ver','reportes.ventas.ver','reportes.productos.ver','reportes.inventario.ver',
                'reportes.caja.ver','reportes.cxc.ver','reportes.cxp.ver','reportes.kds.ver','reportes.exportar','analytics.query.ejecutar'
            ],
            'FINANZAS' => [
                'cxc.ver','cxc.pagar','cxc.anular_pago','cxc.ajustar','cxc.castigar','cxc.planes','cxc.aging','cxc.estado_cuenta','cxc.dunning.ver','cxc.dunning.crear','cxc.dunning.enviar',
                'cxp.ver','cxp.pagar','cxp.anular_pago','cxp.lotes_pago','cxp.aging','cxp.estado_cuenta',
                'tesoreria.bancos.ver','tesoreria.bancos.editar','tesoreria.conciliaciones.ver','tesoreria.conciliaciones.match','tesoreria.conciliaciones.cerrar','tesoreria.tarjetas.ver','tesoreria.tarjetas.conciliar',
                'analytics.dashboard.ver','reportes.ventas.ver','reportes.productos.ver','reportes.inventario.ver','reportes.caja.ver','reportes.cxc.ver','reportes.cxp.ver','reportes.exportar','analytics.query.ejecutar'
            ],
            'BODEGA'      => [
                'inventario.stock.ver','inventario.movimientos.ver','inventario.ajustes.crear',
                'inventario.transferencias.crear','inventario.transferencias.ver','inventario.transferencias.recibir',
                'inventario.transferencias.cancelar','inventario.conteos.crear','inventario.conteos.capturas',
                'inventario.conteos.cerrar','inventario.conteos.ver','inventario.lotes.ver','inventario.alertas.ver',
                'inventario.produccion.crear','inventario.mermas.crear','inventario.costos.ver','inventario.costos.recalcular'
            ],
            'MESERO'      => ['pedidos.crear','productos.ver','reservas.gestionar'],
            'CAJERO'      => ['caja.cobrar','caja.cierres','ventas.facturacion','reportes.ver',
                'caja.aperturas.crear','caja.aperturas.ver','caja.movimientos.crear','caja.depositos.crear',
                'caja.cierre.crear','pagos_venta.crear','pagos_venta.ver','pagos_venta.anular',
                'facturas.ver','facturas.crear','facturas.editar','facturas.emitir','facturas.descargar',
                'facturas.enviar_email','facturas.anular',
                'sri.secuencias.ver','sri.secuencias.next','sri.estados.ver',
                'promociones.ver','promociones.simular','promociones.aplicar','cupones.validar',
                'cxc.ver','cxc.pagar','cxc.anular_pago',
                'analytics.dashboard.ver','reportes.ventas.ver','reportes.productos.ver','reportes.inventario.ver',
                'reportes.caja.ver','reportes.kds.ver','reportes.exportar','analytics.query.ejecutar'
            ],
            'MARKETING'   => [
                'promociones.ver','promociones.crear','promociones.editar','promociones.eliminar',
                'promociones.activar','promociones.desactivar','promociones.reglas.crear',
                'cupones.ver','cupones.crear','cupones.anular','cupones.generar_masivo',
                'promociones.reportes.ver',
                'analytics.dashboard.ver','reportes.ventas.ver','reportes.productos.ver','reportes.exportar','analytics.query.ejecutar'
            ],
            'COCINA'      => ['cocina.ver'],
        ];

        $rolesDb = DB::table('roles')->pluck('id','codigo');
        $permisosDb = DB::table('permisos')->pluck('id','codigo');

        foreach ($map as $rolCodigo => $permList) {
            $rolId = $rolesDb[$rolCodigo] ?? null;
            if (!$rolId) continue;
            foreach ($permList as $permCodigo) {
                $permId = $permisosDb[$permCodigo] ?? null;
                if (!$permId) continue;
                DB::table('rol_permisos')->updateOrInsert(
                    ['rol_id' => $rolId, 'permiso_id' => $permId],
                    []
                );
            }
        }
    }
}
