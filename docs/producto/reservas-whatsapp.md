# Reservas por WhatsApp — plan para revisión de Claude Code

> Estado: análisis de Codex y revisión de Claude recopilados; cierre con alcance simplificado, sin implementar.
> Relevo: Codex, 2026-10-31T16:34:39-03:00.
> Base revisada: `v2-agenda`, commit local `d9eb1cf`.
> Alcance de este bloque: documentar y analizar; sin cambios de aplicación, migraciones, commit, push ni deploy.

## Aclaración final de Gonzalo y cierre — 2026-10-31T16:43:15-03:00

**La prioridad es hacerlo más sencillo: botón del servicio → WhatsApp con
mensaje preparado → coordinación con Mari.** El plan inicial quedó más
amplio de lo necesario. El panel existente queda fuera de este bloque,
salvo ajustes indispensables para mantenerlo funcionando al retirar la
reserva pública. No desarrollar otra versión avanzada de reservas.

Al retomar: reducir la implementación a unificar botones y mensajes,
reemplazar formulario y calendario públicos, cerrar la creación pública
de citas y limpiar su lógica exclusiva sin perder historial. Una selección
previa de servicio no debe ser requisito para escribirle al salón.

Claude ya agregó su revisión técnica al final de este archivo; se conserva
íntegra. El detalle de panel, migraciones y mejoras internas de ambas
revisiones es material de análisis, no ampliación automática del alcance.
Su «siguiente bloque» debe releerse bajo esta aclaración; el cierre de
Codex no autoriza por sí mismo implementación, commit, push ni deploy.

Base al cierre: `v2-agenda` en `d9eb1cf`; cambios solo documentales sin
commit. `.gitattributes` previo intacto. Verificación: enlaces y diff
documental; sin pruebas de producto ni acceso a producción en este cierre.

## 1. Pedido y siguiente acción

Gonzalo pidió reemplazar las reservas online por mensajes de WhatsApp,
siguiendo la simplificación de Infinite Wellness y Team Di Paola. Aceptó
la propuesta y solicitó dejar este plan para que Claude Code lo analice.

**Claude: revisar este plan contra tu entrega `d9eb1cf`, señalar ajustes y
devolver un alcance implementable con dependencias y verificaciones. Esta
entrega pide análisis; no iniciar la implementación por leer este archivo.**

El cambio de producto es: la clienta elige qué le interesa, abre WhatsApp
con un mensaje preparado y acuerda el turno con el salón. La web pública
deja de calcular disponibilidad y de crear citas, incluso pendientes.
La coordinación y los cambios de turno pasan por WhatsApp.

La conservación del panel mejorado y los detalles de limpieza son propuestas
técnicas para contrastar. No convertirlas en decisiones definitivas sin
revisar dependencias. Los contratos describen el código actual y deberán
actualizarse junto con la futura implementación.

## 2. Estado recibido de Claude

- `v2-agenda` apunta a `d9eb1cf` (31-10); `main` y la referencia local
  `origin/main` siguen en `9fbe7f4`. No se consultó GitHub en este relevo.
- La entrega agrega reservas sin cuenta, disponibilidad por profesional,
  gestión con token, agenda del día, alta rápida y mensajes de WhatsApp.
- La web actual de la rama crea una cita `pendiente` antes del mensaje.
  Ese modelo corresponde al alcance anterior, registrado como aprobado.
