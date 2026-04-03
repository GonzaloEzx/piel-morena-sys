# Diagnostico Delete Actual

> Estado: diagnostico vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: no
> Relacion: diagnostico del estado actual de borrados
> Ultima revision: 2026-04-03

## Resumen

El proyecto hoy mezcla soft-delete, hard-delete y cancelacion segun modulo. No existe una politica unica de borrado.

## Empleados: soft-delete

Backend:

- `api/admin/empleados.php`
- Usa `PATCH` para cambiar `activo`.
- Consulta relevante: `UPDATE usuarios SET activo = ? WHERE id = ? AND rol = 'empleado'`.

Vista:

- `admin/views/empleados.php`
- La accion de tabla llama a `toggleEmpleado(...)`.
- El icono alterna entre desactivar y reactivar.

Conclusiones:

- Empleados no se eliminan fisicamente.
- El sistema aplica desactivacion logica sobre `usuarios`.

## Servicios: hard-delete controlado

Backend:

- `api/admin/servicios.php`
- El `DELETE` borra definitivamente el servicio.
- Antes de borrar, valida si existen citas asociadas.
- Si el servicio tiene citas, responde `409` y bloquea la purge.

Vista:

- `admin/views/servicios.php`
- La tabla expone `eliminarServicio(...)`.
- El boton de eliminar termina llamando al endpoint `DELETE` del CRUD.

Conclusiones:

- Servicios ya no quedan como inactivos al eliminarlos.
- La eliminacion definitiva solo se permite cuando no hay citas asociadas.

## Clientes: soft-delete

Backend:

- `api/admin/clientes.php`
- Usa `PATCH` para cambiar `activo`.
- Consulta relevante: `UPDATE usuarios SET activo = ? WHERE id = ? AND rol = 'cliente'`.

Vista:

- `admin/views/clientes.php`
- La tabla expone `toggleCliente(...)`.
- El boton de accion se presenta como desactivar o reactivar.

Conclusiones:

- Clientes no se eliminan fisicamente desde el admin actual.
- El comportamiento real es soft-delete sobre la tabla `usuarios`.

## Mensajes: hard-delete

Backend:

- `api/admin/mensajes.php`
- El `DELETE` borra fisicamente la fila.
- Consulta relevante: `DELETE FROM contacto_mensajes WHERE id = ?`.

Vista:

- `admin/views/mensajes.php`
- La tabla expone `eliminarMensaje(...)`.
- El boton ejecuta `DELETE` contra el endpoint de mensajes.

Conclusiones:

- Mensajes de contacto usan hard-delete real.
- Este modulo ya tiene un comportamiento distinto al resto del admin.

## Citas: solo cancelacion

Backend:

- `api/admin/citas.php`
- No existe handler `DELETE`.
- El flujo disponible es `PATCH` para cambio de estado.

Vista:

- `admin/views/citas.php`
- La accion visible es `cancelarCita(...)`.
- El boton de tabla se muestra como `Cancelar`, no como eliminar.

Conclusiones:

- Las citas hoy no tienen borrado fisico desde UI ni desde endpoint admin.
- El mecanismo actual es cancelacion por estado.

## Observacion general

- El proyecto ya distingue implicitamente entre datos que se desactivan y datos que se borran.
- No hay todavia una separacion explicita y uniforme entre:
  - desactivar
  - cancelar
  - eliminar definitivamente

## Riesgo actual

- La UI no siempre refleja con precision el comportamiento real del backend.
- La politica de borrado sigue siendo mixta entre modulos.
