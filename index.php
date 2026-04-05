<?php
$pagina_actual = "inicio";
require_once "includes/init.php";

$pm_testimonios_fallback = [
    [
        "nombre" => "Carolina López",
        "rol" => "Clienta frecuente",
        "texto" =>
            "Desde que empecé mis sesiones de depilación láser en Piel Morena, mi vida cambió. El trato es increíble, me siento como en casa cada vez que voy. Los resultados son visibles desde la primera sesión. ¡100% recomendado!",
        "orden" => 1,
    ],
    [
        "nombre" => "Valentina Martínez",
        "rol" => "Clienta de crioterapia",
        "texto" =>
            "Me hice la crioterapia facial y quedé fascinada. Mi piel se ve más joven, más firme y radiante. Las chicas de Piel Morena son profesionales de verdad, te explican todo el proceso y te hacen sentir segura.",
        "orden" => 2,
    ],
    [
        "nombre" => "Florencia Sánchez",
        "rol" => "Novia 2025",
        "texto" =>
            "El maquillaje de mi boda fue perfecto. Sofía entendió exactamente lo que quería y el resultado superó mis expectativas. Aguantó toda la fiesta sin retoques. Piel Morena es sinónimo de calidad y profesionalismo.",
        "orden" => 3,
    ],
    [
        "nombre" => "Gonzalo",
        "rol" => "Cliente de depilación",
        "texto" =>
            "Estoy encantado con la depilación para hombres que me hice en Piel Morena, el resultado fue excelente y el proceso fue muy cómodo. El personal fue muy amable y profesional, me hicieron sentir muy cómodo durante todo el tratamiento.",
        "orden" => 4,
    ],
    [
        "nombre" => "Lucía Fernández",
        "rol" => "Clienta de lifting de pestañas",
        "texto" =>
            "Me encantó cómo quedaron mis pestañas. El resultado fue natural, prolijo y duradero. La atención fue cálida desde el primer momento y me fui feliz con el servicio.",
        "orden" => 5,
    ],
    [
        "nombre" => "María Gómez",
        "rol" => "Clienta de limpieza facial",
        "texto" =>
            "La limpieza facial fue súper completa y delicada. Sentí la piel mucho más luminosa y fresca desde ese mismo día. Se nota el profesionalismo y el cuidado en cada detalle.",
        "orden" => 6,
    ],
];

$pm_testimonial_gradients = [
    "linear-gradient(135deg, #DBCEA5, #8A7650)",
    "linear-gradient(135deg, #FFE1AF, #957C62)",
    "linear-gradient(135deg, #B77466, #DBCEA5)",
    "linear-gradient(135deg, #8E977D, #957C62)",
];

$pm_testimonios = $pm_testimonios_fallback;

try {
    $stmt = getDB()->query(
        "SELECT id, nombre, rol, texto, orden FROM testimonios ORDER BY orden ASC, id ASC",
    );
    $rows = $stmt->fetchAll();

    if (count($rows) === 6) {
        $pm_testimonios = $rows;
    }
} catch (Throwable $e) {
    error_log("Piel Morena Testimonios Error: " . $e->getMessage());
}

require_once "includes/header.php";
?>

<!-- ═══════════════════════════════════════════════════════════════
     1. HERO — Carousel 3 slides, auto-play 7s
     ═══════════════════════════════════════════════════════════════ -->
<section id="hero" class="pm-hero-section" data-section="hero">
  <div id="heroCarousel" class="carousel slide carousel-fade"
       data-bs-ride="carousel"
       data-bs-interval="7000"
       data-bs-touch="true">
    <div class="carousel-inner">

      <!-- Slide 1: IDENTIDAD -->
      <div class="carousel-item active">
        <div class="pm-hero-slide" style="background: linear-gradient(135deg, #DBCEA5 0%, #8A7650 50%, #957C62 100%);">
          <div class="pm-hero-overlay"></div>
          <div class="container position-relative" style="z-index: 2;">
            <div class="pm-hero-content text-center">
              <span class="pm-hero-badge pm-animate"><i class="bi bi-gem me-2"></i>Estética Piel Morena Spa &amp; Beauty</span>
              <h1 class="pm-hero-title pm-animate">belleza,<br>cuidado y bienestar</h1>
              <p class="pm-hero-subtitle pm-animate">Faciales &middot; Depilación &middot; Corporal</p>
              <div class="pm-hero-ctas pm-animate">
                <a href="#reservar" class="btn-pm-dorado btn-pm-lg">
                  <i class="bi bi-calendar-check me-2"></i>Reservar Cita
                </a>
                <a href="#servicios" class="btn-pm-outline btn-pm-lg pm-hero-btn-secondary">
                  Ver Servicios <i class="bi bi-arrow-down ms-2"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 2: DEPILACIÓN -->
      <div class="carousel-item">
        <div class="pm-hero-slide" style="background: linear-gradient(135deg, #FFE1AF 0%, #8A7650 50%, #7A654F 100%);">
          <div class="pm-hero-overlay"></div>
          <div class="container position-relative" style="z-index: 2;">
            <div class="pm-hero-content text-center">
              <span class="pm-hero-badge"><i class="bi bi-stars me-2"></i>Destacado</span>
              <h1 class="pm-hero-title pm-animate">Depilación<br>Definitiva</h1>
              <p class="pm-hero-subtitle pm-animate">Tecnología láser de última generación</p>
              <div class="pm-hero-ctas pm-animate">
                <a href="#reservar" class="btn-pm-dorado btn-pm-lg">
                  <i class="bi bi-calendar-check me-2"></i>Agendar Sesión
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 3: TRATAMIENTOS DE FRÍO -->
      <div class="carousel-item">
        <div class="pm-hero-slide" style="background: linear-gradient(135deg, #8E977D 0%, #B7BEA7 40%, #957C62 100%);">
          <div class="pm-hero-overlay"></div>
          <div class="container position-relative" style="z-index: 2;">
            <div class="pm-hero-content text-center">
              <span class="pm-hero-badge"><i class="bi bi-snow me-2"></i>Personalizado</span>
              <h1 class="pm-hero-title pm-animate">Tratamientos<br>faciales</h1>
              <p class="pm-hero-subtitle pm-animate">Dermatocosmiatra certificada MP 21997</p>
              <div class="pm-hero-ctas pm-animate">
                <a href="#servicios" class="btn-pm-dorado btn-pm-lg">
                  <i class="bi bi-arrow-right me-2"></i>Conocer Más
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Flechas de navegación -->
    <button class="carousel-control-prev pm-hero-nav" type="button"
            data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Anterior">
      <span class="pm-hero-nav-icon" aria-hidden="true">
        <i class="bi bi-chevron-left"></i>
      </span>
    </button>
    <button class="carousel-control-next pm-hero-nav" type="button"
            data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Siguiente">
      <span class="pm-hero-nav-icon" aria-hidden="true">
        <i class="bi bi-chevron-right"></i>
      </span>
    </button>

    <!-- Indicadores circulares -->
    <div class="pm-hero-indicators">
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0"
              class="pm-hero-indicator active" aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"
              class="pm-hero-indicator" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"
              class="pm-hero-indicator" aria-label="Slide 3"></button>
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════
     2. SOBRE NOSOTROS — bg: --pm-marfil
     ═══════════════════════════════════════════════════════════════ -->
