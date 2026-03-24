<?php
/**
 * Piel Morena — Empleado: Mis Citas
 */
$titulo_admin = 'Mis Citas';
$rol_admin_requerido = 'empleado';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<!-- Filtros -->
<div class="pm-panel mb-4">
  <div class="pm-panel-body py-3">
    <div class="row g-2 align-items-end">
      <div class="col-auto">
        <label class="form-label">Fecha</label>
        <input type="date" class="form-control" id="filtroFecha" value="<?= date('Y-m-d') ?>">
      </div>
      <div class="col-auto">
        <label class="form-label">Estado</label>
        <select class="form-select" id="filtroEstado">
          <option value="">Todos</option>
          <option value="pendiente">Pendiente</option>
          <option value="confirmada">Confirmada</option>
          <option value="en_proceso">En proceso</option>
          <option value="completada">Completada</option>
        </select>
      </div>
      <div class="col-auto">
        <button class="btn btn-pm btn-pm-sm" onclick="cargarMisCitas()">
          <i class="bi bi-search me-1"></i>Filtrar
        </button>
      </div>
      <div class="col-auto">
        <button class="btn btn-pm-outline btn-pm-sm" onclick="verHoy()">
          <i class="bi bi-calendar-day me-1"></i>Hoy
        </button>
      </div>
    </div>
  </div>
</div>

<div class="pm-panel">
  <div class="pm-panel-header">
    <h3 class="pm-panel-title"><i class="bi bi-calendar-check me-2"></i>Citas Asignadas</h3>
    <button class="btn btn-pm btn-pm-sm" onclick="abrirModalNuevaCita()">
      <i class="bi bi-plus-circle me-1"></i>Nueva Cita
    </button>
  </div>
  <div class="pm-panel-body">
    <table id="dtMisCitas" class="table table-hover w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Cliente</th>
          <th>Tel.</th>
          <th>Servicio</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<script>
let dtMisCitas;

document.addEventListener('DOMContentLoaded', () => {
    dtMisCitas = $('#dtMisCitas').DataTable({
        ...DT_DEFAULTS,
        ajax: {
            url: '<?= URL_API ?>/admin/mis-citas.php',
            data: function(d) {
                d.fecha  = document.getElementById('filtroFecha').value;
                d.estado = document.getElementById('filtroEstado').value;
            },
            dataSrc: (json) => json.success ? json.data : []
        },
        columns: [
            { data: 'id' },
            { data: 'fecha', render: (d) => formatFecha(d) },
            { data: null, render: (d) => formatHora(d.hora_inicio) + ' - ' + formatHora(d.hora_fin) },
            { data: 'cliente' },
            { data: 'cliente_tel', render: (d) => d ? `<a href="tel:${d}">${d}</a>` : '—' },
            { data: 'servicio' },
            { data: 'estado', render: (d) => badgeEstado(d) },
            { data: null, orderable: false, render: (d) => {
                if (d.estado === 'completada' || d.estado === 'cancelada') {
                    return '<span class="text-muted">—</span>';
                }
                let btns = '';
                if (d.estado === 'confirmada' || d.estado === 'pendiente') {
                    btns += `<button class="btn btn-sm btn-outline-primary me-1" onclick="cambiarEstado(${d.id}, 'en_proceso')" title="Iniciar"><i class="bi bi-play-circle"></i></button>`;
                }
                if (d.estado === 'en_proceso' || d.estado === 'confirmada') {
                    btns += `<button class="btn btn-sm btn-outline-success" onclick="cambiarEstado(${d.id}, 'completada')" title="Completar"><i class="bi bi-check-circle"></i></button>`;
                }
                return btns;
            }}
        ],
        order: [[1, 'asc'], [2, 'asc']]
    });
});

function cargarMisCitas() {
    dtMisCitas.ajax.reload();
}

function verHoy() {
    document.getElementById('filtroFecha').value = new Date().toISOString().slice(0, 10);
    document.getElementById('filtroEstado').value = '';
    cargarMisCitas();
}

async function cambiarEstado(id, estado) {
    const label = estado === 'en_proceso' ? 'iniciar' : 'completar';
    if (!await PM.confirm(`¿${label.charAt(0).toUpperCase() + label.slice(1)} esta cita?`)) return;

    const res = await apiCall('<?= URL_API ?>/admin/mis-citas.php', 'PATCH', { id, estado });
    if (res.success) {
        PM.toast('success', estado === 'completada' ? 'Cita completada' : 'Cita en proceso');
        cargarMisCitas();
        if (estado === 'completada' && res.data?.caja_registrada) {
            setTimeout(() => PM.toast('success', 'Ingreso registrado en caja'), 500);
        }
    } else {
        PM.error('Error', res.error);
    }
}

