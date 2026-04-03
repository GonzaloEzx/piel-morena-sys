# Analytics: Consultas de Precio — Servicios Mas Consultados

## Descripcion General

El sistema de **Consultas de Precio** registra cada vez que un visitante hace clic en el icono de precio ($) de un servicio en la landing page. Esto permite al administrador identificar los servicios mas demandados y ajustar su estrategia de marketing.

## Flujo Tecnico

### 1. Frontend (Landing Page)

**Archivo:** `index.php` (seccion Servicios)

Los servicios se cargan **dinamicamente desde la base de datos**. El bloque PHP consulta las categorias activas (hasta 6) y para cada una obtiene un servicio representativo. Las cards se generan en un loop:

```php
$stmt_cats = $db_landing->query(
    "SELECT c.id, c.nombre, c.icono,
            (SELECT COUNT(*) FROM servicios WHERE id_categoria = c.id AND activo = 1) AS total_servicios
     FROM categorias_servicios c
     WHERE c.activo = 1
     ORDER BY c.orden
     LIMIT 6"
);

$stmt_serv = $db_landing->prepare(
    "SELECT s.id, s.nombre, s.descripcion, s.precio, s.duracion_minutos,
            c.nombre AS categoria, c.icono AS categoria_icono
     FROM servicios s
     JOIN categorias_servicios c ON s.id_categoria = c.id
     WHERE s.id_categoria = ? AND s.activo = 1
     ORDER BY s.nombre LIMIT 1"
);
```

Cada card incluye un tooltip de precio para **todos los servicios** (si el precio es 0, el popup muestra "Consultar" en lugar del monto):

```html
<span class="pm-price-tooltip"
      data-service-id="<?= $serv['id'] ?>"
      data-service-name="<?= sanitizar($serv['nombre']) ?>"
      data-price="<?= $serv['precio'] ?>"
      data-duration="<?= $serv['duracion_minutos'] ?>"
      data-category="<?= sanitizar($cat['nombre']) ?>"
      title="Consultar precio">
  <i class="bi bi-currency-dollar"></i>
</span>
```

**Atributos `data-*`:**
| Atributo | Descripcion | Origen |
|----------|-------------|--------|
| `data-service-id` | ID del servicio en la BD (tabla `servicios`) | Query dinamica |
| `data-service-name` | Nombre para mostrar en el popup | Query dinamica |
| `data-price` | Precio del servicio | Query dinamica |
| `data-duration` | Duracion en minutos | Query dinamica |
| `data-category` | Nombre de la categoria | Query dinamica |

> **Nota:** Los IDs, precios y nombres se obtienen directamente de la BD en cada carga de pagina. No hay mapeo hardcodeado. Si se agregan/eliminan servicios o categorias, la landing refleja los cambios automaticamente.

### 2. JavaScript (Modulo PriceTooltip)

**Archivo:** `assets/js/banners.js`

Al hacer clic en un `.pm-price-tooltip`:
1. **Muestra popup SweetAlert2** con nombre, precio formateado (o "Consultar" si precio = 0), duracion y categoria del servicio
2. **Registra la consulta** via `fetch()` POST a la API (fire & forget)
3. **Animacion "$" flotante** sobre el icono (feedback visual, clase `.pm-price-feedback`)
4. El popup ofrece boton **"Reservar Ahora"** que redirige a `reservar.php?servicio={id}`

```js
// Fire and forget — no bloquea UX
PriceTooltip.trackClick(serviceId);

// Internamente:
fetch('api/servicios/consulta_precio.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ id_servicio: serviceId }),
});
```

La animacion "$" se crea en `_showFloatingFeedback()`: inserta un `<span class="pm-price-feedback">` con posicion fija, lo anima hacia arriba con opacity 0 via transiciones inline, y lo remueve del DOM tras 900ms.

### 3. Flujo Popup → Reservas (Preseleccion)

Cuando el usuario hace clic en "Reservar Ahora" en el popup de precio:

