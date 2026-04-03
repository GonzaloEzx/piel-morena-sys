# Flujo Login y Registro

> Estado: vigente
> Audiencia: producto, UX, desarrollo, agentes
> Fuente de verdad: complementaria
> Ultima revision: 2026-04-03

## Objetivo

Describir como vive el usuario el acceso al sistema.

## Recorrido principal

1. el usuario entra a `login.php` o `registro.php`;
2. completa el formulario manual o intenta acceso con Google;
3. si el flujo viene desde reserva, el sistema debe preservar `redirect=/reservar.php`;
4. al autenticarse, la sesion queda activa y el usuario puede continuar el recorrido pendiente.

## Expectativas UX

- claridad de la accion principal;
- bajo esfuerzo para volver a la reserva;
- mensajes de error concretos;
- consistencia entre login, registro y recuperacion;
- seguridad sin friccion innecesaria.

## Pantallas y acciones asociadas

- `login.php`
- `registro.php`
- recuperacion y cambio de password
- `mi-cuenta.php`

## Referencias

- `docs/modulos/auth/implementacion.md`
- `docs/contracts/01-gestion-usuarios.md`
