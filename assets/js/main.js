'use strict';

/* =======================================================================
   main.js — Piel Morena Estetica
   Modulo principal del landing page (one-page)
   Dependencias: Bootstrap 5.3, SweetAlert2, Bootstrap Icons
   ======================================================================= */


/* ═══════════════════════════════════════════════════════════
   NAVBAR — glassmorphism on scroll + shrink
   ═══════════════════════════════════════════════════════════ */
const NavBar = {
  /** @type {HTMLElement|null} */
  el: null,

  init() {
    this.el = document.querySelector('.pm-navbar');
    if (!this.el) return;

    // Estado inicial
    this._onScroll();

    // Escuchar scroll con passive para mejor rendimiento
    window.addEventListener('scroll', () => this._onScroll(), { passive: true });
  },

  _onScroll() {
    const isScrolled = window.scrollY > 50;
    this.el.classList.toggle('scrolled', isScrolled);
  },
};


/* ═══════════════════════════════════════════════════════════
   SCROLL ANIMATIONS — fade-up al entrar al viewport
   Clase trigger: .pm-animate → agrega .pm-visible
   ═══════════════════════════════════════════════════════════ */
const ScrollAnimations = {
  /** @type {IntersectionObserver|null} */
  observer: null,

  init() {
    const elements = document.querySelectorAll(
      '.pm-animate, .pm-animate-left, .pm-animate-right'
    );
    if (!elements.length) return;

    // Verificar soporte de IntersectionObserver
    if (!('IntersectionObserver' in window)) {
      // Fallback: mostrar todo sin animacion
      elements.forEach(el => el.classList.add('pm-visible'));
      return;
    }

    this.observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('pm-visible');
          // Solo animar una vez — dejar de observar
          this.observer.unobserve(entry.target);
        }
      });
    }, {
      root: null,
      rootMargin: '0px 0px 50px 0px',
      threshold: 0.1,
    });

    elements.forEach(el => this.observer.observe(el));

    // Fallback: elementos ya visibles en el viewport al cargar
    // IntersectionObserver puede no disparar el callback inicial de forma confiable
    requestAnimationFrame(() => {
      elements.forEach(el => {
        if (el.classList.contains('pm-visible')) return;
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight && rect.bottom > 0) {
          el.classList.add('pm-visible');
          this.observer.unobserve(el);
        }
      });
    });
  },
};


/* ═══════════════════════════════════════════════════════════
   SMOOTH SCROLL — links internos con offset del navbar
   ═══════════════════════════════════════════════════════════ */
const SmoothScroll = {
  /** Offset en px para compensar la altura del navbar */
  OFFSET: 80,

  init() {
    document.addEventListener('click', (e) => {
      const link = e.target.closest('a[href^="#"]');
      if (!link) return;

      const targetId = link.getAttribute('href');
      // Ignorar href="#" sin destino real
      if (!targetId || targetId === '#') return;

      const target = document.querySelector(targetId);
      if (!target) return;

      e.preventDefault();

      const top = target.getBoundingClientRect().top + window.scrollY - this.OFFSET;
      window.scrollTo({ top, behavior: 'smooth' });

      // Cerrar navbar mobile si esta abierta (Bootstrap offcanvas/collapse)
      const navCollapse = document.querySelector('.navbar-collapse.show');
      if (navCollapse) {
        const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
        if (bsCollapse) bsCollapse.hide();
      }

      // Cerrar offcanvas mobile si esta abierto
      const offcanvas = document.querySelector('.offcanvas.show');
      if (offcanvas) {
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvas);
        if (bsOffcanvas) bsOffcanvas.hide();
      }
    });
  },
};


/* ═══════════════════════════════════════════════════════════
   CONTACT FORM — validacion + SweetAlert2 feedback
   ═══════════════════════════════════════════════════════════ */
