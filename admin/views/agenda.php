<?php
/**
 * Piel Morena — Admin: Agenda del día (v2.0)
 * Los turnos se acuerdan por WhatsApp y se cargan acá.
 * Turnos por profesional, pendientes para confirmar,
 * recordatorios de mañana y alta rápida del turno tomado por WhatsApp.
 * Pensada para usarse desde el celular.
 */
$titulo_admin = 'Agenda';
$rol_admin_requerido = 'empleado';
$extra_css = '<style>
  .pm-ag-barra { display:flex; flex-wrap:wrap; gap:.5rem; align-items:center; }
  .pm-ag-barra .form-control { max-width: 170px; }
  .pm-ag-dia { font-family: var(--pm-font-heading, serif); font-size: 1.25rem; margin: 0 .25rem; text-transform: capitalize; }
  .pm-ag-nuevo { margin-left: auto; }
  .pm-ag-tabs { overflow-x:auto; }
  .pm-ag-tabs .nav-link { color: var(--pm-tierra, #7A654F); min-height: 44px; white-space:nowrap; padding:.5rem .75rem; }
  .pm-ag-tabs .nav-link.active { background: var(--pm-bronce, #8A7650); color: #fff; }
  .pm-ag-chips { display:flex; gap:.5rem; overflow-x:auto; padding-bottom:.25rem; }
  .pm-ag-chip { flex:0 0 auto; min-height:40px; padding:0 1rem; border-radius:999px; border:1.5px solid var(--pm-border, #D8CCAA); background:#fff; }
  .pm-ag-chip.active { background: var(--pm-bronce, #8A7650); border-color: var(--pm-bronce, #8A7650); color:#fff; }
  .pm-ag-cols { display:grid; gap:1rem; }
  .pm-ag-col { display:none; }
  .pm-ag-col.active { display:block; }
  .pm-ag-col h3 { font-size:1rem; font-weight:600; margin:0 0 .5rem; color: var(--pm-tierra, #7A654F); }
  .pm-ag-card { border:1px solid var(--pm-border-light, #E6DFC8); border-left:4px solid var(--pm-border, #D8CCAA); border-radius:10px; padding:.75rem; background:#fff; margin-bottom:.5rem; }
  .pm-ag-card[data-estado="pendiente"] { border-left-color:#D4935A; }
  .pm-ag-card[data-estado="confirmada"] { border-left-color:#8E977D; }
  .pm-ag-card[data-estado="en_proceso"] { border-left-color:#8A7650; }
  .pm-ag-card[data-estado="completada"] { opacity:.65; }
  .pm-ag-hora { font-weight:700; font-variant-numeric: tabular-nums; }
  .pm-ag-cliente { font-weight:600; }
  .pm-ag-meta { font-size:.875rem; color: var(--pm-text-muted, #5C4F42); }
  .pm-ag-acciones { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.5rem; }
  .pm-ag-acciones .btn { min-height:38px; font-size:.85rem; }
  .pm-ag-vacio { color: var(--pm-text-muted, #5C4F42); font-size:.9rem; padding:.5rem 0; }
  .pm-ag-estado { font-size:.75rem; padding:.15rem .5rem; border-radius:999px; background:#F6E3C8; color:#7A4E1F; }
  .pm-ag-estado[data-e="confirmada"] { background:#DDE3D2; color:#3F4A33; }
  .pm-ag-estado[data-e="en_proceso"], .pm-ag-estado[data-e="completada"] { background:#ECE7D1; color:#5C4F42; }
  .pm-ag-enviado { font-size:.8rem; color:#3F4A33; }
  .pm-ag-horas { display:flex; flex-wrap:wrap; gap:.35rem; }
  .pm-ag-horas .btn { min-width:64px; }
  @media (min-width: 992px) {
    .pm-ag-chips { display:none; }
    .pm-ag-cols { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
    .pm-ag-col { display:block; }
  }
</style>';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="pm-panel mb-3">
  <div class="pm-panel-body py-3">
    <div class="pm-ag-barra">
      <button type="button" class="btn btn-pm-outline" onclick="Agenda.mover(-1)" aria-label="Día anterior"><i class="bi bi-chevron-left"></i></button>
      <input type="date" class="form-control" id="agFecha" aria-label="Fecha">
      <button type="button" class="btn btn-pm-outline" onclick="Agenda.mover(1)" aria-label="Día siguiente"><i class="bi bi-chevron-right"></i></button>
      <button type="button" class="btn btn-pm-outline" onclick="Agenda.irHoy()">Hoy</button>
      <button type="button" class="btn btn-pm pm-ag-nuevo" onclick="Alta.abrir()"><i class="bi bi-plus-lg me-1"></i>Turno</button>
    </div>
  </div>
</div>

<ul class="nav nav-pills pm-ag-tabs flex-nowrap mb-3" role="tablist">
  <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabDia" type="button" role="tab">Día <span id="agDiaTexto"></span></button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabPendientes" type="button" role="tab">Pendientes <span class="badge bg-warning text-dark" id="agCantPend">0</span></button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabManana" type="button" role="tab">Recordatorios <span class="badge bg-secondary" id="agCantMan">0</span></button></li>
</ul>

<div class="tab-content">
  <div class="tab-pane fade show active" id="tabDia" role="tabpanel">
    <div class="pm-ag-chips mb-3" id="agChips"></div>
    <div class="pm-ag-cols" id="agCols"></div>
  </div>
  <div class="tab-pane fade" id="tabPendientes" role="tabpanel">
    <p class="pm-ag-meta">Turnos cargados sin confirmar. Abrí WhatsApp para escribirle a la clienta y, cuando lo acuerden, marcalo como confirmado.</p>
    <div id="agPendientes"></div>
  </div>
  <div class="tab-pane fade" id="tabManana" role="tabpanel">
    <p class="pm-ag-meta">Turnos de mañana. Un toque abre WhatsApp con el recordatorio armado; enviarlo queda de tu lado.</p>
    <div id="agManana"></div>
  </div>
</div>

<!-- Alta rápida -->
<div class="modal fade" id="modalAlta" tabindex="-1" aria-labelledby="modalAltaTitulo" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-sm-down">
    <form class="modal-content" id="formAlta" onsubmit="event.preventDefault(); Alta.guardar();">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAltaTitulo">Nuevo turno</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label" for="altaTel">WhatsApp de la clienta</label>
          <input type="tel" class="form-control" id="altaTel" inputmode="tel" placeholder="3624 123456" autocomplete="off">
          <div class="form-text" id="altaClienta"></div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="altaNombre">Nombre</label>
          <input type="text" class="form-control" id="altaNombre" required>
        </div>
        <div class="mb-3">
          <label class="form-label" for="altaServicio">Servicio</label>
          <select class="form-select" id="altaServicio" required></select>
        </div>
        <div class="mb-3">
          <label class="form-label" for="altaPro">Profesional</label>
          <select class="form-select" id="altaPro"><option value="">Cualquiera</option></select>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-7">
            <label class="form-label" for="altaFecha">Día</label>
            <input type="date" class="form-control" id="altaFecha" required>
          </div>
          <div class="col-5">
            <label class="form-label" for="altaHora">Hora</label>
            <input type="time" class="form-control" id="altaHora" step="300" required>
          </div>
        </div>
        <div class="pm-ag-horas mb-2" id="altaLibres"></div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="altaConfirmada" checked>
          <label class="form-check-label" for="altaConfirmada">Ya está confirmado con la clienta</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-pm-outline" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-pm" id="altaGuardar"><i class="bi bi-check-lg me-1"></i>Agendar</button>
      </div>
    </form>
  </div>
</div>

<?php ob_start(); ?>
<script>
const API = '<?= URL_API ?>';
const esc = t => { const d = document.createElement('div'); d.textContent = t ?? ''; return d.innerHTML; };
const isoLocal = d => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
const ESTADO_TEXTO = { pendiente: 'Pendiente', confirmada: 'Confirmado', en_proceso: 'En curso', completada: 'Atendido' };

const Recordados = {
  clave: 'pm_agenda_recordados',
  leer() { try { return JSON.parse(localStorage.getItem(this.clave)) || {}; } catch (e) { return {}; } },
  marcar(id) { try { const r = this.leer(); r[id] = Date.now(); localStorage.setItem(this.clave, JSON.stringify(r)); } catch (e) {} },
};

const Agenda = {
  datos: null,
  filtro: 'todas',

  init() {
    const input = document.getElementById('agFecha');
    input.value = new URLSearchParams(location.search).get('fecha') || isoLocal(new Date());
    input.addEventListener('change', () => this.cargar());
    this.cargar();
  },

  mover(dias) {
    const [y, m, d] = document.getElementById('agFecha').value.split('-').map(Number);
    document.getElementById('agFecha').value = isoLocal(new Date(y, m - 1, d + dias));
    this.cargar();
  },

  irHoy() {
    document.getElementById('agFecha').value = isoLocal(new Date());
    this.cargar();
  },

  async cargar() {
    const fecha = document.getElementById('agFecha').value;
    history.replaceState(null, '', '?fecha=' + fecha);
    const res = await apiCall(`${API}/admin/agenda.php?fecha=${fecha}`);
    if (!res.success) return PM.error('Error', res.error);
    this.datos = res.data;
    document.getElementById('agDiaTexto').textContent = '· ' + res.data.dia;
    this.renderDia();
    this.renderLista('agPendientes', res.data.pendientes, 'pendiente');
    this.renderLista('agManana', res.data.manana, 'recordar');
    document.getElementById('agCantPend').textContent = res.data.pendientes.length;
    document.getElementById('agCantMan').textContent = res.data.manana.length;
  },

  columnas() {
    const cols = this.datos.profesionales.map(p => ({ id: String(p.id), nombre: p.nombre }));
    if (this.datos.citas.some(c => c.id_empleado === null || !cols.some(p => p.id === String(c.id_empleado)))) {
      cols.push({ id: 'sin', nombre: 'Sin asignar' });
    }
    return cols;
  },

  renderDia() {
    const cols = this.columnas();
    if (this.filtro !== 'todas' && !cols.some(c => c.id === this.filtro)) this.filtro = 'todas';

    document.getElementById('agChips').innerHTML =
      [{ id: 'todas', nombre: 'Todas' }, ...cols].map(c =>
        `<button type="button" class="pm-ag-chip${this.filtro === c.id ? ' active' : ''}" onclick="Agenda.filtrar('${c.id}')">${esc(c.nombre.split(' ')[0])}</button>`).join('');

    const citasDe = id => this.datos.citas.filter(c =>
      id === 'sin' ? (c.id_empleado === null || !cols.some(p => p.id === String(c.id_empleado))) : String(c.id_empleado) === id);

    document.getElementById('agCols').innerHTML = cols.map(col => {
      const citas = citasDe(col.id);
      const activa = this.filtro === 'todas' ? citas.length > 0 || cols.length <= 3 : this.filtro === col.id;
      return `<section class="pm-ag-col${activa ? ' active' : ''}">
        <h3>${esc(col.nombre)} <small class="text-muted">(${citas.length})</small></h3>
        ${citas.length ? citas.map(c => this.tarjeta(c, 'dia')).join('') : '<p class="pm-ag-vacio">Sin turnos</p>'}
      </section>`;
    }).join('') || '<p class="pm-ag-vacio">No hay profesionales cargadas. Agregalas en Empleados.</p>';

    if (this.filtro === 'todas' && !this.datos.citas.length) {
      document.getElementById('agCols').insertAdjacentHTML('afterbegin', `<p class="pm-ag-vacio">No hay turnos el ${esc(this.datos.dia)}.</p>`);
    }
  },

  filtrar(id) {
    this.filtro = id;
    this.renderDia();
  },

  tarjeta(c, modo) {
    const acciones = [];
    if (modo === 'recordar') {
      const enviado = Recordados.leer()[c.id];
      acciones.push(c.wa_recordar
        ? `<a class="btn btn-pm btn-sm" href="${c.wa_recordar}" target="_blank" rel="noopener" onclick="Recordados.marcar(${c.id}); setTimeout(() => Agenda.cargar(), 500)"><i class="bi bi-whatsapp me-1"></i>Abrir recordatorio</a>`
        : '<span class="pm-ag-meta">Sin WhatsApp válido</span>');
      if (enviado) acciones.push('<span class="pm-ag-enviado"><i class="bi bi-check2"></i> Ya abriste este recordatorio</span>');
    } else {
      if (c.estado === 'pendiente') {
        if (c.wa_confirmar) {
          acciones.push(`<a class="btn btn-pm-outline btn-sm" href="${c.wa_confirmar}" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-1"></i>Abrir WhatsApp</a>`);
        }
        acciones.push(`<button type="button" class="btn btn-pm btn-sm" onclick="Agenda.estado(${c.id}, 'confirmada')"><i class="bi bi-check2 me-1"></i>Marcar confirmado</button>`);
      }
      if (modo === 'dia' && c.estado === 'confirmada') {
        acciones.push(`<button type="button" class="btn btn-pm-outline btn-sm" onclick="Agenda.estado(${c.id}, 'completada')"><i class="bi bi-check2 me-1"></i>Atendida</button>`);
      }
      if (c.estado === 'pendiente' || c.estado === 'confirmada') {
        acciones.push(`<button type="button" class="btn btn-link btn-sm text-danger" onclick="Agenda.cancelar(${c.id}, '${esc(c.cliente).replace(/'/g, '')}')">Cancelar</button>`);
      }
    }
    const cuando = modo === 'dia' ? '' : `<span class="text-capitalize">${esc(c.dia)}</span> · `;
    return `<article class="pm-ag-card" data-estado="${c.estado}">
      <div class="d-flex justify-content-between align-items-start gap-2">
        <span class="pm-ag-hora">${cuando}${c.hora_inicio}–${c.hora_fin}</span>
        <span class="pm-ag-estado" data-e="${c.estado}">${ESTADO_TEXTO[c.estado] || c.estado}</span>
      </div>
      <div class="pm-ag-cliente">${esc(c.cliente)}</div>
      <div class="pm-ag-meta">${esc(c.servicio)}${modo !== 'dia' && c.profesional ? ' · ' + esc(c.profesional.split(' ')[0]) : ''}${c.telefono ? ' · ' + esc(c.telefono) : ''}</div>
      ${acciones.length ? `<div class="pm-ag-acciones">${acciones.join('')}</div>` : ''}
    </article>`;
  },

  renderLista(id, lista, modo) {
    document.getElementById(id).innerHTML = lista.length
      ? lista.map(c => this.tarjeta(c, modo)).join('')
      : `<p class="pm-ag-vacio">${modo === 'pendiente' ? 'No hay turnos pendientes.' : 'No hay turnos para mañana.'}</p>`;
  },

  async estado(id, estado, silencioso = false) {
    const res = await apiCall(`${API}/admin/citas.php`, 'PATCH', { id, estado });
    if (!res.success) return PM.error('Error', res.error);
    if (!silencioso) PM.toast('success', estado === 'completada' ? 'Turno atendido' : 'Turno confirmado');
    if (res.data && res.data.caja_registrada) setTimeout(() => PM.toast('success', 'Ingreso registrado en caja'), 600);
    this.cargar();
  },

  async cancelar(id, cliente) {
    if (!await PM.confirm('¿Cancelar este turno?', cliente)) return;
    const res = await apiCall(`${API}/admin/citas.php`, 'PATCH', { id, estado: 'cancelada' });
    if (!res.success) return PM.error('Error', res.error);
    PM.toast('success', 'Turno cancelado');
    this.cargar();
  },
};

const Alta = {
  modal: null,
  idCliente: 0,
  servicios: [],
  consulta: 0,

  async abrir() {
    this.modal = this.modal || new bootstrap.Modal(document.getElementById('modalAlta'));
    document.getElementById('formAlta').reset();
    document.getElementById('altaClienta').textContent = '';
    document.getElementById('altaLibres').innerHTML = '';
    document.getElementById('altaFecha').value = document.getElementById('agFecha').value;
    this.idCliente = 0;
    if (!this.servicios.length) {
      const res = await apiCall(`${API}/admin/citas.php?servicios_activos=1`);
      this.servicios = res.success ? res.data : [];
      document.getElementById('altaServicio').innerHTML = '<option value="">Elegí un servicio</option>' +
        this.servicios.map(s => `<option value="${s.id}">${esc(s.nombre)} (${s.duracion_minutos} min)</option>`).join('');
    }
    this.modal.show();
    setTimeout(() => document.getElementById('altaTel').focus(), 300);
  },

  async buscarClienta() {
    const tel = document.getElementById('altaTel').value.replace(/\D/g, '');
    const info = document.getElementById('altaClienta');
    this.idCliente = 0;
    if (tel.length < 6) { info.textContent = ''; return; }
    const res = await apiCall(`${API}/admin/citas.php?buscar_clientes=${encodeURIComponent(tel.slice(-6))}`);
    const c = res.success && res.data.find(u => (u.telefono || '').replace(/\D/g, '').endsWith(tel.slice(-10)));
    if (c) {
      this.idCliente = c.id;
      document.getElementById('altaNombre').value = `${c.nombre} ${c.apellidos || ''}`.trim();
      info.innerHTML = '<i class="bi bi-person-check"></i> Clienta encontrada';
    } else {
      info.textContent = 'Clienta nueva: se guarda con este número.';
    }
  },

  async cargarProfesionales() {
    const id = document.getElementById('altaServicio').value;
    const sel = document.getElementById('altaPro');
    sel.innerHTML = '<option value="">Cualquiera</option>';
    if (!id) return;
    const res = await fetch(`${API}/citas/profesionales.php?id_servicio=${id}`).then(r => r.json()).catch(() => null);
    if (id !== document.getElementById('altaServicio').value) return;
    if (res && res.success) sel.innerHTML += res.data.map(p => `<option value="${p.id}">${esc(p.nombre)}</option>`).join('');
    this.cargarLibres();
  },

  async cargarLibres() {
    const nro = ++this.consulta;
    const cont = document.getElementById('altaLibres');
    const id = document.getElementById('altaServicio').value;
    const fecha = document.getElementById('altaFecha').value;
    if (!id || !fecha || fecha < isoLocal(new Date())) { cont.innerHTML = ''; return; }
    const params = new URLSearchParams({ id_servicio: id, fecha });
    const pro = document.getElementById('altaPro').value;
    if (pro) params.set('id_empleado', pro);
    const res = await fetch(`${API}/citas/disponibilidad.php?${params}`).then(r => r.json()).catch(() => null);
    if (nro !== this.consulta) return; // llegó una respuesta más nueva
    if (!res || !res.success) { cont.innerHTML = ''; return; }
    cont.innerHTML = res.data.turnos.length
      ? '<span class="pm-ag-meta w-100">Libres:</span>' + res.data.turnos.map(t =>
          `<button type="button" class="btn btn-pm-outline btn-sm" onclick="document.getElementById('altaHora').value='${t.hora_inicio}'">${t.hora_inicio}</button>`).join('')
      : `<span class="pm-ag-meta">${esc(res.data.mensaje || 'Sin horarios libres ese día')}</span>`;
  },

  async guardar() {
    const nombre = document.getElementById('altaNombre').value.trim();
    const telefono = document.getElementById('altaTel').value.trim();
    const datos = {
      id_servicio: document.getElementById('altaServicio').value,
      id_empleado: document.getElementById('altaPro').value || null,
      fecha: document.getElementById('altaFecha').value,
      hora_inicio: document.getElementById('altaHora').value.slice(0, 5),
      estado: document.getElementById('altaConfirmada').checked ? 'confirmada' : 'pendiente',
    };
    if (this.idCliente) datos.id_cliente = this.idCliente;
    else Object.assign(datos, { nombre, telefono });

    if (!datos.id_servicio || !datos.fecha || !datos.hora_inicio || (!this.idCliente && !nombre)) {
      return PM.error('Faltan datos', 'Completá nombre, servicio, día y hora.');
    }
    const btn = document.getElementById('altaGuardar');
    btn.disabled = true;
    const res = await apiCall(`${API}/admin/citas.php`, 'POST', datos);
    btn.disabled = false;
    if (!res.success) return PM.error('No se pudo agendar', res.error);
    PM.toast('success', 'Turno agendado');
    this.modal.hide();
    document.getElementById('agFecha').value = datos.fecha;
    Agenda.cargar();
  },
};

document.addEventListener('DOMContentLoaded', () => {
  Agenda.init();
  document.getElementById('altaTel').addEventListener('change', () => Alta.buscarClienta());
  document.getElementById('altaServicio').addEventListener('change', () => Alta.cargarProfesionales());
  document.getElementById('altaPro').addEventListener('change', () => Alta.cargarLibres());
  document.getElementById('altaFecha').addEventListener('change', () => Alta.cargarLibres());
});
</script>
<?php $extra_js = ob_get_clean(); ?>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
