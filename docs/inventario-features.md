# Inventario Completo de Features — Piel Morena

> Documento generado el 2026-03-24. Refleja el estado real del codigo fuente del proyecto.

---

## 1. Resumen General

**Piel Morena** es un sistema web integral para la gestion de un salon de belleza, depilacion, tratamientos de frio y estetica. El sistema consta de:

- **Landing page publica** (one-page con 10 secciones)
- **Sistema de autenticacion de clientes** (login manual, Google OAuth, verificacion email, recuperacion de contrasena)
- **Reservas online** (wizard de 4 pasos con verificacion de disponibilidad en tiempo real)
- **Panel de cuenta del cliente** (perfil, citas, cambio de contrasena)
- **Panel de administracion** (dashboard, CRUD completo de 7 modulos, reportes, configuracion)
- **Panel de empleado** (citas asignadas, horario, servicios)
- **API REST** (39 endpoints organizados en 8 modulos)
- **Sistema de emails** (6 templates HTML, cron de recordatorios)
- **Base de datos** (15 tablas en MySQL/MariaDB)

**Stack:** PHP 8+ (procedural), MySQL 8+ / MariaDB, Bootstrap 5.3, jQuery 3.7+, DataTables, Chart.js 4+, SweetAlert2, FullCalendar 6.

**Produccion:** https://pielmorenaestetica.com.ar (Hostinger shared hosting)

---

## 2. Paginas Publicas

### 2.1 Landing Page (`index.php`)

Pagina one-page con scroll suave y 10 secciones:

| # | Seccion | ID HTML | Descripcion |
|---|---------|---------|-------------|
| 1 | **Hero / Banner Principal** | `#hero` | Carousel de 3 slides con autoplay (7s), fade transition, navegacion con flechas e indicadores. Slides: Identidad, Depilacion Definitiva, Tratamientos de Frio. CTAs a reservar y ver servicios. |
| 2 | **Sobre Nosotros** | `#nosotros` | Historia del salon, mision, features con iconos check (+10 anos experiencia, profesionales certificadas, tecnologia de punta, ambiente calido). |
| 3 | **Nuestros Servicios** | `#servicios` | Grid de 6 service cards (3 cols desktop, 2 tablet, 1 mobile). Cada card tiene: gradiente placeholder, badge de categoria, tooltip de precio ($) que registra consulta via AJAX, duracion, boton "Reservar Cita". Servicios mostrados: Depilacion Laser, Limpieza Facial, Masaje Relajante, Crioterapia Facial, Maquillaje Social, Manicure Spa. |
| 4 | **Nuestro Equipo** | `#equipo` | Grid de 4 miembros del equipo (cards con foto placeholder, nombre, rol, link a Instagram). |
| 5 | **Galeria** | `#galeria` | Grid masonry-style de 6 items con overlay de zoom, alturas variadas. |
| 6 | **Promociones** | `#promos` | Carousel de 3 promociones con autoplay (6s). Cada promo: imagen, badge "OFERTA", porcentaje de descuento, descripcion, CTA. Promos: Pack Depilacion (-30%), Crioterapia Combo (-25%), Dia de Novia (-20%). |
| 7 | **Testimonios** | `#testimonios` | Carousel dinámico con autoplay (8s). Renderiza testimonios desde BD ordenados por `orden`. Cada slide mantiene cita, 5 estrellas, avatar por iniciales, nombre y rol. |
| 8 | **Reserva tu Cita** (CTA) | `#reservar` | Seccion CTA con gradiente. Botones: "Reservar Ahora" (a reservar.php) y "WhatsApp". |
| 9 | **Contacto** | `#contacto` | Formulario de contacto (nombre, email, telefono, mensaje) con envio AJAX a `api/contacto.php`. Info: direccion, telefono, email, horarios. Redes sociales (Instagram, Facebook, WhatsApp). |
| 10 | **Footer** | footer | 4 columnas: info del negocio, enlaces rapidos, servicios, redes sociales. Copyright. |

**Features tecnicas de la landing:**
- Navbar responsiva con offcanvas mobile
- Scroll suave entre secciones
- Animaciones CSS (clase `pm-animate`)
- SEO: meta tags, Open Graph, Schema.org (BeautySalon)
- Google Fonts (Playfair Display, DM Sans, Poppins)
- Bootstrap Icons
- Estado de autenticacion visible en navbar (botones login/registro o nombre de usuario + logout)

### 2.2 Login (`login.php`)

- Formulario de inicio de sesion con email y contrasena
- Boton "Continuar con Google" (Google OAuth via GIS)
- Toggle de visibilidad de contrasena
- Link a "Olvide mi contrasena" (flujo de recuperacion)
- Link a registro
- Redireccion post-login configurable via query param `?redirect=`
- Si el usuario ya esta logueado, redirige a la home
- Validacion client-side y server-side

### 2.3 Registro (`registro.php`)

- Formulario completo: nombre, apellidos, email, telefono, contrasena, confirmar contrasena
- Boton "Registrarse con Google" (Google OAuth)
- Toggle de visibilidad de contrasena
- Verificacion de email con codigo de 6 digitos (post-registro)
- Link a login
- Redireccion post-registro configurable via query param `?redirect=`
- Si el usuario ya esta logueado, redirige a la home

### 2.4 Mi Cuenta (`mi-cuenta.php`)

**Requiere autenticacion** (redirige a login si no esta logueado).

