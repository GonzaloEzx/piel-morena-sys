</main><!-- /main-content -->

<!-- ════════════════════════════════════════════════════════════
     FOOTER — .pm-footer
     ════════════════════════════════════════════════════════════ -->
<footer class="pm-footer" aria-label="Pie de página">
  <div class="container">
    <div class="row g-4 g-lg-5">

      <!-- ── Columna 1: Brand + tagline + redes ──────────── -->
      <div class="col-12 col-md-6 col-lg-3">
        <div class="pm-footer-brand">
          <img src="<?= URL_BASE ?>/assets/img/logo-mark.png" alt="Piel Morena" class="pm-footer-logo">
          <span class="pm-footer-nombre">Piel Morena</span>
        </div>
        <p class="pm-footer-lema">Estética Piel Morena Spa &amp; Beauty</p>
        <p class="pm-footer-tagline">
          Tu espacio de belleza y confianza. Tratamientos profesionales para realzar tu mejor versión.
        </p>
        <div class="pm-footer-redes">
          <a href="<?= defined('INSTAGRAM_NEGOCIO') ? INSTAGRAM_NEGOCIO : '#' ?>" class="pm-footer-red-btn" aria-label="Instagram" target="_blank" rel="noopener">
            <i class="bi bi-instagram"></i>
          </a>
          <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', TELEFONO_NEGOCIO) ?>" class="pm-footer-red-btn" aria-label="WhatsApp" target="_blank" rel="noopener">
            <i class="bi bi-whatsapp"></i>
          </a>
        </div>
      </div>

      <!-- ── Columna 2: Navegación ───────────────────────── -->
      <div class="col-6 col-md-3 col-lg-2">
        <h6 class="pm-footer-titulo">Navegación</h6>
        <ul class="pm-footer-links">
          <li><a href="<?= URL_BASE ?>/#hero">Inicio</a></li>
          <li><a href="<?= URL_BASE ?>/#servicios">Servicios</a></li>
          <li><a href="<?= URL_BASE ?>/#equipo">Equipo</a></li>
          <li><a href="<?= URL_BASE ?>/#galeria">Galería</a></li>
          <li><a href="<?= URL_BASE ?>/#contacto">Contacto</a></li>
        </ul>
      </div>

      <!-- ── Columna 3: Horarios ─────────────────────────── -->
      <div class="col-6 col-md-3 col-lg-3">
        <h6 class="pm-footer-titulo">Horarios</h6>
        <ul class="pm-footer-horarios">
          <li>
            <span class="pm-footer-dia">Lunes - Viernes</span>
            <span class="pm-footer-hora">8:00 — 20:00</span>
          </li>
          <li>
            <span class="pm-footer-dia">Sábado</span>
            <span class="pm-footer-hora">9:00 — 14:00</span>
          </li>
          <li>
            <span class="pm-footer-dia">Domingo</span>
            <span class="pm-footer-hora pm-footer-cerrado">Cerrado</span>
          </li>
        </ul>
      </div>

      <!-- ── Columna 4: Contacto ─────────────────────────── -->
      <div class="col-12 col-md-6 col-lg-4">
        <h6 class="pm-footer-titulo">Contacto</h6>
        <ul class="pm-footer-contacto">
          <li>
            <i class="bi bi-geo-alt-fill"></i>
            <span><?= DIRECCION_NEGOCIO ?></span>
          </li>
          <li>
            <i class="bi bi-telephone-fill"></i>
            <a href="tel:<?= TELEFONO_NEGOCIO ?>"><?= TELEFONO_NEGOCIO ?></a>
          </li>
          <li>
            <i class="bi bi-envelope-fill"></i>
            <a href="mailto:<?= EMAIL_NEGOCIO ?>"><?= EMAIL_NEGOCIO ?></a>
          </li>
          <li>
            <i class="bi bi-whatsapp"></i>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', TELEFONO_NEGOCIO) ?>" target="_blank" rel="noopener">
              Escríbenos por WhatsApp
            </a>
          </li>
        </ul>
      </div>

    </div><!-- /row -->

    <!-- ── Divider ────────────────────────────────────────── -->
    <hr class="pm-footer-divider" />

    <!-- ── Copyright ──────────────────────────────────────── -->
    <div class="pm-footer-bottom">
      <span class="pm-footer-copy">
        &copy; <?= date('Y') ?> Piel Morena Estética. Todos los derechos reservados.
      </span>
    </div>

  </div><!-- /container -->
</footer>

<!-- ════════════════════════════════════════════════════════════
     SCRIPTS
     ════════════════════════════════════════════════════════════ -->

<!-- jQuery 3.7 -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" crossorigin="anonymous"></script>

<!-- Bootstrap 5.3.3 Bundle JS (incluye Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js" crossorigin="anonymous"></script>

<!-- Google Identity Services (OAuth) -->
<?php if (defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID): ?>
<script src="https://accounts.google.com/gsi/client" async defer></script>
<script>
window.addEventListener('load', () => {
  if (typeof google === 'undefined' || !google.accounts) return;
  google.accounts.id.initialize({
    client_id: '<?= GOOGLE_CLIENT_ID ?>',
    callback: (response) => {
      fetch('<?= URL_BASE ?>/api/auth/google.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ credential: response.credential }),
      })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            window.location.reload();
          } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'No se pudo iniciar sesión con Google.', customClass: { popup: 'pm-modal', confirmButton: 'btn-pm' } });
          }
        })
        .catch(() => {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión con Google.', customClass: { popup: 'pm-modal', confirmButton: 'btn-pm' } });
        });
    },
  });

  // Bind all Google buttons
  document.querySelectorAll('#pmGoogleLoginBtn, #pmGoogleRegisterBtn').forEach(btn => {
    btn.addEventListener('click', () => google.accounts.id.prompt());
  });
});
</script>
<?php endif; ?>

<!-- JS locales del proyecto -->
<script src="<?= URL_BASE ?>/assets/js/main.js?v=<?= @filemtime(ROOT_PATH . 'assets/js/main.js') ?: time() ?>"></script>
<script src="<?= URL_BASE ?>/assets/js/auth.js?v=<?= @filemtime(ROOT_PATH . 'assets/js/auth.js') ?: time() ?>"></script>
<script src="<?= URL_BASE ?>/assets/js/banners.js?v=<?= @filemtime(ROOT_PATH . 'assets/js/banners.js') ?: time() ?>"></script>
</body>
</html>
