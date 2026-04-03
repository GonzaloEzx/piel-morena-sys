# Bloque Auth

## Endpoints activos

| Metodo | Ruta | Uso |
|---|---|---|
| POST | `api/auth/login.php` | Login manual |
| POST | `api/auth/registro.php` | Registro manual |
| POST/GET | `api/auth/logout.php` | Cierre de sesion |
| POST | `api/auth/google.php` | Login/registro con Google |
| POST | `api/auth/enviar-codigo.php` | Envio de codigo |
| POST | `api/auth/verificar-codigo.php` | Verificacion de codigo |
| POST | `api/auth/recuperar.php` | Recuperacion de password |
| POST | `api/auth/reset-password.php` | Reset con codigo |
| POST | `api/auth/cambiar-password.php` | Cambio autenticado |
| POST | `api/clientes/actualizar-perfil.php` | Actualizacion de perfil |

## Flujo vigente

### Manual

- `login.php` y `registro.php` usan formularios dedicados.
- `includes/auth.php` crea y destruye la sesion.
- `includes/init.php` controla expiracion por inactividad.

### Google

- El backend existe y funciona con token `credential`.
- La experiencia publica sigue en ajuste y no debe documentarse como cerrada al 100%.

## Selectores clave

- `.pm-btn-login`
- `.pm-btn-register`
- `.pm-btn-logout`

## Seguridad

- bcrypt con `password_hash()` / `password_verify()`
- `session_regenerate_id(true)` en login
- PDO prepared statements
- helpers CSRF disponibles
- validacion server-side
