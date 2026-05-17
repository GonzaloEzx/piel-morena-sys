# Manual Técnico — Módulo de Jornadas

> Audiencia: desarrollo, debugging, agentes IA
> Última revisión: 2026-04-25
> Archivos principales: `api/admin/jornadas.php`, `api/jornadas/disponibles.php`, `admin/views/jornadas.php`

---

## Qué es una Jornada

Una jornada es un registro que habilita una categoría de servicios para recibir reservas en una fecha específica. Sin jornada activa, esa categoría no aparece en el wizard de reserva pública.

El concepto existe porque ciertos servicios dependen de equipamiento alquilado o personal externo que asiste en fechas puntuales. La admin (Mariangel) carga las fechas mes a mes desde el panel.

Categorías que actualmente requieren jornada:

| Categoría | Razón operativa |
|-----------|----------------|
| Depilación (id 1) | Máquina láser alquilada por jornada (08:00–20:00) |
| Extensiones de Pestañas | Naila asiste días puntuales |
| Peluquería (id 10) | Nathalia asiste días puntuales |
| Tratamiento con equipo (id 13) | Criolipolisis y VelaSlim disponibles por jornada |

---

## Modelo de datos

### Tabla `jornadas`

```sql
CREATE TABLE jornadas (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  id_categoria INT NOT NULL,
  fecha        DATE NOT NULL,
  hora_inicio  TIME NOT NULL DEFAULT '08:00:00',
  hora_fin     TIME NOT NULL DEFAULT '20:00:00',
  estado       ENUM('activa', 'cancelada') NOT NULL DEFAULT 'activa',
  notas        TEXT NULL,
  created_at   TIMESTAMP DEFAULT current_timestamp(),
  updated_at   TIMESTAMP DEFAULT current_timestamp() ON UPDATE current_timestamp(),

  UNIQUE KEY uk_categoria_fecha (id_categoria, fecha),
  KEY idx_fecha_estado (fecha, estado),
  KEY idx_categoria (id_categoria),
  FOREIGN KEY (id_categoria) REFERENCES categorias_servicios(id) ON DELETE CASCADE
)
```

**Restricción clave:** `UNIQUE (id_categoria, fecha)` — una categoría puede tener como máximo una jornada por día. Esto implica que si se elimina una jornada, el registro debe desaparecer físicamente de la BD para poder recrear esa misma combinación fecha+categoría en el futuro.

### Campo `categorias_servicios.requiere_jornada`

`TINYINT(1)`. Si es `1`, todos los servicios de esa categoría solo pueden reservarse en fechas con jornada activa.

### Campo `servicios.id_grupo_jornada`

`INT NULL`, FK a `categorias_servicios`. Permite que un servicio use la agenda de jornada de una categoría distinta a la suya. Tiene prioridad sobre `requiere_jornada` de la categoría propia.

Casos reales actuales:
- **Criolipolisis Plana / VelaSlim** — están en su categoría natural de tratamientos corporales, pero usan el grupo de jornada "Tratamiento con equipo" (id 13).
- **PACK REDUCTOR / PACK CELULITIS** — están en Packs, pero usan el grupo "Tratamiento con equipo".
- **Pack Depilación Definitiva** — está en Packs, pero usa el grupo "Depilación" (id 1).

Esto evita mover servicios de su categoría comercial solo por una restricción operativa de agenda.

---

## APIs

### `api/admin/jornadas.php` — protegida (solo admin)

#### GET — Listar o detallar

**Listar con filtros:**
```
GET /api/admin/jornadas.php
    ?id_categoria=1
    &estado=activa
    &fecha_desde=2026-05-01
    &fecha_hasta=2026-05-31
```
Devuelve array de jornadas con `citas_reservadas` (count) calculado inline.

**Detalle por ID:**
```
GET /api/admin/jornadas.php?id=22
```
Devuelve la jornada más `citas_reservadas` (count) y `citas_afectadas` (array completo con nombre de cliente, hora y servicio). Este endpoint alimenta el modal de confirmación de eliminación en el frontend.

**Listar categorías disponibles:**
```
GET /api/admin/jornadas.php?categorias_jornada=1
```
Devuelve todas las categorías activas (no solo las que requieren jornada) para poblar los selects del panel.

#### POST — Crear jornada(s)

```json
{
  "id_categoria": 1,
  "fechas": ["2026-05-07", "2026-05-08", "2026-05-14"],
  "hora_inicio": "08:00",
  "hora_fin": "20:00",
  "notas": "Opcional"
}
```

Acepta `fechas` como array o `fecha` como string único. Itera cada fecha, verifica duplicado (`WHERE id_categoria = ? AND fecha = ?`), inserta las nuevas, reporta las omitidas. Responde con `{ creadas: [...], duplicadas: [...], total: N }`.

Si todas las fechas son duplicadas, responde 409.

#### PUT — Editar jornada

```json
{ "id": 22, "hora_inicio": "09:00", "hora_fin": "18:00", "notas": "..." }
```

Solo modifica horarios y notas. No permite cambiar la fecha ni la categoría.

#### DELETE — Eliminar jornada

```json
{ "id": 22 }
```

1. Busca la jornada; 404 si no existe.
2. Cancela en `citas` todas las filas donde `(s.id_categoria = id_categoria OR s.id_grupo_jornada = id_categoria) AND ci.fecha = fecha AND ci.estado != 'cancelada'`.
3. Hace `DELETE FROM jornadas WHERE id = ?`.
4. Responde con `{ citas_canceladas: N, mensaje: "..." }`.

Al borrar el registro físicamente, la combinación fecha+categoría queda libre para ser recreada sin conflicto con el UNIQUE.

