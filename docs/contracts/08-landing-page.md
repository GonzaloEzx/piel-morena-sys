# Contrato 08 - Landing Publica

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si
> Relacion: contrato funcional del home publico
> Ultima revision: 2026-04-03

## Alcance

Pagina principal, secciones de marca, formulario de contacto y CTAs de reserva.

## Estado actual

### Implementado

- navbar responsive;
- hero con carousel;
- secciones `nosotros`, `servicios`, `equipo`, `galeria`, `promos`, `testimonios`, `reservar`, `contacto`;
- formulario de contacto;
- CTAs "Pedir turno" a WhatsApp o a `reservar.php`;
- bloque "Pedí tu turno por WhatsApp" en `#reservar`, ver abajo;
- SEO basico y favicon;
- responsive segun design system.

### Pendiente de contenido

- reemplazo de placeholders del negocio;
- fotos reales;
- links reales a redes;
- mapa definitivo;
- testimonios nuevos segun criterio editorial del negocio.

## Pedí tu turno (`#reservar`) — desde 2026-10-31

Reemplaza a la agenda pública con turnos libres (2026-10-10). La web no muestra
disponibilidad ni reserva.

- Selector opcional de servicio ("Necesito asesoramiento" por defecto) y botón
  "Continuar por WhatsApp".
- Con JS (`PedirTurno` en `assets/js/main.js`) abre WhatsApp con el mensaje;
  sin JS, el formulario lleva a `reservar.php?servicio=<id>`.
- Textos y número: `includes/whatsapp.php`. El modal de tratamientos usa el
  mismo mensaje.
- Aviso fijo: el turno queda confirmado cuando se acuerda por WhatsApp.

## Regla

No documentar valores reales sensibles o comerciales en este contrato. Si falta contenido final, dejarlo como placeholder explicitamente marcado.
