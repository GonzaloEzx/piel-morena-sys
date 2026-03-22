<?php
/**
 * Piel Morena - Header Público
 * Navbar, meta tags, CSS, sesión de usuario
 */

// ── Inicialización (si no se cargó antes) ─────────────────
if (!defined('PIEL_MORENA')) {
    require_once __DIR__ . '/init.php';
}

// ── Estado de autenticación ───────────────────────────────
$logueado      = esta_autenticado();
$nombreUsuario = sanitizar($_SESSION['usuario_nombre'] ?? '');
$rolUsuario    = $_SESSION['usuario_rol'] ?? '';

// ── Página actual (para nav activo) ──────────────────────
$uri           = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pagina_actual = basename($uri, '.php') ?: 'index';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- ── SEO ──────────────────────────────────────────────── -->
  <title><?= $titulo_pagina ?? 'Piel Morena Estética — Tu belleza, nuestra pasión' ?></title>
  <meta name="description" content="<?= $meta_descripcion ?? 'Salón de belleza, depilación, tratamientos de frío y estética corporal/facial. Reserva tu cita online.' ?>" />
  <meta name="keywords" content="salón de belleza, depilación, estética, tratamientos faciales, tratamientos corporales, crioterapia, piel morena" />
  <meta name="author" content="Piel Morena Estética" />
  <meta name="robots" content="index, follow" />
  <meta name="language" content="es" />

  <!-- ── Open Graph ───────────────────────────────────────── -->
  <meta property="og:title" content="<?= $titulo_pagina ?? 'Piel Morena Estética — Tu belleza, nuestra pasión' ?>" />
  <meta property="og:description" content="<?= $meta_descripcion ?? 'Salón de belleza, depilación, tratamientos de frío y estética corporal/facial. Reserva tu cita online.' ?>" />
  <meta property="og:image" content="<?= URL_BASE ?>/assets/img/og-piel-morena.jpg" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="<?= URL_BASE . $_SERVER['REQUEST_URI'] ?>" />
  <meta property="og:locale" content="es_MX" />
  <meta property="og:site_name" content="Piel Morena Estética" />

  <!-- ── Favicon ──────────────────────────────────────────── -->
  <link rel="icon" type="image/svg+xml" href="<?= URL_BASE ?>/assets/img/favicon.svg" />
  <link rel="apple-touch-icon" href="<?= URL_BASE ?>/assets/img/favicon.svg" />
  <meta name="theme-color" content="#957C62" />

  <!-- ── Schema.org ───────────────────────────────────────── -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BeautySalon",
    "name": "Piel Morena Estética",
    "description": "Salón de belleza, depilación, tratamientos de frío y estética corporal/facial.",
    "image": "<?= URL_BASE ?>/assets/img/og-piel-morena.jpg",
    "telephone": "<?= TELEFONO_NEGOCIO ?>",
    "email": "<?= EMAIL_NEGOCIO ?>",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "<?= DIRECCION_NEGOCIO ?>"
    },
    "openingHoursSpecification": [
      {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"],
        "opens": "09:00",
        "closes": "20:00"
      },
      {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": "Saturday",
        "opens": "09:00",
        "closes": "14:00"
      }
    ]
  }
  </script>

  <!-- ── Preconexiones ────────────────────────────────────── -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />
  <link rel="preconnect" href="https://accounts.google.com" crossorigin />

  <!-- ── Google Fonts ─────────────────────────────────────── -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&family=Poppins:wght@500;600&display=swap" rel="stylesheet" />

  <!-- ── Bootstrap 5.3.3 CSS ──────────────────────────────── -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />

  <!-- ── Bootstrap Icons ──────────────────────────────────── -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" crossorigin="anonymous" />

  <!-- ── SweetAlert2 CSS ──────────────────────────────────── -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />

  <!-- ── CSS Principal (local) ────────────────────────────── -->
  <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/style.css?v=<?= filemtime(ROOT_PATH . 'assets/css/style.css') ?: time() ?>" />

  <!-- ── Premium Design v3.0 — Warm Luxury Botanical ─────── -->
  <?php
  $premiumCss = ROOT_PATH . 'assets/css/premium-v3.css';
  $premiumAuthCss = ROOT_PATH . 'assets/css/premium-auth.css';
  if (file_exists($premiumCss)): ?>
  <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/premium-v3.css?v=<?= filemtime($premiumCss) ?>" />
  <?php endif; ?>
  <?php if (file_exists($premiumAuthCss)): ?>
  <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/premium-auth.css?v=<?= filemtime($premiumAuthCss) ?>" />
  <?php endif; ?>

