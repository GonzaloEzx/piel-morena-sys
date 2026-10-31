# MVP v2 — Piel Morena: reservas reales y nueva imagen

> Estado: v2.04 de refinamiento móvil publicada y verificada en `/test` (`cccd619`, 2026-10-31). La raíz conserva `0c75b1d`.
> Audiencia: producto, desarrollo, agentes.
> Alcance vigente: presentación pública, catálogo y primera visita en `/test`, con contenido de ejemplo identificado. La raíz y el panel no se rediseñan en este bloque.
> Los contratos de `docs/contracts/` acompañan cada entrega. Los apartados de agenda inicial se conservan como antecedente.

## v2.04 — refinamiento móvil

Gonzalo y Mari aprobaron el diseño anterior. Gonzalo autoriza una pasada de
estilo enfocada en móvil, con el nombre **v2.04**, el 2026-10-31.

Tres ejes: jerarquía y listas más cortas, marca/tipografía/espaciado, y acciones
que acompañan sin ocupar permanentemente la pantalla.

- Logo integrado con el fondo desde CSS; no se redibuja la marca.
- Tarjetas de servicios horizontales en móvil y categorías en dos columnas.
- Equipo y experiencias de ejemplo desplegables, sin retirar la información.
- Catálogo antes del bloque de asesoramiento, categorías cerradas inicialmente
  y detalles por servicio con nombre/precio/duración visibles al resumir.
- Avisos de demo ampliables, cabecera compacta que sigue al scroll, barra fija
  más baja y visible cuando hace falta; se oculta con el menú abierto.
- Controles de 48 px, fuentes legibles y menú desplazable para pantallas bajas.
- Conserva WhatsApp, selección por URL, búsqueda, teclado y funcionamiento sin JS.

### Evidencia y verificación

Antes y después: `ss-taller/reports/piel-morena-v204/{antes,despues}/`.
La captura a 390 × 844 px mostraba 9.430 px de inicio y el catálogo a 1.175 px
antes de su primera categoría. La entrega publicada deja 6.498 px y lleva
la búsqueda/categorías a 396 px. La reducción corresponde a la
composición y los desplegables, no a texto más pequeño ni a contenido retirado.

194 comprobaciones locales OK: 320/360/390/430, tablet, escritorio, WebKit,
ausencia de JS, menú horizontal, áreas táctiles y acceso progresivo a los servicios.
Taller local: 26 pasan / 22 omitidas / 0 fallan (las omisiones configuradas por
navegador y PageSpeed sin clave). Repetidas contra la URL pública: **194 OK**;
taller remoto: **26 pasan / 22 omitidas / 0 fallan**. Sin nueva medición de LCP.
Publicado desde `cccd619`; raíz comprobada en `0c75b1d` y sin escrituras de base.

Comparativa visual: `ss-taller/reports/piel-morena-v204/index.html`.
Auditoría remota: `ss-taller/reports/piel-morena-v204-test/`.
Rúbrica: antes 7,3 (diseño 7,5 / usabilidad 8 / idea 7 / contenido 5);
después 7,65 (8 / 8,5 / 7 / 5). El contenido de ejemplo mantiene su tope de 5.

La raíz y el panel quedan fuera del bloque. Se mantienen los datos dummy
identificados y todos los pendientes de material real de v2.1.

## Punto de partida anterior a la entrega d9eb1cf

- `main` = `origin/main` = `9fbe7f4` (10-10). Ese commit trae la agenda
  pública con turnos que abren WhatsApp y el arreglo del anti-solapamiento.
  **Su deploy quedó pendiente** por falta de clave SSH; no está verificado
  qué versión corre en pielmorenaestetica.com.ar.
- Hoy conviven tres agendas: el wizard `reservar.php` (base propia, exige
  cuenta), la agenda pública de la landing (base o Google Calendar, confirma
  por WhatsApp) y los turnos que Mari toma por WhatsApp sin cargarlos.
- **Defecto central:** `includes/agenda.php` bloquea un horario si existe
  *cualquier* cita activa ese día y a esa hora. El salón tiene seis
  profesionales de rubros distintos, pero el sistema atiende de a una
  clienta por vez. Las tablas `horarios` y `empleados_servicios` ya existen
  y el cálculo no las usa.
- No hay reserva sin cuenta (el backend la admite y la UI no), ni
  cancelación o cambio de turno por la clienta, ni recordatorio por
  WhatsApp; los avisos son por email.
