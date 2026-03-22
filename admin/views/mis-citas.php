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
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
