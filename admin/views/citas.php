<?php
/**
 * Piel Morena — Admin: Gestión de Citas
 */
$titulo_admin = 'Citas';
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
    </div>
  </div>
</div>

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
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- Modal cambiar estado -->
<div class="modal fade" id="modalEstado" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar Estado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="estadoCitaId">
        <p class="mb-2" id="estadoCitaInfo"></p>
        <select class="form-select" id="estadoNuevo">
          <option value="pendiente">Pendiente</option>
          <option value="confirmada">Confirmada</option>
          <option value="en_proceso">En proceso</option>
          <option value="completada">Completada</option>
          <option value="cancelada">Cancelada</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-pm-outline btn-pm-sm" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-pm btn-pm-sm" onclick="guardarEstado()">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>
let dtCitas;
let modalEstado;

document.addEventListener('DOMContentLoaded', () => {
    modalEstado = new bootstrap.Modal(document.getElementById('modalEstado'));
    dtCitas = $('#dtCitas').DataTable({
        ...DT_DEFAULTS,
        ajax: {
            url: '<?= URL_API ?>/admin/citas.php',
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
            { data: 'servicio' },
            { data: 'estado', render: (d) => badgeEstado(d) },
            { data: null, orderable: false, render: (d) => `
                <button class="pm-action-btn edit" title="Cambiar estado" onclick="cambiarEstado(${d.id}, '${d.estado}', '${d.cliente} — ${d.servicio}')"><i class="bi bi-arrow-repeat"></i></button>
                <button class="pm-action-btn delete" title="Cancelar" onclick="cancelarCita(${d.id}, '${d.cliente}')" ${d.estado === 'cancelada' || d.estado === 'completada' ? 'disabled' : ''}><i class="bi bi-x-circle"></i></button>
            `}
        ],
        order: [[1, 'asc'], [2, 'asc']]
    });
});

function cargarCitas() {
    dtCitas.ajax.reload();
}

function cambiarEstado(id, estadoActual, info) {
    document.getElementById('estadoCitaId').value = id;
    document.getElementById('estadoCitaInfo').textContent = info;
    document.getElementById('estadoNuevo').value = estadoActual;
    modalEstado.show();
}

async function guardarEstado() {
    const id = document.getElementById('estadoCitaId').value;
    const estado = document.getElementById('estadoNuevo').value;

    const res = await apiCall('<?= URL_API ?>/admin/citas.php', 'PATCH', { id, estado });
    if (res.success) {
        PM.toast('success', 'Estado actualizado');
        modalEstado.hide();
        dtCitas.ajax.reload();
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
    } else {
        PM.error('Error', res.error);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
