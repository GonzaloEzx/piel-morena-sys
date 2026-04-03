# Manual de Gestion de Usuarios

> Estado: vigente
> Audiencia: operacion, producto, desarrollo, agentes
> Fuente de verdad: complementaria
> Relacion: manual operativo del modulo de usuarios
> Ultima revision: 2026-04-03

## Alcance

Este manual cubre:

- registro, login, logout y sesiones;
- verificacion por codigo y recuperacion de password;
- roles `admin`, `empleado` y `cliente`;
- pagina `mi-cuenta.php`;
- permisos en panel interno.

## Archivos clave

- `includes/init.php`
- `includes/functions.php`
- `includes/auth.php`
- `includes/mail_helper.php`
- `includes/header.php`
- `api/auth/*.php`
- `api/clientes/actualizar-perfil.php`
- `login.php`
- `registro.php`
- `mi-cuenta.php`

## Estado actual del modulo

### Implementado

- Registro manual de clientes.
- Login manual.
- Logout GET y POST.
- Recuperacion de password por codigo.
- Verificacion de email por codigo.
- Cambio de password para usuarios autenticados.
- Actualizacion de perfil desde Mi Cuenta.
- Expiracion de sesion por inactividad usando `SESSION_LIFETIME`.
- Roles y guardas de acceso.
- Backend para Google OAuth.

### Implementado con observacion

- Google OAuth:
  - `api/auth/google.php` existe y procesa el token.
  - `includes/auth.php` soporta vinculacion o alta por `google_id`.
  - La UI publica todavia no esta cerrada como flujo final; en `login.php` y `registro.php` el boton puede caer en mensaje de fallback.

## Roles y permisos

| Rol | Capacidades principales |
|---|---|
| `admin` | Acceso total al panel, configuracion, caja, reportes, CRUD |
| `empleado` | Acceso a `mis-citas` y `mi-horario`, puede pasar citas a `en_proceso` o `completada` |
| `cliente` | Reservar, ver historial, editar perfil y cambiar password |

Reglas vigentes:

- `requerir_auth()` protege paginas privadas.
- `requerir_rol()` permite bypass de admin sobre otros roles.
- El panel interno admite `admin` y `empleado`, pero el menu y las vistas cambian segun rol.

## Sesiones

Bootstrap de sesion en `includes/init.php`:

1. inicia `session_start()` si hace falta;
2. carga `config/config.php`;
3. invalida la sesion si `ultima_actividad` supera `SESSION_LIFETIME`;
4. actualiza `$_SESSION['ultima_actividad']`;
5. carga DB, helpers, auth y mail.

Datos persistidos en sesion despues de login:

- `usuario_id`
- `usuario_nombre`
- `usuario_email`
- `usuario_rol`
- `ultima_actividad`

## Flujos activos

### Registro manual

Endpoint: `api/auth/registro.php`

- valida nombre, apellidos, email y password;
- crea usuario con rol `cliente`;
- envia email de bienvenida en modo best-effort;
- el frontend en `registro.php` puede auto-loguear al usuario al terminar.

### Login manual

Endpoint: `api/auth/login.php`

- valida email y password;
- usa `iniciar_sesion()` de `includes/auth.php`;
- regenera `session_id`;
- actualiza `ultimo_acceso`.

### Logout

Endpoint: `api/auth/logout.php`

- soporta GET para redireccion web;
- soporta POST para flujos AJAX;
- limpia por completo la sesion.

### Verificacion por codigo

Endpoints:

- `api/auth/enviar-codigo.php`
- `api/auth/verificar-codigo.php`

Capacidades:

- genera codigo de 6 digitos;
- controla expiracion e intentos;
- puede usarse para verificacion de registro o recuperacion.

### Recuperacion de password

Endpoints:

- `api/auth/recuperar.php`
- `api/auth/reset-password.php`

Flujo:

1. usuario pide codigo por email;
2. verifica codigo;
3. define nueva password;
4. el backend evita revelar si el email existe.

### Google OAuth

Endpoint: `api/auth/google.php`

- recibe `credential` de Google;
- valida el token;
- verifica `GOOGLE_CLIENT_ID` si esta configurado;
- vincula cuenta existente o crea una nueva;
- inicia sesion como `cliente`.

## Mi Cuenta

Archivo: `mi-cuenta.php`

Funciones activas:

- ver datos del usuario;
- editar nombre, apellidos y telefono;
- ver historial de citas;
- cambiar password;
- navegar a `reservar.php`.

APIs asociadas:

- `api/clientes/actualizar-perfil.php`
- `api/auth/cambiar-password.php`

## Aclaracion importante

- el cliente no tiene hoy un flujo publico de cancelacion por API;
- la cancelacion vigente esta restringida a `admin` y `empleado`;
- si este comportamiento cambia, primero debe actualizarse `docs/contracts/03-sistema-reservas.md`.

## Seguridad aplicada

- contraseñas con `password_hash()` y `password_verify()`;
- prepared statements con PDO;
- `session_regenerate_id(true)` en login;
- validacion server-side;
- helper `sanitizar()` para salida HTML;
- CSRF helpers disponibles en `includes/functions.php`.

## Observaciones vigentes

- La documentacion previa marcaba `api/` y `admin/` como inexistentes: eso ya no es correcto.
- `config/database.php` existe en el entorno actual, pero no debe exponerse ni describirse con valores reales en docs.
- El estado de Google debe considerarse "backend listo, experiencia publica en ajuste".
