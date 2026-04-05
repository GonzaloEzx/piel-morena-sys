# Análisis funcional — Servicios, Promociones y Jornadas

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si, para esta iniciativa
> Relacion: analisis previo a implementacion
> Fecha: 2026-04-04
> Wireframe: `docs/para-test/screenshots/qa-04042026-2120/enfoque-nuevo.png`
> BD contrastada: `docs/temp/u347774250_pielmorena.sql` (dump del 04-04-2026)

---

## 1. Diagnóstico del estado actual

### 1.1 Servicios

**Tabla `servicios`:** 69 activos en producción (100 registros totales, ids hasta 100).

Campos relevantes:
- `id_categoria` (FK nullable) — categoría comercial
- `id_grupo_jornada` (FK nullable) — grupo operativo de jornada (escape hatch para packs cross-categoría)
- `precio`, `duracion_minutos`, `destacado`, `activo`

**API admin (`api/admin/servicios.php`):**
- POST y PUT guardan: nombre, descripcion, precio, duracion, id_categoria, imagen, destacado.
- **NO guardan `id_grupo_jornada`** — este campo fue seteado por migration 010, nunca a través del UI.
- El GET del listado sí lee y expone `id_grupo_jornada`, `jornada_origen` y `tiene_jornada_asociada` (campos calculados).

**Admin UI (`admin/views/servicios.php`):**
- Modal "Nuevo Servicio": nombre, categoría, descripción, precio, duración, imagen, destacado.
- La columna "Disponibilidad" en la tabla muestra pills "Con jornadas" o "Normal" (read-only, no editable).
- **No hay forma de asignar o cambiar `id_grupo_jornada` desde el panel.** Es la brecha principal.

### 1.2 Categorías

**Tabla `categorias_servicios`:** 13 registros, 10 activas.

| ID | Nombre | requiere_jornada | activo |
|----|--------|:---:|:---:|
| 1 | Depilación | 1 | 1 |
| 2 | Tratamientos Faciales | 0 | 1 |
| 3 | Tratamientos Corporales | 0 | 1 |
| 4 | Tratamientos de Frío | 0 | 1 |
| 6 | Manicuría | 0 | 1 |
| 9 | Cejas y Pestañas | 0 | 1 |
| 10 | Peluquería | 1 | 1 |
| 11 | Masajes | 0 | 1 |
| 12 | Extensiones de Pestañas | 1 | 1 |
| 13 | Tratamiento con equipo | 1 | 1 |

4 categorías con `requiere_jornada=1`: Depilación, Peluquería, Extensiones de Pestañas, Tratamiento con equipo.

### 1.3 Packs existentes

Hay dos tipos de agrupaciones en la BD:

**COMBOs (dentro de su categoría):**
- COMBO 1-4 (ids 64-67): viven en Depilación (`id_categoria=1`), sin `id_grupo_jornada`. Heredan la jornada de su categoría. Son servicios regulares con nombre "COMBO".

**PACKs (cross-categoría):**
- PACK REDUCTOR (id 96): `id_categoria=NULL`, `id_grupo_jornada=13` (Tratamiento con equipo)
- PACK CELULITIS (id 97): `id_categoria=NULL`, `id_grupo_jornada=13`
- Pack Depilación Definitiva (id 100): `id_categoria=NULL`, `id_grupo_jornada=1` (Depilación)

Estos packs no tienen categoría comercial. En el wizard, aparecen bajo el grupo synthetic "Packs" (línea 237 de `reservar.php`: `s.categoria || 'Packs'`).

**Dato clave:** los packs son servicios normales que se reservan como 1 cita, con su propio precio y duración. La relación con sus servicios componentes no está modelada en BD — solo existe en el texto de la descripción.

### 1.4 Jornadas

**Tabla `jornadas`:** 5 registros actuales.
- 4 jornadas de Depilación: abril 9, 10, 16, 21
- 1 jornada de Peluquería: abril 15
- Sin jornadas para Extensiones de Pestañas ni Tratamiento con equipo

