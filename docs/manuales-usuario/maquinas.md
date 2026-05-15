# Manual de Usuario - Maquinas Alquiladas

> Para: Mariangel (admin)
> Pantallas: Panel admin → Equipamiento → Maquinas / Alquileres
> Ultima revision: 2026-05-15

## Que es

Sistema para gestionar las maquinas que alquilas (laser, criolipo, velaslim, etc.) y los dias en que estan disponibles. Sirve para que tus clientas solo puedan reservar citas para servicios que dependen de una maquina en los dias que la tenes alquilada.

**Importante:** las **maquinas** son instrumentos fisicos. Para Naila (pestañas) y Nathalia (peluqueria) seguis usando **Jornadas** como hasta ahora — son cosas distintas.

## Flujo completo (3 pasos)

### 1. Crear tus maquinas

Entra a **Equipamiento → Maquinas** y tocá **Nueva Maquina**.

Campos:
- **Nombre:** algo claro y unico ("Laser Diodo", "Criolipo Plana", "VelaSlim").
- **Descripcion:** marca, modelo, lo que te ayude a identificarla.
- **Color:** para que se distinga en el calendario de alquileres.
- **Icono:** opcional, ayuda a identificarla visualmente.
- **Activa:** dejarla activa si la vas a usar.

Podes crear todas las maquinas que necesites. Si una se rompe o ya no la alquilas, **desactivala** (no la elimines) para conservar el historial.

### 2. Asignar maquinas a los servicios

Entra a **Servicios**, editá cada servicio que dependa de una maquina, y en el campo **"Maquina alquilada"** seleccioná la que corresponde.

Ejemplo:
- "Depilacion piernas completas" → maquina **Laser**.
- "Criolipolisis abdomen" → maquina **Criolipo Plana**.
- "Manicuria" → **Ninguna** (no necesita maquina).

Cuando un servicio tiene maquina, los campos legacy "Disponibilidad" y "Grupo de Jornada" se desactivan automaticamente (porque la maquina es lo que manda).

### 3. Cargar los alquileres del mes

Entra a **Equipamiento → Alquileres** y tocá **Nuevo Alquiler**.

- Seleccionas la maquina.
- Agregás una o varias fechas (multi-fecha, podes cargar todo el mes de una vez).
- Definis el rango horario en que la maquina esta disponible (ej. 10:00 a 18:00).
- Opcional: agregás notas.

Esos dias quedan visibles para que las clientas reserven citas con servicios de esa maquina.

## Vistas del calendario

- **Calendario:** vista mensual con todos los alquileres como eventos coloreados.
- **Tabla:** lista filtrable por maquina, estado, rango de fechas.

Para cambiar de vista, usa los botones arriba a la derecha (Tabla / Calendario).

## Editar y eliminar

- **Editar alquiler:** podes cambiar horarios y notas; no podes mover la fecha ni la maquina. Si necesitas eso, eliminá el alquiler y creá uno nuevo.
- **Eliminar alquiler:** si tiene citas activas, el sistema te muestra cuales se van a cancelar antes de confirmar.

## Diferencias con Jornadas

| | Maquinas | Jornadas |
|---|----------|----------|
| **Para que** | Instrumento fisico alquilado | Persona externa que asiste dias puntuales |
| **Ejemplos** | Laser, Criolipo, VelaSlim | Naila (pestañas), Nathalia (peluqueria) |
| **Como se asigna** | Por servicio (`servicios.id_maquina`) | Por categoria (`categorias.requiere_jornada=1`) |
| **Catalogo** | Dinamico — vos creas y eliminas | Fijo — categorias del sistema |

## Preguntas frecuentes

**Q: Si una maquina no la alquilo mas, ¿que hago?**
A: Desactivala (no la elimines). Asi se oculta del wizard pero los servicios siguen vinculados y el historial se conserva. Si volves a alquilarla mas adelante, la reactivas.

**Q: Una clienta intenta reservar y no ve fechas. ¿Por que?**
A: Probablemente el servicio tiene maquina asignada pero todavia no cargaste alquileres para los proximos 60 dias. Entrá a Alquileres y agregá fechas.

**Q: ¿Puedo tener la misma maquina alquilada dos veces el mismo dia?**
A: No. Una maquina tiene como maximo un alquiler por fecha (un rango horario unico).

**Q: ¿Puedo poner dos clientas en paralelo con la misma maquina?**
A: No. El sistema asume una clienta a la vez por maquina.

## Atajos

- Sidebar admin → Equipamiento → Maquinas / Alquileres / Jornadas.
- En el modal de Servicios, el selector "Maquina alquilada" es lo unico que necesitas tocar; los campos legacy se ignoran cuando hay maquina.