<section id="nosotros" class="pm-section" data-section="nosotros">
  <div class="container">

    <!-- Título de sección -->
    <div class="text-center mb-5">
      <h2 class="pm-section-title pm-animate">Sobre Nosotros</h2>
      <div class="pm-divider"></div>
      <p class="pm-section-subtitle pm-animate">Conoce nuestra historia y pasión por la belleza</p>
    </div>

    <div class="row align-items-center g-5">

      <!-- Imagen Sobre Nosotros -->
      <div class="col-lg-6 pm-animate">
        <div class="pm-about-img-wrapper">
          <img src="<?= URL_BASE ?>/assets/img/nosotros/marca-logo.PNG" alt="Logo Piel Morena" class="pm-about-img" />
        </div>
      </div>

      <!-- Texto -->
      <div class="col-lg-6 pm-animate">
        <h3 class="pm-about-heading">
          Tu espacio de belleza y confianza
        </h3>
        <p>
          En <strong>Piel Morena</strong> creemos que la belleza comienza cuando te sentís cómoda con vos misma.
          Creamos un espacio pensado para que puedas relajarte, desconectar de la rutina y dedicarte un momento solo para vos.
        </p>
        <p class="pm-small">
          Brindamos servicios como tratamientos faciales personalizados, tratamientos corporales adaptados, método depilación para mejorar tu piel en todos los aspectos, servicios de belleza como manicuria, cejas, pestañas y peluquería. Siempre acompañados de atención cálida y profesional.        </p>
        <p class="pm-small">
          Nuestro objetivo es que cada visita sea una experiencia de bienestar, confianza y renovación, combinado con seguimiento en cada servicio
        </p>

        <!-- Features con checks -->
        <div class="row g-3">
          <div class="col-sm-6">
            <div class="pm-about-feature">
              <i class="bi bi-check-circle-fill"></i>
              <span>+5 años de experiencia</span>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="pm-about-feature">
              <i class="bi bi-check-circle-fill"></i>
              <span>Profesionales certificadas</span>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="pm-about-feature">
              <i class="bi bi-check-circle-fill"></i>
              <span>Tecnología de punta</span>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="pm-about-feature">
              <i class="bi bi-check-circle-fill"></i>
              <span>Ambiente cálido y acogedor</span>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════
     3. NUESTROS SERVICIOS — bg: --pm-crema
     ═══════════════════════════════════════════════════════════════ -->
<section id="servicios" class="pm-section-alt" data-section="servicios">
  <div class="container">

    <!-- Título de sección -->
    <div class="text-center mb-5">
      <h2 class="pm-section-title pm-animate">Nuestros Servicios</h2>
      <div class="pm-divider"></div>
      <p class="pm-section-subtitle pm-animate">Tratamientos profesionales para realzar tu belleza natural</p>
    </div>

    <!-- Grid dinámico: servicios marcados como destacados desde el admin (máx 6) -->
    <?php
    $db_landing = getDB();
    $stmt_dest = $db_landing->query("SELECT s.id, s.nombre, s.descripcion, s.precio, s.duracion_minutos,
                c.nombre AS categoria, c.icono AS categoria_icono,
                (SELECT COUNT(*) FROM servicios WHERE id_categoria = s.id_categoria AND activo = 1) AS total_servicios
         FROM servicios s
         LEFT JOIN categorias_servicios c ON s.id_categoria = c.id
         WHERE s.destacado = 1 AND s.activo = 1
         ORDER BY s.nombre
         LIMIT 6");
    $servicios_destacados = $stmt_dest->fetchAll();
    $gradients = [
        "linear-gradient(135deg, #FFE1AF 0%, #8A7650 100%)",
        "linear-gradient(135deg, #B77466 0%, #8A7650 100%)",
        "linear-gradient(135deg, #DBCEA5 0%, #957C62 100%)",
        "linear-gradient(135deg, #8E977D 0%, #B7BEA7 100%)",
        "linear-gradient(135deg, #E2B59A 0%, #B77466 100%)",
        "linear-gradient(135deg, #DBCEA5 0%, #FFE1AF 100%)",
    ];
    ?>
    <div class="row g-4">
      <?php
      $ci = 0;
      foreach ($servicios_destacados as $serv):

          $icono = $serv["categoria_icono"] ?: "bi-stars";
          $gradient = $gradients[$ci % count($gradients)];
          ?>
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-service-card">
          <div class="pm-service-img">
            <div class="pm-service-img-placeholder" style="background: <?= $gradient ?>;">
              <i class="bi <?= $icono ?>"></i>
            </div>
            <span class="pm-price-tooltip" data-service-id="<?= $serv[
                "id"
            ] ?>" data-service-name="<?= sanitizar(
    $serv["nombre"],
) ?>" data-price="<?= $serv["precio"] ?>" data-duration="<?= $serv[
    "duracion_minutos"
] ?>" data-category="<?= sanitizar(
    $serv["categoria"] ?? "",
) ?>" title="Consultar precio">
              <i class="bi bi-currency-dollar"></i>
            </span>
            <span class="pm-badge"><?= sanitizar(
                $serv["categoria"] ?? "General",
            ) ?></span>
          </div>
          <div class="pm-service-body">
            <h4><?= sanitizar($serv["nombre"]) ?></h4>
            <p><?= sanitizar(
                mb_strimwidth($serv["descripcion"], 0, 180, "..."),
            ) ?></p>
            <span class="pm-service-duration"><i class="bi bi-clock me-1"></i> <?= $serv[
                "duracion_minutos"
            ] ?> min</span>
            <?php if ($serv["total_servicios"] > 1): ?>
            <span class="pm-service-count"><i class="bi bi-grid me-1"></i>+<?= $serv[
                "total_servicios"
            ] - 1 ?> servicios más</span>
            <?php endif; ?>
          </div>
          <div class="pm-service-footer">
            <a href="<?= URL_BASE ?>/reservar.php" class="btn-pm w-100">Reservar Cita <i class="bi bi-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>
      <?php $ci++;
      endforeach;
      ?>
    </div>

  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════
     4. NUESTRO EQUIPO — bg: --pm-marfil
     ═══════════════════════════════════════════════════════════════ -->
