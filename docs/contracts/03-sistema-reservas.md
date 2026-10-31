# Contrato 03 - Sistema de Reservas

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si
> Relacion: contrato funcional del modulo de reservas y citas
> Ultima revision: 2026-10-31 (MVP v2.0, rama `v2-agenda`)

## Alcance

Reservas, disponibilidad, cancelacion, gestion de citas y asignacion de empleado.

## Estado actual

### Implementado

- wizard `reservar.php` de tres pasos: servicio → profesional, dia y horario → datos;
- **reserva sin cuenta (decision D2, 2026-10-31):** la invitada deja nombre y WhatsApp (email opcional) y se la reconoce por telefono (`usuarios.telefono_norm`); con sesion se usan los datos de la cuenta;
- la reserva online queda `pendiente` y el salon la confirma por WhatsApp desde el panel (decision D3);
- calculo unico en `includes/agenda.php`, usado por el wizard, la agenda publica de la landing (contrato 08), la gestion del turno y el alta del panel;
- **disponibilidad por profesional:** si el servicio tiene profesionales en `empleados_servicios`, un horario esta libre cuando alguna de ellas trabaja esa franja (`horarios`; sin horario cargado, el del local) y no tiene otra cita superpuesta. La cita queda asignada a la elegida o a la primera libre;
- servicio sin profesionales asignadas: cualquier cita superpuesta bloquea el horario (comportamiento v1);
- en ambos modos rigen los bloqueos de maquina (una cita por maquina a la vez), alquiler y jornada;
- reserva atomica: bloqueo por fecha (`GET_LOCK`) y nueva verificacion antes de guardar;
- **gestion por la clienta:** link `mi-turno.php?t=<token_gestion>` para ver, cancelar o reprogramar hasta 2 horas antes;
- defensas de la reserva sin cuenta: campo trampa y maximo 5 reservas por hora por IP;
- APIs: `api/citas/profesionales.php`, `disponibilidad.php` (`id_empleado` y `t` opcionales), `crear.php`, `gestionar.php`; `cancelar.php` sigue para el personal;
- alta desde el panel por el mismo calculo, con horarios fuera de grilla (contrato 04);
- emails y notificaciones asociados al flujo (best-effort).

### Decision cerrada

Auth vs invitado: se reserva como invitada (D2). La cuenta queda opcional para ver el historial en `mi-cuenta.php`.

## Estados de cita

- `pendiente`
- `confirmada`
- `en_proceso`
- `completada`
- `cancelada`

## Jornadas

Servicios que requieren jornada, ya sea por `categorias_servicios.requiere_jornada = 1` o por `servicios.id_grupo_jornada`, tienen flujo de reserva alternativo:

- paso 2 del wizard muestra grid de fechas con jornada activa en vez de date picker libre;
- la resolucion del grupo de jornada prioriza `id_grupo_jornada` sobre la categoria natural del servicio;
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
- [x] definicion final auth vs invitado (D2)
- [x] disponibilidad por profesional
- [x] gestion del turno por la clienta con token
- [ ] sena con Mercado Pago (v2.2)
