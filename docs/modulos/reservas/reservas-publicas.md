# Reservas Publicas

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: complementaria
> Relacion: implementacion publica de `reservar.php`
> Ultima revision: 2026-04-03

## Objetivo

Documentar la puerta publica de reserva del sistema: `reservar.php`.

Este archivo cubre:

- que busca resolver para el negocio;
- como se comporta hoy la pagina;
- que APIs consume;
- que dependencias visuales y tecnicas la sostienen.

## Proposito de negocio

La reserva online existe para:

- sacar carga manual de WhatsApp e Instagram;
- permitir que la clienta reserve con menor friccion;
- empujar la creacion de cuenta;
- convertir interes en una cita concreta sin perder tono premium ni cercania.

No hay pago online ni seña. La reserva es una confirmacion operativa de turno.

## Entrada al flujo

La pagina publica vive en `reservar.php`.

Comportamiento real:

- si la usuaria no esta autenticada, no ve el wizard;
- en ese caso aparece un bloque de acceso con CTA a `registro.php` y `login.php`;
- ambos CTAs conservan `redirect=/reservar.php`.

## Wizard actual

La UI visible hoy es de 4 pasos:

1. `Servicio`
2. `Fecha`
3. `Hora`
4. `Confirmar`

El estado visual de exito existe, pero no forma parte del stepper.

## Paso a paso visible

### Paso 1 - Servicio

- consulta `api/servicios/listar.php`;
- agrupa por categoria;
- renderiza accordion por categoria;
- cada opcion muestra nombre, duracion y precio;
- soporta preseleccion por query param `?servicio=<id>`.

### Paso 2 - Fecha

- usa input `type="date"`;
- minimo: hoy;
- maximo: hoy + 60 dias;
- valor inicial: manana.

### Paso 3 - Hora

- consulta `api/citas/disponibilidad.php` con `fecha` e `id_servicio`;
- muestra grilla de horarios;
- informa estado vacio si no hay turnos disponibles.

### Paso 4 - Confirmar

- muestra servicio, fecha, rango horario y precio;
- envia `id_servicio`, `fecha` y `hora_inicio` a `api/citas/crear.php`;
- no pide nombre, email ni telefono dentro del flujo autenticado actual.

### Estado de exito

Si la reserva se confirma:

- se reemplaza el wizard por una vista de exito;
- se informa numero de reserva y referencia;
- se ofrece continuidad hacia `mi-cuenta.php`.

## Dependencias tecnicas

### Pagina

- `reservar.php`
- `includes/header.php`
- `includes/footer.php`

### APIs

- `api/servicios/listar.php`
- `api/citas/disponibilidad.php`
- `api/citas/crear.php`
- `api/citas/cancelar.php`

### Estilos y comportamiento visual

- `assets/css/style.css`
- `assets/css/premium-auth.css`
- Bootstrap para accordion y layout base
- SweetAlert2 para errores y estados

## Lo que no conviene romper

- el redirect a `reservar.php` desde login y registro;
- la agrupacion por categoria;
- la ventana maxima de 60 dias salvo decision de negocio;
- la pantalla de exito con continuidad a `Mi Cuenta`;
- el tono simple, claro y premium del flujo.

## Referencias

- `docs/ux/reservas/flujo-reserva-publica.md`
- `docs/modulos/reservas/disponibilidad-y-reglas.md`
- `docs/contracts/03-sistema-reservas.md`