---

### `api/jornadas/disponibles.php` — pública (sin auth)

```
GET /api/jornadas/disponibles.php?id_servicio=5
GET /api/jornadas/disponibles.php?id_categoria=1
```

**Lógica de resolución:**

```
recibe id_servicio
  └── consulta servicios + categorias_servicios
      ├── si disponibilidad = 'normal'  → { requiere_jornada: false }
      ├── si disponibilidad = 'jornada' → requiere_jornada = true
      └── si disponibilidad = 'auto' (legacy)
          ├── si id_grupo_jornada = NULL y cat.requiere_jornada = 0 → { requiere_jornada: false }
          └── si tiene id_grupo_jornada o cat.requiere_jornada = 1 → requiere_jornada = true

id_categoria final = id_grupo_jornada ?? id_categoria_del_servicio

consulta jornadas WHERE id_categoria = ? AND estado = 'activa'
  AND fecha >= hoy AND fecha <= hoy + 60 días
```

Respuesta cuando requiere jornada:
```json
{
  "requiere_jornada": true,
  "categoria": "Depilación",
  "fechas": [
    { "id": 22, "fecha": "2026-05-07", "dia_semana": "Jueves",
      "dia_numero": 7, "mes": "May", "mes_numero": 5,
      "hora_inicio": "08:00:00", "hora_fin": "20:00:00" }
  ],
  "total": 1
}
```

---

### Interacción con `api/citas/disponibilidad.php`

Cuando el wizard pide turnos disponibles para una fecha, disponibilidad.php detecta si el servicio requiere jornada. Si es así:

1. Usa `id_grupo_jornada` como categoría operativa si existe.
2. Busca `jornadas WHERE id_categoria = ? AND fecha = ? AND estado = 'activa'`.
3. Si no hay jornada → devuelve array vacío de turnos con mensaje "no hay jornada".
4. Si hay jornada → sobreescribe `$apertura` y `$cierre` con los horarios de la jornada.
5. **Salta el chequeo de día laboral** — una jornada puede caer cualquier día de la semana.

---

## Panel admin (`admin/views/jornadas.php`)

### Vistas

Dos vistas intercambiables:
- **Calendario** (default): FullCalendar 6 en `dayGridMonth`. Cada jornada es un evento con color por categoría. Click en evento → abre modal de edición.
- **Tabla**: DataTable con columnas fecha, categoría, horario, citas, estado, notas, acciones.

El filtro de estado por defecto es `activa`. Las jornadas eliminadas desaparecen; no hay vista de "canceladas" porque el modelo actual hace hard-delete.

### Colores de categoría

Asignados dinámicamente desde `CAT_COLORES` (array de 8 colores) según el índice de la categoría en el cache. No son persistentes; si se reordena `categoriasCache`, los colores cambian.

### Flujo de eliminación

```
click botón 🗑️ → eliminarJornada(id)
  ├── GET /api/admin/jornadas.php?id=X
  │     devuelve jornada + citas_afectadas
  ├── SweetAlert2 muestra:
  │     - nombre categoría + fecha + horario
  │     - lista de citas activas que serán canceladas (si las hay)
  │     - aviso "esta acción no se puede deshacer"
  └── confirm → DELETE /api/admin/jornadas.php {id}
        └── toast de éxito + reload tabla/calendario
```

---

## Wizard de reserva pública (`reservar.php`)

```
Paso 1: selección de servicio
  └── POST/GET detecta si el servicio requiere jornada
      ├── No requiere → Paso 2: date picker normal
      └── Requiere → Paso 2: grid de fecha-cards
            └── click en fecha → Paso 3: turnos disponibles
                  └── GET api/citas/disponibilidad.php?fecha=Y&id_servicio=X
```

Los estilos del grid viven en `assets/css/style.css` bajo `.pm-jornada-card`, `.pm-jornada-aviso`, `.pm-jornadas-grid`.

---

## Reglas de negocio

- Los horarios de jornada sobreescriben los horarios generales del negocio para esa fecha y categoría.
- Una jornada puede caer cualquier día de la semana (incluye sábados, domingos, feriados).
- Al eliminar una jornada, las citas activas de esa fecha+categoría quedan automáticamente en estado `cancelada`. La admin debe comunicárselo a los clientes manualmente.
- La ventana pública de jornadas disponibles es de 60 días desde hoy.
- Solo el rol `admin` puede crear, editar o eliminar jornadas.

---

## Bug histórico resuelto (2026-04-25)

**Síntoma:** al intentar crear una jornada para una fecha+categoría que ya había sido "cancelada", el sistema respondía 409 "fecha ya tiene jornada programada" aunque la jornada no apareciera en la vista.

**Causa:** el modelo original usaba soft-cancel (`PATCH` → `UPDATE SET estado='cancelada'`). El registro permanecía en la BD. El check de duplicados al crear no filtraba por estado (`WHERE id_categoria = ? AND fecha = ?`), entonces encontraba el registro cancelado y bloqueaba la inserción.

**Solución:** se reemplazó el soft-cancel por hard-delete (`DELETE`). Al eliminar una jornada el registro desaparece físicamente, la combinación fecha+categoría queda libre y el UNIQUE no interfiere con futuras creaciones. Las citas activas vinculadas se cancelan en el mismo request antes del DELETE.

**Migración de datos requerida en producción:** los registros con `estado='cancelada'` que quedaron del modelo anterior debían eliminarse manualmente con:
```sql
DELETE FROM jornadas WHERE estado = 'cancelada';
```
Ejecutado el 2026-04-25.