- Diseño (auditoría del taller, 10-10): LCP mobile 4,45 s, inicio de unos
  19.000 px de alto, teléfono roto `tel:+54543624254052`, más de un `<h1>`,
  sin canonical y fallas de accesibilidad (iframe sin título, zonas táctiles
  chicas).

## Cómo opera el mercado

- **Software de turnos para estética en Argentina** (AgendaPro, ReservaTurno,
  Pedí Tu Turno): reserva desde link o QR, recordatorio por WhatsApp con
  confirmación o cancelación desde el mensaje, y seña anticipada, en algunos
  casos con Mercado Pago. Es la expectativa con la que llega la clienta.
- **Flujo de reserva:** primero el servicio, después la profesional (con
  la opción "cualquiera") y por último el horario, en tres pasos con el
  precio visible desde el principio. Cada campo obligatorio de más hace
  abandonar a más clientas.
- **Inasistencias:** las fuentes de proveedores hablan de 15–25 % de
  ausencias sin seña y de bajas fuertes con seña y recordatorio. Son cifras
  de los propios proveedores; sirven como dirección, no como dato.
- **Sitios de estética:** un botón "Reservar" fijo abajo en mobile, el CTA
  repetido al final de cada servicio y de cada profesional, carga rápida
  (LCP menor a 2,5 s) y prueba social (fotos reales y reseñas).

## Decisiones que necesita el plan

| # | Decisión | Recomendación |
|---|---|---|
| D1 | ¿Cuál es la agenda única? | El panel propio. Google Calendar queda como lectura opcional y no como fuente. Mari carga desde el celular los turnos que toma por WhatsApp (alta rápida, v2.0). |
| D2 | ¿Reserva con cuenta o como invitada? | Como invitada, con nombre y WhatsApp. La cuenta queda opcional para ver el historial. Cierra la decisión abierta del contrato 03. |
| D3 | ¿La web reserva o solo pide? | Reserva en estado `pendiente` y el salón confirma con un toque por WhatsApp. La confirmación automática queda para cuando exista la seña. |
| D4 | ¿Seña? | Solo en servicios largos o con máquina (láser), con Mercado Pago, en v2.2. Necesita la política de Mari. |

## Estado de entrega

Estados según el [marco del ecosistema](../../../../room_master/ECOSISTEMA_AGENTICO.md):
propuesto, en curso, preparado (commit local verificado), integrado (en `main`)
y desplegado (verificado en producción). Probar una rama no prueba producción.

| Bloque | Estado | Evidencia |
|---|---|---|
| Deploy `9fbe7f4` (agenda de la landing, 10-10) | Integrado, no desplegado | `origin/main`; producción sin verificar. Con el cambio a WhatsApp, su agenda pública queda reemplazada: se puede publicar directo el commit integrado siguiente |
| v2.0 agenda (`d9eb1cf`) | Reemplazado en lo público | Su reserva online y `mi-turno` no se publicaron; el panel y el cálculo siguen |
| Pedir turno por WhatsApp + panel | Desplegado en `/test` (rama `v2-agenda`), no en la raíz | Verificación local y en `/test` abajo |
| `/test` en producción | Desplegado y verificado en `3770a3e` | Worktree `public_html/test`; la demo pública no consulta ni escribe la base; el panel sigue usando la base compartida autorizada |
| Panel de profesionales (rama `claude/profesionales`) | Preparado | Local 31-10: Playwright a 390 y 1440 px, lista sin desborde, editor a pantalla completa en el celular, guardar, validaciones de la API y restauración de datos |
| Raíz de producción | Integrado en `0c75b1d` (mayo) | Sigue con el wizard viejo; comparte base con `/test`, ya con la 016 |

Cada verificación nueva registra alcance, resultado y fecha propios.
Cuando este proyecto quede sin ediciones simultáneas, es candidato para la
prueba de continuidad del marco (dos sesiones nuevas reconstruyen el estado).

### Verificación — pedir turno por WhatsApp (2026-10-31, local)

