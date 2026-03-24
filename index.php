<?php $pagina_actual = 'inicio'; ?>
<?php require_once 'includes/header.php'; ?>

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
              <span>+10 años de experiencia</span>
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

    <!-- Grid de servicios: 3 cols desktop, 2 tablet, 1 mobile -->
    <div class="row g-4">

      <!-- Servicio 1: Depilación Láser -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-service-card">
          <div class="pm-service-img">
            <div class="pm-service-img-placeholder" style="background: linear-gradient(135deg, #FFE1AF 0%, #8A7650 100%);">
              <i class="bi bi-stars"></i>
            </div>
            <span class="pm-price-tooltip" data-service-id="1" data-service-name="Depilación Láser Axilas" data-price="350.00" data-duration="30" data-category="Depilación" title="Consultar precio">
              <i class="bi bi-currency-dollar"></i>
            </span>
            <span class="pm-badge">Depilación</span>
          </div>
          <div class="pm-service-body">
            <h4>Depilación Láser Axilas</h4>
            <p>Resultados duraderos con tecnología de última generación. Sesiones rápidas, seguras y prácticamente indoloras.</p>
            <span class="pm-service-duration"><i class="bi bi-clock me-1"></i> 30 min</span>
          </div>
          <div class="pm-service-footer">
            <a href="#reservar" class="btn-pm w-100">Reservar Cita <i class="bi bi-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>

      <!-- Servicio 2: Limpieza Facial Profunda -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-service-card">
          <div class="pm-service-img">
            <div class="pm-service-img-placeholder" style="background: linear-gradient(135deg, #B77466 0%, #8A7650 100%);">
              <i class="bi bi-droplet-half"></i>
            </div>
            <span class="pm-price-tooltip" data-service-id="2" data-service-name="Limpieza Facial Profunda" data-price="500.00" data-duration="60" data-category="Facial" title="Consultar precio">
              <i class="bi bi-currency-dollar"></i>
            </span>
            <span class="pm-badge">Facial</span>
          </div>
          <div class="pm-service-body">
            <h4>Limpieza Facial Profunda</h4>
            <p>Limpieza profunda con extracción, exfoliación enzimática y mascarilla hidratante. Piel renovada y luminosa.</p>
            <span class="pm-service-duration"><i class="bi bi-clock me-1"></i> 60 min</span>
          </div>
          <div class="pm-service-footer">
            <a href="#reservar" class="btn-pm w-100">Reservar Cita <i class="bi bi-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>

      <!-- Servicio 3: Masaje Relajante -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-service-card">
          <div class="pm-service-img">
            <div class="pm-service-img-placeholder" style="background: linear-gradient(135deg, #DBCEA5 0%, #957C62 100%);">
              <i class="bi bi-person-arms-up"></i>
            </div>
            <span class="pm-price-tooltip" data-service-id="3" data-service-name="Masaje Relajante" data-price="600.00" data-duration="50" data-category="Corporal" title="Consultar precio">
              <i class="bi bi-currency-dollar"></i>
            </span>
            <span class="pm-badge">Corporal</span>
          </div>
          <div class="pm-service-body">
            <h4>Masaje Relajante</h4>
            <p>Masaje descontracturante y relajante con aceites esenciales. Alivia tensiones y renueva tu energía.</p>
            <span class="pm-service-duration"><i class="bi bi-clock me-1"></i> 50 min</span>
          </div>
          <div class="pm-service-footer">
            <a href="#reservar" class="btn-pm w-100">Reservar Cita <i class="bi bi-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>

      <!-- Servicio 4: Crioterapia Facial -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-service-card">
          <div class="pm-service-img">
            <div class="pm-service-img-placeholder" style="background: linear-gradient(135deg, #8E977D 0%, #B7BEA7 100%);">
              <i class="bi bi-snow"></i>
            </div>
            <span class="pm-price-tooltip" data-service-id="4" data-service-name="Crioterapia Facial" data-price="450.00" data-duration="40" data-category="Frío" title="Consultar precio">
              <i class="bi bi-currency-dollar"></i>
            </span>
            <span class="pm-badge pm-badge-frio">Frío</span>
          </div>
          <div class="pm-service-body">
            <h4>Crioterapia Facial</h4>
            <p>Tratamiento con frío controlado que estimula la producción de colágeno, reduce poros y rejuvenece la piel.</p>
            <span class="pm-service-duration"><i class="bi bi-clock me-1"></i> 40 min</span>
          </div>
          <div class="pm-service-footer">
            <a href="#reservar" class="btn-pm w-100">Reservar Cita <i class="bi bi-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>

      <!-- Servicio 5: Maquillaje Social -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-service-card">
          <div class="pm-service-img">
            <div class="pm-service-img-placeholder" style="background: linear-gradient(135deg, #E2B59A 0%, #B77466 100%);">
              <i class="bi bi-palette"></i>
            </div>
            <span class="pm-price-tooltip" data-service-id="5" data-service-name="Maquillaje Social" data-price="800.00" data-duration="45" data-category="Maquillaje" title="Consultar precio">
              <i class="bi bi-currency-dollar"></i>
            </span>
            <span class="pm-badge">Maquillaje</span>
          </div>
          <div class="pm-service-body">
            <h4>Maquillaje Social</h4>
            <p>Maquillaje profesional para eventos, fiestas y ocasiones especiales. Luce radiante en cada momento.</p>
            <span class="pm-service-duration"><i class="bi bi-clock me-1"></i> 45 min</span>
          </div>
          <div class="pm-service-footer">
            <a href="#reservar" class="btn-pm w-100">Reservar Cita <i class="bi bi-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>

      <!-- Servicio 6: Manicure Spa -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-service-card">
          <div class="pm-service-img">
            <div class="pm-service-img-placeholder" style="background: linear-gradient(135deg, #DBCEA5 0%, #FFE1AF 100%);">
              <i class="bi bi-brush"></i>
            </div>
            <span class="pm-price-tooltip" data-service-id="6" data-service-name="Manicure Spa" data-price="300.00" data-duration="40" data-category="Uñas" title="Consultar precio">
              <i class="bi bi-currency-dollar"></i>
            </span>
            <span class="pm-badge">Uñas</span>
          </div>
          <div class="pm-service-body">
            <h4>Manicure Spa</h4>
            <p>Tratamiento completo de manos: exfoliación, hidratación profunda, esmaltado y diseño personalizado.</p>
            <span class="pm-service-duration"><i class="bi bi-clock me-1"></i> 40 min</span>
          </div>
          <div class="pm-service-footer">
            <a href="#reservar" class="btn-pm w-100">Reservar Cita <i class="bi bi-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>

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

    <!-- Grid: 4 cols desktop, 2 tablet, 1 mobile -->
    <div class="row g-4 justify-content-center">

      <!-- Miembro 1 -->
      <div class="col-lg-3 col-md-6 pm-animate">
        <div class="pm-team-card">
          <div class="pm-team-photo">
            <div class="pm-team-photo-placeholder" style="background: linear-gradient(135deg, #DBCEA5, #8A7650);">
              <span class="pm-team-initials">MG</span>
            </div>
          </div>
          <div class="pm-team-info">
            <h4 class="pm-team-name">María García</h4>
            <p class="pm-team-role">Directora &amp; Esteticista</p>
            <div class="pm-team-social">
              <a href="#" class="pm-social-icon" aria-label="Instagram de María" target="_blank" rel="noopener">
                <i class="bi bi-instagram"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Miembro 2 -->
      <div class="col-lg-3 col-md-6 pm-animate">
        <div class="pm-team-card">
          <div class="pm-team-photo">
            <div class="pm-team-photo-placeholder" style="background: linear-gradient(135deg, #FFE1AF, #957C62);">
              <span class="pm-team-initials">LP</span>
            </div>
          </div>
          <div class="pm-team-info">
            <h4 class="pm-team-name">Laura Pérez</h4>
            <p class="pm-team-role">Especialista en Depilación Láser</p>
            <div class="pm-team-social">
              <a href="#" class="pm-social-icon" aria-label="Instagram de Laura" target="_blank" rel="noopener">
                <i class="bi bi-instagram"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Miembro 3 -->
      <div class="col-lg-3 col-md-6 pm-animate">
        <div class="pm-team-card">
          <div class="pm-team-photo">
            <div class="pm-team-photo-placeholder" style="background: linear-gradient(135deg, #B77466, #8A7650);">
              <span class="pm-team-initials">AR</span>
            </div>
          </div>
          <div class="pm-team-info">
            <h4 class="pm-team-name">Ana Rodríguez</h4>
            <p class="pm-team-role">Cosmetóloga &amp; Crioterapia</p>
            <div class="pm-team-social">
              <a href="#" class="pm-social-icon" aria-label="Instagram de Ana" target="_blank" rel="noopener">
                <i class="bi bi-instagram"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Miembro 4 -->
      <div class="col-lg-3 col-md-6 pm-animate">
        <div class="pm-team-card">
          <div class="pm-team-photo">
            <div class="pm-team-photo-placeholder" style="background: linear-gradient(135deg, #E2B59A, #DBCEA5);">
              <span class="pm-team-initials">SL</span>
            </div>
          </div>
          <div class="pm-team-info">
            <h4 class="pm-team-name">Sofía López</h4>
            <p class="pm-team-role">Maquilladora Profesional</p>
            <div class="pm-team-social">
              <a href="#" class="pm-social-icon" aria-label="Instagram de Sofía" target="_blank" rel="noopener">
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
     5. GALERÍA — bg: --pm-crema
     ═══════════════════════════════════════════════════════════════ -->
