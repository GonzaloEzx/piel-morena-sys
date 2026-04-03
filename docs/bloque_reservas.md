# Bloque Reservas

> Estado revisado contra codigo real el 2026-04-02.
> Este documento complementa [bloque_citas.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/bloque_citas.md) y se enfoca en la experiencia publica de `reservar.php`.

## Objetivo

Dar contexto consumible a cualquier agente o desarrollador sobre la seccion de reservas online del sitio:

- que resuelve para el negocio;
- como funciona hoy en la UI publica;
- que archivos tecnicos la sostienen;
- que reglas reales hay en backend;
- que inconsistencias siguen abiertas entre contrato, producto y codigo.

## Que representa esta seccion

La pagina [reservar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/reservar.php) es la puerta publica para convertir interes en una cita concreta.

Desde negocio y producto, la intencion de esta seccion es:

- sacar carga manual de WhatsApp e Instagram;
- permitir que la clienta reserve sin intervenir al salon en cada caso;
- mostrar una experiencia simple, rapida y confiable;
- empujar el alta de cuenta para luego sostener historial, recordatorios y recompra;
- respetar reglas operativas del centro de estetica sin perder cercania.

No hay pago online ni sena. La reserva es una confirmacion operativa de turno.

## Lenguaje de dominio

Usar estos terminos:

- `reservas` para la experiencia publica de pedir turno;
- `citas` para la entidad operativa que termina en base y panel;
- `horarios` o `turnos` para los slots disponibles;
- `servicios` y `categorias de servicios` para la oferta reservable.

Evitar `agenda` como nombre de modulo nuevo. Quedo deprecado en la capa documental.

## Alcance de este documento

Este archivo cubre:

- el flujo publico de reserva;
- dependencias visuales y tecnicas del bloque;
- APIs publicas que consume;
- reglas de disponibilidad y confirmacion;
- expectativas UX del negocio para esta seccion.

No cubre en profundidad:

- gestion interna completa de citas;
- calendario admin;
- caja y efectos post-completada.

Para eso ver [bloque_citas.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/bloque_citas.md) y [03-sistema-reservas.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/contracts/03-sistema-reservas.md).

## Implementacion actual

La seccion vive en [reservar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/reservar.php).

### Entrada al flujo

- si la usuaria no esta autenticada, no ve el wizard;
- en ese caso se muestra un bloque de acceso con beneficios y CTAs a:
  - [registro.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/registro.php)
  - [login.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/login.php)
- ambos CTAs conservan `redirect=/reservar.php`.

### Wizard publico real

Para usuarias autenticadas, hoy el flujo real es de 4 pasos:

1. `Servicio`
2. `Fecha`
3. `Hora`
4. `Confirmar`

Al confirmar con exito aparece un quinto estado visual de exito, pero no forma parte del stepper.

Nota importante:

- el comentario superior de [reservar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/reservar.php) todavia dice `Servicio -> Fecha -> Hora -> Datos -> Confirmar`;
- eso ya no refleja la UI real, porque el paso `Datos` desaparece cuando la reserva exige cuenta.

## Flujo UX paso a paso

### Paso 0 - Gate de autenticacion

Objetivo de negocio:

- empujar registro/login antes de reservar;
- asociar la cita a una cuenta;
- habilitar historial y recordatorios.

Comportamiento actual:

- bloque visual `pm-auth-required`;
- beneficios visibles;
- CTA primario a registro;
- CTA secundario a login.

Referencia visual:

- [reserva_citas_crear_cuenta.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/reserva_citas_crear_cuenta.png)
- [reserva_citas_inicio_sesion.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/reserva_citas_inicio_sesion.png)

### Paso 1 - Elegir servicio

La UI consume [listar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/servicios/listar.php).

Comportamiento:

- trae servicios activos;
- agrupa por `categoria`;
- usa accordion por categoria;
- cada opcion muestra:
  - nombre;
  - duracion en minutos;
  - precio o `Consultar`;
- si llega `?servicio=<id>`, intenta preseleccionar ese servicio y abrir su categoria.

Expectativa de negocio:

- hacer facil encontrar un tratamiento sin exponer una tabla compleja;
- mantener foco en el servicio individual, no en un carrito ni combinaciones.

