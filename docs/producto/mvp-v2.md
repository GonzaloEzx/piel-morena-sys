# MVP v2 — Piel Morena: reservas reales y nueva imagen

> Estado: D1–D4 aprobadas por Gonzalo el 2026-10-31. v2.0 terminada en local, rama `v2-agenda`, sin push ni deploy.
> Audiencia: producto, desarrollo, agentes.
> Relación: antecedente y entrega local de la agenda. Gonzalo aceptó después
> simplificar el flujo público a WhatsApp; siguiente bloque: revisar
> [reservas-whatsapp.md](reservas-whatsapp.md) con Claude Code antes de implementar.
> Los contratos (`docs/contracts/`) se
> actualizan junto con cada entrega, según su regla de uso.
> Referencia: [MVP de Team Di Paola](../../../team-di-paola-kick-boxing-sys/docs/plans/mvp-v1.md).

El contenido siguiente conserva el alcance y los resultados de esa entrega.
Sus pasos pendientes no autorizan publicar la agenda completa tras el cambio
de dirección. La revisión de WhatsApp aún no modificó el comportamiento.

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
| Pedir turno por WhatsApp + panel | Preparado (commit local en `v2-agenda`) | Verificación del 31-10 abajo |
| `/test` en producción | En curso | Falta la base de prueba creada en hPanel |

Cada verificación nueva registra alcance, resultado y fecha propios.
Cuando este proyecto quede sin ediciones simultáneas, es candidato para la
prueba de continuidad del marco (dos sesiones nuevas reconstruyen el estado).

### Verificación — pedir turno por WhatsApp (2026-10-31, local)

- Cálculo interno: 25 casos OK, incluida la contención del bloqueo con dos conexiones (503 y sin escribir).
- curl sin sesión: `crear`, `gestionar`, `agenda-publica`, `mi-turno` y `maquinas/disponibles` dan 404; `disponibilidad` y `profesionales` dan 403.
- Playwright (Edge) a 390, 768 y 1440 px sobre una base de prueba armada con `database/demo/armar_base_test.php`: `#reservar` abre WhatsApp con el servicio; tarjetas con mensaje; `reservar.php?servicio=` destaca; id inválido sin destacado; sin pedidos a endpoints retirados ni errores JS. Sin JS: formulario y categorías funcionan.
- Panel a 390 px: abrir WhatsApp no cambia el estado, "Marcar confirmado" sí; alta rápida con horarios libres; disponibilidad con sesión.
- No verificado: número de WhatsApp desde un celular real; producción.

## Versiones

| Versión | Alcance | Estado |
|---|---|---|
| v1.x | Landing, wizard con cuenta, panel, caja, jornadas, máquinas, promos | En producción; falta verificar si está en `9fbe7f4` |
| **v2.0 Agenda real** | Disponibilidad por profesional, reserva como invitada, gestión del turno por la clienta, agenda del día y alta rápida en el panel, WhatsApp prearmado | Propuesta |
| v2.1 Imagen | Rediseño UI/UX de la landing y del flujo de reserva sobre el design system vigente | Propuesta |
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

Se ejecuta con la skill `mejorar-ui`: revisión a tres anchos, hallazgos al
taller y corrección con capturas de antes y después.

- Botón "Reservar turno" fijo abajo en mobile y CTA al final de cada
  servicio y de cada profesional, que entra al wizard con el servicio o la
  profesional ya elegidos.
- Landing más corta: hero, servicios destacados, equipo con fotos reales,
  reseñas y ubicación. La galería y los tratamientos completos pasan a
  páginas propias o a un desplegable.
- Rendimiento: imagen del hero en WebP o AVIF con `fetchpriority`, carga
  diferida de mapas y galería. Meta: LCP mobile menor a 2,5 s.
- SEO y accesibilidad: canonical, un solo `<h1>`, `title` del iframe, zonas
  táctiles de 44 px y contraste de `/reservar.php`.
- Paleta "Oliva & Arcilla" y tipografías vigentes; el design system se
  ajusta, no se reemplaza.

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
  los sábados.
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