**API admin (`api/admin/jornadas.php`):**
- POST valida `requiere_jornada = 1 AND activo = 1` en la categoría (línea 112). **No permite crear jornadas para categorías sin ese flag.**
- GET `categorias_jornada` filtra por `requiere_jornada = 1` (línea 49). **Solo muestra 4 categorías en el selector.**
- PATCH (cancelar) busca citas afectadas solo por `s.id_categoria`, no por `s.id_grupo_jornada`.

**API pública (`api/jornadas/disponibles.php`):**
- Resuelve grupo: `id_grupo_jornada` > `id_categoria` con `cat_requiere`.
- Pero en línea 68 verifica `$cat['requiere_jornada']` de la categoría resuelta. Si la categoría no tiene ese flag, devuelve `requiere_jornada: false` incluso si el servicio tiene `id_grupo_jornada` apuntando a ella. **Esto es un bug latente**: solo funciona porque hoy todos los grupos de jornada apuntan a categorías que SÍ tienen el flag.

**Disponibilidad (`api/citas/disponibilidad.php`):**
- Resolución: `id_grupo_jornada || cat_requiere` → busca jornada activa.
- Si hay jornada → usa sus horarios, salta chequeo de día laboral.
- Si no hay jornada → devuelve turnos vacíos con mensaje.
- Sin jornada requerida → usa horarios generales (08:00-20:00, L-V).

### 1.5 Promociones

**Tabla `promociones`:** 2 registros de seed, ambos expirados (marzo 2026).

Esquema actual (orientado a descuentos):
```
titulo, descripcion, descuento_porcentaje, descuento_monto,
fecha_inicio, fecha_fin, imagen, activo
```

- **No hay tabla pivot** que vincule promociones con servicios.
- **No hay CRUD admin** implementado.
- **No hay API admin** para promociones.
- La sección `#promos` del landing es 100% HTML estático en `index.php`.

**Conflicto:** la tabla actual es para descuentos (% o monto fijo). El nuevo concepto es bundles (N servicios → 1 precio). Son modelos distintos.

### 1.6 Citas y reservas

**Tabla `citas`:** 5 registros. FK `id_servicio` (1 servicio por cita).

**Flujo de reserva:**
1. Wizard paso 1: agrupa servicios por categoría (accordion). Null-categoría → "Packs".
2. Paso 2: consulta `disponibles.php`. Si `requiere_jornada: true` → grid de fechas. Si no → date picker.
3. Paso 3: consulta `disponibilidad.php` → slots horarios.
4. Paso 4: confirmar → `crear.php` → 1 cita.

**Backend de creación (`api/citas/crear.php`):**
- Acepta `id_servicio` (1 solo). Calcula `hora_fin` con duración del servicio.
- Soporta usuario autenticado o invitado (inconsistencia documentada con la UI que exige login).

### 1.7 Caja

**Auto-registro en caja** al marcar cita como "completada" (`api/admin/citas.php` línea 262):
- Lee `s.precio` del servicio vinculado a la cita.
- Inserta 1 entrada en `caja_movimientos` con ese monto.
- Verifica que no se duplique la entrada (`WHERE id_cita = ?`).

**Implicancia:** si un pack se reserva como 1 servicio, la caja registra 1 entrada con el precio del pack. Funciona correctamente.

---

## 2. Contraste por sub-iniciativa

### A. Promociones como bundles

**Lo que encaja:**
- Los packs ya existen como servicios normales y se reservan correctamente (1 cita, 1 precio, 1 entrada de caja).
- El wizard ya los agrupa bajo "Packs" como fallback.
- La experiencia de reserva no necesita cambios si el pack sigue siendo un servicio.

**Lo que conflictúa:**
- La tabla `promociones` actual es para descuentos, no bundles. No sirve para el nuevo concepto.
- No existe relación formal entre un pack y sus servicios componentes. Hoy eso vive solo en el campo `descripcion` (texto libre).
- La API de servicios no permite asignar `id_grupo_jornada` desde el panel. Los packs actuales fueron cargados por migration.

