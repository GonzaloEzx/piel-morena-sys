# Contrato 11 - Promociones y Packs

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si
> Relacion: contrato funcional del modulo de promociones/packs
> Ultima revision: 2026-04-05

## Alcance

Gestion de promociones y packs entendidos como **bundles de servicios a precio combinado**. No son descuentos porcentuales: cada promo define su propio `precio_pack` y agrupa uno o mas servicios existentes.

## Modelo conceptual

Una **promocion** es un bundle con:

- nombre, descripcion, precio_pack, duracion_estimada;
- rango de vigencia opcional (`fecha_inicio`, `fecha_fin`);
- N servicios componentes (pivot `promocion_servicios` con `cantidad` de sesiones);
- un **servicio-pack generado** (`id_servicio_generado`) que la representa en el listado publico y en el wizard de reservas.

El servicio-pack generado vive en la tabla `servicios` bajo la categoria "Packs y Promociones" y hereda el mismo toggle de `disponibilidad` que cualquier otro servicio. Esto permite mezclar servicios de distintas categorias (con y sin jornada) dentro de un mismo pack: la disponibilidad efectiva la define el pack, no sus componentes.

## Estado actual

### Implementado

- tabla `promociones` rediseñada (migration 012) como bundle con `precio_pack`;
- tabla pivot `promocion_servicios` con `cantidad` por servicio;
- categoria "Packs y Promociones" en `categorias_servicios`;
- CRUD admin en `api/admin/promociones.php` (GET/POST/PUT/DELETE);
- vista admin en `admin/views/promociones.php` con DataTable, modal y selector de servicios agrupados por categoria;
- API publica en `api/promociones/listar.php` que devuelve solo promos vigentes;
- listado publico de servicios (`api/servicios/listar.php`) filtra packs vencidos/desactivados/no iniciados;
- badge read-only en el modal de editar servicio cuando el servicio es un pack generado;
- landing page `index.php` carga la seccion `#promos` dinamicamente desde la API.

## Estados de vigencia

El campo calculado `estado_vigencia` se deriva en tiempo de consulta:

| Estado | Condicion |
|---|---|
| `vigente` | `activo = 1` y fecha actual dentro del rango (o rango abierto) |
| `programada` | `fecha_inicio > CURDATE()` |
| `vencida` | `fecha_fin < CURDATE()` |
| `desactivada` | `activo = 0` (auto-filtrada del listado publico) |

## Reglas

- una promo es siempre un bundle de al menos 1 servicio;
- el precio del pack es explicito y unico, no un descuento porcentual;
- al crear una promo se auto-genera un servicio-pack en la categoria "Packs y Promociones" con el mismo nombre, precio y duracion;
- el servicio-pack hereda el toggle `disponibilidad` definido en el modal de la promo;
- al editar la promo se sincronizan el servicio-pack y la lista de componentes (delete-reinsert en pivot);
- al desactivar la promo se desactiva tambien el servicio-pack asociado (`activo = 0` en ambos);
- **filtrado del listado publico**: un servicio-pack se oculta del listado publico si la promo esta desactivada, vencida o aun no iniciada;
- **1 pack = 1 servicio = 1 cita = 1 movimiento de caja**: el sistema no sabe que es un bundle; lo trata como un servicio mas con su propio precio y duracion;
- el modal de editar servicio muestra un badge read-only "Este servicio es generado por la promocion X" con link a la seccion Promociones cuando aplica;
- desde el modal de servicio **no** se puede editar la vinculacion; la gestion es unicamente desde la seccion Promociones.

## API

### GET `/api/admin/promociones.php`
Lista todas las promos con `estado_vigencia`, `total_servicios`, estado del servicio asociado.

### GET `/api/admin/promociones.php?id={id}`
Detalle de una promo con array `servicios` (componentes del pivot) y `disponibilidad` + `id_grupo_jornada` del servicio-pack.

### POST `/api/admin/promociones.php`
Crea promo + servicio-pack + pivot en una transaccion. Body:
```json
{
  "nombre": "Pack Depi Completa",
  "descripcion": "...",
  "precio_pack": 45000,
  "duracion_estimada": 120,
  "fecha_inicio": "2026-04-01",
  "fecha_fin": "2026-06-30",
  "servicios": [{"id_servicio": 12, "cantidad": 6}],
  "disponibilidad": "jornada",
  "id_grupo_jornada": 1
}
```

### PUT `/api/admin/promociones.php`
Actualiza promo + servicio-pack + resincroniza pivot. Requiere `id` en body.

### DELETE `/api/admin/promociones.php`
Soft-delete: setea `activo = 0` en la promo y en el servicio-pack asociado. Body: `{"id": X}`.

### GET `/api/promociones/listar.php` (publico)
Devuelve solo promos activas y en vigencia, con `servicios_incluidos` (GROUP_CONCAT de nombres) y `id_servicio_generado` para construir el link al wizard (`reservar.php?servicio={id}`).

## Integracion visual

- **Landing `index.php`**: la seccion `#promos` carga el carousel desde la API publica. Si no hay promos vigentes muestra un mensaje de fallback. Cada tarjeta tiene CTA que lleva al wizard con el servicio-pack preseleccionado.
- **Wizard `reservar.php`**: los packs aparecen en el accordion de servicios bajo la categoria "Packs y Promociones", como cualquier otro servicio. La disponibilidad (normal/jornada) se resuelve igual que el resto.
- **Panel admin**: item "Promociones" en el sidebar entre "Servicios" y "Clientes".

## Checklist

- [x] tabla `promociones` como bundle
- [x] pivot `promocion_servicios`
- [x] categoria "Packs y Promociones"
- [x] CRUD admin API
- [x] vista admin con DataTable + modal
- [x] API publica para landing
- [x] filtrado de packs vencidos en listado publico
- [x] landing dinamica desde BD
- [x] badge read-only en modal de servicio
- [x] sincronizacion promo ↔ servicio-pack en create/update/delete
