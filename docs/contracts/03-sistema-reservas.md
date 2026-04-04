# Contrato 03 - Sistema de Reservas

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si
> Relacion: contrato funcional del modulo de reservas y citas
> Ultima revision: 2026-04-03

## Alcance

Reservas, disponibilidad, cancelacion, gestion de citas y asignacion de empleado.

## Estado actual

### Implementado

- wizard en `reservar.php`;
- `api/citas/disponibilidad.php`;
- `api/citas/crear.php`;
- `api/citas/cancelar.php`;
- vista admin de citas;
- alta manual de citas desde admin para clientas sin cuenta o llamadas;
- asignacion de empleado al confirmar/agendar;
- vista calendario en panel;
- restriccion de cancelacion a menos de 2 horas;
- notificaciones y emails asociados al flujo.

### Observacion importante

Hay una definicion de producto abierta:

- frontend publico: pide login para reservar;
- backend: permite reservar como invitado y genera token de cancelacion.

El equipo debe unificar este criterio antes de seguir ampliando el modulo.

## Estados de cita

- `pendiente`
- `confirmada`
- `en_proceso`
- `completada`
- `cancelada`

## Jornadas

Servicios de categorias con `requiere_jornada = 1` tienen flujo de reserva alternativo:

- paso 2 del wizard muestra grid de fechas con jornada activa en vez de date picker libre;
- `disponibilidad.php` verifica jornada activa y usa sus horarios;
- ver contrato `10-jornadas.md` para detalle completo.

## Checklist

- [x] disponibilidad
- [x] anti-solapamiento
- [x] cambio de estado
- [x] asignacion de empleado
- [x] calendario admin
- [x] restriccion de cancelacion
- [x] notificaciones base
- [x] soporte de jornadas en disponibilidad y wizard
- [ ] definicion final auth vs invitado
