# Bloque Tratamiento

Fecha: 2026-04-02

## Objetivo

Actualizar la sección `#tratamientos` del landing para que las tarjetas y el modal de `Ver más` reflejen la lista real provista en `docs/tratamientos.txt`, manteniendo intactos los estilos y el comportamiento visual existentes.

## Fuente de verdad usada

- `docs/tratamientos.txt`
- `docs/negocio/README.md`
- `docs/producto/README.md`
- `database/migrations/004_servicios_reales.sql` para apoyar duraciones y denominaciones ya presentes en el sistema

## Tratamientos implementados

1. Limpieza Facial Profunda
2. Microneedling
3. Peeling Químico
4. Peeling Enzimático
5. Tratamiento Anti Edad
6. Tratamiento Acné
7. Nanoplastia
8. Lifting de Pestañas
9. Kapping
10. Alisado sin Formol
11. Laminado de Cejas
12. Semipermanente

## Criterio de implementación

- Se reemplazó el catálogo estático anterior en `index.php` por la lista real de 12 tratamientos.
- Cada tarjeta ahora usa nombre real, microbeneficio y datos extendidos para el modal.
- El modal conserva la misma estructura visual y de interacción, pero muestra contenido adaptado a esta nueva lista.
- No se modificó CSS.
- No se alteró la mecánica del botón `Ver más`; sigue abriendo SweetAlert2 con contenido generado desde `assets/js/main.js`.

## Categorías visuales reutilizadas

Para no introducir estilos nuevos, las nuevas categorías funcionales se mapearon a variantes visuales ya existentes:

- `Facial` mantiene su variante propia.
- `Mirada` mantiene su variante propia.
- `Capilar` reutiliza la variante visual `corporal`.
- `Manicuría` reutiliza la variante visual `relax`.

## Archivos modificados

- `index.php`
- `assets/js/main.js`
- `docs/bloque_tratamiento.md`

## Validación esperada

- La sección `#tratamientos` muestra 12 tarjetas.
- Cada tarjeta abre un modal coherente con el tratamiento seleccionado.
- El CTA de WhatsApp del modal mantiene el flujo actual de consulta.
