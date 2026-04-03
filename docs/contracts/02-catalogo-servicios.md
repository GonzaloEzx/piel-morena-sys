# Contrato 02 - Catalogo de Servicios

> Estado: vigente
> Audiencia: producto, desarrollo, agentes
> Fuente de verdad: si
> Relacion: contrato funcional del catalogo de servicios
> Ultima revision: 2026-04-03

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

## Reglas

- cada servicio define duracion en minutos;
- los IDs usados en tooltips de landing deben coincidir con BD si se mantiene ese modelo;
- las consultas de precio son metrica, no reserva.

## Checklist

- [x] listado publico
- [x] CRUD admin
- [x] categorias
- [x] upload de imagen
- [x] analytics de consultas
