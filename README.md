# VendePro API

API del sistema VendePro. A continuación se listan los endpoints disponibles y una breve descripción de su funcionalidad.

## Colección de Postman
Puedes importar el archivo `apis.json` en Postman para probar todos los endpoints como una colección. El archivo define la variable `base_url` que puedes ajustar según tu entorno.

## Autenticación
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| POST | `/v1/auth/login` | Inicia sesión y devuelve un token JWT. |
| POST | `/v1/auth/logout` | Cierra la sesión del usuario autenticado. |
| GET  | `/v1/auth/me` | Obtiene los datos del usuario autenticado. |
| POST | `/v1/auth/refresh` | Renueva el token JWT. |
| POST | `/v1/auth/change-password` | Cambia la contraseña del usuario. |
| POST | `/v1/auth/force-invalidate` | Invalida todos los tokens activos del usuario. |

## Menú
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/menu` | Devuelve la estructura del menú principal. |

## Configuración
### Empresa
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/config/empresa` | Obtener parámetros generales de la empresa | `config.ver` |
| PUT | `/v1/config/empresa` | Actualizar parámetros generales de la empresa | `config.editar` |

Ejemplo:

`PUT /v1/config/empresa`
```json
{
  "nombre_comercial": "Mi Restaurante"
}
```
Respuesta:
```json
{
  "data": {
    "nombre_comercial": "Mi Restaurante"
  }
}
```

### Locales
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/locales/{id}/config` | Obtener configuración de un local | `locales.config.ver` |
| PUT | `/v1/locales/{id}/config` | Actualizar configuración de un local | `locales.config.editar` |

Nota: la configuración definida en un local prevalece sobre la configuración de la empresa cuando exista.

## Pedidos
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/pedidos` | Lista todos los pedidos. |
| POST | `/v1/pedidos` | Crea un nuevo pedido. |
| GET | `/v1/pedidos/{id}` | Muestra la información de un pedido. |
| PUT | `/v1/pedidos/{id}` | Actualiza un pedido existente. |
| DELETE | `/v1/pedidos/{id}` | Elimina un pedido. |

## Cuentas
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/cuentas` | Lista todas las cuentas. |
| POST | `/v1/cuentas` | Crea una nueva cuenta. |
| GET | `/v1/cuentas/{id}` | Muestra la información de una cuenta. |
| PUT | `/v1/cuentas/{id}` | Actualiza una cuenta existente. |
| DELETE | `/v1/cuentas/{id}` | Elimina una cuenta. |
| POST | `/v1/cuentas/{id}/items` | Agrega un item a la cuenta. |

## Reservas
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/reservas` | Lista todas las reservas. |
| POST | `/v1/reservas` | Crea una nueva reserva. |
| GET | `/v1/reservas/{id}` | Muestra la información de una reserva. |
| PUT | `/v1/reservas/{id}` | Actualiza una reserva existente. |
| DELETE | `/v1/reservas/{id}` | Elimina una reserva. |
| POST | `/v1/reservas/{id}/confirmar` | Confirma una reserva. |
| POST | `/v1/reservas/{id}/cancelar` | Cancela una reserva. |
| POST | `/v1/reservas/{id}/no-show` | Marca una reserva como no asistida. |

## Recursos protegidos (requieren autenticación y suscripción activa)

### Empresas
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/empresas` | Lista empresas registradas. |
| POST | `/v1/empresas` | Crea una nueva empresa. |
| GET | `/v1/empresas/{id}` | Muestra los datos de una empresa. |
| PUT | `/v1/empresas/{id}` | Actualiza una empresa. |
| DELETE | `/v1/empresas/{id}` | Elimina una empresa. |

### Locales
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/locales` | Lista locales registrados. |
| POST | `/v1/locales` | Crea un nuevo local. |
| GET | `/v1/locales/{id}` | Muestra los datos de un local. |
| PUT | `/v1/locales/{id}` | Actualiza un local. |
| DELETE | `/v1/locales/{id}` | Elimina un local. |

### Suscripciones
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/suscripciones` | Lista suscripciones. |
| POST | `/v1/suscripciones` | Crea una suscripción. |
| GET | `/v1/suscripciones/{id}` | Muestra los datos de una suscripción. |
| PUT | `/v1/suscripciones/{id}` | Actualiza una suscripción. |

### Suscripciones por local
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/suscripciones-locales` | Lista la relación suscripción-local. |
| POST | `/v1/suscripciones-locales` | Crea una relación suscripción-local. |
| GET | `/v1/suscripciones-locales/{id}` | Muestra una relación suscripción-local. |
| PUT | `/v1/suscripciones-locales/{id}` | Actualiza una relación suscripción-local. |

