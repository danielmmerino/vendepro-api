# VendePro API

API del sistema VendePro. A continuación se listan los endpoints disponibles y una breve descripción de su funcionalidad.

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
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/usuarios` | Lista usuarios. |
| POST | `/v1/usuarios` | Crea un usuario. |
| GET | `/v1/usuarios/{id}` | Muestra los datos de un usuario. |
| PUT | `/v1/usuarios/{id}` | Actualiza un usuario. |
| DELETE | `/v1/usuarios/{id}` | Elimina un usuario. |
| POST | `/v1/usuarios/{id}/roles` | Asigna roles a un usuario. |

### Roles
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/roles` | Lista roles disponibles. |
| POST | `/v1/roles` | Crea un rol. |
| GET | `/v1/roles/{id}` | Muestra los datos de un rol. |
| PUT | `/v1/roles/{id}` | Actualiza un rol. |
| DELETE | `/v1/roles/{id}` | Elimina un rol. |

### Permisos
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/permisos` | Lista permisos disponibles. |
| POST | `/v1/permisos` | Crea un permiso. |
| GET | `/v1/permisos/{id}` | Muestra los datos de un permiso. |
| PUT | `/v1/permisos/{id}` | Actualiza un permiso. |
| DELETE | `/v1/permisos/{id}` | Elimina un permiso. |

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

## Otros
| Método | Ruta | Descripción |
| ------ | ---- | ----------- |
| GET | `/v1/estado-suscripcion` | Devuelve el estado de la suscripción actual. |

