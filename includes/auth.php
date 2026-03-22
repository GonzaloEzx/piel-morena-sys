<?php
/**
 * Piel Morena - Funciones de Autenticación
 */

/**
 * Iniciar sesión de usuario
 */
function iniciar_sesion(string $email, string $password): array {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, nombre, apellidos, email, password, rol, activo FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        return ['success' => false, 'error' => 'Credenciales incorrectas'];
    }

    if (!$usuario['activo']) {
        return ['success' => false, 'error' => 'Cuenta desactivada'];
    }

    if (!password_verify($password, $usuario['password'])) {
        return ['success' => false, 'error' => 'Credenciales incorrectas'];
    }

    // Crear sesión
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellidos'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_rol'] = $usuario['rol'];

    // Actualizar último acceso
    $stmt = $db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
    $stmt->execute([$usuario['id']]);

    return ['success' => true, 'rol' => $usuario['rol']];
}

/**
 * Cerrar sesión
 */
function cerrar_sesion(): void {
    session_unset();
    session_destroy();
}

/**
 * Registrar nuevo cliente
 */
function registrar_cliente(string $nombre, string $apellidos, string $email, string $password, string $telefono = ''): array {
    $db = getDB();

    // Verificar si el email ya existe
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'El email ya está registrado'];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO usuarios (nombre, apellidos, email, password, telefono, rol) VALUES (?, ?, ?, ?, ?, 'cliente')");
    $stmt->execute([$nombre, $apellidos, $email, $hash, $telefono]);

    return ['success' => true, 'id' => $db->lastInsertId()];
}

/**
 * Requerir autenticación - redirige si no está logueado
 */
function requerir_auth(string $redirect = '/login.php'): void {
    if (!esta_autenticado()) {
        redirigir(URL_BASE . $redirect);
    }
}

/**
 * Requerir rol específico
 */
function requerir_rol(string $rol, string $redirect = '/'): void {
    requerir_auth();
    if (!tiene_rol($rol) && !tiene_rol('admin')) {
        redirigir(URL_BASE . $redirect);
    }
}

/**
 * Login o registro via Google OAuth
 */
function login_google(string $google_id, string $email, string $nombre, string $apellidos): array {
    $db = getDB();

    // Buscar por google_id primero
    $stmt = $db->prepare("SELECT id, nombre, apellidos, email, rol, activo FROM usuarios WHERE google_id = ? LIMIT 1");
    $stmt->execute([$google_id]);
    $usuario = $stmt->fetch();

    // Si no encontró por google_id, buscar por email
    if (!$usuario) {
        $stmt = $db->prepare("SELECT id, nombre, apellidos, email, rol, activo, google_id FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            // Vincular Google ID a cuenta existente
            $stmt = $db->prepare("UPDATE usuarios SET google_id = ?, email_verificado = 1 WHERE id = ?");
            $stmt->execute([$google_id, $usuario['id']]);
        }
    }

    if ($usuario) {
        if (!$usuario['activo']) {
            return ['success' => false, 'error' => 'Cuenta desactivada'];
        }

        // Crear sesión
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellidos'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_rol'] = $usuario['rol'];

        $stmt = $db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
        $stmt->execute([$usuario['id']]);

        return ['success' => true, 'rol' => $usuario['rol'], 'nuevo' => false];
    }

    // Crear nuevo usuario
    $stmt = $db->prepare("INSERT INTO usuarios (nombre, apellidos, email, google_id, email_verificado, rol) VALUES (?, ?, ?, ?, 1, 'cliente')");
    $stmt->execute([$nombre, $apellidos, $email, $google_id]);
    $id = $db->lastInsertId();

    // Crear sesión
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $id;
    $_SESSION['usuario_nombre'] = $nombre . ' ' . $apellidos;
    $_SESSION['usuario_email'] = $email;
    $_SESSION['usuario_rol'] = 'cliente';

    $stmt = $db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
    $stmt->execute([$id]);

    return ['success' => true, 'rol' => 'cliente', 'nuevo' => true];
}

/**
 * Generar y guardar código de verificación
 */