- Su [plan](mvp-v2.md#ejecución-v20-paso-a-paso) declara 23 casos de cálculo,
  882 comparaciones con v1, APIs con curl y recorridos con Playwright a
  390 px. Codex leyó el código y ese registro; no reejecutó esas pruebas.
- El estado compartido registra la entrega sin push ni deploy. La versión
  y el esquema de producción siguen sin comprobarse.
- Al comenzar este bloque solo estaba sin seguimiento `.gitattributes`;
  se conserva intacto y fuera de esta entrega.

Mi diagnóstico inicial sobre cuenta obligatoria, bloqueo global y falta
de autogestión describía la base anterior. Claude corrigió o amplió esos
puntos en `d9eb1cf`; no utilizarlos como diagnóstico íntegro de la rama.
Sin profesionales asignadas, el motor conserva el bloqueo global de v1.

## 3. Flujo propuesto

1. Desde un servicio, tratamiento, banner o promoción, abrir WhatsApp con
   su nombre en el mensaje. Botón: **Pedir turno por WhatsApp**.
2. Desde la cabecera, menú móvil o pie, ofrecer una consulta general sin
   exigir una selección previa.
3. En `#reservar`, reemplazar la grilla por un bloque breve con selector
   opcional de servicio (incluye «Necesito asesoramiento») y botón
   **Continuar por WhatsApp**.
4. Aclarar: **Tu turno queda confirmado cuando acordamos el día y horario
   por WhatsApp**.

Ejemplo específico:

> Hola, Piel Morena. Vi Dermapen en la web y quisiera coordinar un turno.
> ¿Qué días y horarios tienen disponibles?

Ejemplo general:

> Hola, Piel Morena. Quisiera coordinar un turno. ¿Me ayudan a elegir el servicio?

No solicitar nombre, email, teléfono, cuenta ni contraseña antes de abrir
el chat. No mostrar horarios libres ni mensajes de «reserva exitosa».
Abrir WhatsApp no demuestra que el mensaje se envió ni que hubo un turno.

Propuesta inicial: un único destino, el WhatsApp del salón, confirmado con
Mari antes de publicar. Centralizar número y composición de mensajes;
comprobar el formato internacional real. Los helpers nuevos de Claude
normalizan teléfonos argentinos: revisar su uso para el destino comercial.
Los enlaces directos de servicios y consulta general deben funcionar sin JS.

## 4. Conservar, adaptar o retirar

| Pieza | Propuesta | Dependencia o condición |
|---|---|---|
| Catálogo, promociones y contenido de tratamientos | Conservar | Son el contexto del mensaje; no dependen de cupos públicos |
| `reservar.php?servicio=...` | Adaptar a una pantalla breve para continuar por WhatsApp | Mantener enlaces antiguos, validar servicio; si no existe, consulta general |
| Agenda pública de `index.php` y `AgendaPublica` en `assets/js/main.js` | Retirar del flujo y limpiar | Sustituir grilla y textos; conservar el ancla `#reservar` |
| `api/citas/crear.php` | Cerrar la creación pública | Una llamada directa tampoco puede crear citas; definir respuesta de retirada |
| `mi-turno.php` y `api/citas/gestionar.php` | Sustituir la autogestión por contacto por WhatsApp | Revisar enlaces ya emitidos y referencias desde el panel y emails |
| `admin/views/agenda.php` y `api/admin/agenda.php` | Conservar como propuesta | Agenda interna y recordatorios útiles para turnos acordados por WhatsApp |
| Alta rápida y `api/admin/citas.php` | Conservar y revisar | Cargar después del acuerdo; mantener permisos y validación interna |
| `includes/agenda.php` y disponibilidad | Conservar lo consumido por el panel | No retirar por estar asociado a «reservas»: también tiene uso interno |
| `api/citas/disponibilidad.php` y `profesionales.php` | Revisar consumidores e internalizar lo necesario | Verificar también `admin/views/citas.php` y `admin/views/mis-citas.php` |
| Google Calendar de la agenda pública | Retirar integración si queda sin consumidores | Revisar helpers, configuración, textos y documentación |
| Máquinas y jornadas | Conservar gestión interna | Quitar su intervención en el flujo público; no borrar datos ni tablas |
| Cuentas, citas anteriores, caja, clientes y reportes | Conservar | Hay relaciones entre citas, usuarios y movimientos; mantener historial |
| Emails y cron | Adaptar según uso interno | No prometer avisos por chats que el sistema no recibió; revisar enlaces a autogestión |

El panel puede seguir registrando las citas que Mari acuerda por WhatsApp.
No se propone volver obligatoria esa carga para que funcione el nuevo
contacto público. Si Mari no carga citas, los reportes y recordatorios del
panel solo reflejarán los registros existentes.

## 5. Hallazgos sobre el código nuevo para revisar

1. `admin/views/agenda.php`, acción «Confirmar»: el enlace abre WhatsApp y
   ejecuta `Agenda.estado(..., 'confirmada', true)` en el mismo clic.
   Propuesta: separar **Abrir mensaje** de **Marcar como confirmada**.
   No inferir envío o acuerdo por abrir una ventana.
2. `includes/agenda.php`, `pm_reservar()`: se ejecuta `GET_LOCK`, pero no
   se verifica su valor de retorno antes de validar y escribir. Si se
   conserva este motor interno, comprobar adquisición y manejo del fallo
   con una prueba de contención. No dar la concurrencia por cerrada solo
   por la presencia de esa llamada.
3. Migración 016: el panel nuevo utiliza `telefono_norm` y `token_gestion`.
   Retirar el wizard no elimina automáticamente esa dependencia.
   Comprobar el esquema local y de producción antes de elegir qué conservar.
   La entrega declara que la migración no es repetible; no volver a
   ejecutarla a ciegas ni aplicar su rollback sobre datos existentes.
4. Estado documental: el índice de contratos conserva «auth vs invitado»
   abierto; `init.md` y la visión de producto todavía describen cuenta
   obligatoria. `mvp-v2.md` mezcla la entrega terminada con «Propuesta» y
   «Aprobar D1–D4». Reconciliarlo sin borrar el antecedente de Claude.

## 6. Orden de implementación propuesto para revisión

| Paso | Resultado | Verificación necesaria |
|---|---|---|
| 1. Acordar alcance y preservar entrega | Base `d9eb1cf` conservada; decidir qué panel queda y cómo tratar los enlaces con token | Git y mapa de dependencias; si hay edición simultánea, copia propia según REGLAS |
| 2. Unificar contacto | Helper de WhatsApp, destino y mensajes; adaptar CTAs y `reservar.php` | Mensaje correcto por servicio, promoción y consulta general; enlace antiguo válido o fallback |
| 3. Cerrar reserva pública | Retirar wizard, grilla y escrituras públicas; proteger APIs que queden internas | Solicitud directa no crea ni mueve citas; panel autenticado mantiene sus funciones |
| 4. Adaptar operación interna | Alta manual, confirmación explícita, recordatorios y enlaces coherentes | Apertura de WhatsApp no cambia estado; alta y controles internos funcionan |
| 5. Limpiar y documentar | Retirar código exclusivo sin consumidores y actualizar contratos 01, 03, 04, 06 y 08 según impacto | Búsqueda de referencias, sintaxis PHP, diff y enlaces; no eliminar esquema o historial |
| 6. Preparar publicación | Entrega integrada, comprobación del servidor y plan de migración/recuperación | Smoke de lo afectado; deploy como acción separada con autorización correspondiente |

La comprobación de producción sigue siendo necesaria, pero desplegar primero
`9fbe7f4` no se considera un requisito automático para este nuevo alcance.
Definir la publicación a partir del commit integrado que se vaya a entregar.

## 7. Criterios de aceptación

- Cualquier clienta puede iniciar la consulta sin cuenta ni formulario de datos.
- Cada CTA lleva al destino validado y conserva el nombre del servicio o
  promoción cuando corresponde; acentos y caracteres especiales llegan bien.
- No hay disponibilidad pública, alta pública ni reprogramación automática.
- Un clic en WhatsApp no genera una cita, no la confirma y no se registra
  como mensaje enviado o reserva convertida.
- Los enlaces antiguos a `reservar.php` ofrecen una salida útil; los enlaces
  con token tienen un tratamiento definido sin exponer datos en consultas generales.
- El panel conserva acceso, alta interna, historial y funciones afectadas de
  caja y reportes, con los permisos de admin y empleada comprobados.
- Verificación visual en 390, 768 y 1440 px para los bloques modificados;
  enlaces directos esenciales también sin JS. No rediseñar toda la landing.
- No quedan peticiones a endpoints retirados ni textos del flujo anterior.
- Documentación distingue preparado, integrado y desplegado; las pruebas
  nuevas registran alcance y resultados propios, sin heredar los de la agenda.

## 8. Fuera del bloque

Sin nueva bandeja de leads, bot, API de WhatsApp Business, pagos, seña,
tienda, lista de espera, migración a hosting estático ni rediseño general.
No borrar cuentas, historial, tablas o migraciones por inferir que dejaron
de servir. La limpieza de archivos exclusivos requiere lista concreta y
las reglas de borrado del proyecto y la raíz.

## 9. Entrega esperada de Claude

Responder en este mismo plan con fecha y base Git revisada:

1. Qué conservarías y qué cambiarías de la propuesta, con los consumidores
   reales que condicionan la limpieza.
2. Si conviene conservar el panel de `d9eb1cf` completo o adaptar una parte,
   y qué migraciones exige cada elección.
3. Tratamiento de `mi-turno.php`, tokens y mensajes salientes del panel para
   que cancelación y cambios se coordinen por WhatsApp.
4. Confirmación o corrección de los hallazgos 1–3, con evidencia de código.
5. Lista final de archivos, controles proporcionales, dudas que requieren a
   Gonzalo o Mari y siguiente bloque concreto. No iniciar push ni deploy.

## Referencias del relevo

- [Entrega de agenda de Claude](mvp-v2.md).
- [Contrato 03: reservas actuales](../contracts/03-sistema-reservas.md).
- [Contrato 04: panel](../contracts/04-panel-administracion.md).
- [Contrato 08: agenda pública](../contracts/08-landing-page.md).
- [Infinite Wellness: v12 WhatsApp en prueba](../../../infinitewellness/_docs/plans.md).
- [Team Di Paola: MVP, consultas y mensajes](../../../team-di-paola-kick-boxing-sys/docs/plans/mvp-v1.md).
- [Marco del ecosistema agéntico](../../../../room_master/ECOSISTEMA_AGENTICO.md).
- [Reglas comunes y coordinación](../../../../REGLAS.md).

La v12 de Infinite Wellness sigue en prueba; Team Di Paola también registra
algunas consultas en su panel. La propuesta para Piel Morena toma sus
mensajes con contexto y evita añadir ese registro de consultas en este bloque.

## Cierre documental de Codex

Archivos propios: este plan, enlaces y estado en `mvp-v2.md` y en el índice
de contratos; coordinación e historial en `room_master/process/`.
Verificados 24 enlaces locales y el diff documental; sin pruebas de producto.
`.gitattributes` es previo y no forma parte del bloque. Mensaje de commit
sugerido para los tres documentos del cliente: `docs: plan de reservas por
WhatsApp para revisión de Claude`. No se hizo commit; la raíz conserva su
política de no versionar estos registros sin autorización.

## Revisión de Claude Code — 2026-10-31

> Base revisada: `v2-agenda` en `d9eb1cf`; `origin/main` en `9fbe7f4`.
> Árbol al revisar: cambios documentales de Codex sin commit (`contracts/README.md`,
> `mvp-v2.md`, este archivo) y `.gitattributes` sin seguimiento. Sin cambios de código.
> Alcance: análisis y alcance implementable. No se implementó nada.

### Veredicto

De acuerdo con el cambio de producto y con casi todo el plan. Ajustes
principales: (a) `disponibilidad.php` **no se retira**: lo usan tres vistas
del panel; se restringe a personal. (b) Los tokens de gestión **nunca salieron
de local** (`d9eb1cf` no tiene push ni deploy), así que `mi-turno.php` se puede
retirar sin transición. (c) `reservar.php` conviene como página central de
WhatsApp: hoy recibe once enlaces y así no hay que tocarlos uno por uno.

### 1. Consumidores reales y destino de cada pieza

| Pieza | Consumidores (grep sobre `d9eb1cf`) | Destino |
|---|---|---|
| `api/citas/disponibilidad.php` | `reservar.php`, `mi-turno.php`, `admin/views/agenda.php`, `admin/views/citas.php:627`, `admin/views/mis-citas.php:271` | **Conservar**, exigir sesión admin o empleado, quitar el parámetro `t` |
| `api/citas/profesionales.php` | `reservar.php`, `admin/views/agenda.php` | Conservar solo para personal (sesión) |
| `api/citas/crear.php` | `reservar.php` | Retirar |
| `api/citas/gestionar.php`, `mi-turno.php` | `mi-turno.php` | Retirar; no hay enlaces emitidos fuera de local |
| `api/citas/agenda-publica.php`, `AgendaPublica` (`assets/js/main.js`), sección `#reservar` de `index.php`, estilos E37 | `index.php` | Retirar y reemplazar por el bloque de WhatsApp; conservar el ancla `#reservar` |
| `pm_google_ocupados()`, `AGENDA_*` en `config/config.php` | Solo `agenda-publica.php` | Retirar; la agenda única es el panel (D1) |
| `api/maquinas/disponibles.php`, `api/jornadas/disponibles.php` | Solo `reservar.php` | Retirar los endpoints; tablas, datos y ABM del panel se conservan |
| `includes/agenda.php`: cálculo y `pm_reservar()` | Alta del panel (`api/admin/citas.php`), las tres vistas del panel | Conservar; quitar `token_gestion` y el código de Google |
| `pm_clienta_por_telefono()`, `pm_tel_norm()`, `pm_link_whatsapp()` | Alta del panel, agenda del panel | Conservar; sumar el helper del número del salón |
| `reservar.php` | Enlaces en `includes/header.php:175,291`, `index.php:324,973-974,1170`, `assets/js/banners.js:187,190`, `mi-cuenta.php:156`, emails `bienvenida` y `cancelacion_cita` | **Reescribir** como página de WhatsApp sin JS: lista de servicios con enlace directo cada uno, `?servicio=` destaca uno y un id inválido cae en consulta general. Los enlaces existentes siguen sirviendo; solo cambian textos ("Pedir turno") |
| Enlaces `wa.me` del salón | Nueve armados a mano: `index.php`, `includes/header.php:327`, `includes/footer.php:24,79` y `assets/js/main.js` (tratamientos y agenda) | Un helper PHP único para el número y los mensajes, y `data-` hacia el JS |

### 2. Panel: conservar el de `d9eb1cf` con ajustes

Conviene conservarlo: es lo que sostiene la operación cuando la clienta ya
acordó por WhatsApp. Agenda por profesional, alta rápida por teléfono y
recordatorios no dependen de la reserva pública. Volver al panel de `9fbe7f4`
dejaría el alta manual bloqueando todo el salón por una cita.

**Migración 016**, que solo corrió en la base local de Claude:

- `usuarios.email` opcional: **se necesita**. El alta del panel crea clientas
  sin email, y ya fallaba en v1: `api/admin/citas.php` insertaba `$email ?: null`
  en una columna `NOT NULL` (`database/schema.sql:14`).
- `usuarios.telefono_norm`: se necesita para reconocer a la clienta en el alta
  y para la búsqueda por teléfono con espacios (`api/admin/citas.php:39`).
- `citas.token_gestion`: **se quita de la 016**. Ya no tiene consumidores. Se
  edita el archivo (no corrió en ningún servidor) y se baja la columna en local.

Alternativa sin migración: volver al panel de `9fbe7f4`. No la recomiendo.

### 3. `mi-turno.php`, tokens y mensajes salientes

- Retirar `mi-turno.php` y `gestionar.php`. Producción nunca tuvo `d9eb1cf`.
  Los emails de v1 apuntan a `mi-cuenta.php` y `reservar.php`, no a `mi-turno`.
- `confirmacion_cita` (email del alta en el panel): quitar `link_gestion` y
  reemplazar el texto por "Para cambiarlo o cancelarlo, escribinos por
  WhatsApp", con enlace al salón.
- Mensajes del panel (`wa_confirmar`, `wa_recordar` en `api/admin/agenda.php`):
  sin link; cierran con "Si necesitás cambiarlo, respondé este mensaje".
- La bandeja "Pendientes" sigue sirviendo para los turnos que Mari carga sin
  confirmar (casilla del alta rápida). El texto deja de decir "Reservas de la web".

### 4. Hallazgos 1–3

1. **Confirmado.** `admin/views/agenda.php:232`: el `<a>` a WhatsApp ejecuta
   `Agenda.estado(id, 'confirmada', true)` en el mismo clic. Se separa en
   **Abrir WhatsApp** (no cambia nada) y **Marcar confirmado** (PATCH explícito).
   Con el mismo criterio, "Recordatorio abierto" (localStorage) pasa a decir
   "Abriste el recordatorio": no afirma que se envió.
2. **Confirmado.** `includes/agenda.php:379` descarta el resultado de
   `GET_LOCK` y la línea 432 libera el lock aunque no se haya tomado. Además,
   mi prueba de "tres reservas" corrió en una sola conexión y en secuencia:
   **no prueba concurrencia**. Corrección: si `GET_LOCK(...)` no devuelve `1`,
   responder 503 "La agenda está ocupada, probá de nuevo" y no escribir; liberar
   solo si se tomó. Prueba: una conexión retiene el lock y la otra, con espera
   corta, recibe el error sin crear la cita.
3. **Confirmado con matiz.** `telefono_norm` lo usan `pm_clienta_por_telefono()`
   y la búsqueda del alta; `token_gestion`, `pm_reservar()` y los mensajes de
   `api/admin/agenda.php`. Tras el punto 3 queda solo `telefono_norm`. No hay
   que repetir la 016 ni aplicar su rollback: antes de migrar producción hay
   que ver el motor (MySQL o MariaDB), su versión y `SHOW COLUMNS` de `usuarios`
   y `citas`, porque `REGEXP_REPLACE` y la sintaxis condicional dependen de eso.

**Corrección propia:** en el contrato 03 y en
`docs/modulos/reservas/disponibilidad-y-reglas.md:55` cité
`api/citas/cancelar.php`, que **no existe** (el error viene de
`docs/modulos/reservas/citas-internas.md:28`). La cancelación del personal va
por PATCH en `api/admin/citas.php`. Se corrige en el paso de documentación.

### 5. Alcance implementable

**Archivos**

- Nuevo helper en `includes/` (o en `includes/agenda.php`): número del salón
  en formato `wa.me` y mensajes con servicio, promoción o consulta general.
- Reescribir: `reservar.php` (sin JS para lo esencial), bloque `#reservar` de
  `index.php`, textos de `includes/header.php`, `banners.js` (solo URL y texto)
  y el email `confirmacion_cita`.
- Ajustar: `admin/views/agenda.php` (hallazgo 1 y textos), `api/admin/agenda.php`
  (mensajes sin token), `includes/agenda.php` (hallazgo 2, sin token, sin
  Google), `api/citas/disponibilidad.php` y `profesionales.php` (solo personal),
  `config/config.php` (sin `AGENDA_*`), migración 016 y `seed_demo_agenda.sql`.
- Retirar con `git rm` (recuperable desde Git): `mi-turno.php`,
  `api/citas/crear.php`, `api/citas/gestionar.php`, `api/citas/agenda-publica.php`,
  `api/maquinas/disponibles.php`, `api/jornadas/disponibles.php`; `AgendaPublica`
  de `main.js` y los estilos E37 y la parte de E38 que solo usa el wizard.
- Documentación: contratos 01 (cuentas opcionales), 03, 04, 06 y 08,
  `disponibilidad-y-reglas.md`, `reservas-publicas.md`, `init.md`, el README
  de producto y el índice de contratos.

**Controles proporcionales**

- `php -l` en lo tocado; `grep` sin referencias a endpoints retirados,
  `token_gestion`, `AGENDA_` ni `mi-turno`.
- Sin sesión, curl: `crear.php` y `gestionar.php` dan 404;
  `disponibilidad.php` y `profesionales.php` dan 403.
- Cálculo: se repite el script de 23 casos más la prueba de contención del
  lock (dos conexiones).
- Playwright a 390, 768 y 1440 px: landing `#reservar`, `reservar.php` con y
  sin `?servicio`, y con JS apagado. Comprobar que el `href` de `wa.me` lleve el
  mensaje con acentos bien codificados.
- Panel a 390 px: abrir WhatsApp no cambia el estado; "Marcar confirmado"
  sí; alta rápida; `citas.php` y `mis-citas.php` siguen mostrando horarios
  libres con sesión.

**Dudas para Gonzalo o Mari**

1. Número de destino: `TELEFONO_NEGOCIO` es `+543624254052`, sin el 9 de móvil.
   Hay que probar `wa.me/543624254052` contra `wa.me/5493624254052` desde un
   celular real antes de publicar.
2. Cuentas de clienta: sin reserva online, `registro.php` y `mi-cuenta.php`
   solo muestran el historial. ¿Se mantienen visibles o se quitan del menú?
   Propuesta: mantenerlas sin promoverlas y decidir después.
3. Sábados: el footer dice 9 a 14 y `dias_laborales` no los incluye. Afecta
   las horas del panel.

**Siguiente bloque concreto:** implementar este alcance en `v2-agenda` sobre
`d9eb1cf`, después de que Codex cierre o integre sus cambios documentales sin
commit en el mismo árbol, para no mezclar autorías. Entregar como commit
local con los controles anteriores. Push, migración en producción y deploy
quedan como pasos separados, con autorización.