- **Datos personales** (columna izquierda):
  - Formulario editable: nombre, apellidos, telefono
  - Email visible pero no editable
  - Guardado via AJAX a `api/clientes/actualizar-perfil.php`
  - Confirmacion con SweetAlert2

- **Cambiar contrasena** (columna izquierda):
  - Contrasena actual (omitida si registro fue solo via Google)
  - Nueva contrasena + confirmar
  - Toggle de visibilidad
  - Validacion: minimo 6 caracteres, coincidencia
  - Envio via AJAX a `api/auth/cambiar-password.php`

- **Proximas citas** (columna derecha):
  - Lista de citas pendientes/confirmadas futuras
  - Cada cita muestra: fecha (dia/mes), servicio, horario, estado (badge)
  - Si no hay citas, boton "Reservar cita"

- **Historial de citas** (columna derecha):
  - Lista de citas pasadas/completadas/canceladas
  - Cada cita muestra: fecha, servicio, horario, precio, estado
  - Maximo 20 citas mostradas

### 2.5 Reservar Cita (`reservar.php`)

**Requiere autenticacion.** Si no esta logueado muestra aviso informativo con beneficios y botones a registro/login (con redirect de vuelta).

**Wizard de 4 pasos:**

| Paso | Contenido |
|------|-----------|
| **1. Servicio** | Lista de servicios activos cargados via AJAX (`api/servicios/listar.php`). Cada opcion muestra: icono, nombre, duracion, categoria, precio. Servicio preseleccionable via query param `?servicio=X`. |
| **2. Fecha** | Input date con minimo = hoy, maximo = +60 dias. Valor default = manana. |
| **3. Hora** | Turnos disponibles cargados via AJAX (`api/citas/disponibilidad.php`). Grid de botones con horarios. Muestra mensaje si no hay turnos o si el dia no es laboral. |
| **4. Confirmar** | Resumen: servicio, fecha (formato largo en espanol), horario, precio. Boton "Confirmar Reserva" que envia POST a `api/citas/crear.php`. |
| **Exito** | Pantalla de confirmacion con icono check, resumen de la reserva, token de referencia. Botones: "Ver mis citas" (mi-cuenta.php) y "Volver al inicio". |

**Features tecnicas:**
- Progress bar visual con 4 pasos
- Fechas formateadas en espanol argentino
- Spinner de carga en cada paso
- Disponibilidad en tiempo real segun horario del negocio, dias laborales e intervalo configurado
- Deteccion de conflictos con citas existentes

---

## 3. Panel de Administracion

Acceso: `admin/index.php` y `admin/views/*.php`

**Layout:** Sidebar lateral colapsable + area de contenido principal.
- Sidebar con: logo, badge Admin/Staff, navegacion con iconos
- Responsivo: sidebar se oculta en mobile
- Solo accesible para roles `admin` y `empleado`

### 3.1 Dashboard (`admin/index.php`)

**Acceso:** Solo admin.

- **4 Stat Cards:** Citas Hoy, Total Clientes, Ingresos del Mes, Servicios Activos
- **Grafico de barras:** Top 5 servicios mas consultados (precio) - ultimos 30 dias (Chart.js)
- **Lista:** Proximas 5 citas (cliente, servicio, hora, fecha)
- **Grafico doughnut:** Citas por estado (pendiente, confirmada, en_proceso, completada, cancelada) - ultimos 30 dias
- **Accesos rapidos:** 6 botones a: Citas, Servicios, Clientes, Caja, Mensajes (con badge de no leidos), Configuracion
- Datos cargados via AJAX desde `api/admin/estadisticas.php`

### 3.2 Servicios (`admin/views/servicios.php`)

**Acceso:** Solo admin.

- **DataTable** con columnas: ID, Servicio, Categoria, Precio, Duracion, Estado, Acciones
- **Crear servicio:** Modal con campos: nombre, categoria (select cargado via AJAX), descripcion, precio, duracion (min), imagen (upload)
- **Editar servicio:** Mismo modal pre-llenado
- **Eliminar servicio:** Soft-delete (activo=0) con confirmacion SweetAlert2
- **Upload de imagenes:** Endpoint dedicado `api/admin/upload.php` (validacion MIME, extension, tamano)
- API: `api/admin/servicios.php` (GET, POST, PUT, DELETE)

### 3.3 Clientes (`admin/views/clientes.php`)

**Acceso:** Solo admin.

- **DataTable** con columnas: ID, Nombre, Email, Metodo (manual/Google), Email verificado, Ultimo acceso, Telefono, Total Citas, Estado, Acciones
- **Crear cliente:** Modal con campos: nombre, apellidos, email, telefono, contrasena
- **Editar cliente:** Mismo modal pre-llenado
- **Toggle activo/inactivo:** PATCH para desactivar/activar cuenta
- API: `api/admin/clientes.php` (GET, POST, PUT, PATCH)

### 3.4 Gestion de Citas (`admin/views/citas.php`)

**Acceso:** Admin y empleados.

- **Filtros:** Fecha, Estado (todos/pendiente/confirmada/en_proceso/completada/cancelada)
- **Dos vistas:**
  - **Vista Tabla:** DataTable con columnas: #, Fecha, Hora, Cliente, Servicio, Empleado, Estado, Acciones
  - **Vista Calendario:** FullCalendar 6 con citas como eventos (coloreados por estado)