<section id="equipo" class="pm-section" data-section="equipo">
  <div class="container">

    <!-- Título de sección -->
    <div class="text-center mb-5">
      <h2 class="pm-section-title pm-animate">Nuestro Equipo</h2>
      <div class="pm-divider"></div>
      <p class="pm-section-subtitle pm-animate">Profesionales comprometidas con tu bienestar</p>
    </div>

    <!-- Grid: 3 cols desktop, 2 tablet, 1 mobile -->
    <div class="row g-4 justify-content-center">

      <!-- Mariángeles Zudaire -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-team-card">
          <div class="pm-team-photo">
            <div class="pm-team-photo-placeholder" style="background: linear-gradient(135deg, #DBCEA5, #8A7650);">
              <span class="pm-team-initials">MZ</span>
            </div>
          </div>
          <div class="pm-team-info">
            <h4 class="pm-team-name">Mariangel Zudaire</h4>
            <p class="pm-team-role">Dermatocosmiatra &amp; Operadora Láser (MP 21997)</p>
            <div class="pm-team-social">
              <a href="https://www.instagram.com/pielmorenaesteticaok" class="pm-social-icon" aria-label="Instagram de Mariangel" target="_blank" rel="noopener">
                <i class="bi bi-instagram"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Rosario Prieto -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-team-card">
          <div class="pm-team-photo">
            <div class="pm-team-photo-placeholder" style="background: linear-gradient(135deg, #FFE1AF, #957C62);">
              <span class="pm-team-initials">RP</span>
            </div>
          </div>
          <div class="pm-team-info">
            <h4 class="pm-team-name">Rosario Prieto</h4>
            <p class="pm-team-role">Especialista en Manicuría</p>
            <div class="pm-team-social">
              <a href="https://www.instagram.com/rosario.nailstudio" class="pm-social-icon" aria-label="Instagram de Rosario" target="_blank" rel="noopener">
                <i class="bi bi-instagram"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Lucía Soto -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-team-card">
          <div class="pm-team-photo">
            <div class="pm-team-photo-placeholder" style="background: linear-gradient(135deg, #B77466, #8A7650);">
              <span class="pm-team-initials">LS</span>
            </div>
          </div>
          <div class="pm-team-info">
            <h4 class="pm-team-name">Lucía Soto</h4>
            <p class="pm-team-role">Lashista &amp; Cosmetología</p>
            <div class="pm-team-social">
              <a href="https://www.instagram.com/p.beautyy_" class="pm-social-icon" aria-label="Instagram de Lucía" target="_blank" rel="noopener">
                <i class="bi bi-instagram"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Nathalia Gómez -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-team-card">
          <div class="pm-team-photo">
            <div class="pm-team-photo-placeholder" style="background: linear-gradient(135deg, #E2B59A, #DBCEA5);">
              <span class="pm-team-initials">NG</span>
            </div>
          </div>
          <div class="pm-team-info">
            <h4 class="pm-team-name">Nathalia Gómez</h4>
            <p class="pm-team-role">Peluquera</p>
            <div class="pm-team-social">
              <a href="https://www.instagram.com/alisadosytratamientos_ng" class="pm-social-icon" aria-label="Instagram de Nathalia" target="_blank" rel="noopener">
                <i class="bi bi-instagram"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Nuria Morinigo -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-team-card">
          <div class="pm-team-photo">
            <div class="pm-team-photo-placeholder" style="background: linear-gradient(135deg, #8E977D, #6C5D43);">
              <span class="pm-team-initials">NM</span>
            </div>
          </div>
          <div class="pm-team-info">
            <h4 class="pm-team-name">Nuria Morinigo</h4>
            <p class="pm-team-role">Masajista</p>
            <div class="pm-team-social">
              <a href="#" class="pm-social-icon" aria-label="Instagram de Nuria" target="_blank" rel="noopener">
                <i class="bi bi-instagram"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Nayla Centurión -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-team-card">
          <div class="pm-team-photo">
            <div class="pm-team-photo-placeholder" style="background: linear-gradient(135deg, #957C62, #B77466);">
              <span class="pm-team-initials">NC</span>
            </div>
          </div>
          <div class="pm-team-info">
            <h4 class="pm-team-name">Nayla Centurión</h4>
            <p class="pm-team-role">Certificada en extensiones de pestañas</p>
            <div class="pm-team-social">
              <a href="https://www.instagram.com/by_nayla_lashes" class="pm-social-icon" aria-label="Instagram de Naila" target="_blank" rel="noopener">
                <i class="bi bi-instagram"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>