// ══════════════════════════════════════════
//  NUEVA CITA — Modal logic (empleado)
// ══════════════════════════════════════════
let modalNuevaCita;
let ncServiciosCache = [];
let ncClienteMode = 'existente';
let ncBuscarTimeout = null;

document.addEventListener('DOMContentLoaded', () => {
    modalNuevaCita = new bootstrap.Modal(document.getElementById('modalNuevaCita'));
    document.getElementById('modalNuevaCita').addEventListener('show.bs.modal', cargarServiciosNC);
    document.getElementById('ncServicio').addEventListener('change', onServicioChange);
    document.getElementById('ncFecha').addEventListener('change', cargarTurnos);
    document.getElementById('ncBuscarCliente').addEventListener('input', onBuscarCliente);
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#ncClienteExistente')) {
            document.getElementById('ncClienteResultados').classList.remove('show');
        }
    });
    document.getElementById('formNuevaCita').addEventListener('submit', guardarNuevaCita);
});

function abrirModalNuevaCita() {
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
    document.getElementById('ncFecha').min = new Date().toISOString().slice(0, 10);
    modalNuevaCita.show();
}

function toggleClienteMode(mode) {
    ncClienteMode = mode;
    document.querySelectorAll('.nc-toggle-btn').forEach(b => b.classList.toggle('active', b.dataset.mode === mode));
    document.getElementById('ncClienteExistente').style.display = mode === 'existente' ? '' : 'none';
    document.getElementById('ncClienteNuevo').style.display = mode === 'nuevo' ? '' : 'none';
    if (mode === 'nuevo') {
        document.getElementById('ncIdCliente').value = '';
        document.getElementById('ncClienteSeleccionado').style.display = 'none';
        document.getElementById('ncBuscarCliente').style.display = '';
        document.getElementById('ncBuscarCliente').value = '';
    }
}

function onBuscarCliente() {
    clearTimeout(ncBuscarTimeout);
    const q = document.getElementById('ncBuscarCliente').value.trim();
    const lista = document.getElementById('ncClienteResultados');
    if (q.length < 2) { lista.classList.remove('show'); return; }
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
    cargarEmpleadosNC(sel.value);
    if (document.getElementById('ncFecha').value) cargarTurnos();
}

async function cargarEmpleadosNC(idServicio) {
    const sel = document.getElementById('ncEmpleado');
    sel.innerHTML = '<option value="">Sin asignar</option>';
    const res = await apiCall('<?= URL_API ?>/admin/citas.php?empleados_servicio=' + idServicio);
    if (res.success) {
        res.data.forEach(e => { sel.innerHTML += `<option value="${e.id}">${e.nombre}</option>`; });
    }
}

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
    if (!res.success) { container.innerHTML = '<div class="nc-turnos-empty">' + (res.error || 'Error al cargar horarios') + '</div>'; return; }
    if (res.data.mensaje) { container.innerHTML = '<div class="nc-turnos-empty">' + res.data.mensaje + '</div>'; return; }
    if (!res.data.turnos || !res.data.turnos.length) { container.innerHTML = '<div class="nc-turnos-empty">No hay horarios disponibles para esta fecha</div>'; return; }
    container.innerHTML = '<div class="nc-turnos-grid">' +
        res.data.turnos.map(t => `<button type="button" class="nc-turno-pill" data-hora="${t.hora_inicio}" onclick="seleccionarTurno(this)">${formatHora(t.hora_inicio)}</button>`).join('') + '</div>';
}

function seleccionarTurno(btn) {
    document.querySelectorAll('.nc-turno-pill').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('ncHoraInicio').value = btn.dataset.hora;
}

async function guardarNuevaCita(e) {
    e.preventDefault();
    const data = {
        id_servicio: document.getElementById('ncServicio').value,
        fecha: document.getElementById('ncFecha').value,
        hora_inicio: document.getElementById('ncHoraInicio').value,
        id_empleado: document.getElementById('ncEmpleado').value || null,
        notas: document.getElementById('ncNotas').value,
    };
    if (ncClienteMode === 'existente') {
        data.id_cliente = document.getElementById('ncIdCliente').value;
        if (!data.id_cliente) { PM.error('Cliente requerido', 'Busca y selecciona un cliente existente'); return; }
    } else {
        data.nombre = document.getElementById('ncNuevoNombre').value;
        data.email = document.getElementById('ncNuevoEmail').value;
        data.telefono = document.getElementById('ncNuevoTelefono').value;
        if (!data.nombre) { PM.error('Nombre requerido', 'Ingresa el nombre del cliente'); return; }
    }
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
        cargarMisCitas();
    } else {
        PM.error('Error', res.error || 'No se pudo crear la cita');
    }
}
</script>

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

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