- Cálculo interno: 25 casos OK, incluida la contención del bloqueo con dos conexiones (503 y sin escribir).
- curl sin sesión: `crear`, `gestionar`, `agenda-publica`, `mi-turno` y `maquinas/disponibles` dan 404; `disponibilidad` y `profesionales` dan 403.
- Playwright (Edge) a 390, 768 y 1440 px sobre una base de prueba armada con `database/demo/armar_base_test.php`: `#reservar` abre WhatsApp con el servicio; tarjetas con mensaje; `reservar.php?servicio=` destaca; id inválido sin destacado; sin pedidos a endpoints retirados ni errores JS. Sin JS: formulario y categorías funcionan.
- Panel a 390 px: abrir WhatsApp no cambia el estado, "Marcar confirmado" sí; alta rápida con horarios libres; disponibilidad con sesión.
- Gonzalo verificó desde el celular que el WhatsApp actual abre el número correcto (relevo de Claude, 2026-10-31).

### Verificación — `/test` en producción (2026-10-31)

- Base de producción respaldada en `~/backups/pielmorena-pre-v2-20261031.sql.gz` antes de tocarla; migración 016 aplicada (MariaDB 11.8) y datos de demo de `database/demo/seed_demo_produccion.sql` (asignaciones por categoría a las profesionales reales, horario de Naila, dos citas `demo-v2`).
- Playwright contra `https://pielmorenaestetica.com.ar/test`: los 22 pasos OK (público a 390/768/1440 px, sin JS y panel con admin temporal borrada al terminar).
- `/test` envía `X-Robots-Tag: noindex`; `.sql` y `database/` dan 403/404. La raíz sigue respondiendo con su código de mayo.

## Versiones

| Versión | Alcance | Estado |
|---|---|---|
| v1.x | Landing, wizard con cuenta, panel, caja, jornadas, máquinas, promos | En producción; falta verificar si está en `9fbe7f4` |
| **v2.0 Agenda real** | Disponibilidad por profesional, reserva como invitada, gestión del turno por la clienta, agenda del día y alta rápida en el panel, WhatsApp prearmado | Propuesta |
| v2.04 Estilo móvil | Refinamiento de portada y catálogo para móvil | Desplegado y verificado en `/test` (`cccd619`) |
| v2.1 Imagen | Portada, catálogo y primera visita con demo explícita | Antecedente publicado (`3770a3e`); refinado por v2.04 |
| v2.2 Seña y tienda | Seña con Mercado Pago, lista de espera, tienda de productos con pedido por WhatsApp | Propuesta |
| v3 | Recordatorios automáticos por la API de WhatsApp Business, fidelización y reportes por profesional | Futuro |

## v2.0 Agenda real (reservas)

- **Capacidad por profesional:** un turno está libre si alguna profesional
  que hace ese servicio (`empleados_servicios`) trabaja ese día y a esa hora
  (`horarios`) y no tiene una cita superpuesta. Se mantienen los bloqueos
  por máquina, alquiler y jornada. Al crear la cita, `id_empleado` se
  asigna en la misma transacción (elegida o la primera libre).
- **Reserva en tres pasos:** servicio → profesional ("Cualquiera" por
  defecto) y día → horario, con nombre y WhatsApp al confirmar. La clienta
  existente se reconoce por teléfono. Un único motor de disponibilidad
  alimenta el wizard y la agenda pública; desaparece el doble camino.
- **Gestión por la clienta:** link con token en la confirmación para ver,
  cancelar o reprogramar dentro de la política (hoy, 2 horas antes).
- **Panel:** agenda del día por profesional en columnas, pensada para el
  celular, con alta rápida del turno tomado por WhatsApp (teléfono,
  servicio, hora, en menos de 20 segundos), y bandeja de pendientes con
  botón "Confirmar por WhatsApp" con el mensaje armado, como las cobranzas
  de Di Paola.
- **Recordatorio del día siguiente:** lista con envío por WhatsApp en un
  toque (`wa.me`), sin API paga. El email existente se mantiene.
- **Datos y deuda:** migración 016 si hace falta (token de gestión y
  teléfono normalizado); carga real de horarios y servicios por profesional;
  arreglo del teléfono roto.
- **Verificación:** casos de disponibilidad con varias profesionales en
  paralelo, una sola profesional, máquina compartida y jornada;
  doble reserva simultánea; flujo en mobile con Playwright.

### Ejecución v2.0, paso a paso

