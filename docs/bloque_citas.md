# Bloque Citas

Fecha: 2026-04-02

## Objetivo del documento

Este documento reemplaza el enfoque viejo de "agenda" por el lenguaje real del sistema: `citas`, `reservas`, `horarios`, `servicios` y `categorias de servicios`.

Su objetivo es dar a cualquier agente o desarrollador contexto consumible y accionable sobre el módulo de citas, cubriendo:

- intención de negocio;
- términos correctos del dominio;
- flujo público de reserva;
- integración con panel admin y staff;
- reglas vigentes en backend;
- tablas y configuraciones relevantes;
- inconsistencias abiertas que no deberían ignorarse.

## Terminología recomendada

### Usar

- `citas`
- `reserva`
- `reservar cita`
- `servicio`
- `categoría de servicio`
- `horarios`
- `disponibilidad`
- `estado de cita`
- `empleado asignado`

### Evitar como término principal

- `agenda`

Puede aparecer como término histórico en documentación vieja, pero ya no es la palabra correcta para el modelo actual.

## Propósito de negocio

Según [README.md negocio](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/negocio/README.md), el sistema resuelve un dolor real del negocio:

- hoy los turnos se manejaban por WhatsApp e Instagram;
- Mari los anotaba manualmente en Excel;
- no había confirmación sistematizada;
- existía riesgo de olvido, solapamiento y pérdida de historial.

El módulo de citas existe para profesionalizar esa operación sin perder el trato cercano.

### Qué necesita el negocio

- que una clienta pueda reservar con claridad;
- que la disponibilidad sea confiable;
- que el personal vea y gestione citas desde panel;
- que la asignación de empleados y horarios sea coherente con la operación real;
- que la reserva se integre con notificaciones, emails y caja.

## Ubicación del módulo en el producto

Según [README.md producto](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/producto/README.md), este módulo cruza varias partes del sistema:

- `reservar.php` como flujo público de reserva;
- `api/citas/disponibilidad.php` para disponibilidad;
- `api/citas/crear.php` para crear reserva;
- `api/citas/cancelar.php` para cancelación;
- `admin/views/citas.php` para gestión interna;
- `admin/views/mis-citas.php` para staff;
- `api/admin/citas.php` para CRUD operativo interno;
- tabla `citas` en base;
- integraciones con notificaciones, email y caja.

## Estado actual del módulo

El contrato vigente en [03-sistema-reservas.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/contracts/03-sistema-reservas.md) indica como implementado:

- wizard en `reservar.php`;
- disponibilidad por fecha y servicio;
- creación de cita;
- cancelación;
- vista admin de citas;
- alta manual de citas desde admin;
- asignación de empleado;
- vista calendario;
- restricción de cancelación;
- emails y notificaciones asociados.

Además, el contrato marca una inconsistencia importante todavía abierta:

- frontend público exige login para reservar;
- backend permite reservar como invitado.

Eso hoy no rompe el funcionamiento visible del sitio, pero sí es una definición de producto pendiente y debe ser tenida en cuenta antes de seguir ampliando el módulo.

## Modelo conceptual

### Relación principal

- una `categoría de servicio` agrupa varios `servicios`;
- una `cita` siempre refiere a un `servicio`;
- una `cita` pertenece a un `cliente`;
- una `cita` puede tener `empleado` asignado;
- la disponibilidad se calcula con:
  - fecha;
  - duración del servicio;
  - horario general del negocio;
  - días laborales configurados;
  - intervalo de citas;
  - citas activas existentes.

### Estado de cita

Estados vigentes:

- `pendiente`
- `confirmada`
- `en_proceso`
- `completada`
- `cancelada`

## Flujo público de reserva

La implementación visible vive en [reservar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/reservar.php).

### Comportamiento actual visible

- si el usuario no está autenticado, ve un bloque que exige cuenta;
- si está autenticado, entra al wizard;
- el wizard se presenta como un flujo de 4 pasos;
- el copy promete: `Elige tu servicio, fecha y horario en menos de 1 minuto`.

### Paso 0 implícito: autenticación

Estado actual de UI:

- no logueado: se bloquea la reserva;
- se muestran beneficios de crear cuenta;
- CTA a:
  - `registro.php?redirect=/reservar.php`
  - `login.php?redirect=/reservar.php`

Esto está validado visualmente en las capturas:

