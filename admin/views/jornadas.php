<?php
/**
 * Piel Morena — Admin: Gestión de Jornadas
 * Calendario + tabla de jornadas por categoría
 */
$titulo_admin = 'Jornadas';
$extra_css = '
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
<style>
  #calJornadas { max-height: 620px; }
  .fc .fc-daygrid-event { cursor: pointer; border: none; padding: 2px 6px; font-size: .75rem; border-radius: 4px; }
  .fc .fc-toolbar-title { font-size: 1.1rem; }
  .fc .fc-day-today { background: rgba(138,118,80,.06) !important; }

  /* Badge estado jornada */
  .badge-jornada-activa  { background: #198754; color: #fff; font-size: .72rem; padding: 3px 8px; border-radius: 6px; }
  .badge-jornada-cancelada { background: #dc3545; color: #fff; font-size: .72rem; padding: 3px 8px; border-radius: 6px; }

  /* Selector fechas en modal */
  .jn-fechas-list { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .5rem; }
  .jn-fecha-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    background: var(--pm-crema, #ECE7D1); color: var(--pm-texto, #3F352B);
    padding: 4px 10px; border-radius: 20px; font-size: .82rem; font-weight: 500;
  }
  .jn-fecha-chip .btn-close { font-size: .55rem; filter: none; opacity: .6; }
  .jn-fecha-chip .btn-close:hover { opacity: 1; }

  /* Citas afectadas en cancelación */
  .jn-citas-afectadas { max-height: 200px; overflow-y: auto; }
  .jn-cita-item { display: flex; justify-content: space-between; padding: .4rem .6rem; border-bottom: 1px solid #eee; font-size: .82rem; }

  /* Cat color dots */
  .jn-cat-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 6px; }
</style>';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<!-- Filtros -->
<div class="pm-panel mb-4">
  <div class="pm-panel-body py-3">
    <div class="row g-2 align-items-end">
      <div class="col-auto">
        <label class="form-label">Categoría</label>
        <select class="form-select" id="filtroCategoria">
          <option value="">Todas</option>
        </select>
      </div>
      <div class="col-auto">
        <label class="form-label">Estado</label>
        <select class="form-select" id="filtroEstado">
          <option value="">Todos</option>
          <option value="activa" selected>Activa</option>
          <option value="cancelada">Cancelada</option>
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
        <button class="btn btn-pm btn-pm-sm" onclick="cargarJornadas()">
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
      <h3 class="pm-panel-title"><i class="bi bi-calendar-event me-2"></i>Calendario de Jornadas</h3>
      <button class="btn btn-pm btn-pm-sm" onclick="abrirModalNueva()">
        <i class="bi bi-plus-circle me-1"></i>Nueva Jornada
      </button>
    </div>
    <div class="pm-panel-body">
      <div id="calJornadas"></div>
    </div>
  </div>
</div>

<!-- Vista Tabla -->
<div id="vistaTabla" style="display:none">
  <div class="pm-panel">
    <div class="pm-panel-header">
      <h3 class="pm-panel-title"><i class="bi bi-calendar-event me-2"></i>Listado de Jornadas</h3>
      <button class="btn btn-pm btn-pm-sm" onclick="abrirModalNueva()">
        <i class="bi bi-plus-circle me-1"></i>Nueva Jornada
      </button>
    </div>
    <div class="pm-panel-body">
      <table id="dtJornadas" class="table table-hover w-100">
        <thead>
          <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Categoría</th>
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

<!-- Modal: Nueva Jornada -->
<div class="modal fade" id="modalNueva" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Nueva Jornada</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Categoría <span class="text-danger">*</span></label>
          <select class="form-select" id="jnCategoria" required>
            <option value="">Seleccionar categoría...</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Fechas <span class="text-danger">*</span></label>
          <div class="input-group">
            <input type="date" class="form-control" id="jnFechaInput">
            <button class="btn btn-pm-outline" type="button" onclick="agregarFecha()">
              <i class="bi bi-plus-lg"></i> Agregar
            </button>
          </div>
          <div class="form-text">Podés agregar múltiples fechas para una misma categoría</div>
          <div class="jn-fechas-list" id="jnFechasList"></div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="form-label">Hora inicio</label>
            <input type="time" class="form-control" id="jnHoraInicio" value="08:00">
          </div>
          <div class="col-6">
            <label class="form-label">Hora fin</label>
            <input type="time" class="form-control" id="jnHoraFin" value="20:00">
          </div>
        </div>
        <div class="mb-0">
          <label class="form-label">Notas</label>
          <textarea class="form-control" id="jnNotas" rows="2" placeholder="Observaciones opcionales..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-pm-outline btn-pm-sm" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-pm btn-pm-sm" id="jnBtnCrear" onclick="crearJornadas()">
          <i class="bi bi-check-lg me-1"></i>Crear Jornada(s)
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Editar Jornada -->
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Jornada</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="jeId">
        <p class="mb-3 fw-medium" id="jeInfo"></p>
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="form-label">Hora inicio</label>
            <input type="time" class="form-control" id="jeHoraInicio">
          </div>
          <div class="col-6">
            <label class="form-label">Hora fin</label>
            <input type="time" class="form-control" id="jeHoraFin">
          </div>
        </div>
        <div class="mb-0">
          <label class="form-label">Notas</label>
          <textarea class="form-control" id="jeNotas" rows="2"></textarea>
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
let dtJornadas;
let calendar;
let modalNueva, modalEditar;
let categoriasCache = [];
let jnFechasSeleccionadas = [];

// Colores por categoría (se asignan dinámicamente)
const CAT_COLORES = ['#8A7650', '#B77466', '#8E977D', '#6f42c1', '#0d6efd', '#e67e22', '#1abc9c', '#c0392b'];

function getColorCategoria(idCat) {
    const idx = categoriasCache.findIndex(c => c.id == idCat);
    return CAT_COLORES[idx >= 0 ? idx % CAT_COLORES.length : 0];
}

function getNombreCategoria(idCat) {
    const cat = categoriasCache.find(c => c.id == idCat);
    return cat ? cat.nombre : 'Categoría';
}

// ── Init ──
document.addEventListener('DOMContentLoaded', async () => {
    modalNueva  = new bootstrap.Modal(document.getElementById('modalNueva'));
    modalEditar = new bootstrap.Modal(document.getElementById('modalEditar'));

    // Set min date para input de fecha
    document.getElementById('jnFechaInput').min = new Date().toISOString().slice(0, 10);

    // Cargar categorías con jornada
    await cargarCategorias();

    // Init DataTable
    dtJornadas = $('#dtJornadas').DataTable({
        ...DT_DEFAULTS,
        ajax: {
            url: '<?= URL_API ?>/admin/jornadas.php',
            data: function(d) {
                d.id_categoria = document.getElementById('filtroCategoria').value;
                d.estado       = document.getElementById('filtroEstado').value;
                d.fecha_desde  = document.getElementById('filtroDesde').value;
                d.fecha_hasta  = document.getElementById('filtroHasta').value;
            },
            dataSrc: (json) => json.success ? json.data : []
        },
        columns: [
            { data: 'id' },
            { data: 'fecha', render: (d) => formatFechaJornada(d) },
            { data: null, render: (d) => `<span class="jn-cat-dot" style="background:${getColorCategoria(d.id_categoria)}"></span>${d.categoria}` },
            { data: null, render: (d) => formatHora(d.hora_inicio) + ' — ' + formatHora(d.hora_fin) },
            { data: 'citas_reservadas', render: (d) => d > 0 ? `<span class="badge bg-primary">${d}</span>` : '<span class="text-muted">0</span>' },
            { data: 'estado', render: (d) => `<span class="badge-jornada-${d}">${d === 'activa' ? 'Activa' : 'Cancelada'}</span>` },
            { data: 'notas', render: (d) => d ? `<span title="${d}">${d.substring(0, 30)}${d.length > 30 ? '...' : ''}</span>` : '<span class="text-muted">—</span>' },
            { data: null, orderable: false, render: (d) => {
                if (d.estado === 'cancelada') return '<span class="text-muted">—</span>';
                return `
                    <button class="pm-action-btn edit" title="Editar" onclick="abrirEditar(${d.id})"><i class="bi bi-pencil"></i></button>
                    <button class="pm-action-btn delete" title="Cancelar" onclick="cancelarJornada(${d.id})"><i class="bi bi-x-circle"></i></button>
                `;
            }}
        ],
        order: [[1, 'asc']]
    });

    // Init Calendar
    initCalendario();
});

// ── Cargar categorías ──
async function cargarCategorias() {
    const res = await apiCall('<?= URL_API ?>/admin/jornadas.php?categorias_jornada=1');
    if (!res.success) return;

    categoriasCache = res.data;

    // Poblar filtro
    const filtro = document.getElementById('filtroCategoria');
    const modal  = document.getElementById('jnCategoria');
    categoriasCache.forEach(c => {
        filtro.innerHTML += `<option value="${c.id}">${c.nombre}</option>`;
        modal.innerHTML  += `<option value="${c.id}">${c.nombre}</option>`;
    });
}

// ── FullCalendar ──
function initCalendario() {
    const calEl = document.getElementById('calJornadas');
    calendar = new FullCalendar.Calendar(calEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth'
        },
        height: 600,
        dayMaxEvents: 3,
        eventClick: function(info) {
            const j = info.event.extendedProps;
            if (j.estado === 'cancelada') return;
            abrirEditar(j.jornada_id);
        },
        events: function(fetchInfo, successCallback) {
            const desde = fetchInfo.startStr.slice(0, 10);
            const hasta = fetchInfo.endStr.slice(0, 10);
            const estado = document.getElementById('filtroEstado').value;
            const cat    = document.getElementById('filtroCategoria').value;

            let url = `<?= URL_API ?>/admin/jornadas.php?fecha_desde=${desde}&fecha_hasta=${hasta}`;
            if (estado) url += `&estado=${estado}`;
            if (cat)    url += `&id_categoria=${cat}`;

            apiCall(url).then(res => {
                if (!res.success) return successCallback([]);
                const events = res.data.map(j => ({
                    id: j.id,
                    title: j.categoria + (j.citas_reservadas > 0 ? ` (${j.citas_reservadas} citas)` : ''),
                    start: j.fecha,
                    allDay: true,
                    backgroundColor: j.estado === 'cancelada' ? '#ccc' : getColorCategoria(j.id_categoria),
                    textColor: j.estado === 'cancelada' ? '#888' : '#fff',
                    extendedProps: { ...j, jornada_id: j.id }
                }));
                successCallback(events);
            });
        }
    });
    calendar.render();
}