### Usuarios
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/usuarios` | Lista usuarios. | `usuarios.ver` |
| POST | `/v1/usuarios` | Crea un usuario. | `usuarios.crear` |
| GET | `/v1/usuarios/{id}` | Muestra los datos de un usuario. | `usuarios.ver` |
| PUT | `/v1/usuarios/{id}` | Actualiza un usuario. | `usuarios.editar` |
| DELETE | `/v1/usuarios/{id}` | Elimina un usuario. | `usuarios.eliminar` |
| POST | `/v1/usuarios/{id}/roles` | Asigna roles a un usuario. | `usuarios.asignar_roles` |

### Roles
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/roles` | Lista roles disponibles. | `roles.ver` |
| POST | `/v1/roles` | Crea un rol. | `roles.crear` |
| GET | `/v1/roles/{id}` | Muestra los datos de un rol. | `roles.ver` |
| PUT | `/v1/roles/{id}` | Actualiza un rol. | `roles.editar` |
| DELETE | `/v1/roles/{id}` | Elimina un rol. | `roles.eliminar` |
| POST | `/v1/roles/{id}/permisos` | Asigna permisos a un rol. | `roles.asignar_permisos` |

### Permisos
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/permisos` | Lista permisos disponibles. | `permisos.ver` |
| POST | `/v1/permisos` | Crea un permiso. | `permisos.crear` |
| GET | `/v1/permisos/{id}` | Muestra los datos de un permiso. | `permisos.ver` |
| PUT | `/v1/permisos/{id}` | Actualiza un permiso. | `permisos.editar` |
| DELETE | `/v1/permisos/{id}` | Elimina un permiso. | `permisos.eliminar` |

## Matriz RBAC
| Permiso | admin | supervisor | bodega |
| ------- | :---: | :--------: | :----: |
| usuarios.ver | ✓ | ✓ | — |
| usuarios.crear | ✓ | — | — |
| usuarios.editar | ✓ | ✓ | — |
| usuarios.eliminar | ✓ | — | — |
| usuarios.asignar_roles | ✓ | — | — |
| roles.ver | ✓ | ✓ | — |
| roles.crear | ✓ | — | — |
| roles.editar | ✓ | — | — |
| roles.eliminar | ✓ | — | — |
| roles.asignar_permisos | ✓ | — | — |
| permisos.ver | ✓ | ✓ | — |
| permisos.crear | ✓ | — | — |
| permisos.editar | ✓ | — | — |
| permisos.eliminar | ✓ | — | — |
| facturas.ver | ✓ | ✓ | ✓ |
| facturas.crear | ✓ | ✓ | ✓ |
| facturas.editar | ✓ | ✓ | ✓ |
| facturas.eliminar | ✓ | ✓ | — |
| facturas.emitir | ✓ | ✓ | ✓ |
| facturas.descargar | ✓ | ✓ | ✓ |
| facturas.enviar_email | ✓ | ✓ | ✓ |
| facturas.anular | ✓ | ✓ | ✓ |
| sri.firma.configurar | ✓ | — | — |
| sri.firma.ver | ✓ | ✓ | — |
| sri.secuencias.ver | ✓ | ✓ | ✓ |
| sri.secuencias.next | ✓ | ✓ | ✓ |
| sri.establecimientos.ver | ✓ | ✓ | — |
| sri.establecimientos.crear | ✓ | — | — |
| sri.establecimientos.editar | ✓ | — | — |
| sri.establecimientos.eliminar | ✓ | — | — |
| sri.estados.ver | ✓ | ✓ | ✓ |
| sri.callback.recibir | ✓ | — | — |
| inventario.stock.ver | ✓ | ✓ | ✓ |
| inventario.movimientos.ver | ✓ | ✓ | ✓ |
| inventario.ajustes.crear | ✓ | ✓ | ✓ |
| inventario.transferencias.crear | ✓ | ✓ | ✓ |
| inventario.transferencias.ver | ✓ | ✓ | ✓ |
| inventario.transferencias.recibir | ✓ | ✓ | ✓ |
| inventario.transferencias.cancelar | ✓ | ✓ | ✓ |
| inventario.conteos.crear | ✓ | ✓ | ✓ |
| inventario.conteos.capturas | ✓ | ✓ | ✓ |
| inventario.conteos.cerrar | ✓ | ✓ | ✓ |
| inventario.conteos.ver | ✓ | ✓ | ✓ |
| inventario.lotes.ver | ✓ | ✓ | ✓ |
| inventario.alertas.ver | ✓ | ✓ | ✓ |
| inventario.produccion.crear | ✓ | ✓ | ✓ |
| inventario.mermas.crear | ✓ | ✓ | ✓ |
| inventario.costos.ver | ✓ | ✓ | ✓ |
| inventario.costos.recalcular | ✓ | ✓ | ✓ |

### Unidades de medida
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/unidades` | Lista unidades de medida. |
| POST | `/v1/unidades` | Crea una unidad de medida. |
| GET | `/v1/unidades/{id}` | Muestra una unidad de medida. |
| PUT | `/v1/unidades/{id}` | Actualiza una unidad de medida. |
| DELETE | `/v1/unidades/{id}` | Elimina una unidad de medida. |