const ContactForm = {
  /** @type {HTMLFormElement|null} */
  form: null,

  init() {
    this.form = document.getElementById('contactForm');
    if (!this.form) return;

    this.form.addEventListener('submit', (e) => this._onSubmit(e));
  },

  _onSubmit(e) {
    e.preventDefault();

    // Obtener valores
    const nombre  = this.form.querySelector('[name="nombre"]');
    const email   = this.form.querySelector('[name="email"]');
    const telefono = this.form.querySelector('[name="telefono"]');
    const mensaje = this.form.querySelector('[name="mensaje"]');

    // Limpiar estados de error previos
    this._clearErrors();

    // Validar campos requeridos
    const errors = [];

    if (!nombre || !nombre.value.trim()) {
      errors.push({ field: nombre, msg: 'El nombre es requerido' });
    }

    if (!email || !email.value.trim()) {
      errors.push({ field: email, msg: 'El email es requerido' });
    } else if (!this._isValidEmail(email.value.trim())) {
      errors.push({ field: email, msg: 'Ingresa un email valido' });
    }

    if (!mensaje || !mensaje.value.trim()) {
      errors.push({ field: mensaje, msg: 'El mensaje es requerido' });
    }

    // Si hay errores, mostrarlos
    if (errors.length > 0) {
      errors.forEach(err => {
        if (err.field) {
          err.field.classList.add('is-invalid');
        }
      });

      Swal.fire({
        icon: 'warning',
        title: 'Campos incompletos',
        text: errors[0].msg,
        confirmButtonText: 'Entendido',
        customClass: {
          popup: 'pm-modal',
          confirmButton: 'btn-pm',
        },
      });
      return;
    }

    // Preparar datos para envio
    const formData = {
      nombre: nombre.value.trim(),
      email: email.value.trim(),
      telefono: telefono ? telefono.value.trim() : '',
      mensaje: mensaje.value.trim(),
    };

    // Enviar al backend (cuando este listo)
    // Por ahora: mostrar exito y enviar con fetch si el endpoint existe
    this._sendMessage(formData);
  },

  async _sendMessage(data) {
    try {
      // Intentar enviar al backend
      const response = await fetch('api/contacto.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      if (result.success) {
        this._showSuccess();
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: result.error || 'No se pudo enviar el mensaje. Intenta de nuevo.',
          confirmButtonText: 'Entendido',
          customClass: { popup: 'pm-modal', confirmButton: 'btn-pm' },
        });
      }
    } catch {
      Swal.fire({
        icon: 'error',
        title: 'Error de conexion',
        text: 'No se pudo conectar con el servidor. Intenta mas tarde.',
        confirmButtonText: 'Entendido',
        customClass: { popup: 'pm-modal', confirmButton: 'btn-pm' },
      });
    }
  },

  _showSuccess() {
    Swal.fire({
      icon: 'success',
      title: 'Mensaje enviado',
      html: '<p>Gracias por contactarnos. Te responderemos a la brevedad.</p>',
      confirmButtonText: 'Perfecto',
      customClass: {
        popup: 'pm-modal',
        confirmButton: 'btn-pm',
      },
    });

    // Limpiar formulario
    this.form.reset();
    this._clearErrors();
  },

  _clearErrors() {
    if (!this.form) return;
    this.form.querySelectorAll('.is-invalid').forEach(el => {
      el.classList.remove('is-invalid');
    });
  },

  _isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  },
};


/* ═══════════════════════════════════════════════════════════
   BACK TO TOP — boton con indicador de progreso circular SVG
   Aparece despues del hero, muestra % de scroll
   ═══════════════════════════════════════════════════════════ */
const BackToTop = {
  /** @type {HTMLElement|null} */
  btn: null,
  /** @type {SVGCircleElement|null} */
  progressBar: null,
  /** Circunferencia del circulo SVG (2 * PI * r) */
  CIRCUMFERENCE: 126,

  init() {
    this.btn = document.querySelector('.pm-back-to-top');

    if (!this.btn) {
      this.btn = document.createElement('button');
      this.btn.className = 'pm-back-to-top';
      this.btn.setAttribute('aria-label', 'Volver al inicio');
      this.btn.setAttribute('title', 'Volver al inicio');
      this.btn.innerHTML = `
        <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
          <circle class="progress-bg" cx="24" cy="24" r="20" />
          <circle class="progress-bar" cx="24" cy="24" r="20" />
          <path class="arrow-icon" d="M24 32V18m0 0l-5 5m5-5l5 5" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>`;
      document.body.appendChild(this.btn);
    }

    this.progressBar = this.btn.querySelector('.progress-bar');

    this.btn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    this._onScroll();
    window.addEventListener('scroll', () => this._onScroll(), { passive: true });
  },

  _onScroll() {
    if (!this.btn) return;

    const scrollTop = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    const isVisible = scrollTop > 400;

    this.btn.classList.toggle('visible', isVisible);

    // Actualizar progreso circular
    if (this.progressBar && docHeight > 0) {
      const progress = Math.min(scrollTop / docHeight, 1);
      const offset = this.CIRCUMFERENCE - (progress * this.CIRCUMFERENCE);
      this.progressBar.style.strokeDashoffset = offset;
    }
  },
};