- **Crear cita manual:** Modal con: buscador de clientes (autocomplete), seleccion de servicio, seleccion de empleado (filtrado por servicio), fecha, hora, notas
- **Cambio de estado:** Dropdown en cada fila con los estados posibles (flujo: pendiente -> confirmada -> en_proceso -> completada)
- API: `api/admin/citas.php` (GET con multiples query params, POST, PATCH)

### 3.5 Empleados (`admin/views/empleados.php`)

**Acceso:** Solo admin.

- **DataTable** con columnas: ID, Nombre, Email, Telefono, Estado, Acciones
- **Crear empleado:** Modal con campos: nombre, apellidos, email, telefono, contrasena (crea usuario con rol='empleado')
- **Editar empleado:** Mismo modal pre-llenado
- **Toggle activo/inactivo**
- API: `api/admin/empleados.php` (GET, POST, PUT, PATCH, DELETE)

### 3.6 Productos (`admin/views/productos.php`)

**Acceso:** Solo admin.

- **DataTable** con columnas: ID, Producto, Precio, Stock, Estado, Acciones
- **Crear producto:** Modal con campos: nombre, descripcion, precio, stock, stock minimo, imagen (upload)
- **Editar producto:** Mismo modal pre-llenado
- **Alertas de stock bajo** (stock < stock_minimo)
- **Soft-delete** (activo=0)
- API: `api/admin/productos.php` (GET, POST, PUT, DELETE)

### 3.7 Control de Caja (`admin/views/caja.php`)

**Acceso:** Solo admin.

- **4 Stat Cards:** Entradas Hoy, Salidas Hoy, Saldo Hoy
- **4 Acciones rapidas:**
  - Registrar Entrada (modal)
  - Registrar Salida (modal)
  - Venta de Producto (modal con seleccion de producto, cantidad, metodo de pago; decrementa stock)
  - Cerrar Caja (modal con resumen del dia y notas)
- **DataTable de movimientos** con filtros: fecha desde, fecha hasta. Columnas: ID, Tipo (entrada/salida), Concepto, Monto, Metodo de pago, Fecha, Hora
- **Filtro por rango de fechas** y boton "Hoy"
- API: `api/admin/caja.php` (GET, POST)

### 3.8 Mensajes de Contacto (`admin/views/mensajes.php`)

**Acceso:** Solo admin.

- **DataTable** con columnas: ID, Nombre, Email (con mailto:), Mensaje (truncado 60 chars con tooltip), Fecha, Estado (Nuevo/Leido), Acciones
- **Ver mensaje:** Modal con contenido completo + marcar como leido automaticamente
- **Eliminar mensaje:** DELETE real (no soft-delete)
- **Badge en sidebar:** Contador de mensajes no leidos
- API: `api/admin/mensajes.php` (GET, PATCH, DELETE)

### 3.9 Reportes y Analytics (`admin/views/reportes.php`)

**Acceso:** Solo admin.

- **Filtros globales:** Fecha desde/hasta con atajos (7 dias, 30 dias, 90 dias)
- **4 KPIs comparativos** (periodo actual vs periodo anterior):
  - Total Citas (con variacion %)
  - Ingresos totales (con variacion %)
  - Ticket Promedio (con variacion %)
  - Tasa de Cancelacion (con variacion %)
- **9 Graficos Chart.js:**
  - Ingresos vs Egresos (lineas, agrupable por dia/semana/mes)
  - Por Metodo de Pago (doughnut)
  - Servicios Mas Consultados (barras)
  - Ingresos por Servicio (barras)
  - Clientes (doughnut: nuevos vs recurrentes)
  - Horarios Pico (barras)
  - Citas por Dia de la Semana (barras)
  - Consultas de Precio por Dia (linea)
- **Exportacion CSV** para cada grafico
- API: `api/analytics/servicios_populares.php`, `api/analytics/ingresos.php`, `api/analytics/tendencias.php`

### 3.10 Configuracion (`admin/views/configuracion.php`)

**Acceso:** Solo admin.

- **Datos del Negocio:** Nombre, telefono, email, direccion
- **Horarios y Citas:** Hora apertura, hora cierre, intervalo entre citas (min), dias laborales (checkboxes lun-dom), moneda
- Carga y guardado via AJAX (`api/admin/configuracion.php`)
- Sistema key-value en tabla `configuracion`

### 3.11 Mis Citas — Empleado (`admin/views/mis-citas.php`)

**Acceso:** Empleados (y admin).

- **Filtros:** Fecha (default: hoy), Estado
- **DataTable** con columnas: #, Fecha, Hora, Cliente, Telefono, Servicio, Estado, Acciones
- **Crear cita manual** (modal)
- **Cambio de estado** de citas asignadas (marcar como en_proceso o completada)
- API: `api/admin/mis-citas.php` (GET, PATCH)

### 3.12 Mi Horario — Empleado (`admin/views/mi-horario.php`)

**Acceso:** Empleados (y admin).

- **Tabla de horario semanal:** Dia, Entrada, Salida, Estado (Activo/Inactivo)
- **Lista de servicios asignados:** Nombre, precio, duracion
- Solo lectura (los horarios y servicios se configuran por admin)
- API: `api/admin/mi-horario.php` (GET)

---

## 4. API Endpoints

### 4.1 Autenticacion (`api/auth/`)

