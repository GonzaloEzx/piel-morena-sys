# Disponibilidad y Reglas

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: complementaria
> Relacion: reglas transversales del modulo de reservas
> Ultima revision: 2026-10-31 (uso interno; la web no reserva)

## Objetivo

Centralizar las reglas que antes quedaban repartidas entre documentos de `reservas` y `citas`.

## Lenguaje de dominio

Usar:

- `reservas` para la experiencia publica
- `citas` para la entidad operativa
- `horarios` o `turnos` para los slots disponibles
- `servicios` y `categorias de servicios` para la oferta reservable

Evitar `agenda` como nombre principal de modulo nuevo.

## Reglas vigentes (v2.0)

El calculo vive en `includes/agenda.php` y es de **uso interno del panel**:

- `pm_contexto_dia()`: ventana de maquina o jornada, horario del local, profesionales del servicio con sus franjas del dia y citas activas;
- `pm_franja_libre()`: profesionales libres para una franja;
- `pm_turnos_del_dia()`: grilla de turnos (`max(duracion, intervalo)`, margen de 30 min si es hoy);
- `pm_reservar()`: crea o mueve una cita bajo `GET_LOCK('pm_agenda_<fecha>')`; si el bloqueo no se consigue, responde 503 y no escribe.

### Modo profesional

- el servicio tiene filas en `empleados_servicios` (usuarios activos `empleado` o `admin`);
- cada profesional trabaja segun `horarios` del dia; sin filas en `horarios`, en el horario del local; con filas pero ninguna ese dia, no trabaja;
- una cita asignada ocupa a su profesional;
- una cita sin asignar ocupa a una profesional libre que pueda hacerla; se descuentan primero las que no se pidieron.

### Modo salon

Servicio sin profesionales asignadas: cualquier cita activa superpuesta bloquea el horario. Es identico al calculo v1 (882 casos comparados el 2026-10-31).

### Comunes

- servicio con maquina: requiere alquiler activo y una sola cita por maquina a la vez;
- categoria con jornada: requiere jornada activa; sus horarios mandan;
- dia no laboral: cerrado salvo ventana activa;
- estados que ocupan: `pendiente`, `confirmada`, `en_proceso`.

### Creacion, cancelacion y cambio

- solo personal: `api/admin/citas.php` (alta por POST con el calculo, estados y cancelacion por PATCH);
- `api/citas/disponibilidad.php` y `profesionales.php` exigen sesion de admin o empleada;
- la clienta coordina altas, cambios y cancelaciones por WhatsApp.

## Configuracion relevante

Claves operativas vigentes:

- `horario_apertura = 08:00`
- `horario_cierre = 20:00`
- `dias_laborales = 1,2,3,4,5`
- `intervalo_citas = 30`

## Inconsistencias abiertas

- el footer publica "Sabado 9:00 a 14:00" pero `dias_laborales` es `1,2,3,4,5`: confirmar con Mari;
- una clienta cargada desde el panel que despues se registra queda como dos usuarios (el registro no se vincula por telefono);

## Recomendacion de lectura

Cuando el cambio toque disponibilidad o reglas:

1. revisar este archivo;
2. revisar `docs/contracts/03-sistema-reservas.md`;
3. revisar `reservar.php` y las APIs de `api/citas/`.