// ── Toggle vista ──
function toggleVista(vista) {
    if (vista === 'tabla') {
        document.getElementById('vistaTabla').style.display = '';
        document.getElementById('vistaCalendario').style.display = 'none';
        document.getElementById('btnVistaTabla').className = 'btn btn-pm btn-pm-sm active';
        document.getElementById('btnVistaCal').className = 'btn btn-pm-outline btn-pm-sm';
        dtJornadas.ajax.reload();
    } else {
        document.getElementById('vistaCalendario').style.display = '';
        document.getElementById('vistaTabla').style.display = 'none';
        document.getElementById('btnVistaCal').className = 'btn btn-pm btn-pm-sm active';
        document.getElementById('btnVistaTabla').className = 'btn btn-pm-outline btn-pm-sm';
        if (calendar) calendar.refetchEvents();
    }
}

// ── Filtros ──
function cargarJornadas() {
    dtJornadas.ajax.reload();
    if (calendar) calendar.refetchEvents();
}

function limpiarFiltros() {
    document.getElementById('filtroCategoria').value = '';
    document.getElementById('filtroEstado').value = 'activa';
    document.getElementById('filtroDesde').value = '';
    document.getElementById('filtroHasta').value = '';
    cargarJornadas();
}

// ── Formato fecha con día ──
function formatFechaJornada(dateStr) {
    if (!dateStr) return '-';
    const dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    const d = new Date(dateStr + 'T00:00:00');
    return `${dias[d.getDay()]} ${formatFecha(dateStr)}`;
}

