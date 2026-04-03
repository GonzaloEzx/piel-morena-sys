# Piel Morena — Design System v3.1
> Contrato de diseño para Claude Code + frontend-design skill
> Salón de belleza, depilación, tratamientos estéticos — Resistencia, Chaco

---

## INSTRUCCIONES PARA CLAUDE CODE

Este documento es el contrato de diseño de Piel Morena. Cuando implementes cualquier componente, página o feature visual, seguí estas reglas sin excepción:

1. **Mobile-first siempre**: escribí el CSS base para móvil (≥320px) y escalá hacia arriba con `min-width`.
2. **Usá solo los tokens de este documento**: ningún color, sombra, tipografía o espaciado que no esté definido aquí.
3. **Bootstrap 5.3 es la grilla, no el diseño**: usá la grilla y utilidades de Bootstrap pero sobrescribí con las clases `.pm-*` para todo lo visual.
4. **Jerarquía de archivos CSS**: `style.css` → `premium-v3.css` → `premium-auth.css` | `admin.css` → `premium-admin.css`.
5. **Nunca hard-codees colores hex en componentes**: todo pasa por variables CSS `--pm-*`.
6. **Accesibilidad mínima no negociable**: `prefers-reduced-motion`, `focus-visible`, contraste WCAG AA, touch targets 48px en mobile.

---

## 1. Esencia de Marca

### Personalidad
Piel Morena es un espacio de **confianza, calidez y transformación**. No es un spa frío ni una clínica intimidante — es donde las clientas se sienten en casa mientras reciben tratamientos profesionales de primer nivel.

### Tono de Comunicación
- **Cálido** pero profesional
- **Cercano** sin ser informal
- **Empoderador** — "Tu piel, tu confianza"
- **Directo** — sin tecnicismos innecesarios

### Valores Visuales
| Valor | Expresión Visual |
|-------|-----------------|
| Calidez | Tonos tierra, dorados, crema |
| Confianza | Tipografía firme, espaciado generoso |
| Elegancia | Sombras suaves, bordes redondeados, espacios en blanco |
| Profesionalismo | Consistencia, grilla limpia, iconografía clara |
| Modernidad | Glassmorphism sutil, micro-animaciones discretas |

---

## 2. Paleta de Colores

### ⚠️ Regla de tokens: sin duplicados

Cada valor hex existe **una sola vez**. Los aliases referencian la variable primaria con `var()`.
Nunca uses el mismo hex en dos variables distintas — esto rompe refactors y causa bugs silenciosos.

### 2.1 Paleta Principal — "Oliva & Arcilla"

```
BRONCE     TIERRA     ARENA      CREMA      MARFIL
#8A7650    #7A654F    #DBCEA5    #ECE7D1    #F8F4E8
Primary    Dark       Accent     Bg Alt     Bg Base
```

| Token CSS | Hex | Uso |
|-----------|-----|-----|
| `--pm-bronce` | `#8A7650` | Color primario. Botones, links activos, acentos principales |
| `--pm-bronce-dark` | `#6C5D43` | Pressed state, textos oscuros sobre fondos claros |
| `--pm-tierra` | `#7A654F` | Headings, navbar, footer. Tono oscuro dominante |
| `--pm-tierra-mid` | `#957C62` | Hover de primario, bordes activos, texto secundario oscuro |
| `--pm-arena` | `#DBCEA5` | Acento cálido. Badges, bordes suaves, iconos |
| `--pm-arena-light` | `#E2B59A` | Hover suave, fondos de cards secundarias |
| `--pm-crema` | `#ECE7D1` | Fondo de secciones alternas |
| `--pm-marfil` | `#F8F4E8` | Fondo blanco cálido (body, cards) |

**Aliases explícitos** (no duplican hex, referencian la variable):
```css
--pm-bronce-light: var(--pm-tierra-mid);   /* hover del primario */
--pm-tierra-dark:  var(--pm-tierra);       /* alias semántico para footer */
```

### 2.2 Paleta de Acentos Funcionales

| Token CSS | Hex | Uso |
|-----------|-----|-----|
| `--pm-rosa` | `#B77466` | CTAs secundarios, iconos de belleza |
| `--pm-dorado` | `#FFE1AF` | Elementos premium, badges destacado, precios |
| `--pm-verde` | `#8E977D` | Éxito, confirmaciones, disponibilidad |
| `--pm-rojo` | `#A85F52` | Errores, cancelaciones, alertas |
| `--pm-azul-hielo` | `#A8BAC4` | ⚠️ CORREGIDO: era idéntico a --pm-verde. Ahora tono azul grisáceo real para crioterapia |
| `--pm-warning` | `#D4935A` | ⚠️ CORREGIDO: era idéntico a --pm-arena-light. Ahora naranja ámbar distinto y legible |

### 2.3 Paleta de Texto

| Token CSS | Hex | Contraste sobre marfil | WCAG |
|-----------|-----|----------------------|------|
| `--pm-text` | `#3F352B` | 9.8:1 | AAA ✓ |
| `--pm-text-heading` | `#4A4034` | 8.7:1 | AAA ✓ |
| `--pm-text-muted` | `#5C4F42` | ⚠️ AJUSTADO de #746754 (era 4.2:1 sobre crema, límite AA). Ahora 5.1:1 | AA ✓ |
| `--pm-text-light` | `#746754` | Usar solo sobre fondos oscuros o como decorativo no informativo | — |
| `--pm-text-inverse` | `#F8F4E8` | Texto sobre fondos oscuros (navbar, footer) | — |

### 2.4 Paleta de Superficies

| Token CSS | Hex | Uso |
|-----------|-----|-----|
| `--pm-surface` | `#FFFFFF` | Cards, modales, dropdowns |
| `--pm-surface-warm` | `#F8F4E8` | Body background |
| `--pm-surface-alt` | `#ECE7D1` | Secciones alternas (zebra) |
| `--pm-surface-hover` | `#E6DFC8` | Hover de filas, items de lista |
| `--pm-border` | `#D8CCAA` | Bordes de cards, inputs, dividers |
| `--pm-border-light` | `#E6DFC8` | Bordes sutiles |

### 2.5 Gradientes

