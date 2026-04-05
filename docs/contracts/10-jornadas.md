# Contrato 10 - Jornadas

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si
> Relacion: contrato funcional del sistema de jornadas
> Ultima revision: 2026-04-04

## Alcance

Gestion de jornadas: dias especificos en los que ciertas categorias de servicios estan disponibles para reserva. Aplica a servicios que dependen de equipamiento alquilado o personal externo que asiste en fechas puntuales.

No incluye:

- reglas generales de disponibilidad (viven en contrato 03);
- gestion de horarios regulares de empleados;
- logica de reserva publica mas alla del paso 2 del wizard.

## Concepto de negocio

Algunas categorias de servicios no operan todos los dias. Ejemplos:

- **Depilacion:** maquina laser alquilada por jornada (12 hs, 08:00 a 20:00);
- **Extensiones de Pestanas:** Naila asiste dias puntuales;
- **Peluqueria:** Nathalia asiste dias puntuales;
- **Tratamiento con equipo:** equipos de Criolipolisis y VelaSlim disponibles por jornada.

La admin (Mariangel) define las fechas mes a mes desde el panel.

## Modelo de datos

### `categorias_servicios.requiere_jornada`

- campo `TINYINT(1)` en tabla `categorias_servicios`;
- si es `1`, los servicios de esa categoria solo pueden reservarse en fechas con jornada activa;

### `servicios.id_grupo_jornada`

- campo `INT NULL` en tabla `servicios`;
- FK opcional a `categorias_servicios`;
- si no es `NULL`, el servicio requiere jornada del grupo indicado aunque su categoria natural no requiera jornada;
- tiene prioridad sobre `categorias_servicios.requiere_jornada`;
- permite que un servicio siga visible en su categoria natural o en Packs, pero use la agenda de otro grupo de jornada.

### Tabla `jornadas`

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | INT PK | |
| id_categoria | INT FK | categoria que opera ese dia |
| fecha | DATE | fecha de la jornada |
| hora_inicio | TIME | default 08:00 |
| hora_fin | TIME | default 20:00 |
| estado | ENUM(activa, cancelada) | |
| notas | TEXT | observaciones opcionales |

- constraint UNIQUE en `(id_categoria, fecha)`: una categoria tiene como maximo una jornada por dia;
- FK a `categorias_servicios` con `ON DELETE CASCADE`.

## Categorias y grupos de jornada (estado actual)

1. Depilacion (id 1)
2. Extensiones de Pestanas (nueva)
3. Peluqueria (id 10)
4. Tratamiento con equipo (id 13)

Las categorias nuevas se crearon en migration 009 y la migration 010 agrego grupos de jornada a nivel servicio para los casos mixtos.

## Grupos de jornada y casos especiales

El sistema hoy soporta dos modos:

1. **Categoria completa con jornada:** toda la categoria depende de fechas cargadas en `jornadas`.
2. **Servicio con grupo de jornada:** un servicio individual apunta a `servicios.id_grupo_jornada` y usa las jornadas de ese grupo.

Casos reales actuales:

- Criolipolisis Plana y VelaSlim siguen en su categoria natural de tratamientos corporales, pero usan el grupo de jornada "Tratamiento con equipo";
- PACK REDUCTOR y PACK CELULITIS viven en Packs, pero usan el mismo grupo de jornada "Tratamiento con equipo";
- Pack Depilacion Definitiva vive en Packs, pero usa las jornadas del grupo Depilacion.

Esto evita duplicar servicios o moverlos de su categoria comercial solo por una restriccion operativa de agenda.

## APIs

### `api/admin/jornadas.php` (protegida, solo admin)

- **GET:** listar jornadas con filtros (categoria, fecha, estado); detalle por id; listar categorias con jornada;
- **POST:** crear jornada(s) con soporte multi-fecha; valida categoria, detecta duplicados;
- **PUT:** editar horarios y notas;
- **PATCH:** cancelar jornada; soporta `solo_info` para preview de citas afectadas antes de confirmar.

### `api/jornadas/disponibles.php` (publica)

- **GET:** recibe `id_servicio` o `id_categoria`;
- resuelve si el servicio requiere jornada;
- prioridad de resolucion: `servicios.id_grupo_jornada` > `categorias_servicios.requiere_jornada`;
- devuelve lista de fechas con jornada activa en los proximos 60 dias;
- si no requiere jornada, devuelve `requiere_jornada: false`.

### Interaccion con `api/citas/disponibilidad.php`

- si el servicio tiene `id_grupo_jornada` o su categoria tiene `requiere_jornada = 1`:
  - si existe `id_grupo_jornada`, usa ese grupo como categoria operativa de jornada;
  - verifica existencia de jornada activa para esa fecha;
  - si no hay jornada, devuelve turnos vacios con mensaje;
  - si hay jornada, usa sus horarios en vez de los generales del negocio;
  - salta el chequeo de dia laboral (la jornada puede caer en fines de semana).

## Panel admin

- `admin/views/jornadas.php`: calendario FullCalendar (vista mensual) + tabla DataTable;
- item "Jornadas" en sidebar, solo visible para admin;
- modal de creacion con selector de categoria y fechas multiples;
- modal de edicion de horarios y notas;
- cancelacion con preview de citas afectadas via SweetAlert2.

## Wizard de reserva publica

- al seleccionar un servicio en paso 1, el wizard consulta `api/jornadas/disponibles.php`;
- si `requiere_jornada: true`, paso 2 muestra grid de cards con fechas disponibles en vez de date picker libre;
- al elegir una fecha de jornada, se pasa directamente a paso 3 (turnos);
- si no requiere jornada, el flujo es el normal con date picker.

## Reglas

- cancelar una jornada NO cancela automaticamente las citas asociadas; la admin debe gestionarlas manualmente;
- una jornada cancelada deja de ser visible para reservas publicas;
- los horarios de jornada sobreescriben los horarios generales del negocio para esa fecha y categoria.

## Checklist

- [x] tabla jornadas en BD
- [x] campo requiere_jornada en categorias
- [x] API admin CRUD
- [x] API publica de fechas disponibles
- [x] integracion con disponibilidad.php
- [x] vista admin con calendario y tabla
- [x] wizard de reserva con selector de jornadas
- [ ] notificacion a clientas al cancelar jornada con citas