| Metodo | Endpoint | Proposito | Auth |
|--------|----------|-----------|------|
| POST | `api/auth/login.php` | Iniciar sesion con email/password. Devuelve rol y nombre. | No |
| POST | `api/auth/registro.php` | Crear cuenta de cliente. Envia email de bienvenida. | No |
| POST/GET | `api/auth/logout.php` | Cerrar sesion. POST responde JSON, GET redirige al inicio. | Si |
| POST | `api/auth/google.php` | Login/registro via Google OAuth (JWT id_token). Verifica con Google API, crea/vincula cuenta. | No |
| POST | `api/auth/enviar-codigo.php` | Enviar codigo de 6 digitos por email (para registro o recuperacion). | No |
| POST | `api/auth/verificar-codigo.php` | Verificar codigo de 6 digitos. Marca email como verificado si es tipo registro. | No |
| POST | `api/auth/recuperar.php` | Solicitar recuperacion de contrasena. Genera y envia codigo. No revela si el email existe. | No |
| POST | `api/auth/reset-password.php` | Restablecer contrasena con codigo verificado. | No |
| POST | `api/auth/cambiar-password.php` | Cambiar contrasena del usuario autenticado. | Si |

**Total: 9 endpoints**

### 4.2 Admin — Gestion (`api/admin/`)

| Metodo | Endpoint | Proposito | Auth |
|--------|----------|-----------|------|
| GET | `api/admin/estadisticas.php` | Dashboard: citas hoy, clientes, ingresos, servicios activos, proximas citas, servicios populares, citas por estado, mensajes no leidos. | Admin |
| GET/POST/PUT/DELETE | `api/admin/servicios.php` | CRUD de servicios. GET lista o detalle (?id=). Soft-delete. | Admin |
| GET/POST/PUT/PATCH | `api/admin/clientes.php` | CRUD de clientes. GET lista con total_citas. PATCH para toggle activo. | Admin |
| GET/POST/PATCH | `api/admin/citas.php` | Gestion de citas. GET con filtros (fecha, estado, rango). GET buscar clientes (?buscar_clientes=). GET servicios activos (?servicios_activos). GET empleados por servicio (?empleados_servicio=). POST crear cita manual. PATCH cambiar estado. | Admin/Empleado |
| GET/POST/PUT/PATCH/DELETE | `api/admin/empleados.php` | CRUD de empleados (crea usuario con rol='empleado'). | Admin |
| GET/POST/PUT/DELETE | `api/admin/productos.php` | CRUD de productos con control de stock. | Admin |
| GET/POST | `api/admin/caja.php` | Control de caja. GET movimientos con filtro de fechas + resumen (entradas, salidas, saldo). POST registrar movimiento. | Admin |
| GET/PATCH/DELETE | `api/admin/mensajes.php` | Mensajes de contacto. PATCH marcar leido. DELETE eliminar. | Admin |
| GET/PUT | `api/admin/configuracion.php` | Configuracion key-value del sistema. GET listar todas. PUT actualizar multiples claves. | Admin |
| GET | `api/admin/categorias.php` | Listar categorias de servicios activas (para selects). | Admin |
| POST | `api/admin/upload.php` | Upload de imagenes (multipart/form-data). Tipos: servicios, productos, equipo, banners. Validacion: extension (jpg, png, webp, gif), MIME type, tamano. | Admin/Empleado |
| GET/PATCH | `api/admin/mis-citas.php` | Citas asignadas al empleado logueado. GET con filtros. PATCH cambiar estado. | Empleado |
| GET | `api/admin/mi-horario.php` | Horarios semanales y servicios asignados del empleado logueado. | Empleado |

**Total: 13 endpoints**

### 4.3 Citas — Publicas (`api/citas/`)

| Metodo | Endpoint | Proposito | Auth |
|--------|----------|-----------|------|
| POST | `api/citas/crear.php` | Crear cita/reserva. Calcula hora_fin segun duracion. Asigna empleado disponible. Genera token de referencia. Envia emails de confirmacion (al cliente y al admin). | Si (o invitado con datos) |
| GET | `api/citas/disponibilidad.php` | Obtener turnos disponibles para fecha+servicio. Verifica dias laborales, horario de apertura/cierre, intervalo entre citas, conflictos con citas existentes. | No |
| POST | `api/citas/cancelar.php` | Cancelar cita por sesion (usuario dueno) o por token (invitado). Restriccion: no se puede cancelar con menos de 2 horas de anticipacion. | Si o Token |

**Total: 3 endpoints**

### 4.4 Servicios — Publicos (`api/servicios/`)

| Metodo | Endpoint | Proposito | Auth |
|--------|----------|-----------|------|
| GET | `api/servicios/listar.php` | Listar servicios activos con categoria e icono. Ordenados por orden de categoria y nombre. | No |
| POST | `api/servicios/consulta_precio.php` | Registrar +1 consulta de precio para analytics. Guarda IP y User-Agent. | No |

**Total: 2 endpoints**

### 4.5 Contacto (`api/`)

| Metodo | Endpoint | Proposito | Auth |
|--------|----------|-----------|------|
| POST | `api/contacto.php` | Guardar mensaje de contacto en BD. Campos: nombre, email, telefono, mensaje. | No |

**Total: 1 endpoint**

### 4.6 Clientes (`api/clientes/`)

| Metodo | Endpoint | Proposito | Auth |
|--------|----------|-----------|------|
| POST | `api/clientes/actualizar-perfil.php` | Actualizar nombre, apellidos, telefono del usuario autenticado. | Si |

**Total: 1 endpoint**

### 4.7 Notificaciones (`api/notificaciones/`)

