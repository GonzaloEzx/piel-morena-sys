# Arranque Local Rapido

> Estado: uso local
> Audiencia: desarrollo
> Fuente de verdad: no
> Ultima revision: 2026-04-04

## Comando que funciona en esta maquina

Para probar el sitio en local, levantarlo con el PHP de XAMPP:

```powershell
C:\xampp\php\php.exe -S localhost:8000
```

Luego abrir:

```text
http://localhost:8000
```

## Importante

No usar el `php` global de `C:\php\php.exe` para este proyecto.

En esta maquina ese PHP no tiene `mbstring` habilitado y rompe la home con:

```text
Call to undefined function mb_strimwidth()
```

## Requisito previo

Antes de levantar el sitio:

- `MySQL` debe estar corriendo en XAMPP
- la base local debe existir e importarse correctamente

## Resumen corto

Checklist rapido:

1. iniciar `MySQL` en XAMPP
2. correr `C:\xampp\php\php.exe -S localhost:8000`
3. abrir `http://localhost:8000`