</head>

<body>

<!-- ════════════════════════════════════════════════════════════
     NAVBAR — .pm-navbar
     ════════════════════════════════════════════════════════════ -->
<nav id="pm-navbar" class="pm-navbar navbar navbar-expand-lg fixed-top" aria-label="Navegación principal">
  <div class="container">

    <!-- ── Brand ──────────────────────────────────────────── -->
    <a class="navbar-brand pm-navbar-brand" href="<?= URL_BASE ?>/" aria-label="Inicio — Piel Morena Estética">
      <img src="<?= URL_BASE ?>/assets/img/piel-morena-instagram-logo.jpg" alt="Piel Morena" class="pm-navbar-logo">
      <span class="pm-navbar-nombre">Piel Morena</span>
    </a>

    <!-- ── Toggler mobile (abre offcanvas) ────────────────── -->
    <button class="navbar-toggler pm-navbar-toggler border-0" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#pmOffcanvas"
            aria-controls="pmOffcanvas" aria-expanded="false" aria-label="Abrir menú">
      <i class="bi bi-list"></i>
    </button>

    <!-- ── Menú desktop (collapse) ────────────────────────── -->
    <div class="collapse navbar-collapse" id="pmNavbarCollapse">
      <ul class="navbar-nav mx-auto align-items-lg-center gap-lg-1">

        <li class="nav-item">
          <a class="nav-link pm-nav-link <?= $pagina_actual === 'index' ? 'active' : '' ?>"
             href="<?= URL_BASE ?>/#hero">Inicio</a>
        </li>

        <li class="nav-item">
          <a class="nav-link pm-nav-link <?= $pagina_actual === 'servicios' ? 'active' : '' ?>"
             href="<?= URL_BASE ?>/#servicios">Servicios</a>
        </li>

        <li class="nav-item">
          <a class="nav-link pm-nav-link <?= $pagina_actual === 'equipo' ? 'active' : '' ?>"
             href="<?= URL_BASE ?>/#equipo">Equipo</a>
        </li>

        <li class="nav-item">
          <a class="nav-link pm-nav-link <?= $pagina_actual === 'galeria' ? 'active' : '' ?>"
             href="<?= URL_BASE ?>/#galeria">Galería</a>
        </li>

        <li class="nav-item">
          <a class="nav-link pm-nav-link <?= $pagina_actual === 'contacto' ? 'active' : '' ?>"
             href="<?= URL_BASE ?>/#contacto">Contacto</a>
        </li>

      </ul>

      <!-- ── Botones de acción (desktop) ──────────────────── -->
      <div class="pm-navbar-actions d-flex align-items-center gap-2">

        <!-- Reservar Cita -->
        <a href="<?= URL_BASE ?>/reservar.php" class="btn btn-pm btn-pm-sm">
          <i class="bi bi-calendar-check me-1"></i>Reservar Cita
        </a>

        <!-- Auth -->
        <?php if ($logueado): ?>
          <div class="dropdown">
            <button class="btn btn-pm-outline btn-pm-sm dropdown-toggle pm-user-btn" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle me-1"></i>
              <?= $nombreUsuario ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end pm-dropdown">
              <li>
                <a class="dropdown-item" href="<?= URL_BASE ?>/mi-cuenta.php">
                  <i class="bi bi-person-fill me-2"></i>Mi cuenta
                </a>
              </li>
              <?php if ($rolUsuario === 'admin'): ?>
              <li>
                <a class="dropdown-item" href="<?= URL_ADMIN ?>/">
                  <i class="bi bi-grid-1x2-fill me-2"></i>Panel Admin
                </a>
              </li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item pm-dropdown-danger" href="<?= URL_BASE ?>/api/auth/logout.php">
                  <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                </a>
              </li>
            </ul>
          </div>
        <?php else: ?>
          <button class="btn btn-pm-outline btn-pm-sm pm-btn-login" id="pmBtnLogin" type="button">
            <i class="bi bi-box-arrow-in-right me-1"></i>Ingresar
          </button>
        <?php endif; ?>

      </div>
    </div><!-- /collapse -->

  </div><!-- /container -->
</nav>

<!-- ════════════════════════════════════════════════════════════
     BOTTOM SHEET — Menú mobile (slide-up from bottom)
     ════════════════════════════════════════════════════════════ -->