### Impuestos
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/impuestos` | Lista impuestos. |
| POST | `/v1/impuestos` | Crea un impuesto. |
| GET | `/v1/impuestos/{id}` | Muestra un impuesto. |
| PUT | `/v1/impuestos/{id}` | Actualiza un impuesto. |
| DELETE | `/v1/impuestos/{id}` | Elimina un impuesto. |

### Métodos de pago
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/metodos-pago` | Lista métodos de pago. |
| POST | `/v1/metodos-pago` | Crea un método de pago. |
| GET | `/v1/metodos-pago/{id}` | Muestra un método de pago. |
| PUT | `/v1/metodos-pago/{id}` | Actualiza un método de pago. |
| DELETE | `/v1/metodos-pago/{id}` | Elimina un método de pago. |

### Categorías de productos
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/categorias` | Lista categorías de productos. |
| POST | `/v1/categorias` | Crea una categoría de producto. |
| GET | `/v1/categorias/{id}` | Muestra una categoría de producto. |
| PUT | `/v1/categorias/{id}` | Actualiza una categoría de producto. |
| DELETE | `/v1/categorias/{id}` | Elimina una categoría de producto. |

### Productos
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/productos` | Lista productos. |
| POST | `/v1/productos` | Crea un producto. |
| GET | `/v1/productos/{id}` | Muestra un producto. |
| PUT | `/v1/productos/{id}` | Actualiza un producto. |
| DELETE | `/v1/productos/{id}` | Elimina un producto. |
| GET | `/v1/productos/{id}/receta` | Lista los insumos de la receta. |
| POST | `/v1/productos/{id}/receta` | Agrega un insumo a la receta. |
| DELETE | `/v1/productos/{id}/receta/{insumo_id}` | Elimina un insumo de la receta. |
| POST | `/v1/productos/import` | Importa productos masivamente. |

### Clientes
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/clientes` | Lista clientes. |
| POST | `/v1/clientes` | Crea un cliente. |
| GET | `/v1/clientes/{id}` | Muestra un cliente. |
| PUT | `/v1/clientes/{id}` | Actualiza un cliente. |
| DELETE | `/v1/clientes/{id}` | Elimina un cliente. |

### Proveedores
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/proveedores` | Lista proveedores. |
| POST | `/v1/proveedores` | Crea un proveedor. |
| GET | `/v1/proveedores/{id}` | Muestra un proveedor. |
| PUT | `/v1/proveedores/{id}` | Actualiza un proveedor. |
| DELETE | `/v1/proveedores/{id}` | Elimina un proveedor. |

### Compras
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/compras` | Lista compras realizadas. |
| POST | `/v1/compras` | Registra una compra. |
| GET | `/v1/compras/{id}` | Muestra una compra. |
| PUT | `/v1/compras/{id}` | Actualiza una compra. |
| DELETE | `/v1/compras/{id}` | Elimina una compra. |
| POST | `/v1/compras/{id}/aprobar` | Aprueba una compra. |

### Cuentas por pagar y pagos a proveedores
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/cxp` | Lista cuentas por pagar. |
| GET | `/v1/cxp/{id}` | Muestra una cuenta por pagar. |
| POST | `/v1/pagos-proveedor` | Registra un pago a proveedor. |

### Bodegas
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/bodegas` | Lista bodegas. |
| POST | `/v1/bodegas` | Crea una bodega. |
| GET | `/v1/bodegas/{id}` | Muestra una bodega. |
| PUT | `/v1/bodegas/{id}` | Actualiza una bodega. |
| DELETE | `/v1/bodegas/{id}` | Elimina una bodega. |

### Mesas
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/mesas` | Lista mesas disponibles. |
| POST | `/v1/mesas` | Crea una mesa. |
| GET | `/v1/mesas/{id}` | Muestra una mesa. |
| PUT | `/v1/mesas/{id}` | Actualiza una mesa. |
| DELETE | `/v1/mesas/{id}` | Elimina una mesa. |

### Ventas - Facturas
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/ventas/facturas` | Lista facturas de venta. |
| POST | `/v1/ventas/facturas` | Crea una factura de venta. |
| GET | `/v1/ventas/facturas/{id}` | Muestra una factura de venta. |
| PUT | `/v1/ventas/facturas/{id}` | Actualiza una factura de venta. |
| DELETE | `/v1/ventas/facturas/{id}` | Elimina una factura de venta. |
| POST | `/v1/ventas/facturas/{id}/aprobar` | Aprueba una factura. |
| POST | `/v1/ventas/facturas/{id}/anular` | Anula una factura. |

### Cuentas por cobrar
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/cxc` | Lista cuentas por cobrar. |
| GET | `/v1/cxc/{id}` | Muestra una cuenta por cobrar. |
| POST | `/v1/cxc/pagos` | Registra un pago. |
| GET | `/v1/cxc/{id}/pagos` | Lista los pagos de una cuenta por cobrar. |

### Notas de crédito
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/ventas/notas-credito` | Lista notas de crédito. |
| POST | `/v1/ventas/notas-credito` | Crea una nota de crédito. |
| GET | `/v1/ventas/notas-credito/{id}` | Muestra una nota de crédito. |
| POST | `/v1/ventas/notas-credito/{id}/aplicar` | Aplica una nota de crédito. |
| POST | `/v1/ventas/notas-credito/{id}/anular` | Anula una nota de crédito. |

## Otros
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/estado-suscripcion` | Devuelve el estado de la suscripción actual. |
| POST | `/v1/sri/secuencias/next` | Genera la siguiente secuencia para documentos SRI. |