<?php
$tratamientos_catalogo = [
    [
        "slug" => "limpieza-facial-profunda",
        "categoria" => "Facial",
        "icono" => "bi-droplet-half",
        "titulo" => "Limpieza Facial Profunda",
        "microbeneficio" =>
            "Elimina impurezas, puntos negros y células muertas para una piel más limpia, fresca y luminosa.",
        "meta" => ["90 min", "Limpieza profunda", "Luminosidad"],
        "descripcion" =>
            "Tratamiento personalizado orientado a limpiar en profundidad, retirar impurezas visibles y renovar la superficie de la piel con un acabado fresco.",
        "ideal_para" =>
            "Pieles con puntos negros, impurezas acumuladas o falta de luminosidad que necesitan una limpieza completa.",
        "beneficio_principal" =>
            "Purifica la piel y mejora su aspecto general desde la primera sesión.",
        "sensacion" =>
            "La piel se siente fresca, liviana y visiblemente más limpia.",
        "duracion" => "90 minutos aproximados.",
        "recomendacion" =>
            "Ideal como punto de partida para ordenar el cuidado facial o como mantenimiento periódico.",
    ],
    [
        "slug" => "microneedling",
        "categoria" => "Facial",
        "icono" => "bi-magic",
        "titulo" => "Microneedling",
        "microbeneficio" =>
            "Estimula colágeno, mejora textura y acompaña el tratamiento de cicatrices, arrugas y marcas.",
        "meta" => ["60 min", "Colágeno", "Textura"],
        "descripcion" =>
            "Aplicación de microagujas que promueve la regeneración natural de la piel y acompaña objetivos de renovación, firmeza y mejor textura.",
        "ideal_para" =>
            "Pieles con cicatrices, líneas finas, textura irregular o marcas que buscan una renovación más activa.",
        "beneficio_principal" =>
            "Estimula la producción de colágeno y mejora progresivamente el aspecto de la piel.",
        "sensacion" =>
            "Se percibe como un tratamiento intensivo enfocado en renovación y calidad de la piel.",
        "duracion" => "60 minutos aproximados.",
        "recomendacion" =>
            "Conviene realizar una evaluación previa para definir frecuencia y cuidados posteriores.",
    ],
    [
        "slug" => "peeling-quimico",
        "categoria" => "Facial",
        "icono" => "bi-brightness-high",
        "titulo" => "Peeling Químico",
        "microbeneficio" =>
            "Ayuda a reducir manchas, acné, poros dilatados y líneas finas, renovando la piel en profundidad.",
        "meta" => ["60 min", "Renovación", "Manchas"],
        "descripcion" =>
            "Aplicación de ácidos seleccionados de acuerdo con la piel para favorecer el recambio celular y mejorar visiblemente la textura del rostro.",
        "ideal_para" =>
            "Pieles con manchas, poros dilatados, acné o líneas finas que buscan una renovación profunda.",
        "beneficio_principal" =>
            "Renueva la piel y acompaña el tratamiento de imperfecciones visibles.",
        "sensacion" =>
            "La piel atraviesa un proceso de renovación progresiva con un aspecto más uniforme.",
        "duracion" => "60 minutos aproximados.",
        "recomendacion" =>
            "Se personaliza según el tipo de piel y requiere cuidados posteriores indicados por la profesional.",
    ],
    [
        "slug" => "peeling-enzimatico",
        "categoria" => "Facial",
        "icono" => "bi-stars",
        "titulo" => "Peeling Enzimático",
        "microbeneficio" =>
            "Exfolia de forma suave, aporta luminosidad y mejora la textura sin irritar la piel.",
        "meta" => ["60 min", "Suave", "Luminosidad"],
        "descripcion" =>
            "Exfoliación enzimática pensada para renovar la superficie de la piel de manera delicada, respetando su equilibrio natural.",
        "ideal_para" =>
            "Todo tipo de piel, especialmente pieles sensibles o que buscan una renovación suave sin agresión.",
        "beneficio_principal" =>
            "Mejora la textura y aporta luminosidad sin irritar.",
        "sensacion" => "La piel se percibe más suave, fresca y uniforme.",
        "duracion" => "60 minutos aproximados.",
        "recomendacion" =>
            "Muy buena opción cuando se busca una renovación amable o complementar otros cuidados faciales.",
    ],
    [
        "slug" => "tratamiento-anti-edad",
        "categoria" => "Facial",
        "icono" => "bi-heart-pulse",
        "titulo" => "Tratamiento Anti Edad",
        "microbeneficio" =>
            "Mejora firmeza, hidratación y elasticidad, atenuando líneas de expresión y arrugas.",
        "meta" => ["90 min", "Firmeza", "Elasticidad"],
        "descripcion" =>
            "Protocolo facial enfocado en acompañar los signos de envejecimiento con activos y maniobras pensadas para devolver confort y tonicidad.",
        "ideal_para" =>
            "Pieles que buscan trabajar líneas de expresión, arrugas, hidratación y pérdida de firmeza.",
        "beneficio_principal" =>
            "Mejora la elasticidad de la piel y suaviza la apariencia de los signos de la edad.",
        "sensacion" =>
            "El rostro se siente más hidratado, firme y revitalizado.",
        "duracion" => "90 minutos aproximados.",
        "recomendacion" =>
            "Se adapta según el estado de la piel y puede integrarse en un plan antiage sostenido.",
    ],
    [
        "slug" => "tratamiento-acne",
        "categoria" => "Facial",
        "icono" => "bi-shield-check",
        "titulo" => "Tratamiento Acné",
        "microbeneficio" =>
            "Ayuda a controlar brotes, disminuir inflamación y mejorar las marcas causadas por el acné.",
        "meta" => ["90 min", "Control acné", "Calmante"],
        "descripcion" =>
            "Tratamiento facial pensado para pieles con tendencia acneica, enfocado en limpiar, desinflamar y acompañar la recuperación de la piel.",
        "ideal_para" =>
            "Pieles con brotes activos, inflamación, exceso de sebo o marcas post acné.",
        "beneficio_principal" =>
            "Colabora en el control de brotes y en la mejora gradual del aspecto de la piel.",
        "sensacion" =>
            "La piel se percibe más equilibrada, calmada y ordenada.",
        "duracion" => "90 minutos aproximados.",
        "recomendacion" =>
            "Se define según el estado del acné y conviene acompañarlo con indicaciones específicas de cuidado diario.",
    ],
    [
        "slug" => "nanoplastia",
        "categoria" => "Capilar",
        "icono" => "bi-scissors",
        "titulo" => "Nanoplastia",
        "microbeneficio" =>
            "Alisa, hidrata y repara el cabello, dejándolo suave y brillante.",
        "meta" => ["120 min", "Capilar", "Brillo"],
        "descripcion" =>
            "Tratamiento capilar de alisado y reparación que ayuda a controlar el volumen, mejorar la textura y devolver brillo al cabello.",
        "ideal_para" =>
            "Cabellos con frizz, resequedad, volumen o daño que buscan un acabado más suave y manejable.",
        "beneficio_principal" =>
            "Combina efecto de alisado con hidratación y reparación capilar.",
        "sensacion" => "El cabello se siente más suave, suelto y brillante.",
        "duracion" => "120 minutos aproximados.",
        "recomendacion" =>
            "Se evalúa según largo, cantidad de cabello y resultado esperado antes de realizarlo.",
    ],
    [
        "slug" => "lifting-de-pestanas",
        "categoria" => "Mirada",
        "icono" => "bi-eye",
        "titulo" => "Lifting de Pestañas",
        "microbeneficio" =>
            "Realza y curva tus pestañas naturales para una mirada más abierta y definida.",
        "meta" => ["60 min", "Mirada", "Natural"],
        "descripcion" =>
            "Servicio que trabaja sobre las pestañas naturales para darles curvatura, definición y una apariencia más abierta sin extensiones.",
        "ideal_para" =>
            "Quienes quieren destacar su mirada con un resultado natural, prolijo y de bajo mantenimiento.",
        "beneficio_principal" =>
            "Realza las pestañas naturales y abre visualmente la mirada.",
        "sensacion" => "La mirada se ve más definida, despierta y expresiva.",
        "duracion" => "60 minutos aproximados.",
        "recomendacion" =>
            "Muy buena opción para quienes buscan un efecto natural sin depender del arqueador o máscara todos los días.",
    ],
    [
        "slug" => "kapping",
        "categoria" => "Manicuría",
        "icono" => "bi-gem",
        "titulo" => "Kapping",
        "microbeneficio" =>
            "Crea una capa protectora sobre la uña natural que brinda mayor resistencia y duración.",
        "meta" => ["120 min", "Uñas", "Resistencia"],
        "descripcion" =>
            "Servicio de manicuría que recubre la uña natural con una capa protectora para reforzarla sin perder un aspecto prolijo.",
        "ideal_para" =>
            "Uñas naturales frágiles, quebradizas o que necesitan más resistencia para sostener el esmaltado.",
        "beneficio_principal" =>
            "Aporta protección, duración y una base más firme sobre la uña natural.",
        "sensacion" => "Las uñas se ven más prolijas, parejas y resistentes.",
        "duracion" => "120 minutos aproximados.",
        "recomendacion" =>
            "Ideal para quienes quieren fortalecer sus uñas naturales sin recurrir a extensiones.",
    ],
    [
        "slug" => "alisado-sin-formol",
        "categoria" => "Capilar",
        "icono" => "bi-stars",
        "titulo" => "Alisado sin Formol",
        "microbeneficio" =>
            "Reduce el frizz, alisa y aporta brillo al cabello sin dañar la fibra capilar.",
        "meta" => ["2 a 3 h", "Capilar", "Frizz control"],
        "descripcion" =>
            "Tratamiento capilar pensado para alisar y disciplinar el cabello mientras mejora su brillo y manejabilidad sin recurrir al formol.",
        "ideal_para" =>
            "Cabellos con frizz, volumen o dificultad para peinar que buscan un alisado más prolijo y brillante.",
        "beneficio_principal" =>
            "Reduce el frizz y deja el cabello más lacio, suelto y fácil de mantener.",
        "sensacion" =>
            "El cabello luce más ordenado, brillante y suave al tacto.",
        "duracion" => "Entre 2 y 3 horas, según largo y volumen del cabello.",
        "recomendacion" =>
            "Se define de manera personalizada según el tipo de cabello y el resultado buscado.",
    ],
    [
        "slug" => "laminado-de-cejas",
        "categoria" => "Mirada",
        "icono" => "bi-brush",
        "titulo" => "Laminado de Cejas",
        "microbeneficio" =>
            "Ordena, define y fija las cejas para lograr un efecto peinado natural y prolijo.",
        "meta" => ["45 min", "Cejas", "Definición"],
        "descripcion" =>
            "Tratamiento de cejas que alinea y fija el vello para conseguir una forma más definida, pareja y duradera.",
        "ideal_para" =>
            "Cejas rebeldes, con poco orden o que buscan un efecto peinado natural y sostenido.",
        "beneficio_principal" =>
            "Define y organiza las cejas para resaltar mejor la expresión del rostro.",
        "sensacion" => "Las cejas se ven más prolijas, llenas y direccionadas.",
        "duracion" => "45 minutos aproximados.",
        "recomendacion" =>
            "Muy útil cuando se quiere mantener la ceja alineada y con una forma más uniforme.",
    ],
    [
        "slug" => "semipermanente",
        "categoria" => "Manicuría",
        "icono" => "bi-palette",
        "titulo" => "Semipermanente",
        "microbeneficio" =>
            "Ofrece esmaltado de larga duración, brillo intenso y un acabado impecable por semanas.",
        "meta" => ["90 min", "Uñas", "Brillo intenso"],
        "descripcion" =>
            "Servicio de esmaltado semipermanente pensado para mantener las uñas prolijas, brillantes y con buena duración en el tiempo.",
        "ideal_para" =>
            "Quienes buscan uñas arregladas por más tiempo, con brillo sostenido y menor mantenimiento diario.",
        "beneficio_principal" =>
            "Brinda un acabado prolijo y duradero con aspecto impecable durante semanas.",
        "sensacion" =>
            "Las uñas se ven brillantes, parejas y recién hechas por más tiempo.",
        "duracion" => "90 minutos aproximados.",
        "recomendacion" =>
            "Ideal para quienes priorizan practicidad y duración sin resignar terminación prolija.",
    ],
];

