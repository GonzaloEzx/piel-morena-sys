<?php $pagina_actual = "inicio"; ?>
<?php require_once "includes/header.php"; ?>

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
                mb_strimwidth($serv["descripcion"], 0, 120, "..."),
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
        "slug" => "limpieza-facial-inicial",
        "categoria" => "Facial",
        "icono" => "bi-droplet-half",
        "titulo" => "Limpieza Facial Inicial",
        "microbeneficio" => "Purifica, equilibra y devuelve frescura visible desde la primera sesión.",
        "meta" => ["60 min", "Purificante", "Suave"],
        "descripcion" => "Tratamiento de entrada pensado para limpiar en profundidad, retirar impurezas acumuladas y devolver una sensación de orden y frescura en la piel sin perder confort.",
        "ideal_para" => "Pieles que necesitan reiniciar la rutina, mejorar limpieza visible o prepararse para protocolos posteriores.",
        "beneficio_principal" => "Mejora la textura inmediata y deja la piel lista para absorber mejor activos complementarios.",
        "sensacion" => "Se percibe liviana, prolija y con una terminación fresca.",
        "duracion" => "60 minutos aproximados.",
        "recomendacion" => "Suele funcionar muy bien como primera visita o como mantenimiento periódico del cuidado facial.",
    ],
    [
        "slug" => "glow-hidratante",
        "categoria" => "Facial",
        "icono" => "bi-stars",
        "titulo" => "Glow Hidratante",
        "microbeneficio" => "Aporta confort, elasticidad y un acabado luminoso de aspecto descansado.",
        "meta" => ["60 min", "Luminosidad", "Confort"],
        "descripcion" => "Propuesta facial enfocada en nutrir, devolver suavidad y recuperar el brillo natural cuando la piel se percibe apagada, tirante o con signos de cansancio.",
        "ideal_para" => "Pieles deshidratadas, opacas o que buscan una apariencia más fresca antes de un evento o cambio de estación.",
        "beneficio_principal" => "Reequilibra hidratación y mejora la percepción de luminosidad en el rostro.",
        "sensacion" => "La piel se siente acolchada, cómoda y con un brillo suave.",
        "duracion" => "60 minutos aproximados.",
        "recomendacion" => "Puede incorporarse como sesión puntual de glow o como apoyo entre tratamientos más intensivos.",
    ],
    [
        "slug" => "peeling-renovador",
        "categoria" => "Facial",
        "icono" => "bi-brightness-high",
        "titulo" => "Peeling Renovador",
        "microbeneficio" => "Favorece una textura más uniforme y una piel visualmente más renovada.",
        "meta" => ["50 min", "Textura", "Progresivo"],
        "descripcion" => "Tratamiento orientado a estimular recambio superficial controlado para acompañar objetivos de mayor uniformidad, mejor tacto y un aspecto más despierto.",
        "ideal_para" => "Pieles opacas, con textura irregular o que buscan una renovación gradual y acompañada profesionalmente.",
        "beneficio_principal" => "Ayuda a suavizar la superficie y a mejorar la apariencia general del rostro.",
        "sensacion" => "Se percibe renovación limpia, ligera y un rostro más parejo.",
        "duracion" => "50 minutos aproximados.",
        "recomendacion" => "Conviene evaluarlo dentro de una secuencia guiada para sostener resultados sin sobreestimular la piel.",
    ],
    [
        "slug" => "dermapen-revitalizante",
        "categoria" => "Facial",
        "icono" => "bi-magic",
        "titulo" => "Dermapen Revitalizante",
        "microbeneficio" => "Estimula revitalización visible para una piel con más energía y mejor tono.",
        "meta" => ["75 min", "Revitaliza", "Activos"],
        "descripcion" => "Técnica pensada para acompañar protocolos de renovación y potenciar activos seleccionados según el objetivo estético de cada piel.",
        "ideal_para" => "Quienes desean trabajar tonicidad, uniformidad y aspecto general con una estrategia más activa.",
        "beneficio_principal" => "Impulsa una apariencia más firme, afinada y revitalizada.",
        "sensacion" => "Deja una percepción de tratamiento profesional y recuperación progresiva del glow.",
        "duracion" => "75 minutos aproximados.",
        "recomendacion" => "Se sugiere programarlo con evaluación previa para ajustar intensidad, frecuencia y cuidados complementarios.",
    ],
    [
        "slug" => "radiofrecuencia-tensora",
        "categoria" => "Corporal",
        "icono" => "bi-sunrise",
        "titulo" => "Radiofrecuencia Tensora",
        "microbeneficio" => "Acompaña objetivos de firmeza con una experiencia cálida y confortable.",
        "meta" => ["60 min", "Firmeza", "Cálido"],
        "descripcion" => "Tratamiento que trabaja con sensación térmica agradable para apoyar protocolos de tonificación y bienestar en zonas que buscan verse más tensas y prolijas.",
        "ideal_para" => "Personas que priorizan firmeza visible y una experiencia corporal agradable, sin sensación invasiva.",
        "beneficio_principal" => "Contribuye a la percepción de tonicidad y mejora de contorno en áreas tratadas.",
        "sensacion" => "Cálida, relajante y de trabajo profundo pero amable.",
        "duracion" => "60 minutos aproximados.",
        "recomendacion" => "Suele dar mejores resultados cuando se integra a un plan corporal sostenido y personalizado.",
    ],
    [
        "slug" => "crioterapia-localizada",
        "categoria" => "Corporal",
        "icono" => "bi-snow",
        "titulo" => "Crioterapia Localizada",
        "microbeneficio" => "Brinda una experiencia localizada de alivio, definición y frescura corporal.",
        "meta" => ["45 min", "Frío", "Localizado"],
        "descripcion" => "Propuesta orientada a trabajar zonas puntuales mediante frío controlado, dentro de protocolos corporales que priorizan confort y definición.",
        "ideal_para" => "Quienes buscan una sensación de frescura activa y complementar rutinas corporales focalizadas.",
        "beneficio_principal" => "Aporta una percepción de trabajo localizado y acompaña objetivos de recuperación y definición.",
        "sensacion" => "Fresca, dinámica y tonificante.",
        "duracion" => "45 minutos aproximados.",
        "recomendacion" => "Conviene evaluarla según zona, sensibilidad personal y combinación con otras herramientas corporales.",
    ],
    [
        "slug" => "drenaje-facial-manual",
        "categoria" => "Facial",
        "icono" => "bi-water",
        "titulo" => "Drenaje Facial Manual",
        "microbeneficio" => "Descongestiona suavemente y aporta liviandad al contorno del rostro.",
        "meta" => ["45 min", "Liviano", "Manual"],
        "descripcion" => "Sesión manual, lenta y precisa, pensada para acompañar sensación de desinflamación, descanso visual y armonía facial.",
        "ideal_para" => "Rostros cansados, con sensación de pesadez o para quienes valoran maniobras delicadas y relajantes.",
        "beneficio_principal" => "Ayuda a que el rostro se vea más descansado y liviano.",
        "sensacion" => "Serena, liviana y muy descansante.",
        "duracion" => "45 minutos aproximados.",
        "recomendacion" => "Resulta ideal como sesión puntual de bienestar o como complemento de otros cuidados faciales.",
    ],
    [
        "slug" => "masaje-relajante-integral",
        "categoria" => "Relax",
        "icono" => "bi-flower1",
        "titulo" => "Masaje Relajante Integral",
        "microbeneficio" => "Invita a bajar el ritmo y recuperar una sensación profunda de pausa.",
        "meta" => ["60 min", "Pausa", "Envolvente"],
        "descripcion" => "Experiencia corporal pensada para desconectar del movimiento diario, aliviar sobrecarga y crear un momento de bienestar completo.",
        "ideal_para" => "Personas con jornadas exigentes, estrés acumulado o ganas de regalarse un espacio de calma real.",
        "beneficio_principal" => "Favorece relajación global y sensación de descanso corporal y mental.",
        "sensacion" => "Envolvente, tranquila y restauradora.",
        "duracion" => "60 minutos aproximados.",
        "recomendacion" => "Funciona muy bien como pausa periódica para sostener bienestar general.",
    ],
    [
        "slug" => "masaje-descontracturante",
        "categoria" => "Relax",
        "icono" => "bi-hand-index-thumb",
        "titulo" => "Masaje Descontracturante",
        "microbeneficio" => "Trabaja zonas cargadas para devolver movilidad y alivio corporal.",
        "meta" => ["60 min", "Alivio", "Profundo"],
        "descripcion" => "Tratamiento corporal focalizado en tensiones frecuentes de espalda, cuello y otras zonas con sobrecarga muscular.",
        "ideal_para" => "Quienes pasan muchas horas sentados, entrenan con frecuencia o acumulan tensión física sostenida.",
        "beneficio_principal" => "Ayuda a liberar rigidez y mejorar la sensación de soltura.",
        "sensacion" => "Profunda, liberadora y de alivio progresivo.",
        "duracion" => "60 minutos aproximados.",
        "recomendacion" => "Conviene adaptarlo a intensidad y zonas prioritarias según cada persona.",
    ],
    [
        "slug" => "perfilado-de-cejas",
        "categoria" => "Mirada",
        "icono" => "bi-brush",
        "titulo" => "Perfilado de Cejas",
        "microbeneficio" => "Ordena la expresión y aporta equilibrio visual al rostro.",
        "meta" => ["30 min", "Diseño", "Natural"],
        "descripcion" => "Servicio de mirada enfocado en definir forma, mantener prolijidad y acompañar la expresión facial desde un acabado natural.",
        "ideal_para" => "Quienes desean una ceja mejor definida sin perder armonía con sus rasgos.",
        "beneficio_principal" => "Realza el marco del rostro con una terminación sutil y cuidada.",
        "sensacion" => "Prolija, fresca y de rostro más ordenado.",
        "duracion" => "30 minutos aproximados.",
        "recomendacion" => "Ideal como mantenimiento regular o antes de eventos donde se busca un look pulido.",
    ],
    [
        "slug" => "pestanas-efecto-natural",
        "categoria" => "Mirada",
        "icono" => "bi-eye",
        "titulo" => "Pestañas Efecto Natural",
        "microbeneficio" => "Acentúa la mirada con definición liviana y elegante para todos los días.",
        "meta" => ["50 min", "Mirada", "Sutil"],
        "descripcion" => "Propuesta pensada para destacar la mirada con un resultado cómodo, flexible y compatible con una estética natural.",
        "ideal_para" => "Personas que quieren verse más arregladas sin sentir un efecto recargado.",
        "beneficio_principal" => "Abre visualmente la mirada y aporta definición cotidiana.",
        "sensacion" => "Liviana, femenina y fácil de sostener.",
        "duracion" => "50 minutos aproximados.",
        "recomendacion" => "Conviene para quienes buscan realce sutil y una rutina práctica de maquillaje.",
    ],
    [
        "slug" => "diagnostico-estetico-personalizado",
        "categoria" => "Diagnóstico",
        "icono" => "bi-clipboard2-pulse",
        "titulo" => "Diagnóstico Estético Personalizado",
        "microbeneficio" => "Ordena objetivos y prioridades para elegir un plan de cuidado con criterio.",
        "meta" => ["40 min", "Planifica", "Personal"],
        "descripcion" => "Instancia de evaluación inicial para entender necesidades, prioridades y combinaciones posibles antes de avanzar con tratamientos específicos.",
        "ideal_para" => "Quienes quieren empezar con claridad, evitar improvisaciones y recibir orientación profesional.",
        "beneficio_principal" => "Ayuda a construir una estrategia realista y alineada con cada objetivo.",
        "sensacion" => "Acompañada, clara y con una hoja de ruta concreta.",
        "duracion" => "40 minutos aproximados.",
        "recomendacion" => "Es la mejor puerta de entrada cuando todavía no está definido qué protocolo elegir.",
    ],
];

