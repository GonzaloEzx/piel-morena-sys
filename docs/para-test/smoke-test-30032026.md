# Smoke Test - pielmorenaestetica.com.ar

> Documento historico actualizado para reflejar el estado correcto del proyecto.
> Revision: 2026-04-02
> Dominio vigente: `https://pielmorenaestetica.com.ar`

## Estado esperado

| Area | Estado esperado | Nota |
|---|---|---|
| Front publico | PASS | Todo el trafico operativo se valida solo sobre el dominio principal |
| Admin panel | PASS | Acceso y pruebas sobre el mismo dominio principal |
| Links internos | PASS | No deben apuntar a otro dominio |
| URLs absolutas de servicios | PASS | No deben devolver dominios obsoletos |

## Criterio vigente

- El dominio operativo del proyecto es `https://pielmorenaestetica.com.ar`.
- Las pruebas funcionales y smoke tests deben ejecutarse solo sobre ese dominio.
- Cualquier referencia historica a dominios anteriores se considera obsoleta.

## Smoke test minimo

### Publico

- [ ] abre `https://pielmorenaestetica.com.ar/`
- [ ] hero, servicios, tratamientos y galeria visibles
- [ ] `https://pielmorenaestetica.com.ar/login.php` carga
- [ ] `https://pielmorenaestetica.com.ar/registro.php` carga
- [ ] `https://pielmorenaestetica.com.ar/reservar.php` carga
- [ ] formulario de contacto visible

### Admin

- [ ] login admin correcto
- [ ] dashboard carga
- [ ] servicios carga
- [ ] citas carga
- [ ] caja carga
- [ ] reportes carga

### Integridad de dominio

- [ ] navbar, footer y CTAs permanecen en `pielmorenaestetica.com.ar`
- [ ] `api/servicios/listar.php` no devuelve URLs de dominios anteriores
- [ ] si se usa el dominio anterior, debe redirigir al principal o quedar fuera del flujo operativo

## Nota sobre galeria

- La galeria publica usa los 6 slots de `assets/img/gallery/`.
- La administracion normal de esas imagenes se hace desde `admin/views/galeria.php`.
- No usar un checkout alternativo para cambiar imagenes porque genera divergencia entre servidor y repo.
