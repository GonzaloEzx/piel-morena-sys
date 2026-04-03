# Biblioteca de Prompts Reutilizables

> Estado: vigente
> Audiencia: agentes y colaboradores
> Fuente de verdad: si, para prompts reutilizables concretos
> Relacion: biblioteca normalizada derivada de notas historicas
> Ultima revision: 2026-04-03

## Como leer esta biblioteca

Cada entrada resume un patron de trabajo reutilizable. No replica pedidos historicos palabra por palabra; toma su intencion y la convierte en un prompt mas estable.

## Indice rapido

| ID | Categoria | Uso principal |
|---|---|---|
| PROMPT-ARQ-001 | Arquitectura | Arranque y plan base de proyecto |
| PROMPT-DOC-001 | Documentacion | Crear o actualizar contexto maestro |
| PROMPT-DIS-001 | Diseno | Redisenar sitio segun design system |
| PROMPT-QA-001 | QA | Paneo general en produccion |
| PROMPT-QA-002 | QA | QA enfocado admin + cliente |
| PROMPT-INC-001 | Incidencias | Investigar y corregir bug funcional |
| PROMPT-UI-001 | UI puntual | Corregir detalle visual con captura |
| PROMPT-MOB-001 | Mobile | Revisar y ajustar vista mobile |
| PROMPT-DEP-001 | Deploy | Definir o documentar estrategia Hostinger |
| PROMPT-DOC-002 | Documentacion | Actualizar docs segun estado real |
| PROMPT-FEA-001 | Producto | Inventario de features y contratos |
| PROMPT-BIZ-001 | Negocio | Adaptar backlog a necesidades reales |
| PROMPT-CNT-001 | Contenido | Personalizar sitio segun marca real |
| PROMPT-ANA-001 | Analytics | Instrumentar servicios mas consultados |
| PROMPT-META-001 | Meta prompt | Refinar pedidos en bruto a prompts ejecutables |

---

## PROMPT-ARQ-001

- Estado: vigente
- Categoria: arquitectura
- Objetivo: crear un plan inicial de trabajo y la estructura documental minima para un proyecto PHP monolitico con frontend publico y panel interno.
- Cuando usarlo: al arrancar una nueva version del proyecto o cuando falte ordenar el alcance.
- Inputs minimos: stack, modulos esperados, hosting, restricciones de negocio.

### Prompt base

```txt
Quiero que armes un plan inicial para este proyecto y la estructura de carpetas/documentos necesaria.

Contexto:
- Stack: PHP, MySQL, Bootstrap, modales y DataTables.
- Tipo de sistema: sitio one-page con panel de administracion.
- Negocio: salon de belleza, estetica, tratamientos faciales/corporales y reservas online.
- Hosting objetivo: Hostinger.

Necesito que:
1. propongas la estructura de carpetas y modulos;
2. definas fases de implementacion;
3. enumeres riesgos tecnicos;
4. indiques la documentacion minima a crear;
5. priorices una primera entrega funcional.

Entrega el resultado en Markdown, orientado a ejecucion real y no a teoria.
```

### Salida esperada

- roadmap inicial;
- estructura de carpetas;
- prioridades;
- riesgos;
- documentacion base a crear.

---

## PROMPT-DOC-001

- Estado: vigente
- Categoria: documentacion
- Objetivo: crear o actualizar el archivo maestro de contexto del proyecto.
- Cuando usarlo: cuando el sistema ya cambio y el contexto maestro quedo atrasado.
- Inputs minimos: estado real del repo, modulos existentes, decisiones abiertas.

### Prompt base

```txt
Lee el contexto actual del proyecto y crea o actualiza el archivo maestro de contexto (`CLAUDE.md` o equivalente).

Quiero que el documento:
- describa el sistema tal como existe hoy;
- enumere modulos, flujos clave y decisiones abiertas;
- detalle restricciones del hosting y deploy;
- marque deuda tecnica real;
- quede redactado para servir de punto de arranque a futuros agentes o colaboradores.

No quiero humo ni planes genericos; quiero un documento operativo y fiel al codigo actual.
```

### Salida esperada

- archivo de contexto maestro actualizado;
- diferencias entre estado documentado y estado real;
- deudas abiertas claramente listadas.

---

## PROMPT-DIS-001

- Estado: vigente
- Categoria: diseno
- Objetivo: planificar y ejecutar mejoras visuales apoyadas en el design system y skills de frontend.
- Cuando usarlo: cuando se quiera embellecer el sitio sin perder coherencia ni contexto de negocio.
- Inputs minimos: design system, capturas, paginas objetivo, publico objetivo.

