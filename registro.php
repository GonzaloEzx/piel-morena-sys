<?php
/**
 * Piel Morena - Registro de Cuenta
 */
$titulo_pagina = 'Crear Cuenta — Piel Morena Estética';
$meta_descripcion = 'Creá tu cuenta en Piel Morena para reservar citas, ver tu historial y recibir promociones.';
require_once __DIR__ . '/includes/header.php';

// Si ya está logueado, redirigir
if ($logueado) {
    redirigir(URL_BASE . '/');
}

$redirect = sanitizar($_GET['redirect'] ?? '');
?>

<!-- Auth Page Container -->
<section class="pm-auth-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-5">

        <!-- Card -->
        <div class="pm-auth-card">

          <!-- Header -->
          <div class="pm-auth-header">
            <div class="pm-auth-icon">
              <i class="bi bi-person-plus"></i>
            </div>
            <h1 class="pm-auth-title">Crear Cuenta</h1>
            <p class="pm-auth-subtitle">Registrate para reservar tus citas y acceder a beneficios exclusivos</p>
          </div>

          <!-- Google Register Button -->
          <div class="pm-auth-google">
            <button type="button" class="btn pm-btn-google w-100" id="pmGoogleRegisterBtn">
              <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4"/>
                <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 009 18z" fill="#34A853"/>
                <path d="M3.964 10.71A5.41 5.41 0 013.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 000 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 00.957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
              </svg>
              <span>Registrarse con Google</span>
            </button>
          </div>

          <!-- Divider -->
          <div class="pm-auth-divider">
            <span>o completá el formulario</span>
          </div>

          <!-- Register Form -->
          <form id="pmRegisterForm" class="pm-auth-form" novalidate>
            <input type="hidden" name="redirect" value="<?= $redirect ?>" />

            <!-- Row: Nombre + Apellidos -->
            <div class="row g-3">
              <div class="col-6">
                <div class="pm-form-group">
                  <label for="reg-nombre" class="pm-form-label">Nombre *</label>
                  <input id="reg-nombre" type="text" class="input-pm w-100" placeholder="Tu nombre" autocomplete="given-name" required />
                </div>
              </div>
              <div class="col-6">
                <div class="pm-form-group">
                  <label for="reg-apellidos" class="pm-form-label">Apellidos *</label>
                  <input id="reg-apellidos" type="text" class="input-pm w-100" placeholder="Tus apellidos" autocomplete="family-name" required />
                </div>
              </div>
            </div>

            <!-- Email -->
            <div class="pm-form-group">
              <label for="reg-email" class="pm-form-label">
                <i class="bi bi-envelope me-1"></i>Correo electrónico *
              </label>
              <input id="reg-email" type="email" class="input-pm w-100" placeholder="tu@email.com" autocomplete="email" required />
            </div>

            <!-- Teléfono -->
            <div class="pm-form-group">
              <label for="reg-telefono" class="pm-form-label">
                <i class="bi bi-telephone me-1"></i>Teléfono <span class="pm-text-muted">(opcional)</span>
              </label>
              <input id="reg-telefono" type="tel" class="input-pm w-100" placeholder="+54 (XXX) XXX-XXXX" autocomplete="tel" />
            </div>

            <!-- Password -->
            <div class="pm-form-group">
              <label for="reg-password" class="pm-form-label">
                <i class="bi bi-lock me-1"></i>Contraseña *
              </label>
              <div class="pm-input-password-wrap">
                <input id="reg-password" type="password" class="input-pm w-100" placeholder="Mínimo 6 caracteres" autocomplete="new-password" required />
                <button type="button" class="pm-toggle-pass" data-target="reg-password" aria-label="Mostrar contraseña">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <!-- Confirmar Password -->
            <div class="pm-form-group">
              <label for="reg-password2" class="pm-form-label">
                <i class="bi bi-lock-fill me-1"></i>Confirmar contraseña *
              </label>
              <div class="pm-input-password-wrap">
                <input id="reg-password2" type="password" class="input-pm w-100" placeholder="Repetí tu contraseña" autocomplete="new-password" required />
                <button type="button" class="pm-toggle-pass" data-target="reg-password2" aria-label="Mostrar contraseña">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-pm w-100 pm-btn-submit">
              <i class="bi bi-check-circle me-2"></i>Crear cuenta
            </button>

            <!-- Error message container -->
            <div id="pmRegisterError" class="pm-auth-error" style="display:none;"></div>
          </form>

          <!-- Login link -->
          <div class="pm-auth-alt">
            <p>¿Ya tenés cuenta? <a href="<?= URL_BASE ?>/login.php<?= $redirect ? '?redirect=' . urlencode($redirect) : '' ?>" class="pm-link">Iniciá sesión</a></p>
          </div>

        </div><!-- /card -->

      </div>
    </div>
  </div>
