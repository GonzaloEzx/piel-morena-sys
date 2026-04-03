# Bloque Promociones

Fecha: 2026-04-02

## Objetivo del bloque

Documentar la sección `#promos` del landing de Piel Morena para que cualquier agente o desarrollador pueda:

- entender qué busca el negocio con esta sección;
- ubicar rápido la implementación actual;
- saber qué está hardcodeado y qué está pendiente de volverse administrable;
- modificarla sin romper estilo, comportamiento ni intención comercial.

## Propósito de negocio

La sección de promociones existe para:

- comunicar ofertas concretas y fáciles de entender;
- empujar reservas desde la landing;
- visibilizar descuentos por pack, estacionalidad o medio de pago;
- ayudar a convertir visitas frías en consultas o turnos.

Según `docs/negocio/README.md`, las promociones reales del negocio pueden incluir:

- descuentos por medio de pago;
- packs de sesiones;
- promociones estacionales;
- ofertas con fecha de vencimiento visible.

Según `docs/producto/README.md`, esta sección:

- sí debe mostrarse en el sitio;
- hoy está implementada;
- a futuro debe quedar administrable desde panel admin;
- debe ocultar promos vencidas cuando el CRUD exista.

## Ubicación dentro del sitio

- Archivo principal: `index.php`
- Ancla HTML: `#promos`
- Orden en landing: después de `#galeria` y antes de `#testimonios`
- Fondo de sección: `pm-section-alt`

## Implementación actual

La implementación vigente es estática y vive en [index.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/index.php).

### Estructura

- Título de sección:
  - `Promociones`
  - subtítulo: `Aprovecha nuestras ofertas especiales`
- Un carousel Bootstrap con id `promosCarousel`
- 3 slides hardcodeados
- Flechas laterales custom para navegación
- CTA por slide apuntando a `#reservar`

### Configuración del carousel

En el markup actual:

- clase base: `carousel slide pm-animate`
- `data-bs-ride="carousel"`
- `data-bs-interval="6000"`

Eso implica:

- autoplay activo;
- rotación cada 6 segundos;
- uso del comportamiento nativo de Bootstrap;
- sin JavaScript custom específico para promociones.

## Promociones actuales hardcodeadas

### Promo 1

- Título: `Pack Depilación Completa`
- Descuento visible: `-30%`
- Copy: `Axilas + bikini + piernas completas. Incluye 6 sesiones de depilación láser con la tecnología más avanzada del mercado.`
- CTA: `Aprovechar Oferta`

### Promo 2

- Título: `Crioterapia Facial + Corporal`
- Descuento visible: `-25%`
- Copy: `Combo de tratamientos de frío: rejuvenecimiento facial con crioterapia más sesión corporal reductora. Resultados visibles desde la primera sesión.`
- CTA: `Aprovechar Oferta`

### Promo 3

- Título: `Día de Novia`
- Descuento visible: `-20%`
- Copy: `Paquete completo para novias: limpieza facial, maquillaje profesional, manicure spa y peinado. Tu día especial merece lo mejor.`
- CTA: `Aprovechar Oferta`

## Estructura técnica del markup

Cada promo sigue este patrón:

```html
<div class="carousel-item active|...">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="pm-promo-card">
        <div class="row g-0 align-items-center">
          <div class="col-md-5">
            <div class="pm-promo-img-placeholder" style="background: ...">
              <span class="pm-promo-badge">OFERTA</span>
              <div class="pm-promo-discount">-30%</div>
            </div>
          </div>
          <div class="col-md-7">
            <div class="pm-promo-body">
              <h3 class="pm-promo-title">...</h3>
              <p class="pm-promo-desc">...</p>
              <a href="#reservar" class="btn-pm-dorado">...</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
```

## Estilos relevantes

La sección no depende de un CSS aislado; usa estilos compuestos de:

- [style.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/style.css)
- [premium-v3.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/premium-v3.css)

Además, hereda criterio visual y tokens base desde:

- [design-system.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/design-system.md)

### Clases clave

- `.pm-promo-card`
- `.pm-promo-img-placeholder`
- `.pm-promo-badge`
- `.pm-promo-discount`
- `.pm-promo-body`
- `.pm-promo-title`
- `.pm-promo-desc`
- `.pm-promo-nav`
- `.pm-promo-nav-icon`

### Decisiones visuales observables

- Card grande, centrada y con composición partida en 2 columnas en desktop.
- Bloque izquierdo usado como plano visual del descuento.
- Badge `OFERTA` arriba a la izquierda.
- Descuento enorme como foco primario.
- Cuerpo derecho con título, copy y CTA.
- Paleta cálida alineada con la identidad del sitio.
- La versión `premium-v3.css` intensifica glow, sombras y badge animado.

## Comportamiento responsive

Según CSS y capturas, el bloque:

- conserva una card única por slide;
- en desktop funciona como layout horizontal;
- en mobile se apila verticalmente;
- reduce la altura mínima del placeholder de promo en pantallas chicas;
- mantiene CTA visible dentro de la misma card;
- deja la navegación lateral menos protagonista que en desktop.

