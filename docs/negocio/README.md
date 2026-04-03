# Negocio — Piel Morena Estética
> Estado: vigente
> Audiencia: negocio, producto, desarrollo, agentes, onboarding
> Fuente de verdad: si, para contexto del negocio real
> Relacion: marco comercial y operativo del salon
> Ultima revision: 2026-04-03

---

## 1. Identidad del Negocio

| Campo | Valor |
|---|---|
| **Nombre** | Piel Morena Estética |
| **Trayectoria** | 5 años de actividad |
| **Inauguración del sitio** | 6 de abril de 2026 |
| **Rubro** | Salón de belleza, depilación, tratamientos estéticos faciales y corporales |
| **Dirección** | Vedia 459, Resistencia, Chaco |
| **Teléfono / WhatsApp** | 3624 254052 |
| **Email de contacto** | zudaire83@gmail.com |
| **Instagram** | [@pielmorenaesteticaok](https://instagram.com/pielmorenaesteticaok) |
| **Dominio sugerido** | pielmorenaestetica.com.ar *(libre, pendiente de reservar)* |

---

## 2. Descripción del Negocio

Piel Morena es un espacio de belleza con 5 años de trayectoria en Resistencia, Chaco. Combina tratamientos estéticos profesionales —faciales, corporales, depilación láser, peluquería, manicuria y más— con un trato cercano y personalizado. Mariángeles Zudaire, su fundadora y administradora, es reconocida por la calidez en la atención y el vínculo genuino con sus clientas.

El negocio opera actualmente gestionando turnos por WhatsApp e Instagram y llevando la contabilidad en planillas Excel. El sistema viene a profesionalizar y sistematizar esa operación sin perder la identidad cálida que lo caracteriza.

> **Sección "Sobre Nosotros"**: Mariángeles redacta el texto definitivo. Por ahora se mantiene el contenido actual del sitio.

---

## 3. Identidad Visual

| Elemento | Detalle |
|---|---|
| **Logo** | `assets/img/piel-morena-instagram-logo.jpg` *(JPG actual, pendiente versión PNG/SVG con fondo transparente)* |
| **Design System** | `docs/diseno/design-system.md` — documento de referencia completo |
| **Paleta principal** | "Oliva & Arcilla": Bronce `#8A7650`, Tierra `#7A654F`, Arena `#DBCEA5`, Crema `#ECE7D1`, Marfil `#F8F4E8` |
| **Tipografía web** | Playfair Display (headings) + Lato (cuerpo) — Google Fonts |
| **Referencia visual** | Sin referencias externas; el design-system es la fuente de verdad |

---

## 4. Horarios de Atención

| Día | Horario |
|---|---|
| Lunes a Viernes | 08:00 a 20:00 |
| Feriados | Sí atienden |
| Sábado / Domingo | No especificado *(pendiente confirmar con Mari)* |

**Notas operativas:**
- Algunos servicios tienen jornadas específicas (ej: extensiones de pestañas solo los miércoles).
- Depilación definitiva opera en jornadas determinadas (días específicos a confirmar y configurar).
- Los horarios por empleada son variables y el admin los configura en el sistema.

---

## 5. Equipo

| Nombre | Rol / Especialidad |
|---|---|
| Mariángeles Zudaire | Dermatocosmesta & Operadora Láser — **Administradora del sistema** |
| Rosario Prieto | Especialista certificada en Manicuria |
| Lucía Soto | Lashista & Cosmetología |
| Nathalia Gómez | Peluquera |
| Nuria Morinigo | Masajista |
| Naila Centurión | Certificada en Pestañas |

**Horarios por empleada:**
- Naila Centurión: solo miércoles, 08:00 a 20:00
- Nuria Morinigo: horario variable *(a confirmar)*
- Resto del equipo: 08:00 a 20:00

**Fotos del equipo:** Mariángeles las envía. Por ahora el sitio muestra iniciales.

---

## 6. Catálogo de Servicios

Los servicios están organizados en **categorías** (9 en total). Cada tratamiento individual es un **servicio** dentro de su categoría, con su propia duración, precio y profesional asignada. No hay subcategorías — estructura plana de 1 nivel. El admin configura duraciones, precios y asignación de profesional desde el panel.

> **Nota técnica:** en el sistema, "subcategoría" del catálogo original = servicio individual con `id_categoria`. Ejemplo: "Limpieza facial profunda" es un servicio de la categoría "Tratamientos Faciales".

### 6.1 Depilación Definitiva
- Depilación Láser Soprano (única tecnología usada)
- **Duración:** 30 min aprox.
- **Jornadas:** opera en días específicos de la semana *(a configurar por admin, pendiente definir)*

### 6.2 Tratamientos Faciales
**Duración general:** 1h 30m

Subcategorías:
- Limpieza facial profunda
- Punta de diamante
- Dermaplaning
- Limpieza facial anti-age
- Limpieza facial control acné
- Limpieza facial despigmentante
- Limpieza facial hidratante
- Radiofrecuencia facial
- Peeling químico
- Peeling enzimático
- Dermapen

### 6.3 Tratamientos Corporales

Subcategorías:
- Criolipólisis en frío — 1h
- Radiofrecuencia corporal — 30 min
- Vela Slim (celulitis) — 1h
- Electrodos — 30 min

### 6.4 Manicuria
**Duración general:** aprox. 2h

Subcategorías:
- Kapping
- Soft Gel
- Semipermanente

> Precio base de referencia: $20. Con diseño se arregla en la cita.  
> Detalle completo de servicios: Rosario Prieto los enviará.

### 6.5 Belleza de Cejas y Pestañas
**Duración general:** 1h

Subcategorías:
- Lifting de pestañas
- Tinte de cejas
- Perfilado de cejas
- Laminado de cejas

### 6.6 Masajes
- Cuerpo completo — 1h
- Zona específica — 30 min

Subcategorías:
- Relajante
- Descontracturante

### 6.7 Peluquería
**Duración:** 1h a 3h

Subcategorías:
- Corte de puntas
- Células madres
- Nanoplastia
- Alisado *(rango de precios $40–$45 según largo del cabello — 3h)*
- Shock de keratina
- Botox capilar
- Máscaras reparadoras
- Máscaras matizadoras
- Nutrición capilar

### 6.8 Extensiones de Pestañas
**Duración:** 1h 30 min  
**Jornada exclusiva:** solo los miércoles

> Info detallada de subcategorías: Naila Centurión la envía.

---

## 7. Precios

- **Estado actual:** pendiente. Mariángeles los envía.
- **Criterio:** variable por subcategoría y, en algunos casos, por características del cliente (ej: largo de cabello en peluquería).
- El sistema debe permitir configurar precios por servicio/subcategoría desde el panel admin.
- El precio no se muestra directamente en el sitio en todos los casos; hay un tooltip de consulta de precio ya implementado.

---

## 8. Productos

- Se venden productos en el local en forma esporádica; stock frecuentemente bajo.
- **En el sitio:** manejo interno (panel admin), no se muestra catálogo público de momento.
- **Datos actuales:** se usan ejemplos/datos de prueba hasta que Mari envíe el listado real.

---

## 9. Promociones

- Tipos: descuento por medio de pago (ej: 15% en efectivo), packs de sesiones, promos estacionales (verano, primavera, fechas especiales).
- Las promos **sí se muestran en el sitio**, en la sección dedicada.
- Cada promo puede tener fecha de vencimiento visible.
- Los packs también se muestran en la sección de servicios.
- Todo se administra desde el panel admin.

---

## 10. Clientes

- **Base existente:** Mari tiene clientes en Excel. Los envía más adelante.
- **Mientras tanto:** se usan datos de ejemplo ya existentes en el sistema.
- **Arranque real:** se importa/carga desde cero cuando lleguen los datos reales.
- El negocio tiene una **planilla de consentimiento** que el cliente firma antes de ciertos tratamientos (responsabilidad sobre salud). A futuro puede contemplarse en el sistema.

---

## 11. Cómo Opera el Negocio Hoy (Puntos de Dolor)

| Problema | Impacto |
|---|---|
| Turnos por WhatsApp e Instagram | No sistematizados; Mari los anota manualmente en Excel |
| Contabilidad en planillas | Difícil llevar ingresos del día, movimientos de caja |
| Sin confirmación de turno automática | Riesgo de olvido o solapamiento |
| Sin base de clientes organizada | Pérdida de historial y seguimiento |
| Sin galería administrable | Depende de actualizar archivos manualmente |
| Sin gestión de promociones | Comunicación informal por redes |

---

## 12. Medios de Pago Aceptados

- Efectivo
- Transferencia bancaria
- Billeteras virtuales (Mercado Pago, etc.)

> Sin cobro de seña online por reserva — al menos por ahora.

---

## 13. Comunicación con Clientes

- Canal actual: WhatsApp e Instagram
- **Futuro deseado:** notificaciones WhatsApp a clientes (a investigar e implementar más adelante)
- Sin confirmaciones por email por ahora
- Sin notificaciones automáticas al admin por ahora

---

## 14. Pendientes de Mari

- [ ] Fotos del equipo (por ahora: iniciales)
- [ ] Texto de "Sobre Nosotros"
- [ ] Precios de todos los servicios
- [ ] Detalle completo de Manicuria (Rosario)
- [ ] Detalle completo de Extensiones de Pestañas (Naila)
- [ ] Base de clientes en Excel
- [ ] Horarios de Nuria Morinigo
- [ ] Días de jornada de Depilación Láser
- [ ] Confirmación horarios sábado/domingo