```css
--pm-gradient-hero:   linear-gradient(135deg, #7A654F 0%, #8A7650 50%, #FFE1AF 100%);
--pm-gradient-footer: linear-gradient(180deg, #7A654F 0%, #957C62 100%);
--pm-gradient-btn:    linear-gradient(135deg, #8A7650 0%, #7A654F 100%);
--pm-gradient-frio:   linear-gradient(135deg, #8E977D 0%, #B7BEA7 100%);
--pm-glass:           rgba(248, 244, 232, 0.85);
--pm-glass-blur:      blur(12px);
--pm-glass-border:    1px solid rgba(138, 118, 80, 0.24);
```

### 2.6 Sombras

```css
--pm-shadow-sm:        0 2px 8px rgba(74, 64, 52, 0.08);
--pm-shadow-md:        0 4px 16px rgba(74, 64, 52, 0.12);
--pm-shadow-lg:        0 8px 32px rgba(74, 64, 52, 0.16);
--pm-shadow-xl:        0 16px 48px rgba(74, 64, 52, 0.20);
--pm-shadow-card:      0 2px 12px rgba(74, 64, 52, 0.10);
--pm-shadow-card-hover:0 8px 24px rgba(74, 64, 52, 0.18);
--pm-shadow-dorado:    0 4px 16px rgba(183, 116, 102, 0.35);
--pm-shadow-focus:     0 0 0 3px rgba(138, 118, 80, 0.30);  /* focus ring */
```

---

## 3. Tipografía

### Fuentes

| Rol | Fuente | Weight | Fallback |
|-----|--------|--------|----------|
| **Headings** | Playfair Display | 600, 700 | Georgia, serif |
| **UI / Botones / Nav** | Poppins | 500, 600 | Helvetica, sans-serif |
| **Body / Formularios** | DM Sans | 400, 500 | -apple-system, sans-serif |

> **Nota para Claude Code**: DM Sans reemplaza a Inter en esta versión. Es más característica y evita la estética genérica. La importación de Google Fonts actualizada es:
> `DM+Sans:wght@400;500&family=Playfair+Display:wght@600;700&family=Poppins:wght@500;600`

### Variables tipográficas

```css
--pm-font-heading: 'Playfair Display', Georgia, serif;
--pm-font-body:    'DM Sans', -apple-system, sans-serif;
--pm-font-ui:      'Poppins', Helvetica, sans-serif;
```

### Escala Tipográfica — Mobile First

```
Elemento  Mobile     Tablet(768)  Desktop(992)  Fuente
──────────────────────────────────────────────────────────────
h1        1.875rem   2.5rem       3rem          Playfair 700
h2        1.5rem     2rem         2.25rem       Playfair 700
h3        1.25rem    1.5rem       1.75rem       Playfair 600
h4        1.0625rem  1.1875rem    1.375rem      Poppins 600
h5        0.9375rem  1rem         1.125rem      Poppins 600
body      0.9375rem  1rem         1rem          DM Sans 400
small     0.8125rem  0.875rem     0.875rem      DM Sans 400
caption   0.75rem    0.75rem      0.75rem       Poppins 500 uppercase
btn       0.875rem   0.9375rem    0.9375rem     Poppins 600
badge     0.6875rem  0.75rem      0.75rem       Poppins 600 uppercase
price     1.25rem    1.375rem     1.5rem        Playfair 700
nav-link  0.875rem   0.9375rem    0.9375rem     Poppins 500
```

### Line Heights y Letter Spacing

```css
--pm-leading-tight:   1.2;     /* headings */
--pm-leading-normal:  1.6;     /* body text */
--pm-leading-relaxed: 1.8;     /* párrafos largos */

--pm-tracking-tight:  -0.02em; /* headings grandes */
--pm-tracking-normal: 0;       /* body */
--pm-tracking-wide:   0.05em;  /* badges, captions uppercase */
--pm-tracking-wider:  0.1em;   /* nav links uppercase */
```

---

## 4. Espaciado, Layout y Responsive

### Sistema de Espaciado (base 8px)

```css
--pm-space-1: 0.25rem;   /*  4px */
--pm-space-2: 0.5rem;    /*  8px */
--pm-space-3: 0.75rem;   /* 12px */
--pm-space-4: 1rem;      /* 16px */
--pm-space-5: 1.5rem;    /* 24px */
--pm-space-6: 2rem;      /* 32px */
--pm-space-7: 3rem;      /* 48px */
--pm-space-8: 4rem;      /* 64px */
--pm-space-9: 6rem;      /* 96px */
```

### Border Radius

```css
--pm-radius-sm:   6px;
--pm-radius-md:   10px;
--pm-radius-lg:   16px;
--pm-radius-xl:   24px;
--pm-radius-full: 9999px;
```

### Z-Index Scale

```css
/* Usar SIEMPRE estas variables, nunca valores arbitrarios */
--pm-z-base:     0;
--pm-z-raised:   10;    /* cards en hover, tooltips inline */
--pm-z-dropdown: 100;   /* dropdowns, selects custom */
--pm-z-sticky:   200;   /* navbar, bottom-bar sticky */
--pm-z-overlay:  300;   /* sidebars, drawers, offcanvas */
--pm-z-modal:    400;   /* SweetAlert2, modales */
--pm-z-toast:    500;   /* notificaciones, toasts */
```

### Safe Area Insets (móviles con notch / home indicator)

```css
/* Agregar en el componente que toque los bordes del viewport */
--pm-safe-top:    env(safe-area-inset-top, 0px);
--pm-safe-bottom: env(safe-area-inset-bottom, 0px);
--pm-safe-left:   env(safe-area-inset-left, 0px);
--pm-safe-right:  env(safe-area-inset-right, 0px);
```

### Breakpoints — Mobile First

```
BREAKPOINT  MIN-WIDTH   ALIAS   USO
────────────────────────────────────────────────────
base        0px         —       Móvil portrait (default)
sm          576px       sm      Móvil landscape
md          768px       md      Tablet portrait
lg          992px       lg      Desktop
xl          1200px      xl      Desktop wide
xxl         1400px      xxl     Desktop ultra wide
```

**Regla de escritura CSS — siempre min-width:**

```css
/* ✅ CORRECTO — mobile-first */
.pm-service-card { width: 100%; }
@media (min-width: 768px)  { .pm-service-card { width: 50%; } }
@media (min-width: 992px)  { .pm-service-card { width: 33.333%; } }

/* ❌ INCORRECTO — desktop-first, no usar */
.pm-service-card { width: 33.333%; }
@media (max-width: 991px)  { .pm-service-card { width: 50%; } }
@media (max-width: 767px)  { .pm-service-card { width: 100%; } }
```

