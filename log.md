# Registro de trabajo — Piel Morena

## 2026-10-31T20:30:13Z — Presentación v2.1

- Gonzalo transmite autorización de Mari para rediseñar y publicar en `/test`, con contenido de ejemplo identificado.
- Portada y catálogo nuevos, categorías y formulario GET sin JS, búsqueda progresiva, mensajes de WhatsApp centralizados, primera visita y contacto.
- Demo separada de los datos operativos; precios ARS, fotos y experiencias de referencia etiquetados, banner y noindex.
- 112 comprobaciones locales OK. Taller: 26 pasan / 22 omitidas, sin fallas; PHP/JS y diff revisados. Se conserva el panel y el trabajo ajeno (`.gitattributes`, relevo de Claude).
- Material pendiente y autorización editorial definitiva en `docs/producto/mvp-v2.md`. Publicación autorizada solo en `/test`; verificación remota pendiente al preparar este commit.

## 2026-10-31T20:34:30Z — Publicación y verificación de v2.1

- `3770a3e` subido en `v2-agenda` y desplegado con fast-forward en `public_html/test`.
- 112 comprobaciones remotas OK; taller remoto: 26 pasan / 22 omitidas / 0 fallan. PageSpeed sin clave, LCP sin nueva medición.
- Capturas: `ss-taller/reports/piel-morena-v2-test/capturas/`; comparativa y puntajes en el plan v2. Raíz comprobada en `0c75b1d`, sin cambios; sin escrituras en la base.
- Número de WhatsApp confirmado por Gonzalo en el relevo de Claude. Claude sigue en pausa hasta la señal de Gonzalo; conserva su bloque de profesionales.

## 2026-10-31T21:37:28Z — v2.04, refinamiento móvil

- Gonzalo y Mari aprueban la dirección visual; Gonzalo pide una fase de estilo más precisa para móvil con el nombre v2.04.
- Logo integrado sin recuadro blanco, cabecera sticky compacta, tarjetas horizontales, listas con detalle progresivo y catálogo antes del asesoramiento.
- Barra inferior contextual, controles de 48 px y menú accesible en horizontal. Se conservan WhatsApp, formulario GET sin JS, búsqueda y avisos de ejemplo.
- 194 comprobaciones locales OK en 320/360/390/430 px, tablet, escritorio, WebKit y sin JS. Taller: 26 pasan / 22 omitidas / 0 fallan; PHP/JS y diff revisados.
- Capturas antes/después en ss-taller/reports/piel-morena-v204/. Publicación en /test autorizada y pendiente al preparar este commit; raíz y panel fuera del bloque.

## 2026-10-31T21:41:16Z — v2.04 publicada y verificada

- `cccd619` subido en v2-agenda y publicado mediante fast-forward en /test. Raíz comprobada en `0c75b1d`, sin cambios.
- 194 comprobaciones remotas OK; taller remoto: 26 pasan / 22 omitidas / 0 fallan. Incluye menú horizontal, áreas táctiles, no JS y WebKit. PageSpeed sin clave, LCP sin nueva medición.
- A 390 × 844 px, inicio de 9.430 a 6.498 px y catálogo de 1.175 a 396 px; logo integrado y barra inferior contextual. Comparativa en ss-taller/reports/piel-morena-v204/index.html.
- Material definitivo de Mari pendiente según el plan. Panel de profesionales y trabajo ajeno preservados.
