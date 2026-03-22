'use strict';

/* =======================================================================
   auth.js — Piel Morena Estetica
   Maneja login, registro y logout via SweetAlert2 modales
   Dependencias: SweetAlert2, Bootstrap Icons
   ======================================================================= */


/* ═══════════════════════════════════════════════════════════
   LOGIN MODAL — autenticacion de usuarios
   ═══════════════════════════════════════════════════════════ */
const LoginModal = {

  open() {
    Swal.fire({
      title: '<i class="bi bi-person-circle me-2"></i>Iniciar Sesion',
      html: `
        <form id="swalLoginForm" class="pm-auth-form" autocomplete="on">
          <!-- Email -->
          <div class="pm-form-group">
            <label for="swal-login-email" class="pm-form-label">
              <i class="bi bi-envelope me-1"></i>Correo electronico
            </label>
            <input
              id="swal-login-email"
              type="email"
              class="input-pm w-100"
              placeholder="tu@email.com"
              autocomplete="email"
              required
            />
          </div>

          <!-- Password -->
          <div class="pm-form-group">
            <label for="swal-login-pass" class="pm-form-label">
              <i class="bi bi-lock me-1"></i>Contrasena
            </label>
            <div class="pm-input-password-wrap">
              <input
                id="swal-login-pass"
                type="password"
                class="input-pm w-100"
                placeholder="Tu contrasena"
                autocomplete="current-password"
                required
              />
              <button type="button" class="pm-toggle-pass" data-target="swal-login-pass" aria-label="Mostrar contrasena">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <!-- Olvidaste contrasena -->
          <div class="pm-form-footer">
            <a href="#" id="swal-forgot-link" class="pm-link-forgot">
              <i class="bi bi-question-circle me-1"></i>Olvidaste tu contrasena?
            </a>
          </div>
        </form>
      `,
      confirmButtonText: '<i class="bi bi-box-arrow-in-right me-1"></i>Ingresar',
      cancelButtonText: 'Cancelar',
      showCancelButton: true,
      focusConfirm: false,
      customClass: {
        popup:         'pm-modal',
        title:         'pm-modal-title',
        htmlContainer: 'pm-modal-html',
        confirmButton: 'btn-pm',
        cancelButton:  'btn-pm-outline',
      },

      didOpen() {
        // Focus al email
        const emailInput = document.getElementById('swal-login-email');
        if (emailInput) emailInput.focus();

        // Enter en password -> confirmar
        const passInput = document.getElementById('swal-login-pass');
        if (passInput) {
          passInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
              e.preventDefault();
              Swal.clickConfirm();
            }
          });
        }

        // Toggle password visibility
        document.querySelectorAll('.pm-toggle-pass').forEach(btn => {
          btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.innerHTML = isHidden
              ? '<i class="bi bi-eye-slash"></i>'
              : '<i class="bi bi-eye"></i>';
          });
        });

        // Link "olvidaste tu contrasena"
        const forgotLink = document.getElementById('swal-forgot-link');
        if (forgotLink) {
          forgotLink.addEventListener('click', (e) => {
            e.preventDefault();
            Swal.close();
            ForgotPasswordModal.open();
          });
        }
      },

      preConfirm() {
        const email = document.getElementById('swal-login-email')?.value.trim();
        const pass  = document.getElementById('swal-login-pass')?.value;

        // Validar campos
        if (!email || !pass) {
          Swal.showValidationMessage('Completa tu email y contrasena.');
          return false;
        }

        // Validar formato email
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          Swal.showValidationMessage('Ingresa un correo electronico valido.');
          return false;
        }

        // Fetch al backend
        return fetch('api/auth/login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password: pass }),
        })
          .then(response => {
            if (!response.ok) {
              throw new Error('Error de conexion con el servidor.');
            }
            return response.json();
          })
          .then(data => {
            if (!data.success) {
              Swal.showValidationMessage(data.error || 'Credenciales incorrectas.');
              return false;
            }
            return data;
          })
          .catch(error => {
            Swal.showValidationMessage(error.message || 'Error de conexion. Intenta de nuevo.');
            return false;
          });
      },
    }).then(result => {
      if (result.isConfirmed && result.value) {
        // Login exitoso — recargar pagina
        window.location.reload();
      }
    });
  },
};


