# Citas Internas

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: complementaria
> Relacion: operacion interna del modulo de reservas y citas
> Ultima revision: 2026-04-03

## Objetivo

Separar la gestion interna de citas del flujo publico de reservas.

En esta capa interesa lo que usan admin y staff para:

- ver citas;
- crear citas manualmente;
- cambiar estados;
- asignar empleadas;
- operar calendario y seguimiento diario.

## Ubicacion en el sistema

Las piezas principales son:

- `admin/views/citas.php`
- `admin/views/mis-citas.php`
- `api/admin/citas.php`
- `api/citas/cancelar.php`

## Funcionalidades implementadas

- filtros por fecha y estado;
- vista tabla;
- vista calendario con FullCalendar;
- cambio de estado;
- asignacion de empleado;
- alta manual de citas desde modal;
- refresco de eventos al cambiar estados o crear cita.

## Modal de nueva cita

Permite:

- elegir cliente existente o nuevo;
- buscar cliente por nombre, email o telefono;
- elegir servicio;
- cargar empleados aptos por servicio;
- elegir fecha;
- consultar turnos disponibles;
- asignar horario;
- guardar notas.

## Roles

- `admin`: acceso total a operacion, estados y gestion
- `empleado`: vista y gestion operativa de sus citas
- `cliente`: no opera cancelacion interna ni acciones administrativas

## Integraciones

### Servicios

- tabla `servicios`
- tabla `categorias_servicios`
- duracion y precio por servicio

### Empleados y horarios

- tabla `horarios`
- tabla `empleados_servicios`
- asignacion de empleado en cita

### Notificaciones y emails

- email de confirmacion;
- email de cancelacion;
- tabla `notificaciones`.

### Caja

- una cita completada puede impactar caja;
- ese efecto ocurre en la capa interna, no en la reserva publica.

## Modelo de datos relevante

### Tabla `citas`

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

### Estados vigentes

- `pendiente`
- `confirmada`
- `en_proceso`
- `completada`
- `cancelada`

## Que no conviene romper

- la gestion de estados desde panel;
- la carga manual de citas;
- la relacion entre servicios y empleadas;
- la integracion con notificaciones, email y caja.

## Referencias

- `docs/contracts/03-sistema-reservas.md`
- `docs/contracts/04-panel-administracion.md`
- `docs/modulos/reservas/disponibilidad-y-reglas.md`
