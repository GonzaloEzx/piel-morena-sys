# Modulo Reservas

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: complementaria a `contracts/03-sistema-reservas.md`
> Ultima revision: 2026-04-03

## Objetivo

Ordenar un modulo amplio que antes mezclaba `reservas`, `citas`, `turnos` y `agenda` dentro de dos documentos grandes.

Desde ahora la estructura queda asi:

- `reservas-publicas.md`: flujo visible para clientas en `reservar.php`
- `citas-internas.md`: gestion interna en admin y staff
- `disponibilidad-y-reglas.md`: reglas transversales, disponibilidad y decisiones abiertas

## Regla de lectura

- si la duda es de UX publica, leer `../../ux/reservas/flujo-reserva-publica.md`
- si la duda es de comportamiento pactado, leer `../../contracts/03-sistema-reservas.md`
- si la duda es de implementacion actual, leer los tres documentos de esta carpeta
