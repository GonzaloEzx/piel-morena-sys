# Manual de Promociones

> Para: administradoras y staff de Piel Morena
> Ultima actualizacion: abril 2026

---

## Que es una promocion

Una **promocion** es un pack armado con **2 o mas servicios** que se ofrece con:

- un nombre propio
- un precio pack
- una vigencia
- una forma de reserva

En el sistema, cada promocion genera un **servicio-pack** que despues:

- aparece en la web publica
- puede verse en el wizard de reservas
- se puede editar como servicio, pero con una referencia read-only a la promo

---

## Quien usa esta pantalla

### Admin

Puede:

- crear promociones
- editar promociones
- desactivar promociones
- definir precio, fechas y servicios incluidos
- decidir si la promo usa calendario normal o jornadas

### Staff

No administra promociones desde esta pantalla.

Le sirve este manual para:

- entender por que un pack aparece o desaparece en la web
- identificar si un servicio fue generado por una promo
- saber cuando debe avisar a una admin para corregir una promo

---

## Como entrar a Promociones

1. Ingresar al panel de administracion
2. En el menu lateral buscar **Promociones**
3. Se abre la tabla con todos los packs/promos cargados

---

## Que muestra la tabla

Cada fila muestra:

- **ID**
- **Nombre**
- **Precio Pack**
- **Cantidad de servicios**
- **Vigencia**
- **Estado**

### Estados posibles

| Estado | Que significa |
|--------|---------------|
| Vigente | Ya puede verse en la web y reservarse |
| Programada | Todavia no empezo la fecha de inicio |
| Vencida | Ya paso la fecha fin |
| Desactivada | Se apago manualmente |

---

## Crear una promocion nueva

### Paso a paso

1. Hace clic en **Nueva Promocion**
2. Completa:

| Campo | Que poner |
|-------|-----------|
| Nombre del Pack | Nombre comercial que vera la clienta |
| Precio Pack | Precio final del combo |
| Descripcion | Explicacion corta del beneficio o del contenido |
| Duracion estimada | Tiempo total que se reservara |
| Fecha inicio | Desde cuando debe mostrarse |
| Fecha fin | Hasta cuando debe mostrarse |

3. En **Servicios incluidos en el pack**, marcar los servicios que forman parte de la promo
4. Elegir la **Disponibilidad**
5. Guardar

### Regla practica importante

Una promo debe armarse como un pack real, no como un servicio suelto renombrado.

Recomendacion operativa:

- siempre cargar promos con **2 o mas servicios**

---

## Como elegir la disponibilidad

La promo puede reservarse de 2 formas:

### Normal

Usa el calendario comun del salon.

Conviene cuando:

- el pack se puede hacer cualquier dia laboral
- no depende de una maquina o profesional que viene en fechas puntuales

### Con Jornada

Usa fechas especificas.

Conviene cuando:

- el pack depende de depilacion laser
- el pack depende de peluqueria o extensiones en dias puntuales
- el pack depende de equipos que no estan todos los dias

Si elegis **Con Jornada**, aparece el campo **Grupo de Jornada**.

Ese grupo define de donde toma las fechas.

Ejemplo:

- promo de depilacion -> grupo `Depilacion`
- promo de equipo -> grupo `Tratamiento con equipo`

---

## Que pasa cuando guardas

Al guardar una promo, el sistema:

1. crea o actualiza la promocion
2. genera un servicio-pack asociado
3. publica ese pack en la web si la promo esta vigente y activa

Ese pack despues aparece:

- en `Servicios` del admin
- en el wizard de reserva
- en la landing de promociones si esta vigente

---

## Editar una promocion

1. En la tabla, hace clic en el icono de **Editar**
2. Cambia lo que necesites:
   - servicios incluidos
   - precio
   - descripcion
   - fechas
   - disponibilidad
3. Guarda

### Que se actualiza automaticamente

Cuando editas la promo, tambien se actualiza el servicio-pack generado.

Eso incluye:

- nombre
- descripcion
- precio
- duracion
- disponibilidad

---

## Desactivar una promocion

Si una promo ya no debe mostrarse:

1. En la tabla, hace clic en el icono de **Desactivar**
2. Confirmar

### Que pasa despues

- la promo queda apagada
- el servicio-pack asociado desaparece del publico
- deja de verse en la landing
- deja de aparecer en el wizard de reserva

Esto es util cuando:

- la promo termino antes de tiempo
- hubo un error en el armado
- no queres que se siga ofreciendo

---

## Vigencia: como funciona

El sistema filtra solo las promos que esten:

- activas
- dentro de su fecha de vigencia

### Casos

| Caso | Que pasa |
|------|----------|
| Fecha inicio futura | No aparece todavia en la web |
| Fecha fin vencida | Deja de aparecer sola |
| Promo desactivada | Desaparece aunque la fecha siga vigente |

---

## Como lo ve la clienta

Si la promo esta vigente:

- aparece en la seccion **Promociones** del landing
- muestra un boton **Reservar Pack**
- ese boton lleva al wizard con el pack ya preseleccionado

Si la promo no esta vigente o esta desactivada:

- no aparece en el landing
- no aparece en el listado publico de servicios
- no aparece en el wizard

---

## Como identificar un servicio generado por promo

En la pantalla **Servicios**:

1. Busca el pack
2. Abre **Editar**
3. Arriba del modal vas a ver un badge informativo

El mensaje dice:

`Este servicio es generado por la promocion X`

### Importante

Ese dato es **solo informativo**.

La relacion entre promo y servicios **no se cambia desde Servicios**.
Si necesitas modificar el pack:

- volve a **Promociones**
- edita la promo desde ahi

---

## Casos comunes del dia a dia

### Quiero lanzar una promo para esta semana

1. Crear la promo
2. Fecha inicio: hoy
3. Fecha fin: ultimo dia de la semana
4. Guardar
5. Revisar en la landing que se vea

### Quiero cargar una promo para la semana que viene, pero no mostrarla hoy

1. Crear la promo
2. Fecha inicio: la fecha futura correspondiente
3. Guardar

Resultado:

- queda programada en admin
- no se ve todavia en la web

### La promo ya termino

No hace falta borrarla.

Opciones:

- dejar que venza por fecha
- o desactivarla manualmente

---

## Preguntas frecuentes

### Si cambio el precio de la promo, tambien cambia el precio del pack?

Si. El precio del servicio-pack se actualiza con el de la promo.

### Si la promo vence, hay que borrar el pack a mano?

No. El sistema deja de mostrarlo automaticamente al publico.

### Puedo editar el pack desde Servicios?

Se puede abrir y ver, pero la gestion correcta del pack es desde **Promociones**.

### Por que una promo no aparece en la web?

Revisar estas 3 cosas:

1. que no este desactivada
2. que la fecha inicio ya haya empezado
3. que la fecha fin no haya vencido

### Por que una promo no aparece en el wizard?

Normalmente por la misma razon:

- vencida
- programada
- desactivada

### Que hago si staff detecta que una promo esta mal?

Debe avisar a una admin para que revise:

- servicios incluidos
- precio
- vigencia
- disponibilidad

---

## Resumen rapido

| Quiero... | Hago... |
|-----------|---------|
| Crear una promo | Promociones > Nueva Promocion |
| Cambiar precio o servicios | Editar la promo |
| Sacarla de la web | Desactivar la promo |
| Que aparezca mas adelante | Cargar fecha inicio futura |
| Ver si un pack viene de promo | Servicios > Editar > mirar badge |
