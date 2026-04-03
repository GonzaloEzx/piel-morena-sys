# Contrato 09 - Productos e Inventario

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si
> Relacion: contrato funcional de productos e inventario
> Ultima revision: 2026-04-03

## Alcance

CRUD de productos, stock y venta integrada con caja.

## Estado actual

### Implementado

- CRUD admin de productos;
- stock y stock minimo;
- alerta visual de stock bajo;
- upload de imagen;
- venta de producto desde caja;
- decremento automatico de stock;
- registro de ingreso por venta.

### Pendiente

- alertas de stock critico en dashboard general;
- API publica de productos, solo si el negocio decide exponerlos en la web.

## Checklist

- [x] CRUD
- [x] stock bajo
- [x] upload
- [x] venta e impacto en caja
- [x] decremento de stock
- [ ] alerta global de stock critico
