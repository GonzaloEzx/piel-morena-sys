# Producto — Piel Morena Estética
> Documento vivo de referencia del producto digital.  
> Audiencia: agentes de IA, desarrolladores, colaboradores.  
> Última actualización: Marzo 2026 — **Inauguración: 6 de abril de 2026**

---

## 1. Visión del Producto

Plataforma web PHP para un salón de estética que resuelve tres problemas core:

1. **Agenda digital** — reemplaza el Excel de turnos por WhatsApp
2. **Caja y contabilidad básica** — reemplaza el Excel de ingresos diarios
3. **Presencia de marca** — landing profesional con galería, equipo, servicios y promos

El sistema debe ser fácil de operar por personal no técnico (Mari y su equipo), robusto para un negocio real con clientes, y extensible para incorporar funcionalidades futuras sin romper lo existente.

## 1.1 Estrategia documental aprobada

- `docs/negocio/README.md` pasa a ser la entrada de contexto del negocio real.
- `docs/producto/README.md` pasa a ser la entrada de contexto del producto digital.
- `docs/contracts/` sigue siendo la fuente de verdad funcional por modulo dentro de la capa de producto.
- La aprobacion de consolidar el panel admin ya se materializa en esta capa: `docs/contracts/04-panel-administracion.md` es la fuente funcional vigente del modulo y `docs/bloque_admin_panel.md` queda solo como redireccion temporal.
- La documentacion operativa del producto debe alinearse con los contratos y evitar duplicaciones innecesarias.

---

## 2. Stack Técnico

| Capa | Tecnología |
|---|---|
| Backend | PHP vanilla |
| Base de datos | MySQL (PDO) |
| Frontend | Bootstrap 5.3 + jQuery + SweetAlert2 |
| Admin UI | DataTables + Chart.js |
| CSS Premium | Capas `premium-v3.css` / `premium-auth.css` / `premium-admin.css` |
| Hosting | Hostinger shared hosting (cuenta del primo) |
| Deploy | SSH + Git pull desde PowerShell (ver `docs/deploy-ssh-git-setup.md`) |
| Repo | GitHub — `GonzaloEzx/piel-morena-sys` |
| Design System | `docs/design-system.md` — fuente de verdad visual |

---

## 3. Estructura del Sistema

```
piel-morena-sys/
├── index.php                  # Landing pública
├── reservar.php               # Flujo de reserva de citas
├── includes/                  # Auth, funciones, header, footer
├── api/                       # Endpoints REST (JSON)
│   ├── auth/                  # login, registro, logout
│   ├── citas/                 # crear, cancelar, disponibilidad
│   ├── servicios/             # listar, consulta_precio
│   └── admin/                 # estadísticas, CRUD admin
├── admin/                     # Panel de administración
│   ├── index.php              # Dashboard
│   └── views/                 # servicios, clientes, citas, empleados, productos, caja, etc.
├── config/                    # database.php (no en repo), config.php
├── database/                  # schema.sql, seed.sql, migrations/
├── assets/                    # css, js, img
└── docs/
    ├── negocio/              # contexto del negocio real
    ├── producto/             # vision y estrategia documental de producto
    ├── contracts/            # contratos funcionales por modulo
    └── ...                   # design system, runbooks y documentos operativos
```

---

## 4. Roles y Permisos

| Rol | Acceso |
|---|---|
| **Admin** (Mariángeles) | Acceso total: configuración, CRUD completo, caja, reportes, cancelar citas |
| **Staff / Empleada** | Ver y crear citas propias; ver clientes asignados; cancelar citas |
| **Cliente registrado** | Reservar citas (requiere cuenta); ver historial propio |

- Cada empleada tiene su propio acceso al sistema.
- Admin puede crear/editar empleadas y asignarles servicios y horarios.
- **Solo admin y staff pueden cancelar citas.** El cliente debe contactar al salón por teléfono.
- Staff NO puede acceder a caja ni configuración.
- No hay rol "invitado" — se requiere cuenta para reservar.

---

## 5. Módulos del Sistema

### 5.1 Landing Pública (`index.php`)

Secciones en orden (con anclas):

| # | Sección | Ancla | Estado |
|---|---|---|---|
| 1 | Navbar fijo con glassmorphism | — | ✅ Implementado |
| 2 | Hero Carousel (3 slides configurables) | `#hero` | ✅ Implementado — pendiente ajuste de contenido |
| 3 | Sobre Nosotros | `#nosotros` | ✅ Implementado — texto pendiente de Mari |
| 4 | Nuestros Servicios (dinámicos desde BD) | `#servicios` | ✅ Implementado — 1 servicio destacado por categoría |
| 5 | Nuestro Equipo | `#equipo` | ✅ Implementado — fotos pendientes |
| 6 | Info de Tratamientos (6 cards) | `#tratamientos` | ✅ Implementado — entre Servicios y Galería |
| 7 | Galería (administrable, 6 slots) | `#galeria` | ✅ Implementado — admin sube imágenes |
| 8 | Promociones | `#promos` | ✅ Implementado — pendiente admin configurable |
| 9 | Testimonios | `#testimonios` | ✅ Implementado — administrable desde admin |
| 10 | Reservá tu Cita (CTA) | `#reservar` | ✅ Implementado |
| 11 | Contacto | `#contacto` | ✅ Implementado |
| 12 | Footer | — | ✅ Implementado |

