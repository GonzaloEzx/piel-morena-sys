<p style="color: #3F352B; font-size: 16px; margin: 0 0 16px;">Hola <strong><?= htmlspecialchars($cliente_nombre) ?></strong>,</p>

<p style="color: #3F352B; font-size: 16px; margin: 0 0 24px;">Tu cita fue <strong style="color: #4a8c5c;">confirmada</strong> por el salon. Te esperamos:</p>

<div style="background: #F8F4E8; border-radius: 12px; padding: 20px; margin: 0 0 24px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; color: #746754; font-size: 14px; width: 110px;">Servicio:</td>
            <td style="padding: 8px 0; color: #3F352B; font-size: 14px; font-weight: 600;"><?= htmlspecialchars($servicio) ?></td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #746754; font-size: 14px;">Fecha:</td>
            <td style="padding: 8px 0; color: #3F352B; font-size: 14px; font-weight: 600;"><?= htmlspecialchars($fecha) ?></td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #746754; font-size: 14px;">Hora:</td>
            <td style="padding: 8px 0; color: #3F352B; font-size: 14px; font-weight: 600;"><?= htmlspecialchars($hora) ?></td>
        </tr>
    </table>
</div>

<div style="text-align: center; margin: 24px 0;">
    <a href="<?= URL_BASE ?>/mi-cuenta.php" style="display: inline-block; background: #957C62; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 15px; font-weight: 600;">Ver mis citas</a>
</div>

<p style="color: #746754; font-size: 13px; margin: 16px 0 0;">Si tenes alguna consulta, no dudes en contactarnos.</p>
