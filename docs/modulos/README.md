# Modulos

> Estado: vigente
> Audiencia: desarrollo, agentes
> Fuente de verdad: no; complementa a `contracts/`
> Ultima revision: 2026-04-03

## Objetivo

`modulos/` documenta cada area funcional desde la implementacion real:

- archivos implicados;
- dependencias;
- comportamiento actual;
- decisiones tecnicas relevantes;
- puntos sensibles que no conviene romper.

## Estructura

- `auth/`: autenticacion, sesiones y flujos de acceso.
- `reservas/`: reservas publicas, citas internas y reglas de disponibilidad.
- `landing/`: modulos publicos del home que requieren contexto propio.

## Regla de uso

Antes de tocar codigo:

1. leer el contrato del modulo en `docs/contracts/`;
2. leer la documentacion tecnica de `docs/modulos/`;
3. si hay impacto visible para usuario, revisar tambien `docs/ux/`.
