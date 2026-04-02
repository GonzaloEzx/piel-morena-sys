# Contrato 04 - Panel de Administracion

## Alcance

Modulo interno del sistema para operacion diaria del negocio.

Incluye:

- dashboard;
- layout compartido para `admin` y `empleado`;
- vistas de gestion interna;
- CRUDs principales;
- caja, mensajes y configuracion;
- acceso a reportes y analytics desde el panel.

No incluye como fuente funcional principal:

- definicion detallada de metricas y reportes analytics, que viven en `07-analytics-reportes.md`;
- reglas publicas de reserva, auth o landing, que viven en sus contratos correspondientes.

## Estado actual

### Implementado

- dashboard con estadisticas y graficos;
- soporte de layout para `admin` y `empleado`;
- CRUD de servicios, clientes, empleados y productos;
- gestion de citas con cambio de estado y alta manual desde panel;
- caja con movimientos, ventas, cierres e historico;
- mensajes de contacto;
- configuracion del negocio;
- galeria administrable;
- testimonios administrables;
- campana de notificaciones;
- acceso a reportes con filtros y exportacion CSV.

### Observaciones

- el panel ya no debe pensarse como "solo admin": comparte shell y navegacion para `admin` y `empleado`;
- las vistas y APIs de analytics se consumen desde el panel, pero su contrato funcional especifico sigue siendo `07-analytics-reportes.md`;
- `docs/bloque_admin_panel.md` queda deprecado y redirige a este contrato.

## Roles y proteccion

### `admin`

- acceso total al panel;
- acceso a dashboard, CRUDs, caja, mensajes, configuracion y reportes;
- puede gestionar citas, clientes, empleados, productos y servicios.

### `empleado`

- acceso limitado dentro del mismo layout;
- foco en `mis-citas.php` y `mi-horario.php`;
- puede operar solo las vistas y APIs habilitadas para su rol.

### Reglas activas

- `admin/includes/admin_header.php` admite `admin` y `empleado`;
- las vistas de gestion total permanecen reservadas a `admin`;
- cada API valida autenticacion y rol segun el caso.

## Estructura vigente

```text
admin/
├── index.php
├── includes/
│   ├── admin_header.php
│   └── admin_footer.php
├── views/
│   ├── servicios.php
│   ├── clientes.php
│   ├── citas.php
│   ├── empleados.php
│   ├── productos.php
│   ├── caja.php
│   ├── mensajes.php
│   ├── configuracion.php
│   ├── galeria.php
│   ├── testimonios.php
│   ├── reportes.php
│   ├── mis-citas.php
│   └── mi-horario.php
└── assets/
    ├── css/admin.css
    ├── css/premium-admin.css
    └── js/admin.js
```

## Entradas principales

- `admin/index.php`
- `admin/views/*.php`
- `admin/includes/admin_header.php`
- `admin/includes/admin_footer.php`
- `admin/assets/js/admin.js`

## APIs relacionadas

### Admin

- `api/admin/estadisticas.php`
- `api/admin/servicios.php`
- `api/admin/categorias.php`
- `api/admin/clientes.php`
- `api/admin/citas.php`
- `api/admin/empleados.php`
- `api/admin/productos.php`
- `api/admin/caja.php`
- `api/admin/mensajes.php`
- `api/admin/configuracion.php`
- `api/admin/galeria.php`
- `api/admin/testimonios.php`
- `api/admin/upload.php`

### Empleado

- `api/admin/mis-citas.php`
- `api/admin/mi-horario.php`

### Modulos auxiliares usados por el panel

- `api/caja/cierre_caja.php`
- `api/caja/registrar_venta.php`
- `api/caja/listar_movimientos.php`
- `api/caja/resumen.php`
- `api/analytics/ingresos.php`
- `api/analytics/servicios_populares.php`
- `api/analytics/tendencias.php`
- `api/notificaciones/listar.php`
- `api/notificaciones/marcar_leida.php`

## Modulos funcionales activos

- Dashboard con estadisticas y graficos.
- Gestion de servicios, clientes, empleados y productos.
- Gestion de citas con cambio de estado, asignacion de empleado y alta manual desde panel.
- Caja con movimientos, cierres e historico.
- Reportes con filtros por fecha y exportacion CSV.
- Mensajes de contacto.
- Configuracion del negocio.
- Galeria administrable.
- Testimonios administrables.
- Campana de notificaciones en topbar.
- Panel empleado con mis citas y mi horario.

## Frontera con otros contratos

- `03-sistema-reservas.md`: define el flujo funcional de reservas y citas del lado cliente.
- `05-control-caja.md`: define el comportamiento funcional especifico de caja.
- `06-notificaciones.md`: define notificaciones internas y emails.
- `07-analytics-reportes.md`: define metricas, filtros y exportacion de analytics/reportes.

Este contrato describe al panel como contenedor operativo y punto de acceso a esos modulos.

## Checklist

- [x] dashboard
- [x] layout compartido admin/empleado
- [x] CRUD principales
- [x] gestion de citas desde panel
- [x] caja
- [x] mensajes
- [x] configuracion
- [x] galeria
- [x] testimonios
- [x] reportes
- [x] campana de notificaciones
- [x] proteccion por rol