| Metodo | Endpoint | Proposito | Auth |
|--------|----------|-----------|------|
| GET | `api/notificaciones/listar.php` | Listar notificaciones del usuario. Soporta filtro solo_no_leidas y limit. Devuelve total no leidas. | Si |
| PATCH | `api/notificaciones/marcar_leida.php` | Marcar una notificacion como leida (por ID) o todas (todas: true). | Si |

**Total: 2 endpoints**

### 4.8 Caja — Modular (`api/caja/`)

| Metodo | Endpoint | Proposito | Auth |
|--------|----------|-----------|------|
| POST | `api/caja/registrar_venta.php` | Registrar venta de producto. Crea movimiento de caja + decrementa stock. Soporta metodo de pago (efectivo, tarjeta, transferencia, otro). | Admin |
| GET | `api/caja/listar_movimientos.php` | Listar movimientos con filtros (fecha_desde, fecha_hasta, tipo) y paginacion (limit, offset). | Admin |
| GET/POST | `api/caja/cierre_caja.php` | GET: listar cierres de caja. POST: crear cierre del dia con totales calculados. | Admin |
| GET | `api/caja/resumen.php` | Resumen por periodo: totales, desglose por metodo de pago, movimientos por dia. | Admin |

**Total: 4 endpoints**

### 4.9 Analytics (`api/analytics/`)

| Metodo | Endpoint | Proposito | Auth |
|--------|----------|-----------|------|
| GET | `api/analytics/servicios_populares.php` | Top servicios por consultas de precio. Incluye total de consultas y consultas por dia (para grafico de linea). Filtro por rango de fechas. | Admin |
| GET | `api/analytics/ingresos.php` | Ingresos agrupados por dia/semana/mes. Desglose entradas vs egresos. Filtro por rango de fechas. | Admin |
| GET | `api/analytics/tendencias.php` | Comparativa periodo actual vs anterior: citas, ingresos, tasa cancelacion, ticket promedio, clientes nuevos vs recurrentes, horarios pico, citas por dia de semana, ingresos por servicio. | Admin |

**Total: 3 endpoints**

### Resumen de Endpoints

| Modulo | Cantidad |
|--------|----------|
| Auth | 9 |
| Admin | 13 |
| Citas | 3 |
| Servicios | 2 |
| Contacto | 1 |
| Clientes | 1 |
| Notificaciones | 2 |
| Caja | 4 |
| Analytics | 3 |
| **TOTAL** | **38** |

---

## 5. Sistema de Autenticacion

### 5.1 Flujos Implementados

#### Login Manual
1. Usuario ingresa email + contrasena en `login.php` o modal
2. POST a `api/auth/login.php`
3. Verifica credenciales con `password_verify()`
4. Crea sesion PHP con `session_regenerate_id()`
5. Almacena en sesion: usuario_id, nombre, email, rol
6. Actualiza `ultimo_acceso` en BD
7. Devuelve rol para redireccion (admin -> panel, cliente -> home/redirect)

#### Login / Registro via Google OAuth
1. Usuario hace clic en "Continuar con Google"
2. Frontend obtiene JWT `id_token` de Google Identity Services (GIS)
3. POST a `api/auth/google.php` con el credential
4. Backend verifica token con `https://oauth2.googleapis.com/tokeninfo`
5. Busca usuario por `google_id` o por `email`
6. Si existe: vincula google_id (si falta) e inicia sesion
7. Si no existe: crea cuenta nueva con `email_verificado=1`, sin password
8. Crea sesion PHP

#### Registro Manual
1. Usuario completa formulario en `registro.php` (nombre, apellidos, email, telefono, contrasena)
2. POST a `api/auth/registro.php`
3. Valida campos, verifica email unico
4. Crea usuario con `password_hash()` y `rol='cliente'`
5. Envia email de bienvenida (template `bienvenida.php`)
6. Registra notificacion de bienvenida en BD
7. Opcionalmente, envio de codigo de verificacion de email

#### Verificacion de Email
1. POST a `api/auth/enviar-codigo.php` con tipo='registro'
2. Genera codigo de 6 digitos, almacena en tabla `codigos_verificacion`
3. Expiracion: 15 minutos (configurable)
4. Maximo 3 intentos por codigo, maximo 5 codigos por hora
5. Envia email con el codigo
6. POST a `api/auth/verificar-codigo.php` verifica y marca `email_verificado=1`

#### Recuperacion de Contrasena
1. POST a `api/auth/recuperar.php` con email
2. Genera codigo de recuperacion (no revela si el email existe)
3. Envia email con codigo
4. POST a `api/auth/verificar-codigo.php` con tipo='recuperacion'
5. POST a `api/auth/reset-password.php` con email + codigo + nueva contrasena
6. Verifica codigo y actualiza contrasena con `password_hash()`

#### Cambio de Contrasena (usuario logueado)
1. POST a `api/auth/cambiar-password.php`
2. Verifica contrasena actual (si tiene, los de Google pueden no tener)
3. Actualiza con nueva contrasena hasheada

### 5.2 Roles y Permisos

| Rol | Acceso Panel Admin | Acceso CRUD | Acceso Reportes | Acceso Config | Reservar | Mi Cuenta |
|-----|-------------------|-------------|-----------------|---------------|----------|-----------|
| **admin** | Dashboard completo | Todo (servicios, clientes, citas, empleados, productos, caja, mensajes) | Si | Si | Si | Si |
| **empleado** | Mis Citas, Mi Horario | Solo sus citas (ver, cambiar estado) | No | No | Si | Si |
| **cliente** | No | No | No | No | Si | Si (perfil, historial, contrasena) |