- [reserva_citas01.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/reserva_citas01.png)
- [reserva_citas_crear_cuenta.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/reserva_citas_crear_cuenta.png)
- [reserva_citas_inicio_sesion.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/reserva_citas_inicio_sesion.png)

### Paso 1: servicio

El frontend:

- consulta `api/servicios/listar.php`;
- agrupa servicios por categoría;
- renderiza un accordion por categoría;
- cada opción muestra:
  - nombre;
  - duración;
  - precio;
- soporta preselección por query param `?servicio=ID`.

### Paso 2: fecha

- usa input `type="date"`;
- mínimo: hoy;
- máximo: hoy + 60 días;
- valor inicial: mañana.

### Paso 3: horario

- consulta `api/citas/disponibilidad.php?fecha=...&id_servicio=...`;
- muestra slots disponibles;
- si no hay turnos, informa mensaje contextual.

### Paso 4: confirmación

Muestra resumen de:

- servicio;
- fecha;
- hora;
- precio.

Al confirmar:

- hace POST a `api/citas/crear.php`;
- si sale bien, muestra pantalla de éxito con:
  - servicio;
  - fecha;
  - hora;
  - número de reserva;
  - referencia.

La secuencia completa se ve en:

- [reserva_citas_steps.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/reserva_citas_steps.png)

## Reglas de negocio visibles y técnicas

### Reglas visibles para el usuario

- la web pública hoy exige cuenta;
- la reserva no cobra seña ni pago online;
- la clienta elige servicio, fecha y horario;
- el cliente no cancela por su cuenta desde la web actual de producto, sino que debe contactar al salón.

### Reglas técnicas vigentes

- máximo de anticipación: 60 días;
- no se permiten fechas pasadas;
- disponibilidad calculada contra citas en estado:
  - `pendiente`
  - `confirmada`
  - `en_proceso`
- anti-solapamiento validado en backend;
- duración del slot basada en la duración del servicio;
- el sistema usa configuración general del negocio para apertura, cierre, días laborales e intervalo.

## Backend público de citas

### `api/citas/disponibilidad.php`

Responsabilidad:

- validar fecha e id de servicio;
- cargar servicio activo;
- leer configuración:
  - `horario_apertura`
  - `horario_cierre`
  - `dias_laborales`
  - `intervalo_citas`
- generar slots disponibles;
- excluir turnos solapados;
- si la fecha es hoy, ocultar horarios pasados con margen de 30 minutos.

Decisión técnica importante:

- el servicio usa `max(duracion_servicio, intervalo_citas)` para construir slots.

### `api/citas/crear.php`

Responsabilidad:

- validar servicio, fecha y hora;
- calcular `hora_fin` a partir de duración del servicio;
- determinar cliente;
- verificar anti-solapamiento de nuevo en backend;
- crear la cita en estado `pendiente`;
- guardar token en `notas` con formato `token:...`;
- enviar email de confirmación best-effort;
- registrar notificación de sistema.

Inconsistencia importante:

- este endpoint permite reserva como invitado;
- si no hay sesión, acepta `nombre`, `email`, `telefono`;
- si el email no existe, crea usuario cliente;
- si existe, reutiliza ese usuario.

Esto choca con la UX actual de `reservar.php`, que hoy obliga a login/registro.

### `api/citas/cancelar.php`

Estado actual real:

- solo admin y empleado pueden cancelar;
- exige autenticación;
- bloquea cancelación de citas:
  - inexistentes;
  - ya canceladas;
  - completadas;
- al cancelar, envía email best-effort y crea notificación.

Observación:

- el comentario viejo de algunas capas del producto hablaba de cancelación por token o por cliente, pero el endpoint vigente ya no funciona así.
- la fuente de verdad operativa debe considerarse el código actual y el contrato actualizado del producto.

## Gestión interna de citas

La vista principal es [citas.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/admin/views/citas.php).

### Funcionalidades implementadas

- filtros por fecha y estado;
- vista tabla;
- vista calendario con FullCalendar;
- cambio de estado;
- asignación de empleado;
- nueva cita manual desde modal;
- refresco de eventos al cambiar estados o crear cita.

### Modal de nueva cita

Permite:

- elegir cliente existente o nuevo;
- buscar cliente por nombre, email o teléfono;
- elegir servicio;
- cargar empleados aptos por servicio;
- elegir fecha;
- consultar turnos disponibles;
- asignar horario;
- guardar notas.