**Lo que necesita adaptarse:**
- Crear una tabla pivot `promocion_servicios` para vincular pack ↔ servicios componentes.
- Rediseñar o reemplazar la tabla `promociones` para que sea bundle-oriented (nombre, precio_pack, servicios componentes, vigencia).
- Crear la sección admin "Promociones" con CRUD.
- Definir si la creación de un pack auto-genera un servicio (enfoque pragmático) o si el pack es una entidad separada que requiere cambios en citas/caja (enfoque disruptivo).

### B. Jornada a nivel servicio

**Lo que encaja:**
- El campo `id_grupo_jornada` ya permite asignar jornada a nivel servicio individual. La lógica de resolución en `disponibilidad.php` ya lo prioriza sobre `requiere_jornada` de la categoría.
- El wizard ya chequea jornada por servicio individual (`disponibles.php?id_servicio=X`), no por categoría. Esto significa que servicios con diferente disponibilidad dentro de una misma categoría ya funcionan correctamente en el flujo público.

**Lo que conflictúa:**
- No hay manera de OPT-OUT: un servicio en Depilación (requiere_jornada=1) no puede elegir "Normal". El toggle necesita un override a nivel servicio.
- La API admin de jornadas solo permite crear jornadas para categorías con `requiere_jornada=1` (validación hardcoded en línea 112).
- El selector de categorías en "Nueva Jornada" solo muestra las 4 categorías con `requiere_jornada=1` (línea 49).
- `disponibles.php` tiene un bug latente: si `id_grupo_jornada` apunta a una categoría sin `requiere_jornada=1`, devuelve `requiere_jornada: false` incorrectamente (línea 68).

**Lo que necesita adaptarse:**
- Agregar campo `servicios.disponibilidad` para override per-service.
- API admin servicios: aceptar y guardar `disponibilidad` y `id_grupo_jornada` en POST/PUT.
- Admin UI servicios: toggle Normal/Jornada + selector de grupo.
- Jornada admin API: remover filtro `requiere_jornada=1` para crear jornadas en cualquier categoría.
- Jornada admin UI: mostrar todas las categorías activas en selector.
- Corregir `disponibles.php` para respetar el override de servicio.
- Ajustar `disponibilidad.php` para respetar el override.

### C. Integración y casos mixtos

**Lo que encaja:**
- Si los packs siguen siendo servicios con su propia disponibilidad, el problema de "promo mixta" ya está resuelto: el pack tiene su propio campo de disponibilidad, independiente de sus servicios componentes.
- Caja funciona sin cambios (1 cita = 1 servicio = 1 precio = 1 entrada).
- Wizard funciona sin cambios (1 pack = 1 opción seleccionable).

**Lo que conflictúa:**
- Si la promo tiene `fecha_inicio`/`fecha_fin`, ¿se oculta automáticamente del wizard al vencer? Hoy no hay mecanismo para esto.
- La sección `#promos` del landing es estática. Conectarla con datos dinámicos es una fase separada.

**Lo que necesita adaptarse:**
- Definir si la vigencia de la promo afecta la visibilidad del servicio-pack en el wizard.
- Definir cómo y dónde se muestran los packs/promos: ¿sección separada en wizard? ¿categoría "Promociones"? ¿dentro de la categoría dominante?

---

## 3. Riesgos y regresiones

### Riesgos técnicos

