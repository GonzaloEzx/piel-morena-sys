<?php
/**
 * Piel Morena - Admin Header (Layout con sidebar)
 * Requiere: rol admin o empleado para acceder
 */

if (!defined('PIEL_MORENA')) {
    require_once __DIR__ . '/../../includes/init.php';
}

// Permitir admin y empleado en el panel
$rol_requerido = $rol_admin_requerido ?? 'admin';
if ($rol_requerido === 'empleado') {
    requerir_auth();
    if (!tiene_rol('admin') && !tiene_rol('empleado')) {
        redirigir(URL_BASE . '/');
    }
} else {
    requerir_rol('admin');
}

$logueado      = esta_autenticado();
$nombreUsuario = sanitizar($_SESSION['usuario_nombre'] ?? '');
$rolUsuario    = usuario_actual_rol();
$esAdmin       = tiene_rol('admin');
$uri           = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$vista_actual  = basename($uri, '.php') ?: 'index';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $titulo_admin ?? 'Panel Admin' ?> — Piel Morena</title>
  <meta name="robots" content="noindex, nofollow" />

  <!-- Preconexiones -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&family=Poppins:wght@500;600&display=swap" rel="stylesheet" />

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" />

  <!-- DataTables Bootstrap 5 -->
  <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

  <!-- SweetAlert2 -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />

  <!-- Admin CSS -->
  <link rel="stylesheet" href="<?= URL_ADMIN ?>/assets/css/admin.css?v=<?= @filemtime(ADMIN_PATH . 'assets/css/admin.css') ?: time() ?>" />

  <!-- Premium Admin Design -->
  <?php $premiumAdminCss = ADMIN_PATH . 'assets/css/premium-admin.css';
  if (file_exists($premiumAdminCss)): ?>
  <link rel="stylesheet" href="<?= URL_ADMIN ?>/assets/css/premium-admin.css?v=<?= filemtime($premiumAdminCss) ?>" />
  <?php endif; ?>

  <?php if (!empty($extra_css)): ?>
    <?= $extra_css ?>
  <?php endif; ?>
</head>
<body class="pm-admin-body">

