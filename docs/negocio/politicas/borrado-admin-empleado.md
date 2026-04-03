# Politica De Borrado Admin Y Empleado

## Objetivo

Definir una politica futura de borrado segura, con separacion clara entre desactivar y eliminar definitivamente, sin implementarla todavia.

## Criterios de negocio

- Empleados: solo soft-delete.
- Admin: soft-delete normal mas purge definitiva controlada.
- Usuarios con rol `admin`: no se borran desde UI.
- Servicios: purge controlada segun reglas y dependencias.
- Citas completadas: sin purge libre desde UI.
- La UI debe diferenciar claramente entre `desactivar/cancelar` y `eliminar definitivamente`.

## Recomendacion funcional

- Mantener como accion por defecto el soft-delete o la cancelacion.
- Exponer la purge definitiva solo a administradores.
- Pedir confirmacion reforzada para cualquier borrado irreversible.
- Mostrar dependencias antes de purgar un registro.

## Tabla de auditoria sugerida

Tabla sugerida: `admin_deletes_log`

Campos recomendados:

- `id`
- `actor_user_id`
- `entidad`
- `entidad_id`
- `snapshot_json`
- `motivo`
- `created_at`

## Uso esperado de la auditoria

- Guardar quien ejecuto la purge.
- Guardar sobre que entidad se opero.
- Guardar el estado previo en JSON antes del borrado.
- Permitir trazabilidad operativa y soporte post-incidente.

## Endpoints o acciones futuras sugeridas

- `DELETE /api/admin/servicios.php` para purge real solo admin, con chequeo de dependencias.
- `DELETE /api/admin/productos.php` para purge real solo admin si el producto no requiere conservar historial.
- `DELETE /api/admin/clientes.php` solo para casos controlados y con reglas estrictas.
- `DELETE /api/admin/citas.php` solo para admin y con politica mucho mas restrictiva que cancelar.
- Accion UI separada:
  - `Desactivar` o `Cancelar`
  - `Eliminar definitivamente`

## Validaciones previas recomendadas

- Verificar autenticacion y rol admin.
- Verificar que el ID exista.
- Verificar dependencias antes de borrar.
- Bloquear purge si hay historial que deba conservarse.
- Devolver `409` cuando existan dependencias incompatibles con purge.
- Registrar snapshot y actor antes del `DELETE`.

## Uso de transacciones

- Toda purge definitiva debe correr dentro de una transaccion.
- Si falla una parte del proceso, debe hacerse rollback completo.
- La escritura del log de auditoria debe quedar dentro de la misma transaccion cuando sea posible.

## Politica sugerida por entidad

### Empleados

- Solo soft-delete.
- Razon: impactan horarios, citas, caja y trazabilidad operativa.

### Admins

- No se borran desde UI.
- Cualquier cambio debe resolverse fuera del flujo comun o con intervencion tecnica controlada.

### Servicios

- Soft-delete como accion comun.
- Purge definitiva solo si la politica de dependencias lo permite.
- Si el servicio tiene historial critico que deba preservarse, bloquear purge.

### Clientes

- Soft-delete como opcion segura por defecto.
- Purge definitiva solo con reglas mas estrictas y revision de historial asociado.

### Citas

- Accion comun: cancelar.
- Purge definitiva no libre desde UI.
- Para citas completadas, mantener bloqueo por defecto.

### Mensajes

- Pueden seguir con hard-delete, porque no son pieza central del historial operativo.

## Mensajes de error recomendados

- `409`: el registro tiene dependencias y no puede eliminarse definitivamente.
- `403`: accion no autorizada.
- `404`: registro no encontrado.
- `400`: solicitud invalida o faltan datos obligatorios.

## Linea de implementacion futura

1. Mantener el comportamiento actual estable.
2. Agregar tabla de auditoria.
3. Incorporar chequeos de dependencias por entidad.
4. Exponer purge solo a admin.
5. Separar visualmente soft-delete y purge en el admin.
6. Agregar testing especifico de borrados y rollback.
