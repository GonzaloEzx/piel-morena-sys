<?php
$pagina_actual = 'inicio';
require_once __DIR__ . '/includes/public-content.php';
require __DIR__ . '/includes/public-header.php';
?>
<main id="contenido" tabindex="-1">
  <section id="hero" class="pmv-hero">
    <div class="container">
      <div class="row align-items-center gy-4 gy-lg-0">
        <div class="col-lg-6 pmv-hero-copy">
          <p class="pmv-eyebrow">Estética en Resistencia, Chaco</p>
          <h1>Estamos<br>para cuidarte.</h1>
          <p class="pmv-lead">Tu piel. Tu tiempo.<br>Un espacio para sentirte bien.</p>
          <div class="pmv-actions">
            <a class="pmv-button" href="<?= pm_public_escape(pm_wa_turno()) ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp" aria-hidden="true"></i>Pedir turno</a>
            <a class="pmv-text-link" href="#servicios">Ver servicios <i class="bi bi-arrow-down" aria-hidden="true"></i></a>
          </div>
          <p class="pmv-small pmv-hero-note">Coordinamos día y horario por WhatsApp.</p>
        </div>
        <div class="col-lg-6">
          <figure class="pmv-hero-figure">
            <div class="pmv-photo-frame"><img src="<?= URL_BASE ?>/assets/img/public/galeria-03.webp" alt="Una pausa durante una sesión facial, fotografía de referencia" width="900" height="1600" fetchpriority="high" decoding="async"><div class="pmv-photo-note"><i class="bi bi-flower1" aria-hidden="true"></i><span>El cuidado empieza<br>por escucharte.</span></div></div>
            <figcaption>Un momento de cuidado · fotografía de referencia</figcaption>
          </figure>
        </div>
      </div>
      <div class="pmv-hero-bottom"><span>Cuidado personalizado</span><span>Atención con turno previo</span></div>
    </div>
  </section>

  <section id="servicios" class="pmv-section pmv-services">
    <span id="tratamientos"></span>
    <div class="container">
      <div class="pmv-section-heading"><div><p class="pmv-eyebrow">Encontrá tu momento</p><h2>¿Qué te gustaría cuidar?</h2></div><p>Podés venir con una idea.<br>O empezar por una conversación.</p></div>
      <?php if ($pm_public_preview): ?><p class="pmv-data-note"><i class="bi bi-info-circle" aria-hidden="true"></i>Precios ARS y duraciones de ejemplo. Fotos de referencia.</p><?php else: ?><p class="pmv-small">Fotografías de referencia.</p><?php endif; ?>
      <div id="galeria" class="row g-4">
      <?php foreach (['faciales', 'mirada', 'manos'] as $key):
          $cat = $pm_public_categories[$key];
          $items = array_values(array_filter($pm_public_services, fn($s) => $s['grupo'] === $key));
          $prices = array_filter(array_column($items, 'precio'), fn($p) => (float) $p > 0);
      ?>
        <div class="col-md-4"><a class="pmv-service-feature" href="<?= URL_BASE ?>/reservar.php?categoria=<?= $key ?>#catalogo">
          <div class="pmv-service-photo"><img src="<?= URL_BASE ?>/assets/img/public/<?= str_replace('.jpg', '.webp', $cat['imagen']) ?>" alt="<?= pm_public_escape($cat['alt']) ?>" width="600" height="700" loading="lazy" decoding="async"><span class="pmv-photo-label">Imagen de referencia</span></div>
          <div class="pmv-service-copy"><div class="pmv-service-heading"><h3><?= $cat['nombre'] ?></h3><i class="bi bi-arrow-up-right" aria-hidden="true"></i></div>
          <p><?= $cat['texto'] ?></p>
          <span class="pmv-service-meta"><?= $prices ? 'Desde ' . pm_public_price(min($prices)) . ($pm_public_preview ? ' · ejemplo' : '') : 'Consultá las opciones' ?></span></div>
        </a></div>
      <?php endforeach; ?>
      </div>
      <div class="pmv-service-links">
        <?php foreach (['depilacion', 'corporales', 'cabello', 'masajes'] as $key): $cat = $pm_public_categories[$key]; ?>
        <a href="<?= URL_BASE ?>/reservar.php?categoria=<?= $key ?>#catalogo"><i class="bi <?= $cat['icono'] ?>" aria-hidden="true"></i><span><?= $cat['nombre'] ?></span><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
        <?php endforeach; ?>
      </div>
      <div class="pmv-services-end"><a class="pmv-text-link" href="<?= URL_BASE ?>/reservar.php">Ver todos los servicios y precios <i class="bi bi-arrow-right" aria-hidden="true"></i></a><div class="pmv-service-advice"><span>¿No sabés por dónde empezar?</span><a class="pmv-text-link" href="<?= pm_public_escape(pm_wa_turno()) ?>" target="_blank" rel="noopener">Te ayudamos a elegir.</a></div></div>
    </div>
  </section>

  <section id="nosotros" class="pmv-section pmv-about">
    <div class="container"><div class="row align-items-center gy-4 gx-lg-5">
      <div class="col-lg-6"><figure class="pmv-space-figure"><img src="<?= URL_BASE ?>/assets/img/local/local-2026-03-30.jpeg" alt="Frente del espacio Piel Morena en Vedia 459, Resistencia" width="785" height="753" loading="lazy" decoding="async"><figcaption>Acá te esperamos · Vedia 459, Resistencia</figcaption></figure></div>
      <div class="col-lg-6 pmv-about-copy"><p class="pmv-eyebrow">Conocé Piel Morena</p><h2>La confianza<br>se siente en el trato.</h2><p class="pmv-lead">Soy Mari. Este espacio nace de algo simple: que te sientas cuidada desde que llegás.</p><p>En Piel Morena reunimos cuidado de la piel y servicios de belleza en un mismo lugar. Queremos escucharte, explicarte las opciones y acompañarte en cada visita.</p><div class="pmv-signature"><strong>Mariangel Zudaire</strong><span>Dermatocosmiatra · Operadora láser</span><span>Matrícula profesional 21997</span></div><?php if ($pm_public_preview): ?><p class="pmv-small pmv-editorial-note">Presentación de ejemplo para que Mari la revise. Retrato y texto definitivo pendientes.</p><?php endif; ?><a class="pmv-text-link" href="<?= pm_public_escape(INSTAGRAM_NEGOCIO) ?>" target="_blank" rel="noopener">Conocé nuestro día a día en Instagram <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></div>
    </div></div>
  </section>

  <section id="equipo" class="pmv-section pmv-team-section">
    <div class="container"><div class="pmv-section-heading"><div><p class="pmv-eyebrow">Quiénes te acompañan</p><h2>Personas detrás del cuidado.</h2></div><p>Cada especialidad tiene su lugar.<br>Tu comodidad, también.</p></div>
      <details class="pmv-disclosure pmv-team-disclosure"><summary><span>Conocé el equipo y sus especialidades</span><i class="bi bi-plus" aria-hidden="true"></i></summary>
        <div class="pmv-team-list"><?php foreach ($pm_public_team as [$name, $role]): ?><div><h3><?= pm_public_escape($name) ?></h3><p><?= pm_public_escape($role) ?></p></div><?php endforeach; ?></div>
        <p class="pmv-small">Presentación del equipo a validar con Mari. Próximamente, sus fotos y una presentación de cada profesional.</p>
      </details>
    </div>
  </section>

  <section id="primera-visita" class="pmv-section pmv-visit">
    <div class="container"><div class="row gx-lg-5 gy-4">
      <div class="col-lg-5"><p class="pmv-eyebrow">Tu primera visita</p><h2>Venís por un cuidado.<br>Empezamos por vos.</h2><p>No hace falta que conozcas el nombre de cada tratamiento. Estamos para orientarte.</p><a class="pmv-button pmv-button-outline" href="<?= pm_public_escape(pm_wa_turno()) ?>" target="_blank" rel="noopener">Quiero asesoramiento <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></div>
      <div class="col-lg-7"><ol class="pmv-steps"><li><h3>Nos contás qué buscás</h3><p>Elegí un servicio o escribinos con tus dudas. El mensaje de WhatsApp ya va preparado.</p></li><li><h3>Conversamos las opciones</h3><p>Consultá quién te atiende, el valor y los cuidados. Acordamos juntas el día y horario.</p></li><li><h3>Te recibimos en el espacio</h3><p>Antes de empezar, conversamos sobre tu cuidado y lo que podés esperar de la sesión.</p></li></ol></div>
    </div></div>
  </section>

  <section id="testimonios" class="pmv-section pmv-experiences">
    <div class="container"><div class="pmv-section-heading"><div><p class="pmv-eyebrow">El lugar de las clientas</p><h2>Sentirte cómoda importa.</h2></div><p>Queremos que cada experiencia<br>hable por sí misma.</p></div>
      <p class="pmv-data-note">Ejemplos de redacción para revisar. No son testimonios de clientas reales.</p>
      <details class="pmv-disclosure pmv-experience-disclosure"><summary><span>Ver los ejemplos de experiencias</span><i class="bi bi-plus" aria-hidden="true"></i></summary><div class="row g-4"><div class="col-md-6"><blockquote><p>“Me explicaron las opciones y pude elegir tranquila, sin sentir que tenía que saber de tratamientos.”</p><footer>Texto de ejemplo · Primera visita</footer></blockquote></div><div class="col-md-6"><blockquote><p>“Tener un momento para mí y sentirme cómoda con quien me atiende hace la diferencia.”</p><footer>Texto de ejemplo · Atención personalizada</footer></blockquote></div></div></details>
      <a class="pmv-text-link" href="<?= pm_public_escape(INSTAGRAM_NEGOCIO) ?>" target="_blank" rel="noopener">Visitá nuestro Instagram <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
    </div>
  </section>

  <section id="preguntas" class="pmv-section pmv-faq-section">
    <div class="container"><div class="row gx-lg-5 gy-4"><div class="col-lg-5"><p class="pmv-eyebrow">Antes de venir</p><h2>Con la información clara,<br>se disfruta más.</h2><p>Estas respuestas son una guía para la nueva web. Confirmá los detalles de tu visita por WhatsApp.</p></div><div class="col-lg-7 pmv-faq"><?php foreach ($pm_public_faq as [$question, $answer]): ?><details><summary><?= pm_public_escape($question) ?><i class="bi bi-plus" aria-hidden="true"></i></summary><p><?= pm_public_escape($answer) ?></p></details><?php endforeach; ?></div></div></div>
  </section>

  <section id="reservar" class="pmv-contact-cta"><div class="container"><div><p class="pmv-eyebrow">Tu próximo momento de cuidado</p><h2>¿Lo conversamos?</h2><p>Podés elegir un servicio o empezar contándonos qué necesitás.</p></div><div><form id="pmPedirTurno" action="<?= URL_BASE ?>/reservar.php" method="get" data-wa-numero="<?= pm_public_escape(pm_wa_numero()) ?>" data-msg-servicio="<?= pm_public_escape(pm_wa_mensaje_turno('{servicio}')) ?>" data-msg-general="<?= pm_public_escape(pm_wa_mensaje_turno()) ?>">
        <label for="pmv-service-select">¿Qué te gustaría consultar?</label>
        <select id="pmv-service-select" name="servicio"><option value="" data-wa-url="<?= pm_public_escape(pm_wa_turno()) ?>">Necesito asesoramiento</option><?php foreach ($pm_public_services as $s): ?><option value="<?= (int) $s['id'] ?>" data-wa-url="<?= pm_public_escape(pm_wa_turno($s['nombre'])) ?>"><?= pm_public_escape($s['nombre']) ?></option><?php endforeach; ?></select>
        <button class="pmv-button pmv-button-light" type="submit"><i class="bi bi-whatsapp" aria-hidden="true"></i>Continuar por WhatsApp</button>
        <noscript><p class="pmv-small">Primero te mostramos el servicio elegido. Desde ahí podés abrir WhatsApp.</p></noscript>
      </form><p class="pmv-small">Tu turno se confirma cuando acordamos día y horario.</p></div></div></section>

  <section id="contacto" class="pmv-section pmv-contact">
    <div class="container"><div class="row gx-lg-5 gy-4"><div class="col-lg-5"><p class="pmv-eyebrow">Cerca tuyo</p><h2>Nos vemos<br>en Piel Morena.</h2><p>Vedia 459<br>Resistencia, Chaco.</p><a class="pmv-text-link" href="https://www.google.com/maps/search/?api=1&amp;query=Vedia+459+Resistencia+Chaco" target="_blank" rel="noopener">Cómo llegar <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></div><div class="col-lg-7"><dl class="pmv-contact-details"><div><dt>Para coordinar tu visita</dt><dd><a href="<?= pm_public_escape(pm_wa_turno()) ?>" target="_blank" rel="noopener">WhatsApp · 3624 254052 <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></dd></div><div><dt>Horarios</dt><dd>Atención con turno previo.<br>Consultá los días y horarios disponibles.<small>Horario general y jornadas pendientes de confirmar.</small></dd></div><div><dt>También nos encontrás en</dt><dd><a href="<?= pm_public_escape(INSTAGRAM_NEGOCIO) ?>" target="_blank" rel="noopener">@pielmorenaesteticaok <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></dd></div></dl></div></div></div>
  </section>
</main>
<?php require __DIR__ . '/includes/public-footer.php'; ?>