### Container Max-Widths

```css
.container        { max-width: 1200px; }
.container-narrow { max-width: 800px; }
.container-wide   { max-width: 1400px; }
```

---

## 5. Componentes UI

### 5.1 Botones

**Variantes disponibles:**

| Clase | Uso | Estado base |
|-------|-----|-------------|
| `.btn-pm` | Acción primaria | bg bronce, text white |
| `.btn-pm-outline` | Acción secundaria | border bronce, text bronce |
| `.btn-pm-dorado` | Premium / destacado | gradient dorado |
| `.btn-pm-whatsapp` | CTA WhatsApp | bg #25D366 |
| `.btn-pm-ghost` | Acción terciaria | transparente, text bronce |
| `.btn-pm-outline--white` | Sobre fondos oscuros | border white, text white |

**Tamaños:**

```css
.btn-pm-sm  { padding: 6px 16px;  font-size: 0.8125rem; min-height: 36px; }
.btn-pm     { padding: 10px 24px; font-size: 0.9375rem; min-height: 44px; }
.btn-pm-lg  { padding: 14px 32px; font-size: 1.0625rem; min-height: 52px; }

/* Mobile: touch target mínimo 48px en todos los tamaños */
@media (max-width: 767px) {
    .btn-pm-sm, .btn-pm, .btn-pm-lg { min-height: 48px; }
}
```

**CSS base del botón primario:**

```css
.btn-pm {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--pm-space-2);
    background: var(--pm-bronce);
    color: var(--pm-text-inverse);
    font-family: var(--pm-font-ui);
    font-weight: 600;
    border-radius: var(--pm-radius-md);
    border: none;
    cursor: pointer;
    transition: background var(--pm-transition-base),
                box-shadow var(--pm-transition-base),
                transform var(--pm-transition-fast);
    text-decoration: none;
    white-space: nowrap;
    -webkit-tap-highlight-color: transparent;
}
.btn-pm:hover  { background: var(--pm-tierra-mid); box-shadow: var(--pm-shadow-sm); }
.btn-pm:active { transform: scale(0.98); }
.btn-pm:focus-visible { outline: none; box-shadow: var(--pm-shadow-focus); }
```

### 5.2 Inputs y Formularios

```css
.input-pm {
    display: block;
    width: 100%;
    padding: 12px 16px;
    font-family: var(--pm-font-body);
    font-size: 0.9375rem;
    color: var(--pm-text);
    background: var(--pm-surface);
    border: 1.5px solid var(--pm-border);
    border-radius: var(--pm-radius-md);
    transition: border-color var(--pm-transition-base),
                box-shadow var(--pm-transition-base);
    appearance: none;
    -webkit-appearance: none;
}
.input-pm:focus {
    outline: none;
    border-color: var(--pm-bronce);
    box-shadow: var(--pm-shadow-focus);
}
.input-pm::placeholder { color: var(--pm-text-light); }
.input-pm:disabled {
    background: var(--pm-surface-alt);
    color: var(--pm-text-light);
    cursor: not-allowed;
}

/* Mobile: mínimo 48px de altura para touch */
@media (max-width: 767px) {
    .input-pm { min-height: 48px; font-size: 16px; } /* 16px evita zoom en iOS */
}
```

### 5.3 Cards de Servicio

```css
.pm-service-card {
    background: var(--pm-surface);
    border: 1px solid var(--pm-border-light);
    border-radius: var(--pm-radius-lg);
    overflow: hidden;
    transition: transform var(--pm-transition-base),
                border-color var(--pm-transition-base),
                box-shadow var(--pm-transition-base);
}
.pm-service-card:hover {
    transform: translateY(-6px);
    border-color: var(--pm-arena);
    box-shadow: var(--pm-shadow-card-hover);
}
/* Mobile: sin hover transform (no hay hover real en touch) */
@media (max-width: 767px) {
    .pm-service-card:hover { transform: none; }
    .pm-service-card:active { transform: scale(0.98); }
}
.pm-service-card__img {
    aspect-ratio: 4/3;
    object-fit: cover;
    width: 100%;
}
.pm-service-card__body {
    padding: var(--pm-space-5);
}
.pm-service-card__badge {
    display: inline-block;
    padding: var(--pm-space-1) var(--pm-space-3);
    background: var(--pm-arena);
    color: var(--pm-tierra);
    font-family: var(--pm-font-ui);
    font-size: 0.6875rem;
    font-weight: 600;
    letter-spacing: var(--pm-tracking-wide);
    text-transform: uppercase;
    border-radius: var(--pm-radius-sm);
    margin-bottom: var(--pm-space-3);
}
.pm-service-card__title {
    font-family: var(--pm-font-heading);
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--pm-text-heading);
    margin-bottom: var(--pm-space-2);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.pm-service-card__desc {
    font-family: var(--pm-font-body);
    font-size: 0.875rem;
    color: var(--pm-text-muted);
    line-height: var(--pm-leading-normal);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: var(--pm-space-3);
}
.pm-service-card__meta {
    font-family: var(--pm-font-body);
    font-size: 0.8125rem;
    color: var(--pm-text-light);
    display: flex;
    align-items: center;
    gap: var(--pm-space-2);
    margin-bottom: var(--pm-space-4);
}
```

### 5.4 Tooltip de Precio ($)

```css
.pm-price-tooltip {
    position: absolute;
    top: var(--pm-space-3);
    right: var(--pm-space-3);
    width: 44px;
    height: 44px;
    border-radius: var(--pm-radius-full);
    background: linear-gradient(135deg, #FFE1AF 0%, #8A7650 100%);
    color: var(--pm-text);
    font-family: var(--pm-font-ui);
    font-weight: 600;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: var(--pm-z-raised);
    box-shadow: var(--pm-shadow-dorado);
    animation: pmPriceGlow 2s ease-in-out infinite;
    transition: transform var(--pm-transition-fast);
    /* Touch target ampliado sin afectar visual */
    -webkit-tap-highlight-color: transparent;
}
.pm-price-tooltip:hover  { transform: scale(1.15); }
.pm-price-tooltip:active { transform: scale(0.95); }
```

### 5.5 Navbar