| # | Riesgo | Impacto | Mitigación |
|---|--------|---------|------------|
| R1 | Cambiar resolución de jornada en `disponibilidad.php` puede romper servicios existentes que hoy heredan de categoría | Alto — afecta 40+ servicios en 4 categorías | Valor default `'auto'` para el nuevo campo, que preserva el comportamiento actual |
| R2 | Bug latente en `disponibles.php` línea 68: `$cat['requiere_jornada']` falla si el grupo apunta a categoría sin flag | Medio — hoy no se manifiesta porque los grupos usan categorías con flag | Corregir la lógica para que respete `id_grupo_jornada` sin depender del flag de categoría |
| R3 | PATCH de cancelación de jornada busca citas por `s.id_categoria` pero no por `s.id_grupo_jornada` | Medio — no detecta citas de packs con grupo externo | Ampliar la query para incluir servicios con `id_grupo_jornada` |
| R4 | Rediseñar tabla `promociones` rompe los 2 registros seed existentes | Bajo — datos de prueba, no operativos | Migration limpia: drop y recrear |
| R5 | Admin cambia accidentalmente un servicio de 'auto' a 'normal' en categoría con jornada | Medio — el servicio deja de requerir jornada sin que Mari entienda por qué | UI clara: mostrar warning al cambiar disponibilidad de servicio en categoría con jornada |

### Deuda técnica existente

| # | Deuda | Origen |
|---|-------|--------|
| D1 | `id_grupo_jornada` no es gestionable desde UI admin | migration 010 seteo datos sin UI |
| D2 | Packs no tienen relación formal con servicios componentes | cargados como servicios con descripción de texto libre |
| D3 | Tabla `promociones` inutilizada (modelo de descuento, sin CRUD, sin API, sin conexión a nada) | seed data de planificación inicial |
| D4 | 2 servicios sin categoría funcional: Hidra Lips (id 87, tiene `id_categoria=2` en dump pero apareció sin categoría en snapshot API), Zona bikini (id 70, tiene `id_categoria=1`) | inconsistencia entre snapshot y dump |
| D5 | Backend soporta reserva como invitado pero UI exige login | decisión de producto pendiente |

### Regresiones a vigilar

- Wizard paso 2: verificar que servicios con `disponibilidad='auto'` siguen bifurcando correctamente.
- Packs existentes (96, 97, 100): verificar que siguen apareciendo en wizard después de cambios.
- Caja: verificar que el auto-registro sigue funcionando para packs.
- Admin tabla servicios: verificar que la columna "Disponibilidad" sigue mostrando el estado correcto.

---

## 4. Análisis de promos mixtas

### Escenario

Pack "Combo Belleza" = Laminado de Cejas (cat 9, sin jornada) + Alisado (cat 10, con jornada) + Masaje (cat 11, sin jornada).

### ¿Qué disponibilidad domina?

**La del pack, no la de sus componentes.** El pack es un servicio con su propio campo `disponibilidad`:
- Si el admin lo marca "Jornada" con grupo Peluquería → solo se puede reservar en fechas con jornada de Peluquería.
- Si el admin lo marca "Normal" → se puede reservar cualquier día laboral.

El admin toma esta decisión al crear el pack basándose en la restricción operativa real. Si el pack requiere la máquina de Peluquería (que viene ciertos días), lo marca como jornada de Peluquería. Si todos los componentes están disponibles siempre, lo marca Normal.

### Estructura de la cita

1 pack = 1 cita. La cita apunta al servicio-pack, no a los servicios componentes individuales. La duración del pack es la que Mari defina (no la suma de componentes, porque pueden superponerse en tiempo o hacerse en paralelo).

### Impacto en caja

1 cita completada = 1 entrada de caja con el precio del pack. Sin cambios en la lógica actual. El precio del pack es independiente de los precios individuales de los componentes.

### Visualización en wizard

Opciones evaluadas:

| Opción | Pro | Contra |
|--------|-----|--------|
| Categoría "Promociones" en el accordion | Separación clara, fácil de encontrar | Categoría artificial, no es una categoría de servicio real |
| Dentro de la categoría dominante del pack | Coherente con categorías existentes | Confuso para packs cross-categoría |
| Grupo "Packs" como hoy (fallback para `id_categoria=NULL`) | Ya funciona, zero esfuerzo | Nombre genérico, no es una categoría real en BD |

