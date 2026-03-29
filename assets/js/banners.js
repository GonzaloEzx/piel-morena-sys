'use strict';

/* =======================================================================
   banners.js — Piel Morena Estetica
   Modulo de tooltips de precio en banners de servicios
   Registra clics de consulta de precio para analytics
   Dependencias: SweetAlert2, Bootstrap Icons
   ======================================================================= */


/* ═══════════════════════════════════════════════════════════
   PRICE TOOLTIP — popover de precio + tracking analytics
   ═══════════════════════════════════════════════════════════ */
const PriceTooltip = {

  /** @type {HTMLElement[]} */
  tooltips: [],

  /* ─────────────────────────────────────────────────────────
     init — buscar todos los .pm-price-tooltip y bindear eventos
     ───────────────────────────────────────────────────────── */
  init() {
    this.tooltips = Array.from(document.querySelectorAll('.pm-price-tooltip'));
    if (!this.tooltips.length) return;

    this.tooltips.forEach(tooltip => {
      tooltip.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation(); // Evitar que el click propague a la card

        this._handleClick(tooltip);
      });
    });
  },

  /* ─────────────────────────────────────────────────────────
     _handleClick — logica principal al hacer clic en el tooltip
     ───────────────────────────────────────────────────────── */
  _handleClick(element) {
    // Leer datos del elemento
    const serviceId   = element.getAttribute('data-service-id') || '';
    const serviceName = element.getAttribute('data-service-name') || 'Servicio';
    const price       = element.getAttribute('data-price') || '0.00';
    const duration    = element.getAttribute('data-duration') || '30';
    const category    = element.getAttribute('data-category') || '';

    // Mostrar el popover con la info del precio
    this.show({
      element,
      serviceId,
      serviceName,
      price,
      duration,
      category,
    });

    // Registrar la consulta de precio (fire and forget)
    this.trackClick(serviceId);

    // Animacion visual de "$" flotante
    this._showFloatingFeedback(element);
  },

  /* ─────────────────────────────────────────────────────────
     show — muestra el popup/popover con la info del servicio
     ───────────────────────────────────────────────────────── */
  show({ element, serviceId, serviceName, price, duration, category }) {
    // Formatear precio con separadores
    const formattedPrice = this._formatPrice(price);

    // Badge de categoria (si existe)
    const categoryBadge = category
      ? `<span class="pm-price-category"><i class="bi bi-tag me-1"></i>${category}</span>`
      : '';

    Swal.fire({
      title: serviceName,
      html: `
        <div class="pm-price-display">
          ${categoryBadge}
          <div class="pm-price-main">
            <span class="pm-price-amount">${formattedPrice}</span>
          </div>
          <div class="pm-price-duration">
            <i class="bi bi-clock me-1"></i>${duration} min
          </div>
          <div class="pm-price-divider"></div>
          <p class="pm-price-note">
            <i class="bi bi-info-circle me-1"></i>Precio sujeto a valoracion previa
          </p>
        </div>
      `,
      showConfirmButton: true,
      confirmButtonText: '<i class="bi bi-calendar-check me-1"></i>Reservar Ahora',
      showCancelButton: true,
      cancelButtonText: 'Cerrar',
      customClass: {
        popup:         'pm-modal pm-price-modal',
        title:         'pm-modal-title pm-price-title',
        htmlContainer: 'pm-modal-html',
        confirmButton: 'btn-pm',
        cancelButton:  'btn-pm-ghost',
      },
      showClass: {
        popup: 'swal2-show pm-scale-in',
      },
    }).then(result => {
      if (result.isConfirmed) {
        // Navegar a reservas con el servicio preseleccionado
        this._goToBooking(serviceId, serviceName);
      }
    });
  },

  /* ─────────────────────────────────────────────────────────
     trackClick — registra consulta de precio via AJAX (fire & forget)
     ───────────────────────────────────────────────────────── */
  trackClick(serviceId) {
    if (!serviceId) return;

    // Fire and forget — no esperamos respuesta, no mostramos errores
    try {
      fetch('api/servicios/consulta_precio.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_servicio: serviceId }),
      }).catch(() => {
        // Silenciar errores — no afectar UX por fallo de analytics
      });
    } catch {
      // Graceful fail silencioso
    }
  },

  /* ─────────────────────────────────────────────────────────
     _showFloatingFeedback — animacion "$" que sube y desaparece
     ───────────────────────────────────────────────────────── */
  _showFloatingFeedback(element) {
    const feedback = document.createElement('span');
    feedback.className = 'pm-price-feedback';
    feedback.textContent = '$';

    // Posicionar sobre el elemento
    const rect = element.getBoundingClientRect();
    feedback.style.position = 'fixed';
    feedback.style.left = `${rect.left + rect.width / 2}px`;
    feedback.style.top = `${rect.top}px`;
    feedback.style.transform = 'translateX(-50%)';
    feedback.style.zIndex = '10000';

    document.body.appendChild(feedback);

    // Animar: subir y desaparecer
    requestAnimationFrame(() => {
      feedback.style.transition = 'all 800ms cubic-bezier(0.4, 0, 0.2, 1)';
      feedback.style.top = `${rect.top - 50}px`;
      feedback.style.opacity = '0';
    });

    // Remover despues de la animacion
    setTimeout(() => {
      if (feedback.parentNode) {
        feedback.parentNode.removeChild(feedback);
      }
    }, 900);
  },

  /* ─────────────────────────────────────────────────────────
     _formatPrice — formatear precio como $X,XXX.XX
     ───────────────────────────────────────────────────────── */
  _formatPrice(price) {
    const num = parseFloat(price);
    if (isNaN(num) || num <= 0) return 'Consultar';

    return '$' + num.toLocaleString('es-MX', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  },

  /* ─────────────────────────────────────────────────────────
     _goToBooking — redirigir a la pagina de reservas
     ───────────────────────────────────────────────────────── */
  _goToBooking(serviceId, serviceName) {
    if (serviceId) {
      // Redirigir con el servicio preseleccionado
      window.location.href = `reservar.php?servicio=${encodeURIComponent(serviceId)}`;
    } else {
      // Redirigir a reservas general
      window.location.href = 'reservar.php';
    }
  },
};


/* ═══════════════════════════════════════════════════════════
   BANNER CAROUSEL — logica extra para carrusel de banners
   (si se necesita funcionalidad adicional a Bootstrap Carousel)
   ═══════════════════════════════════════════════════════════ */
const BannerCarousel = {

  /** @type {HTMLElement|null} */
  carousel: null,

  init() {
    this.carousel = document.querySelector('.pm-banner-carousel');
    if (!this.carousel) return;

    // Pausar autoplay al hover en los tooltips de precio
    this._setupHoverPause();
  },

  _setupHoverPause() {
    const bsCarousel = bootstrap.Carousel.getInstance(this.carousel)
      || new bootstrap.Carousel(this.carousel);

    // Pausar cuando SweetAlert esta abierto (popup de precio)
    const observer = new MutationObserver(() => {
      const swalOpen = document.querySelector('.swal2-container');
      if (swalOpen && bsCarousel) {
        bsCarousel.pause();
      } else if (bsCarousel) {
        bsCarousel.cycle();
      }
    });

    observer.observe(document.body, {
      childList: true,
      subtree: false,
    });
  },
};


/* ═══════════════════════════════════════════════════════════
   INICIALIZACION — DOMContentLoaded
   ═══════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  PriceTooltip.init();
  BannerCarousel.init();
});