## Caja
| Método | Ruta | Descripción | Permiso requerido |
| ------ | ---- | ----------- | ----------------- |
| POST | `/v1/caja/aperturas` | Abrir caja | `caja.aperturas.crear` |
| GET | `/v1/caja/aperturas` | Listar aperturas | `caja.aperturas.ver` |
| GET | `/v1/caja/aperturas/{id}` | Ver apertura | `caja.aperturas.ver` |
| POST | `/v1/caja/movimientos` | Registrar movimiento | `caja.movimientos.crear` |
| GET | `/v1/caja/movimientos` | Listar movimientos | `caja.aperturas.ver` |
| POST | `/v1/caja/deposito` | Registrar depósito bancario | `caja.depositos.crear` |
| POST | `/v1/caja/cierre` | Cerrar caja | `caja.cierre.crear` |
| GET | `/v1/caja/estado` | Estado rápido de caja | `caja.aperturas.ver` |

## Pagos de Venta
| Método | Ruta | Descripción | Permiso requerido |
| ------ | ---- | ----------- | ----------------- |
| POST | `/v1/pagos-venta` | Registrar pago de venta | `pagos_venta.crear` |
| GET | `/v1/pagos-venta` | Listar pagos de venta | `pagos_venta.ver` |
| GET | `/v1/pagos-venta/{id}` | Ver pago de venta | `pagos_venta.ver` |
| POST | `/v1/pagos-venta/{id}/anular` | Anular pago de venta | `pagos_venta.anular` |

### Ejemplo Apertura de Caja
```json
POST /v1/caja/aperturas
{
  "local_id": 1,
  "caja_id": 5,
  "usuario_id": 41,
  "saldo_inicial": 100.00
}
```
Respuesta 201:
```json
{"data":{"id":1,"estado":"abierta"}}
```

### Ejemplo Pago Mixto
```json
POST /v1/pagos-venta
{
  "factura_id": 9876,
  "items_pago": [{"metodo":"efectivo","monto":20.00}],
  "caja": {"apertura_id": 10}
}
```

## Facturas
| Método | Ruta | Descripción | Estado esperado | Permiso |
| ------ | ---- | ----------- | --------------- | ------- |
| GET | `/v1/facturas` | Lista facturas | 200 | `facturas.ver` |
| POST | `/v1/facturas` | Crea BORRADOR | 201 | `facturas.crear` |
| GET | `/v1/facturas/{id}` | Detalle de factura | 200 | `facturas.ver` |
| PUT | `/v1/facturas/{id}` | Actualiza borrador | 200 | `facturas.editar` |
| DELETE | `/v1/facturas/{id}` | Elimina borrador | 204 | `facturas.eliminar` |
| POST | `/v1/facturas/{id}/emitir` | Emite y envía a SRI | 200 | `facturas.emitir` |
| POST | `/v1/facturas/{id}/reintentar-envio` | Reintenta envío SRI | 200 | `facturas.emitir` |
| GET | `/v1/facturas/{id}/estado-sri` | Consulta estado en SRI | 200 | `facturas.ver` |
| GET | `/v1/facturas/{id}/xml` | Descarga XML | 200 | `facturas.descargar` |
| GET | `/v1/facturas/{id}/pdf` | Descarga PDF | 200 | `facturas.descargar` |
| POST | `/v1/facturas/{id}/email` | Envía por email | 200 | `facturas.enviar_email` |
| POST | `/v1/facturas/{id}/anular` | Anula factura | 200 | `facturas.anular` |

## Inventario

### Stock
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/stock` | Consultar stock por bodega y producto. | `inventario.stock.ver` |
| GET | `/v1/stock/kardex` | Ver kardex del producto. | `inventario.stock.ver` |

### Movimientos
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/inventario/movimientos` | Listar movimientos de inventario. | `inventario.movimientos.ver` |
| GET | `/v1/inventario/movimientos/{id}` | Detalle de un movimiento. | `inventario.movimientos.ver` |

### Ajustes
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| POST | `/v1/inventario/ajustes` | Crear ajuste de inventario. | `inventario.ajustes.crear` |

### Transferencias
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| POST | `/v1/inventario/transferencias` | Crear transferencia entre bodegas. | `inventario.transferencias.crear` |
| GET | `/v1/inventario/transferencias` | Listar transferencias. | `inventario.transferencias.ver` |
| GET | `/v1/inventario/transferencias/{id}` | Detalle de transferencia. | `inventario.transferencias.ver` |
| POST | `/v1/inventario/transferencias/{id}/recibir` | Recibir transferencia. | `inventario.transferencias.recibir` |
| POST | `/v1/inventario/transferencias/{id}/cancelar` | Cancelar transferencia. | `inventario.transferencias.cancelar` |

