# Manual de Servicios

> Para: administradoras y responsables de gestion
> Ultima actualizacion: abril 2026

---

## Que es un servicio

Un **servicio** es cada tratamiento o pack que el salon ofrece en el sistema.

Desde esta pantalla se define:

- nombre
- categoria
- precio
- duracion
- descripcion
- imagen
- disponibilidad de reserva
- si se muestra como destacado

Lo que se carga aca impacta en:

- el panel interno
- el wizard de reserva
- la web publica
- algunas metricas y reportes

---

## Quien usa esta pantalla

### Admin

Usa la pantalla **Servicios** para:

- crear servicios nuevos
- editar servicios existentes
- ajustar precio y duracion
- decidir si un servicio usa calendario normal o jornadas
- marcar servicios destacados
- eliminar servicios sin historial

### Staff

No administra el catalogo desde esta pantalla.

Le sirve entender este manual para:

- saber por que un servicio aparece o no en reservas
- identificar packs generados por promociones
- avisar a admin cuando un servicio tenga precio, duracion o disponibilidad incorrecta

---

## Como entrar

1. Ingresar al panel de administracion
2. En el menu lateral, abrir **Servicios**

Se abre el **Listado de Servicios**.

---

## Que muestra la tabla

La tabla principal muestra:

- **ID**
- **Servicio**
- **Disponibilidad**
- **Categoria**
- **Precio**
- **Duracion**
- **Acciones**

Ademas tiene:

- buscador
- paginacion
- selector de cantidad de registros

### Que significa cada disponibilidad

| Estado visible | Que significa |
|----------------|---------------|
| Normal | El servicio usa el calendario comun |
| Con jornadas | Solo se reserva en fechas cargadas como jornada |
| Con jornadas + "Fechas de X" | El servicio toma las jornadas de otro grupo operativo |

### Servicio destacado

Si un servicio esta marcado como destacado, aparece con una estrella en el listado.

Importante:

- el sistema permite **hasta 6 destacados**
- esos servicios son los que se priorizan en la seccion de servicios del sitio

---

## Crear un servicio nuevo

1. Hace clic en **Nuevo Servicio**
2. Completa los datos del modal
3. Guarda

### Campos del formulario

| Campo | Para que sirve |
|-------|----------------|
| Nombre | Nombre comercial del servicio |
| Categoria | Grupo al que pertenece |
| Disponibilidad | Define como se reserva |
| Descripcion | Texto interno/publico segun uso del sistema |
| Precio | Valor del servicio |
| Duracion | Minutos que bloquea en agenda |
| Imagen | Imagen asociada al servicio |
| Servicio destacado | Define si se prioriza en la web |

---

## Como elegir la disponibilidad

Este punto es clave porque afecta el flujo de reserva.

### Segun categoria

Es la opcion recomendada por defecto.

El sistema decide segun la configuracion de la categoria:

- si la categoria usa calendario normal
- o si requiere jornadas

Conviene usarla cuando no hace falta ninguna excepcion.

### Normal

Fuerza que el servicio use calendario libre.

Conviene cuando:

- el servicio puede tomarse cualquier dia habil
- no depende de equipo ni profesional externo en fechas puntuales

### Con Jornada

Fuerza que el servicio solo pueda reservarse en jornadas.

Al elegir esta opcion aparece **Grupo de Jornada**.

Ese grupo define de donde toma las fechas.

Ejemplos:

- `Depilacion`
- `Peluqueria`
- `Extensiones de Pestanas`
- `Tratamiento con equipo`

### Regla practica

Si tenes duda, usar primero **Segun categoria**.

Solo usar override manual cuando realmente haga falta cambiar el comportamiento normal del servicio.

---

## Imagen del servicio

Al elegir una imagen, el sistema la sube automaticamente y la deja asociada al servicio.

Recomendacion operativa:

- usar imagenes claras y prolijas
- evitar archivos demasiado pesados
- si una imagen se ve mal en la web, reemplazarla desde la edicion del servicio

---

## Editar un servicio

1. En la tabla, hace clic en **Editar**
2. Cambia lo necesario
3. Guarda

Normalmente se edita para corregir:

- nombre
- precio
- duracion
- categoria
- disponibilidad
- imagen
- destacado

### Que conviene revisar antes de guardar

- que el precio sea correcto
- que la duracion coincida con la operacion real
- que la categoria sea la adecuada
- que la disponibilidad no rompa el wizard de reservas

---

## Servicios generados por promociones

Algunos packs no se gestionan directamente como un servicio comun, sino que vienen de **Promociones**.

Cuando abrís uno de esos registros en **Editar**, aparece un badge informativo:

`Este servicio es generado por la promoción X`

### Importante

Ese aviso es para no editar la relacion del pack desde el lugar equivocado.

Si queres cambiar:

- que incluye el pack
- el precio pack
- la vigencia

tenes que ir a **Promociones**, no a **Servicios**.

---

## Eliminar un servicio

La eliminacion se hace desde el icono de borrar en la tabla.

### Pero hay una regla importante

El sistema **no permite eliminar definitivamente** un servicio si ya tiene citas asociadas.

Esto protege el historial.

### Entonces, cuando se puede borrar

Solo conviene borrar cuando:

- fue cargado por error
- todavia no tiene uso real
- no tiene citas vinculadas

Si el servicio ya fue usado, no intentar forzar el borrado.

---

## Casos comunes del dia a dia

### Quiero agregar un tratamiento nuevo

1. Crear servicio
2. Elegir categoria correcta
3. Cargar precio y duracion
4. Revisar disponibilidad
5. Guardar

### Un servicio no aparece bien en reservas

Revisar:

1. la disponibilidad elegida
2. si la categoria usa jornadas
3. si tiene grupo de jornada correcto
4. si el problema en realidad pertenece a una promo

### Quiero destacar un servicio en la web

1. Editar el servicio
2. Marcar **Servicio destacado**
3. Guardar

Si no te deja, revisar si ya hay 6 destacados activos.

### El precio o tiempo estaba mal

1. Editar servicio
2. Corregir **Precio** o **Duracion**
3. Guardar

---

## Problemas frecuentes

### No aparece el grupo de jornada

Solo aparece si la disponibilidad esta en:

`Con Jornada`

### Un servicio usa jornadas y no deberia

Revisar si:

- esta en `Con Jornada`
- esta en `Segun categoria` pero su categoria requiere jornada
- tiene configurado un grupo de jornada

### No puedo marcar otro destacado

Porque el limite actual es:

`maximo 6 servicios destacados`

### No me deja eliminar un servicio

Lo mas probable es que tenga citas asociadas.

En ese caso:

- no se elimina
- se debe revisar con criterio operativo antes de tocar nada

### Estoy editando un pack y veo un aviso de promocion

Ese pack viene de **Promociones**.

La gestion correcta del pack se hace desde esa seccion.

---

## Resumen rapido

| Quiero... | Hago... |
|-----------|---------|
| Crear un servicio nuevo | Servicios > Nuevo Servicio |
| Cambiar precio o duracion | Editar servicio |
| Cambiar como se reserva | Editar > Disponibilidad |
| Hacer que tome jornadas | Disponibilidad > Con Jornada |
| Priorizarlo en la web | Marcar Servicio destacado |
| Revisar si viene de una promo | Editar y mirar el badge informativo |
| Borrarlo | Eliminar, solo si no tiene citas asociadas |

