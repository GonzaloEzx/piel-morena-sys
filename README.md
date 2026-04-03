# Piel Morena Sys

Sistema web integral para **Piel Morena Estética**, un negocio real de estética y belleza de Resistencia, Chaco. El proyecto combina una **landing pública orientada a conversión**, un **flujo de reservas online**, y un **panel de administración** para operar turnos, servicios, clientes, productos y caja desde un único lugar.

## Contexto de negocio

Piel Morena venía resolviendo la operación diaria con una mezcla de **WhatsApp, Instagram y planillas Excel**. Eso servía para arrancar, pero no para sostener una agenda ordenada, una base de clientes consistente ni una visión clara de ingresos, promociones y disponibilidad del equipo.

Este sistema nace para profesionalizar esa operación sin perder el tono del negocio: atención cercana, catálogo real de servicios, comunicación simple y una presencia digital alineada con la marca.

## Qué resuelve el producto

- Centraliza la gestión de turnos y reduce la dependencia de mensajes manuales.
- Permite mostrar servicios, promociones, equipo y contenido de marca en la web pública.
- Da al negocio un panel interno para administrar citas, clientes, empleadas, productos y caja.
- Prepara la base para crecer con automatizaciones futuras como recordatorios, promociones configurables y más analítica.

## Módulos principales

- **Landing pública**: home one-page con hero, servicios, tratamientos, equipo, galería, promociones, testimonios y contacto.
- **Reservas online**: flujo guiado para que clientes registrados elijan servicio, fecha, horario y confirmen su cita.
- **Autenticación y roles**: acceso diferenciado para admin, staff y clientes.
- **Panel admin**: gestión operativa de servicios, citas, clientes, empleadas, productos, testimonios y caja.
- **Catálogo dinámico**: servicios y categorías alimentados desde base de datos.

## Stack

- **Backend**: PHP vanilla
- **Base de datos**: MySQL con PDO
- **Frontend**: Bootstrap 5.3, jQuery y SweetAlert2
- **Admin UI**: DataTables y Chart.js
- **Deploy actual**: Hostinger shared hosting

## Estructura del proyecto

```text
piel-morena-sys/
├── index.php           # landing pública
├── reservar.php        # flujo de reserva
├── admin/              # panel interno
├── api/                # endpoints y acciones AJAX
├── includes/           # bootstrap, auth y helpers
├── config/             # configuración runtime
├── database/           # schema, seeds y datos auxiliares
├── assets/             # CSS, JS, imágenes y vendor
└── docs/               # documentación funcional, técnica y operativa
```

## Documentación útil

- `docs/README.md`: mapa oficial de la documentación.
- `docs/negocio/README.md`: contexto real del negocio, operación y servicios.
- `docs/producto/README.md`: visión del producto y decisiones generales.
- `docs/contracts/README.md`: contratos funcionales por módulo.
- `docs/operacion/deploy/`: guías y runbooks de despliegue.

## Desarrollo local

```bash
php -S localhost:8000
php -l index.php
php -l includes/auth.php
```

Carga de base local:

```bash
mysql -u <user> -p < database/schema.sql
mysql -u <user> -p < database/seed.sql
```

## Deploy actual

- Producción oficial en `~/domains/pielmorenaestetica.com.ar/public_html`
- URL de producción: `https://pielmorenaestetica.com.ar/`
- Flujo vigente: `git push origin main` + `ssh` + `git pull origin main`
- Referencias operativas: `docs/operacion/deploy/`

## Estado del proyecto

El producto ya cubre el núcleo operativo del negocio: presencia pública, reservas, administración y caja. La siguiente etapa es ordenar mejor la documentación, consolidar criterios operativos y seguir cerrando pendientes de producto y despliegue.