### Conteos
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| POST | `/v1/inventario/conteos` | Crear conteo de inventario. | `inventario.conteos.crear` |
| POST | `/v1/inventario/conteos/{id}/capturas` | Registrar capturas de conteo. | `inventario.conteos.capturas` |
| POST | `/v1/inventario/conteos/{id}/cerrar` | Cerrar conteo. | `inventario.conteos.cerrar` |
| GET | `/v1/inventario/conteos` | Listar conteos. | `inventario.conteos.ver` |
| GET | `/v1/inventario/conteos/{id}` | Ver conteo. | `inventario.conteos.ver` |

### Lotes
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/inventario/lotes` | Listar lotes y caducidades. | `inventario.lotes.ver` |
| GET | `/v1/inventario/alertas` | Alertas de inventario. | `inventario.alertas.ver` |

### Producción y Mermas
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| POST | `/v1/inventario/produccion` | Registrar producción. | `inventario.produccion.crear` |
| POST | `/v1/inventario/mermas` | Registrar merma. | `inventario.mermas.crear` |

### Costos
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/inventario/costos` | Consultar costos de inventario. | `inventario.costos.ver` |
| POST | `/v1/inventario/recalcular-costos` | Recalcular costos. | `inventario.costos.recalcular` |

## Promociones & Descuentos

### Promos
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/promociones` | Lista promociones. | `promociones.ver` |
| POST | `/v1/promociones` | Crear promoción. | `promociones.crear` |
| GET | `/v1/promociones/{id}` | Ver detalle de promoción. | `promociones.ver` |
| PUT | `/v1/promociones/{id}` | Actualizar promoción. | `promociones.editar` |
| DELETE | `/v1/promociones/{id}` | Eliminar promoción. | `promociones.eliminar` |
| POST | `/v1/promociones/{id}/activar` | Activar promoción. | `promociones.activar` |
| POST | `/v1/promociones/{id}/desactivar` | Desactivar promoción. | `promociones.desactivar` |
| POST | `/v1/promociones/{id}/duplicar` | Duplicar promoción. | `promociones.crear` |

### Reglas y Combos
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| POST | `/v1/promociones/{id}/reglas` | Agregar regla avanzada. | `promociones.reglas.crear` |
| POST | `/v1/promociones/{id}/combo` | Definir combo. | `promociones.reglas.crear` |

### Simulación y Aplicación
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| POST | `/v1/promociones/simular` | Simular promociones sobre un carrito. | `promociones.simular` |
| POST | `/v1/promociones/aplicar` | Aplicar promociones a un pedido. | `promociones.aplicar` |

### Cupones
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/cupones` | Listar cupones. | `cupones.ver` |
| POST | `/v1/cupones` | Crear cupón. | `cupones.crear` |
| POST | `/v1/cupones/generar-masivo` | Generar cupones masivos. | `cupones.generar_masivo` |
| POST | `/v1/cupones/validar` | Validar cupón. | `cupones.validar` |
| POST | `/v1/cupones/{id}/anular` | Anular cupón. | `cupones.anular` |

### Reportes
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/promociones/efectividad` | Reporte de efectividad. | `promociones.reportes.ver` |

#### Orden de evaluación
1. Calcular precio base por ítem.
2. Evaluar reglas por prioridad descendente.
3. Resolver conflictos priorizando exclusivas y respetando `stackable=false`.
4. Aplicar recompensas primero a nivel de ítem y luego de orden.
5. Recalcular impuestos sobre precios descontados.
6. Redondear a 2 decimales (half-up) y registrar el log.

### Matriz RBAC
| Permiso | admin | marketing | cajero |
| ------- | :---: | :-------: | :----: |
| promociones.ver | ✓ | ✓ | ✓ |
| promociones.crear | ✓ | ✓ | — |
| promociones.editar | ✓ | ✓ | — |
| promociones.eliminar | ✓ | ✓ | — |
| promociones.activar | ✓ | ✓ | — |
| promociones.desactivar | ✓ | ✓ | — |
| promociones.simular | ✓ | — | ✓ |
| promociones.aplicar | ✓ | — | ✓ |
| promociones.reglas.crear | ✓ | ✓ | — |
| cupones.ver | ✓ | ✓ | — |
| cupones.crear | ✓ | ✓ | — |
| cupones.validar | ✓ | — | ✓ |
| cupones.anular | ✓ | ✓ | — |
| cupones.generar_masivo | ✓ | ✓ | — |
| promociones.reportes.ver | ✓ | ✓ | — |

## CxC
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/cxc` | Listar documentos por cobrar. | `cxc.ver` |
| GET | `/v1/cxc/{id}` | Ver detalle de un documento. | `cxc.ver` |
| GET | `/v1/cxc/saldos` | Consultar saldos de un cliente. | `cxc.ver` |
| POST | `/v1/pagos-cxc` | Registrar pago de CxC. | `cxc.pagar` |
| POST | `/v1/pagos-cxc/{id}/anular` | Anular pago de CxC. | `cxc.anular_pago` |
| GET | `/v1/cxc/{id}/pagos` | Historial de pagos. | `cxc.ver` |

