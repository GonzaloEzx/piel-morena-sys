# Contrato 08 - Landing Publica

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si
> Relacion: contrato funcional del home publico
> Ultima revision: 2026-10-31

## Alcance

Página principal, presentación de marca, orientación de servicios y contacto por WhatsApp.

## V2.1 — imagen autorizada el 2026-10-31

Gonzalo transmite la autorización de Mari para implementar el rediseño y
publicarlo en `/test`, con datos de ejemplo identificados. La raíz no forma
parte de esta publicación.

- Portada estática con un solo h1, fotografía de referencia y CTA visible.
- Recorrido: categorías, espacio/Mari, equipo, primera visita, ejemplos de
  experiencias, preguntas frecuentes, WhatsApp y ubicación.
- Catálogo en `reservar.php`: categorías nativas desplegables, precio y duración
  visibles, búsqueda opcional y servicio preseleccionado por URL.
- Navegación, categorías y enlaces de WhatsApp funcionan sin JavaScript.
- WhatsApp lleva el servicio en el mensaje; abrirlo no confirma una reserva.
- CTA inferior en móvil sin tapar el final de la página; menú con Escape y foco.
- Se retiran de esta presentación el carrusel, los grandes bloques de iniciales,
  el formulario de contacto y la duplicación de servicios/tratamientos.

### Presentación honesta de la demo

`includes/public-content.php` mantiene el contenido público de prueba separado
de la base. No crea ni modifica datos operativos. Los IDs de ejemplo son negativos.

Mientras `public_content_approved` no sea `true` en la configuración local:

- banner visible y `noindex` en meta y cabecera HTTP;
- servicios, precios ARS y duraciones marcados como ejemplos;
- fotografías existentes identificadas como referencias;
- experiencias identificadas como textos de ejemplo, sin estrellas ni autoras ficticias;
- horarios, políticas y presentación del equipo pendientes de validación visibles.

La aprobación del contenido definitivo requiere una revisión editorial conjunta;
no alcanza con ocultar el banner. Fuera del modo demo, el catálogo lee los servicios
del panel y mantiene el filtro de packs vigentes del contrato 02.

Los componentes públicos de v2 usan `public-header.php`, `public-footer.php`,
`public-v2.css` y `public-v2.js`. El panel y las pantallas de autenticación
conservan sus componentes actuales.

## Antecedente v1 / primera entrega de WhatsApp

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