1. `banners.js` → `_goToBooking(serviceId)` → navega a `reservar.php?servicio={id}`
2. `reservar.php` → `$servicio_preseleccionado = intval($_GET['servicio'] ?? 0)`
3. El Wizard JS recibe el valor via `preseleccionado: <?= $servicio_preseleccionado ?>`
4. En `loadServicios()`, si hay preseleccion:
   - Abre automaticamente el accordion de la categoria correspondiente
   - Simula un clic en el servicio para avanzar al paso 2 (fecha)

> **Requiere autenticacion:** si el usuario no esta logueado, ve un aviso con botones de login/registro. El parametro `?servicio={id}` se preserva en el redirect post-login.

### 4. API Backend

**Archivo:** `api/servicios/consulta_precio.php`

- **Metodo:** POST
- **Body:** `{ "id_servicio": N }`
- **Respuesta:** `{ "success": true }`
- **Validaciones:**
  - Solo acepta POST
  - `id_servicio` debe ser entero > 0
  - El servicio debe existir y estar activo en la tabla `servicios`
- **Registro:** Inserta en tabla `consultas_precio` con IP y User-Agent
- **Fallo silencioso:** si el servicio no existe o esta inactivo, retorna 404 pero el frontend ignora la respuesta (fire & forget)

### 5. Tabla de Base de Datos

**Tabla:** `consultas_precio`

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| `id` | INT AUTO_INCREMENT | PK |
| `id_servicio` | INT | FK a `servicios.id` (ON DELETE CASCADE) |
| `ip_visitante` | VARCHAR(45) | IP del visitante |
| `user_agent` | VARCHAR(500) | Navegador del visitante |
| `created_at` | TIMESTAMP | Fecha/hora de la consulta (indexada) |

**Indices:** `idx_servicio` en `id_servicio`, `idx_fecha` en `created_at`.

### 6. Visualizacion en Admin

El sistema tiene **dos vistas** que muestran metricas de consultas de precio, cada una con un enfoque distinto:

#### 6a. Dashboard Principal (`admin/index.php`)

**Fuente de datos:** `api/admin/estadisticas.php` → campo `servicios_populares`

- **Periodo:** Ultimos 30 dias (fijo, no configurable)
- **Grafico:** Barras verticales (Chart.js)
- **Limite:** Top 5 servicios
- **Etiqueta UI:** "Servicios Mas Consultados — Ultimos 30 dias"

Query:
```sql
SELECT s.nombre, COUNT(cp.id) AS consultas
FROM consultas_precio cp
JOIN servicios s ON cp.id_servicio = s.id
WHERE cp.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY cp.id_servicio
ORDER BY consultas DESC
LIMIT 5
```

#### 6b. Vista de Reportes (`admin/views/reportes.php`)

**Fuente de datos:** `api/analytics/servicios_populares.php`

- **Periodo:** Configurable por el usuario con filtros de fecha (desde/hasta) + atajos (7, 30, 90 dias)
- **Graficos:**
  1. **Barras horizontales:** Top servicios por consultas de precio (limite configurable, default 10)
  2. **Linea temporal:** Consultas de precio por dia en el rango seleccionado
- **Datos adicionales:** Total de consultas en el rango, precio de cada servicio
- **Exportar:** CSV descargable para ambos graficos
- **Etiqueta UI:** "Servicios Mas Consultados" y "Consultas de Precio por Dia"

Query (con filtro de rango):
```sql
SELECT s.id, s.nombre, s.precio, COUNT(cp.id) AS consultas
FROM consultas_precio cp
JOIN servicios s ON cp.id_servicio = s.id
WHERE DATE(cp.created_at) >= ? AND DATE(cp.created_at) <= ?
GROUP BY cp.id_servicio
ORDER BY consultas DESC
LIMIT ?
```

**Diferencia clave:** el dashboard muestra un resumen rapido (30 dias fijo, top 5), mientras que reportes permite analisis profundo con rango personalizable, mas servicios y tendencia temporal.

## CSS Involucrado

