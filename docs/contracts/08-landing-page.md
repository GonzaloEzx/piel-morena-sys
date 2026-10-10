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
- CTA a `reservar.php` (secundario dentro de `#reservar`);
- agenda pública en `#reservar` (desde 2026-10-10), ver abajo;
- SEO basico y favicon;
- responsive segun design system.

### Pendiente de contenido

- reemplazo de placeholders del negocio;
- fotos reales;
- links reales a redes;
- mapa definitivo;
- testimonios nuevos segun criterio editorial del negocio.

## Agenda pública (`#reservar`)

La clienta elige servicio (opcional) y ve los turnos de los próximos 7 días.
Cada turno libre abre WhatsApp con servicio, día y hora escritos. La web
**no reserva**: el turno se confirma por WhatsApp.

- Datos: `api/citas/agenda-publica.php`, misma lógica que la reserva online
  (`includes/agenda.php`: horario del local, máquinas, jornadas, citas).
- Fuente (`AGENDA_FUENTE` en `config/config.php`):
  - `google` (por defecto): citas del sistema + Google Calendar del salón.
  - `db`: solo citas del sistema.
- **Si la agenda no se puede verificar** (sin calendario configurado o
  Google sin responder), se muestra solo el horario de atención y se ofrece
  consultar el día. Nunca se muestra un turno como libre sin verificarlo.
- Nunca se expone quién reservó: solo horarios.
- **Dependencia operativa:** en modo `google`, todo turno acordado por
  WhatsApp tiene que cargarse en el Google Calendar; si no, la web lo muestra
  libre.
- Textos de los mensajes: `AgendaPublica.mensajes` en `assets/js/main.js`.

## Regla

No documentar valores reales sensibles o comerciales en este contrato. Si falta contenido final, dejarlo como placeholder explicitamente marcado.