Nota: se mantiene alias `POST /v1/cxc/pagos` (deprecado).

## CxP
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/cxp` | Listar cuentas por pagar. | `cxp.ver` |
| GET | `/v1/cxp/{id}` | Ver detalle de CxP. | `cxp.ver` |
| GET | `/v1/cxp/{id}/pagos` | Historial de pagos. | `cxp.ver` |
| POST | `/v1/pagos-cxp` | Registrar pago de CxP. | `cxp.pagar` |
| POST | `/v1/pagos-cxp/{id}/anular` | Anular pago de CxP. | `cxp.anular_pago` |

Nota: se mantiene alias `POST /v1/pagos-proveedor` (deprecado).

## Conciliaciones
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/tesoreria/cuentas-bancarias` | Listar cuentas bancarias. | `tesoreria.bancos.ver` |
| POST | `/v1/tesoreria/cuentas-bancarias` | Crear cuenta bancaria. | `tesoreria.bancos.editar` |
| POST | `/v1/tesoreria/conciliaciones/estados` | Importar estado de cuenta. | `tesoreria.conciliaciones.ver` |
| POST | `/v1/tesoreria/conciliaciones/{id}/match` | Confirmar match. | `tesoreria.conciliaciones.match` |
| POST | `/v1/tesoreria/conciliaciones/{id}/cerrar` | Cerrar conciliación. | `tesoreria.conciliaciones.cerrar` |
| POST | `/v1/tesoreria/tarjetas/settlements` | Importar settlements de tarjetas. | `tesoreria.tarjetas.ver` |

### Matriz RBAC
| Permiso | admin | finanzas | cajero |
| ------- | :---: | :------: | :----: |
| cxc.ver | ✓ | ✓ | ✓ |
| cxc.pagar | ✓ | ✓ | ✓ |
| cxc.anular_pago | ✓ | ✓ | ✓ |
| cxp.ver | ✓ | ✓ | — |
| cxp.pagar | ✓ | ✓ | — |
| cxp.anular_pago | ✓ | ✓ | — |
| tesoreria.bancos.ver | ✓ | ✓ | — |
| tesoreria.bancos.editar | ✓ | ✓ | — |
| tesoreria.conciliaciones.ver | ✓ | ✓ | — |
| tesoreria.conciliaciones.match | ✓ | ✓ | — |
| tesoreria.conciliaciones.cerrar | ✓ | ✓ | — |
| tesoreria.tarjetas.ver | ✓ | ✓ | — |
| tesoreria.tarjetas.conciliar | ✓ | ✓ | — |

## SRI
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| POST | `/v1/sri/firma/configurar` | Configurar certificado de firma | `sri.firma.configurar` |
| GET | `/v1/sri/firma/estado` | Estado del certificado | `sri.firma.ver` |
| GET | `/v1/sri/secuencias` | Consultar secuencias | `sri.secuencias.ver` |
| POST | `/v1/sri/secuencias/next` | Siguiente secuencia | `sri.secuencias.next` |
| GET | `/v1/sri/establecimientos` | Listar establecimientos | `sri.establecimientos.ver` |
| POST | `/v1/sri/establecimientos` | Crear establecimiento | `sri.establecimientos.crear` |
| GET | `/v1/sri/establecimientos/{id}` | Ver establecimiento | `sri.establecimientos.ver` |
| PUT | `/v1/sri/establecimientos/{id}` | Actualizar establecimiento | `sri.establecimientos.editar` |
| DELETE | `/v1/sri/establecimientos/{id}` | Eliminar establecimiento | `sri.establecimientos.eliminar` |
| GET | `/v1/sri/estados/{clave_acceso}` | Estado por clave de acceso | `sri.estados.ver` |
| POST | `/v1/sri/callback` | Webhook de notificación | `sri.callback.recibir` |

### Códigos de error
- 401 No autorizado
- 403 Prohibido
- 409 Conflicto
- 422 Datos inválidos

### Matriz RBAC
| Permiso | admin | supervisor | bodega |
| ------- | ----- | ---------- | ------ |
| config.ver | ✓ | ✓ | — |
| config.editar | ✓ | ✓ | — |
| locales.config.ver | ✓ | ✓ | — |
| locales.config.editar | ✓ | ✓ | — |
| caja.aperturas.crear | ✓ | ✓ | ✓ |
| caja.aperturas.ver | ✓ | ✓ | ✓ |
| caja.movimientos.crear | ✓ | ✓ | ✓ |
| caja.depositos.crear | ✓ | ✓ | ✓ |
| caja.cierre.crear | ✓ | ✓ | ✓ |
| pagos_venta.crear | ✓ | ✓ | ✓ |
| pagos_venta.ver | ✓ | ✓ | ✓ |
| pagos_venta.anular | ✓ | ✓ | ✓ |

## Reportes & Analytics