### Paso 2 - Elegir fecha

Comportamiento real:

- input `type="date"`;
- minimo: hoy;
- maximo: `+60 days`;
- valor inicial: manana.

Objetivo:

- reducir friccion;
- evitar fechas invalidas;
- limitar la venta anticipada a una ventana operable para el salon.

### Paso 3 - Elegir hora

La UI consulta [disponibilidad.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/citas/disponibilidad.php) con:

- `fecha`
- `id_servicio`

Comportamiento:

- muestra grilla de botones con horarios;
- si no hay turnos, muestra estado vacio con mensaje del backend;
- cada boton representa `hora_inicio`;
- al seleccionar, guarda tambien `hora_fin`.

Referencia visual:

- [reserva_citas_steps.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/reserva_citas_steps.png)
- [reserva_citas01.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/reserva_citas01.png)

### Paso 4 - Confirmar

La UI muestra un resumen con:

- servicio;
- fecha formateada en espanol;
- rango horario;
- precio.

Accion principal:

- POST a [crear.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/citas/crear.php)

Payload real desde la UI autenticada:

- `id_servicio`
- `fecha`
- `hora_inicio`

La UI no envia nombre, email ni telefono desde este flujo.

### Estado de exito

Si el backend confirma:

- se oculta el stepper;
- se muestra resumen final;
- se informa `N deg Reserva`;
- se muestra boton a `mi-cuenta.php`;
- se ofrece volver al inicio.

La UI expone `Referencia: #<id>` aunque el backend tambien devuelva un `token`.

## Reglas backend que sostienen reservas

### Disponibilidad

[disponibilidad.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/citas/disponibilidad.php) hoy:

- acepta solo `GET`;
- exige fecha valida y servicio activo;
- lee configuracion desde tabla `configuracion`;
- usa:
  - `horario_apertura`
  - `horario_cierre`
  - `dias_laborales`
  - `intervalo_citas`
- no devuelve slots para dias no laborables;
- si la fecha es hoy, aplica margen minimo de `30` minutos;
- genera slots con `max(duracion_servicio, intervalo_citas)`;
- bloquea solapamientos con citas en estado:
  - `pendiente`
  - `confirmada`
  - `en_proceso`

### Creacion de reserva

[crear.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/citas/crear.php) hoy:

- acepta solo `POST`;
- valida servicio, fecha y hora;
- recalcula `hora_fin` segun duracion del servicio;
- vuelve a chequear anti-solapamiento;
- crea la cita en estado `pendiente`;
- guarda `token:<hex>` en `notas`;
- intenta enviar email de confirmacion y notificacion interna en modo best-effort.

### Cancelacion

[cancelar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/citas/cancelar.php) hoy:

- acepta solo `POST`;
- requiere sesion autenticada;
- solo permite `admin` o `empleado`;
- no permite cancelar citas `cancelada` o `completada`;
- envia email y notificacion al cliente si puede resolver la cita.

## Dependencias tecnicas del bloque

### Pagina y logica

- [reservar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/reservar.php)
- [includes/header.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/includes/header.php)
- [includes/footer.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/includes/footer.php)

### APIs publicas

- [listar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/servicios/listar.php)
- [disponibilidad.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/citas/disponibilidad.php)
- [crear.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/citas/crear.php)
- [cancelar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/citas/cancelar.php)

### Assets que la afectan

La seccion depende visualmente de:

- [style.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/style.css)
- [premium-auth.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/premium-auth.css)

Responsabilidad por capa:

- `style.css`: base visual, variables, clases de reserva, grilla de turnos, resumen y estados;
- `premium-auth.css`: look premium del bloque, glassmorphism, cards, auth gate, wizard y stepper;
- Bootstrap: accordion, layout utilitario y componentes base;
- SweetAlert2: errores y warnings del flujo.

Clases relevantes que un agente deberia revisar antes de tocar la seccion:

- `pm-reservar-section`
- `pm-reservar-title`
- `pm-auth-required`
- `pm-steps`
- `pm-step`
- `pm-servicio-option`
- `pm-turnos-grid`
- `pm-turno-btn`
- `pm-resumen-card`
- `pm-resumen-row`

## Relacion con el design system