/* ═══════════════════════════════════════════════════════════
   REGISTER MODAL — registro de nuevos usuarios
   ═══════════════════════════════════════════════════════════ */
const RegisterModal = {

  open() {
    Swal.fire({
      title: '<i class="bi bi-person-plus me-2"></i>Crear Cuenta',
      html: `
        <form id="swalRegisterForm" class="pm-auth-form" autocomplete="on">
          <!-- Nombre -->
          <div class="pm-form-group">
            <label for="swal-reg-nombre" class="pm-form-label">
              <i class="bi bi-person me-1"></i>Nombre
            </label>
            <input
              id="swal-reg-nombre"
              type="text"
              class="input-pm w-100"
              placeholder="Tu nombre"
              autocomplete="given-name"
              required
            />
          </div>

          <!-- Apellidos -->
          <div class="pm-form-group">
            <label for="swal-reg-apellidos" class="pm-form-label">
              <i class="bi bi-person me-1"></i>Apellidos
            </label>
            <input
              id="swal-reg-apellidos"
              type="text"
              class="input-pm w-100"
              placeholder="Tus apellidos"
              autocomplete="family-name"
              required
            />
          </div>

          <!-- Email -->
          <div class="pm-form-group">
            <label for="swal-reg-email" class="pm-form-label">
              <i class="bi bi-envelope me-1"></i>Correo electronico
            </label>
            <input
              id="swal-reg-email"
              type="email"
              class="input-pm w-100"
              placeholder="tu@email.com"
              autocomplete="email"
              required
            />
          </div>

          <!-- Telefono -->
          <div class="pm-form-group">
            <label for="swal-reg-telefono" class="pm-form-label">
              <i class="bi bi-telephone me-1"></i>Telefono
            </label>
            <input
              id="swal-reg-telefono"
              type="tel"
              class="input-pm w-100"
              placeholder="(55) 1234-5678"
              autocomplete="tel"
            />
          </div>

          <!-- Password -->
          <div class="pm-form-group">
            <label for="swal-reg-pass" class="pm-form-label">
              <i class="bi bi-lock me-1"></i>Contrasena
            </label>
            <div class="pm-input-password-wrap">
              <input
                id="swal-reg-pass"
                type="password"
                class="input-pm w-100"
                placeholder="Minimo 6 caracteres"
                autocomplete="new-password"
                required
              />
              <button type="button" class="pm-toggle-pass" data-target="swal-reg-pass" aria-label="Mostrar contrasena">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <!-- Confirmar Password -->
          <div class="pm-form-group">
            <label for="swal-reg-pass2" class="pm-form-label">
              <i class="bi bi-lock-fill me-1"></i>Confirmar contrasena
            </label>
            <div class="pm-input-password-wrap">
              <input
                id="swal-reg-pass2"
                type="password"
                class="input-pm w-100"
                placeholder="Repite tu contrasena"
                autocomplete="new-password"
                required
              />
              <button type="button" class="pm-toggle-pass" data-target="swal-reg-pass2" aria-label="Mostrar contrasena">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
        </form>
      `,
      confirmButtonText: '<i class="bi bi-check-circle me-1"></i>Crear cuenta',
      cancelButtonText: 'Cancelar',
      showCancelButton: true,
      focusConfirm: false,
      customClass: {
        popup:         'pm-modal',
        title:         'pm-modal-title',
        htmlContainer: 'pm-modal-html',
        confirmButton: 'btn-pm',
        cancelButton:  'btn-pm-outline',
      },

      didOpen() {
        // Focus al nombre
        const nombreInput = document.getElementById('swal-reg-nombre');
        if (nombreInput) nombreInput.focus();

        // Toggle password visibility para todos los toggles del modal
        document.querySelectorAll('.pm-toggle-pass').forEach(btn => {
          btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.innerHTML = isHidden
              ? '<i class="bi bi-eye-slash"></i>'
              : '<i class="bi bi-eye"></i>';
          });
        });
      },

      preConfirm() {
        const nombre    = document.getElementById('swal-reg-nombre')?.value.trim();
        const apellidos = document.getElementById('swal-reg-apellidos')?.value.trim();
        const email     = document.getElementById('swal-reg-email')?.value.trim();
        const telefono  = document.getElementById('swal-reg-telefono')?.value.trim();
        const pass      = document.getElementById('swal-reg-pass')?.value;
        const pass2     = document.getElementById('swal-reg-pass2')?.value;

        // Validar campos requeridos
        if (!nombre) {
          Swal.showValidationMessage('El nombre es requerido.');
          return false;
        }

        if (!apellidos) {
          Swal.showValidationMessage('Los apellidos son requeridos.');
          return false;
        }

        if (!email) {
          Swal.showValidationMessage('El correo electronico es requerido.');
          return false;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          Swal.showValidationMessage('Ingresa un correo electronico valido.');
          return false;
        }

        if (!pass) {
          Swal.showValidationMessage('La contrasena es requerida.');
          return false;
        }

        if (pass.length < 6) {
          Swal.showValidationMessage('La contrasena debe tener al menos 6 caracteres.');
          return false;
        }

        if (pass !== pass2) {
          Swal.showValidationMessage('Las contrasenas no coinciden.');
          return false;
        }

        // Fetch al backend
        return fetch('api/auth/registro.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            nombre,
            apellidos,
            email,
            telefono,
            password: pass,
          }),
        })
          .then(response => {
            if (!response.ok) {
              throw new Error('Error de conexion con el servidor.');
            }
            return response.json();
          })
          .then(data => {
            if (!data.success) {
              Swal.showValidationMessage(data.error || 'No se pudo crear la cuenta.');
              return false;
            }
            return data;
          })
          .catch(error => {
            Swal.showValidationMessage(error.message || 'Error de conexion. Intenta de nuevo.');
            return false;
          });
      },
    }).then(result => {
      if (result.isConfirmed && result.value) {
        // Registro exitoso — mostrar mensaje y abrir login
        Swal.fire({
          icon: 'success',
          title: 'Cuenta creada',
          html: '<p>Tu cuenta ha sido creada exitosamente. Ya puedes iniciar sesion.</p>',
          confirmButtonText: 'Iniciar sesion',
          customClass: {
            popup: 'pm-modal',
            confirmButton: 'btn-pm',
          },
        }).then(() => {
          LoginModal.open();
        });
      }
    });
  },
};


