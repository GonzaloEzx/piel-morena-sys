# Contrato 12 - Maquinas Alquiladas y sus Alquileres

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si
> Relacion: contrato funcional del sistema de maquinas alquiladas
> Ultima revision: 2026-05-15

## Alcance

Gestion de **maquinas alquiladas** (instrumentos fisicos que la estetica alquila por rangos de tiempo) y sus **alquileres** (dias y horarios en que cada maquina esta disponible). Los servicios que dependen de una maquina solo se pueden reservar en fechas con alquiler activo.

No incluye:

- jornadas de personal externo (ver contrato `10-jornadas.md`);
- reglas generales de disponibilidad (ver contrato `03-sistema-reservas.md`);
- logica de packs / promociones (ver contrato `11-promociones-packs.md`).

## Concepto de negocio

La estetica opera con un modelo en el que algunos servicios dependen de maquinas que se alquilan por dia. Mari (admin) gestiona dos cosas independientes:

1. **El catalogo de maquinas:** alta, baja, modificacion. **Es dinamico:** Mari agrega maquinas a medida que las alquila. No hay seed inicial.
2. **Los alquileres de cada maquina:** dia + rango horario en que esta disponible.

Cuando una clienta intenta reservar un servicio vinculado a una maquina, solo ve las fechas con alquiler activo. La duracion del servicio se acomoda dentro del rango horario del alquiler.

Esto es paralelo al sistema de jornadas, que sigue existiendo para personal externo (Naila, Nathalia). Las dos vias **no se solapan**: un servicio o depende de una maquina, o depende de una jornada de personal, o es libre.

## Modelo de datos

### Tabla `maquinas`

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | INT PK | |
| nombre | VARCHAR(150) UNIQUE | Ej. "Laser", "Criolipolisis", "VelaSlim" |
| descripcion | TEXT NULL | Marca, modelo, observaciones |
| color | VARCHAR(7) | Hex, para calendario admin |
| icono | VARCHAR(50) NULL | Bootstrap Icons (ej. `bi-lightning-charge-fill`) |
| activo | TINYINT(1) | 1 = visible, 0 = oculta |
| notas | TEXT NULL | Observaciones internas |

### Tabla `maquinas_alquileres`

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | INT PK | |
| id_maquina | INT FK | |
| fecha | DATE | |
| hora_inicio | TIME | Default `08:00` |
| hora_fin | TIME | Default `20:00` |
| estado | ENUM(activo, cancelado) | Default `activo` |
| notas | TEXT NULL | |

- UNIQUE en `(id_maquina, fecha)`: una maquina tiene como maximo un alquiler por dia.
- FK a `maquinas` con `ON DELETE CASCADE`.
- Hard-delete al eliminar (libera la combinacion fecha+maquina).

### Campo `servicios.id_maquina`

- INT NULL, FK a `maquinas` con `ON DELETE SET NULL`.
- Si **NO NULL**: el servicio depende de esa maquina. Solo se reserva en fechas con alquiler activo.
- Si **NULL**: la disponibilidad se resuelve por las vias clasicas (jornada por categoria o calendario libre).

## Regla de resolucion de disponibilidad

Para un servicio S en una fecha F:

1. Si `S.id_maquina IS NOT NULL` → buscar `maquinas_alquileres` activo para esa maquina+fecha. Si no hay, no disponible.
2. Sino, si `S.id_categoria` tiene `requiere_jornada=1` → buscar jornada activa. Si no hay, no disponible.
3. Sino → calendario libre con los horarios generales del negocio.

Si por error un servicio tiene maquina Y categoria con jornada, gana la maquina (es mas especifica).

## APIs

### `api/admin/maquinas.php` (protegida, solo admin)

- **GET:** listar (filtros `activo`, `q`); `?id=X` → detalle con counts (servicios asociados, alquileres futuros).
- **POST:** crear (nombre, descripcion, color, icono, activo, notas).
- **PUT:** editar mismos campos.
- **DELETE:** eliminar definitivamente. Bloqueado si tiene servicios asociados o alquileres futuros activos.

### `api/admin/alquileres.php` (protegida, solo admin)

- **GET:** listar con filtros (`id_maquina`, `estado`, `fecha_desde`, `fecha_hasta`); `?id=X` → detalle + lista de citas activas afectadas; `?maquinas_activas=1` → selector.
- **POST:** crear alquileres con soporte multi-fecha (acepta `fechas: []` o `fecha: ''`).
- **PUT:** editar horarios y notas.
- **DELETE:** hard-delete + cancela las citas activas de esa fecha asociadas a la maquina.

### `api/maquinas/disponibles.php` (publica)

- **GET:** `?id_servicio=X` → devuelve `{ requiere_maquina, maquina, fechas: [...] }` con las fechas futuras (proximos 60 dias) en que la maquina del servicio tiene alquiler activo. Si el servicio no tiene maquina, devuelve `requiere_maquina: false`.

### Interaccion con `api/citas/disponibilidad.php`

El bloque "Resolucion de disponibilidad" prioriza maquina sobre jornada. Si hay alquiler activo, sus horarios sobreescriben los horarios generales del negocio para esa fecha. Salta el chequeo de dia laboral (un alquiler puede caer cualquier dia).

## Panel admin

- `admin/views/maquinas.php`: CRUD del catalogo con DataTable + modal (color picker + icono Bootstrap Icons).
- `admin/views/alquileres.php`: FullCalendar + tabla; modal multi-fecha; preview de citas afectadas al eliminar.
- Sidebar admin: ambos items bajo divider "Equipamiento" (junto con "Jornadas" para personal externo).
- Solo rol `admin` accede a las dos vistas.

## Wizard de reserva publica

- En paso 1 (seleccion de servicio), el wizard consulta primero `api/maquinas/disponibles.php`. Si `requiere_maquina: true`, paso 2 muestra grid de fechas con copy "Este servicio se realiza con la maquina X, disponible los siguientes dias:".
- Si no requiere maquina, falls back a `api/jornadas/disponibles.php` con la logica existente.
- Si ambos devuelven `false`, date picker libre.

## Reglas

- Eliminar un alquiler con citas activas las cancela automaticamente (igual que jornadas).
- Desactivar una maquina la oculta del wizard y de los selectores admin pero conserva el historial.
- Eliminar una maquina solo es posible si no tiene servicios asociados ni alquileres futuros.
- La ventana publica de fechas disponibles es de 60 dias desde hoy.

## Mapping inicial (post-deploy)

Mari debe:

1. Crear cada maquina que alquila (Laser, Criolipo, VelaSlim, las que sean).
2. Editar cada servicio que dependa de una maquina y asignarle `id_maquina`.
3. Cargar los alquileres del mes desde el calendario.

La migration 014 (limpieza post-mapping) se ejecuta despues de eso para deprecar campos legacy (`id_grupo_jornada`, `disponibilidad`, `categorias_servicios.requiere_jornada` en categoria Depilacion, categoria 13 "Tratamiento con equipo").

## Checklist

- [x] tabla maquinas + maquinas_alquileres en BD (migracion 013)
- [x] FK servicios.id_maquina
- [x] API admin CRUD maquinas y alquileres
- [x] API publica de fechas disponibles
- [x] Integracion con api/citas/disponibilidad.php
- [x] Vista admin maquinas + alquileres
- [x] Wizard publico con resolucion maquina > jornada > libre
- [ ] Manual usuario (en progreso)
- [ ] Mapping de servicios existentes a maquinas (post-deploy, por Mari)
- [ ] Deprecacion final de id_grupo_jornada y disponibilidad (migracion 015, sprint siguiente)