</section>

<!-- Register Page Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('pmRegisterForm');
  const errorDiv = document.getElementById('pmRegisterError');
  const redirectInput = form.querySelector('input[name="redirect"]');

  // Toggle password visibility
  form.querySelectorAll('.pm-toggle-pass').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      if (!input) return;
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      btn.innerHTML = isHidden ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    });
  });

  // Form submit
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorDiv.style.display = 'none';

    const nombre = document.getElementById('reg-nombre').value.trim();
    const apellidos = document.getElementById('reg-apellidos').value.trim();
    const email = document.getElementById('reg-email').value.trim();
    const telefono = document.getElementById('reg-telefono').value.trim();
    const password = document.getElementById('reg-password').value;
    const password2 = document.getElementById('reg-password2').value;

    // Validaciones
    if (!nombre) { showError('El nombre es requerido.'); return; }
    if (!apellidos) { showError('Los apellidos son requeridos.'); return; }
    if (!email) { showError('El correo electrónico es requerido.'); return; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError('Ingresá un correo electrónico válido.'); return; }
    if (!password) { showError('La contraseña es requerida.'); return; }
    if (password.length < 6) { showError('La contraseña debe tener al menos 6 caracteres.'); return; }
    if (password !== password2) { showError('Las contraseñas no coinciden.'); return; }

    const submitBtn = form.querySelector('.pm-btn-submit');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando cuenta...';

    try {
      const response = await fetch('api/auth/registro.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre, apellidos, email, telefono, password }),
      });

      const data = await response.json();

      if (data.success) {
        // Registro exitoso — auto-login
        const loginResponse = await fetch('api/auth/login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password }),
        });
        const loginData = await loginResponse.json();

        await Swal.fire({
          icon: 'success',
          title: '¡Cuenta creada!',
          html: '<p>Tu cuenta ha sido creada exitosamente. ¡Bienvenida a Piel Morena!</p>',
          confirmButtonText: 'Continuar',
          customClass: { popup: 'pm-modal', confirmButton: 'btn-pm' },
        });

        const redirect = redirectInput?.value;
        window.location.href = redirect || '<?= URL_BASE ?>/';
      } else {
        showError(data.error || 'No se pudo crear la cuenta.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Crear cuenta';
      }
    } catch {
      showError('Error de conexión. Intentá de nuevo.');
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Crear cuenta';
    }
  });

  function showError(msg) {
    errorDiv.textContent = msg;
    errorDiv.style.display = 'block';
  }

  // Google register button
  document.getElementById('pmGoogleRegisterBtn')?.addEventListener('click', () => {
    if (typeof google !== 'undefined' && google.accounts) {
      google.accounts.id.prompt();
    } else {
      Swal.fire({
        icon: 'info',
        title: 'Google',
        text: 'El registro con Google estará disponible próximamente.',
        confirmButtonText: 'Entendido',
        customClass: { popup: 'pm-modal', confirmButton: 'btn-pm' },
      });
    }
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