/* ═══════════════════════════════════════════════════════════
   CAROUSEL SYNC — sincronizar indicadores custom con Bootstrap
   ═══════════════════════════════════════════════════════════ */
const CarouselSync = {
  init() {
    this._sync('#heroCarousel', '.pm-hero-indicator');
    this._sync('#testimoniosCarousel', '.pm-testimonial-indicator');
  },

  _sync(carouselId, indicatorSelector) {
    const carousel = document.querySelector(carouselId);
    if (!carousel) return;

    carousel.addEventListener('slid.bs.carousel', (e) => {
      document.querySelectorAll(indicatorSelector).forEach((ind, i) => {
        ind.classList.toggle('active', i === e.to);
      });
    });
  },
};


/* ═══════════════════════════════════════════════════════════
   GALLERY LIGHTBOX — fullscreen image viewer
   Clicks on .pm-gallery-item open a warm overlay
   ═══════════════════════════════════════════════════════════ */
const GalleryLightbox = {
  /** @type {HTMLElement|null} */
  lightbox: null,
  /** @type {NodeListOf<Element>} */
  items: [],
  /** Current index */
  current: 0,

  init() {
    this.items = document.querySelectorAll('.pm-gallery-item');
    if (!this.items.length) return;

    this._createLightbox();

    this.items.forEach((item, i) => {
      item.addEventListener('click', () => this.open(i));
    });

    // Keyboard nav
    document.addEventListener('keydown', (e) => {
      if (!this.lightbox || !this.lightbox.classList.contains('active')) return;
      if (e.key === 'Escape') this.close();
      if (e.key === 'ArrowRight') this.next();
      if (e.key === 'ArrowLeft') this.prev();
    });
  },

  _createLightbox() {
    this.lightbox = document.createElement('div');
    this.lightbox.className = 'pm-lightbox';
    this.lightbox.setAttribute('role', 'dialog');
    this.lightbox.setAttribute('aria-label', 'Galería de imágenes');
    this.lightbox.innerHTML = `
      <button class="pm-lightbox-close" aria-label="Cerrar">
        <i class="bi bi-x-lg"></i>
      </button>
      <button class="pm-lightbox-nav pm-lightbox-prev" aria-label="Anterior">
        <i class="bi bi-chevron-left"></i>
      </button>
      <div class="pm-lightbox-content"></div>
      <button class="pm-lightbox-nav pm-lightbox-next" aria-label="Siguiente">
        <i class="bi bi-chevron-right"></i>
      </button>
      <div class="pm-lightbox-counter"></div>`;

    document.body.appendChild(this.lightbox);

    // Close on backdrop click
    this.lightbox.addEventListener('click', (e) => {
      if (e.target === this.lightbox) this.close();
    });

    this.lightbox.querySelector('.pm-lightbox-close').addEventListener('click', () => this.close());
    this.lightbox.querySelector('.pm-lightbox-prev').addEventListener('click', (e) => {
      e.stopPropagation();
      this.prev();
    });
    this.lightbox.querySelector('.pm-lightbox-next').addEventListener('click', (e) => {
      e.stopPropagation();
      this.next();
    });
  },

  open(index) {
    this.current = index;
    this._render();
    this.lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
  },

  close() {
    this.lightbox.classList.remove('active');
    document.body.style.overflow = '';
  },

  next() {
    this.current = (this.current + 1) % this.items.length;
    this._render();
  },

  prev() {
    this.current = (this.current - 1 + this.items.length) % this.items.length;
    this._render();
  },

  _render() {
    const item = this.items[this.current];
    const container = this.lightbox.querySelector('.pm-lightbox-content');
    const counter = this.lightbox.querySelector('.pm-lightbox-counter');

    // Check for real image
    const img = item.querySelector('img');
    if (img) {
      container.innerHTML = `<img src="${img.src}" alt="${img.alt || 'Galería Piel Morena'}" />`;
    } else {
      // Use the placeholder gradient
      const placeholder = item.querySelector('.pm-gallery-placeholder');
      const bg = placeholder ? placeholder.style.background || placeholder.style.backgroundImage : '';
      const h = placeholder ? placeholder.style.height : '400px';
      container.innerHTML = `<div class="pm-lightbox-placeholder" style="background: ${bg}; min-height: ${h};">
        <span>Piel Morena</span>
      </div>`;
    }

    counter.textContent = `${this.current + 1} / ${this.items.length}`;
  },
};


