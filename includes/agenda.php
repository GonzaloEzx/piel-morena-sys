<?php
/**
 * Piel Morena - Cálculo de turnos y reserva
 *
 * Única fuente de disponibilidad: la usan la reserva online, la agenda
 * pública de la landing, la gestión del turno por la clienta y el alta
 * desde el panel.
 *
 * Modos:
 *   profesional → el servicio tiene profesionales asignadas
 *                 (empleados_servicios). Un horario está libre si alguna
 *                 de ellas trabaja en esa franja (horarios) y no tiene
 *                 otra cita superpuesta.
 *   salon       → el servicio no tiene profesionales asignadas: cualquier
 *                 cita superpuesta bloquea el horario (comportamiento v1).
 *
 * En ambos modos rigen los bloqueos de máquina, alquiler y jornada.
 */

const PM_ESTADOS_ACTIVOS = ['pendiente', 'confirmada', 'en_proceso'];

/**
 * Datos de un día para un servicio. Devuelve ['mensaje' => string] si ese
 * día no se atiende, o el contexto que usan pm_franja_libre() y
 * pm_turnos_del_dia().
 *
 * $id_empleado: si viene, solo cuenta esa profesional.
 * $ocupados_extra: franjas [inicio, fin] en timestamps (Google Calendar);
 * cada una ocupa a una profesional, como una cita sin asignar.
 * $excluir_cita: id de una cita que no cuenta como ocupada (reprogramar).
 */
