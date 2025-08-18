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
| Permiso | admin | supervisor | cajero |
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
| Permiso | admin | supervisor | cajero |
| ------- | ----- | ---------- | ------ |
| caja.aperturas.crear | ✓ | ✓ | ✓ |
| caja.aperturas.ver | ✓ | ✓ | ✓ |
| caja.movimientos.crear | ✓ | ✓ | ✓ |
| caja.depositos.crear | ✓ | ✓ | ✓ |
| caja.cierre.crear | ✓ | ✓ | ✓ |
| pagos_venta.crear | ✓ | ✓ | ✓ |
| pagos_venta.ver | ✓ | ✓ | ✓ |
| pagos_venta.anular | ✓ | ✓ | ✓ |