### Dashboard / KPIs
| Método | Ruta | Descripción | Filtros | Permiso |
| ------ | ---- | ----------- | ------- | ------- |
| GET | `/v1/dashboard/resumen` | Resumen diario de KPIs con comparación | `fecha`, `local_id`, `comparar_con` | `analytics.dashboard.ver` |
| GET | `/v1/analytics/kpis` | Serie temporal de KPIs | `desde`, `hasta`, `local_id`, `granularidad` | `analytics.dashboard.ver` |

### Ventas
| Método | Ruta | Descripción | Filtros | Permiso |
| ------ | ---- | ----------- | ------- | ------- |
| GET | `/v1/reportes/ventas-dia` | Ventas del día por hora/local/canal | `fecha`, `local_id`, `canal` | `reportes.ventas.ver` |
| GET | `/v1/reportes/ventas` | Agregados de ventas | `desde`, `hasta`, `group_by` | `reportes.ventas.ver` |

### Productos / Categorías
| Método | Ruta | Descripción | Filtros | Permiso |
| ------ | ---- | ----------- | ------- | ------- |
| GET | `/v1/reportes/productos-mas-vendidos` | Top productos | `desde`, `hasta`, `local_id`, `canal`, `categoria_id`, `top`, `ordenar_por` | `reportes.productos.ver` |
| GET | `/v1/reportes/categorias` | Ventas por categoría | `desde`, `hasta`, `local_id`, `canal` | `reportes.productos.ver` |

### Inventario
| Método | Ruta | Descripción | Filtros | Permiso |
| ------ | ---- | ----------- | ------- | ------- |
| GET | `/v1/reportes/inventario-bajo` | Productos con stock bajo | `bodega_id`, `umbral`, `ordenar_por` | `reportes.inventario.ver` |
| GET | `/v1/reportes/rotacion` | Rotación de inventario | `desde`, `hasta`, `bodega_id`, `producto_id` | `reportes.inventario.ver` |

### Caja / Medios de pago
| Método | Ruta | Descripción | Filtros | Permiso |
| ------ | ---- | ----------- | ------- | ------- |
| GET | `/v1/reportes/caja` | Totales de caja por medio de pago | `desde`, `hasta`, `local_id`, `caja_id`, `usuario_id` | `reportes.caja.ver` |
| GET | `/v1/reportes/metodos-pago` | Breakdown por método de pago | `desde`, `hasta`, `local_id` | `reportes.caja.ver` |

### CxC / CxP
| Método | Ruta | Descripción | Filtros | Permiso |
| ------ | ---- | ----------- | ------- | ------- |
| GET | `/v1/reportes/cxc` | Resumen cuentas por cobrar | `hasta`, `local_id`, `cliente_id` | `reportes.cxc.ver` |
| GET | `/v1/reportes/cxp` | Resumen cuentas por pagar | `hasta`, `local_id`, `proveedor_id` | `reportes.cxp.ver` |

### KDS
| Método | Ruta | Descripción | Filtros | Permiso |
| ------ | ---- | ----------- | ------- | ------- |
| GET | `/v1/reportes/kds/tiempos` | Tiempos operativos | `desde`, `hasta`, `local_id`, `estacion` | `reportes.kds.ver` |

### Exportaciones
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| POST | `/v1/reportes/export` | Solicitar exportación de reportes | `reportes.exportar` |
| GET | `/v1/reportes/export/{job_id}` | Estado de exportación | `reportes.exportar` |

### Analytics Query
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| POST | `/v1/analytics/query` | Consulta analítica flexible | `analytics.query.ejecutar` |

### Glosario de KPIs
- **ventas_brutas**: Σ (precio_unitario * cantidad) antes de descuentos.
- **descuentos**: Σ de promociones/cupones aplicados.
- **ventas_netas**: ventas_brutas - descuentos.
- **impuestos**: IVA sobre base neta.
- **propina**: importe de propinas.
- **ventas_totales**: ventas_netas + impuestos + propina.
- **tickets**: número de comprobantes.
- **ticket_promedio**: ventas_totales / número_tickets.
- **margen**: ventas_netas - costo.

### Matriz RBAC Reportes & Analytics
| Permiso | admin | finanzas | operaciones | marketing |
| ------- | :---: | :------: | :---------: | :-------: |
| analytics.dashboard.ver | ✓ | ✓ | ✓ | ✓ |
| reportes.ventas.ver | ✓ | ✓ | ✓ | ✓ |
| reportes.productos.ver | ✓ | ✓ | ✓ | ✓ |
| reportes.inventario.ver | ✓ | ✓ | ✓ | — |
| reportes.caja.ver | ✓ | ✓ | ✓ | — |
| reportes.cxc.ver | ✓ | ✓ | — | — |
| reportes.cxp.ver | ✓ | ✓ | — | — |
| reportes.kds.ver | ✓ | — | ✓ | — |
| reportes.exportar | ✓ | ✓ | ✓ | ✓ |
| analytics.query.ejecutar | ✓ | ✓ | ✓ | ✓ |

## Pedidos/KDS Avanzado

