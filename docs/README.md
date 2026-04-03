# Documentacion del Proyecto

> Estado: vigente
> Audiencia: negocio, producto, desarrollo, operacion, agentes
> Fuente de verdad: indice maestro
> Ultima revision: 2026-04-03

## Objetivo

Este directorio concentra la documentacion oficial de `piel-morena-sys`.

La regla general es simple:

- `negocio/` explica el negocio real y sus necesidades.
- `producto/` explica el producto digital como sistema.
- `contracts/` define el comportamiento funcional esperado por modulo.
- `modulos/` documenta implementacion, dependencias y decisiones tecnicas por modulo.
- `ux/` describe recorridos, experiencia y comportamiento de interfaz.
- `diseno/` centraliza design system, tokens y criterios visuales.
- `analisis/` contiene inventarios, snapshots y diagnosticos.
- `operacion/` agrupa deploys, runbooks y manuales operativos.
- `ia/` guarda prompts y material auxiliar para trabajo con agentes.

## Mapa oficial

| Carpeta | Que contiene | Cuando leerla |
|---|---|---|
| `negocio/` | contexto del negocio, servicios, politicas | cuando la duda es comercial u operativa |
| `producto/` | vision del sistema, alcance y decisiones globales | cuando la duda es de producto |
| `contracts/` | contratos funcionales vigentes por modulo | cuando hay que decidir comportamiento esperado |
| `modulos/` | implementacion, dependencias y estado real por area | cuando hay que tocar codigo de un modulo |
| `ux/` | flujos del usuario y criterios de experiencia | cuando hay que revisar pasos, pantallas o copy |
| `diseno/` | design system y reglas visuales | cuando hay que tocar estilos o UI |
| `analisis/` | inventarios, analytics y diagnosticos | cuando hace falta evaluar el estado actual |
| `operacion/` | deploy, runbooks y manuales operativos | cuando hay que publicar o operar el sistema |
| `ia/` | prompts reutilizables y normas para agentes | cuando se trabaja con asistentes |
| `assets/` | activos documentales auxiliares | cuando se necesitan recursos visuales |

## Regla de precedencia

Si dos documentos parecen hablar de lo mismo, manda este orden:

1. `contracts/`
2. `producto/`
3. `modulos/`
4. `ux/`
5. `analisis/`

`negocio/` no compite con `contracts/`; describe la realidad del salon y el por que del sistema.

## Zonas fuera del mapa oficial

Estas carpetas existen, pero no deben tomarse como fuente oficial:

- `para-test/`: testing manual, screenshots y QA historico
- `temp/`: material transitorio, legado o en limpieza
- `txt/`: notas sueltas sin normalizar

Si un agente o colaborador necesita contexto confiable, debe ignorarlas salvo instruccion explicita.
