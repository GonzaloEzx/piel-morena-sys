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
      <button class="btn btn-pm btn-pm-sm" onclick="abrirModalNuevaCita()">
        <i class="bi bi-plus-circle me-1"></i>Nueva Cita
      </button>
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

<!-- Modal: Nueva Cita -->
<div class="modal fade" id="modalNuevaCita" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Nueva Cita</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formNuevaCita" novalidate>
        <div class="modal-body">

          <!-- Section: Cliente -->
          <div class="nc-section">
            <div class="nc-section-label"><i class="bi bi-person"></i><span>Cliente</span></div>
            <div class="nc-client-toggle mb-3">
              <button type="button" class="nc-toggle-btn active" data-mode="existente" onclick="toggleClienteMode('existente')">Cliente existente</button>
              <button type="button" class="nc-toggle-btn" data-mode="nuevo" onclick="toggleClienteMode('nuevo')">Cliente nuevo</button>
            </div>
            <div id="ncClienteExistente">
              <div class="position-relative">
                <input type="text" class="form-control" id="ncBuscarCliente" placeholder="Buscar por nombre, email o teléfono..." autocomplete="off">
                <i class="bi bi-search nc-search-icon"></i>
                <div id="ncClienteResultados" class="nc-autocomplete-list"></div>
              </div>
              <input type="hidden" id="ncIdCliente">
              <div id="ncClienteSeleccionado" class="nc-selected-client" style="display:none">
                <div class="nc-selected-client-info">
                  <strong id="ncClienteNombre"></strong>
                  <span id="ncClienteDetalle" class="text-muted"></span>
                </div>
                <button type="button" class="nc-selected-client-clear" onclick="limpiarCliente()" title="Cambiar cliente"><i class="bi bi-x-lg"></i></button>
              </div>
            </div>
            <div id="ncClienteNuevo" style="display:none">
              <div class="row g-3">
                <div class="col-sm-6">
                  <label class="form-label">Nombre <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="ncNuevoNombre" placeholder="Nombre completo">
                </div>
                <div class="col-sm-6">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" id="ncNuevoEmail" placeholder="correo@ejemplo.com">
                </div>
                <div class="col-sm-6">
                  <label class="form-label">Teléfono</label>
                  <input type="tel" class="form-control" id="ncNuevoTelefono" placeholder="+54 362 400 0000">
                </div>
              </div>
            </div>
          </div>

          <!-- Section: Servicio -->
          <div class="nc-section">
            <div class="nc-section-label"><i class="bi bi-stars"></i><span>Servicio</span></div>
            <select class="form-select" id="ncServicio" required>
              <option value="">Seleccionar servicio...</option>
            </select>
            <div id="ncServicioInfo" class="nc-service-info" style="display:none">
              <span class="nc-service-info-item"><i class="bi bi-tag"></i><span id="ncServicioPrecio"></span></span>
              <span class="nc-service-info-divider"></span>
              <span class="nc-service-info-item"><i class="bi bi-clock"></i><span id="ncServicioDuracion"></span></span>
            </div>
          </div>

          <!-- Section: Fecha y Hora -->
          <div class="nc-section">
            <div class="nc-section-label"><i class="bi bi-calendar3"></i><span>Fecha y Hora</span></div>
            <div class="row g-3">
              <div class="col-sm-5">
                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="ncFecha" required>
              </div>
              <div class="col-sm-7">
                <label class="form-label">Horario disponible</label>
                <div id="ncTurnosContainer">
                  <div class="nc-turnos-placeholder">Selecciona servicio y fecha para ver horarios</div>
                </div>
              </div>
            </div>
            <input type="hidden" id="ncHoraInicio">
          </div>

          <!-- Section: Empleado & Notas -->
          <div class="nc-section nc-section-last">
            <div class="nc-section-label"><i class="bi bi-person-badge"></i><span>Asignación</span></div>
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label">Empleado</label>
                <select class="form-select" id="ncEmpleado">
                  <option value="">Sin asignar</option>
                </select>
                <div class="form-text">Se asigna según disponibilidad</div>
              </div>
              <div class="col-sm-6">
                <label class="form-label">Notas</label>
                <textarea class="form-control" id="ncNotas" rows="2" placeholder="Observaciones opcionales..."></textarea>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-pm-outline btn-pm-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-pm btn-pm-sm" id="ncBtnGuardar"><i class="bi bi-check-lg me-1"></i>Agendar Cita</button>
        </div>
      </form>
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

// ══════════════════════════════════════════
//  NUEVA CITA — Modal logic
// ══════════════════════════════════════════
let modalNuevaCita;
let ncServiciosCache = [];
let ncClienteMode = 'existente';
let ncBuscarTimeout = null;