### 5.3 Seguridad Implementada

- Passwords hasheados con `password_hash()` (bcrypt por default)
- Verificacion con `password_verify()`
- Sesiones con `session_regenerate_id(true)` al login
- Prepared statements PDO en todas las consultas SQL
- `htmlspecialchars()` para output en HTML (funcion `sanitizar()`)
- Validacion server-side en todos los endpoints
- Anti-enumeracion de emails en recuperacion de contrasena
- Limite de intentos en codigos de verificacion
- Expiracion de codigos (15 min)
- `noindex, nofollow` en paginas de admin

---

## 6. Base de Datos

**Motor:** MySQL 8+ / MariaDB (InnoDB)
**Base de datos produccion:** `u347774250_pielmorena`
**Esquema:** `database/schema.hostinger.sql`

### 6.1 Tablas (15 tablas)

| # | Tabla | Registros tipicos | Proposito |
|---|-------|-------------------|-----------|
| 1 | `usuarios` | Admin + empleados + clientes | Todos los usuarios del sistema. Campos: id, nombre, apellidos, email, password, telefono, rol (ENUM: admin/empleado/cliente), foto, activo, ultimo_acceso, google_id*, email_verificado*, timestamps. |
| 2 | `categorias_servicios` | ~6-10 | Categorias para agrupar servicios. Campos: id, nombre, descripcion, icono (Bootstrap icon class), orden, activo. |
| 3 | `servicios` | ~10-50 | Servicios del salon. Campos: id, id_categoria (FK), nombre, descripcion, precio, duracion_minutos, imagen, banner, activo, timestamps. |
| 4 | `empleados_servicios` | N:M | Relacion muchos-a-muchos entre empleados y servicios que pueden realizar. Campos: id, id_empleado (FK), id_servicio (FK). UNIQUE constraint. |
| 5 | `horarios` | ~7 por empleado | Horarios semanales de cada empleado. Campos: id, id_empleado (FK), dia_semana (1=Lun, 7=Dom), hora_inicio, hora_fin, activo. |
| 6 | `citas` | Creciente | Citas/reservas. Campos: id, id_cliente (FK), id_servicio (FK), id_empleado (FK nullable), fecha, hora_inicio, hora_fin, estado (ENUM: pendiente/confirmada/en_proceso/completada/cancelada), notas, timestamps. Indices en fecha, estado, cliente, empleado+fecha. |
| 7 | `productos` | ~10-50 | Inventario de productos. Campos: id, nombre, descripcion, precio, stock, stock_minimo, imagen, activo, timestamps. |
| 8 | `caja_movimientos` | Creciente | Movimientos de caja. Campos: id, tipo (ENUM: entrada/salida), monto, concepto, metodo_pago (ENUM: efectivo/tarjeta/transferencia/otro), id_cita (FK nullable), id_usuario (FK), fecha, created_at. |
| 9 | `cierres_caja` | 1/dia | Cierres de caja diarios. Campos: id, fecha (UNIQUE), total_entradas, total_salidas, saldo, id_usuario (FK), notas, created_at. |
| 10 | `consultas_precio` | Creciente | Tracking de clics en tooltip de precio (analytics). Campos: id, id_servicio (FK), ip_visitante, user_agent, created_at. |
| 11 | `notificaciones` | Creciente | Notificaciones in-app. Campos: id, id_usuario (FK), tipo (ENUM: recordatorio_cita/promocion/sistema/general), titulo, mensaje, leida, fecha_envio, created_at. |
| 12 | `promociones` | ~3-10 | Promociones/ofertas. Campos: id, titulo, descripcion, descuento_porcentaje, descuento_monto, fecha_inicio, fecha_fin, imagen, activo, timestamps. |
| 13 | `contacto_mensajes` | Creciente | Mensajes del formulario de contacto. Campos: id, nombre, email, telefono, mensaje, leido, created_at. |
| 14 | `configuracion` | ~8-15 | Settings key-value del sistema. Campos: id, clave (UNIQUE), valor, descripcion, updated_at. |
| 15 | `codigos_verificacion` | Temporal | Codigos de 6 digitos para verificacion de email y recuperacion de contrasena. Campos: id, email, codigo, tipo (ENUM: registro/recuperacion), intentos, usado, expira_at, created_at. |

*\* Campos `google_id` y `email_verificado` fueron agregados via migraciones.*

### 6.2 Migraciones

| Archivo | Descripcion |
|---------|-------------|
| `database/migrations/001_add_google_id.sql` | Agrega columna `google_id` a tabla `usuarios` |
| `database/migrations/002_add_email_verificado.sql` | Agrega columna `email_verificado` a tabla `usuarios` |
| `database/migrations/003_create_codigos_verificacion.sql` | Crea tabla `codigos_verificacion` |

### 6.3 Archivos de Base de Datos

| Archivo | Uso |
|---------|-----|
| `database/schema.sql` | Esquema original de desarrollo |
| `database/schema.hostinger.sql` | Esquema de produccion (15 tablas) |
| `database/seed.sql` | Datos iniciales (desarrollo) |
| `database/seed.hostinger.sql` | Datos iniciales (produccion) |
| `database/temp_datos_extra.sql` | Datos de prueba adicionales |
| `database/temp_datos_prueba.sql` | Datos de prueba |
| `database/temp_usuarios.sql` | Usuarios de prueba |

---

## 7. Integraciones y Librerias Externas

### 7.1 Google OAuth 2.0

