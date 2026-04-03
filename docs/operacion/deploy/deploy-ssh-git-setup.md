# Deploy con SSH + Git - Setup Real

> Estado: vigente
> Audiencia: operacion, desarrollo, agentes
> Fuente de verdad: si, para setup de deploy por SSH
> Relacion: configuracion tecnica del checkout remoto
> Ultima revision: 2026-04-03

## Datos del servidor

| Campo | Valor |
|---|---|
| Proveedor | Hostinger |
| Servidor | `br-asc-web853` |
| IP | `147.79.89.169` |
| Puerto SSH | `65002` |
| Usuario SSH | `u347774250` |
| Document root | `~/domains/pielmorenaestetica.com.ar/public_html` |
| URL produccion | `https://pielmorenaestetica.com.ar` |

## Conexion SSH

```powershell
ssh -p 65002 u347774250@147.79.89.169
```

## Deploy actual del proyecto

### Paso 1

```powershell
git add .
git commit -m "tipo: descripcion"
git push origin main
```

### Paso 2

```powershell
ssh -p 65002 u347774250@147.79.89.169 "cd ~/domains/pielmorenaestetica.com.ar/public_html && git pull origin main"
```

## Repo remoto

```text
https://github.com/GonzaloEzx/piel-morena-sys.git
```

## Setup Git hecho en servidor

```bash
cd ~/domains/pielmorenaestetica.com.ar/public_html
git init
git remote add origin https://github.com/GonzaloEzx/piel-morena-sys.git
git fetch origin
git reset --hard origin/main
```

Fecha de configuracion inicial del checkout del dominio principal: `2026-03-30`.

## Estado operativo esperado

- rama local del servidor: `main`
- upstream: `origin/main`
- `origin/HEAD`: `main`
- unico checkout operativo documentado: `~/domains/pielmorenaestetica.com.ar/public_html`

## Comandos utiles

### Ver ultimo estado

```powershell
ssh -p 65002 u347774250@147.79.89.169 "cd ~/domains/pielmorenaestetica.com.ar/public_html && git status"
```

### Ver ultimos commits

```powershell
ssh -p 65002 u347774250@147.79.89.169 "cd ~/domains/pielmorenaestetica.com.ar/public_html && git log --oneline -5"
```

### Reforzar sincronizacion con remoto

```powershell
ssh -p 65002 u347774250@147.79.89.169 "cd ~/domains/pielmorenaestetica.com.ar/public_html && git fetch origin && git reset --hard origin/main"
```

### Confirmar rama y tracking

```powershell
ssh -p 65002 u347774250@147.79.89.169 "cd ~/domains/pielmorenaestetica.com.ar/public_html && git status --branch && git branch -vv && git remote show origin"
```

## Galeria - nota tecnica

- La galeria publica no depende del deploy de otro dominio ni de otro checkout.
- Las imagenes visibles salen de `assets/img/gallery/` del dominio principal.
- El recambio normal se hace desde el panel admin en `admin/views/galeria.php`.

## Nota Hostinger

Hostinger permite dos caminos validos para este proyecto:

1. usar SSH y `git pull` manual;
2. usar Git Deployment en hPanel con webhook.

Mientras el sitio siga en `~/domains/pielmorenaestetica.com.ar/public_html`, el flujo por SSH es el mas directo para publicar y verificar.
