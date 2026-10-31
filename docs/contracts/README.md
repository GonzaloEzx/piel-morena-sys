# Contratos Funcionales

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si, por modulo
> Relacion: capa funcional oficial del sistema
> Ultima revision: 2026-10-31

## Estado y siguiente bloque

La rama `v2-agenda` tiene el flujo público por WhatsApp publicado en `/test`
(`ed905ac`). El panel conserva la agenda por profesional. La raíz permanece
con su versión anterior. Detalle: [estado de entrega](../producto/mvp-v2.md#estado-de-entrega).

El 2026-10-31 Gonzalo transmitió la autorización de Mari para la nueva imagen
v2 y su publicación en `/test`, con datos de ejemplo explícitos. Se implementa
la portada, el catálogo y la orientación de primera visita del contrato 08;
el panel queda fuera del rediseño. El modo demo no escribe en la base operativa.
Verificación y pendientes de material: [plan v2](../producto/mvp-v2.md#v21-imagen-uiux).

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