- **Tipo:** Google Identity Services (GIS) — nuevo flujo One Tap / Sign-In
- **Backend:** Verificacion de JWT via `oauth2.googleapis.com/tokeninfo`
- **Archivo:** `api/auth/google.php`
- **Configuracion:** Constante `GOOGLE_CLIENT_ID` en `config/config.php`

### 7.2 Email / SMTP

- **Metodo:** Funcion nativa `mail()` de PHP
- **Helper:** `includes/mail_helper.php` — funciones `enviar_email()` y `registrar_notificacion()`
- **Templates HTML:** 6 templates en `templates/email/`:
  - `base.php` — Layout base de email (wrapper HTML)
  - `bienvenida.php` — Email de bienvenida post-registro
  - `confirmacion_cita.php` — Confirmacion de reserva al cliente
  - `cita_confirmada_admin.php` — Notificacion de nueva cita al admin
  - `cancelacion_cita.php` — Confirmacion de cancelacion
  - `recordatorio_cita.php` — Recordatorio de cita (para cron)

### 7.3 Cron Job — Recordatorios

- **Archivo:** `cron/recordatorios.php`
- **Funcion:** Envia emails de recordatorio a clientes con citas para el dia siguiente
- **Frecuencia sugerida:** Diario a las 20:00 (`0 20 * * *`)
- **Anti-duplicado:** Verifica si ya se envio recordatorio hoy para ese cliente
- **Registra:** Notificacion en BD + log de envio

### 7.4 Librerias Frontend

| Libreria | Version | Uso |
|----------|---------|-----|
| **Bootstrap** | 5.3.3 | Framework CSS, grid, componentes (modales, carousel, offcanvas, badges, etc.) |
| **Bootstrap Icons** | 1.13.1 | Iconos vectoriales en toda la interfaz |
| **jQuery** | 3.7+ | Manipulacion DOM, AJAX (usado principalmente en admin) |
| **DataTables** | 1.13.8 | Tablas interactivas en admin (busqueda, paginacion, ordenamiento) |
| **Chart.js** | 4+ | Graficos en dashboard y reportes (barras, lineas, doughnut) |
| **SweetAlert2** | 11+ | Modales de confirmacion, alertas, toasts |
| **FullCalendar** | 6.1.11 | Calendario visual de citas en admin |
| **Google Fonts** | - | Tipografias: Playfair Display, DM Sans, Poppins |

### 7.5 Archivos CSS

| Archivo | Descripcion |
|---------|-------------|
| `assets/css/style.css` | Estilos principales de la landing (variables CSS, componentes PM) |
| `assets/css/premium-v3.css` | Estilos premium de la landing (version 3) |
| `assets/css/premium-auth.css` | Estilos para paginas de autenticacion (login, registro, mi-cuenta) |
| `admin/assets/css/admin.css` | Estilos base del panel admin |
| `admin/assets/css/premium-admin.css` | Estilos premium del panel admin |

### 7.6 Archivos JavaScript

| Archivo | Descripcion |
|---------|-------------|
| `assets/js/main.js` | JS principal de la landing (scroll, animaciones, navbar, contacto form) |
| `assets/js/auth.js` | Logica de autenticacion (login, registro, Google OAuth, modales) |
| `assets/js/banners.js` | Logica de banners de servicios y consulta de precio (tooltip $) |
| `admin/assets/js/admin.js` | JS general del admin (sidebar, DataTables config, helpers, CRUD generico) |

---

## 8. Estado de Implementacion

### 8.1 Completo (implementado y funcional)

- [x] Estructura de carpetas y arquitectura
- [x] Base de datos: 15 tablas en produccion con esquema, seeds y migraciones
- [x] Configuracion (config.php, database.php) conectado a Hostinger
- [x] Landing page one-page completa (10 secciones)
- [x] Header/Navbar responsivo con offcanvas mobile
- [x] Footer con info, enlaces y redes sociales
- [x] CSS Design System completo (style.css, premium-v3.css, premium-auth.css)
- [x] JavaScript landing (main.js, auth.js, banners.js)
- [x] Deploy en Hostinger — ONLINE
- [x] Sistema de autenticacion completo:
  - [x] Login manual (email/password)
  - [x] Registro manual con validacion
  - [x] Google OAuth 2.0 (login + registro)
  - [x] Verificacion de email con codigo de 6 digitos
  - [x] Recuperacion de contrasena (email -> codigo -> nueva password)
  - [x] Cambio de contrasena (usuario autenticado)
  - [x] Sesiones seguras con regeneracion de ID
- [x] Paginas de autenticacion dedicadas (login.php, registro.php)
- [x] Pagina Mi Cuenta (perfil, citas proximas, historial, cambio de contrasena)
- [x] Sistema de reservas online completo:
  - [x] Wizard de 4 pasos (servicio -> fecha -> hora -> confirmar)
  - [x] Verificacion de disponibilidad en tiempo real
  - [x] Proteccion de acceso (requiere autenticacion)
  - [x] Cancelacion de citas con restriccion de tiempo
