# Piel Morena -- Guia Completa de Testing

> Documento de referencia para testing manual del sistema Piel Morena.
> Ultima actualizacion: 2026-03-30

---

## Tabla de Contenidos

1. [Cuentas de Prueba](#1-cuentas-de-prueba)
2. [URLs Principales](#2-urls-principales)
3. [Flujos de Testing por Rol](#3-flujos-de-testing-por-rol)
4. [Flujos de Autenticacion](#4-flujos-de-autenticacion)
5. [API Endpoints para Testing](#5-api-endpoints-para-testing)
6. [Casos Borde y Validaciones](#6-casos-borde-y-validaciones)
7. [Checklist de Smoke Test](#7-checklist-de-smoke-test)

---

## 1. Cuentas de Prueba

| Email | Password | Rol | Acceso |
|---|---|---|---|
| `admin@pielmorena.com` | `asdasd` | **admin** | Panel admin completo: dashboard, CRUD servicios/clientes/empleados/productos, citas, caja, reportes, mensajes, configuracion |
| `caja@pielmorena.com` | `asdasd` | **empleado** | Panel staff limitado: mis citas, mi horario, configuracion. Puede crear citas y marcar citas asignadas como en_proceso/completada |
| `cliente3@pielmorena.com` | `asdasd` | **cliente** | Sitio publico: reservar cita, mi cuenta (perfil, historial citas, cambiar password) |

### Notas sobre roles

- **admin**: Acceso total. La funcion `tiene_rol('admin')` siempre pasa los checks de `requerir_rol()`. Ve el sidebar completo: Dashboard, Citas, Servicios, Clientes, Empleados, Productos, Caja, Reportes, Mensajes, Configuracion.
- **empleado**: Accede al panel admin con sidebar reducido: Mis Citas, Mi Horario, Configuracion. Puede cambiar estado de citas asignadas (solo a `en_proceso` o `completada`). Al completar una cita se auto-registra en caja. Puede crear citas nuevas desde el panel.
- **cliente**: Solo accede a paginas publicas. No puede acceder a `/admin/`. Si intenta, es redirigido al inicio.

---

## 2. URLs Principales

**Base de produccion:** `https://pielmorenaestetica.com.ar`

> **Nota:** Todas las pruebas documentadas en este archivo deben ejecutarse sobre `https://pielmorenaestetica.com.ar`.

### Paginas publicas

| Pagina | URL | Requiere auth |
|---|---|---|
| Landing page (one-page) | `/` o `/index.php` | No |
| Login | `/login.php` | No |
| Registro | `/registro.php` | No |
| Reservar cita | `/reservar.php` | Si (muestra aviso si no logueado) |
| Mi cuenta | `/mi-cuenta.php` | Si (redirige a login si no logueado) |

### Panel de administracion

| Vista | URL | Rol minimo |
|---|---|---|
| Dashboard | `/admin/` o `/admin/index.php` | admin |
| Citas (gestion completa) | `/admin/views/citas.php` | admin |
| Mis Citas (empleado) | `/admin/views/mis-citas.php` | empleado |
| Mi Horario | `/admin/views/mi-horario.php` | empleado |
| Servicios (CRUD) | `/admin/views/servicios.php` | admin |
| Clientes (CRUD) | `/admin/views/clientes.php` | admin |
| Empleados (CRUD) | `/admin/views/empleados.php` | admin |
| Productos (CRUD) | `/admin/views/productos.php` | admin |
| Caja (movimientos) | `/admin/views/caja.php` | admin |
| Reportes | `/admin/views/reportes.php` | admin |
| Mensajes de contacto | `/admin/views/mensajes.php` | admin |
| Configuracion | `/admin/views/configuracion.php` | admin (empleado tambien accede) |

### API endpoints (responden JSON)

| Grupo | Base URL |
|---|---|
| Auth | `/api/auth/` |
| Servicios (publico) | `/api/servicios/` |
| Citas (publico) | `/api/citas/` |
| Contacto (publico) | `/api/contacto.php` |
| Clientes (perfil) | `/api/clientes/` |
| Notificaciones | `/api/notificaciones/` |
| Admin | `/api/admin/` |
| Analytics | `/api/analytics/` |
| Caja (compatibilidad) | `/api/caja/` |

---

## 3. Flujos de Testing por Rol

### 3.1 Rol Admin

#### Dashboard (`/admin/`)

1. Iniciar sesion como `admin@pielmorena.com`.
2. Verificar que se muestran las 4 tarjetas de estadisticas: Citas Hoy, Clientes, Ingresos Mes, Servicios.
3. Verificar grafico de barras "Servicios Mas Consultados" (ultimos 30 dias).
4. Verificar grafico de dona "Citas por Estado".
5. Verificar lista "Proximas Citas" con citas de hoy/futuro.
6. Verificar badge de mensajes no leidos en el acceso rapido "Mensajes".
7. Verificar que los 6 accesos rapidos (Citas, Servicios, Clientes, Caja, Mensajes, Config) llevan a las vistas correctas.

#### CRUD Servicios (`/admin/views/servicios.php`)

1. Verificar que la DataTable carga con los servicios existentes.
2. **Crear**: Abrir modal, llenar nombre, precio (>0), duracion, categoria, descripcion. Guardar y verificar que aparece en la tabla.
3. **Editar**: Click en boton editar de un servicio existente. Modificar campos y guardar.
4. **Eliminar definitivo**: Click en eliminar. Confirmar en SweetAlert2. Verificar que el servicio desaparece de la tabla y se borra de la BD.
5. Si el servicio tiene citas asociadas, verificar que la API responde error `409` y que no se elimina.
6. Verificar validaciones: nombre vacio, precio <= 0.
7. Probar subida de imagen (modal deberia tener campo de imagen que llama a `/api/admin/upload.php`).

#### CRUD Clientes (`/admin/views/clientes.php`)

1. Verificar DataTable con clientes (muestra total_citas, google_id, email_verificado, ultimo_acceso).
2. **Crear**: Modal con nombre, apellidos, email, telefono, password. Guardar.
3. **Editar**: Cambiar datos. Si se deja password vacio, no se modifica.
4. **Toggle activo**: Desactivar/activar cliente con PATCH.
5. Verificar que emails duplicados se rechazan (409).

#### CRUD Empleados (`/admin/views/empleados.php`)

1. Verificar DataTable con empleados.
2. **Crear**: nombre, apellidos, email, telefono, password (todos obligatorios).
3. **Editar**: Modificar datos. Password opcional.
4. **Toggle activo**: Desactivar/activar empleado.
5. Verificar rechazo de email duplicado.

#### CRUD Productos (`/admin/views/productos.php`)

1. Verificar DataTable con productos.
2. **Crear**: nombre, precio (>0), stock, stock_minimo, descripcion, imagen.
3. **Editar**: Modificar campos.
4. **Eliminar (soft-delete)**: Marcar activo=0.
5. Verificar alertas de stock bajo (stock < stock_minimo).

#### Gestion de Citas (`/admin/views/citas.php`)

1. Verificar lista/tabla de citas con filtros: fecha, rango de fechas, estado.
2. **Crear cita manual (modal "Nueva Cita")**:
   - Seleccionar servicio (GET `?servicios_activos`).
   - Buscar cliente existente con autocomplete (GET `?buscar_clientes=texto`).
   - O crear cliente nuevo (nombre, email, telefono).
   - Seleccionar empleado (GET `?empleados_servicio=ID`).
   - Elegir fecha y hora.
   - Guardar: la cita se crea en estado `confirmada`.
   - Verificar que el cliente recibe email de confirmacion.
3. **Cambiar estado** (PATCH):
   - Pendiente -> Confirmada (envia email al cliente).
   - Confirmada -> En proceso.
   - En proceso -> Completada (auto-registra entrada en caja si precio > 0, solo una vez por cita).
   - Cualquiera -> Cancelada (envia email de cancelacion al cliente).
4. Verificar que no se permiten horarios solapados al crear.

#### Caja (`/admin/views/caja.php`)

1. Verificar lista de movimientos del dia (GET `?fecha=YYYY-MM-DD`).
2. Verificar resumen: total entradas, total salidas, saldo.
3. **Registrar movimiento manual**: tipo (entrada/salida), concepto, monto (>0), metodo de pago.
4. Probar filtro por rango de fechas (GET `?fecha_desde=&fecha_hasta=`).
5. Verificar que citas completadas generan automaticamente una entrada en caja.

#### Reportes (`/admin/views/reportes.php`)

1. Verificar graficos de analytics:
   - Servicios populares por consultas de precio (`/api/analytics/servicios_populares.php`).
   - Ingresos por periodo con agrupacion dia/semana/mes (`/api/analytics/ingresos.php`).
   - Ingresos por metodo de pago.
   - Ingresos por servicio.
2. Probar filtros de rango de fechas.

#### Mensajes de Contacto (`/admin/views/mensajes.php`)

1. Verificar lista de mensajes (no leidos primero).
2. **Marcar como leido** (PATCH con `{id, leido: 1}`).
3. **Eliminar** (DELETE con `{id}`) - borrado real, no soft-delete.

#### Configuracion (`/admin/views/configuracion.php`)

1. Verificar que se cargan las configuraciones actuales (GET).
2. Modificar valores: horario_apertura, horario_cierre, dias_laborales, intervalo_citas, datos del negocio.
3. Guardar (PUT) y verificar que los cambios se reflejan en el sistema de reservas.

### 3.2 Rol Empleado

Iniciar sesion como `caja@pielmorena.com`.

#### Verificacion de acceso limitado

1. Verificar que el sidebar muestra solo: Mis Citas, Mi Horario, Configuracion.
2. Verificar que NO aparecen: Dashboard, Servicios, Clientes, Empleados, Productos, Caja, Reportes, Mensajes.
3. Intentar acceder directamente a `/admin/` -> debe redirigir (requiere admin).
4. Intentar acceder a `/admin/views/servicios.php` -> debe redirigir.
5. Verificar badge "Staff" en el sidebar (no "Admin").

#### Mis Citas (`/admin/views/mis-citas.php`)

1. Ver lista de citas asignadas al empleado logueado.
2. Filtrar por fecha y estado.
3. **Cambiar estado**: solo puede marcar como `en_proceso` o `completada`.
4. Verificar que al completar una cita se auto-registra en caja.
5. Verificar que no puede cambiar estado de citas no asignadas (error 404).

#### Crear Cita (si el modal esta disponible en mis-citas)

1. El empleado puede crear citas via POST a `/api/admin/citas.php` (el endpoint permite empleado).
2. Verificar que la cita se crea correctamente.

#### Mi Horario (`/admin/views/mi-horario.php`)

1. Ver horarios semanales asignados al empleado.
2. Ver servicios asignados al empleado.

### 3.3 Rol Cliente

Iniciar sesion como `cliente3@pielmorena.com`.

#### Mi Cuenta (`/mi-cuenta.php`)

1. Verificar datos personales cargados: nombre, apellidos, email (deshabilitado), telefono.
2. **Editar perfil**: Cambiar nombre, apellidos, telefono. Guardar y verificar SweetAlert de exito.
3. **Cambiar password**:
   - Ingresar password actual (`asdasd`), nueva password (min 6 chars), confirmar.
   - Verificar que no coinciden -> error "Las contrasenas no coinciden".
   - Verificar nueva muy corta -> error "al menos 6 caracteres".
   - Verificar password actual incorrecta -> error del servidor.
4. Verificar seccion "Proximas citas" (citas futuras con estado pendiente/confirmada).
5. Verificar seccion "Historial de citas" (citas pasadas y con otros estados).

#### Reservar Cita (`/reservar.php`)

1. Verificar wizard de 4 pasos: Servicio -> Fecha -> Hora -> Confirmar.
2. **Paso 1**: Seleccionar un servicio (muestra nombre, duracion, precio).
3. **Paso 2**: Elegir fecha (min=hoy, max=hoy+60 dias). No se puede elegir fechas pasadas.
4. **Paso 3**: Ver turnos disponibles. Si el dia no es laboral, muestra "No atendemos este dia". Si es hoy, no muestra turnos pasados (margen 30 min).
5. **Paso 4**: Resumen con servicio, fecha, hora, precio. Confirmar.
6. **Exito**: Muestra numero de reserva y codigo de cancelacion. Boton "Ver mis citas" lleva a mi-cuenta.
7. Verificar que la cita se crea en estado `pendiente`.
8. Verificar que se recibe email de confirmacion.
9. Verificar preseleccion de servicio con `?servicio=ID` en la URL.

#### Landing Page (`/`)

1. Verificar navegacion del navbar (scroll suave a secciones).
2. Verificar seccion de servicios con banners.
3. Click en icono de precio ($) de un servicio -> registra consulta de precio en analytics.
4. Verificar formulario de contacto -> envia mensaje a la BD.

#### Cancelacion de Cita

1. Desde mi-cuenta o via API, cancelar una cita en estado pendiente/confirmada.
2. Verificar restriccion: no se puede cancelar con menos de 2 horas de anticipacion (excepto admin).
3. No se puede cancelar una cita ya completada o cancelada.

---

## 4. Flujos de Autenticacion

### 4.1 Login Manual (email/password)

**Endpoint:** `POST /api/auth/login.php`

1. Login con credenciales validas -> sesion creada, respuesta con `{success: true, data: {rol, nombre}}`.
2. Login con email inexistente -> `{success: false, error: "Credenciales incorrectas"}` (401).
3. Login con password incorrecta -> mismo error generico (no revela si el email existe).
4. Login con cuenta desactivada (activo=0) -> `{success: false, error: "Cuenta desactivada"}`.
5. Login con email vacio o formato invalido -> error 400.
6. Login con metodo GET -> error 405.
7. Verificar que `session_regenerate_id()` se ejecuta (seguridad contra session fixation).
8. Verificar que `ultimo_acceso` se actualiza en la BD.

### 4.2 Google OAuth

**Endpoint:** `POST /api/auth/google.php`

1. Enviar JWT `credential` valido de Google -> login o registro automatico.
2. Si el `google_id` ya existe en la BD -> login directo.
3. Si el `google_id` no existe pero el email si -> vincula google_id a cuenta existente + marca `email_verificado=1`.
4. Si ni google_id ni email existen -> crea nuevo usuario con rol `cliente`, `email_verificado=1`.
5. Token invalido o expirado -> error 401.
6. Cuenta desactivada -> error 401.
7. Sin credential -> error 400.

### 4.3 Registro de Nuevo Usuario

**Endpoint:** `POST /api/auth/registro.php`

1. Registro con datos completos (nombre, apellidos, email, password, telefono) -> crea cuenta con rol `cliente`.
2. Verificar email de bienvenida (best-effort, no bloquea).
3. Verificar notificacion de bienvenida en BD.
4. Registro con email ya existente -> error 409 "El email ya esta registrado".
5. Registro sin nombre -> error 400.
6. Registro sin apellidos -> error 400.
7. Registro con email invalido -> error 400.
8. Registro con password < 6 caracteres -> error 400.
9. Registro con metodo GET -> error 405.

### 4.4 Verificacion de Email (Codigo 6 Digitos)

**Enviar codigo:** `POST /api/auth/enviar-codigo.php`

1. Enviar codigo de tipo `registro` a email no registrado -> genera codigo de 6 digitos, envia email.
2. Enviar codigo de tipo `registro` a email ya registrado -> error 409 "Este email ya tiene una cuenta".
3. Enviar codigo de tipo `recuperacion` -> siempre responde exito (no revela si el email existe).
4. Verificar rate limiting: maximo 5 codigos por email por hora (CODIGO_MAX_POR_HORA). Exceder -> error 429.
5. Los codigos anteriores del mismo email/tipo se invalidan al generar uno nuevo.
6. Codigo expira en 15 minutos (CODIGO_EXPIRACION_MINUTOS).
7. En entorno `development`, la respuesta incluye `codigo_dev` si el email falla.

**Verificar codigo:** `POST /api/auth/verificar-codigo.php`

1. Codigo correcto -> `{success: true}`. Si tipo=registro, marca `email_verificado=1`.
2. Codigo incorrecto -> incrementa intentos. Maximo 3 intentos (CODIGO_MAX_INTENTOS). Muestra intentos restantes.
3. Codigo expirado -> error "El codigo ha expirado".
4. Codigo ya usado -> error "Codigo invalido o expirado".
5. Codigo con longitud != 6 -> error 400.

### 4.5 Recuperacion de Contrasena

**Paso 1 - Solicitar codigo:** `POST /api/auth/recuperar.php`

1. Enviar email registrado -> genera codigo tipo `recuperacion` y lo envia por email.
2. Enviar email no registrado -> responde igual (no revela existencia del email). No genera codigo.

**Paso 2 - Resetear password:** `POST /api/auth/reset-password.php`

1. Enviar email + codigo correcto + nueva password (>=6 chars) -> cambia password, responde exito.
2. Codigo incorrecto -> error 400.
3. Password < 6 caracteres -> error 400.
4. Email invalido -> error 400.

### 4.6 Cambio de Contrasena (Desde Mi Cuenta)

**Endpoint:** `POST /api/auth/cambiar-password.php`

1. Requiere sesion activa (401 si no autenticado).
2. Password actual correcta + nueva password >= 6 chars -> exito.
3. Password actual incorrecta -> error "La contrasena actual es incorrecta".
4. Nueva password < 6 chars -> error 400.
5. Usuario con solo Google (password NULL en BD) -> no requiere password actual para establecer una nueva.

### 4.7 Logout

**Endpoint:** `POST /api/auth/logout.php` (AJAX) o `GET /api/auth/logout.php` (link directo)

1. POST -> responde JSON `{success: true, data: {message: "Sesion cerrada"}}`.
2. GET -> destruye sesion y redirige a `/`.

---

## 5. API Endpoints para Testing

### Formato de respuesta estandar

```json
// Exito
{"success": true, "data": { ... }}

// Error
{"success": false, "error": "Mensaje de error"}
```

### 5.1 Autenticacion

```bash
# Login
curl -X POST https://pielmorenaestetica.com.ar/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@pielmorena.com", "password": "asdasd"}'

# Registro
curl -X POST https://pielmorenaestetica.com.ar/api/auth/registro.php \
  -H "Content-Type: application/json" \
  -d '{"nombre": "Test", "apellidos": "Usuario", "email": "test@example.com", "password": "123456", "telefono": "+54 111 222 3333"}'

# Enviar codigo de verificacion
curl -X POST https://pielmorenaestetica.com.ar/api/auth/enviar-codigo.php \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "tipo": "registro"}'

# Verificar codigo
curl -X POST https://pielmorenaestetica.com.ar/api/auth/verificar-codigo.php \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "codigo": "123456", "tipo": "registro"}'

# Recuperar password (solicitar codigo)
curl -X POST https://pielmorenaestetica.com.ar/api/auth/recuperar.php \
  -H "Content-Type: application/json" \
  -d '{"email": "cliente3@pielmorena.com"}'

# Resetear password con codigo
curl -X POST https://pielmorenaestetica.com.ar/api/auth/reset-password.php \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "codigo": "123456", "password": "nuevapass"}'

# Cambiar password (requiere cookie de sesion)
curl -X POST https://pielmorenaestetica.com.ar/api/auth/cambiar-password.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=tu_session_id" \
  -d '{"password_actual": "asdasd", "password_nueva": "nuevapass"}'

# Logout (AJAX)
curl -X POST https://pielmorenaestetica.com.ar/api/auth/logout.php \
  -b "PHPSESSID=tu_session_id"
```

### 5.2 Servicios (Publico)

```bash
# Listar servicios activos (no requiere auth)
curl https://pielmorenaestetica.com.ar/api/servicios/listar.php

# Registrar consulta de precio (no requiere auth)
curl -X POST https://pielmorenaestetica.com.ar/api/servicios/consulta_precio.php \
  -H "Content-Type: application/json" \
  -d '{"id_servicio": 1}'
```

### 5.3 Citas (Publico / Cliente)

```bash
# Verificar disponibilidad (no requiere auth)
curl "https://pielmorenaestetica.com.ar/api/citas/disponibilidad.php?fecha=2026-03-25&id_servicio=1"

# Crear cita (requiere sesion de cliente)
curl -X POST https://pielmorenaestetica.com.ar/api/citas/crear.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=tu_session_id" \
  -d '{"id_servicio": 1, "fecha": "2026-03-25", "hora_inicio": "10:00"}'

# Cancelar cita (por sesion o por token)
curl -X POST https://pielmorenaestetica.com.ar/api/citas/cancelar.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=tu_session_id" \
  -d '{"id_cita": 123}'

# Cancelar cita por token (invitado, sin sesion)
curl -X POST https://pielmorenaestetica.com.ar/api/citas/cancelar.php \
  -H "Content-Type: application/json" \
  -d '{"id_cita": 123, "token": "abc123..."}'
```

### 5.4 Contacto (Publico)

```bash
# Enviar mensaje de contacto
curl -X POST https://pielmorenaestetica.com.ar/api/contacto.php \
  -H "Content-Type: application/json" \
  -d '{"nombre": "Juan Perez", "email": "juan@test.com", "telefono": "123456", "mensaje": "Consulta de prueba"}'
```

### 5.5 Perfil del Cliente (Requiere sesion)

```bash
# Actualizar perfil
curl -X POST https://pielmorenaestetica.com.ar/api/clientes/actualizar-perfil.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=tu_session_id" \
  -d '{"nombre": "NuevoNombre", "apellidos": "NuevoApellido", "telefono": "+54 111 000 0000"}'
```

### 5.6 Notificaciones (Requiere sesion)

```bash
# Listar notificaciones
curl "https://pielmorenaestetica.com.ar/api/notificaciones/listar.php?limit=10" \
  -b "PHPSESSID=tu_session_id"

# Solo no leidas
curl "https://pielmorenaestetica.com.ar/api/notificaciones/listar.php?solo_no_leidas=1" \
  -b "PHPSESSID=tu_session_id"

# Marcar una como leida
curl -X PATCH https://pielmorenaestetica.com.ar/api/notificaciones/marcar_leida.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=tu_session_id" \
  -d '{"id": 5}'

# Marcar todas como leidas
curl -X PATCH https://pielmorenaestetica.com.ar/api/notificaciones/marcar_leida.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=tu_session_id" \
  -d '{"todas": true}'
```

### 5.7 Admin - Servicios (Requiere sesion admin)

```bash
# Listar todos los servicios
curl https://pielmorenaestetica.com.ar/api/admin/servicios.php \
  -b "PHPSESSID=admin_session"

# Obtener un servicio
curl "https://pielmorenaestetica.com.ar/api/admin/servicios.php?id=1" \
  -b "PHPSESSID=admin_session"

# Crear servicio
curl -X POST https://pielmorenaestetica.com.ar/api/admin/servicios.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"nombre": "Servicio Test", "precio": 5000, "duracion_minutos": 60, "descripcion": "Descripcion", "id_categoria": 1}'

# Editar servicio
curl -X PUT https://pielmorenaestetica.com.ar/api/admin/servicios.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id": 1, "nombre": "Nombre Editado", "precio": 6000, "duracion_minutos": 45}'

# Eliminar servicio (definitivo)
curl -X DELETE https://pielmorenaestetica.com.ar/api/admin/servicios.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id": 99}'
```

### 5.8 Admin - Clientes (Requiere sesion admin)

```bash
# Listar clientes (incluye total_citas, google_id, email_verificado)
curl https://pielmorenaestetica.com.ar/api/admin/clientes.php \
  -b "PHPSESSID=admin_session"

# Crear cliente
curl -X POST https://pielmorenaestetica.com.ar/api/admin/clientes.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"nombre": "Maria", "apellidos": "Lopez", "email": "maria@test.com", "password": "123456", "telefono": "555-0001"}'

# Editar cliente (password opcional)
curl -X PUT https://pielmorenaestetica.com.ar/api/admin/clientes.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id": 5, "nombre": "Maria", "apellidos": "Lopez Editada", "email": "maria@test.com", "telefono": "555-0002"}'

# Toggle activo/inactivo
curl -X PATCH https://pielmorenaestetica.com.ar/api/admin/clientes.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id": 5, "activo": 0}'
```

### 5.9 Admin - Empleados (Requiere sesion admin)

```bash
# Listar empleados
curl https://pielmorenaestetica.com.ar/api/admin/empleados.php \
  -b "PHPSESSID=admin_session"

# Crear empleado
curl -X POST https://pielmorenaestetica.com.ar/api/admin/empleados.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"nombre": "Carlos", "apellidos": "Garcia", "email": "carlos@test.com", "password": "123456", "telefono": "555-0003"}'

# Editar empleado
curl -X PUT https://pielmorenaestetica.com.ar/api/admin/empleados.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id": 3, "nombre": "Carlos", "apellidos": "Garcia Editado", "email": "carlos@test.com"}'

# Toggle activo
curl -X PATCH https://pielmorenaestetica.com.ar/api/admin/empleados.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id": 3, "activo": 0}'
```

### 5.10 Admin - Productos (Requiere sesion admin)

```bash
# Listar productos
curl https://pielmorenaestetica.com.ar/api/admin/productos.php \
  -b "PHPSESSID=admin_session"

# Crear producto
curl -X POST https://pielmorenaestetica.com.ar/api/admin/productos.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"nombre": "Crema Hidratante", "precio": 2500, "stock": 20, "stock_minimo": 5, "descripcion": "Crema facial"}'

# Editar producto
curl -X PUT https://pielmorenaestetica.com.ar/api/admin/productos.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id": 1, "nombre": "Crema Hidratante Premium", "precio": 3000, "stock": 15, "stock_minimo": 3}'

# Eliminar producto (soft-delete)
curl -X DELETE https://pielmorenaestetica.com.ar/api/admin/productos.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id": 99}'
```

### 5.11 Admin - Citas (Requiere sesion admin o empleado)

```bash
# Listar citas (con filtros opcionales)
curl "https://pielmorenaestetica.com.ar/api/admin/citas.php?fecha=2026-03-25&estado=pendiente" \
  -b "PHPSESSID=admin_session"

# Listar citas por rango
curl "https://pielmorenaestetica.com.ar/api/admin/citas.php?fecha_desde=2026-03-01&fecha_hasta=2026-03-31" \
  -b "PHPSESSID=admin_session"

# Buscar clientes (autocomplete, minimo 2 chars)
curl "https://pielmorenaestetica.com.ar/api/admin/citas.php?buscar_clientes=maria" \
  -b "PHPSESSID=admin_session"

# Listar servicios activos (para selects)
curl "https://pielmorenaestetica.com.ar/api/admin/citas.php?servicios_activos" \
  -b "PHPSESSID=admin_session"

# Empleados por servicio
curl "https://pielmorenaestetica.com.ar/api/admin/citas.php?empleados_servicio=1" \
  -b "PHPSESSID=admin_session"

# Crear cita manual (cliente existente)
curl -X POST https://pielmorenaestetica.com.ar/api/admin/citas.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id_servicio": 1, "id_cliente": 5, "fecha": "2026-03-25", "hora_inicio": "10:00", "id_empleado": 2, "notas": "Nota de prueba"}'

# Crear cita manual (cliente nuevo)
curl -X POST https://pielmorenaestetica.com.ar/api/admin/citas.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id_servicio": 1, "nombre": "Nuevo Cliente", "email": "nuevo@test.com", "telefono": "555-0000", "fecha": "2026-03-25", "hora_inicio": "11:00"}'

# Cambiar estado de cita
curl -X PATCH https://pielmorenaestetica.com.ar/api/admin/citas.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id": 10, "estado": "completada"}'

# Cambiar estado + asignar empleado
curl -X PATCH https://pielmorenaestetica.com.ar/api/admin/citas.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id": 10, "estado": "confirmada", "id_empleado": 2}'
```

### 5.12 Admin - Caja (Requiere sesion admin)

```bash
# Movimientos del dia
curl https://pielmorenaestetica.com.ar/api/admin/caja.php \
  -b "PHPSESSID=admin_session"

# Movimientos de una fecha especifica
curl "https://pielmorenaestetica.com.ar/api/admin/caja.php?fecha=2026-03-20" \
  -b "PHPSESSID=admin_session"

# Movimientos por rango
curl "https://pielmorenaestetica.com.ar/api/admin/caja.php?fecha_desde=2026-03-01&fecha_hasta=2026-03-31" \
  -b "PHPSESSID=admin_session"

# Registrar movimiento manual
curl -X POST https://pielmorenaestetica.com.ar/api/admin/caja.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"tipo": "entrada", "concepto": "Venta de producto", "monto": 2500, "metodo_pago": "efectivo"}'

curl -X POST https://pielmorenaestetica.com.ar/api/admin/caja.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"tipo": "salida", "concepto": "Compra de insumos", "monto": 1000, "metodo_pago": "transferencia"}'
```

### 5.13 Admin - Estadisticas y Analytics (Requiere sesion admin)

```bash
# Dashboard stats
curl https://pielmorenaestetica.com.ar/api/admin/estadisticas.php \
  -b "PHPSESSID=admin_session"

# Servicios populares (consultas de precio)
curl "https://pielmorenaestetica.com.ar/api/analytics/servicios_populares.php?fecha_desde=2026-03-01&fecha_hasta=2026-03-24" \
  -b "PHPSESSID=admin_session"

# Ingresos por periodo
curl "https://pielmorenaestetica.com.ar/api/analytics/ingresos.php?fecha_desde=2026-03-01&fecha_hasta=2026-03-24&agrupar=dia" \
  -b "PHPSESSID=admin_session"

# Categorias de servicios
curl https://pielmorenaestetica.com.ar/api/admin/categorias.php \
  -b "PHPSESSID=admin_session"
```

### 5.14 Admin - Mensajes (Requiere sesion admin)

```bash
# Listar mensajes
curl https://pielmorenaestetica.com.ar/api/admin/mensajes.php \
  -b "PHPSESSID=admin_session"

# Marcar como leido
curl -X PATCH https://pielmorenaestetica.com.ar/api/admin/mensajes.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id": 1, "leido": 1}'

# Eliminar mensaje
curl -X DELETE https://pielmorenaestetica.com.ar/api/admin/mensajes.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"id": 1}'
```

### 5.15 Admin - Configuracion (Requiere sesion admin)

```bash
# Listar configuraciones
curl https://pielmorenaestetica.com.ar/api/admin/configuracion.php \
  -b "PHPSESSID=admin_session"

# Actualizar configuraciones
curl -X PUT https://pielmorenaestetica.com.ar/api/admin/configuracion.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=admin_session" \
  -d '{"horario_apertura": "09:00", "horario_cierre": "20:00", "dias_laborales": "1,2,3,4,5,6", "intervalo_citas": "30"}'
```

### 5.16 Admin - Upload de Imagenes (Requiere sesion admin o empleado)

```bash
# Subir imagen (multipart/form-data)
curl -X POST https://pielmorenaestetica.com.ar/api/admin/upload.php \
  -b "PHPSESSID=admin_session" \
  -F "imagen=@/ruta/a/imagen.jpg" \
  -F "tipo=servicios"
```

Tipos validos: `servicios`, `productos`, `equipo`, `banners`.
Formatos validos: jpg, jpeg, png, webp, gif.
Tamano maximo: 5 MB.
Imagenes > 1200px de ancho se redimensionan automaticamente.

### 5.17 Empleado - Mis Citas (Requiere sesion empleado)

```bash
# Listar citas asignadas al empleado
curl "https://pielmorenaestetica.com.ar/api/admin/mis-citas.php?fecha=2026-03-25" \
  -b "PHPSESSID=empleado_session"

# Cambiar estado (solo en_proceso o completada)
curl -X PATCH https://pielmorenaestetica.com.ar/api/admin/mis-citas.php \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=empleado_session" \
  -d '{"id": 10, "estado": "completada"}'
```

### 5.18 Empleado - Mi Horario (Requiere sesion empleado)

```bash
# Ver horarios y servicios asignados
curl https://pielmorenaestetica.com.ar/api/admin/mi-horario.php \
  -b "PHPSESSID=empleado_session"
```

---

## 6. Casos Borde y Validaciones

### 6.1 Autenticacion y Seguridad

| Caso | Endpoint | Comportamiento esperado |
|---|---|---|
| Email duplicado en registro | `POST /api/auth/registro.php` | Error 409 "El email ya esta registrado" |
| Email duplicado en registro desde admin | `POST /api/admin/clientes.php` | Error 409 "El email ya esta registrado" |
| Email duplicado al editar (otro usuario lo tiene) | `PUT /api/admin/clientes.php` | Error 409 "El email ya esta en uso" |
| Login con cuenta desactivada | `POST /api/auth/login.php` | Error 401 "Cuenta desactivada" |
| Acceso a admin sin sesion | `GET /admin/` | Redireccion a login |
| Acceso a admin con rol cliente | `GET /admin/` | Redireccion al inicio |
| Acceso a API admin sin sesion | `GET /api/admin/servicios.php` | Error 403 "No autorizado" |
| Acceso a API admin con rol empleado (donde se requiere admin) | `GET /api/admin/servicios.php` | Error 403 "No autorizado" |
| Acceso a API admin con rol empleado (donde se permite) | `GET /api/admin/citas.php` | Exito |
| CSRF token vacio | Formularios | Depende de implementacion del formulario |
| Session fixation | Login | `session_regenerate_id(true)` se ejecuta |
| Metodo HTTP incorrecto en todos los endpoints | Cualquiera | Error 405 "Metodo no permitido" |

### 6.2 Reservas y Citas

| Caso | Endpoint | Comportamiento esperado |
|---|---|---|
| Reservar en fecha pasada | `POST /api/citas/crear.php` | Error 400 "No se puede reservar en una fecha pasada" |
| Reservar en dia no laboral | `GET /api/citas/disponibilidad.php` | Devuelve turnos=[] con mensaje "No atendemos este dia" |
| Reservar horario ya ocupado (solapamiento) | `POST /api/citas/crear.php` | Error 409 "Este horario ya no esta disponible" |
| Crear cita admin con horario solapado | `POST /api/admin/citas.php` | Error 409 "Este horario ya esta ocupado" |
| Cancelar cita ya completada | `POST /api/citas/cancelar.php` | Error 400 "No se puede cancelar una cita completada" |
| Cancelar cita ya cancelada | `POST /api/citas/cancelar.php` | Error 400 "La cita ya esta cancelada" |
| Cancelar con menos de 2 horas (cliente) | `POST /api/citas/cancelar.php` | Error 400 "No se puede cancelar con menos de 2 horas" |
| Cancelar con menos de 2 horas (admin) | `POST /api/citas/cancelar.php` | Exito (admin no tiene restriccion) |
| Cancelar cita de otro usuario | `POST /api/citas/cancelar.php` | Error 403 "No tienes permiso" |
| Cancelar cita con token valido (invitado) | `POST /api/citas/cancelar.php` | Exito |
| Cancelar cita con token invalido | `POST /api/citas/cancelar.php` | Error 403 |
| Turnos hoy con margen de 30 min | `GET /api/citas/disponibilidad.php` | No muestra turnos que empiecen en menos de 30 min |
| Reservar sin sesion (como invitado) | `POST /api/citas/crear.php` | Crea usuario temporal si email no existe |
| Reservar sin sesion y email existente | `POST /api/citas/crear.php` | Usa el usuario existente con ese email |
| ID de servicio inexistente | `POST /api/citas/crear.php` | Error 404 "Servicio no encontrado" |
| ID de servicio inactivo | `POST /api/citas/crear.php` | Error 404 "Servicio no encontrado" |
| Formato de fecha invalido | `POST /api/citas/crear.php` | Error 400 "Fecha invalida" |
| Formato de hora invalido | `POST /api/citas/crear.php` | Error 400 "Hora invalida" |

### 6.3 CRUD y Datos

| Caso | Endpoint | Comportamiento esperado |
|---|---|---|
| Crear servicio sin nombre | `POST /api/admin/servicios.php` | Error 400 "Nombre y precio son obligatorios" |
| Crear servicio con precio <= 0 | `POST /api/admin/servicios.php` | Error 400 |
| Crear producto sin nombre | `POST /api/admin/productos.php` | Error 400 |
| Crear cliente sin datos obligatorios | `POST /api/admin/clientes.php` | Error 400 "Nombre, apellidos, email y contrasena son obligatorios" |
| Crear empleado sin datos obligatorios | `POST /api/admin/empleados.php` | Error 400 |
| Editar con ID inexistente | `PUT /api/admin/servicios.php` | Ejecuta update sin afectar filas (no error) |
| Eliminar con ID inexistente | `DELETE /api/admin/servicios.php` | Error 404 "Servicio no encontrado" |
| Eliminar servicio con citas asociadas | `DELETE /api/admin/servicios.php` | Error 409 |
| Eliminar servicio sin citas asociadas | `DELETE /api/admin/servicios.php` | Borra la fila definitivamente |
| Toggle activo cliente con ID invalido | `PATCH /api/admin/clientes.php` | Error 400 "ID requerido" |
| Buscar clientes con < 2 chars | `GET /api/admin/citas.php?buscar_clientes=a` | Devuelve array vacio |

### 6.4 Caja

| Caso | Endpoint | Comportamiento esperado |
|---|---|---|
| Registrar movimiento sin concepto | `POST /api/admin/caja.php` | Error 400 "Tipo, concepto y monto son obligatorios" |
| Registrar movimiento con monto <= 0 | `POST /api/admin/caja.php` | Error 400 |
| Registrar movimiento con tipo invalido | `POST /api/admin/caja.php` | Error 400 |
| Completar cita ya registrada en caja | `PATCH /api/admin/citas.php` | No duplica el registro (verifica si id_cita ya existe en caja_movimientos) |
| Completar cita con precio = 0 | `PATCH /api/admin/citas.php` | No registra en caja |

### 6.5 Verificacion de Email

| Caso | Endpoint | Comportamiento esperado |
|---|---|---|
| Exceder limite de codigos por hora (>5) | `POST /api/auth/enviar-codigo.php` | Error 429 "Demasiados intentos" |
| Codigo expirado (>15 min) | `POST /api/auth/verificar-codigo.php` | Error "El codigo ha expirado" |
| Exceder intentos de verificacion (>3) | `POST /api/auth/verificar-codigo.php` | Error "Demasiados intentos fallidos" |
| Codigo de longitud != 6 | `POST /api/auth/verificar-codigo.php` | Error 400 "Codigo invalido. Debe ser de 6 digitos" |
| Tipo invalido (no registro/recuperacion) | `POST /api/auth/enviar-codigo.php` | Error 400 "Tipo invalido" |

### 6.6 Upload de Imagenes

| Caso | Endpoint | Comportamiento esperado |
|---|---|---|
| Sin archivo | `POST /api/admin/upload.php` | Error 400 "No se selecciono ningun archivo" |
| Tipo de carpeta invalido | `POST /api/admin/upload.php` | Error 400 "Tipo invalido" |
| Extension no permitida (.exe, .pdf, etc.) | `POST /api/admin/upload.php` | Error 400 "Formato no permitido" |
| MIME type falso (archivo renombrado) | `POST /api/admin/upload.php` | Error 400 "El archivo no es una imagen valida" |
| Archivo > 5MB | `POST /api/admin/upload.php` | Error 400 "La imagen no puede superar los 5MB" |
| Imagen > 1200px de ancho | `POST /api/admin/upload.php` | Se redimensiona automaticamente |

### 6.7 Notificaciones

| Caso | Endpoint | Comportamiento esperado |
|---|---|---|
| Listar sin sesion | `GET /api/notificaciones/listar.php` | Error 403 |
| Marcar leida notificacion de otro usuario | `PATCH /api/notificaciones/marcar_leida.php` | Error 404 (el WHERE filtra por id_usuario) |
| Marcar leida sin ID | `PATCH /api/notificaciones/marcar_leida.php` | Error 400 "ID de notificacion requerido" |
| Marcar todas como leidas | `PATCH /api/notificaciones/marcar_leida.php` | Exito, retorna cantidad actualizadas |

---

## 7. Checklist de Smoke Test

Checklist rapido para verificar que un deploy funciona correctamente. Ejecutar en orden.

### Sitio publico

- [ ] **Landing page carga** — Acceder a `https://pielmorenaestetica.com.ar/`, verificar que carga sin errores 500.
- [ ] **Navbar funcional** — Links del menu navegan a las secciones correctas.
- [ ] **Servicios visibles** — La seccion de servicios muestra cards con datos.
- [ ] **Consulta de precio** — Click en icono $ de un servicio, verificar que registra (no error en consola).
- [ ] **Formulario de contacto** — Enviar mensaje de prueba, verificar respuesta exitosa.
- [ ] **Pagina login carga** — Acceder a `/login.php`, verificar que carga.
- [ ] **Pagina registro carga** — Acceder a `/registro.php`, verificar que carga.

### Autenticacion

- [ ] **Login admin funciona** — Login con `admin@pielmorena.com` / `asdasd`, verificar redireccion al panel.
- [ ] **Login empleado funciona** — Login con `caja@pielmorena.com` / `asdasd`.
- [ ] **Login cliente funciona** — Login con `cliente3@pielmorena.com` / `asdasd`.
- [ ] **Logout funciona** — Cerrar sesion, verificar que redirige al inicio y la sesion se destruye.
- [ ] **Login con credenciales invalidas** — Verificar mensaje de error.

### Panel Admin

- [ ] **Dashboard carga** — Estadisticas, graficos y proximas citas se muestran.
- [ ] **API estadisticas responde** — `GET /api/admin/estadisticas.php` retorna JSON valido.
- [ ] **Servicios - listar** — La tabla DataTable carga con datos.
- [ ] **Clientes - listar** — La tabla DataTable carga con datos.
- [ ] **Empleados - listar** — La tabla DataTable carga con datos.
- [ ] **Productos - listar** — La tabla DataTable carga con datos.
- [ ] **Citas - listar** — La tabla/lista carga con datos.
- [ ] **Caja - listar** — Movimientos del dia cargan (puede estar vacio).
- [ ] **Mensajes - listar** — Lista de mensajes carga.
- [ ] **Configuracion carga** — Valores de configuracion se muestran.

### Reservas (como cliente logueado)

- [ ] **Pagina reservar carga** — Wizard visible con paso 1 "Elegir servicio".
- [ ] **Servicios cargan** — Lista de servicios disponibles se muestra.
- [ ] **Disponibilidad responde** — Al elegir fecha, los turnos se muestran.
- [ ] **API disponibilidad** — `GET /api/citas/disponibilidad.php?fecha=YYYY-MM-DD&id_servicio=1` retorna JSON.

### Mi Cuenta (como cliente logueado)

- [ ] **Mi cuenta carga** — Datos del usuario visibles, secciones de citas visibles.
- [ ] **Perfil editable** — Cambiar nombre/telefono y guardar.

### Panel Empleado

- [ ] **Mis citas carga** — Lista de citas asignadas al empleado.
- [ ] **Mi horario carga** — Horarios y servicios asignados visibles.

### APIs criticas (verificacion rapida con curl)

```bash
# Landing - servicios publicos
curl -s https://pielmorenaestetica.com.ar/api/servicios/listar.php | head -c 100

# Disponibilidad
curl -s "https://pielmorenaestetica.com.ar/api/citas/disponibilidad.php?fecha=$(date -d '+1 day' +%Y-%m-%d)&id_servicio=1" | head -c 100

# Login (verificar que la API responde)
curl -s -X POST https://pielmorenaestetica.com.ar/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@pielmorena.com", "password": "asdasd"}' | head -c 100
```

### Responsividad

- [ ] **Mobile** — Verificar landing en viewport 375px. Navbar offcanvas funciona.
- [ ] **Tablet** — Verificar en viewport 768px.
- [ ] **Desktop** — Verificar en viewport 1440px.
- [ ] **Admin mobile** — Sidebar colapsa correctamente en mobile.

### Consola del navegador

- [ ] **Sin errores JS criticos** — Abrir DevTools > Console en la landing.
- [ ] **Sin errores JS criticos** — Abrir DevTools > Console en el admin.
- [ ] **Sin errores de red (4xx/5xx)** — Verificar en pestaña Network.

---

## Notas Adicionales

### Obtencion de PHPSESSID para testing con curl

Para usar los endpoints que requieren sesion desde curl/Postman, primero hacer login y capturar la cookie:

```bash
# Login y capturar cookie
curl -c cookies.txt -X POST https://pielmorenaestetica.com.ar/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@pielmorena.com", "password": "asdasd"}'

# Usar la cookie en requests subsiguientes
curl -b cookies.txt https://pielmorenaestetica.com.ar/api/admin/estadisticas.php
```

### Estados validos de citas

`pendiente` -> `confirmada` -> `en_proceso` -> `completada`

Cualquier estado (excepto completada) -> `cancelada`

Los empleados solo pueden cambiar a: `en_proceso`, `completada`.
El admin puede cambiar a cualquier estado valido: `pendiente`, `confirmada`, `en_proceso`, `completada`, `cancelada`.

### Timezone

El sistema usa `America/Argentina/Buenos_Aires`. Tener en cuenta al verificar fechas y horarios.

### Entorno

La configuracion actual es `ENVIRONMENT = 'production'`. En este modo:
- Los errores PHP no se muestran en pantalla.
- El endpoint `enviar-codigo.php` NO devuelve el codigo en la respuesta (solo en development).
- Para debugging, cambiar a `'development'` en `config/config.php`.