**Recomendación:** crear una categoría real "Packs y Promociones" (o "Combos y Promos") en `categorias_servicios` con `requiere_jornada=0`, `activo=1`. Asignar los packs a esa categoría (`id_categoria = <id_nueva_cat>`). Esto:
- Elimina el fallback synthetic "Packs" en el JS.
- Le da a Mari un grupo real donde ver sus packs en el admin.
- Permite que cada pack individual tenga su propia `disponibilidad` y `id_grupo_jornada`.

### Visualización en landing

La sección `#promos` puede seguir estática por ahora. Conectarla con datos dinámicos de la tabla de promos es una fase separada que no bloquea la funcionalidad operativa.

---

## 5. Propuesta técnica

### 5.1 Modelo de datos

#### Nuevo campo en `servicios`

```sql
ALTER TABLE servicios
ADD COLUMN disponibilidad ENUM('auto','normal','jornada') NOT NULL DEFAULT 'auto'
COMMENT 'auto=hereda de categoría, normal=calendario libre, jornada=requiere jornada activa'
AFTER id_grupo_jornada;
```

- `auto`: comportamiento actual (resuelve por `id_grupo_jornada` > `categorias_servicios.requiere_jornada`). Es el default para todos los servicios existentes → **zero migration de datos**.
- `normal`: calendario libre siempre, aunque la categoría requiera jornada.
- `jornada`: requiere jornada activa. Usa `id_grupo_jornada` si está seteado, sino usa su propia categoría.

#### Tabla `promociones` (rediseño)

```sql
DROP TABLE IF EXISTS promociones;

CREATE TABLE promociones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    precio_pack DECIMAL(10,2) NOT NULL COMMENT 'Precio del bundle',
    duracion_estimada INT NOT NULL DEFAULT 60 COMMENT 'Duración en minutos',
    fecha_inicio DATE DEFAULT NULL COMMENT 'Inicio de vigencia (NULL=sin límite)',
    fecha_fin DATE DEFAULT NULL COMMENT 'Fin de vigencia (NULL=sin límite)',
    imagen VARCHAR(255) DEFAULT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    id_servicio_generado INT DEFAULT NULL COMMENT 'FK al servicio que representa este pack en el wizard',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_activo (activo),
    KEY idx_vigencia (fecha_inicio, fecha_fin),
    CONSTRAINT fk_promo_servicio FOREIGN KEY (id_servicio_generado) REFERENCES servicios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Tabla `promocion_servicios` (nueva pivot)

```sql
CREATE TABLE promocion_servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_promocion INT NOT NULL,
    id_servicio INT NOT NULL,
    UNIQUE KEY uk_promo_servicio (id_promocion, id_servicio),
    CONSTRAINT fk_ps_promocion FOREIGN KEY (id_promocion) REFERENCES promociones(id) ON DELETE CASCADE,
    CONSTRAINT fk_ps_servicio FOREIGN KEY (id_servicio) REFERENCES servicios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Nueva categoría

```sql
INSERT INTO categorias_servicios (nombre, descripcion, icono, orden, activo, requiere_jornada)
VALUES ('Packs y Promociones', 'Combos y packs de servicios a precio especial', 'bi-gift', 12, 1, 0);
```

### 5.2 Lógica de resolución (nueva)

En `disponibilidad.php` y `disponibles.php`, la resolución pasa a ser:

```
1. Leer servicios.disponibilidad
2. Si 'normal'  → calendario libre, no chequear jornada
3. Si 'jornada' → buscar jornada (usar id_grupo_jornada si existe, sino id_categoria)
4. Si 'auto'    → comportamiento actual (id_grupo_jornada > categorias.requiere_jornada)
```

### 5.3 Cambios en APIs