<div class="offcanvas offcanvas-bottom pm-offcanvas pm-bottom-sheet" tabindex="-1" id="pmOffcanvas" aria-labelledby="pmOffcanvasLabel">

  <!-- Drag handle -->
  <div class="pm-sheet-handle" aria-hidden="true">
    <span class="pm-sheet-handle-bar"></span>
  </div>

  <!-- Header -->
  <div class="offcanvas-header pm-offcanvas-header">
    <h5 class="offcanvas-title pm-offcanvas-title" id="pmOffcanvasLabel">
      <img src="<?= URL_BASE ?>/assets/img/piel-morena-instagram-logo.jpg" alt="Piel Morena" class="pm-navbar-logo"> Piel Morena
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
  </div>

  <!-- Body -->
  <div class="offcanvas-body pm-offcanvas-body">
    <ul class="navbar-nav pm-offcanvas-nav">

      <li class="nav-item">
        <a class="nav-link pm-offcanvas-link pm-sheet-nav <?= $pagina_actual === 'index' ? 'active' : '' ?>"
           href="#hero">
          <i class="bi bi-house"></i><span>Inicio</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link pm-offcanvas-link pm-sheet-nav <?= $pagina_actual === 'servicios' ? 'active' : '' ?>"
           href="#servicios">
          <i class="bi bi-stars"></i><span>Servicios</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link pm-offcanvas-link pm-sheet-nav <?= $pagina_actual === 'equipo' ? 'active' : '' ?>"
           href="#equipo">
          <i class="bi bi-people"></i><span>Equipo</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link pm-offcanvas-link pm-sheet-nav <?= $pagina_actual === 'galeria' ? 'active' : '' ?>"
           href="#galeria">
          <i class="bi bi-images"></i><span>Galería</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link pm-offcanvas-link pm-sheet-nav <?= $pagina_actual === 'contacto' ? 'active' : '' ?>"
           href="#contacto">
          <i class="bi bi-envelope"></i><span>Contacto</span>
        </a>
      </li>

    </ul>

    <!-- Divider -->
    <hr class="pm-offcanvas-divider" />

    <!-- Acciones mobile -->
    <div class="pm-offcanvas-actions">
      <a href="<?= URL_BASE ?>/reservar.php" class="btn btn-pm w-100 mb-3 pm-sheet-action">
        <i class="bi bi-calendar-check me-2"></i>Reservar Cita
      </a>

      <?php if ($logueado): ?>
        <a href="<?= URL_BASE ?>/mi-cuenta.php" class="btn btn-pm-outline w-100 mb-2 pm-sheet-action">
          <i class="bi bi-person-fill me-2"></i>Mi cuenta
        </a>
        <?php if ($rolUsuario === 'admin'): ?>
        <a href="<?= URL_ADMIN ?>/" class="btn btn-pm-outline w-100 mb-2 pm-sheet-action">
          <i class="bi bi-grid-1x2-fill me-2"></i>Panel Admin
        </a>
        <?php endif; ?>
        <a href="<?= URL_BASE ?>/api/auth/logout.php" class="btn btn-pm-ghost w-100 pm-sheet-action">
          <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
        </a>
      <?php else: ?>
        <button class="btn btn-pm-outline w-100 pm-btn-login pm-sheet-action" type="button">
          <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
        </button>
      <?php endif; ?>
    </div>

    <!-- Info rápida mobile -->
    <div class="pm-offcanvas-info">
      <hr class="pm-offcanvas-divider" />
      <div class="pm-sheet-info-row">
        <span class="pm-offcanvas-horario">
          <i class="bi bi-clock"></i> Lun-Vie 9:00 - 20:00
        </span>
        <span class="pm-offcanvas-telefono">
          <i class="bi bi-telephone-fill"></i>
          <a href="tel:<?= TELEFONO_NEGOCIO ?>"><?= TELEFONO_NEGOCIO ?></a>
        </span>
        <span class="pm-offcanvas-whatsapp">
          <i class="bi bi-whatsapp"></i>
          <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', TELEFONO_NEGOCIO) ?>" target="_blank" rel="noopener">WhatsApp</a>
        </span>
      </div>
    </div>

  </div><!-- /offcanvas-body -->
</div><!-- /offcanvas -->

<!-- ── Main content wrapper ───────────────────────────────── -->
<main id="main-content">
