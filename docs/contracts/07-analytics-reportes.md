# Contrato 07 - Analytics y Reportes

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si
> Relacion: contrato funcional de analytics y reportes
> Ultima revision: 2026-04-03

## Alcance

Metricas de ingresos, consultas de precio, cancelaciones, clientes y horarios.

## Estado actual

### Implementado

- dashboard con KPIs y graficos;
- `admin/views/reportes.php`;
- `api/analytics/ingresos.php`;
- `api/analytics/servicios_populares.php`;
- `api/analytics/tendencias.php`;
- exportacion CSV por bloque;
- metricas de ticket promedio, cancelacion, clientes nuevos/recurrentes y horarios pico.

## Checklist

- [x] vista de reportes
- [x] filtros por rango
- [x] APIs analytics
- [x] exportacion CSV
- [x] comparativa de periodos
