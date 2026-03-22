<p style="color: #3F352B; font-size: 16px; margin: 0 0 16px;">Hola <strong><?= htmlspecialchars($cliente_nombre) ?></strong>,</p>

<?php if (($cancelado_por ?? 'cliente') === 'salon'): ?>
<p style="color: #3F352B; font-size: 16px; margin: 0 0 24px;">Lamentablemente debimos <strong style="color: #c0392b;">cancelar</strong> tu cita. Disculpa las molestias.</p>
<?php else: ?>
<p style="color: #3F352B; font-size: 16px; margin: 0 0 24px;">Tu cita fue <strong style="color: #c0392b;">cancelada</strong> correctamente.</p>
<?php endif; ?>

<div style="background: #F8F4E8; border-radius: 12px; padding: 20px; margin: 0 0 24px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; color: #746754; font-size: 14px; width: 110px;">Servicio:</td>
            <td style="padding: 8px 0; color: #3F352B; font-size: 14px; text-decoration: line-through;"><?= htmlspecialchars($servicio) ?></td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #746754; font-size: 14px;">Fecha:</td>
            <td style="padding: 8px 0; color: #3F352B; font-size: 14px; text-decoration: line-through;"><?= htmlspecialchars($fecha) ?></td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #746754; font-size: 14px;">Hora:</td>
            <td style="padding: 8px 0; color: #3F352B; font-size: 14px; text-decoration: line-through;"><?= htmlspecialchars($hora) ?></td>
        </tr>
    </table>
</div>

<div style="text-align: center; margin: 24px 0;">
    <a href="<?= URL_BASE ?>/reservar.php" style="display: inline-block; background: #957C62; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 15px; font-weight: 600;">Reservar nueva cita</a>
</div>

<p style="color: #746754; font-size: 13px; margin: 16px 0 0;">Podes reservar una nueva cita cuando quieras. Te esperamos.</p>