$tratamiento_badges = [
    "Facial" => "pm-tratamiento-badge--facial",
    "Mirada" => "pm-tratamiento-badge--mirada",
    "Capilar" => "pm-tratamiento-badge--corporal",
    "Manicuría" => "pm-tratamiento-badge--relax",
];

$tratamiento_cards = [
    "Facial" => "pm-tratamiento-card--facial",
    "Mirada" => "pm-tratamiento-card--mirada",
    "Capilar" => "pm-tratamiento-card--corporal",
    "Manicuría" => "pm-tratamiento-card--relax",
];

$tratamientos_catalogo_json = json_encode(
    $tratamientos_catalogo,
    JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES |
        JSON_HEX_TAG |
        JSON_HEX_AMP |
        JSON_HEX_APOS |
        JSON_HEX_QUOT,
);
?>

<!-- ═══════════════════════════════════════════════════════════════
     4b. CATÁLOGO DE TRATAMIENTOS — bg: --pm-marfil
     Editá contenidos de cards y modal en $tratamientos_catalogo.
     ═══════════════════════════════════════════════════════════════ -->
<section
  id="tratamientos"
  class="pm-section-alt pm-tratamientos-section"
  data-section="tratamientos"
  data-whatsapp-number="<?= preg_replace("/[^0-9]/", "", TELEFONO_NEGOCIO) ?>"
