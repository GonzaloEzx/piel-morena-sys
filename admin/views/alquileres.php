<?php
/**
 * Piel Morena — Admin: Gestion de Alquileres de Maquinas
 * Calendario + tabla de alquileres por maquina
 */
$titulo_admin = 'Alquileres';
$extra_css = '
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
<style>
  #calAlquileres { max-height: 620px; }
  .fc .fc-daygrid-event { cursor: pointer; border: none; padding: 2px 6px; font-size: .75rem; border-radius: 4px; }
  .fc .fc-toolbar-title { font-size: 1.1rem; }
  .fc .fc-day-today { background: rgba(138,118,80,.06) !important; }

  .badge-alquiler-activo   { background: #198754; color: #fff; font-size: .72rem; padding: 3px 8px; border-radius: 6px; }
  .badge-alquiler-cancelado { background: #dc3545; color: #fff; font-size: .72rem; padding: 3px 8px; border-radius: 6px; }

  .al-fechas-list { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .5rem; }
  .al-fecha-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    background: var(--pm-crema, #ECE7D1); color: var(--pm-texto, #3F352B);
    padding: 4px 10px; border-radius: 20px; font-size: .82rem; font-weight: 500;
  }
  .al-fecha-chip .btn-close { font-size: .55rem; filter: none; opacity: .6; }
  .al-fecha-chip .btn-close:hover { opacity: 1; }

  .al-citas-afectadas { max-height: 200px; overflow-y: auto; }
  .al-cita-item { display: flex; justify-content: space-between; padding: .4rem .6rem; border-bottom: 1px solid #eee; font-size: .82rem; }

  .al-maq-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 6px; }
</style>';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<!-- Filtros -->
<div class="pm-panel mb-4">
  <div class="pm-panel-body py-3">
    <div class="row g-2 align-items-end">
      <div class="col-auto">
        <label class="form-label">Maquina</label>
        <select class="form-select" id="filtroMaquina">
          <option value="">Todas</option>
        </select>
      </div>
      <div class="col-auto">
        <label class="form-label">Estado</label>
        <select class="form-select" id="filtroEstado">
          <option value="">Todos</option>
          <option value="activo" selected>Activo</option>
          <option value="cancelado">Cancelado</option>
        </select>
      </div>
      <div class="col-auto">
        <label class="form-label">Desde</label>
        <input type="date" class="form-control" id="filtroDesde">
      </div>
      <div class="col-auto">
        <label class="form-label">Hasta</label>
        <input type="date" class="form-control" id="filtroHasta">
      </div>
      <div class="col-auto">
        <button class="btn btn-pm btn-pm-sm" onclick="cargarAlquileres()">
          <i class="bi bi-search me-1"></i>Filtrar
        </button>
      </div>
      <div class="col-auto">
        <button class="btn btn-pm-outline btn-pm-sm" onclick="limpiarFiltros()">
          <i class="bi bi-x-lg me-1"></i>Limpiar
        </button>
      </div>
      <div class="col-auto ms-auto">
        <div class="btn-group" role="group">
          <button class="btn btn-pm-outline btn-pm-sm" id="btnVistaTabla" onclick="toggleVista('tabla')">
            <i class="bi bi-table me-1"></i>Tabla
          </button>
          <button class="btn btn-pm btn-pm-sm active" id="btnVistaCal" onclick="toggleVista('calendario')">
            <i class="bi bi-calendar3 me-1"></i>Calendario
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Vista Calendario -->
<div id="vistaCalendario">
  <div class="pm-panel">
    <div class="pm-panel-header">
      <h3 class="pm-panel-title"><i class="bi bi-calendar-event me-2"></i>Calendario de Alquileres</h3>
      <button class="btn btn-pm btn-pm-sm" onclick="abrirModalNueva()">
        <i class="bi bi-plus-circle me-1"></i>Nuevo Alquiler
      </button>
    </div>
    <div class="pm-panel-body">
      <div id="calAlquileres"></div>
    </div>
  </div>
</div>

<!-- Vista Tabla -->
<div id="vistaTabla" style="display:none">
  <div class="pm-panel">
    <div class="pm-panel-header">
      <h3 class="pm-panel-title"><i class="bi bi-calendar-event me-2"></i>Listado de Alquileres</h3>
      <button class="btn btn-pm btn-pm-sm" onclick="abrirModalNueva()">
        <i class="bi bi-plus-circle me-1"></i>Nuevo Alquiler
      </button>
    </div>
    <div class="pm-panel-body">
      <table id="dtAlquileres" class="table table-hover w-100">
        <thead>
          <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Maquina</th>
            <th>Horario</th>
            <th>Citas</th>
            <th>Estado</th>
            <th>Notas</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal: Nuevo Alquiler -->
<div class="modal fade" id="modalNueva" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Nuevo Alquiler</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Maquina <span class="text-danger">*</span></label>
          <select class="form-select" id="alMaquina" required>
            <option value="">Seleccionar maquina...</option>
          </select>
          <div class="form-text">Si no aparece la maquina, primero cargala en <a href="<?= URL_ADMIN ?>/views/maquinas.php">Maquinas</a>.</div>
        </div>
        <div class="mb-3">
          <label class="form-label">Fechas <span class="text-danger">*</span></label>
          <div class="input-group">
            <input type="date" class="form-control" id="alFechaInput">
            <button class="btn btn-pm-outline" type="button" onclick="agregarFecha()">
              <i class="bi bi-plus-lg"></i> Agregar
            </button>
          </div>
          <div class="form-text">Podes agregar multiples fechas para una misma maquina</div>
          <div class="al-fechas-list" id="alFechasList"></div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="form-label">Hora inicio</label>
            <input type="time" class="form-control" id="alHoraInicio" value="08:00">
          </div>
          <div class="col-6">
            <label class="form-label">Hora fin</label>
            <input type="time" class="form-control" id="alHoraFin" value="20:00">
          </div>
        </div>
        <div class="mb-0">
          <label class="form-label">Notas</label>
          <textarea class="form-control" id="alNotas" rows="2" placeholder="Observaciones opcionales..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-pm-outline btn-pm-sm" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-pm btn-pm-sm" id="alBtnCrear" onclick="crearAlquileres()">
          <i class="bi bi-check-lg me-1"></i>Crear Alquiler(es)
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Editar Alquiler -->
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Alquiler</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="aeId">
        <p class="mb-3 fw-medium" id="aeInfo"></p>
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="form-label">Hora inicio</label>
            <input type="time" class="form-control" id="aeHoraInicio">
          </div>
          <div class="col-6">
            <label class="form-label">Hora fin</label>
            <input type="time" class="form-control" id="aeHoraFin">
          </div>
        </div>
        <div class="mb-0">
          <label class="form-label">Notas</label>
          <textarea class="form-control" id="aeNotas" rows="2"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-pm-outline btn-pm-sm" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-pm btn-pm-sm" onclick="guardarEdicion()">
          <i class="bi bi-check-lg me-1"></i>Guardar
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
let dtAlquileres;
let calendar;
let modalNueva, modalEditar;
let maquinasCache = [];
let alFechasSeleccionadas = [];

function getColorMaquina(id) {
    const m = maquinasCache.find(x => x.id == id);
    return m ? m.color : '#8A7650';
}
function getNombreMaquina(id) {
    const m = maquinasCache.find(x => x.id == id);
    return m ? m.nombre : 'Maquina';
}

document.addEventListener('DOMContentLoaded', async () => {
    modalNueva  = new bootstrap.Modal(document.getElementById('modalNueva'));
    modalEditar = new bootstrap.Modal(document.getElementById('modalEditar'));
    document.getElementById('alFechaInput').min = new Date().toISOString().slice(0, 10);

    await cargarMaquinas();

    dtAlquileres = $('#dtAlquileres').DataTable({
        ...DT_DEFAULTS,
        ajax: {
            url: '<?= URL_API ?>/admin/alquileres.php',
            data: function(d) {
                d.id_maquina = document.getElementById('filtroMaquina').value;
                d.estado      = document.getElementById('filtroEstado').value;
                d.fecha_desde = document.getElementById('filtroDesde').value;
                d.fecha_hasta = document.getElementById('filtroHasta').value;
            },
            dataSrc: (json) => json.success ? json.data : []
        },
        columns: [
            { data: 'id' },
            { data: 'fecha', render: (d) => formatFechaAlquiler(d) },
            { data: null, render: (d) => `<span class="al-maq-dot" style="background:${d.maquina_color || '#8A7650'}"></span>${escapeHtml(d.maquina)}` },
            { data: null, render: (d) => formatHora(d.hora_inicio) + ' — ' + formatHora(d.hora_fin) },
            { data: 'citas_reservadas', render: (d) => d > 0 ? `<span class="badge bg-primary">${d}</span>` : '<span class="text-muted">0</span>' },
            { data: 'estado', render: (d) => `<span class="badge-alquiler-${d}">${d === 'activo' ? 'Activo' : 'Cancelado'}</span>` },
            { data: 'notas', render: (d) => d ? `<span title="${escapeHtml(d)}">${escapeHtml(d.substring(0, 30))}${d.length > 30 ? '...' : ''}</span>` : '<span class="text-muted">—</span>' },
            { data: null, orderable: false, render: (d) => {
                if (d.estado === 'cancelado') return '<span class="text-muted">—</span>';
                return `
                    <button class="pm-action-btn edit" title="Editar" onclick="abrirEditar(${d.id})"><i class="bi bi-pencil"></i></button>
                    <button class="pm-action-btn delete" title="Eliminar" onclick="eliminarAlquiler(${d.id})"><i class="bi bi-trash"></i></button>
                `;
            }}
        ],
        order: [[1, 'asc']]
    });

    initCalendario();
});

function escapeHtml(s) {
    return String(s || '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

async function cargarMaquinas() {
    const res = await apiCall('<?= URL_API ?>/admin/alquileres.php?maquinas_activas=1');
    if (!res.success) return;
    maquinasCache = res.data;

    const filtro = document.getElementById('filtroMaquina');
    const modal  = document.getElementById('alMaquina');
    maquinasCache.forEach(m => {
        filtro.innerHTML += `<option value="${m.id}">${escapeHtml(m.nombre)}</option>`;
        modal.innerHTML  += `<option value="${m.id}">${escapeHtml(m.nombre)}</option>`;
    });
}

function initCalendario() {
    const calEl = document.getElementById('calAlquileres');
    calendar = new FullCalendar.Calendar(calEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth' },
        height: 600,
        dayMaxEvents: 3,
        eventClick: function(info) {
            const a = info.event.extendedProps;
            if (a.estado === 'cancelado') return;
            abrirEditar(a.alquiler_id);
        },
        events: function(fetchInfo, successCallback) {
            const desde = fetchInfo.startStr.slice(0, 10);
            const hasta = fetchInfo.endStr.slice(0, 10);
            const estado = document.getElementById('filtroEstado').value;
            const maq    = document.getElementById('filtroMaquina').value;

            let url = `<?= URL_API ?>/admin/alquileres.php?fecha_desde=${desde}&fecha_hasta=${hasta}`;
            if (estado) url += `&estado=${estado}`;
            if (maq)    url += `&id_maquina=${maq}`;

            apiCall(url).then(res => {
                if (!res.success) return successCallback([]);
                const events = res.data.map(a => ({
                    id: a.id,
                    title: a.maquina + (a.citas_reservadas > 0 ? ` (${a.citas_reservadas} citas)` : ''),
                    start: a.fecha,
                    allDay: true,
                    backgroundColor: a.estado === 'cancelado' ? '#ccc' : (a.maquina_color || '#8A7650'),
                    textColor: a.estado === 'cancelado' ? '#888' : '#fff',
                    extendedProps: { ...a, alquiler_id: a.id }
                }));
                successCallback(events);
            });
        }
    });
    calendar.render();
}

function toggleVista(vista) {
    if (vista === 'tabla') {
        document.getElementById('vistaTabla').style.display = '';
        document.getElementById('vistaCalendario').style.display = 'none';
        document.getElementById('btnVistaTabla').className = 'btn btn-pm btn-pm-sm active';
        document.getElementById('btnVistaCal').className = 'btn btn-pm-outline btn-pm-sm';
        dtAlquileres.ajax.reload();
    } else {
        document.getElementById('vistaCalendario').style.display = '';
        document.getElementById('vistaTabla').style.display = 'none';
        document.getElementById('btnVistaCal').className = 'btn btn-pm btn-pm-sm active';
        document.getElementById('btnVistaTabla').className = 'btn btn-pm-outline btn-pm-sm';
        if (calendar) calendar.refetchEvents();
    }
}

function cargarAlquileres() {
    dtAlquileres.ajax.reload();
    if (calendar) calendar.refetchEvents();
}

function limpiarFiltros() {
    document.getElementById('filtroMaquina').value = '';
    document.getElementById('filtroEstado').value = 'activo';
    document.getElementById('filtroDesde').value = '';
    document.getElementById('filtroHasta').value = '';
    cargarAlquileres();
}

function formatFechaAlquiler(dateStr) {
    if (!dateStr) return '-';
    const dias = ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'];
    const d = new Date(dateStr + 'T00:00:00');
    return `${dias[d.getDay()]} ${formatFecha(dateStr)}`;
}

// ══════════════════════════════════════════
//  CREAR ALQUILER(ES)
// ══════════════════════════════════════════

function abrirModalNueva() {
    document.getElementById('alMaquina').value = '';
    document.getElementById('alFechaInput').value = '';
    document.getElementById('alHoraInicio').value = '08:00';
    document.getElementById('alHoraFin').value = '20:00';
    document.getElementById('alNotas').value = '';
    alFechasSeleccionadas = [];
    renderFechasChips();
    modalNueva.show();
}

function agregarFecha() {
    const input = document.getElementById('alFechaInput');
    const fecha = input.value;
    if (!fecha) { PM.error('Fecha requerida', 'Selecciona una fecha'); return; }
    if (alFechasSeleccionadas.includes(fecha)) { PM.toast('warning', 'Esa fecha ya esta agregada'); return; }
    alFechasSeleccionadas.push(fecha);
    alFechasSeleccionadas.sort();
    input.value = '';
    renderFechasChips();
}

function quitarFecha(fecha) {
    alFechasSeleccionadas = alFechasSeleccionadas.filter(f => f !== fecha);
    renderFechasChips();
}

function renderFechasChips() {
    const container = document.getElementById('alFechasList');
    if (!alFechasSeleccionadas.length) { container.innerHTML = ''; return; }
    container.innerHTML = alFechasSeleccionadas.map(f => {
        const d = new Date(f + 'T00:00:00');
        const dias = ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'];
        const label = `${dias[d.getDay()]} ${formatFecha(f)}`;
        return `<span class="al-fecha-chip">${label}<button type="button" class="btn-close" onclick="quitarFecha('${f}')"></button></span>`;
    }).join('');
}

async function crearAlquileres() {
    const id_maquina  = document.getElementById('alMaquina').value;
    const hora_inicio = document.getElementById('alHoraInicio').value;
    const hora_fin    = document.getElementById('alHoraFin').value;
    const notas       = document.getElementById('alNotas').value;

    if (!id_maquina) { PM.error('Maquina requerida', 'Selecciona una maquina'); return; }
    if (!alFechasSeleccionadas.length) { PM.error('Fechas requeridas', 'Agrega al menos una fecha'); return; }
    if (hora_inicio >= hora_fin) { PM.error('Horario invalido', 'La hora de inicio debe ser anterior a la de fin'); return; }

    const btn = document.getElementById('alBtnCrear');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Creando...';

    const res = await apiCall('<?= URL_API ?>/admin/alquileres.php', 'POST', {
        id_maquina: parseInt(id_maquina),
        fechas: alFechasSeleccionadas,
        hora_inicio, hora_fin, notas
    });

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Crear Alquiler(es)';

    if (res.success) {
        const data = res.data;
        let msg = `${data.total} alquiler(es) creado(s)`;
        if (data.duplicadas && data.duplicadas.length) {
            msg += `. ${data.duplicadas.length} fecha(s) ya existian y se omitieron.`;
        }
        PM.toast('success', msg);
        modalNueva.hide();
        cargarAlquileres();
    } else {
        PM.error('Error', res.error || 'No se pudieron crear los alquileres');
    }
}

// ══════════════════════════════════════════
//  EDITAR ALQUILER
// ══════════════════════════════════════════

async function abrirEditar(id) {
    const res = await apiCall('<?= URL_API ?>/admin/alquileres.php?id=' + id);
    if (!res.success) { PM.error('Error', res.error); return; }

    const a = res.data;
    document.getElementById('aeId').value = a.id;
    document.getElementById('aeInfo').innerHTML =
        `<span class="al-maq-dot" style="background:${a.maquina_color || '#8A7650'}"></span>` +
        `${escapeHtml(a.maquina)} — ${formatFechaAlquiler(a.fecha)}` +
        (a.citas_reservadas > 0 ? ` <span class="badge bg-primary ms-2">${a.citas_reservadas} citas</span>` : '');
    document.getElementById('aeHoraInicio').value = a.hora_inicio.substring(0, 5);
    document.getElementById('aeHoraFin').value = a.hora_fin.substring(0, 5);
    document.getElementById('aeNotas').value = a.notas || '';
    modalEditar.show();
}

async function guardarEdicion() {
    const id          = document.getElementById('aeId').value;
    const hora_inicio = document.getElementById('aeHoraInicio').value;
    const hora_fin    = document.getElementById('aeHoraFin').value;
    const notas       = document.getElementById('aeNotas').value;

    if (hora_inicio >= hora_fin) { PM.error('Horario invalido', 'La hora de inicio debe ser anterior a la de fin'); return; }

    const res = await apiCall('<?= URL_API ?>/admin/alquileres.php', 'PUT', {
        id: parseInt(id), hora_inicio, hora_fin, notas
    });

    if (res.success) {
        PM.toast('success', 'Alquiler actualizado');
        modalEditar.hide();
        cargarAlquileres();
    } else {
        PM.error('Error', res.error);
    }
}

// ══════════════════════════════════════════
//  ELIMINAR ALQUILER
// ══════════════════════════════════════════

async function eliminarAlquiler(id) {
    const info = await apiCall('<?= URL_API ?>/admin/alquileres.php?id=' + id);
    if (!info.success) { PM.error('Error', info.error); return; }

    const a = info.data;
    const citas = a.citas_afectadas || [];
    const total = citas.length;

    let htmlCitas = '';
    if (total > 0) {
        htmlCitas = `
            <div class="alert alert-warning mt-3 mb-0 py-2 px-3" style="font-size:.82rem">
                <strong><i class="bi bi-exclamation-triangle me-1"></i>${total} cita(s) que seran canceladas:</strong>
                <div class="al-citas-afectadas mt-2">
                    ${citas.map(c => `
                        <div class="al-cita-item">
                            <span>${escapeHtml(c.cliente_nombre + ' ' + c.cliente_apellidos)}</span>
                            <span>${formatHora(c.hora_inicio)} — ${escapeHtml(c.servicio)}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    const result = await Swal.fire({
        title: 'Eliminar alquiler',
        html: `
            <div style="text-align:left">
                <p><strong>${escapeHtml(a.maquina)}</strong> — ${formatFechaAlquiler(a.fecha)}</p>
                <p style="font-size:.85rem">${formatHora(a.hora_inicio)} a ${formatHora(a.hora_fin)}</p>
                ${htmlCitas}
                <p class="mt-3 mb-0" style="font-size:.82rem;color:#A85F52">Esta accion no se puede deshacer.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#A85F52',
        cancelButtonColor: '#8A7650',
        confirmButtonText: total > 0 ? `Eliminar (cancela ${total} cita(s))` : 'Si, eliminar',
        cancelButtonText: 'Volver'
    });

    if (!result.isConfirmed) return;

    const res = await apiCall('<?= URL_API ?>/admin/alquileres.php', 'DELETE', { id });
    if (res.success) {
        PM.toast('success', res.data.mensaje || 'Alquiler eliminado');
        cargarAlquileres();
    } else {
        PM.error('Error', res.error);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