$tratamiento_badges = [
    "Facial" => "pm-tratamiento-badge--facial",
    "Corporal" => "pm-tratamiento-badge--corporal",
    "Relax" => "pm-tratamiento-badge--relax",
    "Diagnóstico" => "pm-tratamiento-badge--diagnostico",
    "Mirada" => "pm-tratamiento-badge--mirada",
];

$tratamiento_cards = [
    "Facial" => "pm-tratamiento-card--facial",
    "Corporal" => "pm-tratamiento-card--corporal",
    "Relax" => "pm-tratamiento-card--relax",
    "Diagnóstico" => "pm-tratamiento-card--diagnostico",
    "Mirada" => "pm-tratamiento-card--mirada",
];

$tratamientos_catalogo_json = json_encode(
    $tratamientos_catalogo,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
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
      <p class="pm-tratamientos-kicker pm-animate">Curaduría de bienestar y estética</p>
      <h2 class="pm-section-title pm-animate">Conocé Nuestros Tratamientos</h2>
      <div class="pm-divider pm-animate"></div>
      <p class="pm-section-subtitle pm-animate pm-tratamientos-subtitle">
        Una selección informativa de propuestas pensadas para cuidado facial, corporal y bienestar integral
      </p>
    </div>

    <div class="row g-3 g-lg-4 pm-tratamientos-grid">
      <?php foreach ($tratamientos_catalogo as $tratamiento): ?>
        <div class="col-12 col-md-6 col-lg-4 pm-animate">
          <article class="pm-tratamiento-card <?= $tratamiento_cards[$tratamiento["categoria"]] ?? "" ?>">
            <div class="pm-tratamiento-card__top">
              <span class="pm-tratamiento-badge <?= $tratamiento_badges[$tratamiento["categoria"]] ?? "" ?>">
                <?= htmlspecialchars($tratamiento["categoria"], ENT_QUOTES, "UTF-8") ?>
              </span>
              <span class="pm-tratamiento-card__icon" aria-hidden="true">
                <i class="bi <?= htmlspecialchars($tratamiento["icono"], ENT_QUOTES, "UTF-8") ?>"></i>
              </span>
            </div>

            <div class="pm-tratamiento-card__body">
              <h3 class="pm-tratamiento-card__title">
                <?= htmlspecialchars($tratamiento["titulo"], ENT_QUOTES, "UTF-8") ?>
              </h3>
              <p class="pm-tratamiento-card__lead">
                <?= htmlspecialchars($tratamiento["microbeneficio"], ENT_QUOTES, "UTF-8") ?>
              </p>
            </div>

            <div class="pm-tratamiento-card__footer">
              <div
                class="pm-tratamiento-card__meta"
                aria-label="Datos rápidos de <?= htmlspecialchars($tratamiento["titulo"], ENT_QUOTES, "UTF-8") ?>"
              >
                <?php foreach ($tratamiento["meta"] as $meta): ?>
                  <span class="pm-tratamiento-card__meta-item">
                    <?= htmlspecialchars($meta, ENT_QUOTES, "UTF-8") ?>
                  </span>
                <?php endforeach; ?>
              </div>

              <button
                type="button"
                class="pm-tratamiento-card__link js-pm-tratamiento-modal"
                data-tratamiento-id="<?= htmlspecialchars($tratamiento["slug"], ENT_QUOTES, "UTF-8") ?>"
                aria-label="Ver más sobre <?= htmlspecialchars($tratamiento["titulo"], ENT_QUOTES, "UTF-8") ?>"
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

    <!-- Carousel de promociones -->
    <div id="promosCarousel" class="carousel slide pm-animate" data-bs-ride="carousel" data-bs-interval="6000">
      <div class="carousel-inner">

        <!-- Promo 1 -->
        <div class="carousel-item active">
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="pm-promo-card">
                <div class="row g-0 align-items-center">
                  <div class="col-md-5">
                    <div class="pm-promo-img-placeholder" style="background: linear-gradient(135deg, #FFE1AF, #8A7650);">
                      <span class="pm-promo-badge">OFERTA</span>
                      <div class="pm-promo-discount">-30%</div>
                    </div>
                  </div>
                  <div class="col-md-7">
                    <div class="pm-promo-body">
                      <h3 class="pm-promo-title">Pack Depilación Completa</h3>
                      <p class="pm-promo-desc">
                        Axilas + bikini + piernas completas. Incluye 6 sesiones de depilación láser
                        con la tecnología más avanzada del mercado.
                      </p>
                      <a href="#reservar" class="btn-pm-dorado">
                        <i class="bi bi-calendar-check me-2"></i>Aprovechar Oferta
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Promo 2 -->
        <div class="carousel-item">
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="pm-promo-card">
                <div class="row g-0 align-items-center">
                  <div class="col-md-5">
                    <div class="pm-promo-img-placeholder" style="background: linear-gradient(135deg, #8E977D, #B7BEA7);">
                      <span class="pm-promo-badge">OFERTA</span>
                      <div class="pm-promo-discount">-25%</div>
                    </div>
                  </div>
                  <div class="col-md-7">
                    <div class="pm-promo-body">
                      <h3 class="pm-promo-title">Crioterapia Facial + Corporal</h3>
                      <p class="pm-promo-desc">
                        Combo de tratamientos de frío: rejuvenecimiento facial con crioterapia
                        más sesión corporal reductora. Resultados visibles desde la primera sesión.
                      </p>
                      <a href="#reservar" class="btn-pm-dorado">
                        <i class="bi bi-calendar-check me-2"></i>Aprovechar Oferta
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Promo 3 -->
        <div class="carousel-item">
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="pm-promo-card">
                <div class="row g-0 align-items-center">
                  <div class="col-md-5">
                    <div class="pm-promo-img-placeholder" style="background: linear-gradient(135deg, #B77466, #DBCEA5);">
                      <span class="pm-promo-badge">OFERTA</span>
                      <div class="pm-promo-discount">-20%</div>
                    </div>
                  </div>
                  <div class="col-md-7">
                    <div class="pm-promo-body">
                      <h3 class="pm-promo-title">Día de Novia</h3>
                      <p class="pm-promo-desc">
                        Paquete completo para novias: limpieza facial, maquillaje profesional,
                        manicure spa y peinado. Tu día especial merece lo mejor.
                      </p>
                      <a href="#reservar" class="btn-pm-dorado">
                        <i class="bi bi-calendar-check me-2"></i>Aprovechar Oferta
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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

        <!-- Testimonio 1 -->
        <div class="carousel-item active">
          <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
              <div class="pm-testimonial-card text-center">
                <div class="pm-testimonial-quote">
                  <i class="bi bi-quote pm-testimonial-quote-icon"></i>
                </div>
                <p class="pm-testimonial-text">
                  "Desde que empecé mis sesiones de depilación láser en Piel Morena, mi vida cambió.
                  El trato es increíble, me siento como en casa cada vez que voy. Los resultados
                  son visibles desde la primera sesión. ¡100% recomendado!"
                </p>
                <div class="pm-testimonial-stars">
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                </div>
                <div class="pm-testimonial-author">
                  <div class="pm-testimonial-avatar" style="background: linear-gradient(135deg, #DBCEA5, #8A7650);">
                    <span>CL</span>
                  </div>
                  <div class="pm-testimonial-info">
                    <strong class="pm-testimonial-name">Carolina López</strong>
                    <span class="pm-testimonial-role">Clienta frecuente</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonio 2 -->
        <div class="carousel-item">
          <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
              <div class="pm-testimonial-card text-center">
                <div class="pm-testimonial-quote">
                  <i class="bi bi-quote pm-testimonial-quote-icon"></i>
                </div>
                <p class="pm-testimonial-text">
                  "Me hice la crioterapia facial y quedé fascinada. Mi piel se ve más joven,
                  más firme y radiante. Las chicas de Piel Morena son profesionales de verdad,
                  te explican todo el proceso y te hacen sentir segura."
                </p>
                <div class="pm-testimonial-stars">
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                </div>
                <div class="pm-testimonial-author">
                  <div class="pm-testimonial-avatar" style="background: linear-gradient(135deg, #FFE1AF, #957C62);">
                    <span>VM</span>
                  </div>
                  <div class="pm-testimonial-info">
                    <strong class="pm-testimonial-name">Valentina Martínez</strong>
                    <span class="pm-testimonial-role">Clienta de crioterapia</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonio 3 -->
        <div class="carousel-item">
          <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
              <div class="pm-testimonial-card text-center">
                <div class="pm-testimonial-quote">
                  <i class="bi bi-quote pm-testimonial-quote-icon"></i>
                </div>
                <p class="pm-testimonial-text">
                  "El maquillaje de mi boda fue perfecto. Sofía entendió exactamente lo que quería
                  y el resultado superó mis expectativas. Aguantó toda la fiesta sin retoques.
                  Piel Morena es sinónimo de calidad y profesionalismo."
                </p>
                <div class="pm-testimonial-stars">
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                </div>
                <div class="pm-testimonial-author">
                  <div class="pm-testimonial-avatar" style="background: linear-gradient(135deg, #B77466, #DBCEA5);">
                    <span>FS</span>
                  </div>
                  <div class="pm-testimonial-info">
                    <strong class="pm-testimonial-name">Florencia Sánchez</strong>
                    <span class="pm-testimonial-role">Novia 2025</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Indicadores -->
      <div class="pm-testimonial-indicators">
        <button type="button" data-bs-target="#testimoniosCarousel" data-bs-slide-to="0"
                class="pm-testimonial-indicator active" aria-current="true" aria-label="Testimonio 1"></button>
        <button type="button" data-bs-target="#testimoniosCarousel" data-bs-slide-to="1"
                class="pm-testimonial-indicator" aria-label="Testimonio 2"></button>
        <button type="button" data-bs-target="#testimoniosCarousel" data-bs-slide-to="2"
                class="pm-testimonial-indicator" aria-label="Testimonio 3"></button>
      </div>
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