| API | Cambio |
|-----|--------|
| `api/admin/servicios.php` POST | Aceptar y guardar `disponibilidad` e `id_grupo_jornada` |
| `api/admin/servicios.php` PUT | Aceptar y guardar `disponibilidad` e `id_grupo_jornada` |
| `api/admin/jornadas.php` GET `categorias_jornada` | Devolver todas las categorías activas (remover filtro `requiere_jornada=1`) |
| `api/admin/jornadas.php` POST | Remover validación `requiere_jornada=1` al crear jornada |
| `api/jornadas/disponibles.php` | Respetar `servicios.disponibilidad` antes de chequear categoría |
| `api/citas/disponibilidad.php` | Respetar `servicios.disponibilidad` en la resolución |
| `api/admin/promociones.php` (nuevo) | CRUD completo: GET listar, POST crear (+ auto-generar servicio), PUT editar, DELETE |

### 5.4 Cambios en Admin UI

| Vista | Cambio |
|-------|--------|
| `admin/views/servicios.php` modal | Agregar toggle "Disponibilidad" (Normal/Jornada) + selector de grupo jornada (condicional, visible solo si Jornada) |
| `admin/views/servicios.php` JS | Enviar `disponibilidad` e `id_grupo_jornada` en POST/PUT. Cargar categorías para selector de grupo. |
| `admin/views/jornadas.php` | Mostrar todas las categorías activas en selector de "Nueva Jornada" |
| `admin/views/promociones.php` (nuevo) | CRUD de promos: nombre, selección múltiple de servicios, precio pack, duración, vigencia |
| Sidebar admin | Agregar item "Promociones" |

### 5.5 Qué se preserva

- Tabla `citas`: sin cambios. `id_servicio` sigue apuntando al servicio (o pack-servicio).
- Tabla `caja_movimientos`: sin cambios. Auto-registro sigue usando `s.precio`.
- Tabla `jornadas`: sin cambios de esquema.
- Campo `categorias_servicios.requiere_jornada`: se mantiene como default para servicios con `disponibilidad='auto'`.
- Wizard de reserva (`reservar.php`): sin cambios estructurales (el chequeo ya es per-service).
- `api/citas/crear.php`: sin cambios.

### 5.6 Qué se refactoriza

- Resolución de jornada en `disponibilidad.php` y `disponibles.php`: agregar capa de override per-service.
- Tabla `promociones`: drop y recrear con modelo de bundles.
- Admin servicios: modal ampliado con toggle + grupo.
- Admin jornadas: selector de categorías ampliado.

---

## 6. Plan de fases

### Fase 1 — Análisis y planeamiento ✅

Este documento.

### Fase 2 — Toggle de disponibilidad per-service

**Scope:**
- Migration: agregar `servicios.disponibilidad` ENUM.
- API admin servicios: aceptar `disponibilidad` e `id_grupo_jornada` en POST/PUT.
- Admin UI servicios: toggle + selector de grupo en modal.
- Corregir resolución en `disponibilidad.php` y `disponibles.php`.

**Por qué primero:** es el cambio más chico, puramente aditivo, backward-compatible gracias al default `'auto'`. Desbloquea la gestión de `id_grupo_jornada` desde el panel (deuda técnica D1). No requiere tablas nuevas.

**Criterio de validación:**
- Crear un servicio nuevo en Manicuría con toggle "Jornada" → verificar que el wizard pide fecha de jornada.
- Servicio existente en Depilación con toggle "Normal" → verificar que el wizard muestra date picker libre.
- Servicios existentes sin tocar (disponibilidad='auto') → verificar que el comportamiento no cambió.
- Packs existentes (96, 97, 100) → verificar que siguen funcionando.

### Fase 3 — Jornadas para cualquier categoría

**Scope:**
- API admin jornadas: remover filtro `requiere_jornada=1` en POST y GET `categorias_jornada`.
- Admin UI jornadas: mostrar todas las categorías activas en selector.
- Corregir bug en `disponibles.php` línea 68 (respetar grupo sin depender del flag de categoría).
- Corregir PATCH cancelar jornada: incluir servicios con `id_grupo_jornada` en búsqueda de citas afectadas.

**Por qué segundo:** depende de fase 2 (el toggle per-service permite que un servicio en cualquier categoría use jornada). Es un cambio pequeño, concentrado en 2 archivos.

