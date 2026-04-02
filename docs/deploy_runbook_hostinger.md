# Deploy Runbook Hostinger - Piel Morena

> Documento operativo vigente.
> Revision: 2026-04-02

## Objetivo

Mantener un flujo de deploy simple, repetible y alineado a una unica produccion:

- Produccion oficial: `https://pielmorenaestetica.com.ar`
- Checkout remoto operativo: `~/domains/pielmorenaestetica.com.ar/public_html`
- Branch operativo: `main`

## Contexto actual

- Hosting: Hostinger shared hosting
- Repositorio: `https://github.com/GonzaloEzx/piel-morena-sys.git`
- Base de datos: `u347774250_pielmorena`
- Metodo principal de publicacion: `git push origin main` + `ssh` + `git pull origin main`
- No existe un segundo dominio operativo documentado para este proyecto

## Metodo recomendado

### Opcion A - Deploy rapido por SSH + git pull

Es el flujo principal porque:

- no depende de reconfigurar Git Deployment en hPanel;
- permite ver salida inmediata del servidor;
- reduce puntos de falla para un proyecto mantenido manualmente.

### Opcion B - Git Deployment de Hostinger + Auto Deployment

Es una mejora posible para mas adelante, no el flujo base actual.

## Flujo diario recomendado

1. Hacer cambios en local.
2. Validar sintaxis y smoke de lo tocado.
3. Commit a `main`.
4. Push a GitHub.
5. Ejecutar deploy por SSH en `~/domains/pielmorenaestetica.com.ar/public_html`.
6. Verificar el sitio online.

## Comandos de trabajo

### Local

```powershell
git add .
git commit -m "tipo: descripcion"
git push origin main
```

### Remoto

```powershell
ssh -p 65002 u347774250@147.79.89.169
cd ~/domains/pielmorenaestetica.com.ar/public_html
git pull origin main
```

### Deploy directo en una linea

```powershell
ssh -p 65002 u347774250@147.79.89.169 "cd ~/domains/pielmorenaestetica.com.ar/public_html && git pull origin main"
```

### Verificacion rapida de branch en servidor

```powershell
ssh -p 65002 u347774250@147.79.89.169 "cd ~/domains/pielmorenaestetica.com.ar/public_html && git status --branch && git branch -vv"
```

## Checklist previo al deploy

- [ ] `php -l` sobre todos los PHP modificados
- [ ] revisar que no haya errores JS obvios en landing, auth o panel
- [ ] verificar login / logout
- [ ] verificar reservas si el cambio toca servicios, agenda o auth
- [ ] verificar panel admin si el cambio toca CRUD, caja o reportes
- [ ] revisar links absolutos o URLs hardcodeadas si el cambio toca assets o contenido

## Smoke test minimo post deploy

### Publico

- [ ] abre `https://pielmorenaestetica.com.ar/`
- [ ] abre `https://pielmorenaestetica.com.ar/login.php`
- [ ] abre `https://pielmorenaestetica.com.ar/registro.php`
- [ ] abre `https://pielmorenaestetica.com.ar/reservar.php`
- [ ] formulario de contacto visible

### Admin

- [ ] login admin correcto
- [ ] dashboard carga stats
- [ ] `admin/views/citas.php` abre
- [ ] `admin/views/caja.php` abre
- [ ] `admin/views/reportes.php` abre

### Servicios y galeria

- [ ] `api/servicios/listar.php` responde `success: true`
- [ ] la seccion `#tratamientos` muestra contenido vigente
- [ ] la galeria publica carga 6 slots sin errores de imagen
- [ ] si se actualizo la galeria, verificar los cambios desde admin y desde la home

## Galeria - criterio operativo

- La galeria publica toma archivos fijos en `assets/img/gallery/`:
  - `galeria-01.jpg`
  - `galeria-02.jpg`
  - `galeria-03.jpg`
  - `galeria-04.jpg`
  - `galeria-05.jpg`
  - `galeria-06.jpg`
- La fuente operativa para cambiarlas es `admin/views/galeria.php`.
- No editar manualmente esos archivos en un checkout remoto salvo emergencia.
- Si se sube una imagen nueva desde admin, pisa la anterior del mismo slot.

## Cuando usar File Manager

Solo en casos de emergencia:

- el SSH no responde;
- el checkout remoto quedo en estado inconsistente;
- hace falta subir un archivo puntual urgente.

No usarlo como flujo normal.

## Rollback rapido

### Metodo 1 - volver a un commit anterior

```powershell
ssh -p 65002 u347774250@147.79.89.169 "cd ~/domains/pielmorenaestetica.com.ar/public_html && git log --oneline -5"
```

Elegir el commit bueno y volver:

```powershell
ssh -p 65002 u347774250@147.79.89.169 "cd ~/domains/pielmorenaestetica.com.ar/public_html && git reset --hard <commit>"
```

### Metodo 2 - resincronizacion limpia contra GitHub

```powershell
ssh -p 65002 u347774250@147.79.89.169 "cd ~/domains/pielmorenaestetica.com.ar/public_html && git fetch origin && git reset --hard origin/main"
```

## Nota final

Hoy el deploy efectivo del proyecto es:

1. `git push origin main`
2. `ssh` al servidor
3. `git pull origin main` en `~/domains/pielmorenaestetica.com.ar/public_html`
4. smoke test corto sobre `https://pielmorenaestetica.com.ar`

Si mas adelante se quiere cero pasos manuales, el siguiente upgrade natural es dejar configurado Git Deployment de Hostinger sobre ese mismo path.