### Personal interno

Producto define:

- admin: acceso total;
- staff/empleada: ver y crear citas propias, cancelar citas, operar flujos internos;
- cliente: reservar y ver historial, pero no operar cancelación directa por API pública vigente.

## Integraciones del módulo

### Servicios

El módulo depende de:

- tabla `servicios`;
- tabla `categorias_servicios`;
- endpoint `api/servicios/listar.php`;
- duración y precio por servicio.

### Horarios y configuración

Depende de configuración general:

- `horario_apertura = 08:00`
- `horario_cierre = 20:00`
- `dias_laborales = 1,2,3,4,5`
- `intervalo_citas = 30`

Fuente revisada:

- [u347774250_pielmorena.sql](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/para-test/u347774250_pielmorena.sql)

### Empleados

Depende de:

- tabla `horarios`;
- tabla `empleados_servicios`;
- asignación de empleado en cita;
- filtrado de empleados por servicio.

### Notificaciones y emails

El módulo dispara:

- email de confirmación;
- email de cancelación;
- notificaciones en tabla `notificaciones`.

### Caja

A nivel de producto y panel:

- una cita completada puede impactar caja;
- el registro automático de ingreso ocurre en la capa admin, no en `api/citas/crear.php`.

## Estructura de datos relevante

### Tabla `citas`

Campos relevantes:

- `id`
- `id_cliente`
- `id_servicio`
- `id_empleado`
- `fecha`
- `hora_inicio`
- `hora_fin`
- `estado`
- `notas`
- `created_at`
- `updated_at`

### Tabla `horarios`

Campos relevantes:

- `id_empleado`
- `dia_semana`
- `hora_inicio`
- `hora_fin`
- `activo`

### Tabla `empleados_servicios`

Define qué empleado puede prestar qué servicio.

### Tabla `configuracion`

Claves relevantes para citas:

- `horario_apertura`
- `horario_cierre`
- `dias_laborales`
- `intervalo_citas`

## Fuentes visuales QA

Capturas útiles para comprender la UX actual:

- [reserva_citas01.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/reserva_citas01.png)
- [reserva_citas_steps.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/reserva_citas_steps.png)
- [reserva_citas_crear_cuenta.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/reserva_citas_crear_cuenta.png)
- [reserva_citas_inicio_sesion.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/reserva_citas_inicio_sesion.png)

## Qué no romper al tocar este módulo

- No cambiar el lenguaje de dominio: usar `citas`, no `agenda`.
- No romper el flujo del wizard de 4 pasos.
- No mover la lógica de disponibilidad y anti-solapamiento al frontend.
- No asumir que la disponibilidad visible es suficiente: el backend debe volver a validar.
- No ignorar la relación entre servicio, duración y slot.
- No desalinear la UX pública con la política de auth sin decisión explícita.
- No romper integración con:
  - notificaciones;
  - emails;
  - panel admin;
  - panel staff;
  - caja.

## Inconsistencias y pendientes abiertos

### Inconsistencia principal

- UI pública: requiere cuenta.
- API pública de creación: permite invitado.

Esto debe resolverse en una futura definición de producto. Mientras tanto, cualquier cambio debe ser consciente de esa dualidad.

### Pendientes operativos

- jornadas específicas por servicio aún no están resueltas completamente en la capa pública;
- horarios por empleada son parte del modelo, pero la disponibilidad pública hoy se apoya en configuración global del negocio;
- cancelación para cliente no está habilitada en el endpoint vigente;
- el naming documental viejo todavía arrastra el término `agenda`.

## Recomendación documental

Desde ahora, la fuente de contexto específica de este módulo debería ser este archivo:

- [bloque_citas.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/bloque_citas.md)

Y el documento viejo de agenda debería considerarse solo una referencia deprecada/redirigida.

## Archivos clave para cualquier agente

- [reservar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/reservar.php)
- [disponibilidad.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/citas/disponibilidad.php)
- [crear.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/citas/crear.php)
- [cancelar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/citas/cancelar.php)
- [citas.php admin](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/admin/views/citas.php)
- [03-sistema-reservas.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/contracts/03-sistema-reservas.md)
- [README.md negocio](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/negocio/README.md)
- [README.md producto](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/producto/README.md)
- [u347774250_pielmorena.sql](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/para-test/u347774250_pielmorena.sql)