>
  <div class="container">

    <div class="pm-tratamientos-intro text-center">
      <h2 class="pm-section-title pm-animate">Conocé Nuestros Tratamientos</h2>
      <div class="pm-divider pm-animate"></div>
      <p class="pm-section-subtitle pm-animate pm-tratamientos-subtitle">
        Conocé los tratamientos faciales, capilares, de mirada y manicuría disponibles en Piel Morena
      </p>
    </div>

    <div class="row g-3 g-lg-4 pm-tratamientos-grid">
      <?php foreach ($tratamientos_catalogo as $tratamiento): ?>
        <div class="col-12 col-md-6 col-lg-4 pm-animate">
          <article class="pm-tratamiento-card <?= $tratamiento_cards[
              $tratamiento["categoria"]
          ] ?? "" ?>">
            <div class="pm-tratamiento-card__top">
              <span class="pm-tratamiento-badge <?= $tratamiento_badges[
                  $tratamiento["categoria"]
              ] ?? "" ?>">
                <?= htmlspecialchars(
                    $tratamiento["categoria"],
                    ENT_QUOTES,
                    "UTF-8",
                ) ?>
              </span>
              <span class="pm-tratamiento-card__icon" aria-hidden="true">
                <i class="bi <?= htmlspecialchars(
                    $tratamiento["icono"],
                    ENT_QUOTES,
                    "UTF-8",
                ) ?>"></i>
              </span>
            </div>

            <div class="pm-tratamiento-card__body">
              <h3 class="pm-tratamiento-card__title">
                <?= htmlspecialchars(
                    $tratamiento["titulo"],
                    ENT_QUOTES,
                    "UTF-8",
                ) ?>
              </h3>
              <p class="pm-tratamiento-card__lead">
                <?= htmlspecialchars(
                    $tratamiento["microbeneficio"],
                    ENT_QUOTES,
                    "UTF-8",
                ) ?>
              </p>
            </div>

            <div class="pm-tratamiento-card__footer">
              <button
                type="button"
                class="pm-tratamiento-card__link js-pm-tratamiento-modal"
                data-tratamiento-id="<?= htmlspecialchars(
                    $tratamiento["slug"],
                    ENT_QUOTES,
                    "UTF-8",
                ) ?>"
                aria-label="Ver más sobre <?= htmlspecialchars(
                    $tratamiento["titulo"],
                    ENT_QUOTES,
                    "UTF-8",
                ) ?>"
              >
                Ver más
                <i class="bi bi-arrow-up-right"></i>
              </button>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
    </div>

    <script id="pmTratamientosData" type="application/json"><?= $tratamientos_catalogo_json ?></script>
  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════
     5. GALERÍA — bg: --pm-crema
     ═══════════════════════════════════════════════════════════════ -->
<section id="galeria" class="pm-section" data-section="galeria">
  <div class="container">

    <!-- Título de sección -->
    <div class="text-center mb-5">
      <h2 class="pm-section-title pm-animate">Galería</h2>
      <div class="pm-divider"></div>
      <p class="pm-section-subtitle pm-animate">Nuestros trabajos y el ambiente de Piel Morena</p>
    </div>

    <!-- Grid masonry-style: 3 cols -->
    <div class="row g-3">
      <?php
      $gallery_gradients = [
          "linear-gradient(135deg, #DBCEA5, #8A7650)",
          "linear-gradient(135deg, #FFE1AF, #957C62)",
          "linear-gradient(135deg, #B77466, #8A7650)",
          "linear-gradient(135deg, #8E977D, #B7BEA7)",
          "linear-gradient(135deg, #E2B59A, #DBCEA5)",
          "linear-gradient(135deg, #957C62, #7A654F)",
      ];
      $gallery_heights = [280, 340, 300, 320, 260, 340];
      for ($gi = 1; $gi <= 6; $gi++):

          $gimg = sprintf("galeria-%02d.jpg", $gi);
          $gpath = __DIR__ . "/assets/img/gallery/" . $gimg;
          $gexiste = file_exists($gpath);
          $gurl = $gexiste
              ? URL_BASE .
                  "/assets/img/gallery/" .
                  $gimg .
                  "?v=" .
                  filemtime($gpath)
              : "";
          ?>
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-gallery-item">
          <?php if ($gexiste): ?>
            <img src="<?= $gurl ?>" alt="Galería Piel Morena <?= $gi ?>" class="pm-gallery-real-img" loading="lazy" />
          <?php else: ?>
            <div class="pm-gallery-placeholder" style="background: <?= $gallery_gradients[
                $gi - 1
            ] ?>; height: <?= $gallery_heights[$gi - 1] ?>px;">
              <span class="pm-gallery-text">Piel Morena</span>
            </div>
          <?php endif; ?>
          <div class="pm-gallery-overlay">
            <i class="bi bi-zoom-in"></i>
          </div>
        </div>
      </div>
      <?php
      endfor;
      ?>
    </div>

  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════
     6. PROMOCIONES — bg: --pm-marfil
     ═══════════════════════════════════════════════════════════════ -->
