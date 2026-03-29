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
              <h1 class="pm-hero-title pm-animate">Tu belleza,<br>nuestra pasión</h1>
              <p class="pm-hero-subtitle pm-animate">Estética &middot; Depilación &middot; Tratamientos de Frío</p>
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
              <span class="pm-hero-badge"><i class="bi bi-snow me-2"></i>Nuevo</span>
              <h1 class="pm-hero-title pm-animate">Tratamientos<br>de Frío</h1>
              <p class="pm-hero-subtitle pm-animate">Crioterapia facial y corporal</p>
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

      <!-- Imagen placeholder -->
      <div class="col-lg-6 pm-animate">
        <div class="pm-about-img-placeholder">
          <div class="pm-placeholder-content">
            <span class="pm-placeholder-icon">&#127832;</span>
            <span class="pm-placeholder-text">Piel Morena</span>
          </div>
        </div>
      </div>

      <!-- Texto -->
      <div class="col-lg-6 pm-animate">
        <h3 class="pm-about-heading">
          Tu espacio de belleza y confianza
        </h3>
        <p>
          En <strong>Piel Morena</strong> creemos que cada persona merece sentirse hermosa en su propia piel.
          Somos un salón de belleza integral especializado en depilación láser, tratamientos faciales,
          corporales, crioterapia y estética avanzada. Nuestro compromiso es ofrecer un servicio
          personalizado, con calidez humana y tecnología de vanguardia.
        </p>
        <p class="pm-small">
          Nuestra misión es transformar la experiencia de cuidado personal en un momento de bienestar
          y confianza. Trabajamos con productos de primera calidad y técnicas actualizadas para
          garantizar los mejores resultados.
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

    <!-- Grid dinámico: 1 servicio destacado por categoría (máx 6) -->
    <?php // Cargar un servicio representativo por categoría desde BD


    $db_landing = getDB();
    $stmt_cats = $db_landing->query("SELECT c.id, c.nombre, c.icono,
              (SELECT COUNT(*) FROM servicios WHERE id_categoria = c.id AND activo = 1) AS total_servicios
       FROM categorias_servicios c
       WHERE c.activo = 1
       ORDER BY c.orden
       LIMIT 6");
    $categorias_landing = $stmt_cats->fetchAll(); // Un servicio destacado por categoría (el primero activo)
    $stmt_serv = $db_landing->prepare("SELECT s.id, s.nombre, s.descripcion, s.precio, s.duracion_minutos,
              c.nombre AS categoria, c.icono AS categoria_icono
       FROM servicios s
       JOIN categorias_servicios c ON s.id_categoria = c.id
       WHERE s.id_categoria = ? AND s.activo = 1
       ORDER BY s.nombre LIMIT 1");
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
      foreach ($categorias_landing as $cat):

          $stmt_serv->execute([$cat["id"]]);
          $serv = $stmt_serv->fetch();
          if (!$serv) {
              continue;
          }
          $icono = $cat["icono"] ?: "bi-stars";
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
] ?>" data-category="<?= sanitizar($cat["nombre"]) ?>" title="Consultar precio">
              <i class="bi bi-currency-dollar"></i>
            </span>
            <span class="pm-badge"><?= sanitizar($cat["nombre"]) ?></span>
          </div>
          <div class="pm-service-body">
            <h4><?= sanitizar($serv["nombre"]) ?></h4>
            <p><?= sanitizar(
                mb_strimwidth($serv["descripcion"], 0, 120, "..."),
            ) ?></p>
            <span class="pm-service-duration"><i class="bi bi-clock me-1"></i> <?= $serv[
                "duracion_minutos"
            ] ?> min</span>
            <?php if ($cat["total_servicios"] > 1): ?>
            <span class="pm-service-count"><i class="bi bi-grid me-1"></i>+<?= $cat[
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
            <h4 class="pm-team-name">Mariángeles Zudaire</h4>
            <p class="pm-team-role">Dermatocosmiatra &amp; Operadora Láser</p>
            <div class="pm-team-social">
              <a href="https://www.instagram.com/pielmorenaesteticaok" class="pm-social-icon" aria-label="Instagram de Mariángeles" target="_blank" rel="noopener">
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
              <a href="#" class="pm-social-icon" aria-label="Instagram de Rosario" target="_blank" rel="noopener">
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
              <a href="#" class="pm-social-icon" aria-label="Instagram de Lucía" target="_blank" rel="noopener">
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
              <a href="#" class="pm-social-icon" aria-label="Instagram de Nathalia" target="_blank" rel="noopener">
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

      <!-- Naila Centurión -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-team-card">
          <div class="pm-team-photo">
            <div class="pm-team-photo-placeholder" style="background: linear-gradient(135deg, #957C62, #B77466);">
              <span class="pm-team-initials">NC</span>
            </div>
          </div>
          <div class="pm-team-info">
            <h4 class="pm-team-name">Naila Centurión</h4>
            <p class="pm-team-role">Certificada en Pestañas</p>
            <div class="pm-team-social">
              <a href="#" class="pm-social-icon" aria-label="Instagram de Naila" target="_blank" rel="noopener">
                <i class="bi bi-instagram"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════
     4b. INFO DE TRATAMIENTOS — bg: --pm-marfil
     Sección informativa sobre los tratamientos estrella del salón.
     ═══════════════════════════════════════════════════════════════ -->
<section id="tratamientos" class="pm-section-alt" data-section="tratamientos">
  <div class="container">

    <div class="text-center mb-5">
      <h2 class="pm-section-title pm-animate">Conocé Nuestros Tratamientos</h2>
      <div class="pm-divider"></div>
      <p class="pm-section-subtitle pm-animate">Información detallada para que elijas el tratamiento ideal para vos</p>
    </div>

    <div class="row g-4">

      <!-- Tratamiento 1: Limpieza Facial Profunda -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-tratamiento-card">
          <div class="pm-tratamiento-icon">
            <i class="bi bi-droplet-half"></i>
          </div>
          <h4 class="pm-tratamiento-title">Limpieza Facial Profunda</h4>
          <p class="pm-tratamiento-desc">
            Tratamiento completo que limpia en profundidad los poros, elimina impurezas y células muertas. Incluye vapor de ozono, extracción profesional y mascarilla hidratante personalizada según tu tipo de piel.
          </p>
          <ul class="pm-tratamiento-beneficios">
            <li><i class="bi bi-check2-circle"></i>Piel limpia, luminosa y oxigenada</li>
            <li><i class="bi bi-check2-circle"></i>Previene puntos negros y acné</li>
            <li><i class="bi bi-check2-circle"></i>Mejora la absorción de productos</li>
          </ul>
          <div class="pm-tratamiento-meta">
            <span><i class="bi bi-clock me-1"></i>90 min</span>
            <span><i class="bi bi-arrow-repeat me-1"></i>Mensual</span>
          </div>
          <a href="<?= URL_BASE ?>/reservar.php" class="btn btn-pm-outline btn-sm w-100 mt-auto">Reservar turno</a>
        </div>
      </div>

      <!-- Tratamiento 2: Depilación Láser Soprano -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-tratamiento-card">
          <div class="pm-tratamiento-icon">
            <i class="bi bi-stars"></i>
          </div>
          <h4 class="pm-tratamiento-title">Depilación Láser Soprano</h4>
          <p class="pm-tratamiento-desc">
            Tecnología Soprano de última generación para depilación definitiva. Indoloro y apto para todo tipo de piel y vello. Sesiones rápidas con resultados progresivos que reducen el vello hasta un 95%.
          </p>
          <ul class="pm-tratamiento-beneficios">
            <li><i class="bi bi-check2-circle"></i>Prácticamente indoloro</li>
            <li><i class="bi bi-check2-circle"></i>Apto para pieles sensibles</li>
            <li><i class="bi bi-check2-circle"></i>Resultados desde la primera sesión</li>
          </ul>
          <div class="pm-tratamiento-meta">
            <span><i class="bi bi-clock me-1"></i>15-60 min</span>
            <span><i class="bi bi-arrow-repeat me-1"></i>Cada 4-6 semanas</span>
          </div>
          <a href="<?= URL_BASE ?>/reservar.php" class="btn btn-pm-outline btn-sm w-100 mt-auto">Reservar turno</a>
        </div>
      </div>

      <!-- Tratamiento 3: Criolipólisis -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-tratamiento-card">
          <div class="pm-tratamiento-icon">
            <i class="bi bi-snow"></i>
          </div>
          <h4 class="pm-tratamiento-title">Criolipólisis en Frío</h4>
          <p class="pm-tratamiento-desc">
            Tratamiento no invasivo que elimina grasa localizada mediante aplicación controlada de frío. Las células grasas se cristalizan y el cuerpo las elimina naturalmente. Sin cirugía, sin agujas, sin tiempo de recuperación.
          </p>
          <ul class="pm-tratamiento-beneficios">
            <li><i class="bi bi-check2-circle"></i>Reduce grasa localizada sin cirugía</li>
            <li><i class="bi bi-check2-circle"></i>Resultados visibles en 2-3 semanas</li>
            <li><i class="bi bi-check2-circle"></i>Sin tiempo de recuperación</li>
          </ul>
          <div class="pm-tratamiento-meta">
            <span><i class="bi bi-clock me-1"></i>60 min</span>
            <span><i class="bi bi-arrow-repeat me-1"></i>1-3 sesiones</span>
          </div>
          <a href="<?= URL_BASE ?>/reservar.php" class="btn btn-pm-outline btn-sm w-100 mt-auto">Reservar turno</a>
        </div>
      </div>

      <!-- Tratamiento 4: Dermapen -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-tratamiento-card">
          <div class="pm-tratamiento-icon">
            <i class="bi bi-brush"></i>
          </div>
          <h4 class="pm-tratamiento-title">Dermapen</h4>
          <p class="pm-tratamiento-desc">
            Microagujas automatizadas que estimulan la regeneración natural de la piel. Activa la producción de colágeno y elastina, mejorando visiblemente cicatrices, poros dilatados, arrugas finas y manchas.
          </p>
          <ul class="pm-tratamiento-beneficios">
            <li><i class="bi bi-check2-circle"></i>Rejuvenece y reafirma la piel</li>
            <li><i class="bi bi-check2-circle"></i>Reduce cicatrices y marcas de acné</li>
            <li><i class="bi bi-check2-circle"></i>Mejora textura y tono</li>
          </ul>
          <div class="pm-tratamiento-meta">
            <span><i class="bi bi-clock me-1"></i>60 min</span>
            <span><i class="bi bi-arrow-repeat me-1"></i>Cada 4 semanas</span>
          </div>
          <a href="<?= URL_BASE ?>/reservar.php" class="btn btn-pm-outline btn-sm w-100 mt-auto">Reservar turno</a>
        </div>
      </div>

      <!-- Tratamiento 5: Radiofrecuencia Facial -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-tratamiento-card">
          <div class="pm-tratamiento-icon">
            <i class="bi bi-lightning-charge"></i>
          </div>
          <h4 class="pm-tratamiento-title">Radiofrecuencia Facial</h4>
          <p class="pm-tratamiento-desc">
            Energía de radiofrecuencia que calienta las capas profundas de la piel estimulando la producción de colágeno. Efecto lifting inmediato, tensor y rejuvenecedor. Ideal para flacidez y líneas de expresión.
          </p>
          <ul class="pm-tratamiento-beneficios">
            <li><i class="bi bi-check2-circle"></i>Efecto lifting desde la primera sesión</li>
            <li><i class="bi bi-check2-circle"></i>Reduce arrugas y líneas finas</li>
            <li><i class="bi bi-check2-circle"></i>Reafirma y tonifica el rostro</li>
          </ul>
          <div class="pm-tratamiento-meta">
            <span><i class="bi bi-clock me-1"></i>60 min</span>
            <span><i class="bi bi-arrow-repeat me-1"></i>Semanal (6-8 sesiones)</span>
          </div>
          <a href="<?= URL_BASE ?>/reservar.php" class="btn btn-pm-outline btn-sm w-100 mt-auto">Reservar turno</a>
        </div>
      </div>

      <!-- Tratamiento 6: Masaje Descontracturante -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-tratamiento-card">
          <div class="pm-tratamiento-icon">
            <i class="bi bi-hand-index-thumb"></i>
          </div>
          <h4 class="pm-tratamiento-title">Masaje Descontracturante</h4>
          <p class="pm-tratamiento-desc">
            Masaje terapéutico profundo que libera contracturas, nudos musculares y tensión acumulada. Trabaja puntos gatillo y cadenas musculares para aliviar dolor y restaurar movilidad. Ideal para estrés y malas posturas.
          </p>
          <ul class="pm-tratamiento-beneficios">
            <li><i class="bi bi-check2-circle"></i>Alivia dolor muscular y tensión</li>
            <li><i class="bi bi-check2-circle"></i>Mejora la circulación sanguínea</li>
            <li><i class="bi bi-check2-circle"></i>Reduce el estrés y la ansiedad</li>
          </ul>
          <div class="pm-tratamiento-meta">
            <span><i class="bi bi-clock me-1"></i>30-60 min</span>
            <span><i class="bi bi-arrow-repeat me-1"></i>Semanal o quincenal</span>
          </div>
          <a href="<?= URL_BASE ?>/reservar.php" class="btn btn-pm-outline btn-sm w-100 mt-auto">Reservar turno</a>
        </div>
      </div>

    </div>

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

        <!-- Mapa placeholder -->
        <div class="pm-map-placeholder mt-4">
          <div class="pm-map-inner">
            <i class="bi bi-geo-alt"></i>
            <p>Mapa - Próximamente</p>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>

</main>

<!-- Estilos movidos a assets/css/style.css — JS movido a assets/js/main.js -->

<?php require_once "includes/footer.php"; ?>
