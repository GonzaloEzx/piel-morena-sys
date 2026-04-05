# Jornadas — Implementacion

> Estado: vigente
> Audiencia: desarrollo, agentes
> Fuente de verdad: complementaria
> Relacion: detalle tecnico del modulo de jornadas
> Ultima revision: 2026-04-04

## Contexto

El contrato funcional vive en `docs/contracts/10-jornadas.md`. Este documento cubre detalles de implementacion.

## Archivos clave

### Base de datos

- `database/migrations/009_jornadas.sql` — migracion: campo `requiere_jornada`, nuevas categorias, movimiento de servicios, tabla `jornadas`
- `database/migrations/010_grupo_jornada.sql` — migracion: campo `servicios.id_grupo_jornada` para soporte de grupo de jornada a nivel servicio
- `database/schema.sql` y `database/schema.hostinger.sql` — actualizados con definicion de tabla `jornadas` y campo `requiere_jornada`

### APIs

- `api/admin/jornadas.php` — CRUD admin (GET/POST/PUT/PATCH)
- `api/jornadas/disponibles.php` — endpoint publico para fechas con jornada
- `api/citas/disponibilidad.php` — modificado para chequear jornada antes de generar slots

### Panel admin

- `admin/views/jornadas.php` — vista completa con FullCalendar + DataTable + modales
- `admin/includes/admin_header.php` — item "Jornadas" agregado al sidebar

### Reserva publica

- `reservar.php` — paso 2 alternativo con grid de fecha-cards para servicios con jornada
- `assets/css/style.css` — estilos `.pm-jornada-card`, `.pm-jornada-aviso`, `.pm-jornadas-grid`

## Flujo de datos

### Creacion de jornada (admin)

```
admin/views/jornadas.php → POST api/admin/jornadas.php
  ├── valida categoria/grupo (requiere_jornada = 1)
  ├── itera array de fechas
  ├── detecta duplicados (UNIQUE constraint)
  └── inserta en tabla jornadas
```

### Reserva publica (cliente)

```
reservar.php paso 1: selecciona servicio
  └── GET api/jornadas/disponibles.php?id_servicio=X
      ├── resuelve grupo: id_grupo_jornada > categoria con requiere_jornada
      ├── requiere_jornada = false → date picker normal
      └── requiere_jornada = true → grid de fechas
          └── click en fecha → GET api/citas/disponibilidad.php?fecha=Y&id_servicio=X
              ├── busca jornada activa para esa fecha
              ├── usa hora_inicio/hora_fin de la jornada
              └── genera slots y devuelve turnos
```

### Cancelacion de jornada (admin)

```
admin/views/jornadas.php → PATCH api/admin/jornadas.php {id, solo_info: true}
  ├── devuelve preview con citas afectadas
  └── SweetAlert2 muestra confirmacion
      └── PATCH api/admin/jornadas.php {id}
          └── UPDATE jornadas SET estado = 'cancelada'
          (citas NO se cancelan automaticamente)
```

## Logica en disponibilidad.php

El chequeo de jornada se ejecuta despues de validar el servicio y antes de generar slots:

1. consulta `categorias_servicios.requiere_jornada` para el servicio
2. consulta `servicios.id_grupo_jornada`
3. si hay `id_grupo_jornada` o la categoria requiere jornada:
   - usa `id_grupo_jornada` como prioridad si existe
   - busca jornada activa en `jornadas` para esa categoria y fecha
   - si no encuentra → devuelve turnos vacios con mensaje
   - si encuentra → sobreescribe `$apertura` y `$cierre` con horarios de la jornada
   - salta chequeo de dia laboral (jornada puede caer cualquier dia)

## Colores de categoria en calendario admin

Se asignan dinamicamente desde un array fijo de 8 colores, basado en el indice de la categoria en el cache. El color principal es `#8A7650` (bronce del design system).

## Decisiones de diseno

- **Modelo hibrido categoria + grupo:** el disparador base sigue existiendo a nivel categoria (`requiere_jornada`), pero se agrego `id_grupo_jornada` para servicios que comercialmente deben quedar en su categoria natural o en Packs y operativamente dependen de otra agenda.
- **Prioridad del grupo de jornada:** si un servicio tiene `id_grupo_jornada`, ese grupo manda aunque su `id_categoria` natural no requiera jornada.
- **Grupos sin servicios visibles:** una categoria puede existir solo como grupo operativo de jornada para el admin, aunque no tenga servicios listados directamente en el wizard.
- **Multi-fecha en creacion:** Mari define varias fechas a la vez para el mes. El POST acepta array y reporta duplicados sin fallar.
- **Cancel no cascadea:** cancelar jornada no toca las citas. Decision de negocio: Mari prefiere gestionar caso por caso.