/* ═══════════════════════════════════════════════════════════
   TRATAMIENTOS — catálogo + modal enriquecido
   Fuente única: #pmTratamientosData (JSON embebido en index.php)
   ═══════════════════════════════════════════════════════════ */
const TreatmentsCatalog = {
  /** @type {HTMLElement|null} */
  section: null,
  /** @type {Map<string, any>} */
  items: new Map(),
  /** @type {string} */
  whatsappNumber: '',

  badgeClassByCategory: {
    Facial: 'pm-tratamiento-badge--facial',
    Mirada: 'pm-tratamiento-badge--mirada',
    Capilar: 'pm-tratamiento-badge--corporal',
    'Manicuría': 'pm-tratamiento-badge--relax',
  },

  modalClassByCategory: {
    Facial: 'pm-tratamiento-modal--facial',
    Mirada: 'pm-tratamiento-modal--mirada',
    Capilar: 'pm-tratamiento-modal--corporal',
    'Manicuría': 'pm-tratamiento-modal--relax',
  },

  init() {
    this.section = document.querySelector('.pm-tratamientos-section');
    const dataNode = document.getElementById('pmTratamientosData');

    if (!this.section || !dataNode || typeof Swal === 'undefined') return;

    this.whatsappNumber = this.section.dataset.whatsappNumber || '';

    try {
      const parsed = JSON.parse(dataNode.textContent || '[]');
      this.items = new Map(parsed.map(item => [item.slug, item]));
    } catch {
      this.items = new Map();
      return;
    }

    if (!this.items.size) return;

    this.section.addEventListener('click', (event) => {
      const trigger = event.target.closest('.js-pm-tratamiento-modal');
      if (!trigger) return;

      const treatmentId = trigger.dataset.tratamientoId;
      if (!treatmentId) return;

      const item = this.items.get(treatmentId);
      if (!item) return;

      this._openModal(item);
    });
  },

  _openModal(item) {
    Swal.fire({
      titleText: item.titulo,
      html: this._renderModalContent(item),
      showConfirmButton: false,
      showCloseButton: true,
      focusConfirm: false,
      customClass: {
        popup: 'pm-modal pm-modal--tratamiento',
        closeButton: 'pm-modal__close',
      },
    });
  },

  _renderModalContent(item) {
    const badgeClass = this.badgeClassByCategory[item.categoria] || '';
    const modalClass = this.modalClassByCategory[item.categoria] || '';
    const metaItems = Array.isArray(item.meta)
      ? item.meta
        .map(meta => `
          <span class="pm-tratamiento-modal__chip">
            ${this._escapeHtml(meta)}
          </span>
        `)
        .join('')
      : '';

    const whatsappHref = this._buildWhatsappHref(item.titulo);

    return `
      <div class="pm-tratamiento-modal ${modalClass}">
        <div class="pm-tratamiento-modal__header">
          <span class="pm-tratamiento-badge ${badgeClass}">
            ${this._escapeHtml(item.categoria)}
          </span>
          <div class="pm-tratamiento-modal__chips" aria-label="Datos rápidos">
            ${metaItems}
          </div>
        </div>

        <p class="pm-tratamiento-modal__lead">
          ${this._escapeHtml(item.descripcion)}
        </p>

        <div class="pm-tratamiento-modal__grid">
          <article class="pm-tratamiento-modal__item">
            <span class="pm-tratamiento-modal__label">Ideal para</span>
            <p class="pm-tratamiento-modal__value">${this._escapeHtml(item.ideal_para)}</p>
          </article>

          <article class="pm-tratamiento-modal__item">
            <span class="pm-tratamiento-modal__label">Beneficio principal</span>
            <p class="pm-tratamiento-modal__value">${this._escapeHtml(item.beneficio_principal)}</p>
          </article>

          <article class="pm-tratamiento-modal__item">
            <span class="pm-tratamiento-modal__label">Sensación esperada</span>
            <p class="pm-tratamiento-modal__value">${this._escapeHtml(item.sensacion)}</p>
          </article>

          <article class="pm-tratamiento-modal__item">
            <span class="pm-tratamiento-modal__label">Duración estimada</span>
            <p class="pm-tratamiento-modal__value">${this._escapeHtml(item.duracion)}</p>
          </article>
        </div>

        <div class="pm-tratamiento-modal__note">
          <span class="pm-tratamiento-modal__label">Recomendación orientativa</span>
          <p class="pm-tratamiento-modal__value">${this._escapeHtml(item.recomendacion)}</p>
        </div>

        <a
          href="${whatsappHref}"
          class="btn-pm-whatsapp btn-pm-sm pm-tratamiento-modal__cta"
          target="_blank"
          rel="noopener"
        >
          <i class="bi bi-whatsapp"></i>
          Consultar por WhatsApp
        </a>
      </div>
    `;
  },

  _buildWhatsappHref(title) {
    const message = encodeURIComponent(`Hola, quiero consultar por el tratamiento ${title}.`);
    return `https://wa.me/${this.whatsappNumber}?text=${message}`;
  },

  _escapeHtml(value) {
    return String(value ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  },
};


/* ═══════════════════════════════════════════════════════════
   MOBILE MENU (Bottom Sheet) — manejo correcto de nav + close
   Resuelve bug de Bootstrap data-bs-dismiss en <a> tags
   ═══════════════════════════════════════════════════════════ */
const MobileMenu = {
  /** @type {HTMLElement|null} */
  offcanvasEl: null,
  /** @type {bootstrap.Offcanvas|null} */
  bsOffcanvas: null,

  init() {
    this.offcanvasEl = document.getElementById('pmOffcanvas');
    if (!this.offcanvasEl) return;

    // Esperar a que Bootstrap inicialice el offcanvas
    this.offcanvasEl.addEventListener('shown.bs.offcanvas', () => {
      if (!this.bsOffcanvas) {
        this.bsOffcanvas = bootstrap.Offcanvas.getInstance(this.offcanvasEl);
      }
    });

    // Manejar clicks en nav links (.pm-sheet-nav) — scroll a sección
    this.offcanvasEl.addEventListener('click', (e) => {
      const navLink = e.target.closest('.pm-sheet-nav');
      if (navLink) {
        e.preventDefault();
        e.stopPropagation();
        const href = navLink.getAttribute('href');
        this._closeAndNavigate(href);
        return;
      }

      // Manejar clicks en action buttons (.pm-sheet-action) — navegar a URL
      const actionBtn = e.target.closest('.pm-sheet-action');
      if (actionBtn) {
        // Si es el botón de login, dejar que auth.js lo maneje
        if (actionBtn.classList.contains('pm-btn-login')) {
          this._close();
          return;
        }

        const href = actionBtn.getAttribute('href');
        if (href) {
          e.preventDefault();
          e.stopPropagation();
          this._close();
          // Esperar cierre de la animación antes de navegar
          setTimeout(() => { window.location.href = href; }, 280);
        }
        return;
      }
    });
  },

  /** Cierra el offcanvas y hace scroll suave a la sección */
  _closeAndNavigate(hash) {
    if (!hash || hash === '#') return;

    this._close();

    // Esperar a que la animación de cierre termine antes de scrollear
    setTimeout(() => {
      const target = document.querySelector(hash);
      if (target) {
        const offset = 80; // altura del navbar
        const top = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: 'smooth' });
      }
    }, 300);
  },

  /** Cierra el bottom sheet */
  _close() {
    if (!this.bsOffcanvas) {
      this.bsOffcanvas = bootstrap.Offcanvas.getInstance(this.offcanvasEl);
    }
    if (this.bsOffcanvas) {
      this.bsOffcanvas.hide();
    }
  },
};


/* ═══════════════════════════════════════════════════════════
   INICIALIZACION — DOMContentLoaded
   ═══════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  NavBar.init();
  ScrollAnimations.init();
  SmoothScroll.init();
  ContactForm.init();
  BackToTop.init();
  CarouselSync.init();
  GalleryLightbox.init();
  TreatmentsCatalog.init();
  MobileMenu.init();
});