document.addEventListener('DOMContentLoaded', () => {
    modalNuevaCita = new bootstrap.Modal(document.getElementById('modalNuevaCita'));

    // Cargar servicios al abrir modal
    document.getElementById('modalNuevaCita').addEventListener('show.bs.modal', cargarServiciosNC);

    // Servicio change → mostrar info + cargar empleados
    document.getElementById('ncServicio').addEventListener('change', onServicioChange);

    // Fecha change → cargar turnos
    document.getElementById('ncFecha').addEventListener('change', cargarTurnos);

    // Buscar cliente
    document.getElementById('ncBuscarCliente').addEventListener('input', onBuscarCliente);

    // Cerrar autocomplete al hacer click fuera
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#ncClienteExistente')) {
            document.getElementById('ncClienteResultados').classList.remove('show');
        }
    });

    // Form submit
    document.getElementById('formNuevaCita').addEventListener('submit', guardarNuevaCita);
});

function abrirModalNuevaCita() {
    // Reset form
    document.getElementById('formNuevaCita').reset();
    document.getElementById('ncIdCliente').value = '';
    document.getElementById('ncHoraInicio').value = '';
    document.getElementById('ncClienteSeleccionado').style.display = 'none';
    document.getElementById('ncBuscarCliente').style.display = '';
    document.getElementById('ncBuscarCliente').value = '';
    document.getElementById('ncServicioInfo').style.display = 'none';
    document.getElementById('ncTurnosContainer').innerHTML = '<div class="nc-turnos-placeholder">Selecciona servicio y fecha para ver horarios</div>';
    document.getElementById('ncEmpleado').innerHTML = '<option value="">Sin asignar</option>';
    toggleClienteMode('existente');

    // Set min date to today
    document.getElementById('ncFecha').min = new Date().toISOString().slice(0, 10);

    modalNuevaCita.show();
}

// ── Cliente toggle ──
function toggleClienteMode(mode) {
    ncClienteMode = mode;
    document.querySelectorAll('.nc-toggle-btn').forEach(b => {
        b.classList.toggle('active', b.dataset.mode === mode);
    });
    document.getElementById('ncClienteExistente').style.display = mode === 'existente' ? '' : 'none';
    document.getElementById('ncClienteNuevo').style.display = mode === 'nuevo' ? '' : 'none';
    if (mode === 'nuevo') {
        document.getElementById('ncIdCliente').value = '';
        document.getElementById('ncClienteSeleccionado').style.display = 'none';
        document.getElementById('ncBuscarCliente').style.display = '';
        document.getElementById('ncBuscarCliente').value = '';
    }
}

// ── Buscar cliente (autocomplete) ──
function onBuscarCliente() {
    clearTimeout(ncBuscarTimeout);
    const q = document.getElementById('ncBuscarCliente').value.trim();
    const lista = document.getElementById('ncClienteResultados');

    if (q.length < 2) {
        lista.classList.remove('show');
        return;
    }

    ncBuscarTimeout = setTimeout(async () => {
        const res = await apiCall('<?= URL_API ?>/admin/citas.php?buscar_clientes=' + encodeURIComponent(q));
        if (!res.success || !res.data.length) {
            lista.innerHTML = '<div class="nc-autocomplete-empty">No se encontraron clientes</div>';
            lista.classList.add('show');
            return;
        }
        lista.innerHTML = res.data.map(c => `
            <div class="nc-autocomplete-item" onclick="seleccionarCliente(${c.id}, '${(c.nombre + ' ' + c.apellidos).replace(/'/g, "\\'")}', '${c.email || ''}', '${c.telefono || ''}')">
                <strong>${c.nombre} ${c.apellidos}</strong>
                <span>${c.email || ''} ${c.telefono ? '· ' + c.telefono : ''}</span>
            </div>
        `).join('');
        lista.classList.add('show');
    }, 300);
}

function seleccionarCliente(id, nombre, email, telefono) {
    document.getElementById('ncIdCliente').value = id;
    document.getElementById('ncClienteNombre').textContent = nombre;
    document.getElementById('ncClienteDetalle').textContent = [email, telefono].filter(Boolean).join(' · ');
    document.getElementById('ncClienteSeleccionado').style.display = 'flex';
    document.getElementById('ncBuscarCliente').style.display = 'none';
    document.getElementById('ncClienteResultados').classList.remove('show');
}

function limpiarCliente() {
    document.getElementById('ncIdCliente').value = '';
    document.getElementById('ncClienteSeleccionado').style.display = 'none';
    document.getElementById('ncBuscarCliente').style.display = '';
    document.getElementById('ncBuscarCliente').value = '';
    document.getElementById('ncBuscarCliente').focus();
}

// ── Cargar servicios ──
async function cargarServiciosNC() {
    if (ncServiciosCache.length) return;
    const res = await apiCall('<?= URL_API ?>/admin/citas.php?servicios_activos=1');
    if (res.success) {
        ncServiciosCache = res.data;
        const sel = document.getElementById('ncServicio');
        sel.innerHTML = '<option value="">Seleccionar servicio...</option>';
        res.data.forEach(s => {
            sel.innerHTML += `<option value="${s.id}" data-precio="${s.precio}" data-duracion="${s.duracion_minutos}">${s.nombre}</option>`;
        });
    }
}

