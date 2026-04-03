# Prompt Perfect Creator

> Estandar operativo para transformar pedidos en bruto en prompts listos para usar. Creado el 2026-03-28.

## Objetivo

Este documento define como convertir un pedido corto, incompleto o informal en un prompt claro, ejecutable y reutilizable.

La regla de trabajo pasa a ser esta:

- si el usuario entrega un prompt en bruto;
- el agente lo interpreta;
- lo formaliza con este estandar;
- y devuelve una version refinada, mas precisa y mas util para ejecutar.

## Cuando usarlo

Usar este metodo cuando el usuario escriba pedidos como:

- observaciones de QA;
- ideas sueltas;
- bugs visuales;
- cambios funcionales;
- tareas de documentacion;
- mejoras de diseno;
- necesidades del negocio.

## Que es un prompt en bruto

Un prompt en bruto suele tener una o varias de estas caracteristicas:

- esta escrito de forma conversacional;
- mezcla problema y solucion;
- no indica contexto suficiente;
- no define salida esperada;
- no deja claro el criterio de calidad;
- incluye ruido historico o frases emocionales sin valor operativo.

Ejemplo:

```txt
el fondo de "conoce nuestros tratamientos" se ve disparejo y tengo una captura, acomodalo o hacelo mas estetico
```

## Que debe producir el Prompt Perfect Creator

Cada refinamiento debe devolver un prompt con:

1. objetivo claro;
2. contexto suficiente;
3. inputs utiles;
4. restricciones relevantes;
5. pasos o tareas esperadas;
6. criterio de calidad;
7. salida esperada.

## Plantilla canonica

```txt
Necesito <accion principal>.

Contexto:
- Modulo, pagina o seccion: <ruta_o_seccion>
- Problema o necesidad: <descripcion_clara>
- Evidencia disponible: <captura_url_archivo_dato>
- Restriccion: <coherencia_visual_regla_negocio_stack_etc>

Quiero que:
1. <tarea 1>
2. <tarea 2>
3. <tarea 3>
4. <tarea 4>

Prioridad:
- <criterio_1>
- <criterio_2>
- <criterio_3>
```

## Estructura de salida esperada

Cuando el usuario entregue un pedido en bruto, la respuesta refinada debe incluir:

1. `Pedido en bruto`
2. `Tipo de tarea detectado`
3. `Prompt refinado`
4. `Salida esperada`

## Tipos de tarea mas comunes

- `UI puntual`: ajuste visual concreto en una seccion, componente o pantalla.
- `Incidencia funcional`: algo no funciona como deberia.
- `QA`: revisar, reproducir y priorizar fallas.
- `Diseno`: mejorar estetica general o consistencia con design system.
- `Documentacion`: registrar contexto, decisiones o procedimientos.
- `Deploy`: definir o ejecutar estrategia de publicacion.
- `Negocio`: traducir una necesidad operativa a backlog tecnico.

## Reglas de refinamiento

- Reemplazar vaguedades por contexto util.
- Mantener el lenguaje claro y directo.
- Sacar muletillas, enfasis emocionales y ruido conversacional.
- Convertir datos variables en placeholders cuando el prompt deba reutilizarse.
- No inventar contexto que no fue dado.
- Si el usuario dio una captura, archivo o URL, incorporarlo explicitamente.
- Si el pedido afecta diseno, mencionar coherencia con design system.
- Si el pedido afecta funcionalidad, pedir reproduccion, causa y validacion.

## Regla de aclaracion

Si el pedido en bruto no ofrece contexto suficiente para producir un prompt confiable, el agente puede hacer preguntas breves para refinarlo mejor.

### Criterio

- preguntar solo si la falta de informacion cambia materialmente el resultado;
- hacer entre 1 y 3 preguntas concretas;
- evitar preguntas decorativas, obvias o innecesarias;
- si hay suficiente contexto para avanzar con una suposicion razonable, refinar el prompt sin interrumpir;
- si hay varias interpretaciones posibles, primero resumir lo entendido y luego preguntar solo lo minimo faltante.

### Ejemplos de cuando si preguntar

- no esta claro si el problema es visual, funcional o de contenido;
- falta la ruta, pantalla o modulo afectado;
- no se sabe si el pedido es para local, produccion o documentacion;
- hay dos soluciones validas pero con impacto distinto.

### Ejemplos de cuando no preguntar

- el pedido ya incluye captura, ruta y problema visible;
- el contexto del repo permite inferir razonablemente el modulo afectado;
- la duda no cambia el tipo de prompt ni la salida esperada.

## Criterios de calidad

Un prompt refinado esta bien hecho si:

- se puede ejecutar sin adivinar demasiado;
- deja claro que se debe analizar;
- deja claro que se debe entregar;
- reduce el riesgo de interpretar mal el pedido original;
- se puede guardar luego como prompt reutilizable.

## Ejemplo real

### Pedido en bruto

```txt
el fondo de "Conocé nuestros tratamientos" se muestra disparejo con referencia a toda la landing se debe acomodar o realizar alguna accion mas estetica, tengo la captura del issue en docs/screenshots/qa-28032026-1822/fondo-seccion-disparejo.png
```

### Tipo de tarea detectado

`UI puntual`

### Prompt refinado

```txt
Necesito revisar y corregir un problema visual en la landing publica.

Contexto:
- Seccion afectada: "Conoce nuestros tratamientos"
- Problema observado: el fondo de la seccion se percibe disparejo respecto del resto de la landing y rompe la continuidad visual general
- Evidencia visual: docs/screenshots/qa-28032026-1822/fondo-seccion-disparejo.png
- Restriccion: respetar el design system actual y mantener coherencia con la estetica general del sitio

Quiero que:
1. analices la captura y ubiques en el codigo la seccion correspondiente;
2. determines si el problema viene del color, textura, gradiente, borde, sombra, separacion entre bloques o superposicion de capas;
3. propongas la correccion mas estetica y consistente, evitando parches visuales improvisados;
4. implementes el ajuste en local;
5. indiques como validar el resultado en desktop y mobile;
6. evalúes si el cambio merece actualizar el design system.

Prioridad:
- coherencia visual con toda la landing;
- solucion elegante y reutilizable;
- evitar que la seccion quede aislada o artificial.
```

### Salida esperada

- diagnostico visual;
- correccion recomendada o aplicada;
- criterio de validacion;
- nota documental si cambia una regla reusable.

## Regla operativa acordada

Cada vez que el usuario entregue un prompt en bruto, el agente debe:

1. interpretarlo;
2. detectar el tipo de tarea;
3. formalizarlo con este estandar;
4. devolver la version refinada lista para usar.

## Relacion con la biblioteca de prompts

Este documento no reemplaza la biblioteca.

Su funcion es:

- transformar pedidos crudos en prompts bien escritos;
- y luego, si ese prompt demuestra ser reutilizable, pasarlo a `biblioteca-prompts.md`.
