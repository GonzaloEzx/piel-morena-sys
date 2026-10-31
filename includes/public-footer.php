<footer class="pmv-footer">
  <div class="container">
    <div class="pmv-footer-top">
      <a class="pmv-brand" href="<?= URL_BASE ?>/"><img src="<?= URL_BASE ?>/assets/img/logo-mark.png" alt="" width="48" height="48"><span>Piel Morena<small>Estamos para cuidarte.</small></span></a>
      <p>Un espacio para vos.<br>Vedia 459 · Resistencia, Chaco.</p>
      <div class="pmv-footer-links"><a href="<?= pm_public_escape(INSTAGRAM_NEGOCIO) ?>" target="_blank" rel="noopener">Instagram <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a><a href="<?= pm_public_escape(pm_wa_turno()) ?>" target="_blank" rel="noopener">WhatsApp <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a><a href="<?= URL_BASE ?>/login.php">Acceso del equipo</a></div>
    </div>
    <div class="pmv-footer-bottom"><span>© <?= date('Y') ?> Piel Morena Estética.</span><span><?= $pm_public_preview ? 'Versión de prueba · contenido pendiente de validación.' : 'Atención con turno coordinado por WhatsApp.' ?></span><a href="#contenido">Volver arriba ↑</a></div>
  </div>
</footer>
<div class="pmv-mobile-bar"><a class="pmv-button" aria-describedby="pmv-mobile-note" href="<?= pm_public_escape(pm_wa_turno($pm_public_selected_name ?? '')) ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp" aria-hidden="true"></i>Pedir turno por WhatsApp</a><span id="pmv-mobile-note" class="visually-hidden">El día y horario se acuerdan por mensaje.</span></div>
</body>
</html>