| Paso | Cambio | Cierre |
|---|---|---|
| 1 | Rama `v2-agenda`, migración 016 (email opcional, token de gestión, teléfono normalizado) y datos de demo local con profesionales y horarios | La migración corre dos veces sin error; la demo carga |
| 2 | Motor de disponibilidad por profesional y reserva atómica (bloqueo por fecha, nueva verificación y asignación) en `includes/agenda.php` | Script de casos: paralelo, una profesional, máquina compartida, jornada, sin asignaciones (comportamiento anterior) |
| 3 | APIs públicas: profesionales por servicio, disponibilidad con profesional, creación como invitada y gestión con token | Respuestas probadas con curl, incluida la doble reserva |
| 4 | Wizard sin login en tres pasos y página `mi-turno.php` para cancelar o reprogramar | Recorrido completo en mobile con Playwright |
| 5 | Panel: agenda del día por profesional, alta rápida, confirmación y recordatorio por WhatsApp; el alta del panel usa el motor | Recorrido en mobile con Playwright |
| 6 | Contratos 03 y 04, `disponibilidad-y-reglas.md` y commit local | Diff revisado, `php -l` en los archivos tocados |

**Resultado (2026-10-31, local):**

- Pasos 1–6 hechos. La migración 016 no es repetible (como las anteriores: la segunda vez da "ya existe"); los datos de demo sí.
- Cálculo: 23 casos OK (paralelo, profesional elegida, horarios, máquina, jornada, modo salón, reservas sucesivas, Google, reprogramar, panel) y 882 casos idénticos a v1 sin profesionales asignadas.
- APIs probadas con curl: doble reserva (la tercera da 409), validación, campo trampa, gestión con token.
- Playwright en Edge, 390 px: reserva sin cuenta → éxito → mi turno → reprogramar → cancelar, preselección por URL; panel: agenda por profesional, filtro, alta rápida, alta superpuesta rechazada, confirmar por WhatsApp. Sin errores de consola.
- Arreglados durante la prueba: respuestas viejas que pisaban a las nuevas en el alta rápida, y búsqueda de clienta por teléfono con espacios.
- Para producción: correr la migración 016 y que Mari cargue en Empleados los servicios y horarios de cada profesional. Mientras no estén cargados, cada servicio funciona como en v1.

## v2.1 Imagen (UI/UX)

**Autorizado el 2026-10-31:** Gonzalo transmite el permiso de Mari para aplicar
la propuesta de marca/UX y publicar en `/test`. Admite datos dummy explícitos.
Se mantiene el flujo de WhatsApp y el panel operativo.

- Portada estática con fotografía, voz de marca «Estamos para cuidarte» y CTA.
- Servicios agrupados; catálogo aparte con precios ARS, duración, búsqueda,
  asesoramiento y mensajes por servicio. Categorías y navegación sin JS.
- Presentación de Mari y equipo sin retratos inventados ni grandes bloques de iniciales.
- Primera visita, FAQ, ubicación y experiencias de ejemplo identificadas.
- Banner de prueba, noindex HTTP/meta y precios/fotos/textos marcados.
- Contenido demo fuera de la base operativa. Los IDs negativos de ejemplo
  no se usan en la agenda. Publicación de datos definitivos exige revisión
  editorial; `public_content_approved` permanece desactivado por defecto.
- Paleta «Oliva & Arcilla», Playfair Display/DM Sans, CSS propio sobre Bootstrap.
- Fotos existentes optimizadas a WebP. Panel y auth conservan sus componentes.

### Material que Gonzalo puede conseguir

1. Retrato de Mari y fotos del equipo, con nombres y especialidades confirmados.
2. Fotos actuales del interior y de trabajos autorizados para publicar; confirmar
   la procedencia y permiso de las imágenes de referencia existentes.
3. Presentación breve de Mari en su voz y versión principal del logo.
4. Catálogo vigente: servicios, qué incluye cada uno, precio, duración y profesional.
5. Horarios generales, jornadas y medios de pago; reglas de seña, cancelación y cambios.
6. Dos o tres testimonios reales, con permiso y procedencia confirmada.
WhatsApp ya fue probado por Gonzalo desde el celular: número actual correcto.

Estos materiales no bloquean la demo; sí la publicación definitiva sin etiquetas.

### Verificación de esta entrega

- 112 comprobaciones locales OK: 320/390/768/1440 px, un h1, noindex,
  imágenes, ausencia de desborde, menú con Escape/foco, búsqueda sin acentos,
  vacío/limpiar, selección válida e inválida, WhatsApp por servicio, formulario
  GET sin JS, WebKit móvil y accesibilidad axe. No se envían mensajes.