---

### 5.2 Módulo de Servicios

**Estructura implementada:**
- **Categorías** (tabla `categorias_servicios`): 9 categorías activas (Depilación, Trat. Faciales, Trat. Corporales, Trat. de Frío, Manicuría, Cejas y Pestañas, Peluquería, Masajes).
- **Servicios** (tabla `servicios`): cada tratamiento es un servicio individual con `id_categoria`. ~40+ servicios reales del negocio.
- Sin subcategorías — estructura plana de 1 nivel (categoría → servicio). Escalable y práctica.
- Todos los servicios se muestran en el sitio (ninguno oculto por ahora).
- El admin configura: servicios, duraciones, precios, empleada asignada.

**Sección "Info de Tratamientos" (`#tratamientos`):**
- Ubicación: entre `#servicios` y `#galeria` en el landing.
- 6 tratamientos estrella: Limpieza Facial, Depilación Láser, Criolipólisis, Dermapen, Radiofrecuencia, Masaje Descontracturante.
- Cada card: icono, descripción real, 3 beneficios, meta (duración + frecuencia), CTA de reserva.
- Contenido estático con información profesional real de cada tratamiento.

---

### 5.3 Módulo de Reservas (`reservar.php`)

**Flujo UI (wizard 4 pasos):**
1. Selección de categoría (accordion) → servicio específico dentro de la categoría
2. Selección de fecha (hoy + máx. 60 días)
3. Selección de horario disponible (grilla de slots)
4. Confirmación con resumen (servicio, fecha, hora, precio)

**Reglas de negocio:**
- **Requiere cuenta** — los usuarios no logueados ven aviso con CTA de registro/login.
- Sin pago/seña online.
- Slots generados según duración del servicio seleccionado.
- **Solo admin y staff pueden cancelar citas** — el cliente contacta por teléfono.
- Emails de confirmación se envían best-effort (no bloquean la reserva).
- Anticipación máxima: 60 días.
- Anti-solapamiento: verificación en backend antes de confirmar (responde 409 si ocupado).
- Soporte para jornadas específicas (ej: extensiones de pestañas solo miércoles).
- Staff puede registrar citas manualmente para clientes que no tienen cuenta (modal "Nueva Cita").

---

### 5.4 Panel de Administración (`admin/`)

#### Dashboard
- Estadísticas del día: citas, ingresos, clientes nuevos.
- Gráficos básicos (Chart.js).
- Acceso rápido a todos los módulos.

#### Gestión de Servicios
- CRUD completo: categorías, subcategorías, duración, precio, descripción.
- Asignación de empleada por servicio/subcategoría.
- Configuración de jornadas (días en que se ofrece el servicio).
- Activar/desactivar servicios.

#### Gestión de Citas
- Vista con filtros por fecha, estado, empleada.
- Cambiar estado: pendiente → confirmada → en proceso → completada / cancelada.
- Solo admin y staff cancelan citas.

#### Gestión de Clientes
- CRUD completo.
- Historial de citas por cliente.
- Importación futura desde Excel (cuando Mari envíe la base).

#### Gestión de Empleadas
- CRUD: nombre, especialidad, foto, horarios, servicios asignados.
- Configuración de horario por empleada (flexible, no fijo global).
- Toggle activo/inactivo.

#### Gestión de Productos
- CRUD: nombre, precio, stock.
- Alerta de stock bajo.
- No se muestra en el sitio público de momento.

#### Caja
- Registro de movimientos del día (entradas/salidas).
- Resumen diario de ingresos.
- Sin integración de pago online — registro manual.

#### Galería (Admin) — ✅ Implementado
- Vista admin: `admin/views/galeria.php` con 6 slots visuales.
- API: `api/admin/galeria.php` (GET listar slots, POST subir a slot 1-6).
- Subida de hasta 6 imágenes. Formato horizontal recomendado.
- Nomenclatura fija: `galeria-01.jpg` a `galeria-06.jpg`.
- Al subir una imagen nueva **pisa** la anterior del mismo slot (sin acumulación de archivos).
- Las imágenes se alojan en `assets/img/gallery/`.
- Optimización automática: conversión a JPG, resize a máx 1400px, calidad 85%.
- Landing muestra imágenes reales con fallback a gradientes si el slot está vacío.

#### Testimonios (Admin)
- CRUD de testimonios: nombre, texto, rol y orden.
- Eliminar borra definitivamente.
- La landing renderiza el carrusel desde la tabla `testimonios`.
- Lo que se muestra hoy en el sitio queda como base; se vuelve editable.

