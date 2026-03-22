<?php
/**
 * Piel Morena - Iniciar Sesión
 */
$titulo_pagina = 'Iniciar Sesión — Piel Morena Estética';
$meta_descripcion = 'Inicia sesión en tu cuenta de Piel Morena para gestionar tus citas y más.';
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
      <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">

        <!-- Card -->
        <div class="pm-auth-card">

          <!-- Header -->
          <div class="pm-auth-header">
            <div class="pm-auth-icon">
              <i class="bi bi-person-circle"></i>
            </div>
            <h1 class="pm-auth-title">Iniciar Sesión</h1>
            <p class="pm-auth-subtitle">Ingresá a tu cuenta para gestionar tus citas</p>
          </div>

          <!-- Google Login Button -->
          <div class="pm-auth-google">
            <button type="button" class="btn pm-btn-google w-100" id="pmGoogleLoginBtn">
              <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4"/>
                <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 009 18z" fill="#34A853"/>
                <path d="M3.964 10.71A5.41 5.41 0 013.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 000 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 00.957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
              </svg>
              <span>Continuar con Google</span>
            </button>
          </div>

          <!-- Divider -->
          <div class="pm-auth-divider">
            <span>o ingresá con tu email</span>
          </div>

          <!-- Login Form -->
          <form id="pmLoginForm" class="pm-auth-form" novalidate>
            <input type="hidden" name="redirect" value="<?= $redirect ?>" />

            <!-- Email -->
            <div class="pm-form-group">
              <label for="login-email" class="pm-form-label">
                <i class="bi bi-envelope me-1"></i>Correo electrónico
              </label>
              <input
                id="login-email"
                type="email"
                class="input-pm w-100"
                placeholder="tu@email.com"
                autocomplete="email"
                required
              />
            </div>

            <!-- Password -->
            <div class="pm-form-group">
              <label for="login-password" class="pm-form-label">
                <i class="bi bi-lock me-1"></i>Contraseña
              </label>
              <div class="pm-input-password-wrap">
                <input
                  id="login-password"
                  type="password"
                  class="input-pm w-100"
                  placeholder="Tu contraseña"
                  autocomplete="current-password"
                  required
                />
                <button type="button" class="pm-toggle-pass" data-target="login-password" aria-label="Mostrar contraseña">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <!-- Forgot password -->
            <div class="pm-form-footer">
              <a href="#" class="pm-link-forgot" id="pmForgotLink">
                <i class="bi bi-question-circle me-1"></i>¿Olvidaste tu contraseña?
              </a>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-pm w-100 pm-btn-submit">
              <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
            </button>

            <!-- Error message container -->
            <div id="pmLoginError" class="pm-auth-error" style="display:none;"></div>
          </form>

          <!-- Register link -->
          <div class="pm-auth-alt">
            <p>¿No tenés cuenta? <a href="<?= URL_BASE ?>/registro.php<?= $redirect ? '?redirect=' . urlencode($redirect) : '' ?>" class="pm-link">Creá una cuenta</a></p>
          </div>

        </div><!-- /card -->

      </div>
    </div>
  </div>
</section>

<!-- Login Page Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('pmLoginForm');
  const errorDiv = document.getElementById('pmLoginError');
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

  // Forgot password link
  document.getElementById('pmForgotLink')?.addEventListener('click', (e) => {
    e.preventDefault();
    if (typeof ForgotPasswordModal !== 'undefined') {
      ForgotPasswordModal.open();
    }
  });

  // Form submit
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorDiv.style.display = 'none';

    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;

    if (!email || !password) {
      showError('Completá tu email y contraseña.');
      return;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showError('Ingresá un correo electrónico válido.');
      return;
    }

    const submitBtn = form.querySelector('.pm-btn-submit');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Ingresando...';

    try {
      const response = await fetch('api/auth/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();

      if (data.success) {
        const redirect = redirectInput?.value;
        window.location.href = redirect || '<?= URL_BASE ?>/';
      } else {
        showError(data.error || 'Credenciales incorrectas.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Ingresar';
      }
    } catch {
      showError('Error de conexión. Intentá de nuevo.');
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Ingresar';
    }
  });

  function showError(msg) {
    errorDiv.textContent = msg;
    errorDiv.style.display = 'block';
  }

  // Google login button (placeholder until OAuth is configured)
  document.getElementById('pmGoogleLoginBtn')?.addEventListener('click', () => {
    if (typeof google !== 'undefined' && google.accounts) {
      google.accounts.id.prompt();
    } else {
      Swal.fire({
        icon: 'info',
        title: 'Google Login',
        text: 'El inicio de sesión con Google estará disponible próximamente.',
        confirmButtonText: 'Entendido',
        customClass: { popup: 'pm-modal', confirmButton: 'btn-pm' },
      });
    }
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