<!-- Sidebar -->
<aside id="pmSidebar" class="pm-sidebar">
  <!-- Brand -->
  <div class="pm-sidebar-brand">
    <span class="pm-sidebar-emoji">&#127832;</span>
    <span class="pm-sidebar-nombre">Piel Morena</span>
    <span class="pm-sidebar-badge"><?= $esAdmin ? 'Admin' : 'Staff' ?></span>
  </div>

  <!-- Nav -->
  <nav class="pm-sidebar-nav">
    <ul class="pm-sidebar-menu">
      <?php if ($esAdmin): ?>
      <li class="pm-sidebar-item">
        <a href="<?= URL_ADMIN ?>/" class="pm-sidebar-link <?= $vista_actual === 'index' ? 'active' : '' ?>">
          <i class="bi bi-grid-1x2-fill"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <?php endif; ?>

      <li class="pm-sidebar-divider"><?= $esAdmin ? 'Gestión' : 'Mi Panel' ?></li>

      <li class="pm-sidebar-item">
        <a href="<?= URL_ADMIN ?>/views/<?= $esAdmin ? 'citas' : 'mis-citas' ?>.php" class="pm-sidebar-link <?= in_array($vista_actual, ['citas','mis-citas']) ? 'active' : '' ?>">
          <i class="bi bi-calendar-check"></i>
          <span><?= $esAdmin ? 'Citas' : 'Mis Citas' ?></span>
        </a>
      </li>

      <?php if (!$esAdmin): ?>
      <li class="pm-sidebar-item">
        <a href="<?= URL_ADMIN ?>/views/mi-horario.php" class="pm-sidebar-link <?= $vista_actual === 'mi-horario' ? 'active' : '' ?>">
          <i class="bi bi-clock"></i>
          <span>Mi Horario</span>
        </a>
      </li>
      <?php endif; ?>

      <?php if ($esAdmin): ?>
      <li class="pm-sidebar-item">
        <a href="<?= URL_ADMIN ?>/views/servicios.php" class="pm-sidebar-link <?= $vista_actual === 'servicios' ? 'active' : '' ?>">
          <i class="bi bi-stars"></i>
          <span>Servicios</span>
        </a>
      </li>
      <li class="pm-sidebar-item">
        <a href="<?= URL_ADMIN ?>/views/clientes.php" class="pm-sidebar-link <?= $vista_actual === 'clientes' ? 'active' : '' ?>">
          <i class="bi bi-people-fill"></i>
          <span>Clientes</span>
        </a>
      </li>
      <li class="pm-sidebar-item">
        <a href="<?= URL_ADMIN ?>/views/empleados.php" class="pm-sidebar-link <?= $vista_actual === 'empleados' ? 'active' : '' ?>">
          <i class="bi bi-person-badge"></i>
          <span>Empleados</span>
        </a>
      </li>
      <li class="pm-sidebar-item">
        <a href="<?= URL_ADMIN ?>/views/productos.php" class="pm-sidebar-link <?= $vista_actual === 'productos' ? 'active' : '' ?>">
          <i class="bi bi-box-seam"></i>
          <span>Productos</span>
        </a>
      </li>

      <li class="pm-sidebar-divider">Finanzas</li>

      <li class="pm-sidebar-item">
        <a href="<?= URL_ADMIN ?>/views/caja.php" class="pm-sidebar-link <?= $vista_actual === 'caja' ? 'active' : '' ?>">
          <i class="bi bi-cash-stack"></i>
          <span>Caja</span>
        </a>
      </li>
      <li class="pm-sidebar-item">
        <a href="<?= URL_ADMIN ?>/views/reportes.php" class="pm-sidebar-link <?= $vista_actual === 'reportes' ? 'active' : '' ?>">
          <i class="bi bi-bar-chart-line"></i>
          <span>Reportes</span>
        </a>
      </li>

      <li class="pm-sidebar-divider">Sistema</li>

      <li class="pm-sidebar-item">
        <a href="<?= URL_ADMIN ?>/views/mensajes.php" class="pm-sidebar-link <?= $vista_actual === 'mensajes' ? 'active' : '' ?>">
          <i class="bi bi-envelope-fill"></i>
          <span>Mensajes</span>
        </a>
      </li>
      <?php endif; ?>
      <li class="pm-sidebar-item">
        <a href="<?= URL_ADMIN ?>/views/configuracion.php" class="pm-sidebar-link <?= $vista_actual === 'configuracion' ? 'active' : '' ?>">
          <i class="bi bi-gear-fill"></i>
          <span>Configuraci&oacute;n</span>
        </a>
      </li>
    </ul>
  </nav>

  <!-- Footer sidebar -->
  <div class="pm-sidebar-footer">
    <a href="<?= URL_BASE ?>/" class="pm-sidebar-link" target="_blank">
      <i class="bi bi-box-arrow-up-right"></i>
      <span>Ver sitio</span>
    </a>
  </div>
</aside>

<!-- Main content wrapper -->
<div id="pmAdminMain" class="pm-admin-main">

  <!-- Top bar -->
  <header class="pm-topbar">
    <button id="pmSidebarToggle" class="pm-topbar-toggle" type="button" aria-label="Toggle sidebar">
      <i class="bi bi-list"></i>
    </button>

    <h1 class="pm-topbar-title"><?= $titulo_admin ?? 'Dashboard' ?></h1>

    <div class="pm-topbar-actions">
      <!-- Campana de notificaciones -->
      <div class="dropdown me-2">
        <button class="pm-topbar-toggle position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="btnNotifCampana" title="Notificaciones">
          <i class="bi bi-bell"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notifBadge" style="font-size:.65rem">0</span>
        </button>
        <div class="dropdown-menu dropdown-menu-end shadow" style="width:320px;max-height:380px;overflow-y:auto" id="notifDropdown">
          <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
            <strong style="font-size:.85rem">Notificaciones</strong>
            <button class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size:.75rem" onclick="marcarTodasLeidas()">Marcar todas</button>
          </div>
          <div id="notifLista">
            <div class="text-center text-muted py-3" style="font-size:.82rem">Cargando...</div>
          </div>
        </div>
      </div>

      <div class="dropdown">
        <button class="pm-topbar-user dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-person-circle"></i>
          <span class="d-none d-md-inline"><?= $nombreUsuario ?></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="<?= URL_BASE ?>/" target="_blank"><i class="bi bi-house me-2"></i>Ir al sitio</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="<?= URL_BASE ?>/api/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesi&oacute;n</a></li>
        </ul>
      </div>
    </div>
  </header>

  <!-- Page content -->
  <div class="pm-admin-content">
