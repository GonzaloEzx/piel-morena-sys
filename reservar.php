<?php
/**
 * Piel Morena - Página de Reservas Online
 * Flujo: Servicio → Fecha → Hora → Datos → Confirmar
 */
$titulo_pagina = "Reservar Cita — Piel Morena Estética";
$meta_descripcion =
    "Reserva tu cita online en Piel Morena Estética. Elige servicio, fecha y horario.";
$pagina_actual = "reservar";
require_once "includes/header.php";

$servicio_preseleccionado = intval($_GET["servicio"] ?? 0);
$logueado = esta_autenticado();
?>

<!-- ═══ RESERVAS — Wizard paso a paso ═══ -->
<section class="pm-section pm-reservar-section">
  <div class="container" style="max-width: 720px;">

    <!-- Título -->
    <div class="text-center mb-4">
      <h1 class="pm-reservar-title">
        <i class="bi bi-calendar-check me-2"></i>Reservar Cita
      </h1>
      <p class="pm-reservar-subtitle">Elige tu servicio, fecha y horario en menos de 1 minuto</p>
      <div class="pm-divider"></div>
    </div>

    <?php if (!$logueado): ?>
    <!-- ═══ AVISO: Necesita cuenta para reservar ═══ -->
    <div class="pm-auth-required">
      <div class="pm-auth-required-icon">
        <i class="bi bi-calendar-heart"></i>
      </div>
      <h3>Necesitás una cuenta para reservar</h3>
      <p>Creá tu cuenta o iniciá sesión para poder reservar tus citas, ver tu historial y recibir recordatorios.</p>
      <ul class="pm-auth-required-benefits">
        <li><i class="bi bi-check-circle-fill"></i>Gestioná tus citas desde tu cuenta</li>
        <li><i class="bi bi-check-circle-fill"></i>Recibí recordatorios antes de tu turno</li>
        <li><i class="bi bi-check-circle-fill"></i>Accedé a tu historial de servicios</li>
        <li><i class="bi bi-check-circle-fill"></i>Reservá de forma rápida y sencilla</li>
      </ul>
      <div class="pm-auth-required-actions">
        <a href="<?= URL_BASE ?>/registro.php?redirect=<?= urlencode(
    "/reservar.php",
) ?>" class="btn btn-pm w-100">
          <i class="bi bi-person-plus me-2"></i>Crear cuenta
        </a>
        <div class="pm-auth-required-divider"><span>o</span></div>
        <a href="<?= URL_BASE ?>/login.php?redirect=<?= urlencode(
    "/reservar.php",
) ?>" class="btn btn-pm-outline w-100">
          <i class="bi bi-box-arrow-in-right me-2"></i>Ya tengo cuenta
        </a>
      </div>
    </div>
    <?php else: ?>

    <!-- Progress Steps -->
    <div class="pm-steps mb-4" id="progressSteps">
      <div class="pm-step active" data-step="1"><span class="pm-step-num">1</span><span class="pm-step-label">Servicio</span></div>
      <div class="pm-step-line"></div>
      <div class="pm-step" data-step="2"><span class="pm-step-num">2</span><span class="pm-step-label">Fecha</span></div>
      <div class="pm-step-line"></div>
      <div class="pm-step" data-step="3"><span class="pm-step-num">3</span><span class="pm-step-label">Hora</span></div>
      <div class="pm-step-line"></div>
      <div class="pm-step" data-step="4"><span class="pm-step-num">4</span><span class="pm-step-label">Confirmar</span></div>
    </div>

    <!-- ═══ PASO 1: Elegir servicio ═══ -->
    <div class="pm-wizard-step" id="step1">
      <h3 style="font-family: var(--pm-font-heading); color: var(--pm-tierra-dark);">
        <i class="bi bi-stars me-2"></i>Elige un servicio
      </h3>
      <div id="serviciosList" class="pm-servicios-lista mt-3">
        <div class="text-center py-4">
          <div class="spinner-border" style="color: var(--pm-bronce);" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ PASO 2: Elegir fecha ═══ -->
    <div class="pm-wizard-step d-none" id="step2">
      <h3 style="font-family: var(--pm-font-heading); color: var(--pm-tierra-dark);">
        <i class="bi bi-calendar3 me-2"></i>Elige una fecha
      </h3>
      <p id="step2Servicio" style="color: var(--pm-text-muted);"></p>

      <!-- Fecha libre (servicios sin jornada) -->
      <div class="mt-3" id="step2FechaLibre">
        <input type="date" id="fechaCita" class="input-pm w-100"
               min="<?= date("Y-m-d") ?>" max="<?= date(
    "Y-m-d",
    strtotime("+60 days"),
) ?>"
               value="<?= date("Y-m-d", strtotime("+1 day")) ?>">
      </div>

      <!-- Fechas de jornada (servicios con jornada) -->
      <div class="mt-3 d-none" id="step2Jornadas">
        <p class="pm-jornada-aviso">
          <i class="bi bi-info-circle me-1"></i>
          <span id="step2JornadaMsg">Este servicio tiene fechas específicas disponibles</span>
        </p>
        <div id="jornadasGrid" class="pm-jornadas-grid"></div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-pm-outline flex-fill" onclick="Wizard.goTo(1)">
          <i class="bi bi-arrow-left me-1"></i>Atrás
        </button>
        <button class="btn btn-pm flex-fill" id="btnVerTurnos" onclick="Wizard.selectFecha()">
          Ver turnos<i class="bi bi-arrow-right ms-1"></i>
        </button>
      </div>
    </div>

    <!-- ═══ PASO 3: Elegir hora ═══ -->
    <div class="pm-wizard-step d-none" id="step3">
      <h3 style="font-family: var(--pm-font-heading); color: var(--pm-tierra-dark);">
        <i class="bi bi-clock me-2"></i>Elige un horario
      </h3>
      <p id="step3Info" style="color: var(--pm-text-muted);"></p>
      <div id="turnosList" class="pm-turnos-grid mt-3"></div>
      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-pm-outline flex-fill" onclick="Wizard.goTo(2)">
          <i class="bi bi-arrow-left me-1"></i>Atrás
        </button>
      </div>
    </div>

    <!-- ═══ PASO 4: Datos + Confirmar ═══ -->
    <div class="pm-wizard-step d-none" id="step4">
      <h3 style="font-family: var(--pm-font-heading); color: var(--pm-tierra-dark);">
        <i class="bi bi-check-circle me-2"></i>Confirmar reserva
      </h3>

      <!-- Resumen -->
      <div class="pm-resumen-card mt-3">
        <div class="pm-resumen-row"><span>Servicio:</span><strong id="resServicio"></strong></div>
        <div class="pm-resumen-row"><span>Fecha:</span><strong id="resFecha"></strong></div>
        <div class="pm-resumen-row"><span>Hora:</span><strong id="resHora"></strong></div>
        <div class="pm-resumen-row"><span>Precio:</span><strong id="resPrecio" style="color: var(--pm-dorado);"></strong></div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-pm-outline flex-fill" onclick="Wizard.goTo(3)">
          <i class="bi bi-arrow-left me-1"></i>Atrás
        </button>
        <button class="btn btn-pm flex-fill" id="btnConfirmar" onclick="Wizard.confirmar()">
          <i class="bi bi-check-circle me-1"></i>Confirmar Reserva
        </button>
      </div>
    </div>

    <!-- ═══ ÉXITO ═══ -->
    <div class="pm-wizard-step d-none text-center" id="stepExito">
      <div style="font-size: 4rem; color: var(--pm-verde);">
        <i class="bi bi-check-circle-fill"></i>
      </div>
      <h2 style="font-family: var(--pm-font-heading); color: var(--pm-tierra);">¡Reserva confirmada!</h2>
      <p style="color: var(--pm-text-muted);" id="exitoMensaje"></p>
      <div class="pm-resumen-card mt-3" id="exitoResumen"></div>
      <p class="mt-3" style="font-size: 0.875rem; color: var(--pm-text-light);" id="exitoToken"></p>
      <div class="d-flex gap-3 mt-4 justify-content-center">
        <a href="<?= URL_BASE ?>/mi-cuenta.php" class="btn btn-pm">
          <i class="bi bi-person me-1"></i>Ver mis citas
        </a>
        <a href="<?= URL_BASE ?>/" class="btn btn-pm-outline">
          <i class="bi bi-house me-1"></i>Volver al inicio
        </a>
      </div>
    </div>

    <?php endif;
