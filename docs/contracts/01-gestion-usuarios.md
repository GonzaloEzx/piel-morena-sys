# Contrato 01 - Gestion de Usuarios

## Alcance

Define identidad, autenticacion, roles, sesiones y perfil.

## Requisitos cubiertos

- registro manual de cliente
- login manual
- logout
- recuperacion de password
- verificacion de email por codigo
- cambio de password autenticado
- perfil del cliente
- roles `admin`, `empleado`, `cliente`
- expiracion de sesion por inactividad

## Estado actual

### Implementado

- `api/auth/login.php`
- `api/auth/registro.php`
- `api/auth/logout.php`
- `api/auth/recuperar.php`
- `api/auth/reset-password.php`
- `api/auth/enviar-codigo.php`
- `api/auth/verificar-codigo.php`
- `api/auth/cambiar-password.php`
- `api/clientes/actualizar-perfil.php`
- `mi-cuenta.php`
- helpers de auth y session expiry

### Implementado con observacion

- `api/auth/google.php` y `login_google()` existen.
- El frontend publico todavia no debe considerarse cerrado como experiencia final de Google.

## Reglas de negocio

- un usuario tiene un solo rol;
- admin puede atravesar guardas de rol especifico;
- empleado opera solo su panel acotado;
- cliente solo accede a sus datos y citas;
- el login exitoso regenera `session_id`.

## Checklist

- [x] roles operativos
- [x] login/logout
- [x] registro manual
- [x] recuperacion de password
- [x] verificacion por codigo
- [x] Mi Cuenta
- [x] expiracion de sesion
- [ ] UX publica final de Google OAuth