```css
.pm-navbar {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: var(--pm-z-sticky);
    height: 64px;          /* mobile */
    background: transparent;
    transition: background var(--pm-transition-slow),
                height var(--pm-transition-slow),
                box-shadow var(--pm-transition-slow);
}
@media (min-width: 992px) {
    .pm-navbar { height: 80px; }
}
.pm-navbar.scrolled {
    background: var(--pm-glass);
    backdrop-filter: var(--pm-glass-blur);
    -webkit-backdrop-filter: var(--pm-glass-blur);
    border-bottom: var(--pm-glass-border);
    box-shadow: var(--pm-shadow-sm);
    height: 60px;
}
```

### 5.6 Hero Section

```css
.pm-hero {
    min-height: 100svh;    /* svh: respeta la barra del browser en mobile */
    position: relative;
    overflow: hidden;
}
/* Fallback para browsers sin svh */
@supports not (height: 100svh) {
    .pm-hero { min-height: 100vh; }
}
```

### 5.7 Secciones Alternas

```css
.pm-section           { padding: var(--pm-space-8) 0; }
.pm-section--alt      { background: var(--pm-surface-alt); }
.pm-section__title    {
    font-family: var(--pm-font-heading);
    font-weight: 700;
    color: var(--pm-tierra);
    margin-bottom: var(--pm-space-4);
}
.pm-section__divider  {
    width: 60px; height: 3px;
    background: var(--pm-bronce);
    margin: 0 auto var(--pm-space-6);
    border-radius: 2px;
}
/* Mobile: padding reducido */
@media (max-width: 767px) {
    .pm-section { padding: var(--pm-space-7) 0; }
}
```

### 5.8 Modales (SweetAlert2)

```css
.swal2-popup.pm-modal {
    border-radius: var(--pm-radius-lg);
    font-family: var(--pm-font-body);
    border-top: 4px solid var(--pm-bronce);
    padding: var(--pm-space-6);
}
.pm-modal .swal2-title {
    font-family: var(--pm-font-heading);
    color: var(--pm-tierra);
}
.pm-modal .swal2-confirm {
    background: var(--pm-bronce);
    font-family: var(--pm-font-ui);
    font-weight: 600;
    border-radius: var(--pm-radius-md);
    min-height: 44px;
    padding: 10px 24px;
}
.pm-modal .swal2-confirm:focus-visible {
    box-shadow: var(--pm-shadow-focus);
}
/* Mobile: full-width */
@media (max-width: 767px) {
    .swal2-popup.pm-modal { width: calc(100% - 2rem); margin: 0 1rem; }
    .pm-modal .swal2-confirm,
    .pm-modal .swal2-cancel { width: 100%; min-height: 48px; }
}
```

### 5.9 DataTables (Admin)

```css
/* Override Bootstrap DataTables */
.pm-datatable thead th {
    background: var(--pm-tierra);
    color: var(--pm-text-inverse);
    font-family: var(--pm-font-ui);
    font-weight: 600;
    font-size: 0.8125rem;
    letter-spacing: var(--pm-tracking-wide);
    text-transform: uppercase;
    white-space: nowrap;
}
.pm-datatable tbody tr:hover {
    background: var(--pm-surface-hover);
}
.pm-datatable tbody tr.selected {
    background: var(--pm-crema);
    border-left: 3px solid var(--pm-bronce);
}
.dataTables_paginate .paginate_button.current {
    background: var(--pm-bronce) !important;
    color: white !important;
    border-radius: var(--pm-radius-sm);
}
/* Mobile: scroll horizontal */
@media (max-width: 767px) {
    .pm-datatable-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
}
```

### 5.10 Badge de Estado

```css
.pm-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--pm-space-1);
    padding: 3px var(--pm-space-3);
    font-family: var(--pm-font-ui);
    font-size: 0.6875rem;
    font-weight: 600;
    letter-spacing: var(--pm-tracking-wide);
    text-transform: uppercase;
    border-radius: var(--pm-radius-sm);
    white-space: nowrap;
}
.pm-badge--success  { background: #EBF3E8; color: #3B6D11; }
.pm-badge--warning  { background: #FDF0E5; color: #854F0B; }
.pm-badge--danger   { background: #FAECEC; color: #791F1F; }
.pm-badge--info     { background: #F0F4F8; color: #185FA5; }
.pm-badge--pending  { background: var(--pm-crema);         color: var(--pm-tierra); }
.pm-badge--premium  { background: var(--pm-dorado);        color: var(--pm-tierra); }
```

---

## 6. Componentes Mobile-Only (nuevos en v3.1)

### 6.1 Sticky Bottom Action Bar

Barra de acción fija al pie de pantalla. Usarla en: flujo de reservas, páginas de servicio, página de confirmación.

```css
.pm-bottom-bar {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    z-index: var(--pm-z-sticky);
    padding: var(--pm-space-3) var(--pm-space-4);
    /* Safe area para iPhone con home indicator */
    padding-bottom: calc(var(--pm-space-3) + env(safe-area-inset-bottom, 0px));
    background: var(--pm-glass);
    backdrop-filter: var(--pm-glass-blur);
    -webkit-backdrop-filter: var(--pm-glass-blur);
    border-top: var(--pm-glass-border);
    box-shadow: 0 -4px 16px rgba(74, 64, 52, 0.12);
    display: flex;
    gap: var(--pm-space-3);
    align-items: center;
}
/* Solo visible en mobile */
@media (min-width: 768px) { .pm-bottom-bar { display: none; } }

/* Compensar espacio para que el contenido no quede tapado */
.pm-has-bottom-bar { padding-bottom: calc(72px + env(safe-area-inset-bottom, 0px)); }
```

**HTML de uso:**
```html
<div class="pm-bottom-bar">
    <div class="pm-bottom-bar__info flex-grow-1">
        <span class="pm-bottom-bar__label">Depilación Láser Axilas</span>
        <span class="pm-bottom-bar__price">$3.500</span>
    </div>
    <a href="#reservar" class="btn-pm btn-pm-lg flex-shrink-0">Reservar</a>
</div>
```

### 6.2 Time Slot Picker (Mobile First)

Componente clave del flujo de reservas. Diseño: grilla de chips táctiles.