**Criterio de validación:**
- Crear jornada para Masajes (categoría sin `requiere_jornada`) → éxito.
- Crear servicio en Masajes con toggle "Jornada" + grupo Masajes → wizard muestra grid de jornadas.
- Cancelar jornada que tiene citas de packs con `id_grupo_jornada` → preview muestra todas las citas afectadas.

### Fase 4 — Promociones/Packs CRUD

**Scope:**
- Migration: redesign `promociones`, crear `promocion_servicios`, crear categoría "Packs y Promociones".
- Nueva API `api/admin/promociones.php` (CRUD).
- Nueva vista admin `admin/views/promociones.php`.
- Item "Promociones" en sidebar.
- Flujo de creación: admin selecciona servicios → define precio pack → sistema genera servicio-pack vinculado a la promo.
- Absorber packs existentes (96, 97, 100) y COMBOs de depilación (64-67): crear registros de promo y pivot para todos.
- Lógica de vigencia: filtrar servicios-pack cuya promo venció en listado público y wizard.
- Badge read-only "Parte de: Pack X" en modal de edición de servicio.

**Por qué tercero:** es la funcionalidad nueva más grande. Depende de fases 2 y 3 (los packs necesitan poder setear su disponibilidad y grupo de jornada). Es una sección admin completa nueva.

**Criterio de validación:**
- Crear promo "Pack Test" con 3 servicios → verificar que se genera servicio-pack con precio correcto.
- Pack con disponibilidad "Jornada" → verificar que aparece en wizard con grid de fechas.
- Pack con disponibilidad "Normal" → verificar que aparece con date picker.
- Reservar un pack → verificar 1 cita, 1 entrada de caja al completar.
- Packs existentes migrados → verificar que siguen funcionando igual.

### Fase 5 — Integración visual (landing + wizard)

**Scope:**
- Mover packs a la nueva categoría "Packs y Promociones" (eliminar el fallback JS "Packs").
- Evaluar: conectar sección `#promos` del landing con datos dinámicos de `promociones`.
- Evaluar: destacar packs/promos en el wizard con badge o sección especial.

**Por qué último:** es cosmético y no bloquea funcionalidad operativa. Puede hacerse iterativamente.

**Criterio de validación:**
- Wizard muestra categoría real "Packs y Promociones" en vez del fallback "Packs".
- Packs visualmente distinguibles de servicios regulares.

---

## 7. Decisiones tomadas (ex-preguntas abiertas)

### D1. Vigencia de promo → desactivación automática + manual

**Decisión:** el servicio-pack se desactiva automáticamente cuando la promo vence (`fecha_fin < hoy`). Mari también puede desactivarlo manualmente en cualquier momento.

**Implementación:** el listado público de servicios (`api/servicios/listar.php`) y el wizard deben filtrar servicios cuya promo asociada haya vencido. Alternativa: un cron diario que desactive el servicio cuando la promo vence. Se evalúa en fase 4.

### D2. COMBOs de depilación (64-67) → migrar al modelo de promos

**Decisión:** sí, migrar los COMBOs al modelo de promociones para consistencia. Son bundles de zonas de depilación con precio propio — encajan en el modelo.

**Implementación:** en fase 4, crear registros de promo y pivot para cada COMBO. Reasignar `id_categoria` a la nueva categoría "Packs y Promociones".

### D3. "Asociar promoción" en modal de servicio → informativo (read-only)

**Decisión:** el modal de servicio muestra un badge read-only "Parte de: Pack X" si el servicio está incluido en alguna promo. No es editable. La gestión de la relación promo↔servicios se hace exclusivamente desde la sección Promociones.

**Razones:**
- Una sola fuente de verdad para la relación promo↔servicios.
- El modal de servicio ya incorpora toggle de disponibilidad + selector de grupo; agregar un dropdown editable lo sobrecarga.
- Visibilidad sin riesgo: Mari sabe que el servicio es parte de un pack sin poder romper la relación accidentalmente.
- Flujo claro: para modificar un pack, va a Promociones → edita el pack.
