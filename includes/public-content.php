<?php
/** Contenido de la presentación pública. La demo nunca escribe en la base. */
if (!defined('PIEL_MORENA')) {
    require_once __DIR__ . '/init.php';
}

// La publicación de contenido definitivo requiere aprobación explícita del negocio.
$pm_public_preview = !($pm_local_config['public_content_approved'] ?? false);
$pm_public_version = '2.04';

function pm_public_escape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function pm_public_price($value): string
{
    return (float) $value > 0 ? '$' . number_format((float) $value, 0, ',', '.') : 'A consultar';
}

$pm_public_categories = [
    'faciales' => ['nombre' => 'Cuidado facial', 'texto' => 'Un momento para conocer tu piel y darle el cuidado que necesita.', 'imagen' => 'galeria-03.jpg', 'alt' => 'Referencia visual de una sesión de cuidado facial', 'icono' => 'bi-droplet'],
    'depilacion' => ['nombre' => 'Depilación', 'texto' => 'Te orientamos sobre las zonas, las sesiones y los cuidados.', 'imagen' => null, 'alt' => '', 'icono' => 'bi-brightness-high'],
    'corporales' => ['nombre' => 'Cuerpo y bienestar', 'texto' => 'Opciones de cuidado corporal, con una propuesta pensada para vos.', 'imagen' => null, 'alt' => '', 'icono' => 'bi-person-arms-up'],
    'mirada' => ['nombre' => 'Cejas y pestañas', 'texto' => 'Detalles que acompañan tus rasgos y tu forma de verte.', 'imagen' => 'galeria-02.jpg', 'alt' => 'Referencia visual de un trabajo de cejas y pestañas', 'icono' => 'bi-eye'],
    'manos' => ['nombre' => 'Manos cuidadas', 'texto' => 'Elegí el acabado, el color y el estilo que van con vos.', 'imagen' => 'galeria-05.jpg', 'alt' => 'Referencia visual de una manicura con diseño', 'icono' => 'bi-hand-index'],
    'cabello' => ['nombre' => 'Cuidado capilar', 'texto' => 'Tratamientos y terminaciones según las necesidades de tu cabello.', 'imagen' => 'galeria-04.jpg', 'alt' => 'Referencia visual de un trabajo de peluquería', 'icono' => 'bi-scissors'],
    'masajes' => ['nombre' => 'Masajes y pausa', 'texto' => 'Un espacio para aflojar el ritmo y dedicarte tiempo.', 'imagen' => null, 'alt' => '', 'icono' => 'bi-flower1'],
];

// IDs negativos: los ejemplos no se confunden con servicios de la base operativa.
$pm_public_demo = [
    ['faciales', 'Limpieza facial personalizada', 90, 32000, 'Limpieza y cuidado según las necesidades de tu piel. El protocolo se conversa en la consulta.'],
    ['faciales', 'Hidratación facial', 60, 28000, 'Una propuesta de cuidado para acompañar la hidratación y el confort de la piel.'],
    ['faciales', 'Dermaplaning', 60, 30000, 'Consultá si este tratamiento se adapta a tu piel y qué cuidados requiere.'],
    ['depilacion', 'Depilación · axilas', 15, 10000, 'Consultá las próximas jornadas y la evaluación previa para empezar.'],
    ['depilacion', 'Depilación · piernas completas', 30, 18000, 'La frecuencia y preparación se coordinan personalmente con la profesional.'],
    ['depilacion', 'Combo · axilas y cavado', 30, 20000, 'Una propuesta de zonas combinadas. Confirmá el alcance antes de tu visita.'],
    ['corporales', 'Radiofrecuencia corporal', 45, 18000, 'Evaluamos la zona y tu objetivo antes de proponer un plan de sesiones.'],
    ['corporales', 'Cuidado corporal personalizado', 60, 24000, 'Una consulta para conversar sobre tus objetivos y las opciones disponibles.'],
    ['mirada', 'Lifting de pestañas nutritivo', 75, 26000, 'Curvatura de las pestañas naturales. Incluye orientación sobre su cuidado.'],
    ['mirada', 'Diseño y perfilado de cejas', 30, 16000, 'Diseño conversado con vos para acompañar la forma de tu rostro.'],
    ['mirada', 'Laminado de cejas', 60, 26000, 'Para ordenar y dar dirección al pelo de las cejas. Consultá qué incluye.'],
    ['manos', 'Esmaltado semipermanente', 90, 22000, 'Elegí el color y consultá por retiro previo y diseños especiales.'],
    ['manos', 'Kapping', 120, 26000, 'Refuerzo sobre la uña natural, con terminación a elección.'],
    ['manos', 'Soft gel', 120, 30000, 'Extensiones con largo y forma a conversar con la profesional.'],
    ['cabello', 'Lavado y nutrición', 60, 25000, 'Una propuesta de cuidado según el estado y las necesidades del cabello.'],
    ['cabello', 'Corte de puntas', 30, 10000, 'Para mantener el largo y renovar la terminación del cabello.'],
    ['cabello', 'Tratamiento capilar', 90, 35000, 'El valor final puede variar según el largo y el tratamiento indicado.'],
    ['masajes', 'Masaje relajante', 60, 24000, 'Una pausa de bienestar. Contanos tus preferencias antes de la sesión.'],
    ['masajes', 'Masaje de zona específica', 30, 16000, 'Una sesión enfocada en la zona que querés trabajar, previa consulta.'],
];