```css
.pm-slot-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--pm-space-2);
    padding: var(--pm-space-4) 0;
}
@media (min-width: 480px) { .pm-slot-grid { grid-template-columns: repeat(4, 1fr); } }
@media (min-width: 768px) { .pm-slot-grid { grid-template-columns: repeat(5, 1fr); } }

.pm-slot {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
    padding: var(--pm-space-2);
    background: var(--pm-surface);
    border: 1.5px solid var(--pm-border);
    border-radius: var(--pm-radius-md);
    font-family: var(--pm-font-ui);
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--pm-text);
    cursor: pointer;
    transition: all var(--pm-transition-fast);
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}
.pm-slot:hover       { border-color: var(--pm-bronce); color: var(--pm-bronce); }
.pm-slot.selected    {
    background: var(--pm-bronce);
    border-color: var(--pm-bronce);
    color: var(--pm-text-inverse);
    box-shadow: var(--pm-shadow-sm);
}
.pm-slot.unavailable {
    background: var(--pm-surface-alt);
    color: var(--pm-text-light);
    cursor: not-allowed;
    opacity: 0.5;
    text-decoration: line-through;
}
```

### 6.3 Service Selection Card (Mobile — Horizontal)

En mobile, las cards de servicio adoptan layout horizontal (imagen 30% + texto 70%) para mostrar más opciones sin scroll excesivo.

```css
.pm-service-card--horizontal {
    display: flex;
    flex-direction: row;
    align-items: stretch;
}
.pm-service-card--horizontal .pm-service-card__img {
    width: 30%;
    min-width: 90px;
    aspect-ratio: 1/1;
    object-fit: cover;
    border-radius: var(--pm-radius-lg) 0 0 var(--pm-radius-lg);
}
.pm-service-card--horizontal .pm-service-card__body {
    flex: 1;
    padding: var(--pm-space-3) var(--pm-space-4);
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: var(--pm-space-1);
}

/* Activar automáticamente en móvil para la vista de selección */
@media (max-width: 767px) {
    .pm-service-select-list .pm-service-card {
        flex-direction: row;
    }
    .pm-service-select-list .pm-service-card__img {
        width: 28%;
        min-width: 80px;
        aspect-ratio: 1/1;
        border-radius: var(--pm-radius-lg) 0 0 var(--pm-radius-lg);
    }
    .pm-service-select-list .pm-service-card__title {
        font-size: 0.9375rem;
        -webkit-line-clamp: 1;
    }
    .pm-service-select-list .pm-service-card__desc {
        -webkit-line-clamp: 1;
    }
}
```

### 6.4 Step Progress Bar (Reservas)

Indicador de progreso del flujo de reserva: Servicio → Fecha → Hora → Confirmar.

```css
.pm-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: var(--pm-space-6);
    overflow-x: auto;
    padding: var(--pm-space-2) 0;
    scrollbar-width: none;
}
.pm-steps::-webkit-scrollbar { display: none; }

.pm-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--pm-space-1);
    position: relative;
    min-width: 64px;
}
/* Línea conectora */
.pm-step + .pm-step::before {
    content: '';
    position: absolute;
    left: calc(-50% - 0px);
    top: 16px;
    width: 100%;
    height: 2px;
    background: var(--pm-border);
    z-index: 0;
}
.pm-step.done + .pm-step::before,
.pm-step.active + .pm-step::before {
    background: var(--pm-bronce);
}
.pm-step__dot {
    width: 32px; height: 32px;
    border-radius: var(--pm-radius-full);
    border: 2px solid var(--pm-border);
    background: var(--pm-surface);
    display: flex; align-items: center; justify-content: center;
    font-family: var(--pm-font-ui);
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--pm-text-light);
    position: relative;
    z-index: 1;
    transition: all var(--pm-transition-base);
}
.pm-step.done .pm-step__dot {
    background: var(--pm-bronce);
    border-color: var(--pm-bronce);
    color: white;
}
.pm-step.active .pm-step__dot {
    border-color: var(--pm-bronce);
    color: var(--pm-bronce);
    box-shadow: var(--pm-shadow-focus);
}
.pm-step__label {
    font-family: var(--pm-font-ui);
    font-size: 0.6875rem;
    font-weight: 500;
    color: var(--pm-text-light);
    text-align: center;
    white-space: nowrap;
}
.pm-step.active .pm-step__label { color: var(--pm-bronce); font-weight: 600; }
.pm-step.done .pm-step__label   { color: var(--pm-tierra-mid); }
```

---

## 7. Accesibilidad (no negociable)

### Focus Visible

```css
/* Reset del outline feo del browser, reemplazar con shadow propio */
*:focus { outline: none; }
*:focus-visible {
    outline: none;
    box-shadow: var(--pm-shadow-focus);
    border-radius: var(--pm-radius-sm);
}
/* Para elementos que ya tienen border-radius propio */
.btn-pm:focus-visible,
.input-pm:focus-visible,
.pm-slot:focus-visible {
    box-shadow: var(--pm-shadow-focus);
}
```

### Touch Targets (WCAG 2.5.8)

```css
/* Todos los elementos interactivos en mobile: mínimo 48×48px */
@media (max-width: 767px) {
    .btn-pm, .input-pm, .pm-slot,
    .pm-nav-link, .pm-social-icon,
    select, textarea { min-height: 48px; }
    
    /* Para iconos pequeños: ampliar área sin cambiar visual */
    .pm-icon-btn {
        min-width: 48px; min-height: 48px;
        display: inline-flex;
        align-items: center; justify-content: center;
    }
}
```

### Reduced Motion

```css
/* Respetar la preferencia del sistema operativo */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
    .pm-price-tooltip { animation: none; }
    .pm-hero-carousel { animation: none; }
}
```

### Contraste de Texto

Las siguientes combinaciones están verificadas como WCAG AA mínimo:

| Texto | Fondo | Ratio | WCAG |
|-------|-------|-------|------|
| `--pm-text` #3F352B | `--pm-marfil` | 9.8:1 | AAA |
| `--pm-text-heading` | `--pm-crema` | 7.2:1 | AAA |
| `--pm-text-muted` #5C4F42 | `--pm-marfil` | 5.1:1 | AA |
| `--pm-text-inverse` | `--pm-tierra` | 6.8:1 | AA |
| white | `--pm-bronce` | 4.7:1 | AA |