## Relación con el design system

`docs/design-system.md` sí interactúa con Promociones de forma indirecta pero importante. No define un bloque exclusivo de promos con contrato cerrado, pero sí establece piezas que esta sección consume:

- tokens de color cálidos usados por cards, fondos, bordes y acentos;
- sombras de card y hover;
- jerarquía tipográfica usada en título de sección, título de promo y copy;
- radios, bordes y espaciados consistentes con el resto del sitio;
- criterios responsive del landing y orden de secciones donde `#promos` aparece entre Galería y Testimonios.

Para un agente que vaya a tocar esta sección, el design system funciona como marco visual superior. Si una promo cambia de layout o estilo, debería seguir respetando:

- la paleta oliva, arcilla, arena y marfil;
- el lenguaje de cards suaves con sombra cálida;
- la jerarquía editorial de heading + copy + CTA;
- la consistencia responsive del landing.

## Relación con `assets/`

Sí, la carpeta `assets/` interactúa directamente con esta sección.

### Activos y archivos involucrados

- `assets/css/style.css`
  - base de la card promo, badge, descuento, cuerpo, copy y navegación
- `assets/css/premium-v3.css`
  - refuerzo premium visual: glow, sombras, gradientes y badge más protagonista

### Qué no usa hoy

- no hay JS custom específico en `assets/js/` para promos;
- no hay imágenes reales de promo dentro de `assets/img/` asociadas a esta sección;
- no hay fuente de datos JSON o API pública dedicada a promos.

### Implicancia práctica

Si un agente modifica Promociones, debería mirar primero:

1. `index.php`
2. `assets/css/style.css`
3. `assets/css/premium-v3.css`
4. `docs/design-system.md`

## Referencias visuales QA

Capturas revisadas:

- [promo_desk.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/promo_desk.png)
- [promo_mob.png](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/screenshots/qa-02042026-1303/promo_mob.png)

### Lectura de las capturas

- Desktop:
  - la sección respira bien entre Galería y Testimonios;
  - el título y subtítulo quedan centrados;
  - la card promo ocupa una caja contenida, no full-width;
  - el descuento es el elemento más dominante.

- Mobile:
  - la sección sigue siendo legible sin rediseño alternativo;
  - la card apila correctamente el área de descuento y el cuerpo de contenido;
  - el CTA sigue visible sin scroll horizontal;
  - la promo visible en captura es `Día de Novia`.

## Fuente de verdad actual vs futura

### Hoy

La fuente de verdad actual es el HTML hardcodeado en `index.php`.

No hay:

- consulta a base de datos para promos públicas;
- API pública dedicada a promociones del landing;
- render dinámico de promos;
- lógica de vencimiento en frontend.

### Futuro esperado

La capa de producto define que este bloque debería evolucionar a:

- CRUD admin de promociones;
- promos con título, descripción, descuento, imagen y vigencia;
- ocultamiento automático de promos vencidas;
- gestión compartida con packs.

## Qué no romper al tocar esta sección

- No cambiar el ancla `#promos` sin revisar navbar y links internos.
- No romper el CTA hacia `#reservar` salvo que exista una decisión explícita de negocio.
- No eliminar el formato de lectura rápida: badge, descuento, título, copy, CTA.
- No introducir más densidad de texto de la que la card puede sostener.
- No agregar dependencia JS extra si Bootstrap ya cubre el carousel.
- No desacoplar estilos de promo de las capas premium si no hay motivo.

## Limitaciones actuales

- Contenido promocional estático.
- Sin fechas visibles de vigencia.
- Sin administración desde panel.
- Sin imágenes reales por promo: hoy usa placeholders con gradientes inline.
- Sin trazabilidad de clicks o performance del bloque.

## Recomendaciones para futuras iteraciones

- Migrar el contenido a fuente dinámica administrable.
- Mantener el patrón de una sola promo protagonista por slide.
- Agregar vigencia visible solo si el negocio empieza a trabajar promos con vencimiento real.
- Reemplazar gradientes por assets reales solo si mejoran conversión y no ensucian la composición.
- Si se agrega modelado en BD, preservar fallback seguro cuando no haya promos activas.

## Archivos relevantes

- [index.php](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/index.php)
- [style.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/style.css)
- [premium-v3.css](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/assets/css/premium-v3.css)
- [design-system.md](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/design-system.md)
- [README.md negocio](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/negocio/README.md)
- [README.md producto](C:/Users/ggest/OneDrive/Escritorio/proyectos/piel-morena-sys/docs/producto/README.md)

## Resumen ejecutivo para agentes

- `#promos` es un bloque comercial de conversión.
- Hoy es 100% estático en `index.php`.
- Usa Bootstrap carousel y estilos premium existentes.
- El negocio quiere promos reales, administrables y eventualmente con vigencia.
- Cualquier refactor debería preservar: visibilidad del descuento, claridad del pack/oferta y CTA inmediato a reserva.