/* ═══════════════════════════════════════════════════════════
   FORGOT PASSWORD MODAL — recuperacion de contrasena (multi-paso)
   Paso 1: email → Paso 2: codigo → Paso 3: nueva contrasena
   ═══════════════════════════════════════════════════════════ */
const ForgotPasswordModal = {

  _email: '',

  /* ── Paso 1: Pedir email ──────────────────────────────── */
  open() {
    Swal.fire({
      title: '<i class="bi bi-key me-2"></i>Recuperar contrasena',
      html: `
        <form id="swalForgotForm" class="pm-auth-form">
          <p class="pm-text-muted mb-3">
            Ingresa tu correo electronico y te enviaremos un codigo de 6 digitos para restablecer tu contrasena.
          </p>
          <div class="pm-form-group">
            <label for="swal-forgot-email" class="pm-form-label">
              <i class="bi bi-envelope me-1"></i>Correo electronico
            </label>
            <input
              id="swal-forgot-email"
              type="email"
              class="input-pm w-100"
              placeholder="tu@email.com"
              autocomplete="email"
              required
            />
          </div>
        </form>
      `,
      confirmButtonText: '<i class="bi bi-send me-1"></i>Enviar codigo',
      cancelButtonText: 'Volver al login',
      showCancelButton: true,
      focusConfirm: false,
      customClass: {
        popup:         'pm-modal',
        title:         'pm-modal-title',
        htmlContainer: 'pm-modal-html',
        confirmButton: 'btn-pm',
        cancelButton:  'btn-pm-outline',
      },

      didOpen() {
        const emailInput = document.getElementById('swal-forgot-email');
        if (emailInput) emailInput.focus();
      },

      preConfirm() {
        const email = document.getElementById('swal-forgot-email')?.value.trim();

        if (!email) {
          Swal.showValidationMessage('Ingresa tu correo electronico.');
          return false;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          Swal.showValidationMessage('Ingresa un correo electronico valido.');
          return false;
        }

        return fetch('api/auth/recuperar.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email }),
        })
          .then(response => response.json())
          .then(data => {
            if (!data.success) {
              Swal.showValidationMessage(data.error || 'No se pudo procesar la solicitud.');
              return false;
            }
            return { email };
          })
          .catch(() => {
            return { email };
          });
      },
    }).then(result => {
      if (result.isConfirmed && result.value) {
        this._email = result.value.email;
        this._stepCode();
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        LoginModal.open();
      }
    });
  },

  /* ── Paso 2: Ingresar codigo ──────────────────────────── */
  _stepCode() {
    Swal.fire({
      title: '<i class="bi bi-shield-lock me-2"></i>Ingresa el codigo',
      html: `
        <form id="swalCodeForm" class="pm-auth-form">
          <p class="pm-text-muted mb-3">
            Enviamos un codigo de 6 digitos a <strong>${this._email}</strong>. Revisa tu bandeja de entrada.
          </p>
          <div class="pm-form-group">
            <label for="swal-forgot-code" class="pm-form-label">
              <i class="bi bi-input-cursor me-1"></i>Codigo de verificacion
            </label>
            <input
              id="swal-forgot-code"
              type="text"
              class="input-pm w-100"
              placeholder="000000"
              maxlength="6"
              pattern="[0-9]{6}"
              inputmode="numeric"
              autocomplete="one-time-code"
              style="text-align:center;font-size:1.5rem;letter-spacing:8px;font-weight:600;"
              required
            />
          </div>
        </form>
      `,
      confirmButtonText: '<i class="bi bi-check-circle me-1"></i>Verificar',
      cancelButtonText: 'Cancelar',
      showCancelButton: true,
      focusConfirm: false,
      customClass: {
        popup:         'pm-modal',
        title:         'pm-modal-title',
        htmlContainer: 'pm-modal-html',
        confirmButton: 'btn-pm',
        cancelButton:  'btn-pm-outline',
      },

      didOpen() {
        const codeInput = document.getElementById('swal-forgot-code');
        if (codeInput) {
          codeInput.focus();
          codeInput.addEventListener('input', () => {
            codeInput.value = codeInput.value.replace(/\D/g, '').slice(0, 6);
          });
        }
      },

      preConfirm() {
        const code = document.getElementById('swal-forgot-code')?.value.trim();

        if (!code || code.length !== 6) {
          Swal.showValidationMessage('Ingresa el codigo de 6 digitos.');
          return false;
        }

        return { code };
      },
    }).then(result => {
      if (result.isConfirmed && result.value) {
        this._stepNewPassword(result.value.code);
      }
    });
  },

  /* ── Paso 3: Nueva contrasena ─────────────────────────── */
  _stepNewPassword(code) {
    Swal.fire({
      title: '<i class="bi bi-lock me-2"></i>Nueva contrasena',
      html: `
        <form id="swalNewPassForm" class="pm-auth-form">
          <div class="pm-form-group">
            <label for="swal-new-pass" class="pm-form-label">
              <i class="bi bi-lock me-1"></i>Nueva contrasena
            </label>
            <div class="pm-input-password-wrap">
              <input
                id="swal-new-pass"
                type="password"
                class="input-pm w-100"
                placeholder="Minimo 6 caracteres"
                autocomplete="new-password"
                required
              />
              <button type="button" class="pm-toggle-pass" data-target="swal-new-pass" aria-label="Mostrar contrasena">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
          <div class="pm-form-group">
            <label for="swal-new-pass2" class="pm-form-label">
              <i class="bi bi-lock-fill me-1"></i>Confirmar contrasena
            </label>
            <div class="pm-input-password-wrap">
              <input
                id="swal-new-pass2"
                type="password"
                class="input-pm w-100"
                placeholder="Repite la contrasena"
                autocomplete="new-password"
                required
              />
              <button type="button" class="pm-toggle-pass" data-target="swal-new-pass2" aria-label="Mostrar contrasena">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
        </form>
      `,
      confirmButtonText: '<i class="bi bi-check-circle me-1"></i>Restablecer contrasena',
      cancelButtonText: 'Cancelar',
      showCancelButton: true,
      focusConfirm: false,
      customClass: {
        popup:         'pm-modal',
        title:         'pm-modal-title',
        htmlContainer: 'pm-modal-html',
        confirmButton: 'btn-pm',
        cancelButton:  'btn-pm-outline',
      },

      didOpen() {
        const passInput = document.getElementById('swal-new-pass');
        if (passInput) passInput.focus();

        document.querySelectorAll('.pm-toggle-pass').forEach(btn => {
          btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            if (!input) return;
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.innerHTML = isHidden ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
          });
        });
      },

      preConfirm: () => {
        const pass = document.getElementById('swal-new-pass')?.value;
        const pass2 = document.getElementById('swal-new-pass2')?.value;

        if (!pass || pass.length < 6) {
          Swal.showValidationMessage('La contrasena debe tener al menos 6 caracteres.');
          return false;
        }
        if (pass !== pass2) {
          Swal.showValidationMessage('Las contrasenas no coinciden.');
          return false;
        }

        return fetch('api/auth/reset-password.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            email: ForgotPasswordModal._email,
            codigo: code,
            password: pass,
          }),
        })
          .then(r => r.json())
          .then(data => {
            if (!data.success) {
              Swal.showValidationMessage(data.error || 'Error al restablecer.');
              return false;
            }
            return data;
          })
          .catch(() => {
            Swal.showValidationMessage('Error de conexion. Intenta de nuevo.');
            return false;
          });
      },
    }).then(result => {
      if (result.isConfirmed && result.value) {
        Swal.fire({
          icon: 'success',
          title: 'Contrasena restablecida',
          html: '<p>Tu contrasena se actualizo correctamente. Ya podes iniciar sesion.</p>',
          confirmButtonText: 'Iniciar sesion',
          customClass: { popup: 'pm-modal', confirmButton: 'btn-pm' },
        }).then(() => {
          LoginModal.open();
        });
      }
    });
  },
};


