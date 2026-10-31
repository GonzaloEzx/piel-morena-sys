# Init — Piel Morena Sys

> Primer archivo que lee el agente al iniciar 
> cualquier sesión en este proyecto.

## Qué es este sistema

Sistema web de gestión integral para salón de 
belleza y estética. Producción activa en 
pielmorenaestetica.com.ar.

Tres capas:
- Pública: landing one-page
- Clientas: piden turno por WhatsApp desde la web (la web no reserva)
- Operativa: panel admin y empleado

## Stack

- PHP vanilla + MySQL
- Bootstrap 5.3 como base de UI. CSS vanilla
  solo para overrides específicos.
- Mobile-first en todo el frontend
- Deploy en Hostinger vía Git

## Antes de escribir cualquier código

Leer en este orden:

1. docs/README.md → mapa general y reglas de precedencia
2. docs/contracts/ → comportamiento esperado por módulo
3. docs/ia/ → normas específicas para agentes

## Reglas de este proyecto

- Los contratos en docs/contracts/ mandan sobre 
  cualquier otro documento
- Si dos documentos se contradicen, 
  docs/README.md define cuál prevalece
- No tocar módulos fuera del scope de la sesión
- Mobile-first sin excepciones
- Confirmar en 3 líneas qué entendiste antes 
  de escribir código

## Estado general

Ver progress en docs/contracts/README.md.
Módulos con pendiente real: 06, 09, 10, 12.
Reservas: la web abre WhatsApp y el salón carga
el turno en el panel (contrato 03, 2026-10-31).
Decisión abierta: Google OAuth UX (01).