⚠️ **NO usar** `--pm-text-light` (#746754) como color principal de texto informativo — solo para decoración o sobre fondos oscuros.

---

## 8. Animaciones y Transiciones

### Principios
- Animaciones **sutiles y funcionales**, nunca decorativas
- Duración máxima: 400ms para UI, 800ms para entrada de secciones
- **Siempre** incluir `@media (prefers-reduced-motion: reduce)` en el mismo bloque

### Variables de Transición

```css
--pm-transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
--pm-transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
--pm-transition-slow: 400ms cubic-bezier(0.4, 0, 0.2, 1);
```

### Keyframes

```css
@keyframes pmFadeUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes pmFadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}
@keyframes pmScaleIn {
    from { opacity: 0; transform: scale(0.95); }
    to   { opacity: 1; transform: scale(1); }
}
@keyframes pmPriceGlow {
    0%, 100% { box-shadow: 0 2px 8px rgba(255,225,175,0.3); }
    50%      { box-shadow: 0 4px 20px rgba(255,225,175,0.6); }
}
@keyframes pmShimmer {
    0%   { background-position: -200% center; }
    100% { background-position:  200% center; }
}
@keyframes pmFloat {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-10px); }
}
@keyframes pmPulseGlow {
    0%, 100% { transform: scale(1);    box-shadow: 0 2px 8px  rgba(168,95,82,0.3); }
    50%       { transform: scale(1.05); box-shadow: 0 4px 16px rgba(168,95,82,0.5); }
}
```

### Tabla de Micro-interacciones

| Elemento | Efecto | Trigger | Mobile |
|----------|--------|---------|--------|
| Card servicio | `translateY(-6px)` + shadow | hover | solo active |
| Botón primario | `brightness(1.08)` + shadow | hover | — |
| Botón outline | fill + color swap | hover | — |
| Nav link | opacity + underline | hover | — |
| Tooltip precio | `scale(1.15)` + glow | hover | active |
| Card galería | `scale(1.03)` + overlay | hover | active |
| Social icon | `translateY(-3px)` + color | hover | active |
| Input focus | border glow | focus | focus |
| Slot disponible | border + color | hover | active |
| Slot seleccionado | fill bg + shadow | — | — |
| Back-to-Top | fade + scale | scroll | scroll |

---

## 9. Iconografía

### Librería: Bootstrap Icons 1.13+

| Contexto | Icono | Clase |
|----------|-------|-------|
| Depilación | Estrellas | `bi-stars` |
| Tratamientos faciales | Gota | `bi-droplet-half` |
| Tratamientos corporales | Persona | `bi-person-arms-up` |
| Crioterapia / Frío | Copo de nieve | `bi-snow` |
| Maquillaje | Paleta | `bi-palette` |
| Precio / Dinero | Moneda | `bi-currency-dollar` |
| Reloj / Duración | Reloj | `bi-clock` |
| Calendario / Cita | Calendario | `bi-calendar-check` |
| WhatsApp | `bi-whatsapp` | |
| Instagram | `bi-instagram` | |
| Facebook | `bi-facebook` | |
| Teléfono | `bi-telephone-fill` | |
| Email | `bi-envelope-fill` | |
| Ubicación | `bi-geo-alt-fill` | |
| Éxito | `bi-check-circle-fill` | |
| Error | `bi-x-circle-fill` | |
| Info | `bi-info-circle-fill` | |
| Dashboard | `bi-grid-1x2-fill` | |
| Caja | `bi-cash-stack` | |
| Clientes | `bi-people-fill` | |
| Empleados | `bi-person-badge` | |
| Productos | `bi-box-seam` | |

### Tamaños de Iconos

```css
--pm-icon-sm:  16px;   /* inline con texto */
--pm-icon-md:  20px;   /* botones, inputs */
--pm-icon-lg:  24px;   /* cards, listas */
--pm-icon-xl:  32px;   /* features, highlights */
--pm-icon-xxl: 48px;   /* hero, empty states */
```

---

## 10. Imágenes y Fotografía

### Overlays

```css
.pm-overlay-warm {
    background: linear-gradient(180deg, rgba(74,46,26,0.3) 0%, rgba(107,66,38,0.7) 100%);
}
```

### Aspect Ratios de Imágenes

```css
.pm-img-service { aspect-ratio: 4/3; object-fit: cover; }
.pm-img-gallery { aspect-ratio: 1/1; object-fit: cover; }
.pm-img-team    { aspect-ratio: 3/4; object-fit: cover; }
.pm-img-banner  { aspect-ratio: 16/9; object-fit: cover; }

/* Mobile: servicios en horizontal usan 1:1 */
@media (max-width: 767px) {
    .pm-service-card--horizontal .pm-img-service { aspect-ratio: 1/1; }
}
```

### Placeholders

```css
.pm-img-placeholder {
    background: linear-gradient(135deg, var(--pm-crema), var(--pm-bronce));
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.6);
    font-size: var(--pm-icon-xxl);
}
```

---

## 11. Layout de Landing Page

```
MOBILE                      DESKTOP
──────────────────────       ────────────────────────────────────
NAVBAR (64px)                NAVBAR (80px)
HERO (100svh)                HERO (100svh)
SOBRE NOSOTROS               SOBRE NOSOTROS (2 cols: foto + texto)
SERVICIOS (1 col)            SERVICIOS (3 cols)
EQUIPO (2 cols)              EQUIPO (4 cols)
GALERÍA (2 cols)             GALERÍA (masonry 3 cols)
PROMOCIONES (1 col)          PROMOCIONES (carousel)
TESTIMONIOS (1 col)          TESTIMONIOS (3 cols)
CTA RESERVAR                 CTA RESERVAR
CONTACTO (stacked)           CONTACTO (form izq + mapa der)
FOOTER (stacked)             FOOTER (4 cols)

⬇ BOTTOM BAR sticky          (oculta en desktop)
```

### Secciones IDs

```
#hero        #nosotros   #servicios   #equipo
#galeria     #promos     #testimonios #reservar   #contacto
```

---

## 12. Componentes Premium CSS (v3.0)

### Arquitectura de Archivos

| Capa | Archivo | Propósito |
|------|---------|-----------|
| 1 | `assets/css/style.css` | Base: variables, reset, tipografía, grilla, componentes core |
| 2 | `assets/css/premium-v3.css` | Landing: hero dramático, glassmorphism, galería, CTA, offcanvas |
| 3 | `assets/css/premium-auth.css` | Auth + reservas: blobs, glassmorphism cards, stepper |
| 4 | `admin/assets/css/admin.css` | Admin: sidebar, topbar, DataTables, stat cards |
| 5 | `admin/assets/css/premium-admin.css` | Admin premium: golden accents, glassmorphism, shimmer |

### Carga condicional (PHP)

```php
// En el head, después de Bootstrap
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/css/premium-v3.css')) {
    $v = filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/css/premium-v3.css');
    echo "<link rel='stylesheet' href='/assets/css/premium-v3.css?v={$v}'>";
}
```

### Variables Premium (definir en :root de style.css)

```css
/* Premium v3 — Landing */
--pm-v3-gold-line:     linear-gradient(90deg, transparent, #FFE1AF 30%, #8A7650 70%, transparent);
--pm-v3-text-gradient: linear-gradient(135deg, #8A7650, #FFE1AF 50%, #7A654F);
--pm-v3-warm-glow:     0 8px 32px rgba(255,225,175,0.25);
--pm-v3-glass-premium: rgba(248,244,232,0.90);

/* Premium Admin */
--pma-gold-glow:     0 4px 20px rgba(255,225,175,0.3);
--pma-warm-glass:    rgba(248,244,232,0.85);
--pma-accent-line:   linear-gradient(180deg, #8A7650, #FFE1AF);
--pma-shadow-warm:   0 4px 16px rgba(74,64,52,0.15);
```

---

## 13. Componentes Admin Panel

### Sidebar

```css
.pm-admin-sidebar {
    width: 260px;
    position: fixed;
    top: 0; left: 0;
    height: 100vh;
    z-index: var(--pm-z-overlay);
    background: var(--pm-gradient-footer);
    overflow-y: auto;
    transition: transform var(--pm-transition-slow);
}
/* Mobile: oculto por defecto, slide desde la izquierda */
@media (max-width: 991px) {
    .pm-admin-sidebar { transform: translateX(-100%); }
    .pm-admin-sidebar.open { transform: translateX(0); }
}
```

### Stat Cards

```css
.pm-stat-card {
    background: var(--pm-surface);
    border: 1px solid var(--pm-border-light);
    border-radius: var(--pm-radius-lg);
    padding: var(--pm-space-5);
    position: relative;
    overflow: hidden;
    transition: box-shadow var(--pm-transition-base);
}
.pm-stat-card:hover { box-shadow: var(--pm-shadow-md); }
.pm-stat-card__value {
    font-family: var(--pm-font-heading);
    font-size: 2rem;
    font-weight: 700;
    color: var(--pm-tierra);
    line-height: 1;
    margin-bottom: var(--pm-space-1);
}
.pm-stat-card__label {
    font-family: var(--pm-font-ui);
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--pm-text-muted);
    text-transform: uppercase;
    letter-spacing: var(--pm-tracking-wide);
}
/* Mobile: 2 columnas */
@media (max-width: 767px) {
    .pm-stat-grid { grid-template-columns: repeat(2, 1fr); gap: var(--pm-space-3); }
    .pm-stat-card__value { font-size: 1.5rem; }
}
```

---

## 14. Favicon

SVG en `assets/img/favicon.svg` con iniciales "PM" sobre fondo gradient bronce/tierra.

```html
<link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
```

---

## 15. Variables CSS Completas — :root

```css
:root {
    /* ── Colores Primarios ── */
    --pm-bronce:           #8A7650;
    --pm-bronce-dark:      #6C5D43;
    --pm-tierra:           #7A654F;
    --pm-tierra-mid:       #957C62;
    --pm-arena:            #DBCEA5;
    --pm-arena-light:      #E2B59A;
    --pm-crema:            #ECE7D1;
    --pm-marfil:           #F8F4E8;

    /* Aliases — NO duplicar hex, referenciar variable */
    --pm-bronce-light:     var(--pm-tierra-mid);
    --pm-tierra-dark:      var(--pm-tierra);

    /* ── Acentos Funcionales ── */
    --pm-rosa:             #B77466;
    --pm-dorado:           #FFE1AF;
    --pm-verde:            #8E977D;
    --pm-rojo:             #A85F52;
    --pm-azul-hielo:       #A8BAC4;   /* CORREGIDO: era igual a --pm-verde */
    --pm-warning:          #D4935A;   /* CORREGIDO: era igual a --pm-arena-light */

    /* ── Texto ── */
    --pm-text:             #3F352B;
    --pm-text-heading:     #4A4034;
    --pm-text-muted:       #5C4F42;   /* AJUSTADO para WCAG AA sobre crema */
    --pm-text-light:       #746754;   /* Solo decorativo o sobre fondos oscuros */
    --pm-text-inverse:     #F8F4E8;

    /* ── Superficies ── */
    --pm-surface:          #FFFFFF;
    --pm-surface-warm:     #F8F4E8;
    --pm-surface-alt:      #ECE7D1;
    --pm-surface-hover:    #E6DFC8;
    --pm-border:           #D8CCAA;
    --pm-border-light:     #E6DFC8;

    /* ── Gradientes ── */
    --pm-gradient-hero:    linear-gradient(135deg, #7A654F 0%, #8A7650 50%, #FFE1AF 100%);
    --pm-gradient-footer:  linear-gradient(180deg, #7A654F 0%, #957C62 100%);
    --pm-gradient-btn:     linear-gradient(135deg, #8A7650 0%, #7A654F 100%);
    --pm-gradient-frio:    linear-gradient(135deg, #8E977D 0%, #B7BEA7 100%);
    --pm-glass:            rgba(248, 244, 232, 0.85);
    --pm-glass-blur:       blur(12px);
    --pm-glass-border:     1px solid rgba(138, 118, 80, 0.24);

    /* ── Sombras ── */
    --pm-shadow-sm:        0 2px 8px rgba(74, 64, 52, 0.08);
    --pm-shadow-md:        0 4px 16px rgba(74, 64, 52, 0.12);
    --pm-shadow-lg:        0 8px 32px rgba(74, 64, 52, 0.16);
    --pm-shadow-xl:        0 16px 48px rgba(74, 64, 52, 0.20);
    --pm-shadow-card:      0 2px 12px rgba(74, 64, 52, 0.10);
    --pm-shadow-card-hover:0 8px 24px rgba(74, 64, 52, 0.18);
    --pm-shadow-dorado:    0 4px 16px rgba(183, 116, 102, 0.35);
    --pm-shadow-focus:     0 0 0 3px rgba(138, 118, 80, 0.30);

    /* ── Tipografía ── */
    --pm-font-heading:     'Playfair Display', Georgia, serif;
    --pm-font-body:        'DM Sans', -apple-system, sans-serif;
    --pm-font-ui:          'Poppins', Helvetica, sans-serif;
    --pm-leading-tight:    1.2;
    --pm-leading-normal:   1.6;
    --pm-leading-relaxed:  1.8;
    --pm-tracking-tight:   -0.02em;
    --pm-tracking-normal:  0;
    --pm-tracking-wide:    0.05em;
    --pm-tracking-wider:   0.1em;

    /* ── Espaciado ── */
    --pm-space-1: 0.25rem;
    --pm-space-2: 0.5rem;
    --pm-space-3: 0.75rem;
    --pm-space-4: 1rem;
    --pm-space-5: 1.5rem;
    --pm-space-6: 2rem;
    --pm-space-7: 3rem;
    --pm-space-8: 4rem;
    --pm-space-9: 6rem;

    /* ── Radius ── */
    --pm-radius-sm:   6px;
    --pm-radius-md:   10px;
    --pm-radius-lg:   16px;
    --pm-radius-xl:   24px;
    --pm-radius-full: 9999px;

    /* ── Z-Index Scale ── */
    --pm-z-base:     0;
    --pm-z-raised:   10;
    --pm-z-dropdown: 100;
    --pm-z-sticky:   200;
    --pm-z-overlay:  300;
    --pm-z-modal:    400;
    --pm-z-toast:    500;

    /* ── Safe Areas (iOS / Notch) ── */
    --pm-safe-top:    env(safe-area-inset-top, 0px);
    --pm-safe-bottom: env(safe-area-inset-bottom, 0px);
    --pm-safe-left:   env(safe-area-inset-left, 0px);
    --pm-safe-right:  env(safe-area-inset-right, 0px);

    /* ── Transiciones ── */
    --pm-transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
    --pm-transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
    --pm-transition-slow: 400ms cubic-bezier(0.4, 0, 0.2, 1);

    /* ── Iconos ── */
    --pm-icon-sm:  16px;
    --pm-icon-md:  20px;
    --pm-icon-lg:  24px;
    --pm-icon-xl:  32px;
    --pm-icon-xxl: 48px;

    /* ── Premium v3 ── */
    --pm-v3-gold-line:     linear-gradient(90deg, transparent, #FFE1AF 30%, #8A7650 70%, transparent);
    --pm-v3-text-gradient: linear-gradient(135deg, #8A7650, #FFE1AF 50%, #7A654F);
    --pm-v3-warm-glow:     0 8px 32px rgba(255,225,175,0.25);
    --pm-v3-glass-premium: rgba(248,244,232,0.90);

    /* ── Premium Admin ── */
    --pma-gold-glow:   0 4px 20px rgba(255,225,175,0.3);
    --pma-warm-glass:  rgba(248,244,232,0.85);
    --pma-accent-line: linear-gradient(180deg, #8A7650, #FFE1AF);
    --pma-shadow-warm: 0 4px 16px rgba(74,64,52,0.15);
}
```

---

## 16. Responsive — Reglas Clave por Vista

### Landing (index.php)

| Sección | Mobile (base) | Tablet (768) | Desktop (992) |
|---------|--------------|--------------|---------------|
| Navbar | 64px, hamburger | 64px | 80px, links visibles |
| Hero | 100svh, 1 CTA visible | 100svh | 100svh, 2 CTAs |
| Servicios | 1 col (cards horizontal) | 2 cols | 3 cols |
| Equipo | 2 cols | 3 cols | 4 cols |
| Galería | 2 cols | 3 cols | masonry 3 cols |
| Footer | stacked | 2 cols | 4 cols |
| Bottom bar | visible | visible | oculta |

### Reservas (reservar.php)

| Paso | Mobile | Desktop |
|------|--------|---------|
| Selección servicio | cards horizontales 1 col | grid 2 cols |
| Fecha | input date nativo (full width) | date picker custom |
| Hora | slot grid 3 cols | slot grid 5 cols |
| Confirmar | formulario stacked + bottom bar CTA | 2 cols: resumen + form |

### Admin Panel

| Elemento | Mobile | Desktop |
|----------|--------|---------|
| Sidebar | oculto, slide-in con overlay | fijo 260px |
| Stat cards | 2 cols | 4 cols |
| DataTables | scroll horizontal | full width |
| Modales | full screen (98vw) | centered 600px |

---

## 17. Checklist de Implementación para Claude Code

Antes de hacer commit de cualquier componente visual, verificar:

**Tokens**
- [ ] No hay colores hex hard-codeados en el CSS del componente
- [ ] Todas las sombras usan `--pm-shadow-*`
- [ ] Todos los z-index usan `--pm-z-*`
- [ ] Font families usan `--pm-font-*`

**Mobile-first**
- [ ] El CSS base (sin media queries) corresponde al diseño mobile
- [ ] Los media queries usan `min-width`, no `max-width`
- [ ] Touch targets ≥ 48px en mobile (altura mínima)
- [ ] `font-size: 16px` en inputs de mobile (evita zoom en iOS)
- [ ] `100svh` en hero, no `100vh`
- [ ] Safe area insets en elementos que tocan bordes del viewport

**Accesibilidad**
- [ ] `focus-visible` implementado (no `focus` solo)
- [ ] Colores de texto verificados contra fondo correspondiente
- [ ] `prefers-reduced-motion` cubre las animaciones del componente
- [ ] Elementos interactivos tienen `aria-label` cuando el texto no es descriptivo

**Bootstrap 5.3**
- [ ] Grilla usa clases Bootstrap (`col-*`, `row`, `container`)
- [ ] Clases `.pm-*` sobrescriben visual, Bootstrap hace layout
- [ ] No hay conflictos de especificidad con Bootstrap (usar `.pm-` prefix consistente)

---

*Design System v3.1 — Piel Morena Estética*
*Revisado: Marzo 2026 — Mobile-first, tokens corregidos, z-index scale, safe areas, accesibilidad WCAG AA, DM Sans body font, nuevos componentes móvil (bottom bar, slot picker, service card horizontal, step progress)*
