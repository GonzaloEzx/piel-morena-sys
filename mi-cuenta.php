<?php
/**
 * Piel Morena — Mi Cuenta
 */
$titulo_pagina = 'Mi Cuenta — Piel Morena Estética';
$meta_descripcion = 'Gestioná tu perfil, citas e historial en Piel Morena.';
require_once __DIR__ . '/includes/header.php';

// Requiere autenticación
if (!$logueado) {
    redirigir(URL_BASE . '/login.php?redirect=' . urlencode('/mi-cuenta.php'));
}

$db = getDB();

// Obtener datos del usuario
$stmt = $db->prepare("SELECT id, nombre, apellidos, email, telefono, foto, google_id, email_verificado, created_at FROM usuarios WHERE id = ?");
$stmt->execute([usuario_actual_id()]);
$usuario = $stmt->fetch();

// Obtener citas del usuario
$stmt = $db->prepare("
    SELECT c.id, c.fecha, c.hora_inicio, c.hora_fin, c.estado, c.notas, c.created_at,
           s.nombre as servicio_nombre, s.precio as servicio_precio, s.duracion_minutos
    FROM citas c
    JOIN servicios s ON c.id_servicio = s.id
    WHERE c.id_cliente = ?
    ORDER BY c.fecha DESC, c.hora_inicio DESC
    LIMIT 20
");
$stmt->execute([usuario_actual_id()]);
$citas = $stmt->fetchAll();

// Separar próximas y pasadas
$hoy = date('Y-m-d');
$citas_proximas = [];
$citas_pasadas = [];
foreach ($citas as $cita) {
    if ($cita['fecha'] >= $hoy && in_array($cita['estado'], ['pendiente', 'confirmada'])) {
        $citas_proximas[] = $cita;
    } else {
        $citas_pasadas[] = $cita;
    }
}

$meses_es = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
?>

<section class="pm-cuenta-section">
  <div class="container">

    <!-- Header -->
    <div class="pm-cuenta-header">
      <h1><i class="bi bi-person-circle me-2"></i>Mi Cuenta</h1>
      <p>Hola, <?= sanitizar($usuario['nombre']) ?>. Gestioná tu perfil y tus citas.</p>
    </div>

    <div class="row g-4">

      <!-- ── Col izquierda: Datos + Password ───────────────── -->
      <div class="col-12 col-lg-5">

        <!-- Datos personales -->
        <div class="pm-cuenta-card">
          <h5 class="pm-cuenta-card-title"><i class="bi bi-person-fill"></i>Datos personales</h5>

          <form id="pmPerfilForm" class="pm-auth-form">
            <div class="row g-3">
              <div class="col-6">
                <div class="pm-form-group">
                  <label for="perfil-nombre" class="pm-form-label">Nombre</label>
                  <input id="perfil-nombre" type="text" class="input-pm w-100" value="<?= sanitizar($usuario['nombre']) ?>" required />
                </div>
              </div>
              <div class="col-6">
                <div class="pm-form-group">
                  <label for="perfil-apellidos" class="pm-form-label">Apellidos</label>
                  <input id="perfil-apellidos" type="text" class="input-pm w-100" value="<?= sanitizar($usuario['apellidos']) ?>" required />
                </div>
              </div>
            </div>

            <div class="pm-form-group">
              <label for="perfil-email" class="pm-form-label"><i class="bi bi-envelope me-1"></i>Email</label>
              <input id="perfil-email" type="email" class="input-pm w-100" value="<?= sanitizar($usuario['email']) ?>" disabled />
              <small class="text-muted">El email no se puede cambiar.</small>
            </div>

            <div class="pm-form-group">
              <label for="perfil-telefono" class="pm-form-label"><i class="bi bi-telephone me-1"></i>Teléfono</label>
              <input id="perfil-telefono" type="tel" class="input-pm w-100" value="<?= sanitizar($usuario['telefono'] ?? '') ?>" placeholder="+54 (XXX) XXX-XXXX" />
            </div>

            <button type="submit" class="btn btn-pm w-100">
              <i class="bi bi-check-circle me-2"></i>Guardar cambios
            </button>
            <div id="pmPerfilMsg" class="pm-auth-error" style="display:none;"></div>
          </form>
        </div>

        <!-- Cambiar contraseña -->
        <?php if ($usuario['password'] ?? true): // Solo mostrar si no es solo Google ?>
        <div class="pm-cuenta-card">
          <h5 class="pm-cuenta-card-title"><i class="bi bi-shield-lock"></i>Cambiar contraseña</h5>

          <form id="pmPasswordForm" class="pm-auth-form">
            <?php if (!$usuario['google_id']): ?>
            <div class="pm-form-group">
              <label for="pass-actual" class="pm-form-label">Contraseña actual</label>
              <div class="pm-input-password-wrap">
                <input id="pass-actual" type="password" class="input-pm w-100" placeholder="Tu contraseña actual" />
                <button type="button" class="pm-toggle-pass" data-target="pass-actual"><i class="bi bi-eye"></i></button>
              </div>
            </div>
            <?php endif; ?>

            <div class="pm-form-group">
              <label for="pass-nueva" class="pm-form-label">Nueva contraseña</label>
              <div class="pm-input-password-wrap">
                <input id="pass-nueva" type="password" class="input-pm w-100" placeholder="Mínimo 6 caracteres" />
                <button type="button" class="pm-toggle-pass" data-target="pass-nueva"><i class="bi bi-eye"></i></button>
              </div>
            </div>

            <div class="pm-form-group">
              <label for="pass-confirmar" class="pm-form-label">Confirmar nueva contraseña</label>
              <div class="pm-input-password-wrap">
                <input id="pass-confirmar" type="password" class="input-pm w-100" placeholder="Repetí la nueva contraseña" />
                <button type="button" class="pm-toggle-pass" data-target="pass-confirmar"><i class="bi bi-eye"></i></button>
              </div>
            </div>

            <button type="submit" class="btn btn-pm-outline w-100">
              <i class="bi bi-key me-2"></i>Actualizar contraseña
            </button>
            <div id="pmPasswordMsg" class="pm-auth-error" style="display:none;"></div>
          </form>
        </div>
        <?php endif; ?>

      </div>

      <!-- ── Col derecha: Citas ────────────────────────────── -->
      <div class="col-12 col-lg-7">

        <!-- Próximas citas -->
        <div class="pm-cuenta-card">
          <h5 class="pm-cuenta-card-title"><i class="bi bi-calendar-check"></i>Próximas citas</h5>

          <?php if (empty($citas_proximas)): ?>
            <div class="text-center py-4">
              <i class="bi bi-calendar-x" style="font-size:2.5rem;color:var(--pm-arena);"></i>
              <p class="mt-2 mb-3" style="color:var(--pm-text-muted);font-size:0.9rem;">No tenés citas próximas.</p>
              <a href="<?= URL_BASE ?>/reservar.php" class="btn btn-pm btn-pm-sm">
                <i class="bi bi-plus-circle me-1"></i>Reservar cita
              </a>
            </div>
          <?php else: ?>
            <?php foreach ($citas_proximas as $cita):
              $fecha_ts = strtotime($cita['fecha']);
              $dia = date('d', $fecha_ts);
              $mes = $meses_es[(int)date('n', $fecha_ts) - 1];
            ?>
            <div class="pm-cita-item">
              <div class="pm-cita-fecha">
                <span class="pm-cita-fecha-dia"><?= $dia ?></span>
                <span class="pm-cita-fecha-mes"><?= $mes ?></span>
              </div>
              <div class="pm-cita-info">
                <div class="pm-cita-servicio"><?= sanitizar($cita['servicio_nombre']) ?></div>
                <div class="pm-cita-hora">
                  <i class="bi bi-clock me-1"></i><?= date('H:i', strtotime($cita['hora_inicio'])) ?> - <?= date('H:i', strtotime($cita['hora_fin'])) ?>
                </div>
              </div>
              <div class="pm-cita-estado">
                <span class="pm-badge pm-badge-<?= $cita['estado'] ?>"><?= ucfirst($cita['estado']) ?></span>
              </div>
            </div>
            <?php endforeach; ?>
            <div class="pm-cita-cancelar-info">
              <i class="bi bi-info-circle me-2"></i>Para cancelar o reprogramar una cita, contactanos al <a href="tel:+543624254052"><strong>3624 254052</strong></a>
            </div>
          <?php endif; ?>
        </div>

        <!-- Historial -->
        <div class="pm-cuenta-card">
          <h5 class="pm-cuenta-card-title"><i class="bi bi-clock-history"></i>Historial de citas</h5>

          <?php if (empty($citas_pasadas)): ?>
            <p class="text-center py-3" style="color:var(--pm-text-muted);font-size:0.9rem;">Todavía no tenés historial de citas.</p>
          <?php else: ?>
            <?php foreach ($citas_pasadas as $cita):
              $fecha_ts = strtotime($cita['fecha']);
              $dia = date('d', $fecha_ts);
              $mes = $meses_es[(int)date('n', $fecha_ts) - 1];
            ?>
            <div class="pm-cita-item" style="opacity:0.7;">
              <div class="pm-cita-fecha">
                <span class="pm-cita-fecha-dia"><?= $dia ?></span>
                <span class="pm-cita-fecha-mes"><?= $mes ?></span>
              </div>
              <div class="pm-cita-info">
                <div class="pm-cita-servicio"><?= sanitizar($cita['servicio_nombre']) ?></div>
                <div class="pm-cita-hora">
                  <i class="bi bi-clock me-1"></i><?= date('H:i', strtotime($cita['hora_inicio'])) ?> - <?= date('H:i', strtotime($cita['hora_fin'])) ?>
                  &middot; <?= formatear_precio($cita['servicio_precio']) ?>
                </div>
              </div>
              <div class="pm-cita-estado">
                <span class="pm-badge pm-badge-<?= $cita['estado'] ?>"><?= ucfirst($cita['estado']) ?></span>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

      </div>

    </div><!-- /row -->
  </div>
</section>

<!-- Mi Cuenta Scripts -->
<script>
document.addEventListener('DOMContentLoaded', () => {

  // Toggle password visibility
  document.querySelectorAll('.pm-toggle-pass').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      if (!input) return;
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      btn.innerHTML = isHidden ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    });
  });

  // Perfil form
  document.getElementById('pmPerfilForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const msgDiv = document.getElementById('pmPerfilMsg');
    msgDiv.style.display = 'none';

    const nombre = document.getElementById('perfil-nombre').value.trim();
    const apellidos = document.getElementById('perfil-apellidos').value.trim();
    const telefono = document.getElementById('perfil-telefono').value.trim();

    if (!nombre || !apellidos) {
      msgDiv.textContent = 'Nombre y apellidos son requeridos.';
      msgDiv.style.display = 'block';
      return;
    }

    try {
      const resp = await fetch('api/clientes/actualizar-perfil.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre, apellidos, telefono }),
      });
      const data = await resp.json();

      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Perfil actualizado',
          text: 'Tus datos se guardaron correctamente.',
          confirmButtonText: 'OK',
          customClass: { popup: 'pm-modal', confirmButton: 'btn-pm' },
        }).then(() => window.location.reload());
      } else {
        msgDiv.textContent = data.error || 'Error al guardar.';
        msgDiv.style.display = 'block';
      }
    } catch {
      msgDiv.textContent = 'Error de conexión.';
      msgDiv.style.display = 'block';
    }
  });

  // Password form
  document.getElementById('pmPasswordForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const msgDiv = document.getElementById('pmPasswordMsg');
    msgDiv.style.display = 'none';

    const actual = document.getElementById('pass-actual')?.value || '';
    const nueva = document.getElementById('pass-nueva').value;
    const confirmar = document.getElementById('pass-confirmar').value;

    if (!nueva || nueva.length < 6) {
      msgDiv.textContent = 'La nueva contraseña debe tener al menos 6 caracteres.';
      msgDiv.style.display = 'block';
      return;
    }
    if (nueva !== confirmar) {
      msgDiv.textContent = 'Las contraseñas no coinciden.';
      msgDiv.style.display = 'block';
      return;
    }

    try {
      const resp = await fetch('api/auth/cambiar-password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ password_actual: actual, password_nueva: nueva }),
      });
      const data = await resp.json();

      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Contraseña actualizada',
          text: 'Tu contraseña se cambió correctamente.',
          confirmButtonText: 'OK',
          customClass: { popup: 'pm-modal', confirmButton: 'btn-pm' },
        });
        document.getElementById('pmPasswordForm').reset();
      } else {
        msgDiv.textContent = data.error || 'Error al cambiar contraseña.';
        msgDiv.style.display = 'block';
      }
    } catch {
      msgDiv.textContent = 'Error de conexión.';
      msgDiv.style.display = 'block';
    }
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