<section id="galeria" class="pm-section-alt" data-section="galeria">
  <div class="container">

    <!-- Título de sección -->
    <div class="text-center mb-5">
      <h2 class="pm-section-title pm-animate">Galería</h2>
      <div class="pm-divider"></div>
      <p class="pm-section-subtitle pm-animate">Nuestros trabajos y el ambiente de Piel Morena</p>
    </div>

    <!-- Grid masonry-style: 3 cols -->
    <div class="row g-3">

      <!-- Galería item 1 -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-gallery-item">
          <div class="pm-gallery-placeholder" style="background: linear-gradient(135deg, #DBCEA5, #8A7650); height: 280px;">
            <span class="pm-gallery-text">Piel Morena</span>
          </div>
          <div class="pm-gallery-overlay">
            <i class="bi bi-zoom-in"></i>
          </div>
        </div>
      </div>

      <!-- Galería item 2 -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-gallery-item">
          <div class="pm-gallery-placeholder" style="background: linear-gradient(135deg, #FFE1AF, #957C62); height: 340px;">
            <span class="pm-gallery-text">Piel Morena</span>
          </div>
          <div class="pm-gallery-overlay">
            <i class="bi bi-zoom-in"></i>
          </div>
        </div>
      </div>

      <!-- Galería item 3 -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-gallery-item">
          <div class="pm-gallery-placeholder" style="background: linear-gradient(135deg, #B77466, #8A7650); height: 300px;">
            <span class="pm-gallery-text">Piel Morena</span>
          </div>
          <div class="pm-gallery-overlay">
            <i class="bi bi-zoom-in"></i>
          </div>
        </div>
      </div>

      <!-- Galería item 4 -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-gallery-item">
          <div class="pm-gallery-placeholder" style="background: linear-gradient(135deg, #8E977D, #B7BEA7); height: 320px;">
            <span class="pm-gallery-text">Piel Morena</span>
          </div>
          <div class="pm-gallery-overlay">
            <i class="bi bi-zoom-in"></i>
          </div>
        </div>
      </div>

      <!-- Galería item 5 -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-gallery-item">
          <div class="pm-gallery-placeholder" style="background: linear-gradient(135deg, #E2B59A, #DBCEA5); height: 260px;">
            <span class="pm-gallery-text">Piel Morena</span>
          </div>
          <div class="pm-gallery-overlay">
            <i class="bi bi-zoom-in"></i>
          </div>
        </div>
      </div>

      <!-- Galería item 6 -->
      <div class="col-lg-4 col-md-6 pm-animate">
        <div class="pm-gallery-item">
          <div class="pm-gallery-placeholder" style="background: linear-gradient(135deg, #957C62, #7A654F); height: 340px;">
            <span class="pm-gallery-text">Piel Morena</span>
          </div>
          <div class="pm-gallery-overlay">
            <i class="bi bi-zoom-in"></i>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════
     6. PROMOCIONES — bg: --pm-marfil
     ═══════════════════════════════════════════════════════════════ -->