[design-system.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/design-system.md) si interactua con este bloque.

Puntos especialmente relevantes:

- define la jerarquia de capas CSS y ubica `premium-auth.css` como capa de `auth + reservas`;
- documenta la `Sticky Bottom Action Bar` como patron mobile util para reservas, paginas de servicio y confirmacion;
- documenta el `Time Slot Picker` como patron mobile-first del flujo de reservas;
- refuerza que el selector de horarios debe sentirse tactil, rapido y legible.

Aunque la implementacion publica actual usa `pm-turnos-grid` y `pm-turno-btn`, cualquier rediseño de reservas deberia respetar esa intencion del design system.

## Datos y configuracion relevantes

Desde el SQL operativo documentado en [u347774250_pielmorena.sql](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/para-test/u347774250_pielmorena.sql), las claves base del bloque son:

- `horario_apertura = 08:00`
- `horario_cierre = 20:00`
- `dias_laborales = 1,2,3,4,5`
- `intervalo_citas = 30`

La entidad persistida final es `citas`.

Campos mas relevantes para esta pagina:

- `id_cliente`
- `id_servicio`
- `fecha`
- `hora_inicio`
- `hora_fin`
- `estado`
- `notas`

## Expectativa del negocio sobre la seccion

El negocio espera que la seccion de reservas:

- convierta trafico de landing en turnos reales;
- sea simple para clientas no tecnicas;
- mantenga consistencia con el tono calido/premium del sitio;
- reduzca consultas manuales para disponibilidad;
- no permita prometer horarios que despues el salon no pueda sostener.

En terminos de UX, la prioridad no es complejidad funcional sino:

- claridad;
- confianza;
- baja friccion;
- confirmacion visible del turno;
- continuidad hacia `Mi Cuenta`.

## Inconsistencias y decisiones abiertas

### 1. Auth requerido en UI vs invitado permitido en backend

Hoy conviven dos realidades:

- la UI publica exige cuenta;
- [crear.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/citas/crear.php) todavia soporta invitadas.

Esto ya esta marcado tambien en [bloque_citas.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/bloque_citas.md) y en [03-sistema-reservas.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/contracts/03-sistema-reservas.md).

### 2. Cancelacion documentada historicamente vs cancelacion real

Parte de la documentacion vieja todavia habla de:

- cancelacion por cliente;
- token para invitado;
- restriccion de 2 horas.

El codigo actual de [cancelar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/citas/cancelar.php) no hace eso.

La realidad vigente hoy es:

- cancela solo personal autenticado;
- cliente final debe contactar al salon;
- no hay flujo publico expuesto de cancelacion.

### 3. Paso `Datos` ya no existe en la UI real

La reserva publica autenticada no pide datos adicionales dentro del wizard.
Si se reabre modo invitado en la UI, habra que redisenar el paso y volver a documentarlo.

## Que no conviene romper

- el redirect a `reservar.php` desde login/registro;
- la agrupacion por categoria en accordion;
- la ventana maxima de `60` dias salvo cambio de negocio;
- el chequeo doble de anti-solapamiento en backend;
- la salida clara de exito con numero de reserva;
- el tono premium y simple del bloque;
- la continuidad entre landing -> reservar -> mi cuenta.

## Checklist para tocar esta seccion

Antes de modificar reservas, revisar:

- [reservar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/reservar.php)
- [style.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/style.css)
- [premium-auth.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/premium-auth.css)
- [design-system.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/design-system.md)
- [bloque_citas.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/bloque_citas.md)
- [03-sistema-reservas.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/contracts/03-sistema-reservas.md)

Validaciones minimas despues de tocarla:

- carga de `reservar.php`;
- login y vuelta a `reservar.php` por redirect;
- carga de servicios;
- cambio de fecha;
- carga de disponibilidad;
- confirmacion de reserva;
- vista de exito;
- verificacion de la cita creada en panel o `mi-cuenta.php`.

## Referencias

- [reservar.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/reservar.php)
- [bloque_citas.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/bloque_citas.md)
- [03-sistema-reservas.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/contracts/03-sistema-reservas.md)
- [negocio/README.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/negocio/README.md)
- [producto/README.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/producto/README.md)
- [design-system.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/design-system.md)
