# Contrato 05 - Control de Caja

## Alcance

Movimientos, resumen, cierre diario, historico y ventas de productos.

## Estado actual

### Implementado

- vista `admin/views/caja.php`;
- `api/admin/caja.php` para movimientos y resumen;
- `api/caja/cierre_caja.php`;
- `api/caja/listar_movimientos.php`;
- `api/caja/resumen.php`;
- `api/caja/registrar_venta.php`;
- ingreso asociado a citas completadas;
- venta de producto con decremento de stock;
- cierre diario con notas;
- historico de cierres.

## Checklist

- [x] movimientos manuales
- [x] resumen
- [x] ingreso por cita completada
- [x] cierre diario
- [x] historico
- [x] venta de productos
