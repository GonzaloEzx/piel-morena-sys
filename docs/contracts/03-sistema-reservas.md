# Contrato 03 - Sistema de Reservas

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si
> Relacion: contrato funcional del modulo de reservas y citas
> Ultima revision: 2026-10-31 (reservas por WhatsApp, rama `v2-agenda`)

## Alcance

Reservas, disponibilidad, cancelacion, gestion de citas y asignacion de empleado.

## Estado actual

### Decision vigente (2026-10-31)

**La web no reserva.** La clienta toca un boton, se abre WhatsApp con el
servicio escrito y coordina el turno con el salon. El turno queda confirmado
cuando lo acuerdan por WhatsApp; el salon lo carga en el panel (contrato 04).
Reemplaza a la reserva online sin cuenta de `d9eb1cf` (D2/D3), que no llego a
publicarse.

### Publico

- `reservar.php`: pagina "Pedi tu turno" armada en el servidor, sin JS para lo
  esencial. Lista de servicios por categoria, cada uno con su enlace a
  WhatsApp; `?servicio=<id>` destaca uno arriba; id invalido = consulta general;
- `#reservar` de la landing: selector opcional de servicio y boton "Continuar
  por WhatsApp" (sin JS va a `reservar.php?servicio=`);
- tarjetas de servicios, tratamientos, promos, menu y pie: enlaces de WhatsApp
  con el nombre del servicio cuando corresponde;
- numero y mensajes en un solo lugar: `includes/whatsapp.php`
  (`WHATSAPP_NUMERO` en `config/config.php`, ajustable en `config.local.php`);
- no hay disponibilidad publica, alta publica ni reprogramacion online.
  Abrir WhatsApp no crea ni confirma una cita.

### Interno (panel)

- calculo de disponibilidad por profesional en `includes/agenda.php`
  (ver `docs/modulos/reservas/disponibilidad-y-reglas.md`);
- `api/citas/disponibilidad.php` y `api/citas/profesionales.php` exigen sesion
  de admin o empleada;
- alta y cambios de estado por `api/admin/citas.php`;
- retirados: `api/citas/crear.php`, `gestionar.php`, `agenda-publica.php`,
  `api/maquinas/disponibles.php`, `api/jornadas/disponibles.php`, `mi-turno.php`
  y la integracion con Google Calendar.

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
- [x] la web no reserva: contacto por WhatsApp con el servicio escrito
- [x] disponibilidad por profesional (uso interno)
- [ ] numero de WhatsApp validado desde un celular real
- [ ] sena con Mercado Pago (fuera de este bloque)