function pm_contexto_dia(PDO $db, array $servicio, string $fecha, array $ocupados_extra = [], ?int $id_empleado = null, int $excluir_cita = 0): array
{
    $cerrado = fn(string $mensaje) => ['mensaje' => $mensaje];
    $id_servicio = (int) ($servicio['id'] ?? 0);

    // ── Ventana de máquina o jornada ──
    // 1. servicios.id_maquina IS NOT NULL → requiere alquiler activo de esa maquina.
    // 2. Sino, si categoria tiene requiere_jornada=1 → requiere jornada activa (personal externo).
    // 3. Sino → calendario libre.
    $ventana_activa = null;
    $id_maquina = null;
    if ($id_servicio > 0) {
        $stmt_check = $db->prepare(
            "SELECT s.id_maquina, s.id_categoria,
                    m.nombre AS maquina_nombre, m.activo AS maquina_activa,
                    c.requiere_jornada AS cat_requiere, c.nombre AS categoria_nombre
             FROM servicios s
             LEFT JOIN maquinas m ON s.id_maquina = m.id
             LEFT JOIN categorias_servicios c ON s.id_categoria = c.id
             WHERE s.id = ? LIMIT 1"
        );
        $stmt_check->execute([$id_servicio]);
        $rcheck = $stmt_check->fetch();

        if (!empty($rcheck['id_maquina'])) {
            if (!$rcheck['maquina_activa']) {
                return $cerrado('La maquina ' . $rcheck['maquina_nombre'] . ' esta inactiva');
            }
            $id_maquina = (int) $rcheck['id_maquina'];

            $stmt_a = $db->prepare(
                "SELECT hora_inicio, hora_fin FROM maquinas_alquileres
                 WHERE id_maquina = ? AND fecha = ? AND estado = 'activo'
                 LIMIT 1"
            );
            $stmt_a->execute([$id_maquina, $fecha]);
            $ventana_activa = $stmt_a->fetch();

            if (!$ventana_activa) {
                return $cerrado('No hay alquiler activo de la maquina ' . $rcheck['maquina_nombre'] . ' para esta fecha');
            }
        } elseif (!empty($rcheck['cat_requiere'])) {
            $stmt_j = $db->prepare(
                "SELECT hora_inicio, hora_fin FROM jornadas
                 WHERE id_categoria = ? AND fecha = ? AND estado = 'activa'
                 LIMIT 1"
            );
            $stmt_j->execute([(int) $rcheck['id_categoria'], $fecha]);
            $ventana_activa = $stmt_j->fetch();

            if (!$ventana_activa) {
                return $cerrado('No hay jornada programada para ' . $rcheck['categoria_nombre'] . ' en esta fecha');
            }
        }
    }

    // ── Configuración del negocio ──
    $stmt = $db->prepare("SELECT valor FROM configuracion WHERE clave = ?");
    $stmt->execute(['horario_apertura']);
    $apertura = $stmt->fetchColumn() ?: '09:00';
    $stmt->execute(['horario_cierre']);
    $cierre = $stmt->fetchColumn() ?: '20:00';
    $stmt->execute(['dias_laborales']);
    $dias_laborales = $stmt->fetchColumn() ?: '1,2,3,4,5,6';
    $stmt->execute(['intervalo_citas']);
    $intervalo = intval($stmt->fetchColumn() ?: 30);

    // Si hay ventana activa (alquiler de maquina o jornada), sus horarios mandan
    if ($ventana_activa) {
        $apertura = substr($ventana_activa['hora_inicio'], 0, 5);
        $cierre   = substr($ventana_activa['hora_fin'], 0, 5);
    }

    // Verificar si el día de la semana es laboral (1=Lun, 7=Dom)
    $dia_semana = intval(date('N', strtotime($fecha)));
    $dias_array = array_map('intval', explode(',', $dias_laborales));

    // Si hay ventana activa (alquiler o jornada), el dia laboral no aplica
    if (!$ventana_activa && !in_array($dia_semana, $dias_array)) {
        return $cerrado('No atendemos este día');
    }

    // ── Profesionales del servicio y sus franjas de hoy ──
    $profesionales = [];   // id => [[ini, fin], ...] en 'H:i'
    $servicios_de = [];    // id => [id_servicio => true]
    if ($id_servicio > 0) {
        $stmt = $db->prepare(
            "SELECT u.id FROM empleados_servicios es
             JOIN usuarios u ON u.id = es.id_empleado
             WHERE es.id_servicio = ? AND u.activo = 1 AND u.rol IN ('empleado', 'admin')
             ORDER BY u.nombre"
        );
        $stmt->execute([$id_servicio]);
        $ids = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

        if ($id_empleado !== null && $ids && !in_array($id_empleado, $ids, true)) {
            return $cerrado('Esa profesional no realiza este servicio');
        }

        if ($ids) {
            $marcas = implode(',', array_fill(0, count($ids), '?'));

            $stmt = $db->prepare(
                "SELECT id_empleado, dia_semana, hora_inicio, hora_fin FROM horarios
                 WHERE activo = 1 AND id_empleado IN ($marcas)"
            );
            $stmt->execute($ids);
            $con_horario = [];
            foreach ($ids as $id) {
                $profesionales[$id] = [];
            }
            foreach ($stmt->fetchAll() as $h) {
                $id = (int) $h['id_empleado'];
                $con_horario[$id] = true;
                if ((int) $h['dia_semana'] === $dia_semana) {
                    $profesionales[$id][] = [substr($h['hora_inicio'], 0, 5), substr($h['hora_fin'], 0, 5)];
                }
            }
            // Sin horario cargado → trabaja en el horario del local
            foreach ($ids as $id) {
                if (empty($con_horario[$id])) {
                    $profesionales[$id][] = [$apertura, $cierre];
                }
            }

            $stmt = $db->prepare("SELECT id_empleado, id_servicio FROM empleados_servicios WHERE id_empleado IN ($marcas)");
            $stmt->execute($ids);
            foreach ($stmt->fetchAll() as $r) {
                $servicios_de[(int) $r['id_empleado']][(int) $r['id_servicio']] = true;
            }
        }
    }

    // ── Citas activas del día ──
    $marcas_estado = implode(',', array_fill(0, count(PM_ESTADOS_ACTIVOS), '?'));
    $stmt = $db->prepare(
        "SELECT c.id, c.id_servicio, c.id_empleado, c.hora_inicio, c.hora_fin, s.id_maquina
         FROM citas c JOIN servicios s ON s.id = c.id_servicio
         WHERE c.fecha = ? AND c.id <> ? AND c.estado IN ($marcas_estado)
         ORDER BY c.hora_inicio"
    );
    $stmt->execute(array_merge([$fecha, $excluir_cita], PM_ESTADOS_ACTIVOS));
    $citas = [];
    foreach ($stmt->fetchAll() as $c) {
        $citas[] = [
            'ini'         => substr($c['hora_inicio'], 0, 5),
            'fin'         => substr($c['hora_fin'], 0, 5),
            'id_servicio' => (int) $c['id_servicio'],
            'id_empleado' => $c['id_empleado'] !== null ? (int) $c['id_empleado'] : null,
            'id_maquina'  => $c['id_maquina'] !== null ? (int) $c['id_maquina'] : null,
        ];
    }
    foreach ($ocupados_extra as [$o_ini, $o_fin]) {
        $citas[] = [
            'ini' => date('H:i', max($o_ini, strtotime("$fecha 00:00"))),
            'fin' => $o_fin >= strtotime("$fecha 00:00 +1 day") ? '24:00' : date('H:i', $o_fin),
            'id_servicio' => 0, 'id_empleado' => null, 'id_maquina' => null,
        ];
    }

    return [
        'mensaje'       => null,
        'fecha'         => $fecha,
        'apertura'      => $apertura,
        'cierre'        => $cierre,
        'intervalo'     => $intervalo,
        'duracion'      => max((int) ($servicio['duracion_minutos'] ?? 0), $intervalo),
        'modo'          => $profesionales ? 'profesional' : 'salon',
        'profesionales' => $profesionales,
        'servicios_de'  => $servicios_de,
        'id_empleado'   => $id_empleado,
        'id_maquina'    => $id_maquina,
        'citas'         => $citas,
    ];
}

