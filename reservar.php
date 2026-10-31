<?php
/**
 * Piel Morena - Pedir turno por WhatsApp
 *
 * La web no reserva: cada servicio abre WhatsApp con su nombre escrito y el
 * turno se coordina con el salón. Funciona sin JavaScript.
 *
 * ?servicio=<id> destaca ese servicio arriba (enlaces de servicios,
 * banners y promociones). Un id inválido muestra la consulta general.
 */
$titulo_pagina = "Pedir turno — Piel Morena Estética";
$meta_descripcion =
    "Pedí tu turno en Piel Morena Estética: elegí el servicio y te abrimos WhatsApp con el mensaje listo para coordinar día y horario.";
$pagina_actual = "reservar";
require_once "includes/header.php";

$db = getDB();
$stmt = $db->query(
    "SELECT s.id, s.nombre, s.duracion_minutos, s.precio,
            c.nombre AS categoria, c.icono AS categoria_icono
     FROM servicios s
     LEFT JOIN categorias_servicios c ON s.id_categoria = c.id
     LEFT JOIN promociones p ON p.id_servicio_generado = s.id
     WHERE s.activo = 1
       AND (
         p.id IS NULL
         OR (
           p.activo = 1
           AND (p.fecha_inicio IS NULL OR p.fecha_inicio <= CURDATE())
           AND (p.fecha_fin IS NULL OR p.fecha_fin >= CURDATE())
         )
       )
     ORDER BY c.orden IS NULL, c.orden, c.nombre, s.nombre"
);
$categorias = [];
$elegido = null;
$id_elegido = intval($_GET["servicio"] ?? 0);
foreach ($stmt->fetchAll() as $s) {
    $categorias[$s["categoria"] ?? "Otros"]["icono"] = $s["categoria_icono"] ?: "bi-stars";
    $categorias[$s["categoria"] ?? "Otros"]["servicios"][] = $s;
    if ((int) $s["id"] === $id_elegido) {
        $elegido = $s;
    }
}

$precio = fn($p) => (float) $p > 0 ? "$" . number_format((float) $p, 0, ",", ".") : "";
?>

<section class="pm-section pm-reservar-section">
  <div class="container" style="max-width: 720px;">

    <div class="text-center mb-4">
      <h1 class="pm-reservar-title">
        <i class="bi bi-whatsapp me-2"></i>Pedí tu turno
      </h1>
      <p class="pm-reservar-subtitle">Elegí el servicio y te abrimos WhatsApp con el mensaje listo. Coordinamos el día y el horario con vos.</p>
      <div class="pm-divider"></div>
    </div>

    <?php if ($elegido): ?>
    <div class="pm-wizard-step pm-pedir-elegido">
      <p class="pm-reserva-etiqueta">Elegiste</p>
      <h2 class="pm-wizard-titulo"><?= htmlspecialchars($elegido["nombre"]) ?></h2>
      <p class="pm-wizard-sub">
        <?= (int) $elegido["duracion_minutos"] ?> min<?= $precio($elegido["precio"]) ? " · " . $precio($elegido["precio"]) : "" ?>
      </p>
      <a href="<?= htmlspecialchars(pm_wa_turno($elegido["nombre"])) ?>" class="btn btn-pm w-100 mt-3 pm-btn-wa" target="_blank" rel="noopener">
        <i class="bi bi-whatsapp me-2"></i>Pedir turno por WhatsApp
      </a>
      <p class="pm-wizard-ayuda text-center mt-2">¿Querés otro servicio? Elegilo abajo.</p>
    </div>
    <?php endif; ?>

    <div class="pm-wizard-step">
      <div class="pm-pedir-general">
        <div>
          <h2 class="pm-wizard-titulo">¿No sabés qué elegir?</h2>
          <p class="pm-wizard-sub">Contanos qué buscás y te asesoramos.</p>
        </div>
        <a href="<?= htmlspecialchars(pm_wa_turno()) ?>" class="btn btn-pm-outline pm-btn-wa" target="_blank" rel="noopener">
          <i class="bi bi-chat-heart me-1"></i>Necesito asesoramiento
        </a>
      </div>
    </div>

    <div class="pm-wizard-step">
      <h2 class="pm-wizard-titulo mb-3"><i class="bi bi-stars me-2"></i>Servicios</h2>
      <?php if (!$categorias): ?>
        <p class="pm-wizard-sub">Escribinos y te contamos qué hacemos.</p>
      <?php endif; ?>
      <?php foreach ($categorias as $nombre_cat => $cat): ?>
      <details class="pm-pedir-cat" <?= $elegido && ($elegido["categoria"] ?? "Otros") === $nombre_cat ? "open" : "" ?>>
        <summary><i class="bi <?= htmlspecialchars($cat["icono"]) ?> me-2" aria-hidden="true"></i><?= htmlspecialchars($nombre_cat) ?></summary>
        <ul class="pm-pedir-lista">
          <?php foreach ($cat["servicios"] as $s): ?>
          <li>
            <a href="<?= htmlspecialchars(pm_wa_turno($s["nombre"])) ?>" class="pm-pedir-item" target="_blank" rel="noopener">
              <span class="pm-pedir-info">
                <span class="pm-so-name"><?= htmlspecialchars($s["nombre"]) ?></span>
                <span class="pm-so-meta"><i class="bi bi-clock me-1" aria-hidden="true"></i><?= (int) $s["duracion_minutos"] ?> min<?= $precio($s["precio"]) ? " · " . $precio($s["precio"]) : "" ?></span>
              </span>
              <span class="pm-pedir-cta"><i class="bi bi-whatsapp" aria-hidden="true"></i><span class="visually-hidden">Pedir turno por WhatsApp</span></span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </details>
      <?php endforeach; ?>
    </div>

    <p class="pm-pedir-aviso text-center">
      <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
      Tu turno queda confirmado cuando acordamos el día y el horario por WhatsApp.
    </p>

  </div>
</section>

<?php require_once "includes/footer.php"; ?>
