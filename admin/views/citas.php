<?php
/**
 * Piel Morena — Admin: Gestión de Citas
 */
$titulo_admin = 'Citas';
$extra_css = '
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
<style>
  #calendario { max-height: 600px; }
  .fc .fc-event { cursor: pointer; border: none; padding: 2px 4px; font-size: .78rem; }
  .fc .fc-toolbar-title { font-size: 1.1rem; }
</style>';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<!-- Filtros -->
<div class="pm-panel mb-4">
  <div class="pm-panel-body py-3">
    <div class="row g-2 align-items-end">
      <div class="col-auto">
        <label class="form-label">Fecha</label>
        <input type="date" class="form-control" id="filtroFecha" value="">
      </div>
      <div class="col-auto">
        <label class="form-label">Estado</label>
        <select class="form-select" id="filtroEstado">
          <option value="">Todos</option>
          <option value="pendiente">Pendiente</option>
          <option value="confirmada">Confirmada</option>
          <option value="en_proceso">En proceso</option>
          <option value="completada">Completada</option>
          <option value="cancelada">Cancelada</option>
        </select>
      </div>
      <div class="col-auto">
        <button class="btn btn-pm btn-pm-sm" onclick="cargarCitas()">
          <i class="bi bi-search me-1"></i>Filtrar
        </button>
      </div>
      <div class="col-auto">
        <button class="btn btn-pm-outline btn-pm-sm" onclick="document.getElementById('filtroFecha').value='';document.getElementById('filtroEstado').value='';cargarCitas()">
          <i class="bi bi-x-lg me-1"></i>Limpiar
        </button>
      </div>
      <div class="col-auto ms-auto">
        <div class="btn-group" role="group">
          <button class="btn btn-pm btn-pm-sm active" id="btnVistaTabla" onclick="toggleVista('tabla')">
            <i class="bi bi-table me-1"></i>Tabla
          </button>
          <button class="btn btn-pm-outline btn-pm-sm" id="btnVistaCal" onclick="toggleVista('calendario')">
            <i class="bi bi-calendar3 me-1"></i>Calendario
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Vista Tabla -->
<div id="vistaTabla">
  <div class="pm-panel">
    <div class="pm-panel-header">
      <h3 class="pm-panel-title"><i class="bi bi-calendar-check me-2"></i>Listado de Citas</h3>
    </div>
    <div class="pm-panel-body">
      <table id="dtCitas" class="table table-hover w-100">
        <thead>
          <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Cliente</th>
            <th>Servicio</th>
            <th>Empleado</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Vista Calendario -->
<div id="vistaCalendario" style="display:none">
  <div class="pm-panel">
    <div class="pm-panel-header">
      <h3 class="pm-panel-title"><i class="bi bi-calendar3 me-2"></i>Calendario de Citas</h3>
    </div>
    <div class="pm-panel-body">
      <div id="calendario"></div>
    </div>
  </div>
</div>

<!-- Modal cambiar estado + asignar empleado -->
<div class="modal fade" id="modalEstado" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar Estado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="estadoCitaId">
        <input type="hidden" id="estadoCitaServicioId">
        <p class="mb-3 fw-medium" id="estadoCitaInfo"></p>
        <div class="mb-3">
          <label class="form-label">Estado</label>
          <select class="form-select" id="estadoNuevo">
            <option value="pendiente">Pendiente</option>
            <option value="confirmada">Confirmada</option>
            <option value="en_proceso">En proceso</option>
            <option value="completada">Completada</option>
            <option value="cancelada">Cancelada</option>
          </select>
        </div>
        <div class="mb-3" id="empleadoGroup">
          <label class="form-label">Asignar Empleado</label>
          <select class="form-select" id="estadoEmpleado">
            <option value="">Sin asignar</option>
          </select>
          <div class="form-text">Solo empleados que ofrecen este servicio</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-pm-outline btn-pm-sm" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-pm btn-pm-sm" onclick="guardarEstado()">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal detalle cita (para click desde calendario) -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle de Cita</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="detalleBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-pm-outline btn-pm-sm" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-pm btn-pm-sm" id="detalleBtnEstado">Cambiar Estado</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