/**
 * Profesionales libres para la franja [$ini, $fin) ('H:i').
 * Modo salon: devuelve [0] si está libre. En ambos modos, [] = ocupado.
 * Con $ctx['id_empleado'] solo puede devolver esa profesional.
 */
function pm_franja_libre(array $ctx, string $ini, string $fin): array
{
    if ($ini < $ctx['apertura'] || $fin > $ctx['cierre']) {
        return [];
    }

    $superpuestas = array_filter($ctx['citas'], fn($c) => $ini < $c['fin'] && $fin > $c['ini']);

    if ($ctx['id_maquina'] !== null) {
        foreach ($superpuestas as $c) {
            if ($c['id_maquina'] === $ctx['id_maquina']) {
                return [];
            }
        }
    }

    if ($ctx['modo'] === 'salon') {
        return $superpuestas ? [] : [0];
    }

    // Profesionales que trabajan toda la franja y no tienen cita asignada encima
    $libres = [];
    foreach ($ctx['profesionales'] as $id => $franjas) {
        $trabaja = false;
        foreach ($franjas as [$f_ini, $f_fin]) {
            if ($ini >= $f_ini && $fin <= $f_fin) {
                $trabaja = true;
                break;
            }
        }
        if (!$trabaja) {
            continue;
        }
        foreach ($superpuestas as $c) {
            if ($c['id_empleado'] === $id) {
                continue 2;
            }
        }
        $libres[] = $id;
    }

    // Cada cita sin asignar (o evento de Google) ocupa a una profesional
    // que pueda hacerla; se descuentan primero las que no pidió la clienta.
    $pedida = $ctx['id_empleado'];
    foreach ($superpuestas as $c) {
        if ($c['id_empleado'] !== null) {
            continue;
        }
        $orden = $libres;
        usort($orden, fn($a, $b) => ($a === $pedida) <=> ($b === $pedida));
        foreach ($orden as $id) {
            if ($c['id_servicio'] === 0 || !empty($ctx['servicios_de'][$id][$c['id_servicio']])) {
                $libres = array_values(array_diff($libres, [$id]));
                break;
            }
        }
    }

    if ($pedida !== null) {
        return in_array($pedida, $libres, true) ? [$pedida] : [];
    }
    return $libres;
}

