# Contrato 03 - Sistema de Reservas

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

## Checklist

- [x] disponibilidad
- [x] anti-solapamiento
- [x] cambio de estado
- [x] asignacion de empleado
- [x] calendario admin
- [x] restriccion de cancelacion
- [x] notificaciones base
- [ ] definicion final auth vs invitado