/* ═══════════════════════════════════════════════════════════
   LOGOUT — cierre de sesion
   ═══════════════════════════════════════════════════════════ */
const Logout = {

  async execute() {
    // Confirmacion antes de cerrar sesion
    const result = await Swal.fire({
      icon: 'question',
      title: 'Cerrar sesion',
      text: 'Estas seguro de que deseas cerrar sesion?',
      confirmButtonText: 'Si, cerrar sesion',
      cancelButtonText: 'Cancelar',
      showCancelButton: true,
      customClass: {
        popup: 'pm-modal',
        confirmButton: 'btn-pm',
        cancelButton: 'btn-pm-outline',
      },
    });

    if (!result.isConfirmed) return;

    try {
      const response = await fetch('api/auth/logout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
      });

      const data = await response.json();

      if (data.success) {
        window.location.reload();
      } else {
        // Recargar de todas formas (la sesion puede haberse cerrado en el server)
        window.location.reload();
      }
    } catch {
      // Error de red — recargar para limpiar estado
      window.location.reload();
    }
  },
};


/* ═══════════════════════════════════════════════════════════
   EVENT LISTENERS — delegacion de eventos para auth
   ═══════════════════════════════════════════════════════════ */
document.addEventListener('click', (e) => {
  // Boton Login (.pm-btn-login en header.php desktop + offcanvas mobile)
  const loginBtn = e.target.closest('.pm-btn-login');
  if (loginBtn) {
    e.preventDefault();
    LoginModal.open();
    return;
  }

  // Boton Register (.pm-btn-register)
  const registerBtn = e.target.closest('.pm-btn-register');
  if (registerBtn) {
    e.preventDefault();
    RegisterModal.open();
    return;
  }

  // Boton Logout (.pm-btn-logout)
  const logoutBtn = e.target.closest('.pm-btn-logout');
  if (logoutBtn) {
    e.preventDefault();
    Logout.execute();
    return;
  }
});