/* fin del check $logueado */
?>

  </div>
</section>

<?php if ($logueado): ?>
<!-- ═══ Script del wizard ═══ -->
<script>
const DIAS_ES = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
const DIAS_CORTOS = ['dom','lun','mar','mié','jue','vie','sáb'];
const MESES_ES = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

function formatearFechaAR(fechaISO, opciones = {}) {
    const [y, m, d] = fechaISO.split('-').map(Number);
    const date = new Date(y, m - 1, d);
    const dia = String(d).padStart(2, '0');
    const mes = String(m).padStart(2, '0');
    const diaSemana = DIAS_ES[date.getDay()];
    const diaSemanaCorto = DIAS_CORTOS[date.getDay()];
    const mesNombre = MESES_ES[m - 1];

    if (opciones.largo) {
        return `${diaSemana} ${d} de ${mesNombre} de ${y}`;
    }
    if (opciones.medio) {
        return `${diaSemanaCorto} ${dia}/${mes}/${y}`;
    }
    return `${dia}/${mes}/${y}`;
}

const Wizard = {
  state: { servicio: null, fecha: '', hora: '', turnoFin: '', precio: '', servicioNombre: '', requiereJornada: false },
  logueado: <?= $logueado ? "true" : "false" ?>,
  preseleccionado: <?= $servicio_preseleccionado ?>,

  async init() {
    await this.loadServicios();
    if (this.preseleccionado) {
      const opt = document.querySelector(`.pm-servicio-option[data-id="${this.preseleccionado}"]`);
      if (opt) opt.click();
    }
  },

  async loadServicios() {
    try {
      const r = await fetch('api/servicios/listar.php');
      const data = await r.json();
      if (!data.success) throw new Error(data.error);

      const container = document.getElementById('serviciosList');
      if (!data.data.length) {
        container.innerHTML = '<p class="text-center" style="color:var(--pm-text-muted)">No hay servicios disponibles</p>';
        return;
      }

      // Agrupar servicios por categoría
      const categorias = {};
      data.data.forEach(s => {
        const cat = s.categoria || 'Packs';
        if (!categorias[cat]) {
          categorias[cat] = { icono: s.categoria_icono || 'bi-stars', servicios: [] };
        }
        categorias[cat].servicios.push(s);
      });

      let html = '<div class="accordion pm-categorias-accordion" id="accordionServicios">';
      let idx = 0;
      for (const [catName, catData] of Object.entries(categorias)) {
        const collapseId = 'cat-' + idx;
        html += `
        <div class="accordion-item pm-cat-item">
          <h2 class="accordion-header">
            <button class="accordion-button pm-cat-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}">
              <i class="bi ${catData.icono} me-2"></i>${catName}
            </button>
          </h2>
          <div id="${collapseId}" class="accordion-collapse collapse" data-bs-parent="#accordionServicios">
            <div class="accordion-body p-0">`;
        catData.servicios.forEach(s => {
          const precio = parseFloat(s.precio) > 0
            ? '$' + parseFloat(s.precio).toLocaleString('es-AR', {minimumFractionDigits:2})
            : 'Consultar';
          html += `<div class="pm-servicio-option" data-id="${s.id}" data-name="${s.nombre}" data-price="${s.precio}" data-duration="${s.duracion_minutos}" onclick="Wizard.selectServicio(this)">
            <div class="pm-so-info">
              <div class="pm-so-name">${s.nombre}</div>
              <div class="pm-so-meta"><i class="bi bi-clock me-1"></i>${s.duracion_minutos} min</div>
            </div>
            <div class="pm-so-price">${precio}</div>
          </div>`;
        });
        html += `</div></div></div>`;
        idx++;
      }
      html += '</div>';
      container.innerHTML = html;

      // Si hay preselección, abrir la categoría correspondiente
      if (this.preseleccionado) {
        const opt = container.querySelector(`.pm-servicio-option[data-id="${this.preseleccionado}"]`);
        if (opt) {
          const collapse = opt.closest('.accordion-collapse');
          if (collapse) new bootstrap.Collapse(collapse, { show: true });
        }
      }
    } catch(e) {
      document.getElementById('serviciosList').innerHTML = '<p class="text-center" style="color:var(--pm-rojo)">Error al cargar servicios</p>';
    }
  },

  async selectServicio(el) {
    document.querySelectorAll('.pm-servicio-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    this.state.servicio = el.dataset.id;
    this.state.servicioNombre = el.dataset.name;
    this.state.precio = el.dataset.price;
    this.state.requiereJornada = false;
    document.getElementById('step2Servicio').textContent = el.dataset.name + ' · ' + el.dataset.duration + ' min';

    // Chequear si el servicio requiere jornada
    try {
      const r = await fetch('api/jornadas/disponibles.php?id_servicio=' + el.dataset.id);
      const data = await r.json();
      if (data.success && data.data.requiere_jornada) {
        this.state.requiereJornada = true;
        this.renderJornadas(data.data);
      }
    } catch(e) { /* falla silenciosa, usa fecha libre */ }

    // Mostrar/ocultar UI según tipo
    const fechaLibre = document.getElementById('step2FechaLibre');
    const jornadas   = document.getElementById('step2Jornadas');
    const btnTurnos  = document.getElementById('btnVerTurnos');
    if (this.state.requiereJornada) {
      fechaLibre.classList.add('d-none');
      jornadas.classList.remove('d-none');
      btnTurnos.classList.add('d-none');
    } else {
      fechaLibre.classList.remove('d-none');
      jornadas.classList.add('d-none');
      btnTurnos.classList.remove('d-none');
    }

    this.goTo(2);
  },

  renderJornadas(data) {
    const grid = document.getElementById('jornadasGrid');
    document.getElementById('step2JornadaMsg').textContent =
      data.fechas.length > 0
        ? `Fechas disponibles para ${data.categoria}`
        : `No hay fechas programadas para ${data.categoria} próximamente`;

    if (!data.fechas.length) {
      grid.innerHTML = '<div class="pm-no-turnos"><i class="bi bi-calendar-x"></i>No hay jornadas programadas<br><small>Consultá por WhatsApp para más información</small></div>';
      return;
    }

    grid.innerHTML = data.fechas.map(f => `
      <button class="pm-jornada-card" onclick="Wizard.selectJornadaFecha(this, '${f.fecha}')">
        <span class="pm-jornada-dia">${f.dia_semana}</span>
        <span class="pm-jornada-num">${f.dia_numero}</span>
        <span class="pm-jornada-mes">${f.mes}</span>
      </button>
    `).join('');
  },

  selectJornadaFecha(el, fecha) {
    document.querySelectorAll('.pm-jornada-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    this.state.fecha = fecha;
    this.loadTurnos();
  },

  selectFecha() {
    const fecha = document.getElementById('fechaCita').value;
    if (!fecha) { Swal.fire({icon:'warning',text:'Selecciona una fecha',customClass:{popup:'pm-modal',confirmButton:'btn-pm'}}); return; }
    this.state.fecha = fecha;
    this.loadTurnos();
  },

  async loadTurnos() {
    const container = document.getElementById('turnosList');
    container.innerHTML = '<div class="text-center py-3"><div class="spinner-border" style="color:var(--pm-bronce)" role="status"></div></div>';
    this.goTo(3);

    const fechaTexto = formatearFechaAR(this.state.fecha, { largo: true });
    document.getElementById('step3Info').textContent = this.state.servicioNombre + ' — ' + fechaTexto;

    try {
      const r = await fetch(`api/citas/disponibilidad.php?fecha=${this.state.fecha}&id_servicio=${this.state.servicio}`);
      const data = await r.json();
      if (!data.success) throw new Error(data.error);

      if (!data.data.turnos.length) {
        container.innerHTML = `<div class="pm-no-turnos"><i class="bi bi-calendar-x"></i>${data.data.mensaje || 'No hay turnos disponibles para esta fecha'}<br><small>Probá otro día</small></div>`;
        return;
      }

      container.innerHTML = data.data.turnos.map(t =>
        `<button class="pm-turno-btn" onclick="Wizard.selectTurno(this,'${t.hora_inicio}','${t.hora_fin}')">${t.hora_inicio}</button>`
      ).join('');
    } catch(e) {
      container.innerHTML = '<p class="text-center" style="color:var(--pm-rojo)">Error al cargar turnos</p>';
    }
  },

  selectTurno(el, inicio, fin) {
    document.querySelectorAll('.pm-turno-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    this.state.hora = inicio;
    this.state.turnoFin = fin;

    // Llenar resumen
    document.getElementById('resServicio').textContent = this.state.servicioNombre;
    document.getElementById('resFecha').textContent = formatearFechaAR(this.state.fecha, { largo: true });
    document.getElementById('resHora').textContent = inicio + ' - ' + fin;
    document.getElementById('resPrecio').textContent = parseFloat(this.state.precio) > 0
      ? '$' + parseFloat(this.state.precio).toLocaleString('es-AR', {minimumFractionDigits:2})
      : 'Consultar';

    this.goTo(4);
  },

  async confirmar() {
    const btn = document.getElementById('btnConfirmar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Reservando...';

    const body = {
      id_servicio: this.state.servicio,
      fecha: this.state.fecha,
      hora_inicio: this.state.hora,
    };

    // Usuario siempre autenticado — no se requieren datos adicionales

    try {
      const r = await fetch('api/citas/crear.php', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(body) });
      const data = await r.json();

      if (!data.success) {
        Swal.fire({icon:'error',title:'Error',text:data.error,customClass:{popup:'pm-modal',confirmButton:'btn-pm'}});
        btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Confirmar Reserva'; return;
      }

      // Éxito
      document.getElementById('exitoMensaje').textContent = 'Tu cita ha sido reservada exitosamente. Te esperamos.';
      document.getElementById('exitoResumen').innerHTML = `
        <div class="pm-resumen-row"><span>Servicio:</span><strong>${data.data.servicio}</strong></div>
        <div class="pm-resumen-row"><span>Fecha:</span><strong>${formatearFechaAR(this.state.fecha, { largo: true })}</strong></div>
        <div class="pm-resumen-row"><span>Hora:</span><strong>${data.data.hora}</strong></div>
        <div class="pm-resumen-row"><span>N° Reserva:</span><strong>#${data.data.id}</strong></div>
      `;
      // Token informativo (uso interno)
      if (data.data.token) {
        document.getElementById('exitoToken').textContent = 'Referencia: #' + data.data.id;
      }
      this.goTo('exito');
    } catch(e) {
      Swal.fire({icon:'error',title:'Error de conexión',text:'Intenta de nuevo más tarde',customClass:{popup:'pm-modal',confirmButton:'btn-pm'}});
      btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Confirmar Reserva';
    }
  },

  goTo(step) {
    document.querySelectorAll('.pm-wizard-step').forEach(s => s.classList.add('d-none'));
    if (step === 'exito') {
      document.getElementById('stepExito').classList.remove('d-none');
      document.getElementById('progressSteps').style.display = 'none';
      return;
    }
    document.getElementById('step' + step).classList.remove('d-none');
    // Actualizar progress
    document.querySelectorAll('.pm-step').forEach(s => {
      const n = parseInt(s.dataset.step);
      s.classList.toggle('active', n === step);
      s.classList.toggle('done', n < step);
    });
    window.scrollTo({top: 0, behavior: 'smooth'});
  }
};

document.addEventListener('DOMContentLoaded', () => Wizard.init());
</script>
<?php endif; ?>

<?php require_once "includes/footer.php"; ?>
