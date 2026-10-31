<?php
require_once __DIR__ . '/public-content.php';
$pm_public_title = $titulo_pagina ?? 'Piel Morena · Estamos para cuidarte';
$pm_public_description = 'Estética y cuidado personal en Resistencia, Chaco. Conocé los servicios y coordiná tu visita por WhatsApp con Piel Morena.';
if ($pm_public_preview) {
    header('X-Robots-Tag: noindex, nofollow');
}
$pm_canonical = URL_BASE . (($pagina_actual ?? '') === 'reservar' ? '/reservar.php' : '/');
?>
<!doctype html>
<html lang="es-AR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= pm_public_escape($pm_public_title) ?></title>
  <meta name="description" content="<?= pm_public_escape($pm_public_description) ?>">
  <meta name="robots" content="<?= $pm_public_preview ? 'noindex, nofollow' : 'index, follow' ?>">
  <link rel="canonical" href="<?= pm_public_escape($pm_canonical) ?>">
  <meta property="og:title" content="<?= pm_public_escape($pm_public_title) ?>">
  <meta property="og:description" content="<?= pm_public_escape($pm_public_description) ?>">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="es_AR">
  <meta property="og:url" content="<?= pm_public_escape($pm_canonical) ?>">
  <meta property="og:image" content="<?= URL_BASE ?>/assets/img/logo-full.png">
  <meta name="theme-color" content="#F8F4E8">
  <link rel="icon" href="<?= URL_BASE ?>/assets/img/favicon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet" crossorigin="anonymous">
  <link href="<?= URL_BASE ?>/assets/css/style.css?v=<?= filemtime(ROOT_PATH . 'assets/css/style.css') ?>" rel="stylesheet">
  <link href="<?= URL_BASE ?>/assets/css/public-v2.css?v=<?= filemtime(ROOT_PATH . 'assets/css/public-v2.css') ?>" rel="stylesheet">
  <script src="<?= URL_BASE ?>/assets/js/public-v2.js?v=<?= filemtime(ROOT_PATH . 'assets/js/public-v2.js') ?>" defer></script>
</head>
<body class="pm-public">
<a class="pmv-skip" href="#contenido">Ir al contenido</a>
<?php if ($pm_public_preview): ?>
<div class="pmv-preview" role="note">
  <div class="container"><strong>Vista previa · v<?= $pm_public_version ?></strong><span>Servicios, precios y textos de ejemplo. Confirmá la información por WhatsApp.</span></div>
</div>
<?php endif; ?>
<header class="pmv-header">
  <div class="container pmv-header-inner">
    <a class="pmv-brand" href="<?= URL_BASE ?>/" aria-label="Piel Morena, inicio">
      <img src="<?= URL_BASE ?>/assets/img/logo-mark.png" alt="" width="48" height="48">
      <span>Piel Morena<small>Estética &amp; cuidado personal</small></span>
    </a>
    <nav class="pmv-nav-desktop" aria-label="Navegación principal">
      <a href="<?= URL_BASE ?>/#servicios">Servicios</a><a href="<?= URL_BASE ?>/#nosotros">Nuestro espacio</a><a href="<?= URL_BASE ?>/#primera-visita">Tu primera visita</a><a href="<?= URL_BASE ?>/#contacto">Contacto</a>
    </nav>
    <a class="pmv-button pmv-header-cta" href="<?= pm_public_escape(pm_wa_turno()) ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp" aria-hidden="true"></i>Pedir turno</a>
    <details class="pmv-menu">
      <summary aria-label="Menú de navegación"><span>Menú</span><i class="bi bi-list" aria-hidden="true"></i></summary>
      <nav aria-label="Navegación móvil"><a href="<?= URL_BASE ?>/#servicios">Servicios</a><a href="<?= URL_BASE ?>/#nosotros">Nuestro espacio</a><a href="<?= URL_BASE ?>/#equipo">Quiénes te atienden</a><a href="<?= URL_BASE ?>/#primera-visita">Tu primera visita</a><a href="<?= URL_BASE ?>/#contacto">Cómo llegar</a><a href="<?= URL_BASE ?>/reservar.php">Ver servicios y precios</a></nav>
    </details>
  </div>
</header>
