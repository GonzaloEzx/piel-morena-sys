<?php
/**
 * Piel Morena - Reserva online (v2.0)
 * Flujo: Servicio → Profesional, día y horario → Tus datos
 *
 * No exige cuenta: la invitada deja nombre y WhatsApp. Con sesión, se usan
 * los datos de la cuenta. La reserva queda pendiente hasta que el salón la
 * confirma por WhatsApp.
 *
 * Preselección por URL: ?servicio=<id>&profesional=<id>&fecha=<Y-m-d>
 */
$titulo_pagina = "Reservar turno — Piel Morena Estética";
$meta_descripcion =
    "Reservá tu turno online en Piel Morena Estética: elegí servicio, profesional y horario. Te confirmamos por WhatsApp.";
$pagina_actual = "reservar";
require_once "includes/header.php";

$logueado = esta_autenticado();
$preseleccion = [
    'servicio'    => intval($_GET["servicio"] ?? 0),
    'profesional' => intval($_GET["profesional"] ?? 0),
    'fecha'       => preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET["fecha"] ?? '') ? $_GET["fecha"] : '',
];

$stmt = getDB()->prepare("SELECT valor FROM configuracion WHERE clave = 'dias_laborales'");
$stmt->execute();
$dias_laborales = array_map('intval', explode(',', $stmt->fetchColumn() ?: '1,2,3,4,5'));
?>