function onServicioChange() {
    const sel = document.getElementById('ncServicio');
    const opt = sel.options[sel.selectedIndex];
    const info = document.getElementById('ncServicioInfo');

    if (!sel.value) {
        info.style.display = 'none';
        document.getElementById('ncEmpleado').innerHTML = '<option value="">Sin asignar</option>';
        return;
    }

    document.getElementById('ncServicioPrecio').textContent = formatPrecio(opt.dataset.precio);
    document.getElementById('ncServicioDuracion').textContent = opt.dataset.duracion + ' min';
    info.style.display = 'inline-flex';

    // Cargar empleados para este servicio
    cargarEmpleadosNC(sel.value);

    // Recargar turnos si ya hay fecha
    if (document.getElementById('ncFecha').value) cargarTurnos();
}

async function cargarEmpleadosNC(idServicio) {
    const sel = document.getElementById('ncEmpleado');
    sel.innerHTML = '<option value="">Sin asignar</option>';
    const res = await apiCall('<?= URL_API ?>/admin/citas.php?empleados_servicio=' + idServicio);
    if (res.success) {
        res.data.forEach(e => {
            sel.innerHTML += `<option value="${e.id}">${e.nombre}</option>`;
        });
    }
}

// ── Cargar turnos disponibles ──
async function cargarTurnos() {
    const fecha = document.getElementById('ncFecha').value;
    const idServicio = document.getElementById('ncServicio').value;
    const container = document.getElementById('ncTurnosContainer');

    if (!fecha || !idServicio) {
        container.innerHTML = '<div class="nc-turnos-placeholder">Selecciona servicio y fecha para ver horarios</div>';
        return;
    }

    container.innerHTML = '<div class="nc-turnos-loading"><span class="spinner-border"></span>Cargando horarios...</div>';
    document.getElementById('ncHoraInicio').value = '';

    const res = await apiCall(`<?= URL_API ?>/citas/disponibilidad.php?fecha=${fecha}&id_servicio=${idServicio}`);

    if (!res.success) {
        container.innerHTML = '<div class="nc-turnos-empty">' + (res.error || 'Error al cargar horarios') + '</div>';
        return;
    }

    if (res.data.mensaje) {
        container.innerHTML = '<div class="nc-turnos-empty">' + res.data.mensaje + '</div>';
        return;
    }

    if (!res.data.turnos || !res.data.turnos.length) {
        container.innerHTML = '<div class="nc-turnos-empty">No hay horarios disponibles para esta fecha</div>';
        return;
    }

    container.innerHTML = '<div class="nc-turnos-grid">' +
        res.data.turnos.map(t =>
            `<button type="button" class="nc-turno-pill" data-hora="${t.hora_inicio}" onclick="seleccionarTurno(this)">${formatHora(t.hora_inicio)}</button>`
        ).join('') + '</div>';
}

function seleccionarTurno(btn) {
    document.querySelectorAll('.nc-turno-pill').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('ncHoraInicio').value = btn.dataset.hora;
}

// ── Guardar nueva cita ──
async function guardarNuevaCita(e) {
    e.preventDefault();

    const data = {
        id_servicio: document.getElementById('ncServicio').value,
        fecha: document.getElementById('ncFecha').value,
        hora_inicio: document.getElementById('ncHoraInicio').value,
        id_empleado: document.getElementById('ncEmpleado').value || null,
        notas: document.getElementById('ncNotas').value,
    };

    // Cliente
    if (ncClienteMode === 'existente') {
        data.id_cliente = document.getElementById('ncIdCliente').value;
        if (!data.id_cliente) {
            PM.error('Cliente requerido', 'Busca y selecciona un cliente existente');
            return;
        }
    } else {
        data.nombre = document.getElementById('ncNuevoNombre').value;
        data.email = document.getElementById('ncNuevoEmail').value;
        data.telefono = document.getElementById('ncNuevoTelefono').value;
        if (!data.nombre) {
            PM.error('Nombre requerido', 'Ingresa el nombre del cliente');
            return;
        }
    }

    // Validaciones
    if (!data.id_servicio) { PM.error('Servicio requerido', 'Selecciona un servicio'); return; }
    if (!data.fecha) { PM.error('Fecha requerida', 'Selecciona una fecha'); return; }
    if (!data.hora_inicio) { PM.error('Hora requerida', 'Selecciona un horario disponible'); return; }

    const btn = document.getElementById('ncBtnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Agendando...';

    const res = await apiCall('<?= URL_API ?>/admin/citas.php', 'POST', data);

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Agendar Cita';

    if (res.success) {
        PM.toast('success', 'Cita agendada correctamente');
        modalNuevaCita.hide();
        dtCitas.ajax.reload();
        if (calendar) calendar.refetchEvents();
    } else {
        PM.error('Error', res.error || 'No se pudo crear la cita');
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
