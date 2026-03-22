<p style="color: #3F352B; font-size: 16px; margin: 0 0 16px;">Hola <strong><?= htmlspecialchars($cliente_nombre) ?></strong>,</p>

<p style="color: #3F352B; font-size: 16px; margin: 0 0 24px;">Te recordamos que <strong>manana</strong> tenes una cita con nosotras:</p>

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

<p style="color: #746754; font-size: 14px; margin: 0 0 8px;">Si no podes asistir, te pedimos que canceles con anticipacion para que otra persona pueda usar ese horario.</p>

<div style="text-align: center; margin: 24px 0;">
    <a href="<?= URL_BASE ?>/mi-cuenta.php" style="display: inline-block; background: #957C62; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 15px; font-weight: 600;">Ver mis citas</a>
</div>
