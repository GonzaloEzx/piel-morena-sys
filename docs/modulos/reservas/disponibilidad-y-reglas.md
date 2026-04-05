# Disponibilidad y Reglas

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: complementaria
> Relacion: reglas transversales del modulo de reservas
> Ultima revision: 2026-04-04

## Objetivo

Centralizar las reglas que antes quedaban repartidas entre documentos de `reservas` y `citas`.

## Lenguaje de dominio

Usar:

- `reservas` para la experiencia publica
- `citas` para la entidad operativa
- `horarios` o `turnos` para los slots disponibles
- `servicios` y `categorias de servicios` para la oferta reservable

Evitar `agenda` como nombre principal de modulo nuevo.

## Reglas vigentes

### Disponibilidad

`api/citas/disponibilidad.php` hoy:

- acepta solo `GET`;
- valida fecha e id de servicio;
- exige servicio activo;
- **chequea si el servicio requiere jornada:**
  - prioridad: `servicios.id_grupo_jornada` > `categorias_servicios.requiere_jornada`;
  - si requiere jornada y no hay jornada activa para esa fecha → devuelve turnos vacios con mensaje;
  - si hay jornada activa → usa horarios de la jornada (sobreescribe apertura/cierre general);
  - si hay jornada activa → salta chequeo de dia laboral;
- usa configuracion general del negocio (para servicios sin jornada);
- no devuelve slots para dias no laborables (excepto con jornada activa);
- si la fecha es hoy, aplica margen de 30 minutos;
- genera slots con `max(duracion_servicio, intervalo_citas)`;
- bloquea solapamientos contra citas `pendiente`, `confirmada` y `en_proceso`.

### Creacion

`api/citas/crear.php` hoy:

- acepta solo `POST`;
- valida servicio, fecha y hora;
- recalcula `hora_fin` segun duracion;
- vuelve a chequear anti-solapamiento;
- crea la cita en estado `pendiente`;
- guarda `token:<hex>` en `notas`;
- intenta enviar email y notificacion en modo best-effort.

### Cancelacion

`api/citas/cancelar.php` hoy:

- requiere sesion autenticada;
- solo permite `admin` o `empleado`;
- no permite cancelar citas `cancelada` o `completada`;
- el cliente final no tiene hoy un flujo publico de cancelacion.

## Configuracion relevante

Claves operativas vigentes:

- `horario_apertura = 08:00`
- `horario_cierre = 20:00`
- `dias_laborales = 1,2,3,4,5`
- `intervalo_citas = 30`

## Inconsistencias abiertas

### Auth requerido en UI vs invitado permitido en backend

Hoy conviven dos realidades:

- la UI publica exige cuenta;
- `api/citas/crear.php` todavia soporta invitadas.

Esto no debe resolverse por accidente. Requiere decision de producto.

### Cancelacion historica vs cancelacion real

Parte de la documentacion vieja hablaba de:

- cancelacion por cliente;
- cancelacion por token;
- limite de 2 horas.

El codigo vigente ya no funciona asi. La realidad actual es cancelacion solo por personal autenticado.

### Jornadas especificas

Implementado. Ver `docs/contracts/10-jornadas.md` para el contrato funcional completo y `docs/modulos/reservas/jornadas.md` para detalle tecnico.

## Recomendacion de lectura

Cuando el cambio toque disponibilidad o reglas:

1. revisar este archivo;
2. revisar `docs/contracts/03-sistema-reservas.md`;
3. revisar `reservar.php` y las APIs de `api/citas/`.