/**
 * Turnos de un día para un servicio.
 *
 * $servicio: fila con `id` y `duracion_minutos`. Con id 0 es un turno
 * genérico: horario del local, sin reglas de máquina ni de jornada.
 *
 * Devuelve:
 *   mensaje  → por qué no hay turnos (null si el día se atiende)
 *   franja   → [apertura, cierre] que rigió ese día (null si no se atiende)
 *   turnos   → [['hora_inicio', 'hora_fin', 'libre', 'profesionales'], ...]
 */
function pm_turnos_del_dia(PDO $db, array $servicio, string $fecha, array $ocupados_extra = [], ?int $id_empleado = null, int $excluir_cita = 0): array
{
    $ctx = pm_contexto_dia($db, $servicio, $fecha, $ocupados_extra, $id_empleado, $excluir_cita);
    if ($ctx['mensaje'] !== null) {
        return ['mensaje' => $ctx['mensaje'], 'franja' => null, 'turnos' => []];
    }

    $intervalo = $ctx['intervalo'];
    $hora_actual = strtotime($ctx['apertura']);
    $hora_cierre = strtotime($ctx['cierre']);

    // Si es hoy, no mostrar turnos pasados (con margen de 30 min)
    if ($fecha === date('Y-m-d')) {
        $ahora_mas_margen = strtotime('+30 minutes');
        if ($ahora_mas_margen > $hora_actual) {
            $minutos = intval(date('i', $ahora_mas_margen));
            $redondeo = ceil($minutos / $intervalo) * $intervalo;
            $hora_actual = strtotime(date('H', $ahora_mas_margen) . ':00') + ($redondeo * 60);
        }
    }

    $slots = [];
    while ($hora_actual + ($ctx['duracion'] * 60) <= $hora_cierre) {
        $inicio = date('H:i', $hora_actual);
        $fin    = date('H:i', $hora_actual + ($ctx['duracion'] * 60));
        $libres = pm_franja_libre($ctx, $inicio, $fin);

        $slots[] = [
            'hora_inicio'   => $inicio,
            'hora_fin'      => $fin,
            'libre'         => $libres !== [],
            'profesionales' => $ctx['modo'] === 'profesional' ? $libres : [],
        ];

        $hora_actual += $intervalo * 60;
    }

    return ['mensaje' => null, 'franja' => [$ctx['apertura'], $ctx['cierre']], 'turnos' => $slots];
}

/**
 * Profesionales que hacen un servicio: [['id', 'nombre'], ...].
 */
function pm_profesionales_servicio(PDO $db, int $id_servicio): array
{
    $stmt = $db->prepare(
        "SELECT u.id, TRIM(CONCAT(u.nombre, ' ', u.apellidos)) AS nombre, u.foto
         FROM empleados_servicios es
         JOIN usuarios u ON u.id = es.id_empleado
         WHERE es.id_servicio = ? AND u.activo = 1 AND u.rol IN ('empleado', 'admin')
         ORDER BY u.nombre"
    );
    $stmt->execute([$id_servicio]);
    return array_map(fn($r) => ['id' => (int) $r['id'], 'nombre' => $r['nombre'], 'foto' => $r['foto']], $stmt->fetchAll());
}

/**
 * Crea o mueve una cita verificando la disponibilidad bajo un bloqueo por
 * fecha, para que dos reservas simultáneas no tomen el mismo lugar.
 *
 * $datos: id_servicio, fecha, hora_inicio ('H:i'), id_empleado (opcional),
 *         id_cliente, estado, notas; id_cita para reprogramar una existente.
 * $opciones['panel'] = true: se permiten horarios fuera de la grilla y de
 * hoy ya pasados (el salón carga turnos tomados por WhatsApp).
 *
 * Devuelve ['ok' => true, 'id', 'id_empleado', 'hora_fin', 'token']
 *       o ['ok' => false, 'codigo', 'error'].
 */