<section class="pm-section pm-reservar-section">
  <div class="container" style="max-width: 720px;">

    <div class="text-center mb-4">
      <h1 class="pm-reservar-title">
        <i class="bi bi-calendar-check me-2"></i>Reservar turno
      </h1>
      <p class="pm-reservar-subtitle">Elegí servicio, profesional y horario. Sin crear cuenta.</p>
      <div class="pm-divider"></div>
    </div>

    <div class="pm-steps mb-4" id="progressSteps">
      <div class="pm-step active" data-step="1"><span class="pm-step-num">1</span><span class="pm-step-label">Servicio</span></div>
      <div class="pm-step-line"></div>
      <div class="pm-step" data-step="2"><span class="pm-step-num">2</span><span class="pm-step-label">Día y hora</span></div>
      <div class="pm-step-line"></div>
      <div class="pm-step" data-step="3"><span class="pm-step-num">3</span><span class="pm-step-label">Tus datos</span></div>
    </div>

    <!-- ═══ PASO 1: Servicio ═══ -->
    <div class="pm-wizard-step" id="step1">
      <h2 class="pm-wizard-titulo"><i class="bi bi-stars me-2"></i>¿Qué te querés hacer?</h2>
      <div id="serviciosList" class="pm-servicios-lista mt-3">
        <div class="text-center py-4">
          <div class="spinner-border" style="color: var(--pm-bronce);" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ PASO 2: Profesional, día y horario ═══ -->
    <div class="pm-wizard-step d-none" id="step2">
      <h2 class="pm-wizard-titulo"><i class="bi bi-calendar3 me-2"></i>Elegí día y horario</h2>
      <p id="step2Servicio" class="pm-wizard-sub"></p>

      <div id="bloqueProfesional" class="d-none">
        <p class="pm-reserva-etiqueta">Profesional</p>
        <div class="pm-chips" id="profesionalesList" role="radiogroup" aria-label="Profesional"></div>
      </div>

      <p class="pm-reserva-etiqueta mt-3">Día</p>
      <!-- Días libres (servicios sin máquina ni jornada) -->
      <div id="step2FechaLibre">
        <div class="pm-dias-scroll" id="diasList"></div>
        <label class="pm-otra-fecha mt-2">
          <span>Otra fecha</span>
          <input type="date" id="fechaCita" class="input-pm"
                 min="<?= date("Y-m-d") ?>" max="<?= date("Y-m-d", strtotime("+60 days")) ?>">
        </label>
      </div>

      <!-- Fechas de jornada o alquiler de máquina -->
      <div class="d-none" id="step2Jornadas">
        <p class="pm-jornada-aviso">
          <i class="bi bi-info-circle me-1"></i>
          <span id="step2JornadaMsg">Este servicio tiene fechas específicas disponibles</span>
        </p>
        <div id="jornadasGrid" class="pm-jornadas-grid"></div>
      </div>

      <div id="bloqueTurnos" class="d-none">
        <p class="pm-reserva-etiqueta mt-3" id="step2Info">Horario</p>
        <div id="turnosList" class="pm-turnos-grid"></div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="button" class="btn btn-pm-outline flex-fill" onclick="Wizard.goTo(1)">
          <i class="bi bi-arrow-left me-1"></i>Cambiar servicio
        </button>
      </div>
    </div>

    <!-- ═══ PASO 3: Datos y confirmar ═══ -->
    <div class="pm-wizard-step d-none" id="step3">
      <h2 class="pm-wizard-titulo"><i class="bi bi-person-check me-2"></i>Confirmá tu turno</h2>

      <div class="pm-resumen-card mt-3">
        <div class="pm-resumen-row"><span>Servicio</span><strong id="resServicio"></strong></div>
        <div class="pm-resumen-row"><span>Profesional</span><strong id="resProfesional"></strong></div>
        <div class="pm-resumen-row"><span>Día</span><strong id="resFecha"></strong></div>
        <div class="pm-resumen-row"><span>Horario</span><strong id="resHora"></strong></div>
        <div class="pm-resumen-row"><span>Precio</span><strong id="resPrecio"></strong></div>
      </div>

      <form id="formDatos" class="mt-4" novalidate onsubmit="event.preventDefault(); Wizard.confirmar();">
        <?php if ($logueado): ?>
          <p class="pm-wizard-sub">Reservás como <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?></strong>.</p>
        <?php else: ?>
          <div class="mb-3">
            <label for="datoNombre" class="pm-reserva-etiqueta">Nombre y apellido</label>
            <input type="text" id="datoNombre" class="input-pm w-100" autocomplete="name" required maxlength="100">
          </div>
          <div class="mb-3">
            <label for="datoTelefono" class="pm-reserva-etiqueta">WhatsApp</label>
            <input type="tel" id="datoTelefono" class="input-pm w-100" autocomplete="tel" inputmode="tel"
                   placeholder="3624 123456" required maxlength="20">
            <small class="pm-wizard-ayuda">Por acá te confirmamos el turno.</small>
          </div>
          <div class="mb-3">
            <label for="datoEmail" class="pm-reserva-etiqueta">Email <span class="pm-opcional">(opcional)</span></label>
            <input type="email" id="datoEmail" class="input-pm w-100" autocomplete="email" maxlength="150">
          </div>
          <div class="pm-campo-trampa" aria-hidden="true">
            <label for="datoSitio">Sitio web</label>
            <input type="text" id="datoSitio" tabindex="-1" autocomplete="off">
          </div>
          <p class="pm-wizard-ayuda">¿Ya tenés cuenta? <a href="<?= URL_BASE ?>/login.php?redirect=<?= urlencode("/reservar.php") ?>">Ingresá</a> para ver tu historial.</p>
        <?php endif; ?>

        <div class="d-flex gap-2 mt-4">
          <button type="button" class="btn btn-pm-outline flex-fill" onclick="Wizard.goTo(2)">
            <i class="bi bi-arrow-left me-1"></i>Atrás
          </button>
          <button type="submit" class="btn btn-pm flex-fill" id="btnConfirmar">
            <i class="bi bi-check-circle me-1"></i>Reservar turno
          </button>
        </div>
      </form>
    </div>

    <!-- ═══ ÉXITO ═══ -->
    <div class="pm-wizard-step d-none text-center" id="stepExito">
      <div style="font-size: 3.5rem; color: var(--pm-verde);"><i class="bi bi-check-circle-fill"></i></div>
      <h2 class="pm-wizard-titulo">¡Recibimos tu reserva!</h2>
      <p class="pm-wizard-sub">Queda <strong>pendiente</strong> hasta que te confirmemos por WhatsApp.</p>
      <div class="pm-resumen-card mt-3 text-start" id="exitoResumen"></div>
      <div class="pm-exito-acciones mt-4">
        <a href="#" class="btn btn-pm" id="exitoWhatsapp" target="_blank" rel="noopener">
          <i class="bi bi-whatsapp me-1"></i>Avisar por WhatsApp
        </a>
        <a href="#" class="btn btn-pm-outline" id="exitoGestion">
          <i class="bi bi-calendar2-week me-1"></i>Ver o cambiar mi turno
        </a>
      </div>
      <p class="pm-wizard-ayuda mt-3">Guardá el link de tu turno: desde ahí podés cancelarlo o cambiarlo hasta 2 horas antes.</p>
    </div>

  </div>