### Prompt base

```txt
Usa el design system del proyecto y el skill `frontend-design` para mejorar el diseno del sitio.

Contexto:
- El sitio apunta a un publico femenino y debe transmitir elegancia, profesionalismo y cercania.
- Quiero mejoras tanto en desktop como en mobile.
- Si detectas que el design system quedo viejo o incompleto, actualizalo.

Necesito que:
1. hagas un plan por fases;
2. detectes componentes o secciones debiles;
3. propongas cambios visuales reutilizables;
4. ejecutes los cambios priorizados;
5. documentes cualquier ajuste relevante al design system.

Trata cada componente con respeto y evita cambios decorativos sin criterio de producto.
```

### Salida esperada

- plan de redisenio por fases;
- cambios visuales concretos;
- actualizacion documental si cambia la guia visual.

---

## PROMPT-QA-001

- Estado: vigente
- Categoria: QA
- Objetivo: hacer un paneo general del sitio en produccion apoyado en Playwright y documentacion viva.
- Cuando usarlo: luego de varios cambios acumulados o antes de definir nuevas prioridades.
- Inputs minimos: URL de produccion, credenciales de prueba, docs relevantes.

### Prompt base

```txt
Lee el contexto documental del proyecto, visita el sitio en produccion y realiza un paneo general usando `playwright-cli`.

Quiero que:
1. revises landing publica, login, registro, reservas y panel;
2. identifiques fallas funcionales, visuales y de consistencia;
3. cruces hallazgos con la documentacion actual;
4. propongas un plan de continuidad ordenado por prioridad;
5. indiques que deberia corregirse primero y por que.

Si detectas desajustes entre codigo, produccion y docs, dejalos explicitados.
```

### Salida esperada

- hallazgos por severidad;
- plan de continuidad;
- diferencias entre docs y realidad del sistema.

---

## PROMPT-QA-002

- Estado: vigente
- Categoria: QA
- Objetivo: planificar y ejecutar QA funcional enfocado en panel admin y experiencia cliente.
- Cuando usarlo: antes de una entrega grande o despues de tocar modulos criticos.
- Inputs minimos: credenciales admin, credenciales cliente, rutas principales.

### Prompt base

```txt
Planea y ejecuta un test enfocado al admin y al cliente usando cuentas de prueba y `playwright-cli`.

Quiero cobertura minima sobre:
- login y permisos por rol;
- gestion de citas;
- clientes;
- empleados;
- productos;
- caja;
- reserva publica y Mi Cuenta del cliente.

Entrega:
1. plan de smoke test;
2. ejecucion resumida;
3. bugs detectados con prioridad;
4. mejoras recomendadas.
```

### Salida esperada

- matriz de QA;
- hallazgos reproducibles;
- prioridades de correccion.

---

## PROMPT-INC-001

- Estado: vigente
- Categoria: incidencias
- Objetivo: investigar y corregir un bug funcional real en un modulo concreto.
- Cuando usarlo: cuando existe un problema observable en produccion o local y hay que cerrarlo de punta a punta.
- Inputs minimos: URL o archivo afectado, comportamiento esperado, credenciales si aplica.

### Prompt base

```txt
Necesito que investigues y corrijas un problema funcional en este modulo.

Contexto del problema:
- Ruta o archivo afectado: <ruta_o_url>
- Sintoma visible: <descripcion_del_error>
- Comportamiento esperado: <resultado_esperado>
- Usuario de prueba: <credenciales_si_aplica>

Quiero que:
1. reproduzcas el problema;
2. determines si falla la UI, el JS, la API, la consulta SQL o la persistencia;
3. apliques la correccion minima pero solida;
4. pruebes el flujo completo despues del cambio;
5. dejes claro que archivos tocaron y como validar.

Usa `playwright-cli` si hace falta para automatizar la reproduccion.
```

### Salida esperada

- causa raiz;
- cambio aplicado;
- verificacion posterior.

---

## PROMPT-UI-001

- Estado: vigente
- Categoria: UI puntual
- Objetivo: corregir una falla visual o de interaccion apoyada en screenshots y design system.
- Cuando usarlo: cuando hay una incidencia precisa de UI y conviene atacar con contexto visual.
- Inputs minimos: captura, archivo o modulo afectado, restriccion visual.

### Prompt base

