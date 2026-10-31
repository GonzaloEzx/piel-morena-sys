<?php
/**
 * Piel Morena - Mi turno (gestión sin cuenta)
 * ?t=<token> → ver el turno, cancelarlo o cambiar el horario.
 * El link llega en la confirmación de la reserva.
 */
$titulo_pagina = "Mi turno — Piel Morena Estética";
$meta_descripcion = "Consultá, cancelá o cambiá tu turno en Piel Morena Estética.";
$pagina_actual = "mi-turno";
$meta_robots = "noindex, nofollow";
require_once "includes/header.php";

$token = preg_match('/^[0-9a-f]{32}$/', $_GET['t'] ?? '') ? $_GET['t'] : '';
?>

<section class="pm-section pm-reservar-section">
  <div class="container" style="max-width: 640px;">
    <div class="text-center mb-4">
      <h1 class="pm-reservar-title"><i class="bi bi-calendar2-week me-2"></i>Mi turno</h1>
      <div class="pm-divider"></div>
    </div>

    <div class="pm-wizard-step" id="vistaTurno">
      <div class="text-center py-4"><div class="spinner-border" style="color: var(--pm-bronce);" role="status"><span class="visually-hidden">Cargando...</span></div></div>
    </div>

    <div class="pm-wizard-step d-none" id="vistaReprogramar">
      <h2 class="pm-wizard-titulo"><i class="bi bi-arrow-repeat me-2"></i>Elegí otro horario</h2>
      <p class="pm-reserva-etiqueta mt-3">Día</p>
      <div class="pm-dias-scroll" id="diasList"></div>
      <div id="bloqueTurnos" class="d-none">
        <p class="pm-reserva-etiqueta mt-3" id="turnosInfo">Horario</p>
        <div id="turnosList" class="pm-turnos-grid"></div>
      </div>
      <div class="d-flex gap-2 mt-4">
        <button type="button" class="btn btn-pm-outline flex-fill" onclick="MiTurno.mostrar()">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </button>
      </div>
    </div>
  </div>
</section>

<script>
const TOKEN = <?= json_encode($token) ?>;
const TELEFONO_SALON = <?= json_encode(preg_replace('/\D/', '', TELEFONO_NEGOCIO)) ?>;
const DIAS_ES = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
const DIAS_CORTOS = ['dom','lun','mar','mié','jue','vie','sáb'];
const MESES_ES = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
const MESES_CORTOS = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
const ESTADOS = {
  pendiente:  ['Pendiente de confirmación', 'pm-estado--pendiente'],
  confirmada: ['Confirmado', 'pm-estado--confirmada'],
  en_proceso: ['En curso', 'pm-estado--confirmada'],
  completada: ['Realizado', 'pm-estado--completada'],
  cancelada:  ['Cancelado', 'pm-estado--cancelada'],
};

const fechaLarga = iso => { const [y, m, d] = iso.split('-').map(Number); return `${DIAS_ES[new Date(y, m - 1, d).getDay()]} ${d} de ${MESES_ES[m - 1]}`; };
const isoLocal = d => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
const esc = t => { const div = document.createElement('div'); div.textContent = t ?? ''; return div.innerHTML; };
const whatsapp = texto => `https://wa.me/${TELEFONO_SALON}?text=${encodeURIComponent(texto)}`;