</section>

<script>
const DIAS_ES = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
const DIAS_CORTOS = ['dom','lun','mar','mié','jue','vie','sáb'];
const MESES_ES = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
const MESES_CORTOS = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
const TELEFONO_SALON = <?= json_encode(preg_replace('/\D/', '', TELEFONO_NEGOCIO)) ?>;
const DIAS_LABORALES = <?= json_encode($dias_laborales) ?>; // 1=lun ... 7=dom

function formatearFechaAR(fechaISO, opciones = {}) {
  const [y, m, d] = fechaISO.split('-').map(Number);
  const date = new Date(y, m - 1, d);
  if (opciones.largo) return `${DIAS_ES[date.getDay()]} ${d} de ${MESES_ES[m - 1]}`;
  return `${String(d).padStart(2, '0')}/${String(m).padStart(2, '0')}/${y}`;
}

function isoLocal(date) {
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
}

function esc(texto) {
  const div = document.createElement('div');
  div.textContent = texto ?? '';
  return div.innerHTML;
}

function precioTexto(precio) {
  return parseFloat(precio) > 0
    ? '$' + parseFloat(precio).toLocaleString('es-AR', { maximumFractionDigits: 0 })
    : 'A consultar';
}

const Wizard = {
  state: { servicio: null, servicioNombre: '', precio: '', duracion: 0, profesional: 0, profesionales: [], fecha: '', hora: '', turnoFin: '', fechasFijas: false },
  logueado: <?= $logueado ? 'true' : 'false' ?>,
  pre: <?= json_encode($preseleccion) ?>,

  async init() {
    await this.loadServicios();
    if (this.pre.servicio) {
      const opt = document.querySelector(`.pm-servicio-option[data-id="${this.pre.servicio}"]`);
      if (opt) opt.click();
    }
    document.getElementById('fechaCita').addEventListener('change', e => {
      if (!e.target.value) return;
      document.querySelectorAll('#diasList .pm-jornada-card').forEach(c => c.classList.remove('selected'));
      this.elegirFecha(e.target.value);
    });
  },

  async loadServicios() {
    const container = document.getElementById('serviciosList');
    try {
      const r = await fetch('api/servicios/listar.php');
      const data = await r.json();
      if (!data.success) throw new Error(data.error);
      if (!data.data.length) {
        container.innerHTML = '<p class="text-center" style="color:var(--pm-text-muted)">No hay servicios disponibles</p>';
        return;
      }

      const categorias = {};
      data.data.forEach(s => {
        const cat = s.categoria || 'Packs';
        if (!categorias[cat]) categorias[cat] = { icono: s.categoria_icono || 'bi-stars', servicios: [] };
        categorias[cat].servicios.push(s);
      });

      let html = '<div class="accordion pm-categorias-accordion" id="accordionServicios">';
      Object.entries(categorias).forEach(([catName, catData], idx) => {
        html += `
        <div class="accordion-item pm-cat-item">
          <h3 class="accordion-header">
            <button class="accordion-button pm-cat-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cat-${idx}">
              <i class="bi ${esc(catData.icono)} me-2"></i>${esc(catName)}
            </button>
          </h3>
          <div id="cat-${idx}" class="accordion-collapse collapse" data-bs-parent="#accordionServicios">
            <div class="accordion-body p-0">`;
        catData.servicios.forEach(s => {
          html += `<button type="button" class="pm-servicio-option" data-id="${s.id}" data-name="${esc(s.nombre)}" data-price="${s.precio}" data-duration="${s.duracion_minutos}" onclick="Wizard.selectServicio(this)">
            <span class="pm-so-info">
              <span class="pm-so-name">${esc(s.nombre)}</span>
              <span class="pm-so-meta"><i class="bi bi-clock me-1"></i>${s.duracion_minutos} min</span>
            </span>
            <span class="pm-so-price">${precioTexto(s.precio)}</span>
          </button>`;
        });
        html += '</div></div></div>';
      });
      container.innerHTML = html + '</div>';

      if (this.pre.servicio) {
        const opt = container.querySelector(`.pm-servicio-option[data-id="${this.pre.servicio}"]`);
        const collapse = opt && opt.closest('.accordion-collapse');
        if (collapse) new bootstrap.Collapse(collapse, { show: true });
      }
    } catch (e) {
      container.innerHTML = '<p class="text-center" style="color:var(--pm-rojo)">No pudimos cargar los servicios. Probá de nuevo o escribinos por WhatsApp.</p>';
    }
  },

  async selectServicio(el) {
    document.querySelectorAll('.pm-servicio-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    Object.assign(this.state, {
      servicio: el.dataset.id, servicioNombre: el.dataset.name, precio: el.dataset.price,
      duracion: el.dataset.duration, fecha: '', hora: '', fechasFijas: false,
    });
    document.getElementById('step2Servicio').textContent = `${el.dataset.name} · ${el.dataset.duration} min · ${precioTexto(el.dataset.price)}`;
    document.getElementById('bloqueTurnos').classList.add('d-none');

    const [pros, maquina, jornada] = await Promise.all([
      fetch('api/citas/profesionales.php?id_servicio=' + el.dataset.id).then(r => r.json()).catch(() => null),
      fetch('api/maquinas/disponibles.php?id_servicio=' + el.dataset.id).then(r => r.json()).catch(() => null),
      fetch('api/jornadas/disponibles.php?id_servicio=' + el.dataset.id).then(r => r.json()).catch(() => null),
    ]);

    this.renderProfesionales(pros && pros.success ? pros.data : []);

    if (maquina && maquina.success && maquina.data.requiere_maquina) {
      this.state.fechasFijas = true;
      this.renderFechasFijas(maquina.data, 'maquina');
    } else if (jornada && jornada.success && jornada.data.requiere_jornada) {
      this.state.fechasFijas = true;
      this.renderFechasFijas(jornada.data, 'jornada');
    } else {
      this.renderDias();
    }
    document.getElementById('step2FechaLibre').classList.toggle('d-none', this.state.fechasFijas);
    document.getElementById('step2Jornadas').classList.toggle('d-none', !this.state.fechasFijas);

    this.goTo(2);

    const preFecha = this.pre.fecha;
    this.pre.fecha = '';
    if (preFecha) {
      const card = document.querySelector(`#step2 .pm-jornada-card[data-fecha="${preFecha}"]`);
      if (card) card.click();
      else if (!this.state.fechasFijas) { document.getElementById('fechaCita').value = preFecha; this.elegirFecha(preFecha); }
    }
  },

  renderProfesionales(lista) {
    this.state.profesionales = lista;
    const preId = this.pre.profesional;
    this.pre.profesional = 0;
    this.state.profesional = lista.some(p => p.id === preId) ? preId : 0;

    const bloque = document.getElementById('bloqueProfesional');
    if (lista.length < 2) {
      bloque.classList.add('d-none');
      this.state.profesional = 0;
      return;
    }
    bloque.classList.remove('d-none');
    const chip = (id, nombre) => `<button type="button" class="pm-chip${this.state.profesional === id ? ' selected' : ''}" role="radio"
        aria-checked="${this.state.profesional === id}" data-id="${id}" onclick="Wizard.selectProfesional(${id})">${esc(nombre)}</button>`;
    document.getElementById('profesionalesList').innerHTML =
      chip(0, 'Cualquiera') + lista.map(p => chip(p.id, p.nombre.split(' ')[0])).join('');
  },

  selectProfesional(id) {
    this.state.profesional = id;
    document.querySelectorAll('#profesionalesList .pm-chip').forEach(c => {
      const activo = Number(c.dataset.id) === id;
      c.classList.toggle('selected', activo);
      c.setAttribute('aria-checked', activo);
    });
    if (this.state.fecha) this.loadTurnos();
  },

  renderDias() {
    const hoy = new Date();
    let html = '';
    for (let i = 0; i < 14; i++) {
      const d = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate() + i);
      const iso = isoLocal(d);
      const laboral = DIAS_LABORALES.includes(d.getDay() === 0 ? 7 : d.getDay());
      html += `<button type="button" class="pm-jornada-card" data-fecha="${iso}" ${laboral ? '' : 'disabled aria-disabled="true"'}
          onclick="Wizard.selectDia(this)" aria-label="${formatearFechaAR(iso, { largo: true })}">
        <span class="pm-jornada-dia">${i === 0 ? 'hoy' : i === 1 ? 'mañana' : DIAS_CORTOS[d.getDay()]}</span>
        <span class="pm-jornada-num">${d.getDate()}</span>
        <span class="pm-jornada-mes">${MESES_CORTOS[d.getMonth()]}</span>
      </button>`;
    }
    document.getElementById('diasList').innerHTML = html;
    document.getElementById('fechaCita').value = '';
  },

  selectDia(el) {
    document.querySelectorAll('#step2 .pm-jornada-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('fechaCita').value = '';
    this.elegirFecha(el.dataset.fecha);
  },

  renderFechasFijas(data, tipo) {
    const grid = document.getElementById('jornadasGrid');
    const nombre = tipo === 'maquina' ? (data.maquina || 'el equipo') : (data.categoria || 'este servicio');
    document.getElementById('step2JornadaMsg').textContent = data.fechas.length
      ? (tipo === 'maquina' ? `Este servicio se hace con ${nombre}, disponible estos días:` : `Fechas disponibles para ${nombre}:`)
      : `No hay fechas programadas para ${nombre} por ahora.`;

    if (!data.fechas.length) {
      grid.innerHTML = `<div class="pm-no-turnos"><i class="bi bi-calendar-x"></i>Sin fechas disponibles<br>
        <a href="https://wa.me/${TELEFONO_SALON}?text=${encodeURIComponent('Hola! Quería consultar fechas para ' + this.state.servicioNombre)}" target="_blank" rel="noopener">Consultá por WhatsApp</a></div>`;
      return;
    }
    grid.innerHTML = data.fechas.map(f => `
      <button type="button" class="pm-jornada-card" data-fecha="${f.fecha}" onclick="Wizard.selectDia(this)">
        <span class="pm-jornada-dia">${esc(f.dia_semana)}</span>
        <span class="pm-jornada-num">${esc(String(f.dia_numero))}</span>
        <span class="pm-jornada-mes">${esc(f.mes)}</span>
      </button>`).join('');
  },

  elegirFecha(fecha) {
    this.state.fecha = fecha;
    this.loadTurnos();
  },

  async loadTurnos() {
    const bloque = document.getElementById('bloqueTurnos');
    const container = document.getElementById('turnosList');
    bloque.classList.remove('d-none');
    container.innerHTML = '<div class="text-center py-3"><div class="spinner-border" style="color:var(--pm-bronce)" role="status"></div></div>';
    document.getElementById('step2Info').textContent = 'Horarios del ' + formatearFechaAR(this.state.fecha, { largo: true });

    const params = new URLSearchParams({ fecha: this.state.fecha, id_servicio: this.state.servicio });
    if (this.state.profesional) params.set('id_empleado', this.state.profesional);

    try {
      const r = await fetch('api/citas/disponibilidad.php?' + params);
      const data = await r.json();
      if (!data.success) throw new Error(data.error);

      if (!data.data.turnos.length) {
        const quien = this.state.profesional ? ' con esa profesional' : '';
        container.innerHTML = `<div class="pm-no-turnos"><i class="bi bi-calendar-x"></i>${esc(data.data.mensaje || 'No quedan horarios' + quien + ' este día')}<br><small>Probá otro día${quien ? ' o elegí "Cualquiera"' : ''}</small></div>`;
        return;
      }
      container.innerHTML = data.data.turnos.map(t =>
        `<button type="button" class="pm-turno-btn" onclick="Wizard.selectTurno(this,'${t.hora_inicio}','${t.hora_fin}')">${t.hora_inicio}</button>`
      ).join('');
      bloque.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } catch (e) {
      container.innerHTML = '<p class="text-center" style="color:var(--pm-rojo)">No pudimos cargar los horarios. Probá de nuevo.</p>';
    }
  },

  nombreProfesional() {
    const p = this.state.profesionales.find(p => p.id === this.state.profesional);
    return p ? p.nombre : 'La primera disponible';
  },

  selectTurno(el, inicio, fin) {
    document.querySelectorAll('.pm-turno-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    this.state.hora = inicio;
    this.state.turnoFin = fin;

    document.getElementById('resServicio').textContent = this.state.servicioNombre;
    document.getElementById('resProfesional').textContent = this.nombreProfesional();
    document.getElementById('resProfesional').parentElement.classList.toggle('d-none', !this.state.profesionales.length);
    document.getElementById('resFecha').textContent = formatearFechaAR(this.state.fecha, { largo: true });
    document.getElementById('resHora').textContent = `${inicio} a ${fin}`;
    document.getElementById('resPrecio').textContent = precioTexto(this.state.precio);
    this.goTo(3);
  },

  async confirmar() {
    const btn = document.getElementById('btnConfirmar');
    const body = {
      id_servicio: this.state.servicio,
      fecha: this.state.fecha,
      hora_inicio: this.state.hora,
      id_empleado: this.state.profesional || null,
    };
    if (!this.logueado) {
      body.nombre = document.getElementById('datoNombre').value.trim();
      body.telefono = document.getElementById('datoTelefono').value.trim();
      body.email = document.getElementById('datoEmail').value.trim();
      body.sitio = document.getElementById('datoSitio').value;
      if (body.nombre.length < 2) return this.aviso('Contanos tu nombre', 'datoNombre');
      if (body.telefono.replace(/\D/g, '').length < 10) return this.aviso('Ingresá tu WhatsApp con código de área (ej: 3624 123456)', 'datoTelefono');
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Reservando...';
    const restaurar = () => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Reservar turno'; };

    try {
      const r = await fetch('api/citas/crear.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) });
      const data = await r.json();
      if (!data.success) {
        restaurar();
        if (r.status === 409) {
          await this.aviso(data.error);
          this.goTo(2);
          this.loadTurnos();
          return;
        }
        return this.aviso(data.error);
      }

      const d = data.data;
      const fechaTexto = formatearFechaAR(this.state.fecha, { largo: true });
      document.getElementById('exitoResumen').innerHTML = `
        <div class="pm-resumen-row"><span>Servicio</span><strong>${esc(d.servicio)}</strong></div>
        ${d.profesional ? `<div class="pm-resumen-row"><span>Profesional</span><strong>${esc(d.profesional)}</strong></div>` : ''}
        <div class="pm-resumen-row"><span>Día</span><strong>${fechaTexto}</strong></div>
        <div class="pm-resumen-row"><span>Horario</span><strong>${esc(d.hora)}</strong></div>
        <div class="pm-resumen-row"><span>Reserva</span><strong>#${d.id}</strong></div>`;
      const mensaje = `Hola! Reservé ${d.servicio} para el ${fechaTexto} a las ${d.hora} (reserva #${d.id}).`;
      document.getElementById('exitoWhatsapp').href = `https://wa.me/${TELEFONO_SALON}?text=${encodeURIComponent(mensaje)}`;
      document.getElementById('exitoGestion').href = d.link_gestion;
      this.goTo('exito');
    } catch (e) {
      restaurar();
      this.aviso('Error de conexión. Probá de nuevo en un momento.');
    }
  },

  aviso(texto, foco) {
    return Swal.fire({ icon: 'warning', text: texto, customClass: { popup: 'pm-modal', confirmButton: 'btn-pm' } })
      .then(() => { if (foco) document.getElementById(foco).focus(); });
  },

  goTo(step) {
    document.querySelectorAll('.pm-wizard-step').forEach(s => s.classList.add('d-none'));
    if (step === 'exito') {
      document.getElementById('stepExito').classList.remove('d-none');
      document.getElementById('progressSteps').style.display = 'none';
      window.scrollTo({ top: 0, behavior: 'smooth' });
      return;
    }
    document.getElementById('step' + step).classList.remove('d-none');
    document.querySelectorAll('.pm-step').forEach(s => {
      const n = parseInt(s.dataset.step);
      s.classList.toggle('active', n === step);
      s.classList.toggle('done', n < step);
    });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

document.addEventListener('DOMContentLoaded', () => Wizard.init());
</script>

<?php require_once "includes/footer.php"; ?>
