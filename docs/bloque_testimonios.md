# Bloque Testimonios

Fecha: 2026-04-02

## Objetivo del documento

Documentar la sección `#testimonios` del landing y su nueva autogestión desde admin para que cualquier agente o desarrollador pueda:

- entender qué busca el negocio con este bloque;
- ubicar rápido la implementación actual;
- identificar la fuente de verdad vigente;
- modificarlo sin romper estilo, comportamiento ni intención comercial.

## Propósito de negocio

La sección de testimonios existe para reforzar confianza y conversión.

Su trabajo dentro de la landing es:

- aportar prueba social real;
- bajar fricción antes de reservar;
- transmitir calidez, profesionalismo y resultados;
- darle a Mari una forma simple de publicar devoluciones de clientas;
- convertir comentarios que llegan por WhatsApp, Instagram o en persona en contenido visible del sitio.

## Estado actual

La sección ya no depende de contenido hardcodeado como única fuente.

Hoy el bloque funciona con:

- persistencia en base de datos;
- gestión desde admin;
- render dinámico en la landing;
- fallback seguro al contenido histórico si la tabla no existe o está vacía.

Eso permite:

- crear nuevos testimonios;
- editar testimonios existentes para pisarlos con contenido nuevo;
- eliminar testimonios viejos sin dejar datos innecesarios activos en la UI pública.

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
- genera slides dinámicamente;
- mantiene el carrusel Bootstrap actual;
- conserva indicadores custom;
- sigue mostrando una sola card protagonista por slide.

Si la consulta falla o no devuelve filas:

- cae al set histórico embebido como fallback seguro;
- la sección no se rompe.

### Panel admin

La autogestión vive en [testimonios.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/admin/views/testimonios.php).

Desde ahí Mari puede:

- listar testimonios;
- crear uno nuevo;
- editar uno existente para reemplazar su contenido;
- eliminar definitivamente un testimonio.

La API asociada es [testimonios.php API](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/admin/testimonios.php).

Métodos implementados:

- `GET`
- `POST`
- `PUT`
- `DELETE`

Acceso:

- solo `admin`

## Modelo de datos

La tabla nueva es `testimonios`.

Campos relevantes:

- `id`
- `nombre`
- `rol`
- `texto`
- `orden`
- `created_at`
- `updated_at`

Decisión deliberada:

- no hay histórico;
- no hay soft delete;
- no hay versionado;
- borrar realmente elimina el testimonio;
- editar pisa el contenido existente.

Esto responde a la necesidad de no ensuciar la base con testimonios viejos que ya no se quieren mostrar.

## Seed inicial

La implementación crea el bloque dinámico con un seed inicial equivalente al contenido histórico:

1. Carolina López
2. Valentina Martínez
3. Florencia Sánchez

Ese seed está contemplado en:

- migración `007_create_testimonios.sql`
- `database/schema.sql`
- `database/schema.hostinger.sql`
- `database/seed.sql`
- `database/seed.hostinger.sql`

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

Campos de contenido administrables:

- `.pm-testimonial-text`
- `.pm-testimonial-name`
- `.pm-testimonial-role`

Campos derivados automáticamente:

- iniciales del avatar a partir del nombre;
- gradiente del avatar a partir de una rotación fija en código;
- indicadores según cantidad de testimonios cargados.

## Elementos visuales que se conservan

La versión administrable mantiene:

- comillas editoriales grandes;
- 5 estrellas visibles;
- avatar circular con iniciales;
- nombre y rol debajo del testimonio;
- card única centrada;
- indicadores inferiores del carrusel.

No hay carga de foto de clienta en esta primera versión.

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

[design-system.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/design-system.md) sigue siendo el marco visual del bloque.

Puntos relevantes:

- `#testimonios` está documentado dentro del orden del landing;
- el bloque debe seguir la paleta cálida del sitio;
- la tipografía testimonial mantiene intención editorial;
- la card debe seguir viéndose premium, aireada y cálida;
- testimonios no debe mutar a una UI de panel o red social.

## Comportamiento JavaScript

El carrusel público sigue siendo Bootstrap.

La sincronización extra de indicadores custom vive en [main.js](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/js/main.js):

- `CarouselSync.init()`
- `_sync('#testimoniosCarousel', '.pm-testimonial-indicator')`

Implicancia práctica:

- si cambia la cantidad de testimonios, los indicadores deben renderizarse con la misma cantidad;
- el JS no trae datos, solo acompaña la interacción visual.

## Fuente de verdad vigente

La fuente de verdad principal del bloque ahora es la tabla `testimonios`.

La fuente operativa para negocio es:

- [testimonios.php admin](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/admin/views/testimonios.php)

La fuente de render público es:

- [index.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/index.php)

## Lectura visual QA

Captura de referencia:

- [testimonios.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/testimonios.png)

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
- el tono editorial y cálido del bloque.

## Limitaciones actuales

- no hay fotos reales de clientas;
- no hay moderación ni workflow de aprobación;
- no hay histórico de cambios;
- no hay activo/inactivo: si no se quiere mostrar, se edita o se elimina;
- el avatar sigue siendo derivado, no editable.

## Recomendaciones futuras

- si algún día Mari quiere ocultar sin borrar, recién ahí agregar `activo`;
- si quiere distinguir mejor testimonios, se podría sumar `canal_origen` o `destacado`;
- si se agregan imágenes reales, deberían ser opcionales y no romper el diseño actual.

## Archivos relevantes

- [index.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/index.php)
- [testimonios.php admin](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/admin/views/testimonios.php)
- [testimonios.php API](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/api/admin/testimonios.php)
- [main.js](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/js/main.js)
- [style.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/style.css)
- [premium-v3.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/premium-v3.css)
- [design-system.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/design-system.md)
- [producto/README.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/producto/README.md)
- [04-panel-administracion.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/contracts/04-panel-administracion.md)
- [08-landing-page.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/contracts/08-landing-page.md)

## Resumen ejecutivo para agentes

- `#testimonios` ya es dinámico.
- Mari administra testimonios desde el panel.
- Los campos mínimos gestionables son `nombre`, `rol`, `texto` y `orden`.
- Editar sirve para pisar contenido viejo.
- Eliminar borra definitivamente y evita basura en la base.
- El bloque público mantiene el mismo estilo, carrusel y jerarquía visual del sitio.