<section id="promos" class="pm-section-alt" data-section="promos">
  <div class="container">

    <!-- Título de sección -->
    <div class="text-center mb-5">
      <h2 class="pm-section-title pm-animate">Promociones</h2>
      <div class="pm-divider"></div>
      <p class="pm-section-subtitle pm-animate">Aprovecha nuestras ofertas especiales</p>
    </div>

    <!-- Carousel de promociones (carga dinámica) -->
    <div id="promosCarousel" class="carousel slide pm-animate" data-bs-ride="carousel" data-bs-interval="6000">
      <div class="carousel-inner" id="promosCarouselInner">
        <div class="text-center py-4">
          <div class="spinner-border" style="color: var(--pm-bronce);" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
        </div>
      </div>

      <!-- Flechas de navegación -->
      <button class="carousel-control-prev pm-promo-nav" type="button"
              data-bs-target="#promosCarousel" data-bs-slide="prev" aria-label="Promo anterior">
        <span class="pm-promo-nav-icon" aria-hidden="true">
          <i class="bi bi-chevron-left"></i>
        </span>
      </button>
      <button class="carousel-control-next pm-promo-nav" type="button"
              data-bs-target="#promosCarousel" data-bs-slide="next" aria-label="Promo siguiente">
        <span class="pm-promo-nav-icon" aria-hidden="true">
          <i class="bi bi-chevron-right"></i>
        </span>
      </button>
    </div>

    <script>
    (function() {
      const GRADIENTS = [
        'linear-gradient(135deg, #FFE1AF, #8A7650)',
        'linear-gradient(135deg, #8E977D, #B7BEA7)',
        'linear-gradient(135deg, #B77466, #DBCEA5)',
        'linear-gradient(135deg, #DBCEA5, #957C62)',
        'linear-gradient(135deg, #8A7650, #FFE1AF)'
      ];
      const BASE = '<?= URL_BASE ?>';

      function esc(s) {
        const d = document.createElement('div');
        d.textContent = s ?? '';
        return d.innerHTML;
      }

      function formatPrecio(n) {
        return '$' + parseFloat(n).toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
      }

      async function cargarPromos() {
        const inner = document.getElementById('promosCarouselInner');
        try {
          const r = await fetch(BASE + '/api/promociones/listar.php');
          const json = await r.json();
          if (!json.success || !json.data.length) {
            inner.innerHTML = `<div class="carousel-item active">
              <div class="text-center py-4">
                <p style="color:var(--pm-text-muted)">Muy pronto tendremos ofertas especiales para vos</p>
              </div>
            </div>`;
            return;
          }

          inner.innerHTML = json.data.map((p, i) => {
            const grad = GRADIENTS[i % GRADIENTS.length];
            const reservaUrl = p.id_servicio_generado
              ? `${BASE}/reservar.php?servicio=${p.id_servicio_generado}`
              : `${BASE}/reservar.php`;
            const desc = p.descripcion
              ? esc(p.descripcion)
              : (p.servicios_incluidos ? 'Incluye: ' + esc(p.servicios_incluidos) : '');
            return `
            <div class="carousel-item${i === 0 ? ' active' : ''}">
              <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                  <div class="pm-promo-card">
                    <div class="row g-0 align-items-center">
                      <div class="col-md-5">
                        ${p.imagen
                          ? `<img src="${esc(p.imagen)}" alt="${esc(p.nombre)}" class="pm-promo-img" style="width:100%;height:100%;object-fit:cover;min-height:220px;border-radius:.75rem 0 0 .75rem">`
                          : `<div class="pm-promo-img-placeholder" style="background:${grad}">
                              <span class="pm-promo-badge">PACK</span>
                              <div class="pm-promo-discount">${esc(formatPrecio(p.precio_pack))}</div>
                            </div>`
                        }
                      </div>
                      <div class="col-md-7">
                        <div class="pm-promo-body">
                          <h3 class="pm-promo-title">${esc(p.nombre)}</h3>
                          ${desc ? `<p class="pm-promo-desc">${desc}</p>` : ''}
                          <a href="${reservaUrl}" class="btn-pm-dorado">
                            <i class="bi bi-calendar-check me-2"></i>Reservar Pack
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>`;
          }).join('');

        } catch(e) {
          inner.innerHTML = `<div class="carousel-item active">
            <div class="text-center py-4">
              <p style="color:var(--pm-text-muted)">Muy pronto tendremos ofertas especiales para vos</p>
            </div>
          </div>`;
        }
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', cargarPromos);
      } else {
        cargarPromos();
      }
    })();
    </script>

  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════
     7. TESTIMONIOS — bg: --pm-crema
     ═══════════════════════════════════════════════════════════════ -->
<section id="testimonios" class="pm-section" data-section="testimonios">
  <div class="container">

    <!-- Título de sección -->
    <div class="text-center mb-5">
      <h2 class="pm-section-title pm-animate">Testimonios</h2>
      <div class="pm-divider"></div>
      <p class="pm-section-subtitle pm-animate">Lo que dicen nuestras clientas</p>
    </div>

    <!-- Carousel de testimonios -->
    <div id="testimoniosCarousel" class="carousel slide pm-animate" data-bs-ride="carousel" data-bs-interval="8000">
      <div class="carousel-inner">
        <?php foreach ($pm_testimonios as $i => $testimonio):

            $gradient =
                $pm_testimonial_gradients[
                    $i % count($pm_testimonial_gradients)
                ];
            $nombre_testimonio = sanitizar($testimonio["nombre"] ?? "");
            $rol_testimonio = sanitizar($testimonio["rol"] ?? "");
            $texto_testimonio = sanitizar($testimonio["texto"] ?? "");
            $iniciales_testimonio = sanitizar(
                obtener_iniciales($testimonio["nombre"] ?? ""),
            );
            ?>
        <div class="carousel-item <?= $i === 0 ? "active" : "" ?>">
          <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
              <div class="pm-testimonial-card text-center">
                <div class="pm-testimonial-quote">
                  <i class="bi bi-quote pm-testimonial-quote-icon"></i>
                </div>
                <p class="pm-testimonial-text">"<?= $texto_testimonio ?>"</p>
                <div class="pm-testimonial-stars">
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                </div>
                <div class="pm-testimonial-author">
                  <div class="pm-testimonial-avatar" style="background: <?= $gradient ?>;">
                    <span><?= $iniciales_testimonio ?></span>
                  </div>
                  <div class="pm-testimonial-info">
                    <strong class="pm-testimonial-name"><?= $nombre_testimonio ?></strong>
                    <span class="pm-testimonial-role"><?= $rol_testimonio !== ""
                        ? $rol_testimonio
                        : "Clienta de Piel Morena" ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php
        endforeach; ?>

      </div>

      <!-- Indicadores -->
      <?php if (count($pm_testimonios) > 1): ?>
      <div class="pm-testimonial-indicators">
        <?php foreach ($pm_testimonios as $i => $testimonio): ?>
        <button type="button" data-bs-target="#testimoniosCarousel" data-bs-slide-to="<?= $i ?>"
                class="pm-testimonial-indicator <?= $i === 0 ? "active" : "" ?>"
                <?= $i === 0 ? 'aria-current="true"' : "" ?>
                aria-label="Testimonio <?= $i + 1 ?>"></button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════
     8. RESERVA TU CITA — bg: gradiente hero
     ═══════════════════════════════════════════════════════════════ -->