function generar_codigo_verificacion(string $email, string $tipo = 'registro'): array {
    $db = getDB();

    // Rate limiting: máximo CODIGO_MAX_POR_HORA por email por hora
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM codigos_verificacion WHERE email = ? AND tipo = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $stmt->execute([$email, $tipo]);
    $count = $stmt->fetch()['total'];

    if ($count >= CODIGO_MAX_POR_HORA) {
        return ['success' => false, 'error' => 'Demasiados intentos. Esperá un momento antes de solicitar otro código.'];
    }

    // Invalidar códigos anteriores del mismo email/tipo
    $stmt = $db->prepare("UPDATE codigos_verificacion SET usado = 1 WHERE email = ? AND tipo = ? AND usado = 0");
    $stmt->execute([$email, $tipo]);

    // Generar código de 6 dígitos
    $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expira = date('Y-m-d H:i:s', strtotime('+' . CODIGO_EXPIRACION_MINUTOS . ' minutes'));

    $stmt = $db->prepare("INSERT INTO codigos_verificacion (email, codigo, tipo, expira_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $codigo, $tipo, $expira]);

    return ['success' => true, 'codigo' => $codigo];
}

/**
 * Verificar código de verificación
 */
function verificar_codigo(string $email, string $codigo, string $tipo = 'registro'): array {
    $db = getDB();

    $stmt = $db->prepare("SELECT id, codigo, intentos, expira_at FROM codigos_verificacion WHERE email = ? AND tipo = ? AND usado = 0 ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$email, $tipo]);
    $registro = $stmt->fetch();

    if (!$registro) {
        return ['success' => false, 'error' => 'Código inválido o expirado. Solicitá uno nuevo.'];
    }

    // Verificar expiración
    if (strtotime($registro['expira_at']) < time()) {
        $stmt = $db->prepare("UPDATE codigos_verificacion SET usado = 1 WHERE id = ?");
        $stmt->execute([$registro['id']]);
        return ['success' => false, 'error' => 'El código ha expirado. Solicitá uno nuevo.'];
    }

    // Verificar intentos
    if ($registro['intentos'] >= CODIGO_MAX_INTENTOS) {
        $stmt = $db->prepare("UPDATE codigos_verificacion SET usado = 1 WHERE id = ?");
        $stmt->execute([$registro['id']]);
        return ['success' => false, 'error' => 'Demasiados intentos fallidos. Solicitá un nuevo código.'];
    }

    // Verificar código
    if (!hash_equals($registro['codigo'], $codigo)) {
        $stmt = $db->prepare("UPDATE codigos_verificacion SET intentos = intentos + 1 WHERE id = ?");
        $stmt->execute([$registro['id']]);
        $restantes = CODIGO_MAX_INTENTOS - $registro['intentos'] - 1;
        return ['success' => false, 'error' => "Código incorrecto. Te quedan $restantes intentos."];
    }

    // Código correcto — marcar como usado
    $stmt = $db->prepare("UPDATE codigos_verificacion SET usado = 1 WHERE id = ?");
    $stmt->execute([$registro['id']]);

    return ['success' => true];
}

/**
 * Enviar email con código de verificación
 */
function enviar_email_codigo(string $email, string $codigo, string $tipo = 'registro'): bool {
    $asunto = $tipo === 'registro'
        ? 'Tu código de verificación — Piel Morena'
        : 'Recuperar contraseña — Piel Morena';

    $mensaje_texto = $tipo === 'registro'
        ? "Tu código de verificación es: $codigo\nVálido por " . CODIGO_EXPIRACION_MINUTOS . " minutos."
        : "Tu código para restablecer tu contraseña es: $codigo\nVálido por " . CODIGO_EXPIRACION_MINUTOS . " minutos.";

    $cuerpo = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="font-family:DM Sans,Arial,sans-serif;background:#F8F4E8;padding:40px 20px;">';
    $cuerpo .= '<div style="max-width:480px;margin:0 auto;background:#fff;border-radius:16px;padding:40px;box-shadow:0 4px 24px rgba(0,0,0,0.08);">';
    $cuerpo .= '<div style="text-align:center;margin-bottom:24px;">';
    $cuerpo .= '<h1 style="font-family:Playfair Display,serif;color:#957C62;font-size:24px;margin:0;">Piel Morena</h1>';
    $cuerpo .= '</div>';

    if ($tipo === 'registro') {
        $cuerpo .= '<p style="color:#3F352B;font-size:16px;">Hola, tu código de verificación es:</p>';
    } else {
        $cuerpo .= '<p style="color:#3F352B;font-size:16px;">Hola, tu código para restablecer tu contraseña es:</p>';
    }

    $cuerpo .= '<div style="text-align:center;margin:24px 0;">';
    $cuerpo .= '<span style="display:inline-block;background:#ECE7D1;color:#8A7650;font-size:32px;font-weight:700;letter-spacing:8px;padding:16px 32px;border-radius:12px;">' . $codigo . '</span>';
    $cuerpo .= '</div>';
    $cuerpo .= '<p style="color:#746754;font-size:14px;">Este código es válido por ' . CODIGO_EXPIRACION_MINUTOS . ' minutos.</p>';
    $cuerpo .= '<p style="color:#746754;font-size:14px;">Si no solicitaste este código, ignorá este mensaje.</p>';
    $cuerpo .= '<hr style="border:none;border-top:1px solid #ECE7D1;margin:24px 0;">';
    $cuerpo .= '<p style="color:#A69882;font-size:12px;text-align:center;">Piel Morena Estética — Tu belleza, nuestra pasión</p>';
    $cuerpo .= '</div></body></html>';

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Piel Morena <" . EMAIL_NEGOCIO . ">\r\n";

    return @mail($email, $asunto, $cuerpo, $headers);
}

/**
 * Cambiar contraseña del usuario
 */
function cambiar_password(int $usuario_id, string $password_actual, string $password_nueva): array {
    $db = getDB();

    $stmt = $db->prepare("SELECT password FROM usuarios WHERE id = ? LIMIT 1");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        return ['success' => false, 'error' => 'Usuario no encontrado'];
    }

    // Si tiene password (no es solo Google), verificar la actual
    if ($usuario['password'] && !password_verify($password_actual, $usuario['password'])) {
        return ['success' => false, 'error' => 'La contraseña actual es incorrecta'];
    }

    $hash = password_hash($password_nueva, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
    $stmt->execute([$hash, $usuario_id]);

    return ['success' => true];
}

/**
 * Resetear contraseña por código de verificación
 */
function resetear_password(string $email, string $password_nueva): array {
    $db = getDB();

    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        return ['success' => false, 'error' => 'Usuario no encontrado'];
    }

    $hash = password_hash($password_nueva, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
    $stmt->execute([$hash, $usuario['id']]);

    return ['success' => true];
}
