# Flujo de Reserva Publica

> Estado: vigente
> Audiencia: producto, UX, desarrollo, agentes
> Fuente de verdad: complementaria
> Ultima revision: 2026-04-03

## Objetivo

Explicar el recorrido visible que hace una clienta para reservar una cita.

## Paso a paso

### Paso 0 - Acceso

Si la usuaria no esta autenticada:

- ve un bloque que explica por que conviene crear cuenta;
- puede ir a `registro.php` o `login.php`;
- ambos caminos deben volver a `reservar.php`.

### Paso 1 - Elegir servicio

- selecciona una categoria;
- elige un servicio concreto;
- ve duracion y precio antes de avanzar.

### Paso 2 - Elegir fecha

- selecciona una fecha dentro de la ventana disponible;
- no puede elegir fechas pasadas;
- la experiencia debe sentirse simple y rapida.

### Paso 3 - Elegir horario

- ve horarios disponibles para ese servicio y fecha;
- si no hay disponibilidad, recibe un mensaje claro;
- el horario elegido debe sentirse definitivo y confiable.

### Paso 4 - Confirmar

- revisa resumen de servicio, fecha, hora y precio;
- confirma la reserva;
- recibe una pantalla de exito con numero de referencia.

## Criterios UX del flujo

- baja friccion;
- lenguaje simple;
- confianza en la disponibilidad;
- continuidad natural hacia `Mi Cuenta`;
- cero pasos redundantes.

## Referencias

- `docs/modulos/reservas/reservas-publicas.md`
- `docs/modulos/reservas/disponibilidad-y-reglas.md`
- `docs/contracts/03-sistema-reservas.md`