<section id="reservar" class="pm-section-alt pm-cta-section" data-section="reservar">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <h2 class="pm-cta-title pm-animate">
          ¿Lista para transformar tu belleza?
        </h2>
        <p class="pm-cta-subtitle pm-animate">
          Agenda tu cita en menos de 1 minuto. Nuestro equipo de profesionales te espera
          para brindarte la mejor experiencia de cuidado personal.
        </p>
        <div class="pm-animate pm-cta-actions">
          <a href="reservar.php" class="btn-pm-dorado btn-pm-lg">
            <i class="bi bi-calendar-check me-2"></i>Reservar Ahora
          </a>
          <a href="https://wa.me/<?= preg_replace(
              "/[^0-9]/",
              "",
              TELEFONO_NEGOCIO,
          ) ?>" class="btn-pm-outline btn-pm-outline--white btn-pm-lg" target="_blank" rel="noopener">
            <i class="bi bi-whatsapp me-2"></i>Contáctanos por WhatsApp
          </a>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════
     9. CONTACTO — bg: --pm-marfil
     ═══════════════════════════════════════════════════════════════ -->
<section id="contacto" class="pm-section" data-section="contacto">
  <div class="container">

    <!-- Título de sección -->
    <div class="text-center mb-5">
      <h2 class="pm-section-title pm-animate">Contacto</h2>
      <div class="pm-divider"></div>
      <p class="pm-section-subtitle pm-animate">Estamos para atenderte, escríbenos o visítanos</p>
    </div>

    <div class="row g-5">

      <!-- Columna izquierda: Formulario -->
      <div class="col-lg-6 pm-animate">
        <form id="contactForm" class="pm-contact-form" novalidate>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="pm-label" for="contactNombre">Nombre completo</label>
              <input type="text" id="contactNombre" name="nombre" class="input-pm w-100" placeholder="Tu nombre" required>
            </div>
            <div class="col-md-6">
              <label class="pm-label" for="contactEmail">Correo electrónico</label>
              <input type="email" id="contactEmail" name="email" class="input-pm w-100" placeholder="tu@email.com" required>
            </div>
            <div class="col-12">
              <label class="pm-label" for="contactTelefono">Teléfono</label>
              <input type="tel" id="contactTelefono" name="telefono" class="input-pm w-100" placeholder="+52 (xxx) xxx-xxxx">
            </div>
            <div class="col-12">
              <label class="pm-label" for="contactMensaje">Mensaje</label>
              <textarea id="contactMensaje" name="mensaje" class="input-pm w-100" rows="5"
                        placeholder="Cuéntanos en qué podemos ayudarte..." required></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn-pm btn-pm-lg">
                <i class="bi bi-send me-2"></i>Enviar Mensaje
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Columna derecha: Info + Mapa -->
      <div class="col-lg-6 pm-animate">
        <div class="pm-contact-info-card">

          <!-- Dirección -->
          <div class="pm-contact-item">
            <div class="pm-contact-icon">
              <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div>
              <strong>Dirección</strong>
              <p><?= DIRECCION_NEGOCIO ?></p>
            </div>
          </div>

          <!-- Teléfono -->
          <div class="pm-contact-item">
            <div class="pm-contact-icon">
              <i class="bi bi-telephone-fill"></i>
            </div>
            <div>
              <strong>Teléfono</strong>
              <p><a href="tel:+54<?= preg_replace(
                  "/[^0-9]/",
                  "",
                  TELEFONO_NEGOCIO,
              ) ?>"><?= TELEFONO_NEGOCIO ?></a></p>
            </div>
          </div>

          <!-- Email -->
          <div class="pm-contact-item">
            <div class="pm-contact-icon">
              <i class="bi bi-envelope-fill"></i>
            </div>
            <div>
              <strong>Correo</strong>
              <p><a href="mailto:<?= EMAIL_NEGOCIO ?>"><?= EMAIL_NEGOCIO ?></a></p>
            </div>
          </div>

          <!-- Horarios -->
          <div class="pm-contact-item">
            <div class="pm-contact-icon">
              <i class="bi bi-clock-fill"></i>
            </div>
            <div>
              <strong>Horarios</strong>
              <p>Lun - Vie: 8:00 - 20:00</p>
              <p>Sáb - Dom: A confirmar</p>
            </div>
          </div>

          <!-- Redes sociales -->
          <div class="pm-contact-social">
            <a href="<?= defined("INSTAGRAM_NEGOCIO")
                ? INSTAGRAM_NEGOCIO
                : "#" ?>" class="pm-social-icon" aria-label="Instagram" target="_blank" rel="noopener">
              <i class="bi bi-instagram"></i>
            </a>
            <a href="https://wa.me/<?= preg_replace(
                "/[^0-9]/",
                "",
                TELEFONO_NEGOCIO,
            ) ?>" class="pm-social-icon" aria-label="WhatsApp" target="_blank" rel="noopener">
              <i class="bi bi-whatsapp"></i>
            </a>
          </div>

        </div>

        <!-- Mapa Google -->
        <div class="pm-map-wrapper mt-4">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4356.493697414219!2d-58.99473882367033!3d-27.45320591600523!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94450c8b78c54733%3A0xe6a7c6d335b00999!2sVedia%20459%2C%20H3500BMJ%20Resistencia%2C%20Chaco!5e1!3m2!1ses-419!2sar!4v1774923101741!5m2!1ses-419!2sar"
                  width="100%" height="280" style="border:0; border-radius: 16px;"
                  allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>

    </div>

  </div>
</section>

</main>

<!-- Estilos movidos a assets/css/style.css — JS movido a assets/js/main.js -->

<?php require_once "includes/footer.php"; ?>
