# Contratos Funcionales

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si, por modulo
> Relacion: capa funcional oficial del sistema
> Ultima revision: 2026-04-03

## Indice

| # | Contrato | Estado actual |
|---|---|---|
| 01 | Gestion de usuarios | Implementado con observacion sobre Google UI |
| 02 | Catalogo de servicios | Implementado |
| 03 | Sistema de reservas | Implementado con decision abierta auth vs invitado |
| 04 | Panel de administracion | Implementado |
| 05 | Control de caja | Implementado |
| 06 | Notificaciones | Parcial alto |
| 07 | Analytics y reportes | Implementado |
| 08 | Landing publica | Implementado con placeholders de contenido |
| 09 | Productos e inventario | Implementado con mejoras pendientes |

## Regla de uso

Si un modulo cambia de comportamiento:

1. actualizar primero su contrato;
2. luego tocar el codigo o dejar ambos cambios juntos en la misma entrega;
3. registrar la decision abierta si el cambio queda parcial.

## Regla editorial

- `contracts/` define comportamiento esperado, no detalle de implementacion.
- Si un documento de `modulos/` o `ux/` contradice a un contrato, el contrato manda hasta que se actualice.