```txt
Tengo una correccion visual puntual y quiero resolverla respetando el design system actual.

Contexto:
- Captura de referencia: <archivo_captura>
- Modulo o pagina: <ruta_o_url>
- Problema: <descripcion>
- Restriccion: mantener coherencia visual con el resto del sitio.

Necesito que:
1. diagnostiques la causa real del problema;
2. propongas un ajuste pequeno pero correcto;
3. implementes la correccion;
4. verifiques desktop y mobile si corresponde;
5. indiques si conviene documentar el ajuste en `design-system.md`.
```

### Salida esperada

- correccion visual aplicada;
- justificacion breve;
- validacion posterior.

---

## PROMPT-MOB-001

- Estado: vigente
- Categoria: mobile
- Objetivo: revisar y mejorar problemas de comportamiento o layout en dispositivos moviles reales.
- Cuando usarlo: cuando una vista se ve aceptable en emulador pero falla en telefono real.
- Inputs minimos: capturas mobile, rutas afectadas, criterios de exito.

### Prompt base

```txt
Genera un plan de correccion mobile y luego ejecutalo.

Contexto:
- Las fallas se ven en dispositivos moviles reales.
- Hay capturas de QA en `docs/para-test/screenshots/`.
- Debes respetar el design system y validar con `playwright-cli`.

Revisa, como minimo:
- overflow horizontal;
- scrolls innecesarios por seccion;
- navbar mobile y menu hamburguesa;
- contraste de iconos y botones;
- espaciado entre CTAs;
- legibilidad del hero y bloques principales.

Entrega primero un TODO list priorizado y luego ejecuta los cambios.
```

### Salida esperada

- lista de tareas mobile;
- correcciones de layout/interaccion;
- smoke test resumido.

---

## PROMPT-DEP-001

- Estado: vigente
- Categoria: deploy
- Objetivo: definir o actualizar una estrategia de deploy efectiva para Hostinger.
- Cuando usarlo: cuando el flujo actual manual ya no escala o la documentacion de deploy quedo vieja.
- Inputs minimos: estado actual de hosting, ramas usadas, acceso SSH si existe, politica de trabajo.

### Prompt base

```txt
Necesito definir y documentar la mejor estrategia de deploy para este proyecto en Hostinger.

Contexto:
- Proyecto PHP propio, no WordPress.
- Hay trabajo local, pruebas y luego publicacion.
- Quiero evaluar Git deployment, webhook, SSH, git remote y cualquier alternativa practica.

Necesito que:
1. compares metodos posibles;
2. recomiendes uno principal y uno de respaldo;
3. indiques riesgos reales;
4. propongas flujo diario de ramas;
5. actualices o recrees el documento de deploy en Markdown con pasos operativos.

Priorizo velocidad, trazabilidad y simplicidad real de mantenimiento.
```

### Salida esperada

- recomendacion de estrategia;
- runbook de deploy;
- notas de riesgos y rollback.

---

## PROMPT-DOC-002

- Estado: vigente
- Categoria: documentacion
- Objetivo: actualizar la documentacion del sitio contra el estado real del codigo.
- Cuando usarlo: despues de una tanda grande de cambios o cuando las docs estan mezcladas con historicos.
- Inputs minimos: arbol `docs/`, codigo actual, cambios recientes.

### Prompt base

```txt
Quiero que actualices y sanees la documentacion del proyecto en `docs/`.

Tareas:
1. leer los markdowns existentes para entender el contexto;
2. detectar duplicados, historicos ruidosos y discrepancias con el codigo;
3. actualizar documentos operativos y contratos afectados;
4. mejorar nombres de archivos pobres si hace falta;
5. dejar un TODO list de mantenimiento documental.

No quiero texto generico: quiero documentos utiles para quien siga el proyecto.
```

### Salida esperada

- docs actualizadas;
- discrepancias cerradas;
- backlog documental claro.

---

## PROMPT-FEA-001

- Estado: vigente
- Categoria: producto
- Objetivo: levantar un inventario de modulos, features y contratos funcionales.
- Cuando usarlo: cuando se necesita una fotografia del sistema para mantenimiento, QA o handoff.
- Inputs minimos: codigo del proyecto, `docs/contracts/`, panel y flujos principales.

### Prompt base

```txt
Haz un paneo general del proyecto y documenta todos los modulos y caracteristicas disponibles como inventario de features.

Quiero que:
- listes modulos, capacidades y restricciones;
- cruces la informacion con los contratos de negocio;
- detectes discrepancias entre documentacion y codigo;
- mejores cualquier documento de testing o usuarios de prueba que este incompleto;
- dejes el inventario en un Markdown claro y mantenible.
```

### Salida esperada

- inventario de features;
- contratos revisados;
- mejoras de documentacion para testing.

---

## PROMPT-BIZ-001