### `.pm-price-tooltip` (style.css + premium-v3.css)
Boton circular dorado (44px) con gradiente y glow, posicionado absolute en la esquina superior derecha de la card de servicio. Escala y brilla al hover.

### `.pm-price-feedback` (style.css)
Estilo base para el "$" flotante que se crea via JS al hacer clic. Usa la fuente heading en dorado con text-shadow. La animacion (posicion, opacity) se aplica inline desde `_showFloatingFeedback()`.

### Clases del popup SweetAlert2
Las clases `pm-price-display`, `pm-price-amount`, `pm-price-category`, `pm-price-duration`, `pm-price-divider`, `pm-price-note` y `pm-price-modal` se usan dentro del HTML generado por SweetAlert2. Su estilo viene de las reglas globales de `.pm-modal` en premium-v3.css y los estilos inline del popup.

## Uso para el Administrador

### Interpretar los datos
- **Servicios con mas consultas** = Mayor interes/demanda del publico
- **Servicios con pocas consultas** = Posible necesidad de mejor posicionamiento o promocion
- **Tendencias temporales** = Usar el grafico de linea en Reportes para detectar estacionalidad
- **Dashboard vs Reportes** = Dashboard para vistazo rapido del mes; Reportes para analisis detallado

### Acciones sugeridas basadas en datos
1. **Promocionar** servicios populares (reforzar lo que funciona)
2. **Crear ofertas** en servicios con menos consultas para generar interes
3. **Ajustar precios** segun la demanda observada
4. **Exportar CSV** desde Reportes para analisis en Excel/Google Sheets
5. **Comparar periodos** usando el filtro de fechas para medir impacto de campanas

### Limitaciones
- Solo registra clics en el tooltip de precio de la landing page
- No registra consultas desde la pagina de reservas (`reservar.php`)
- Un mismo visitante puede generar multiples consultas (no se deduplica por IP/sesion)
- El tooltip se muestra para todos los servicios; servicios con precio 0 muestran "Consultar" en el popup

## Archivos Relacionados

| Archivo | Responsabilidad |
|---------|----------------|
| `index.php` | Carga dinamica de servicios desde BD + HTML de tooltips con `data-*` |
| `assets/js/banners.js` | Modulo `PriceTooltip` (clic → popup → tracking → redireccion) |
| `assets/css/style.css` | Estilos `.pm-price-tooltip`, `.pm-price-feedback` |
| `assets/css/premium-v3.css` | Estilos premium del tooltip (hover glow, scale) |
| `api/servicios/consulta_precio.php` | Endpoint POST para registrar consulta |
| `api/admin/estadisticas.php` | Endpoint GET: `servicios_populares` (top 5, 30 dias fijo) |
| `api/analytics/servicios_populares.php` | Endpoint GET: top servicios + consultas/dia (rango configurable) |
| `admin/index.php` | Dashboard: grafico barras verticales (top 5, 30 dias) |
| `admin/views/reportes.php` | Reportes: graficos barras horizontales + linea temporal + CSV |
| `reservar.php` | Preseleccion de servicio via `?servicio={id}` |
| `database/schema.hostinger.sql` | Definicion de tabla `consultas_precio` |

## Validacion Manual del Flujo Completo

1. **Landing → Tooltip:** Abrir `/`, scroll a "Nuestros Servicios", hacer clic en el icono $ de una card
2. **Popup:** Verificar que muestra nombre, precio, duracion y categoria correctos
3. **Tracking:** En la BD, verificar `SELECT * FROM consultas_precio ORDER BY id DESC LIMIT 1`
4. **Reservar:** Clic en "Reservar Ahora" en el popup → verifica que redirige a `reservar.php?servicio={id}`
5. **Preseleccion:** En reservar.php, verifica que el servicio queda seleccionado y el wizard avanza al paso 2
6. **Dashboard:** Ir a `admin/index.php` → verificar que el grafico "Servicios Mas Consultados" refleja la consulta
7. **Reportes:** Ir a `admin/views/reportes.php` → ajustar rango → verificar graficos de barras y linea temporal