// ══════════════════════════════════════════
//  CREAR JORNADA(S)
// ══════════════════════════════════════════

function abrirModalNueva() {
    document.getElementById('jnCategoria').value = '';
    document.getElementById('jnFechaInput').value = '';
    document.getElementById('jnHoraInicio').value = '08:00';
    document.getElementById('jnHoraFin').value = '20:00';
    document.getElementById('jnNotas').value = '';
    jnFechasSeleccionadas = [];
    renderFechasChips();
    modalNueva.show();
}

function agregarFecha() {
    const input = document.getElementById('jnFechaInput');
    const fecha = input.value;
    if (!fecha) { PM.error('Fecha requerida', 'Seleccioná una fecha'); return; }
    if (jnFechasSeleccionadas.includes(fecha)) { PM.toast('warning', 'Esa fecha ya está agregada'); return; }
    jnFechasSeleccionadas.push(fecha);
    jnFechasSeleccionadas.sort();
    input.value = '';
    renderFechasChips();
}

function quitarFecha(fecha) {
    jnFechasSeleccionadas = jnFechasSeleccionadas.filter(f => f !== fecha);
    renderFechasChips();
}

function renderFechasChips() {
    const container = document.getElementById('jnFechasList');
    if (!jnFechasSeleccionadas.length) {
        container.innerHTML = '';
        return;
    }
    container.innerHTML = jnFechasSeleccionadas.map(f => {
        const d = new Date(f + 'T00:00:00');
        const dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        const label = `${dias[d.getDay()]} ${formatFecha(f)}`;
        return `<span class="jn-fecha-chip">${label}<button type="button" class="btn-close" onclick="quitarFecha('${f}')"></button></span>`;
    }).join('');
}

