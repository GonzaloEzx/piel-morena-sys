# Manual de Jornadas

> Para: administradoras y staff de Piel Morena
> Ultima actualizacion: abril 2026

---

## Que es una jornada

Una **jornada** es un dia especifico en el que un servicio esta disponible para reservas. Se usa para servicios que no se ofrecen todos los dias, sino en fechas puntuales.

**Ejemplos reales:**

- La maquina de **depilacion laser** se alquila por dia. Si este mes se alquila el 9, 10, 16 y 21 de abril, esas son las 4 jornadas de depilacion.
- **Naila** (extensiones de pestanas) viene solo ciertos dias al salon. Esos dias son jornadas.
- **Nathalia** (peluqueria) trabaja dias puntuales. Idem.
- Los equipos de **Criolipólisis** y **VelaSlim** estan disponibles solo ciertos dias.

Sin jornada creada para una fecha, **las clientas no pueden reservar** ese servicio en esa fecha. Asi de simple.

---

## Que categorias usan jornadas

No todos los servicios necesitan jornadas. Solo estas 4 categorias:

| Categoria | Por que usa jornadas |
|-----------|---------------------|
| Depilacion | Maquina laser alquilada por dia |
| Extensiones de Pestanas | Naila asiste dias puntuales |
| Peluqueria | Nathalia asiste dias puntuales |
| Tratamiento con equipo | Equipos Crio/VelaSlim por dia |

El resto de servicios (Cejas y Pestanas, Tratamientos Faciales, Unas, etc.) funcionan con el calendario normal de todos los dias laborales.

---

## Como entrar a Jornadas

1. Entra al **panel de administracion** (`/admin`)
2. En el menu lateral (sidebar) busca **Jornadas** (icono de calendario)
3. Se abre la pantalla con el calendario y la tabla

---

## Crear jornadas nuevas

Este es el uso mas comun: cargar las fechas del mes en las que va a haber servicio.

### Paso a paso

1. Hace clic en el boton **"+ Nueva Jornada"** (arriba a la derecha)
2. Se abre un formulario con estos campos:

| Campo | Que poner | Ejemplo |
|-------|-----------|---------|
| Categoria | Elegir de la lista | "Depilacion" |
| Fechas | Agregar una o varias fechas | 9/04, 10/04, 16/04, 21/04 |
| Hora inicio | Horario de apertura de ese dia | 08:00 (viene por defecto) |
| Hora fin | Horario de cierre de ese dia | 20:00 (viene por defecto) |
| Notas | Opcional, para recordatorios | "Maquina modelo XL" |

### Como agregar varias fechas

No hace falta crear una jornada por dia. Podes cargar todas juntas:

1. Selecciona una fecha en el campo de fecha
2. Hace clic en **"+ Agregar"**
3. Aparece un chip con esa fecha (ej: "Mie 09/04/2026")
4. Repeti para cada fecha que necesites
5. Si te equivocaste, hace clic en la **X** del chip para sacarlo
6. Cuando estan todas las fechas, hace clic en **"Crear Jornada(s)"**

### Que pasa despues

- El sistema crea todas las jornadas de una vez
- Si alguna fecha ya tenia jornada para esa categoria, la saltea y te avisa
- Las jornadas aparecen en el calendario y en la tabla

### Ejemplo practico

> Mari alquila la maquina laser para el 9, 10, 16 y 21 de abril.
>
> 1. Abre Jornadas > Nueva Jornada
> 2. Categoria: "Depilacion"
> 3. Agrega las 4 fechas
> 4. Hora: deja 08:00 a 20:00 (el default)
> 5. Crea
>
> Resultado: las clientas ya pueden reservar depilacion en esas 4 fechas desde la web.

---

## Ver las jornadas creadas

Hay dos formas de verlas:

### Vista Calendario

- Es la vista por defecto al entrar
- Muestra un calendario mensual con las jornadas como bloques de color
- Cada categoria tiene su color
- Si una jornada tiene citas reservadas, lo indica entre parentesis (ej: "Depilacion (3 citas)")
- Hace clic en una jornada para editarla

### Vista Tabla

- Hace clic en el boton **"Tabla"** (arriba a la derecha)
- Muestra un listado con columnas: fecha, categoria, horario, citas reservadas, estado, notas
- Podes buscar, ordenar y paginar como en cualquier otra tabla del panel

### Filtros

Arriba de ambas vistas hay filtros para encontrar rapidamente lo que buscas:

- **Categoria:** ver solo una categoria (ej: solo Depilacion)
- **Estado:** Activa o Cancelada (por defecto muestra solo Activas)
- **Desde / Hasta:** rango de fechas

