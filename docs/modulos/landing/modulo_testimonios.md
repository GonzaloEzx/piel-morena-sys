# Modulo Testimonios

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: complementaria
> Relacion: implementacion actual del bloque `#testimonios`
> Ultima revision: 2026-04-03

## Objetivo del documento

Documentar la sección `#testimonios` del landing y su autogestión actual desde admin para que cualquier agente o desarrollador pueda:

- entender qué busca el negocio con este bloque;
- ubicar rápido la implementación real;
- identificar la fuente de verdad vigente;
- modificarlo sin romper estilo, comportamiento ni criterio editorial.

## Propósito de negocio

La sección de testimonios existe para reforzar confianza y conversión.

Su función dentro de la landing es:

- aportar prueba social;
- bajar fricción antes de reservar;
- transmitir calidez, profesionalismo y resultados;
- darle a Mari una forma simple de reciclar devoluciones reales que recibe por WhatsApp, Instagram o en persona;
- mostrar siempre una selección curada, no un histórico infinito.

## Estado actual

La sección ya no depende solo de contenido hardcodeado.

Hoy el bloque funciona con:

- persistencia en base de datos;
- gestión desde admin;
- render dinámico en la landing;
- fallback seguro embebido en `index.php` si la tabla falla o no tiene exactamente 6 slots válidos.

La decisión vigente es deliberada:

- no hay acumulación libre de testimonios;
- no hay botón de crear;
- no hay borrado libre desde UI;
- hay 6 slots fijos;
- Mari reemplaza el contenido del slot existente cuando quiere publicar un testimonio nuevo.

Este enfoque replica la lógica operativa de Galería:

- contenido acotado;
- reemplazo sobre una posición fija;
- sin crecimiento innecesario de datos.

## Ubicación dentro del sitio

- archivo público principal: `index.php`
- ancla HTML: `#testimonios`
- orden en landing: después de `#promos` y antes de `#reservar`
- vista admin: `admin/views/testimonios.php`
- endpoint admin: `api/admin/testimonios.php`

## Implementación actual

### Landing pública

La implementación pública vive en [index.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/index.php).

El bloque:

- consulta testimonios desde tabla `testimonios`;
- ordena por `orden ASC, id ASC`;
- solo acepta la data de base si existen exactamente 6 registros válidos;
- genera slides dinámicamente;
- mantiene el carrusel Bootstrap actual;
- conserva indicadores custom;
- sigue mostrando una sola card protagonista por slide.

Si la consulta falla o no devuelve los 6 slots esperados:

- cae al set embebido como fallback seguro;
- la sección no se rompe.

### Panel admin

La autogestión vive en [testimonios.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/admin/views/testimonios.php).

Desde ahí Mari puede:

- listar los 6 slots;
- abrir el modal de edición desde el lápiz;
- actualizar `nombre`, `rol` y `texto`;
- reemplazar el contenido del slot sin generar filas nuevas.

No puede:

- crear un séptimo testimonio;
- borrar slots desde UI;
- desordenar la estructura fija del bloque.

La API asociada es [testimonios.php API](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/admin/testimonios.php).

Métodos implementados:

- `GET`
- `PUT`

Acceso:

- solo `admin`

## Modelo de datos

La tabla es `testimonios`.

Campos relevantes:

- `id`
- `nombre`
- `rol`
- `texto`
- `orden`
- `created_at`
- `updated_at`

Regla central del modelo:

- `orden` representa el slot fijo;
- solo existen slots `1..6`;
- `orden` es único;
- el sistema no debe crecer por encima de esos 6 registros.

Decisión deliberada:

- no hay histórico;
- no hay soft delete;
- no hay versionado;
- no hay acumulación de testimonios viejos.

Esto responde a la necesidad de no ensuciar la base con datos que no tienen valor operativo una vez reemplazados.

## Migraciones y seed

La evolución del módulo quedó así:

- `007_create_testimonios.sql`
  - crea la tabla base;
  - introduce el bloque dinámico inicial.

- `008_testimonios_slots_fijos.sql`
  - normaliza el módulo a 6 slots fijos;
  - reinserta 6 testimonios verificables;
  - agrega unicidad sobre `orden`.

También quedó alineado en:

- `database/schema.sql`
- `database/schema.hostinger.sql`
- `database/seed.sql`
- `database/seed.hostinger.sql`

## Seed operativo actual

Los 6 slots iniciales verificables son:

1. Carolina López
2. Valentina Martínez
3. Florencia Sánchez
4. Gonzalo
5. Lucía Fernández
6. María Gómez

Estos datos son de arranque y están pensados para ser reemplazados por Mari desde admin cuando lleguen testimonios reales nuevos.

## Estructura técnica del markup público

Cada slide mantiene este patrón:

```html
<div class="carousel-item active|...">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
      <div class="pm-testimonial-card text-center">
        <div class="pm-testimonial-quote">
          <i class="bi bi-quote pm-testimonial-quote-icon"></i>
        </div>
        <p class="pm-testimonial-text">...</p>
        <div class="pm-testimonial-stars">...</div>
        <div class="pm-testimonial-author">
          <div class="pm-testimonial-avatar" style="background: ...">
            <span>CL</span>
          </div>
          <div class="pm-testimonial-info">
            <strong class="pm-testimonial-name">...</strong>
            <span class="pm-testimonial-role">...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
```

