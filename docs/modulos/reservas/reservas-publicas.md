# Reservas Publicas — Pedi tu turno por WhatsApp

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: complementaria (manda el contrato 03)
> Ultima revision: 2026-10-31

## Que resuelve

Una web que facilita escribirle al salon: la clienta toca un boton, se abre
WhatsApp con el servicio escrito y coordina el turno con Mari. La web no
calcula disponibilidad ni crea citas. Hasta 2026-10-31 `reservar.php` era un
wizard de reserva con cuenta; esa historia queda en Git.

## Piezas

- `reservar.php`: lista de servicios por categoria (`<details>`, sin JS), un
  enlace a WhatsApp por servicio, consulta general ("Necesito asesoramiento")
  y `?servicio=<id>` para destacar uno. Id invalido o inactivo: sin destacado.
- `index.php#reservar`: selector + "Continuar por WhatsApp" (`PedirTurno` en
  `assets/js/main.js`; sin JS, formulario GET a `reservar.php`).
- `includes/whatsapp.php`: `pm_wa_numero()`, `pm_wa_link()`,
  `pm_wa_mensaje_turno()`, `pm_wa_turno()`.

## Mensajes

- Servicio: "Hola, Piel Morena. Vi {servicio} en la web y quisiera coordinar
  un turno. ¿Que dias y horarios tienen disponibles?"
- General: "Hola, Piel Morena. Quisiera coordinar un turno. ¿Me ayudan a
  elegir el servicio?"

## Lo que no conviene romper

- los enlaces existentes a `reservar.php` y `reservar.php?servicio=` (menu,
  banners, promos, emails, `mi-cuenta.php`);
- que todo funcione sin JS;
- que abrir WhatsApp no se trate como reserva, envio ni confirmacion.