let dtCitas;
let modalEstado;
let modalDetalle;
let calendar;
let citasCache = [];

const ESTADO_COLORES = {
    pendiente:   '#f0ad4e',
    confirmada:  '#0d6efd',
    en_proceso:  '#6f42c1',
    completada:  '#198754',
    cancelada:   '#dc3545'
};

document.addEventListener('DOMContentLoaded', () => {
    modalEstado = new bootstrap.Modal(document.getElementById('modalEstado'));
    modalDetalle = new bootstrap.Modal(document.getElementById('modalDetalle'));

    dtCitas = $('#dtCitas').DataTable({
        ...DT_DEFAULTS,
        ajax: {
            url: '<?= URL_API ?>/admin/citas.php',
            data: function(d) {
                d.fecha  = document.getElementById('filtroFecha').value;
                d.estado = document.getElementById('filtroEstado').value;
            },
            dataSrc: (json) => {
                citasCache = json.success ? json.data : [];
                return citasCache;
            }
        },
        columns: [
            { data: 'id' },
            { data: 'fecha', render: (d) => formatFecha(d) },
            { data: null, render: (d) => formatHora(d.hora_inicio) + ' - ' + formatHora(d.hora_fin) },
            { data: 'cliente' },
            { data: 'servicio' },
            { data: 'empleado', render: (d) => d && d.trim() ? d : '<span class="text-muted">—</span>' },
            { data: 'estado', render: (d) => badgeEstado(d) },
            { data: null, orderable: false, render: (d) => `
                <button class="pm-action-btn edit" title="Cambiar estado" onclick="cambiarEstado(${d.id}, '${d.estado}', '${d.cliente} — ${d.servicio}', ${d.id_servicio}, ${d.id_empleado || 'null'})"><i class="bi bi-arrow-repeat"></i></button>
                <button class="pm-action-btn delete" title="Cancelar" onclick="cancelarCita(${d.id}, '${d.cliente}')" ${d.estado === 'cancelada' || d.estado === 'completada' ? 'disabled' : ''}><i class="bi bi-x-circle"></i></button>
            `}
        ],
        order: [[1, 'asc'], [2, 'asc']]
    });

    initCalendario();
});

// ── Toggle vista ──
function toggleVista(vista) {
    if (vista === 'calendario') {
        document.getElementById('vistaTabla').style.display = 'none';
        document.getElementById('vistaCalendario').style.display = '';
        document.getElementById('btnVistaTabla').className = 'btn btn-pm-outline btn-pm-sm';
        document.getElementById('btnVistaCal').className = 'btn btn-pm btn-pm-sm active';
        cargarCalendario();
    } else {
        document.getElementById('vistaTabla').style.display = '';
        document.getElementById('vistaCalendario').style.display = 'none';
        document.getElementById('btnVistaTabla').className = 'btn btn-pm btn-pm-sm active';
        document.getElementById('btnVistaCal').className = 'btn btn-pm-outline btn-pm-sm';
    }
}

// ── FullCalendar ──
function initCalendario() {
    const calEl = document.getElementById('calendario');
    calendar = new FullCalendar.Calendar(calEl, {
        locale: 'es',
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridDay,timeGridWeek'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '21:00:00',
        allDaySlot: false,
        height: 580,
        eventClick: function(info) {
            mostrarDetalle(info.event);
        },
        events: function(fetchInfo, successCallback) {
            // Usar API con rango de fechas del calendario
            const desde = fetchInfo.startStr.slice(0, 10);
            const hasta = fetchInfo.endStr.slice(0, 10);
            apiCall(`<?= URL_API ?>/admin/citas.php?fecha_desde=${desde}&fecha_hasta=${hasta}`)
                .then(res => {
                    if (!res.success) return successCallback([]);
                    const events = res.data.map(c => ({
                        id: c.id,
                        title: c.servicio + ' — ' + c.cliente,
                        start: c.fecha + 'T' + c.hora_inicio,
                        end: c.fecha + 'T' + c.hora_fin,
                        backgroundColor: ESTADO_COLORES[c.estado] || '#6c757d',
                        extendedProps: c
                    }));
                    successCallback(events);
                });
        }
    });
    calendar.render();
}