- Taller local: 26 pruebas pasan, 22 omitidas por alcance de navegador o
  PageSpeed sin clave. Smoke, accesibilidad WCAG 2.2, capturas, noindex y CLS
  dentro del umbral. Evidencia: `ss-taller/reports/piel-morena-v2-local/`.
- Capturas antes: `ss-taller/reports/piel-morena-v2/antes/`.
  Después: `.playwright/public-v2/`. La primera pantalla móvil se ajustó para
  incluir la fotografía y el CTA sin obligar a leer varios párrafos.
- Rúbrica de diseño: antes 5,6 (diseño 5,5 / usabilidad 6 / idea 5,5 / contenido 5);
  después 7,3 (7,5 / 8 / 7 / 5). Contenido limitado a 5 por los ejemplos
  explícitos y las fotos/presentaciones aún pendientes. No es una valoración
  de la futura versión con material definitivo.
- Publicado en `/test` desde el commit `3770a3e`. Repetidas las 112 comprobaciones
  contra la URL pública: todas OK. Taller remoto: 26 pasan / 22 omitidas / 0 fallan.
  Las omisiones son las configuradas por navegador y PageSpeed sin clave;
  no hay medición nueva de LCP. Capturas y resumen en
  `ss-taller/reports/piel-morena-v2-test/`.
- Raíz verificada por SSH: `0c75b1d`, sin desplegar cambios allí. Sin migraciones
  ni escrituras de datos en este bloque. `.htaccess` local del servidor preservado.
- El siguiente bloque de Claude (servicios/horarios por profesional) sigue en
  [su relevo](relevo-claude-2026-10-31.md), a la espera de la señal de Gonzalo.

## v2.2 Seña y tienda

- Seña por link de Mercado Pago en los servicios marcados; la cita se
  confirma sola al acreditarse el pago (webhook) y se libera si vence.
- Lista de espera: si se cancela un turno, se avisa por WhatsApp a la
  primera interesada.
- Tienda: productos del inventario (contrato 09) con opción de publicarlos
  y pedido por WhatsApp, igual que la tienda DPC.

## Lo que falta para continuar

**De Mari (negocio):**
- Qué servicios hace cada profesional y sus horarios reales; si atienden
  los sábados. Se cargan en Panel → Profesionales; hoy hay datos de demo.
- Política de cancelación, de inasistencias y de seña (monto y servicios).
- Cuántas clientas atiende a la vez cada rubro (camillas o puestos) y qué
  máquinas comparten.
- Fotos del equipo, logo en PNG o SVG, reseñas que se puedan publicar y el
  texto de "Sobre nosotros".
- Si va a usar el panel desde el celular para cargar los turnos de
  WhatsApp. Sin esa rutina, la agenda online no es confiable.

**De Gonzalo (operación):**
- Acceso SSH a Hostinger (clave cargada en hPanel) o el deploy hecho por
  él.
- Comprobar qué commit corre en producción y el `git status` del servidor
  por el aviso "Malware limpiado".
- Aprobar D1–D4.
- Restablecer la dirección iCal secreta del calendario de prueba.
- Para v2.2: cuenta de Mercado Pago del salón (credenciales fuera del repo).

## Orden propuesto

1. Desplegar `9fbe7f4` y verificar producción.
2. v2.0 en una rama, con contratos 03 y 04 actualizados en la misma
   entrega; demo local y en producción después de la aprobación.
3. v2.1 sobre la v2.0 ya publicada, para que el CTA lleve al flujo nuevo.
4. v2.2 cuando estén la política de seña y Mercado Pago.

## Fuentes de mercado (31-10-2026)

- [AgendaPro — software para centros de estética](https://agendapro.com/ar/centros-de-estetica/software-para-centros-de-estetica)
- [ReservaTurno (Dealroom)](https://app.dealroom.co/companies/reservaturno)
- [Pedí Tu Turno (Capterra)](https://www.capterra.es/software/1113334/Pedi-Tu-Turno)
- [Salon Booking System — customer booking system](https://www.salonbookingsystem.com/salon-booking-system-blog/customer-booking-system)
- [Goldie — depósitos y reservas digitales](https://heygoldie.com/blog/digital-booking-and-deposits-for-stylists)
- [Workee — reservas de med spa desde la web](https://workee.net/blog/get-more-med-spa-bookings-from-website)
- [Redefine Web — diseño web para salones](https://redefineweb.com/blog/beauty-salon-web-design-services-book-chairs-fast/)
