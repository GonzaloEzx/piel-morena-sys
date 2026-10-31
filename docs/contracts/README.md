# Contratos Funcionales

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si, por modulo
> Relacion: capa funcional oficial del sistema
> Ultima revision: 2026-10-31

## Estado y siguiente bloque

La rama `v2-agenda` contiene la entrega local `d9eb1cf`, sin push ni deploy
según el último cierre. Gonzalo aceptó simplificar las reservas públicas a
mensajes de WhatsApp. Codex y Claude dejaron su análisis en el
[plan de WhatsApp](../producto/reservas-whatsapp.md). Al cerrar, Gonzalo
reafirmó el alcance mínimo: botones y mensajes, retiro de reserva pública
y panel fuera del bloque salvo ajustes indispensables. Sin implementación;
los contratos 03 y 04 describen la agenda entregada.

## Indice

| # | Contrato | Estado actual |
|---|---|---|
| 01 | Gestion de usuarios | Implementado con observacion sobre Google UI |
| 02 | Catalogo de servicios | Implementado con override de disponibilidad |
| 03 | Sistema de reservas | La web abre WhatsApp; turnos cargados en el panel con calculo por profesional |
| 04 | Panel de administracion | Implementado |
| 05 | Control de caja | Implementado |
| 06 | Notificaciones | Parcial alto |
| 07 | Analytics y reportes | Implementado |
| 08 | Landing publica | Implementado con placeholders de contenido |
| 09 | Productos e inventario | Implementado con mejoras pendientes |
| 10 | Jornadas | Alcance reducido a personal externo (desde mayo 2026) |
| 11 | Promociones y packs | Implementado |
| 12 | Maquinas alquiladas | Implementado (mayo 2026); mapping inicial post-deploy pendiente |

## Regla de uso

Si un modulo cambia de comportamiento:

1. actualizar primero su contrato;
2. luego tocar el codigo o dejar ambos cambios juntos en la misma entrega;
3. registrar la decision abierta si el cambio queda parcial.

## Regla editorial

- `contracts/` define comportamiento esperado, no detalle de implementacion.
- Si un documento de `modulos/` o `ux/` contradice a un contrato, el contrato manda hasta que se actualice.