function cargarCalendario() {
    if (calendar) calendar.refetchEvents();
}

function mostrarDetalle(event) {
    const c = event.extendedProps;
    const emp = c.empleado && c.empleado.trim() ? c.empleado : 'Sin asignar';
    document.getElementById('detalleBody').innerHTML = `
        <div class="mb-2"><strong>${c.servicio}</strong></div>
        <div class="mb-1"><i class="bi bi-person me-1"></i>${c.cliente}</div>
        <div class="mb-1"><i class="bi bi-calendar me-1"></i>${formatFecha(c.fecha)}</div>
        <div class="mb-1"><i class="bi bi-clock me-1"></i>${formatHora(c.hora_inicio)} - ${formatHora(c.hora_fin)}</div>
        <div class="mb-1"><i class="bi bi-person-badge me-1"></i>${emp}</div>
        <div class="mt-2">${badgeEstado(c.estado)}</div>
        ${c.notas ? '<div class="mt-2 text-muted small">' + c.notas + '</div>' : ''}
    `;
    document.getElementById('detalleBtnEstado').onclick = () => {
        modalDetalle.hide();
        cambiarEstado(c.id, c.estado, c.cliente + ' — ' + c.servicio, c.id_servicio, c.id_empleado);
    };
    modalDetalle.show();
}

// ── Tabla ──
function cargarCitas() {
    dtCitas.ajax.reload();
}

async function cambiarEstado(id, estadoActual, info, idServicio, idEmpleado) {
    document.getElementById('estadoCitaId').value = id;
    document.getElementById('estadoCitaServicioId').value = idServicio;
    document.getElementById('estadoCitaInfo').textContent = info;
    document.getElementById('estadoNuevo').value = estadoActual;

    // Cargar empleados para este servicio
    const sel = document.getElementById('estadoEmpleado');
    sel.innerHTML = '<option value="">Sin asignar</option>';
    const res = await apiCall('<?= URL_API ?>/admin/citas.php?empleados_servicio=' + idServicio);
    if (res.success) {
        res.data.forEach(e => {
            sel.innerHTML += `<option value="${e.id}" ${e.id == idEmpleado ? 'selected' : ''}>${e.nombre}</option>`;
        });
    }

    modalEstado.show();
}

async function guardarEstado() {
    const id = document.getElementById('estadoCitaId').value;
    const estado = document.getElementById('estadoNuevo').value;
    const id_empleado = document.getElementById('estadoEmpleado').value || null;

    const res = await apiCall('<?= URL_API ?>/admin/citas.php', 'PATCH', { id, estado, id_empleado });
    if (res.success) {
        PM.toast('success', 'Estado actualizado');
        modalEstado.hide();
        dtCitas.ajax.reload();
        if (calendar) calendar.refetchEvents();
        if (estado === 'completada' && res.data && res.data.caja_registrada) {
            setTimeout(() => PM.toast('success', 'Ingreso registrado en caja automáticamente'), 500);
        }
    } else {
        PM.error('Error', res.error);
    }
}

async function cancelarCita(id, cliente) {
    if (!await PM.confirm('¿Cancelar esta cita?', cliente)) return;

    const res = await apiCall('<?= URL_API ?>/admin/citas.php', 'PATCH', { id, estado: 'cancelada' });
    if (res.success) {
        PM.toast('success', 'Cita cancelada');
        dtCitas.ajax.reload();
        if (calendar) calendar.refetchEvents();
    } else {
        PM.error('Error', res.error);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