Campos administrables:

- `.pm-testimonial-text`
- `.pm-testimonial-name`
- `.pm-testimonial-role`

Campos derivados automáticamente:

- iniciales del avatar a partir del nombre;
- gradiente del avatar a partir de una rotación fija en código;
- indicadores según cantidad de slides renderizados.

## Elementos visuales que se conservan

La versión administrable mantiene:

- comillas editoriales grandes;
- 5 estrellas visibles;
- avatar circular con iniciales;
- nombre y rol debajo del testimonio;
- card única centrada;
- indicadores inferiores del carrusel.

No hay carga de foto real de clienta en esta versión.

## Estilos relevantes

La sección depende de:

- [style.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/style.css)
- [premium-v3.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/premium-v3.css)

Clases clave:

- `.pm-testimonial-card`
- `.pm-testimonial-quote-icon`
- `.pm-testimonial-text`
- `.pm-testimonial-stars`
- `.pm-testimonial-author`
- `.pm-testimonial-avatar`
- `.pm-testimonial-info`
- `.pm-testimonial-name`
- `.pm-testimonial-role`
- `.pm-testimonial-indicators`
- `.pm-testimonial-indicator`

Responsabilidad por capa:

- `style.css`
  - estructura base del bloque;
  - quote, texto, estrellas, avatar, autor e indicadores;
  - spacing, borde y sombras.

- `premium-v3.css`
  - tono editorial del quote;
  - escalado visual del texto testimonial;
  - subrayado cálido en el nombre;
  - refinamiento premium del hover y la card.

## Relación con el design system

[design-system.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/diseno/design-system.md) sigue siendo el marco visual del bloque.

Puntos relevantes:

- `#testimonios` está documentado dentro del orden del landing;
- el bloque debe seguir la paleta cálida del sitio;
- la tipografía testimonial mantiene intención editorial;
- la card debe seguir viéndose premium, aireada y cálida;
- testimonios no debe mutar a una UI de red social ni a un listado administrativo visible para cliente.

## Comportamiento JavaScript

El carrusel público sigue siendo Bootstrap.

La sincronización extra de indicadores custom vive en [main.js](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/js/main.js):

- `CarouselSync.init()`
- `_sync('#testimoniosCarousel', '.pm-testimonial-indicator')`

Implicancia práctica:

- la cantidad de indicadores depende de la cantidad de slides renderizados;
- el JS no trae datos, solo acompaña la interacción visual.

## Fuente de verdad vigente

La fuente de verdad principal del bloque es la tabla `testimonios`, acotada a 6 slots fijos.

La fuente operativa para negocio es:

- [testimonios.php admin](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/admin/views/testimonios.php)

La fuente de render público es:

- [index.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/index.php)

## Lectura visual QA

Captura de referencia:

- [testimonios.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/para-test/screenshots/qa-02042026-1303/testimonios.png)

Lo que se preserva visualmente:

- bastante aire en desktop;
- card única y protagonista;
- foco principal en el texto;
- autor compacto debajo;
- buena lectura en mobile sin rediseño agresivo.

## Qué no conviene romper

- el ancla `#testimonios`;
- el carrusel Bootstrap actual;
- la estructura de una sola card por slide;
- la jerarquía visual:
  - quote grande;
  - texto testimonial;
  - nombre;
  - rol;
- los indicadores custom;
- la continuidad entre `#promos`, `#testimonios` y `#reservar`;
- el tono editorial y cálido del bloque;
- la lógica de 6 slots fijos.

## Limitaciones actuales

- no hay fotos reales de clientas;
- no hay moderación ni workflow de aprobación;
- no hay histórico de cambios;
- no hay activo/inactivo;
- no hay alta libre de testimonios;
- el avatar sigue siendo derivado, no editable.

## Recomendaciones futuras

- si algún día el negocio necesita más control editorial, se podría sumar `activo` por slot;
- si se quiere distinguir origen, se podría sumar `canal_origen`;
- si se agregan fotos reales, deberían ser opcionales y no romper el diseño actual.

## Archivos relevantes

- [index.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/index.php)
- [testimonios.php admin](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/admin/views/testimonios.php)
- [testimonios.php API](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/admin/testimonios.php)
- [008_testimonios_slots_fijos.sql](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/database/migrations/008_testimonios_slots_fijos.sql)
- [main.js](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/js/main.js)
- [style.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/style.css)
- [premium-v3.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/premium-v3.css)
- [design-system.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/diseno/design-system.md)
- [producto/README.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/producto/README.md)
- [04-panel-administracion.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/contracts/04-panel-administracion.md)
- [08-landing-page.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/contracts/08-landing-page.md)

## Referencias cruzadas

- UX del bloque: `docs/ux/landing/testimonios.md`
- contrato funcional: `docs/contracts/08-landing-page.md`
- marco visual: `docs/diseno/design-system.md`

## Resumen ejecutivo para agentes

- `#testimonios` es dinámico.
- Mari administra testimonios desde el panel.
- Los campos gestionables son `nombre`, `rol` y `texto`.
- Existen 6 slots fijos.
- Editar un slot pisa el contenido anterior.
- No hay acumulación libre ni borrado desde UI.
- El bloque público mantiene el mismo estilo, carrusel y jerarquía visual del sitio.