async function crearJornadas() {
    const id_categoria = document.getElementById('jnCategoria').value;
    const hora_inicio  = document.getElementById('jnHoraInicio').value;
    const hora_fin     = document.getElementById('jnHoraFin').value;
    const notas        = document.getElementById('jnNotas').value;

    if (!id_categoria) { PM.error('Categoría requerida', 'Seleccioná una categoría'); return; }
    if (!jnFechasSeleccionadas.length) { PM.error('Fechas requeridas', 'Agregá al menos una fecha'); return; }
    if (hora_inicio >= hora_fin) { PM.error('Horario inválido', 'La hora de inicio debe ser anterior a la de fin'); return; }

    const btn = document.getElementById('jnBtnCrear');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Creando...';

    const res = await apiCall('<?= URL_API ?>/admin/jornadas.php', 'POST', {
        id_categoria: parseInt(id_categoria),
        fechas: jnFechasSeleccionadas,
        hora_inicio,
        hora_fin,
        notas
    });

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Crear Jornada(s)';

    if (res.success) {
        const data = res.data;
        let msg = `${data.total} jornada(s) creada(s)`;
        if (data.duplicadas && data.duplicadas.length) {
            msg += `. ${data.duplicadas.length} fecha(s) ya existían y se omitieron.`;
        }
        PM.toast('success', msg);
        modalNueva.hide();
        dtJornadas.ajax.reload();
        if (calendar) calendar.refetchEvents();
    } else {
        PM.error('Error', res.error || 'No se pudieron crear las jornadas');
    }
}

// ══════════════════════════════════════════
//  EDITAR JORNADA
// ══════════════════════════════════════════

