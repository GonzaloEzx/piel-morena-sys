<p style="color: #3F352B; font-size: 16px; margin: 0 0 16px;">Hola <strong><?= htmlspecialchars($nombre) ?></strong>,</p>

<p style="color: #3F352B; font-size: 16px; margin: 0 0 24px;">Bienvenida a <strong style="color: #957C62;">Piel Morena</strong>. Tu cuenta fue creada con exito.</p>

<div style="background: #F8F4E8; border-radius: 12px; padding: 20px; margin: 0 0 24px;">
    <p style="color: #3F352B; font-size: 14px; margin: 0 0 12px; font-weight: 600;">Desde tu cuenta podes:</p>
    <ul style="color: #746754; font-size: 14px; margin: 0; padding-left: 20px;">
        <li style="margin-bottom: 8px;">Reservar citas online cuando quieras</li>
        <li style="margin-bottom: 8px;">Ver el historial de tus citas</li>
        <li style="margin-bottom: 8px;">Cancelar o reprogramar facilmente</li>
        <li style="margin-bottom: 0;">Editar tu perfil y datos de contacto</li>
    </ul>
</div>

<div style="text-align: center; margin: 24px 0;">
    <a href="<?= URL_BASE ?>/reservar.php" style="display: inline-block; background: #957C62; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 15px; font-weight: 600;">Reserva tu primera cita</a>
</div>

<p style="color: #746754; font-size: 13px; margin: 16px 0 0;">Gracias por elegirnos. Te esperamos.</p>
