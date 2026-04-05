# Manual de Citas

> Para: administradoras y staff de Piel Morena
> Ultima actualizacion: abril 2026

---

## Que es una cita

Una **cita** es un turno agendado para una clienta.

Puede venir de 2 lugares:

- desde la reserva web
- desde una carga manual hecha por admin o staff

Cada cita guarda:

- cliente
- servicio
- fecha
- horario
- empleada asignada
- estado
- notas internas

---

## Quien usa cada pantalla

### Admin

Usa la pantalla **Citas**.

Puede:

- ver todas las citas
- filtrar por fecha y estado
- cambiar entre vista tabla y calendario
- crear citas manualmente
- asignar empleada
- cambiar estados
- cancelar citas

### Staff

Usa la pantalla **Mis Citas**.

Puede:

- ver solo las citas que tiene asignadas
- filtrar por fecha y estado
- crear citas manualmente
- iniciar una cita
- completar una cita

No usa esta pantalla para administrar todas las citas del salon.

---

## Como entrar

### Para admin

1. Ingresar al panel
2. En el menu lateral, abrir **Citas**

### Para staff

1. Ingresar al panel con usuario de empleada
2. Abrir **Mis Citas**

---

## Estados posibles

| Estado | Que significa |
|--------|---------------|
| Pendiente | La cita fue creada pero todavia no se confirmo o no empezo |
| Confirmada | La cita ya esta tomada y lista para realizarse |
| En proceso | La clienta ya esta siendo atendida |
| Completada | El servicio ya termino |
| Cancelada | La cita se dio de baja y ya no debe atenderse |

### Regla practica

Lo ideal es mover la cita siguiendo este orden:

`Pendiente -> Confirmada -> En proceso -> Completada`

Si no va a realizarse:

`Pendiente/Confirmada -> Cancelada`

---

## Que muestra la pantalla Citas

La vista de admin tiene:

- filtro por **Fecha**
- filtro por **Estado**
- boton **Filtrar**
- boton **Limpiar**
- cambio de vista **Tabla / Calendario**
- boton **Nueva Cita**

### Vista tabla

La tabla muestra:

- numero de cita
- fecha
- horario
- cliente
- servicio
- empleada
- estado
- acciones

### Vista calendario

Sirve para ver el dia o la semana de manera visual.

Conviene usarla cuando:

- queres revisar ocupacion rapida
- necesitas detectar cruces
- queres abrir el detalle de una cita desde el calendario

---

## Crear una cita nueva

La carga manual sirve para:

- llamadas telefonicas
- clientas que escriben por WhatsApp
- clientas que llegan al salon
- reprogramaciones internas

### Paso a paso

1. Hace clic en **Nueva Cita**
2. Elegi si es:
   - **Cliente existente**
   - **Cliente nuevo**
3. Completa o busca los datos de la clienta
4. Selecciona el **Servicio**
5. Revisa el precio y la duracion que muestra el sistema
6. Elegi la **Fecha**
7. Espera que carguen los **Horarios disponibles**
8. Marca un horario
9. Si corresponde, asigna una empleada
10. Agrega notas si hace falta
11. Guarda con **Agendar Cita**

### Si la clienta ya existe

Busca por:

- nombre
- email
- telefono

Luego selecciona la coincidencia correcta.

### Si la clienta es nueva

Carga como minimo:

- nombre

Opcionalmente:

- email
- telefono

### Importante

El sistema solo deja agendar sobre horarios disponibles para ese servicio y esa fecha.

Si no aparecen horarios:

- revisar la fecha elegida
- revisar disponibilidad del servicio
- revisar jornadas si ese servicio depende de jornada

---

## Cambiar estado de una cita

### En admin

Desde **Citas**, cada fila tiene acciones para:

- cambiar estado
- cancelar

Al cambiar estado, admin puede tambien:

- asignar o corregir la empleada

### En staff

Desde **Mis Citas**, el staff puede:

- pasar la cita a **En proceso**
- pasar la cita a **Completada**

### Recomendacion operativa

- usar **Confirmada** cuando la cita ya quedo cerrada con la clienta
- usar **En proceso** cuando la atencion efectivamente empezo
- usar **Completada** solo cuando el servicio termino
- usar **Cancelada** cuando la clienta no vendra o el turno se anula

---

## Que pasa al completar una cita

Cuando una cita pasa a **Completada**, el sistema puede registrar automaticamente el ingreso en **Caja**.

Si eso ocurre, aparece un aviso indicando que el ingreso fue registrado.

### Buen criterio de uso

No completar una cita antes de tiempo.

Si se marca como completada por error:

- revisar de inmediato caja
- avisar a admin para corregir el movimiento si hace falta

---

## Cancelar una cita

La cancelacion se hace desde **Citas** en admin.

### Cuando conviene cancelar

- la clienta avisa que no viene
- hubo una reprogramacion y el turno viejo ya no sirve
- el turno se cargo mal

### Importante

Una cita cancelada:

- deja de contarse como turno activo
- no debe marcarse luego como completada

---

## Que puede hacer staff en Mis Citas

La vista **Mis Citas** esta pensada para operacion diaria.

Sirve para:

- ver las citas asignadas del dia
- filtrar por fecha
- usar el boton **Hoy**
- iniciar atenciones
- completar atenciones
- crear una nueva cita si hace falta

### Limite importante

Si el problema afecta:

- otra empleada
- una cancelacion
- una asignacion incorrecta
- una cita que no aparece

lo correcto es avisar a una admin.

---

## Casos comunes del dia a dia

### Llamo una clienta para sacar turno

1. Abrir **Nueva Cita**
2. Buscar si ya existe
3. Elegir servicio
4. Elegir fecha y horario
5. Guardar

### Llego una clienta sin reserva previa

1. Crear cita manual
2. Usar **Cliente nuevo** si hace falta
3. Agendar el horario real
4. Cuando empieza, pasar a **En proceso**
5. Al terminar, pasar a **Completada**

### La clienta cambia de dia

No conviene dejar la cita vieja activa.

Hacer esto:

1. Crear o reubicar la nueva cita
2. Cancelar la cita anterior si ya no se usara

### Quiero ver rapido todo lo del dia

Opciones:

- admin: usar filtro por fecha o vista calendario
- staff: usar **Mis Citas** y el boton **Hoy**

---

## Problemas frecuentes

### No encuentro a la clienta

Revisar si la busqueda se hizo por:

- nombre
- email
- telefono

Si no aparece, cargar como cliente nuevo.

### No me aparecen horarios

Revisar:

1. que haya un servicio elegido
2. que la fecha sea correcta
3. que el servicio este disponible
4. que no dependa de una jornada inexistente para ese dia

### La cita no aparece en Mis Citas

Normalmente pasa por una de estas razones:

- no esta asignada a esa empleada
- la fecha filtrada no coincide
- el estado o filtro aplicado la oculta

### Veo una cita completada por error

Avisar a admin para revisar:

- estado de la cita
- movimiento en caja

---

## Resumen rapido

| Quiero... | Hago... |
|-----------|---------|
| Ver todas las citas | Admin > Citas |
| Ver mis turnos asignados | Staff > Mis Citas |
| Agendar una cita manual | Nueva Cita |
| Revisar ocupacion visual | Vista Calendario |
| Empezar una atencion | Cambiar a En proceso |
| Cerrar una atencion | Cambiar a Completada |
| Dar de baja un turno | Cancelar desde admin |