async function abrirEditar(id) {
    const res = await apiCall('<?= URL_API ?>/admin/jornadas.php?id=' + id);
    if (!res.success) { PM.error('Error', res.error); return; }

    const j = res.data;
    document.getElementById('jeId').value = j.id;
    document.getElementById('jeInfo').innerHTML =
        `<span class="jn-cat-dot" style="background:${getColorCategoria(j.id_categoria)}"></span>` +
        `${j.categoria} — ${formatFechaJornada(j.fecha)}` +
        (j.citas_reservadas > 0 ? ` <span class="badge bg-primary ms-2">${j.citas_reservadas} citas</span>` : '');
    document.getElementById('jeHoraInicio').value = j.hora_inicio.substring(0, 5);
    document.getElementById('jeHoraFin').value = j.hora_fin.substring(0, 5);
    document.getElementById('jeNotas').value = j.notas || '';
    modalEditar.show();
}

async function guardarEdicion() {
    const id          = document.getElementById('jeId').value;
    const hora_inicio = document.getElementById('jeHoraInicio').value;
    const hora_fin    = document.getElementById('jeHoraFin').value;
    const notas       = document.getElementById('jeNotas').value;

    if (hora_inicio >= hora_fin) { PM.error('Horario inválido', 'La hora de inicio debe ser anterior a la de fin'); return; }

    const res = await apiCall('<?= URL_API ?>/admin/jornadas.php', 'PUT', {
        id: parseInt(id), hora_inicio, hora_fin, notas
    });

    if (res.success) {
        PM.toast('success', 'Jornada actualizada');
        modalEditar.hide();
        dtJornadas.ajax.reload();
        if (calendar) calendar.refetchEvents();
    } else {
        PM.error('Error', res.error);
    }
}

// ══════════════════════════════════════════
//  CANCELAR JORNADA
// ══════════════════════════════════════════

async function cancelarJornada(id) {
    // Paso 1: obtener info de citas afectadas
    const info = await apiCall('<?= URL_API ?>/admin/jornadas.php', 'PATCH', { id, solo_info: true });
    if (!info.success) { PM.error('Error', info.error); return; }

    const j = info.data.jornada;
    const citas = info.data.citas_afectadas;
    const total = info.data.total_afectadas;

    let htmlCitas = '';
    if (total > 0) {
        htmlCitas = `
            <div class="alert alert-warning mt-3 mb-0 py-2 px-3" style="font-size:.82rem">
                <strong><i class="bi bi-exclamation-triangle me-1"></i>${total} cita(s) afectada(s):</strong>
                <div class="jn-citas-afectadas mt-2">
                    ${citas.map(c => `
                        <div class="jn-cita-item">
                            <span>${c.cliente_nombre} ${c.cliente_apellidos}</span>
                            <span>${formatHora(c.hora_inicio)} — ${c.servicio}</span>
                        </div>
                    `).join('')}
                </div>
                <div class="mt-2 text-muted" style="font-size:.75rem">Las citas NO se cancelan automáticamente. Deberás gestionarlas manualmente.</div>
            </div>
        `;
    }

    // Paso 2: confirmar
    const result = await Swal.fire({
        title: 'Cancelar jornada',
        html: `
            <div style="text-align:left">
                <p><strong>${j.categoria}</strong> — ${formatFechaJornada(j.fecha)}</p>
                <p style="font-size:.85rem">${formatHora(j.hora_inicio)} a ${formatHora(j.hora_fin)}</p>
                ${htmlCitas}
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#A85F52',
        cancelButtonColor: '#8A7650',
        confirmButtonText: total > 0 ? `Cancelar jornada (${total} citas afectadas)` : 'Sí, cancelar',
        cancelButtonText: 'Volver'
    });

    if (!result.isConfirmed) return;

    // Paso 3: cancelar
    const res = await apiCall('<?= URL_API ?>/admin/jornadas.php', 'PATCH', { id });
    if (res.success) {
        PM.toast('success', res.data.mensaje || 'Jornada cancelada');
        dtJornadas.ajax.reload();
        if (calendar) calendar.refetchEvents();
    } else {
        PM.error('Error', res.error);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
