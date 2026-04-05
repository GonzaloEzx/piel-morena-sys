# Revisión Legal Rápida del Sitio

> Fecha: abril 2026
> Alcance: sitio público y flujo de reservas online de Piel Morena
> Ubicación del negocio: Resistencia, Chaco, Argentina

---

## Conclusión corta

No aparece nada claramente delictivo en el sitio.

Lo que sí aparece es **riesgo de incumplimiento administrativo o regulatorio** en temas de:

- protección de datos personales
- información al consumidor
- contratación o reserva a distancia

En otras palabras:

- no parece un problema penal
- pero tampoco conviene decir que el sitio está "cerrado legalmente"

---

## Qué está bien hoy

El sitio ya muestra información básica del negocio:

- dirección
- teléfono
- email

Eso hoy se ve en:

- [includes/footer.php](C:\Users\ggest\OneDrive\Escritorio\proyectos\piel-morena-sys\includes\footer.php#L61)
- [includes/header.php](C:\Users\ggest\OneDrive\Escritorio\proyectos\piel-morena-sys\includes\header.php#L49)

Además:

- el flujo de reserva es claro para la usuaria
- el negocio está identificado visualmente
- hay canales visibles de contacto

---

## Qué falta o genera riesgo

### 1. Política de privacidad

No aparece una política de privacidad ni un aviso claro sobre tratamiento de datos personales.

Eso es sensible porque el sistema procesa:

- nombre
- email
- teléfono
- historial o datos de citas

También debería informarse:

- quién es el responsable de la base de datos
- para qué se usan los datos
- cómo pedir acceso, rectificación o supresión

### 2. Términos y condiciones de reserva

No encontré una página clara con reglas del servicio o de la reserva.

Conviene fijar por escrito:

- cómo funciona la reserva
- política de cancelación
- tolerancia de demora
- reprogramaciones
- medios de contacto

### 3. Identificación comercial más completa

Hoy se muestra contacto, pero para un sitio que toma reservas online conviene publicar también:

- nombre completo o razón social
- CUIT
- domicilio comercial o legal si corresponde

### 4. Derecho de arrepentimiento / baja

Si la reserva online entra dentro del marco de contratación a distancia, hay que revisar seriamente si corresponde implementar:

- botón de arrepentimiento
- botón de baja

Esto se volvió más exigente con normativa reciente.

### 5. Inconsistencia funcional entre front y API

La web pública pide cuenta para reservar, pero la API todavía admite reservas como invitada.

Eso hoy se ve en:

- [reservar.php](C:\Users\ggest\OneDrive\Escritorio\proyectos\piel-morena-sys\reservar.php#L29)
- [api/citas/crear.php](C:\Users\ggest\OneDrive\Escritorio\proyectos\piel-morena-sys\api\citas\crear.php#L61)

No es ilegal por sí solo, pero sí complica:

- consentimiento
- trazabilidad
- coherencia del flujo
- gestión de cancelación o baja

---

## Lectura práctica y honesta

### Si el sitio fuera solo vidriera

Si el sitio fuese únicamente:

- información del negocio
- catálogo
- contacto

entonces el riesgo legal sería bastante menor.

### Como hoy hay reservas online

Al existir un flujo de reserva, aunque sea simple, ya conviene tratarlo con estándar más serio de cumplimiento.

Mi lectura es:

- no diría que "está fuera de la ley" de forma tajante
- sí diría que **faltan piezas importantes de compliance**

---

## Prioridad recomendada

### Alta

1. Crear **Política de Privacidad**
2. Crear **Términos de Reserva / Cancelación**
3. Publicar **nombre completo del proveedor y CUIT**

### Media

4. Revisar si corresponde implementar **botón de arrepentimiento**
5. Revisar si corresponde implementar **botón de baja**
6. Unificar el flujo real de reservas para que coincida con lo que el sitio promete

---

## Fuentes oficiales a revisar

- [Ley 25.326 de Protección de Datos Personales](https://www.argentina.gob.ar/sites/default/files/arg_ley25326.pdf)
- [Resolución 104/2005 sobre información al consumidor en Internet](https://www.argentina.gob.ar/normativa/nacional/resoluci%C3%B3n-104-2005-107456/texto)
- [Disposición 954/2025 sobre botón de arrepentimiento y baja](https://www.argentina.gob.ar/normativa/nacional/disposici%C3%B3n-954-2025-417152/texto)
- [Derecho Fácil: contratos de consumo](https://www.argentina.gob.ar/justicia/derechofacil/leysimple/contratos-de-consumo)

---

## Criterio final

El sitio hoy parece **operable**, pero no todavía **legalmente prolijo**.

La buena noticia es que el problema no parece estar en una práctica gravemente ilícita, sino en faltantes típicos de:

- textos legales
- información obligatoria
- consistencia del flujo de reservas

Eso se puede corregir sin rehacer el producto.
