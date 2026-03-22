<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Inter, Arial, sans-serif; background: #F8F4E8; padding: 40px 20px; margin: 0;">
    <div style="max-width: 560px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 40px; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px solid #ECE7D1;">
            <h1 style="font-family: 'Playfair Display', Georgia, serif; color: #957C62; font-size: 28px; margin: 0; letter-spacing: 1px;">Piel Morena</h1>
            <p style="color: #A69882; font-size: 13px; margin: 6px 0 0;">Estetica &amp; Belleza</p>
        </div>

        <!-- Contenido del template especifico -->
        <?= $contenido ?>

        <!-- Footer -->
        <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid #ECE7D1; text-align: center;">
            <p style="color: #A69882; font-size: 12px; margin: 0 0 8px;"><?= NOMBRE_NEGOCIO ?> — Tu belleza, nuestra pasion</p>
            <p style="color: #B5A892; font-size: 11px; margin: 0;"><?= TELEFONO_NEGOCIO ?> | <?= EMAIL_NEGOCIO ?></p>
            <p style="color: #B5A892; font-size: 11px; margin: 4px 0 0;"><?= DIRECCION_NEGOCIO ?></p>
        </div>
    </div>

    <div style="text-align: center; margin-top: 16px;">
        <p style="color: #B5A892; font-size: 11px;">Este email fue enviado automaticamente. No respondas a este mensaje.</p>
    </div>
</body>
</html>