### Ítems y Modificadores
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| POST | `/v1/pedidos/{id}/items` | Agregar ítems al pedido | `pedidos.items.crear` |
| PUT | `/v1/pedidos/{id}/items/{item_id}` | Actualizar ítem | `pedidos.items.editar` |
| DELETE | `/v1/pedidos/{id}/items/{item_id}` | Eliminar ítem | `pedidos.items.eliminar` |
| POST | `/v1/pedidos/{id}/items/{item_id}/modificadores` | Agregar modificadores | `pedidos.items.editar` |
| DELETE | `/v1/pedidos/{id}/items/{item_id}/modificadores/{mod_id}` | Quitar modificador | `pedidos.items.eliminar` |

Ejemplo:
```json
POST /v1/pedidos/123/items
{
  "items": [
    {"producto_id": "uuid", "descripcion": "Hamburguesa Clásica", "cantidad": 1, "precio_unitario": 5.5}
  ]
}
```

### Envío a Cocina
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| POST | `/v1/pedidos/{id}/enviar-cocina` | Genera comandas por estación | `pedidos.enviar_cocina` |

### Flujo KDS
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/comandas` | Listar comandas | `kds.comandas.ver` |
| GET | `/v1/comandas/{id}` | Ver comanda | `kds.comandas.ver` |
| POST | `/v1/comandas/{id}/start` | Iniciar preparación | `kds.start` |
| POST | `/v1/comandas/{id}/ready` | Marcar lista | `kds.ready` |
| POST | `/v1/comandas/{id}/bump` | Servir comanda | `kds.bump` |
| POST | `/v1/comandas/{id}/recall` | Volver a lista | `kds.recall` |
| POST | `/v1/comandas/{id}/reassign` | Reasignar a otra estación | `kds.reassign` |
| POST | `/v1/comandas/{id}/nota` | Añadir nota | `kds.nota` |

### Estaciones y Ruteo
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/kds/estaciones` | Listar estaciones | `kds.estaciones.ver` |
| POST | `/v1/kds/estaciones` | Crear estación | `kds.estaciones.crear` |
| GET | `/v1/kds/estaciones/{id}` | Ver estación | `kds.estaciones.ver` |
| PUT | `/v1/kds/estaciones/{id}` | Editar estación | `kds.estaciones.editar` |
| DELETE | `/v1/kds/estaciones/{id}` | Eliminar estación | `kds.estaciones.eliminar` |
| GET | `/v1/kds/ruteo/test` | Simular ruteo de pedido | `kds.estaciones.ver` |

### SLA/Tiempos
| Método | Ruta | Descripción | Permiso |
| ------ | ---- | ----------- | ------- |
| GET | `/v1/kds/sla` | Ver configuración SLA | `kds.sla.ver` |
| PUT | `/v1/kds/sla` | Actualizar SLA | `kds.sla.editar` |
| GET | `/v1/kds/carga` | Métricas de carga de estación | `kds.comandas.ver` |

### Integraciones
- Pedidos y comandas incluyen `mesa_id` y `reserva_id` cuando corresponda.
- Inventario: opción de consumo al enviar a cocina con reversos en recall/anulación.

### Estados
- Pedido: `abierto → enviada_cocina → preparando → lista → servida → cerrada|anulada`.
- Comanda: `pendiente → en_preparacion → lista → servida → (recall opcional)`.

### Matriz RBAC Pedidos/KDS
| Permiso | admin | cocina | bar | mesero | expo |
| ------- | :---: | :----: | :-: | :----: | :--: |
| pedidos.items.crear | ✓ | ✓ | ✓ | ✓ | ✓ |
| pedidos.items.editar | ✓ | ✓ | ✓ | ✓ | ✓ |
| pedidos.items.eliminar | ✓ | ✓ | ✓ | ✓ | ✓ |
| pedidos.hold | ✓ | ✓ | ✓ | ✓ | ✓ |
| pedidos.fire | ✓ | ✓ | ✓ | ✓ | ✓ |
| pedidos.prioridad | ✓ | ✓ | ✓ | ✓ | ✓ |
| pedidos.enviar_cocina | ✓ | ✓ | ✓ | ✓ | ✓ |
| kds.comandas.ver | ✓ | ✓ | ✓ | — | ✓ |
| kds.start | ✓ | ✓ | ✓ | — | ✓ |
| kds.ready | ✓ | ✓ | ✓ | — | ✓ |
| kds.bump | ✓ | ✓ | ✓ | — | ✓ |
| kds.recall | ✓ | ✓ | ✓ | — | ✓ |
| kds.reassign | ✓ | ✓ | ✓ | — | ✓ |
| kds.nota | ✓ | ✓ | ✓ | — | ✓ |
| kds.estaciones.ver | ✓ | ✓ | ✓ | — | — |
| kds.estaciones.crear | ✓ | ✓ | ✓ | — | — |
| kds.estaciones.editar | ✓ | ✓ | ✓ | — | — |
| kds.estaciones.eliminar | ✓ | ✓ | ✓ | — | — |
| kds.sla.ver | ✓ | ✓ | ✓ | — | — |
| kds.sla.editar | ✓ | ✓ | ✓ | — | — |
| kds.stream.ver | ✓ | ✓ | ✓ | — | ✓ |