function pm_reservar(PDO $db, array $datos, array $opciones = []): array
{
    $error = fn(int $codigo, string $msg) => ['ok' => false, 'codigo' => $codigo, 'error' => $msg];

    $stmt = $db->prepare("SELECT id, nombre, duracion_minutos, precio FROM servicios WHERE id = ? AND activo = 1 LIMIT 1");
    $stmt->execute([(int) $datos['id_servicio']]);
    $servicio = $stmt->fetch();
    if (!$servicio) {
        return $error(404, 'Servicio no encontrado');
    }

    $fecha = $datos['fecha'];
    $ini = $datos['hora_inicio'];
    $fin = date('H:i', strtotime($ini) + (int) $servicio['duracion_minutos'] * 60);
    $id_empleado = !empty($datos['id_empleado']) ? (int) $datos['id_empleado'] : null;
    $id_cita = (int) ($datos['id_cita'] ?? 0);
    $panel = !empty($opciones['panel']);

    if (!$panel && strtotime("$fecha $ini") < strtotime('+30 minutes')) {
        return $error(400, 'Ese horario ya pasó. Elegí otro.');
    }

    $candado = 'pm_agenda_' . $fecha;
    $db->query("SELECT GET_LOCK(" . $db->quote($candado) . ", 10)");
    try {
        $ctx = pm_contexto_dia($db, $servicio, $fecha, [], $id_empleado, $id_cita);
        if ($ctx['mensaje'] !== null) {
            return $error(409, $ctx['mensaje']);
        }

        if ($panel) {
            $libres = pm_franja_libre($ctx, $ini, $fin);
        } else {
            $libres = [];
            foreach (pm_turnos_del_dia($db, $servicio, $fecha, [], $id_empleado, $id_cita)['turnos'] as $t) {
                if ($t['hora_inicio'] === $ini && $t['libre']) {
                    $libres = $ctx['modo'] === 'profesional' ? $t['profesionales'] : [0];
                    break;
                }
            }
        }
        if (!$libres) {
            return $error(409, $panel
                ? 'Ese horario se superpone con otro turno o la profesional no trabaja a esa hora.'
                : 'Este horario ya no está disponible. Elegí otro.');
        }

        $asignada = $ctx['modo'] === 'profesional' ? $libres[0] : $id_empleado;

        if ($id_cita > 0) {
            $stmt = $db->prepare(
                "UPDATE citas SET fecha = ?, hora_inicio = ?, hora_fin = ?, id_empleado = ?, estado = 'pendiente'
                 WHERE id = ?"
            );
            $stmt->execute([$fecha, $ini, $fin, $asignada, $id_cita]);
            return ['ok' => true, 'id' => $id_cita, 'id_empleado' => $asignada, 'hora_fin' => $fin, 'token' => null];
        }

        $token = bin2hex(random_bytes(16));
        $stmt = $db->prepare(
            "INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora_inicio, hora_fin, estado, notas, token_gestion)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            (int) $datos['id_cliente'],
            (int) $servicio['id'],
            $asignada,
            $fecha,
            $ini,
            $fin,
            $datos['estado'] ?? 'pendiente',
            ($datos['notas'] ?? '') !== '' ? $datos['notas'] : null,
            $token,
        ]);
        return ['ok' => true, 'id' => (int) $db->lastInsertId(), 'id_empleado' => $asignada, 'hora_fin' => $fin, 'token' => $token];
    } finally {
        $db->query("SELECT RELEASE_LOCK(" . $db->quote($candado) . ")");
    }
}

/**
 * Teléfono a los últimos 10 dígitos (código de área + número), o '' si no
 * alcanza. Sirve para reconocer a la clienta y armar el link de WhatsApp.
 */
function pm_tel_norm(string $telefono): string
{
    $digitos = preg_replace('/\D/', '', $telefono);
    return strlen($digitos) >= 10 ? substr($digitos, -10) : '';
}

/**
 * Link de WhatsApp a un teléfono argentino con un mensaje.
 */
function pm_link_whatsapp(string $telefono, string $mensaje): string
{
    $norm = pm_tel_norm($telefono);
    return $norm === '' ? '' : 'https://wa.me/549' . $norm . '?text=' . rawurlencode($mensaje);
}

/**
 * Busca a la clienta por teléfono (y si no, por email) o la crea.
 * Devuelve su id.
 */
