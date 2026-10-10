<?php
/**
 * Piel Morena - Cálculo de turnos
 *
 * Lo usan la reserva online (api/citas/disponibilidad.php) y la agenda
 * pública de la landing (api/citas/agenda-publica.php), así las dos ven
 * los mismos horarios, máquinas y jornadas.
 */

/**
 * Turnos de un día para un servicio.
 *
 * $servicio: fila con `id` y `duracion_minutos`. Con id 0 es un turno
 * genérico: horario del local, sin reglas de máquina ni de jornada.
 * $ocupados_extra: franjas [inicio, fin] en timestamps (Google Calendar).
 *
 * Devuelve:
 *   mensaje  → por qué no hay turnos (null si el día se atiende)
 *   franja   → [apertura, cierre] que rigió ese día (null si no se atiende)
 *   turnos   → [['hora_inicio', 'hora_fin', 'libre'], ...]
 */
function pm_turnos_del_dia(PDO $db, array $servicio, string $fecha, array $ocupados_extra = []): array
{
    $cerrado = fn(string $mensaje) => ['mensaje' => $mensaje, 'franja' => null, 'turnos' => []];

    // ── Resolucion de disponibilidad ──
    // 1. servicios.id_maquina IS NOT NULL → requiere alquiler activo de esa maquina.
    // 2. Sino, si categoria tiene requiere_jornada=1 → requiere jornada activa (personal externo).
    // 3. Sino → calendario libre.
    $ventana_activa = null;
    if (!empty($servicio['id'])) {
        $stmt_check = $db->prepare(
            "SELECT s.id_maquina, s.id_categoria,
                    m.nombre AS maquina_nombre, m.activo AS maquina_activa,
                    c.requiere_jornada AS cat_requiere, c.nombre AS categoria_nombre
             FROM servicios s
             LEFT JOIN maquinas m ON s.id_maquina = m.id
             LEFT JOIN categorias_servicios c ON s.id_categoria = c.id
             WHERE s.id = ? LIMIT 1"
        );
        $stmt_check->execute([(int) $servicio['id']]);
        $rcheck = $stmt_check->fetch();

        if (!empty($rcheck['id_maquina'])) {
            if (!$rcheck['maquina_activa']) {
                return $cerrado('La maquina ' . $rcheck['maquina_nombre'] . ' esta inactiva');
            }

            $stmt_a = $db->prepare(
                "SELECT hora_inicio, hora_fin FROM maquinas_alquileres
                 WHERE id_maquina = ? AND fecha = ? AND estado = 'activo'
                 LIMIT 1"
            );
            $stmt_a->execute([(int) $rcheck['id_maquina'], $fecha]);
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

    // Obtener configuración de horarios del negocio
    $stmt = $db->prepare("SELECT valor FROM configuracion WHERE clave = ?");

    $stmt->execute(['horario_apertura']);
    $apertura = $stmt->fetchColumn() ?: '09:00';

    $stmt->execute(['horario_cierre']);
    $cierre = $stmt->fetchColumn() ?: '20:00';

    // Si hay ventana activa (alquiler de maquina o jornada), sus horarios mandan
    if ($ventana_activa) {
        $apertura = substr($ventana_activa['hora_inicio'], 0, 5);
        $cierre   = substr($ventana_activa['hora_fin'], 0, 5);
    }

    $stmt->execute(['dias_laborales']);
    $dias_laborales = $stmt->fetchColumn() ?: '1,2,3,4,5,6';

    $stmt->execute(['intervalo_citas']);
    $intervalo = intval($stmt->fetchColumn() ?: 30);

    // Verificar si el día de la semana es laboral (1=Lun, 7=Dom)
    $dia_semana = intval(date('N', strtotime($fecha)));
    $dias_array = array_map('intval', explode(',', $dias_laborales));

    // Si hay ventana activa (alquiler o jornada), el dia laboral no aplica
    if (!$ventana_activa && !in_array($dia_semana, $dias_array)) {
        return $cerrado('No atendemos este día');
    }

    // Obtener citas ya reservadas para esa fecha (estados activos)
    $stmt = $db->prepare(
        "SELECT hora_inicio, hora_fin FROM citas
         WHERE fecha = ? AND estado IN ('pendiente', 'confirmada', 'en_proceso')
         ORDER BY hora_inicio"
    );
    $stmt->execute([$fecha]);
    $citas_ocupadas = $stmt->fetchAll();

    // Generar slots
    $duracion = max($servicio['duracion_minutos'], $intervalo);
    $slots = [];

    $hora_actual = strtotime($apertura);
    $hora_cierre = strtotime($cierre);

    // Si es hoy, no mostrar turnos pasados (con margen de 30 min)
    if ($fecha === date('Y-m-d')) {
        $ahora_mas_margen = strtotime('+30 minutes');
        if ($ahora_mas_margen > $hora_actual) {
            // Redondear al próximo intervalo
            $minutos = intval(date('i', $ahora_mas_margen));
            $redondeo = ceil($minutos / $intervalo) * $intervalo;
            $hora_actual = strtotime(date('H', $ahora_mas_margen) . ':00') + ($redondeo * 60);
        }
    }

    while ($hora_actual + ($duracion * 60) <= $hora_cierre) {
        $inicio = date('H:i', $hora_actual);
        $fin    = date('H:i', $hora_actual + ($duracion * 60));

        // Verificar si el slot se solapa con alguna cita existente.
        // MySQL devuelve "11:30:00": sin recortar, "11:30" < "11:30:00" y el
        // turno que arranca justo cuando termina una cita salía ocupado.
        $libre = true;
        foreach ($citas_ocupadas as $cita) {
            if ($inicio < substr($cita['hora_fin'], 0, 5) && $fin > substr($cita['hora_inicio'], 0, 5)) {
                $libre = false;
                break;
            }
        }

        if ($libre && $ocupados_extra) {
            $ini_ts = strtotime("$fecha $inicio");
            $fin_ts = strtotime("$fecha $fin");
            foreach ($ocupados_extra as [$o_ini, $o_fin]) {
                if ($ini_ts < $o_fin && $fin_ts > $o_ini) {
                    $libre = false;
                    break;
                }
            }
        }

        $slots[] = [
            'hora_inicio' => $inicio,
            'hora_fin'    => $fin,
            'libre'       => $libre,
        ];

        $hora_actual += $intervalo * 60;
    }

    return ['mensaje' => null, 'franja' => [$apertura, $cierre], 'turnos' => $slots];
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
