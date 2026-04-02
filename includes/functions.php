<?php
/**
 * Piel Morena - Funciones Helper Globales
 */

/**
 * Sanitizar entrada de texto
 */
function sanitizar($dato): string {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

/**
 * Respuesta JSON estandarizada para APIs
 */
function responder_json(bool $success, $data = null, string $error = '', int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');

    $response = ['success' => $success];

    if ($success && $data !== null) {
        $response['data'] = $data;
    }

    if (!$success && $error) {
        $response['error'] = $error;
    }

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Verificar si el usuario está autenticado
 */
function esta_autenticado(): bool {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Obtener el ID del usuario actual
 */
function usuario_actual_id(): ?int {
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Obtener el rol del usuario actual
 */
function usuario_actual_rol(): ?string {
    return $_SESSION['usuario_rol'] ?? null;
}

/**
 * Verificar si el usuario tiene un rol específico
 */
function tiene_rol(string $rol): bool {
    return usuario_actual_rol() === $rol;
}

/**
 * Redirigir a una URL
 */
function redirigir(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Generar token CSRF
 */
function generar_csrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 */
function verificar_csrf(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Formatear precio con moneda
 */
function formatear_precio(float $precio): string {
    return '$' . number_format($precio, 2, '.', ',');
}

/**
 * Formatear fecha al español
 */
function formatear_fecha(string $fecha): string {
    $timestamp = strtotime($fecha);
    $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $mes = $meses[(int)date('n', $timestamp) - 1];
    return date('d', $timestamp) . ' de ' . $mes . ' de ' . date('Y', $timestamp);
}

/**
 * Formatear hora (24h a 12h)
 */
function formatear_hora(string $hora): string {
    return date('g:i A', strtotime($hora));
}

/**
 * Obtener iniciales a partir de un nombre.
 */
function obtener_iniciales(string $nombre, int $max = 2): string {
    $nombre = trim(preg_replace('/\s+/', ' ', $nombre));
    if ($nombre === '') {
        return 'PM';
    }

    $partes = preg_split('/\s+/', $nombre) ?: [];
    $iniciales = '';

    foreach ($partes as $parte) {
        if ($parte === '') {
            continue;
        }

        if (function_exists('mb_substr')) {
            $iniciales .= mb_strtoupper(mb_substr($parte, 0, 1, 'UTF-8'), 'UTF-8');
        } else {
            $iniciales .= strtoupper(substr($parte, 0, 1));
        }

        if (strlen($iniciales) >= $max) {
            break;
        }
    }

    return $iniciales !== '' ? $iniciales : 'PM';
}