function pm_clienta_por_telefono(PDO $db, string $nombre, string $telefono, string $email = ''): int
{
    $norm = pm_tel_norm($telefono);
    if ($norm !== '') {
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE telefono_norm = ? AND rol = 'cliente' ORDER BY id LIMIT 1");
        $stmt->execute([$norm]);
        $id = $stmt->fetchColumn();
        if ($id) {
            return (int) $id;
        }
    }
    if ($email !== '') {
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $id = $stmt->fetchColumn();
        if ($id) {
            if ($norm !== '') {
                $db->prepare("UPDATE usuarios SET telefono = COALESCE(telefono, ?), telefono_norm = COALESCE(telefono_norm, ?) WHERE id = ?")
                   ->execute([$telefono, $norm, $id]);
            }
            return (int) $id;
        }
    }

    $partes = preg_split('/\s+/', trim($nombre), 2);
    $stmt = $db->prepare(
        "INSERT INTO usuarios (nombre, apellidos, email, password, telefono, telefono_norm, rol)
         VALUES (?, ?, ?, ?, ?, ?, 'cliente')"
    );
    $stmt->execute([
        $partes[0],
        $partes[1] ?? '',
        $email !== '' ? $email : null,
        password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT),
        $telefono,
        $norm !== '' ? $norm : null,
    ]);
    return (int) $db->lastInsertId();
}

/**
 * Franjas ocupadas del Google Calendar del salón, como [inicio, fin] en
 * timestamps, entre $desde y $hasta (fechas Y-m-d, $hasta inclusive).
 *
 * Devuelve null si no hay calendario configurado o Google no contesta:
 * en ese caso quien llama no puede afirmar que un turno está libre.
 *
 * Solo se piden inicio y fin de cada evento: el título (nombre de la
 * clienta) nunca sale de Google. La respuesta se guarda 60 s para no
 * pegarle a Google en cada visita.
 */
function pm_google_ocupados(string $desde, string $hasta): ?array
{
    if (AGENDA_GOOGLE_CALENDAR_ID === '' || AGENDA_GOOGLE_API_KEY === '') {
        return null;
    }

    $cache = sys_get_temp_dir() . '/pm_agenda_' . md5(AGENDA_GOOGLE_CALENDAR_ID . $desde . $hasta) . '.json';
    if (is_file($cache) && time() - filemtime($cache) < 60) {
        $guardado = json_decode((string) file_get_contents($cache), true);
        if (is_array($guardado)) {
            return $guardado;
        }
    }

    $url = 'https://www.googleapis.com/calendar/v3/calendars/'
        . rawurlencode(AGENDA_GOOGLE_CALENDAR_ID) . '/events?' . http_build_query([
            'key'          => AGENDA_GOOGLE_API_KEY,
            'timeMin'      => date('c', strtotime("$desde 00:00")),
            'timeMax'      => date('c', strtotime("$hasta 00:00 +1 day")),
            'singleEvents' => 'true',
            'orderBy'      => 'startTime',
            'maxResults'   => 500,
            'fields'       => 'items(start,end,status,transparency)',
        ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT        => 5,
    ]);
    $cuerpo = curl_exec($ch);
    $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($cuerpo === false || $codigo !== 200) {
        error_log("Agenda: Google Calendar no contestó (HTTP $codigo)");
        return null;
    }

    $json = json_decode($cuerpo, true);
    if (!is_array($json)) {
        return null;
    }

    $ocupados = [];
    foreach ($json['items'] ?? [] as $evento) {
        // "Disponible" en Google (transparent) no bloquea
        if (($evento['status'] ?? '') === 'cancelled' || ($evento['transparency'] ?? '') === 'transparent') {
            continue;
        }
        // Eventos de día completo vienen con `date` en vez de `dateTime`
        $momento = fn(array $p) => isset($p['dateTime']) ? strtotime($p['dateTime'])
            : (isset($p['date']) ? strtotime($p['date'] . ' 00:00') : false);
        $ini = $momento($evento['start'] ?? []);
        $fin = $momento($evento['end'] ?? []);
        if ($ini && $fin && $fin > $ini) {
            $ocupados[] = [$ini, $fin];
        }
    }

    @file_put_contents($cache, json_encode($ocupados));
    return $ocupados;
}
