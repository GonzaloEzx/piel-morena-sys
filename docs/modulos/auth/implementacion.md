# Modulo Auth - Implementacion

> Estado: vigente
> Audiencia: desarrollo, agentes
> Fuente de verdad: complementaria a `docs/contracts/01-gestion-usuarios.md`
> Relacion: implementacion real del modulo auth
> Ultima revision: 2026-04-03

## Objetivo

Documentar el estado tecnico real del modulo de autenticacion y gestion de acceso.

Este archivo no reemplaza el contrato funcional ni el manual operativo. Sirve para ubicar rapido:

- endpoints activos;
- flujos soportados;
- capas responsables de sesion;
- medidas de seguridad aplicadas;
- puntos todavia sensibles o no cerrados.

## Endpoints activos

| Metodo | Ruta | Uso |
|---|---|---|
| POST | `api/auth/login.php` | login manual |
| POST | `api/auth/registro.php` | registro manual |
| POST/GET | `api/auth/logout.php` | cierre de sesion |
| POST | `api/auth/google.php` | login o registro con Google |
| POST | `api/auth/enviar-codigo.php` | envio de codigo |
| POST | `api/auth/verificar-codigo.php` | verificacion de codigo |
| POST | `api/auth/recuperar.php` | recuperacion de password |
| POST | `api/auth/reset-password.php` | reset con codigo |
| POST | `api/auth/cambiar-password.php` | cambio autenticado |
| POST | `api/clientes/actualizar-perfil.php` | actualizacion de perfil |

## Flujo vigente

### Manual

- `login.php` y `registro.php` usan formularios dedicados.
- `includes/auth.php` centraliza login, logout y helpers de acceso.
- `includes/init.php` controla expiracion por inactividad.
- el redireccionamiento a `reservar.php` sigue siendo parte importante del flujo de conversion.

### Google

- el backend existe y funciona con token `credential`;
- el flujo tecnico esta disponible;
- la experiencia publica no debe documentarse como cerrada al 100%, porque todavia requiere ajustes de interfaz y validacion operativa.

## Archivos clave

- `includes/auth.php`
- `includes/init.php`
- `login.php`
- `registro.php`
- `mi-cuenta.php`
- `api/auth/`
- `api/clientes/actualizar-perfil.php`

## Selectores y hooks visibles

- `.pm-btn-login`
- `.pm-btn-register`
- `.pm-btn-logout`

## Seguridad aplicada

- bcrypt con `password_hash()` y `password_verify()`;
- `session_regenerate_id(true)` en login;
- validacion server-side;
- PDO con consultas preparadas;
- helpers CSRF disponibles;
- expiracion de sesion por inactividad desde bootstrap.

## Relacion con otras capas

- contrato funcional: `docs/contracts/01-gestion-usuarios.md`
- UX visible: `docs/ux/auth/flujo-login-registro.md`
- manual operativo: `docs/operacion/manuales/gestion-usuarios.md`

## Observaciones

- no conviene tratar el modulo auth como una sola pantalla; cruza login, registro, recuperacion, sesion y perfil;
- cualquier cambio de copy o recorrido visible debe revisar tambien `docs/ux/auth/`.
