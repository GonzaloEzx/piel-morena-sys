# Contrato 02 - Catalogo de Servicios

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si
> Relacion: contrato funcional del catalogo de servicios
> Ultima revision: 2026-04-05

## Alcance

Gestion de servicios, categorias, precios, duracion, imagenes y consultas de precio.

## Estado actual

### Implementado

- cards en landing con CTA y tooltip de precio;
- `api/servicios/listar.php`;
- `api/servicios/consulta_precio.php`;
- CRUD admin en `api/admin/servicios.php`;
- categorias activas;
- upload de imagenes por `api/admin/upload.php`;
- tracking de consultas para dashboard y reportes.

## Categorias con jornada

Algunas categorias tienen `requiere_jornada = 1`. Ademas, algunos servicios individuales usan `id_grupo_jornada` para colgarse de un grupo operativo de jornadas aunque su categoria comercial no requiera jornada. Ver contrato `10-jornadas.md` para detalle completo.

Categorias o grupos afectados: Depilacion, Extensiones de Pestanas, Peluqueria y Tratamiento con equipo.

## Disponibilidad por servicio (override)

Cada servicio tiene un campo `disponibilidad` ENUM('auto','normal','jornada') que permite anular la regla heredada de su categoria:

- `auto` (default): hereda el comportamiento de la categoria o del grupo de jornada. Cero impacto sobre servicios existentes.
- `normal`: fuerza calendario libre aunque la categoria tenga `requiere_jornada = 1`.
- `jornada`: fuerza el uso de jornadas aunque la categoria no lo requiera; si se combina con `id_grupo_jornada`, usa las fechas de ese grupo.

La resolucion efectiva se hace en `api/citas/disponibilidad.php` y `api/jornadas/disponibles.php`.

## Reglas

- cada servicio define duracion en minutos;
- los IDs usados en tooltips de landing deben coincidir con BD si se mantiene ese modelo;
- las consultas de precio son metrica, no reserva;
- `disponibilidad` override manda sobre el comportamiento de categoria/grupo cuando no es `auto`;
- si el servicio tiene `id_grupo_jornada`, usa las jornadas de ese grupo;
- si no tiene grupo y su categoria tiene `requiere_jornada = 1`, la disponibilidad depende de que exista jornada activa;
- los servicios-pack generados por promociones (ver contrato `11-promociones-packs.md`) se filtran del listado publico cuando la promo esta vencida, desactivada o aun no vigente.

## Checklist

- [x] listado publico
- [x] CRUD admin
- [x] categorias
- [x] upload de imagen
- [x] analytics de consultas
- [x] soporte de jornadas por categoria y por grupo de jornada a nivel servicio
- [x] override de disponibilidad por servicio (`auto`/`normal`/`jornada`)
- [x] filtro de packs vencidos en listado publico
