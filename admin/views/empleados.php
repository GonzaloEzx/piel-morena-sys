<?php
/**
 * Piel Morena — Admin: CRUD Empleados
 */
$titulo_admin = 'Empleados';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="pm-panel">
  <div class="pm-panel-header">
    <h3 class="pm-panel-title"><i class="bi bi-person-badge me-2"></i>Listado de Empleados</h3>
    <button class="btn btn-pm btn-pm-sm" onclick="abrirModalEmpleado()">
      <i class="bi bi-plus-lg me-1"></i>Nuevo Empleado
    </button>
  </div>
  <div class="pm-panel-body">
    <table id="dtEmpleados" class="table table-hover w-100">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Teléfono</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- Modal Empleado -->
<div class="modal fade" id="modalEmpleado" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEmpleadoTitle">Nuevo Empleado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEmpleado">
        <div class="modal-body">
          <input type="hidden" name="id" id="empId">
          <div class="row g-3 mb-3">
            <div class="col-6">
              <label class="form-label">Nombre</label>
              <input type="text" class="form-control" name="nombre" id="empNombre" required>
            </div>
            <div class="col-6">
              <label class="form-label">Apellidos</label>
              <input type="text" class="form-control" name="apellidos" id="empApellidos" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="empEmail" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono" id="empTelefono">
          </div>
          <div class="mb-3" id="empPasswordGroup">
            <label class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="password" id="empPassword" minlength="6">
            <small class="text-muted">Dejar en blanco para no cambiar (al editar)</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-pm-outline btn-pm-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-pm btn-pm-sm">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let dtEmpleados;
let modalEmpleado;

document.addEventListener('DOMContentLoaded', () => {
    modalEmpleado = new bootstrap.Modal(document.getElementById('modalEmpleado'));
    dtEmpleados = $('#dtEmpleados').DataTable({
        ...DT_DEFAULTS,
        ajax: {
            url: '<?= URL_API ?>/admin/empleados.php',
            dataSrc: (json) => json.success ? json.data : []
        },
        columns: [
            { data: 'id' },
            { data: null, render: (d) => `${d.nombre} ${d.apellidos}` },
            { data: 'email' },
            { data: 'telefono', defaultContent: '—' },
            { data: 'activo', render: (d) => d == 1
                ? '<span class="badge-estado badge-completada">Activo</span>'
                : '<span class="badge-estado badge-cancelada">Inactivo</span>'
            },
            { data: null, orderable: false, render: (d) => `
                <button class="pm-action-btn edit" onclick="editarEmpleado(${d.id})"><i class="bi bi-pencil"></i></button>
                <button class="pm-action-btn delete" onclick="toggleEmpleado(${d.id}, '${d.nombre}', ${d.activo})"><i class="bi bi-${d.activo == 1 ? 'person-dash' : 'person-check'}"></i></button>
            `}
        ]
    });
});

function abrirModalEmpleado() {
    document.getElementById('modalEmpleadoTitle').textContent = 'Nuevo Empleado';
    document.getElementById('formEmpleado').reset();
    document.getElementById('empId').value = '';
    document.getElementById('empPassword').setAttribute('required', 'required');
    modalEmpleado.show();
}

async function editarEmpleado(id) {
    const res = await apiCall('<?= URL_API ?>/admin/empleados.php?id=' + id);
    if (!res.success) return PM.error('Error', res.error);
    const e = res.data;
    document.getElementById('modalEmpleadoTitle').textContent = 'Editar Empleado';
    document.getElementById('empId').value = e.id;
    document.getElementById('empNombre').value = e.nombre;
    document.getElementById('empApellidos').value = e.apellidos;
    document.getElementById('empEmail').value = e.email;
    document.getElementById('empTelefono').value = e.telefono || '';
    document.getElementById('empPassword').value = '';
    document.getElementById('empPassword').removeAttribute('required');
    modalEmpleado.show();
}

document.getElementById('formEmpleado').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const id = fd.get('id');
    const data = Object.fromEntries(fd);
    if (!data.password) delete data.password;

    const res = await apiCall('<?= URL_API ?>/admin/empleados.php', id ? 'PUT' : 'POST', data);
    if (res.success) {
        PM.toast('success', id ? 'Empleado actualizado' : 'Empleado creado');
        modalEmpleado.hide();
        dtEmpleados.ajax.reload();
    } else {
        PM.error('Error', res.error);
    }
});

async function toggleEmpleado(id, nombre, activo) {
    const accion = activo == 1 ? 'desactivar' : 'activar';
    if (!await PM.confirm(`¿${accion.charAt(0).toUpperCase() + accion.slice(1)}?`, nombre)) return;

    const res = await apiCall('<?= URL_API ?>/admin/empleados.php', 'PATCH', { id, activo: activo == 1 ? 0 : 1 });
    if (res.success) {
        PM.toast('success', `Empleado ${accion === 'desactivar' ? 'desactivado' : 'activado'}`);
        dtEmpleados.ajax.reload();
    } else {
        PM.error('Error', res.error);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
