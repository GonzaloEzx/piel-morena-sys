# Contrato 06 - Notificaciones

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si
> Relacion: contrato funcional de notificaciones
> Ultima revision: 2026-04-03

## Alcance

Emails transaccionales, cron de recordatorios y notificaciones internas del sistema.

## Estado actual

### Implementado

- helper `includes/mail_helper.php`;
- templates en `templates/email/`;
- email de bienvenida;
- email de confirmacion de reserva;
- email por cambio de estado relevante;
- email de cancelacion;
- cron `cron/recordatorios.php`;
- notificaciones internas en BD;
- listado y marcado de leidas;
- campana de notificaciones en panel.

### Pendiente

- endpoint de envio masivo o promocional desde admin;
- estrategia formal para notificaciones al cliente fuera del panel interno.

## Endpoints activos

- `api/notificaciones/listar.php`
- `api/notificaciones/marcar_leida.php`

## Checklist

- [x] emails transaccionales
- [x] recordatorios por cron
- [x] notificaciones internas
- [x] campana en topbar
- [ ] envio masivo de promociones
