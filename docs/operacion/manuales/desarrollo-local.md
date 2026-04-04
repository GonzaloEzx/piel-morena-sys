# Manual de Desarrollo Local

> Estado: vigente
> Audiencia: desarrollo, operacion, agentes
> Fuente de verdad: si, para setup local
> Relacion: manual operativo del entorno local
> Ultima revision: 2026-04-04

## Objetivo

Dejar `piel-morena-sys` funcionando en una maquina local sin tocar la operativa de produccion.

Este manual cubre:

- configuracion de entorno local;
- base de datos local con XAMPP o MySQL;
- arranque rapido del sitio;
- problemas frecuentes vistos en Windows.

## Archivos clave

- `config/config.php`
- `config/config.local.example.php`
- `config/config.local.php`
- `config/database.php`
- `database/schema.sql`
- `database/seed.sql`

## Como funciona hoy el doble entorno

El proyecto ya soporta dos modos sin romper produccion:

- `production`: para el dominio real;
- `development`: para `localhost`, `127.0.0.1`, `[::1]` o `php -S`.

Reglas actuales:

1. `config/config.php` detecta automaticamente si el request es local.
2. Si existe `config/config.local.php`, ese archivo puede forzar `environment` y `url_base`.
3. `config/database.php` usa defaults locales cuando `ENVIRONMENT === development`.
4. `config/database.php` tambien admite override por variables de entorno `PM_DB_*`.

## Configuracion recomendada

### 1. Config local opcional

Existe una plantilla versionada:

- `config/config.local.example.php`

Para usar override local:

1. copiar `config/config.local.example.php` como `config/config.local.php`
2. ajustar solo lo necesario

Ejemplo:

```php
<?php

return [
    "environment" => "development",
    "url_base" => "http://localhost:8000",
];
```

`config/config.local.php` queda ignorado por Git.

### 2. Base local por defecto

En modo `development`, `config/database.php` intenta conectarse a:

- host: `localhost`
- base: `piel_morena`
- usuario: `root`
- password: vacia

Si tu MySQL local usa otros valores, se pueden sobreescribir con:

- `PM_DB_HOST`
- `PM_DB_NAME`
- `PM_DB_USER`
- `PM_DB_PASS`

## Setup rapido con XAMPP

Esta es la opcion mas practica en Windows.

### Componentes minimos

Al instalar XAMPP alcanza con:

- `Apache`
- `MySQL`
- `PHP`
- `phpMyAdmin`

No hace falta instalar:

- `FileZilla FTP Server`
- `Mercury Mail Server`
- `Tomcat`
- `Perl`
- `Webalizer`
- `Fake Sendmail`

### Ruta recomendada

Instalar en:

```text
C:\xampp
```

Evitar `C:\Program Files\...` por las restricciones de UAC.

## Crear la base local

### Opcion A: phpMyAdmin

1. iniciar `MySQL` desde XAMPP
2. abrir `http://localhost/phpmyadmin`
3. crear la base `piel_morena`
4. importar `database/schema.sql`
5. importar `database/seed.sql`

### Opcion B: consola con el cliente de XAMPP

Si `mysql` no esta en el `PATH`, usar el ejecutable completo:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS piel_morena CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
```

Para importar desde `cmd.exe`:

```cmd
C:\xampp\mysql\bin\mysql.exe -u root piel_morena < database\schema.sql
C:\xampp\mysql\bin\mysql.exe -u root piel_morena < database\seed.sql
```

Para importar desde PowerShell:

```powershell
Get-Content database\schema.sql | C:\xampp\mysql\bin\mysql.exe -u root piel_morena
Get-Content database\seed.sql | C:\xampp\mysql\bin\mysql.exe -u root piel_morena
```

## Arranque del sitio

Con la base ya importada:

```powershell
php -S localhost:8000
```

Abrir despues:

```text
http://localhost:8000
```

## Variables de entorno opcionales

Si tu base local no usa `root` sin password:

```powershell
$env:PM_DB_HOST='localhost'
$env:PM_DB_NAME='piel_morena'
$env:PM_DB_USER='tu_usuario'
$env:PM_DB_PASS='tu_password'
php -S localhost:8000
```

## Validaciones utiles

```powershell
php -l config\config.php
php -l index.php
php -l includes\auth.php
```

Si queres validar que la base exista:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "SHOW DATABASES;"
```

## Problemas frecuentes

### `mysql` no es reconocido

Si PowerShell devuelve:

```text
mysql : The term 'mysql' is not recognized...
```

significa una de estas dos cosas:

- no hay MySQL/MariaDB instalado localmente;
- el cliente no esta en el `PATH`.

Solucion mas simple para este proyecto:

- usar `C:\xampp\mysql\bin\mysql.exe`
- o trabajar desde `phpMyAdmin`

### PowerShell no acepta `<`

Si PowerShell devuelve:

```text
The '<' operator is reserved for future use.
```

no es un error de MySQL; es sintaxis del shell.

Opciones correctas:

- usar `cmd.exe` para imports con `<`
- o usar `Get-Content ... | mysql.exe` en PowerShell

### XAMPP detecta rutas viejas a otra unidad

Caso visto en esta maquina:

- instalacion actual en `C:\xampp`
- servicios viejos apuntando a `D:\xampp`

Sintomas tipicos:

- `Found Path: D:\xampp\...`
- `Expected Path: C:\xampp\...`

Primero revisar servicios viejos:

```powershell
Get-Service | Where-Object { $_.Name -match 'mysql|mariadb|apache|xampp' -or $_.DisplayName -match 'mysql|mariadb|apache|xampp' }
```

Inspeccionar la ruta registrada:

```powershell
sc.exe qc mysql
sc.exe qc Apache2.4
```

Si un servicio apunta a una ruta vieja, eliminar ese servicio residual como administrador:

```powershell
sc.exe delete mysql
sc.exe delete Apache2.4
```

Usar el nombre real del servicio que devuelva `Get-Service`.

Despues:

1. cerrar XAMPP
2. abrir XAMPP otra vez
3. iniciar `MySQL`

## Resultado esperado

Si el entorno local quedo bien:

- `php -S localhost:8000` responde sin usar la base de Hostinger;
- `ENVIRONMENT` queda en `development`;
- el sitio conecta contra `piel_morena` local;
- los errores de PDO muestran detalle solo en local.