- [x] Panel admin — Dashboard con estadisticas y graficos
- [x] Panel admin — CRUD Servicios (DataTable + modal + upload imagenes)
- [x] Panel admin — CRUD Clientes (DataTable + modal + toggle activo)
- [x] Panel admin — Gestion Citas (tabla + calendario FullCalendar + cambio estado)
- [x] Panel admin — CRUD Empleados (DataTable + modal)
- [x] Panel admin — CRUD Productos (DataTable + modal + control de stock)
- [x] Panel admin — Control de Caja (entradas/salidas, ventas de producto, cierre diario)
- [x] Panel admin — Mensajes de contacto (ver, marcar leido, eliminar)
- [x] Panel admin — Configuracion (datos negocio, horarios, dias laborales)
- [x] Panel admin — Reportes avanzados (9 graficos, KPIs comparativos, exportacion CSV)
- [x] Panel empleado — Mis Citas (filtros, DataTable, cambio de estado)
- [x] Panel empleado — Mi Horario (lectura de horarios y servicios asignados)
- [x] Analytics de consultas de precio (tracking + graficos)
- [x] API completa: 38 endpoints en 9 modulos
- [x] Formulario de contacto con guardado en BD
- [x] Sistema de emails con 6 templates HTML
- [x] Cron de recordatorios de citas

### 8.2 Parcial (implementado pero con limitaciones)

- [~] **Notificaciones in-app:** Se pueden listar y marcar como leidas via API, pero no hay interfaz dedicada en el panel admin para gestionar/enviar notificaciones masivas. Las notificaciones se crean programaticamente (registro, recordatorios).
- [~] **Promociones:** La tabla existe y la landing muestra 3 promos hardcodeadas, pero no hay CRUD admin para gestion dinamica de promociones.
- [~] **Upload de imagenes:** El endpoint existe y funciona, pero las imagenes de servicios en la landing son placeholders (gradientes CSS), no fotos reales subidas.
- [~] **Google OAuth:** Configurado pero `GOOGLE_CLIENT_ID` esta vacio en config.php (requiere configuracion en Google Cloud Console para produccion).

### 8.3 Pendiente (no implementado)

- [ ] **CRUD de Promociones en admin** (tabla BD existe, falta vista y API CRUD)
- [ ] **Envio masivo de notificaciones/promociones** desde admin
- [ ] **Sistema de notificaciones push** o in-app con UI en el panel
- [ ] **Reportes exportables a PDF**
- [ ] **Asignacion de horarios a empleados desde admin** (la tabla existe, falta interfaz)
- [ ] **Asignacion de servicios a empleados desde admin** (la tabla existe, falta interfaz)
- [ ] **Galeria dinamica** (actualmente son placeholders hardcodeados)
- [x] **Testimonios dinámicos** administrables desde panel
- [ ] **Testing y QA formal**
- [ ] **Facturacion/comprobantes** (fuera del alcance actual, solo control interno)

---

## Apendice: Mapa de Archivos

```
piel-morena-sys/
|-- index.php                          # Landing one-page (10 secciones)
|-- login.php                          # Login clientes
|-- registro.php                       # Registro clientes
|-- mi-cuenta.php                      # Panel del cliente
|-- reservar.php                       # Reservas online (wizard 4 pasos)
|
|-- config/
|   |-- config.php                     # Constantes del sistema
|   |-- database.php                   # Conexion PDO
|   |-- database.example.php           # Ejemplo de conexion
|
|-- includes/
|   |-- init.php                       # Bootstrap (carga config, DB, sesion)
|   |-- header.php                     # Header publico (HTML head, navbar)
|   |-- footer.php                     # Footer publico
|   |-- functions.php                  # Helpers (sanitizar, responder_json, auth checks)
|   |-- auth.php                       # Funciones de autenticacion
|   |-- mail_helper.php                # Envio de emails + registro notificaciones
|
|-- admin/
|   |-- index.php                      # Dashboard admin
|   |-- includes/
|   |   |-- admin_header.php           # Layout admin (sidebar + head)
|   |   |-- admin_footer.php           # Footer admin (scripts)
|   |-- views/
|   |   |-- servicios.php              # CRUD Servicios
|   |   |-- clientes.php               # CRUD Clientes
|   |   |-- citas.php                  # Gestion Citas (tabla + calendario)
|   |   |-- empleados.php              # CRUD Empleados
|   |   |-- productos.php              # CRUD Productos
|   |   |-- caja.php                   # Control de Caja
|   |   |-- mensajes.php               # Mensajes de Contacto
|   |   |-- reportes.php               # Reportes y Analytics
|   |   |-- configuracion.php          # Configuracion del Sistema
|   |   |-- mis-citas.php              # Citas del Empleado
|   |   |-- mi-horario.php             # Horario del Empleado
|   |-- assets/
|       |-- css/admin.css              # Estilos admin
|       |-- css/premium-admin.css      # Estilos premium admin
|       |-- js/admin.js                # JS admin
|
|-- api/
|   |-- auth/                          # 9 endpoints de autenticacion
|   |-- admin/                         # 13 endpoints de gestion admin
|   |-- citas/                         # 3 endpoints de citas publicas
|   |-- servicios/                     # 2 endpoints de servicios
|   |-- clientes/                      # 1 endpoint (actualizar perfil)
|   |-- caja/                          # 4 endpoints de caja
|   |-- notificaciones/                # 2 endpoints de notificaciones
|   |-- analytics/                     # 3 endpoints de analytics
|   |-- contacto.php                   # 1 endpoint de contacto
|
|-- templates/email/                   # 6 templates de email HTML
|-- cron/recordatorios.php             # Cron de recordatorios diarios
|-- database/                          # Esquemas SQL, seeds, migraciones
|-- assets/                            # CSS, JS, imagenes publicas
|-- uploads/                           # Archivos subidos (servicios, productos, equipo, banners)
```