$pm_public_services = [];
if ($pm_public_preview) {
    foreach ($pm_public_demo as $i => [$categoria, $nombre, $minutos, $precio, $descripcion]) {
        $pm_public_services[] = ['id' => -($i + 1), 'grupo' => $categoria, 'nombre' => $nombre, 'duracion_minutos' => $minutos, 'precio' => $precio, 'descripcion' => $descripcion];
    }
} else {
    // Conserva las reglas del catálogo del panel, incluidos los packs vigentes.
    $rows = getDB()->query("SELECT s.id, s.nombre, s.descripcion, s.precio, s.duracion_minutos,
        c.nombre AS categoria FROM servicios s
        LEFT JOIN categorias_servicios c ON c.id = s.id_categoria
        LEFT JOIN promociones p ON p.id_servicio_generado = s.id
        WHERE s.activo = 1 AND (p.id IS NULL OR (p.activo = 1
        AND (p.fecha_inicio IS NULL OR p.fecha_inicio <= CURDATE())
        AND (p.fecha_fin IS NULL OR p.fecha_fin >= CURDATE())))
        ORDER BY c.orden, s.nombre")->fetchAll();
    foreach ($rows as $row) {
        $categoria = mb_strtolower($row['categoria'] ?? '');
        $grupo = match (true) {
            str_contains($categoria, 'facial') => 'faciales',
            str_contains($categoria, 'depil') => 'depilacion',
            str_contains($categoria, 'corporal') => 'corporales',
            str_contains($categoria, 'ceja'), str_contains($categoria, 'pesta') => 'mirada',
            str_contains($categoria, 'manic') => 'manos',
            str_contains($categoria, 'pelu'), str_contains($categoria, 'capilar') => 'cabello',
            str_contains($categoria, 'masaj') => 'masajes',
            default => 'otros',
        };
        $pm_public_services[] = $row + ['grupo' => $grupo];
    }
    $pm_public_categories['otros'] = ['nombre' => 'Otras propuestas', 'texto' => 'Consultá las opciones disponibles.', 'imagen' => null, 'alt' => '', 'icono' => 'bi-stars'];
}

$pm_public_team = [
    ['Rosario Prieto', 'Manicuría'],
    ['Lucía Soto', 'Cejas, pestañas y cosmetología'],
    ['Nathalia Gómez', 'Peluquería'],
    ['Nuria Morinigo', 'Masajes'],
    ['Naila Centurión', 'Extensiones de pestañas'],
];

$pm_public_faq = [
    ['¿Y si no sé qué tratamiento elegir?', 'Podés escribirnos sin tener un tratamiento decidido. Contanos qué buscás y conversamos sobre las opciones antes de coordinar tu visita.'],
    ['¿Cómo pido un turno?', 'Elegí un servicio o pedí asesoramiento. Se abre WhatsApp con el mensaje preparado; vos lo enviás y coordinamos el día y el horario. La web no reserva automáticamente.'],
    ['¿Qué pasa en la primera visita?', 'La propuesta es empezar con una conversación sobre tus objetivos y los cuidados que necesitás. El protocolo de cada servicio se confirma con Mari.'],
    ['¿Los precios que veo son definitivos?', 'En esta versión de prueba, los servicios, precios y duraciones son ejemplos. Pedí el presupuesto vigente por WhatsApp antes de confirmar tu turno.'],
    ['¿Cómo puedo pagar o cambiar mi turno?', 'Consultá los medios de pago, la seña y las condiciones para cancelar o reprogramar al coordinar. Estas políticas están pendientes de confirmar para la nueva web.'],
];