- Estado: vigente
- Categoria: negocio
- Objetivo: traducir necesidades reales del negocio a backlog tecnico accionable.
- Cuando usarlo: cuando aparecen pedidos del negocio que afectan modelo de servicio, galeria, reservas o contenido.
- Inputs minimos: reglas del negocio, restricciones del sitio actual, docs pertinentes.

### Prompt base

```txt
En base a la documentacion existente y a estas reglas del negocio, adapta el proyecto a lo que realmente necesita la operacion.

Reglas o pedidos:
- <lista_de_reglas_del_negocio>

Necesito que:
1. analices impacto funcional y tecnico;
2. propongas una implementacion pragmatica;
3. separes decisiones de producto, datos y UI;
4. armes un TODO list esperando aprobacion;
5. indiques que docs deben actualizarse al final.

No me devuelvas ideas sueltas; quiero un backlog accionable y coherente con el sistema actual.
```

### Salida esperada

- plan de implementacion por frentes;
- decisiones abiertas;
- actualizaciones documentales requeridas.

---

## PROMPT-CNT-001

- Estado: experimental
- Categoria: contenido
- Objetivo: personalizar el sitio usando activos reales de marca y referencias externas.
- Cuando usarlo: cuando ya existen logo, Instagram, telefonos o capturas reales del negocio.
- Inputs minimos: activos de marca, enlaces reales, restricciones de contenido.

### Prompt base

```txt
Quiero personalizar el sitio con datos reales de la marca.

Inputs disponibles:
- logo: <ruta_logo>
- red social principal: <url_instagram>
- telefono: <telefono>
- activos visuales: <capturas_o_imagenes>

Necesito que:
1. revises que contenido real se puede reutilizar;
2. propongas un plan de personalizacion del sitio;
3. detectes datos utiles como telefonos, ubicacion, servicios y tono de marca;
4. armes un TODO list para aprobacion antes de tocar codigo.

Debes priorizar precision y evitar inventar datos no confirmados.
```

### Salida esperada

- plan de personalizacion;
- lista de datos confirmados y faltantes;
- siguientes pasos antes de implementar.

---

## PROMPT-ANA-001

- Estado: vigente
- Categoria: analytics
- Objetivo: entender, corregir o documentar la metrica de "Servicios mas consultados".
- Cuando usarlo: cuando la instrumentacion de consultas de precio no refleja la actividad esperada.
- Inputs minimos: ruta del componente UI, dashboard admin, flujo esperado de conteo.

### Prompt base

```txt
Necesito revisar y dejar documentado el comportamiento de "Servicios mas consultados".

Objetivo:
- entender como deberia sumar eventos al consultar precio;
- verificar por que no esta contando o mostrando bien;
- corregir el flujo si hace falta;
- dejar un Markdown de referencia y contexto tecnico en `docs/`.

Quiero que revises:
1. UI del boton o tooltip de consulta de precio;
2. evento JS que dispara el conteo;
3. endpoint o persistencia asociada;
4. consumo del dato en dashboard y reportes;
5. forma de validar que el numero sube correctamente.
```

### Salida esperada

- diagnostico de la metrica;
- correccion o estado real;
- documento de referencia tecnica.

---

## PROMPT-META-001

- Estado: vigente
- Categoria: meta prompt
- Objetivo: transformar pedidos cortos o desordenados en prompts claros, ejecutables y reutilizables.
- Cuando usarlo: cuando el usuario describe algo de forma breve, informal o incompleta y conviene formalizarlo antes de ejecutar.
- Inputs minimos: pedido en bruto, contexto disponible, archivos o capturas si existen.

### Prompt base

```txt
Actua como un "Prompt Perfect Creator".

Voy a darte un pedido en bruto. Quiero que lo conviertas en un prompt refinado, claro y orientado a ejecucion.

Necesito que:
1. detectes el tipo de tarea;
2. limpies ruido conversacional;
3. extraigas contexto, restricciones e inputs utiles;
4. estructures el pedido en formato profesional;
5. agregues una salida esperada.

Devuelvelo con esta estructura:
- Pedido en bruto
- Tipo de tarea detectado
- Prompt refinado
- Salida esperada
```

### Salida esperada

- prompt refinado;
- clasificacion del tipo de tarea;
- estructura consistente para reuso.

### Referencia

- `docs/ia/prompts/prompt-perfect-creator.md`

---

## Nota de mantenimiento

Si una conversacion futura produce un prompt claramente reutilizable, no debe volver a `docs/txt/nota`. Debe entrar aca, con ID, objetivo, inputs y salida esperada.