const MiTurno = {
  cita: null,
  fecha: '',

  async init() {
    if (!TOKEN) return this.error('El link no es válido. Revisá el mensaje de tu reserva o escribinos por WhatsApp.');
    try {
      const r = await fetch('api/citas/gestionar.php?t=' + TOKEN);
      const data = await r.json();
      if (!data.success) return this.error(data.error);
      this.cita = data.data;
      this.mostrar();
    } catch (e) {
      this.error('No pudimos cargar tu turno. Probá de nuevo en un momento.');
    }
  },

  error(texto) {
    document.getElementById('vistaTurno').innerHTML = `<div class="pm-no-turnos"><i class="bi bi-exclamation-circle"></i>${esc(texto)}<br>
      <a href="${whatsapp('Hola! Quería consultar por mi turno.')}" target="_blank" rel="noopener">Escribinos por WhatsApp</a></div>`;
  },

  mostrar() {
    const c = this.cita;
    const [estado, clase] = ESTADOS[c.estado] || [c.estado, ''];
    document.getElementById('vistaReprogramar').classList.add('d-none');
    const vista = document.getElementById('vistaTurno');
    vista.classList.remove('d-none');
    const consulta = whatsapp(`Hola! Soy ${c.cliente}, tengo turno de ${c.servicio} el ${fechaLarga(c.fecha)} a las ${c.hora_inicio}.`);
    vista.innerHTML = `
      <p class="text-center"><span class="pm-estado ${clase}">${esc(estado)}</span></p>
      <div class="pm-resumen-card">
        <div class="pm-resumen-row"><span>Servicio</span><strong>${esc(c.servicio)}</strong></div>
        ${c.profesional ? `<div class="pm-resumen-row"><span>Profesional</span><strong>${esc(c.profesional)}</strong></div>` : ''}
        <div class="pm-resumen-row"><span>Día</span><strong>${fechaLarga(c.fecha)}</strong></div>
        <div class="pm-resumen-row"><span>Horario</span><strong>${c.hora_inicio} a ${c.hora_fin}</strong></div>
      </div>
      ${c.modificable ? `
      <div class="pm-exito-acciones mt-4">
        <button type="button" class="btn btn-pm" onclick="MiTurno.abrirReprogramar()"><i class="bi bi-arrow-repeat me-1"></i>Cambiar horario</button>
        <button type="button" class="btn btn-pm-outline" onclick="MiTurno.cancelar()"><i class="bi bi-x-circle me-1"></i>Cancelar turno</button>
      </div>
      <p class="pm-wizard-ayuda mt-3 text-center">Podés cambiarlo o cancelarlo hasta ${c.horas_limite} horas antes.</p>` : `
      <p class="pm-wizard-ayuda mt-3 text-center">${c.estado === 'cancelada' || c.estado === 'completada' ? '' : 'Faltan menos de ' + c.horas_limite + ' horas: para cambios, escribinos.'}</p>`}
      <div class="text-center mt-3">
        <a href="${consulta}" target="_blank" rel="noopener" class="pm-link-whatsapp"><i class="bi bi-whatsapp me-1"></i>Escribir al salón</a>
        ${c.estado === 'cancelada' ? `<br><a href="reservar.php?servicio=${c.id_servicio}" class="btn btn-pm mt-3">Reservar otro turno</a>` : ''}
      </div>`;
  },

  async cancelar() {
    const ok = await Swal.fire({
      icon: 'question', text: '¿Cancelar tu turno? El horario queda libre para otra persona.',
      showCancelButton: true, confirmButtonText: 'Sí, cancelar', cancelButtonText: 'No',
      customClass: { popup: 'pm-modal', confirmButton: 'btn-pm', cancelButton: 'btn-pm-outline' },
    });
    if (!ok.isConfirmed) return;
    this.enviar({ accion: 'cancelar' }, 'Tu turno quedó cancelado.');
  },

  async enviar(cuerpo, exito) {
    try {
      const r = await fetch('api/citas/gestionar.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ t: TOKEN, ...cuerpo }) });
      const data = await r.json();
      if (!data.success) {
        await Swal.fire({ icon: 'warning', text: data.error, customClass: { popup: 'pm-modal', confirmButton: 'btn-pm' } });
        if (r.status === 409 && this.fecha) this.cargarTurnos();
        return;
      }
      this.cita = data.data;
      this.mostrar();
      Swal.fire({ icon: 'success', text: exito, timer: 2500, showConfirmButton: false, customClass: { popup: 'pm-modal' } });
    } catch (e) {
      Swal.fire({ icon: 'error', text: 'Error de conexión. Probá de nuevo.', customClass: { popup: 'pm-modal', confirmButton: 'btn-pm' } });
    }
  },

  abrirReprogramar() {
    document.getElementById('vistaTurno').classList.add('d-none');
    document.getElementById('vistaReprogramar').classList.remove('d-none');
    document.getElementById('bloqueTurnos').classList.add('d-none');
    const hoy = new Date();
    let html = '';
    for (let i = 0; i < 21; i++) {
      const d = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate() + i);
      const iso = isoLocal(d);
      html += `<button type="button" class="pm-jornada-card" data-fecha="${iso}" onclick="MiTurno.elegirDia(this)" aria-label="${fechaLarga(iso)}">
        <span class="pm-jornada-dia">${i === 0 ? 'hoy' : i === 1 ? 'mañana' : DIAS_CORTOS[d.getDay()]}</span>
        <span class="pm-jornada-num">${d.getDate()}</span>
        <span class="pm-jornada-mes">${MESES_CORTOS[d.getMonth()]}</span>
      </button>`;
    }
    document.getElementById('diasList').innerHTML = html;
  },

  elegirDia(el) {
    document.querySelectorAll('#diasList .pm-jornada-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    this.fecha = el.dataset.fecha;
    this.cargarTurnos();
  },

  async cargarTurnos() {
    const bloque = document.getElementById('bloqueTurnos');
    const lista = document.getElementById('turnosList');
    bloque.classList.remove('d-none');
    document.getElementById('turnosInfo').textContent = 'Horarios del ' + fechaLarga(this.fecha);
    lista.innerHTML = '<div class="text-center py-3"><div class="spinner-border" style="color:var(--pm-bronce)" role="status"></div></div>';
    const params = new URLSearchParams({ fecha: this.fecha, id_servicio: this.cita.id_servicio, t: TOKEN });
    try {
      const r = await fetch('api/citas/disponibilidad.php?' + params);
      const data = await r.json();
      if (!data.success) throw new Error(data.error);
      lista.innerHTML = data.data.turnos.length
        ? data.data.turnos.map(t => `<button type="button" class="pm-turno-btn" onclick="MiTurno.reprogramar('${t.hora_inicio}')">${t.hora_inicio}</button>`).join('')
        : `<div class="pm-no-turnos"><i class="bi bi-calendar-x"></i>${esc(data.data.mensaje || 'No quedan horarios este día')}<br><small>Probá otro día</small></div>`;
    } catch (e) {
      lista.innerHTML = '<p class="text-center" style="color:var(--pm-rojo)">No pudimos cargar los horarios.</p>';
    }
  },

  async reprogramar(hora) {
    const ok = await Swal.fire({
      icon: 'question', text: `¿Pasar tu turno al ${fechaLarga(this.fecha)} a las ${hora}?`,
      showCancelButton: true, confirmButtonText: 'Sí, cambiar', cancelButtonText: 'No',
      customClass: { popup: 'pm-modal', confirmButton: 'btn-pm', cancelButton: 'btn-pm-outline' },
    });
    if (!ok.isConfirmed) return;
    this.enviar({ accion: 'reprogramar', fecha: this.fecha, hora_inicio: hora }, 'Listo. Te confirmamos el nuevo horario por WhatsApp.');
  },
};

document.addEventListener('DOMContentLoaded', () => MiTurno.init());
</script>

<?php require_once "includes/footer.php"; ?>