#### Promociones (Admin)
- CRUD de promos: título, descripción, descuento, fecha de vencimiento, imagen.
- Tipos: descuento por medio de pago (ej: 15% efectivo), packs de sesiones, estacionales.
- Las promos vencidas se ocultan automáticamente del sitio.
- Comparte gestión con packs de servicios.

#### Hero (Admin)
- Configuración de los 3 slides del carrusel principal.
- Campos por slide: imagen de fondo, título, subtítulo, texto de CTA, link del CTA.

#### Configuración General
- Datos del negocio: nombre, dirección, teléfono, email, redes sociales.
- Horarios de atención generales.
- Intervalo de citas (duración de slots).
- Días laborales.

---

### 5.5 Módulo de Auth

- Login / Registro / Logout con SweetAlert2 modales.
- Roles: `admin`, `empleado`, `cliente`.
- Sesiones PHP con regeneración de ID en login.
- Rutas protegidas con `requerir_auth()` / `requerir_rol()`.
- Ver detalle completo: `docs/bloque_auth_login.md`.

---

### 5.6 Equipo — Hover con Foto

- En la sección `#equipo` del sitio público: al hacer hover sobre la card de una empleada, se muestra su foto.
- Mientras no haya fotos reales: mostrar iniciales (comportamiento actual).
- Transición suave entre iniciales → foto al cargar la imagen.

---

## 6. Funcionalidades Pendientes / Backlog

### MVP (antes del 6 de abril) — ✅ Completado
- [x] Reserva de citas operativa end-to-end (requiere cuenta)
- [x] Panel admin: citas, caja, servicios, clientes, empleados, productos
- [x] Galería administrable (6 slots, upload desde admin)
- [x] Servicios reales con 9 categorías (~40+ servicios de `docs/negocio/README.md`)
- [x] Sección "Info de Tratamientos" (6 tratamientos estrella)
- [x] Flujo de reserva agrupado por categoría (accordion)
- [x] Servicios dinámicos en landing (desde BD)
- [x] Cancelación restringida a staff/admin
- [x] Config general del negocio desde admin

### Post-lanzamiento (próximas semanas)
- [x] Testimonios administrables desde admin
- [ ] Promociones administrables desde admin
- [ ] Hero configurable desde admin
- [ ] Gestión de jornadas específicas por servicio (depilación láser, extensiones miércoles)
- [ ] Hover con foto en sección Equipo
- [ ] Foto de equipo reales (pendiente de Mari)
- [ ] Precios reales en todos los servicios (pendiente de Mari)
- [ ] Planilla de consentimiento digital (análisis pendiente)

### Futuro / A investigar
- [ ] Envío de mensajes WhatsApp a clientes (API WhatsApp Business)
- [ ] Notificaciones automáticas al admin por nueva reserva
- [ ] Confirmación de turno por email al cliente
- [ ] Importación de base de clientes desde Excel
- [ ] Personalización en la experiencia del cliente (saludos, historial, cumpleaños)
- [ ] Gestión de consentimientos firmados digitalmente

---

## 7. Decisiones de Diseño y UX

- **Cancelación de citas:** solo admin y staff pueden cancelar vía API. El cliente contacta por teléfono (3624 254052).
- **Anticipación máxima de reserva:** 60 días.
- **Requiere cuenta:** reserva online solo para usuarios registrados. Staff registra clientes sin cuenta desde el panel.
- **Sin pago online:** reserva sin seña.
- **Galería:** máximo 6 imágenes administrables desde panel admin. Reemplazo por slot (sin historial de archivos).
- **Servicios:** estructura plana (categoría → servicio), sin subcategorías. ~40+ servicios reales en 9 categorías.
- **Imágenes:** formato horizontal (Instagram post/reel), subidas por Mari.
- **Emails transaccionales:** confirmación de cita y cancelación se envían best-effort. Cron de recordatorios activo.
- **Notificaciones push/WhatsApp:** investigación pendiente para fase posterior.
- **Personalización del trato:** a investigar cómo reflejarlo en UX.

---

## 8. Convención de Commits

```
feat:     nueva funcionalidad
fix:      corrección de bug
style:    cambios visuales sin lógica
docs:     cambios en documentación
refactor: reorganización sin cambio funcional
deploy:   ajustes específicos de producción
```

---

## 9. Referencias Internas

| Documento | Contenido |
|---|---|
| `docs/negocio/README.md` | Contexto del negocio real (este repo) |
| `docs/design-system.md` | Sistema visual completo |
| `docs/bloque_auth_login.md` | Flujo de autenticación |
| `docs/bloque_reservas.md` | Lógica de reservas y disponibilidad |
| `docs/contracts/04-panel-administracion.md` | Contrato funcional del panel admin |
| `docs/bloque_citas.md` | Contexto y decisiones del módulo de citas |
| `docs/deploy-ssh-git-setup.md` | Setup SSH + Git deploy |
| `docs/deploy_runbook_hostinger.md` | Runbook de deploy en Hostinger |
