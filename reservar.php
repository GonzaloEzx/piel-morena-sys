<?php
$pagina_actual = 'reservar';
$titulo_pagina = 'Servicios y turnos · Piel Morena';
require_once __DIR__ . '/includes/public-content.php';
$id = filter_input(INPUT_GET, 'servicio', FILTER_VALIDATE_INT);
$selected = null;
$groups = [];
foreach ($pm_public_services as $service) {
    $groups[$service['grupo']][] = $service;
    if ($id !== false && $id !== null && (int) $service['id'] === $id) $selected = $service;
}
$category = is_string($_GET['categoria'] ?? null) ? $_GET['categoria'] : '';
if ($selected) $category = $selected['grupo'];
$pm_public_selected_name = $selected['nombre'] ?? '';
require __DIR__ . '/includes/public-header.php';
?>
<main id="contenido" class="pmv-catalog-page" tabindex="-1">
  <div class="container">
    <a class="pmv-back" href="<?= URL_BASE ?>/#servicios"><i class="bi bi-arrow-left" aria-hidden="true"></i>Volver a Piel Morena</a>
    <div class="pmv-catalog-intro"><p class="pmv-eyebrow">Tu cuidado, a tu ritmo</p><h1>Encontrá tu próximo<br>momento para vos.</h1><p class="pmv-lead">Explorá los servicios. Coordinamos tu turno por WhatsApp.</p></div>
    <?php if ($pm_public_preview): ?><div class="pmv-catalog-notice" role="note"><strong>Estás viendo un catálogo de ejemplo.</strong><p>Precios en pesos argentinos, servicios y duraciones de referencia. No constituyen una oferta vigente. Consultá el presupuesto y la disponibilidad con Mari.</p></div><?php endif; ?>
    <?php if ($selected): ?><section class="pmv-selected" aria-labelledby="pmv-selected-title"><div><p class="pmv-eyebrow">Tu elección<?= $pm_public_preview ? ' · ejemplo' : '' ?></p><h2 id="pmv-selected-title"><?= pm_public_escape($selected['nombre']) ?></h2><p><?= (int) $selected['duracion_minutos'] ?> min · <?= pm_public_price($selected['precio']) ?><?= $pm_public_preview ? ' · precio de ejemplo' : '' ?></p></div><a class="pmv-button" href="<?= pm_public_escape(pm_wa_turno($selected['nombre'])) ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp" aria-hidden="true"></i>Consultar este servicio</a></section><?php endif; ?>
    <div class="pmv-catalog-layout row gy-4 gx-lg-5">
      <aside class="col-lg-4"><div class="pmv-catalog-aside"><h2>¿No sabés qué elegir?</h2><p>No hace falta tenerlo decidido. Contanos qué buscás y te orientamos.</p><a class="pmv-button pmv-button-outline" href="<?= pm_public_escape(pm_wa_turno()) ?>" target="_blank" rel="noopener">Necesito asesoramiento <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a><p class="pmv-small">El turno queda confirmado cuando acordamos el día y horario por WhatsApp.</p><a class="pmv-text-link" href="<?= URL_BASE ?>/#primera-visita">Cómo es tu primera visita <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div></aside>
      <section id="catalogo" class="col-lg-8" aria-labelledby="pmv-catalog-title"><h2 id="pmv-catalog-title" class="pmv-catalog-title">Servicios y precios</h2>
        <div class="pmv-search"><label for="pmv-search">Buscá un servicio</label><div><i class="bi bi-search" aria-hidden="true"></i><input id="pmv-search" type="search" placeholder="Por ejemplo: facial, cejas, masaje" autocomplete="off" aria-describedby="pmv-search-results"></div><p id="pmv-search-results" role="status" aria-live="polite"></p></div>
        <?php foreach ($groups as $key => $services): $cat = $pm_public_categories[$key]; ?>
        <details class="pmv-catalog-group" id="categoria-<?= pm_public_escape($key) ?>" <?= $category === $key || ($category === '' && $key === 'faciales') ? 'open' : '' ?>><summary><span><i class="bi <?= $cat['icono'] ?>" aria-hidden="true"></i><?= $cat['nombre'] ?></span><span class="pmv-group-count"><?= count($services) ?> opciones<i class="bi bi-plus" aria-hidden="true"></i></span></summary><ul>
          <?php foreach ($services as $s): ?><li data-service="<?= pm_public_escape($s['nombre'] . ' ' . $cat['nombre']) ?>"><div class="pmv-service-row"><div><h3><?= pm_public_escape($s['nombre']) ?></h3><p class="pmv-service-description"><?= pm_public_escape($s['descripcion'] ?? 'Consultá qué incluye y si se adapta a lo que buscás.') ?></p><p class="pmv-service-time"><?= (int) $s['duracion_minutos'] ?> min<?= $pm_public_preview ? ' aprox. · ejemplo' : '' ?></p></div><div class="pmv-service-price"><strong><?= pm_public_price($s['precio']) ?></strong><?php if ($pm_public_preview): ?><small>Precio de ejemplo</small><?php endif; ?></div></div><a class="pmv-text-link" href="<?= pm_public_escape(pm_wa_turno($s['nombre'])) ?>" target="_blank" rel="noopener">Consultar por WhatsApp <i class="bi bi-arrow-up-right" aria-hidden="true"></i><span class="visually-hidden">: <?= pm_public_escape($s['nombre']) ?></span></a></li><?php endforeach; ?>
        </ul></details>
        <?php endforeach; ?>
        <div id="pmv-no-results" class="pmv-empty" hidden><h3>No encontramos ese servicio.</h3><p>Probá con otra palabra o contanos qué necesitás.</p><a class="pmv-text-link" href="<?= pm_public_escape(pm_wa_turno()) ?>" target="_blank" rel="noopener">Consultar con Piel Morena <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></div>
        <p class="pmv-small pmv-catalog-end">Abrir WhatsApp no confirma una reserva. Revisá el servicio, presupuesto y horario con el salón antes de tu visita.</p>
      </section>
    </div>
  </div>
</main>
<?php require __DIR__ . '/includes/public-footer.php'; ?>