<section id="promos" class="pm-section" data-section="promos">
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
<section id="testimonios" class="pm-section-alt" data-section="testimonios">
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
<section id="reservar" class="pm-section pm-cta-section" data-section="reservar">
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
          <a href="https://wa.me/5491100000000" class="btn-pm-outline btn-pm-outline--white btn-pm-lg" target="_blank" rel="noopener">
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
              <p>Av. Principal #123, Col. Centro, Ciudad, CP 00000</p>
            </div>
          </div>

          <!-- Teléfono -->
          <div class="pm-contact-item">
            <div class="pm-contact-icon">
              <i class="bi bi-telephone-fill"></i>
            </div>
            <div>
              <strong>Teléfono</strong>
              <p><a href="tel:+5200000000">+52 (000) 000-0000</a></p>
            </div>
          </div>

          <!-- Email -->
          <div class="pm-contact-item">
            <div class="pm-contact-icon">
              <i class="bi bi-envelope-fill"></i>
            </div>
            <div>
              <strong>Correo</strong>
              <p><a href="mailto:contacto@pielmorena.com">contacto@pielmorena.com</a></p>
            </div>
          </div>

          <!-- Horarios -->
          <div class="pm-contact-item">
            <div class="pm-contact-icon">
              <i class="bi bi-clock-fill"></i>
            </div>
            <div>
              <strong>Horarios</strong>
              <p>Lun - Vie: 9:00 - 20:00</p>
              <p>Sáb: 9:00 - 14:00</p>
              <p>Dom: Cerrado</p>
            </div>
          </div>

          <!-- Redes sociales -->
          <div class="pm-contact-social">
            <a href="#" class="pm-social-icon" aria-label="Instagram" target="_blank" rel="noopener">
              <i class="bi bi-instagram"></i>
            </a>
            <a href="#" class="pm-social-icon" aria-label="Facebook" target="_blank" rel="noopener">
              <i class="bi bi-facebook"></i>
            </a>
            <a href="https://wa.me/5491100000000" class="pm-social-icon" aria-label="WhatsApp" target="_blank" rel="noopener">
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

<?php require_once 'includes/footer.php'; ?>