---

## Editar una jornada

Si necesitas cambiar el horario de una jornada ya creada:

1. **Desde el calendario:** hace clic en la jornada
2. **Desde la tabla:** hace clic en el icono de lapiz
3. Se abre el formulario de edicion con:
   - Info de la jornada (categoria, fecha, citas reservadas)
   - Hora inicio y hora fin
   - Notas
4. Modifica lo que necesites y hace clic en **"Guardar"**

> **Importante:** no se puede cambiar la fecha ni la categoria de una jornada ya creada. Si necesitas otra fecha, crea una jornada nueva y cancela la anterior.

---

## Cancelar una jornada

Si un dia se cae (la maquina no llega, Naila no puede venir, etc.):

1. En la tabla, hace clic en el icono de **X roja** de la jornada
2. El sistema te muestra un resumen:
   - Que jornada estas por cancelar
   - **Cuantas citas hay reservadas** en esa fecha para esa categoria
   - El detalle de cada cita (nombre de la clienta, horario, servicio)
3. Si estas segura, confirma la cancelacion

### Que pasa con las citas

**Las citas NO se cancelan automaticamente.** Cancelar la jornada solo evita nuevas reservas. Las citas que ya existian siguen en el sistema.

Esto es a proposito: te da tiempo para contactar a las clientas, reprogramar o gestionar cada caso individualmente desde la pantalla de Citas.

### Ejemplo practico

> El 16 de abril no llega la maquina laser.
>
> 1. Abre Jornadas
> 2. Busca la jornada del 16/04 - Depilacion
> 3. Hace clic en cancelar
> 4. Ve que hay 2 citas reservadas: Maria a las 10:00 y Laura a las 14:00
> 5. Confirma la cancelacion
> 6. Va a la pantalla de Citas y contacta a Maria y Laura para reprogramar

---

## Como lo ve la clienta

Cuando una clienta entra a **Reservar Cita** en la web y elige un servicio de depilacion (o cualquier categoria con jornada):

1. En vez del calendario comun, ve una grilla con las **fechas disponibles**
2. Cada fecha aparece como una tarjeta con el dia de la semana, numero y mes
3. Solo aparecen las fechas que tienen jornada activa
4. La clienta elige una fecha y despues elige el horario disponible
5. El resto del proceso es igual (confirmar y listo)

Si no hay jornadas creadas para los proximos dias, la clienta ve un mensaje diciendo que no hay fechas disponibles.

---

## Preguntas frecuentes

### Puedo crear jornadas para varias categorias a la vez?

No. Cada creacion es para una categoria. Si necesitas crear jornadas para Depilacion y para Peluqueria, hace dos creaciones separadas (pueden tener las mismas fechas sin problema).

### Que pasa si creo una jornada en un dia que ya tiene una?

El sistema la detecta como duplicada, la saltea y te avisa. No se crea dos veces.

### Puedo crear jornadas en fin de semana?

Si. Las jornadas pueden caer cualquier dia, incluso domingos. El sistema ignora la configuracion de "dias laborales" cuando hay jornada activa.

### Que horario tienen las jornadas?

Por defecto 08:00 a 20:00 (12 horas). Podes cambiarlo al crear o editar la jornada. Los turnos disponibles para las clientas se generan dentro de ese rango.

### Si cancelo una jornada, puedo reactivarla?

No directamente. Tendrias que crear una jornada nueva para esa misma fecha y categoria.

### Depilacion Laser y los combos/packs de depilacion tambien necesitan jornada?

Si. Todo lo que esta en la categoria "Depilacion" requiere jornada, incluyendo combos y packs. Todos se hacen con la misma maquina.

### Y si la clienta quiere reservar depilacion pero no hay jornadas cargadas?

Ve un mensaje que dice "No hay fechas disponibles" y no puede reservar. Por eso es importante cargar las jornadas con anticipacion, idealmente al principio de cada mes.

---

## Resumen rapido

| Quiero... | Hago... |
|-----------|---------|
| Cargar las fechas del mes | Nueva Jornada > elegir categoria > agregar fechas > crear |
| Ver que jornadas hay | Entrar a Jornadas y mirar el calendario o la tabla |
| Cambiar el horario de un dia | Editar la jornada desde el calendario o la tabla |
| Cancelar un dia | Cancelar la jornada > gestionar citas afectadas manualmente |
| Saber cuantas citas hay en un dia | Ver la columna "Citas" en la tabla o el numero entre parentesis en el calendario |
