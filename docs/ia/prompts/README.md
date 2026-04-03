# Biblioteca de Prompts

> Fuente operativa para prompts reutilizables del proyecto. Actualizado el 2026-03-28.

## Objetivo

Esta carpeta reemplaza el uso de notas sueltas para prompts de trabajo. La idea es:

1. conservar prompts que siguen siendo utiles;
2. normalizarlos para que se puedan volver a usar;
3. separar contexto estable de pedidos historicos;
4. mantener una base viva que crezca sin perder orden.

## Archivos

- `biblioteca-prompts.md`: biblioteca principal, agrupada por categoria.
- `plantilla-prompt.md`: molde para agregar prompts nuevos.
- `prompt-perfect-creator.md`: estandar para convertir pedidos en bruto en prompts refinados.

## Regla de uso

Cada prompt de esta carpeta debe incluir, como minimo:

- un objetivo claro;
- cuando conviene usarlo;
- inputs minimos;
- prompt base reutilizable;
- salida esperada;
- notas de mantenimiento si aplica.

## Convencion recomendada

- ID: `PROMPT-AREA-NNN`
- Estado: `vigente`, `experimental` o `archivado`
- Categoria: arquitectura, diseno, QA, deploy, documentacion, negocio

## Flujo para sumar un prompt nuevo

1. Copiar `plantilla-prompt.md`.
2. Crear un ID nuevo y una categoria.
3. Limpiar referencias historicas, credenciales y ruido conversacional.
4. Pasar el prompt a tono reutilizable, con variables reemplazables.
5. Si el prompt cambia la forma de trabajar del repo, enlazarlo desde `docs/README.md`.

## Regla editorial

- No usar esta carpeta como bitacora conversacional.
- No mezclar prompts vigentes con pedidos cerrados sin marcar estado.
- Si un prompt nace de una incidencia puntual, abstraerlo a patron reutilizable.
- Si un prompt deja de servir, marcarlo como archivado en vez de borrarlo sin contexto.
