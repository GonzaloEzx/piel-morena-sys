<?php
/**
 * Piel Morena — Admin: CRUD Clientes
 */
$titulo_admin = 'Clientes';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="pm-panel">
  <div class="pm-panel-header">
    <h3 class="pm-panel-title"><i class="bi bi-people-fill me-2"></i>Listado de Clientes</h3>
    <button class="btn btn-pm btn-pm-sm" onclick="abrirModalCliente()">
      <i class="bi bi-plus-lg me-1"></i>Nuevo Cliente
    </button>
  </div>
  <div class="pm-panel-body">
    <table id="dtClientes" class="table table-hover w-100">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Método</th>
          <th>Email verificado</th>
          <th>Último acceso</th>
          <th>Teléfono</th>
          <th>Citas</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- Modal Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalClienteTitle">Nuevo Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formCliente">
        <div class="modal-body">
          <input type="hidden" name="id" id="cliId">
          <div class="row g-3 mb-3">
            <div class="col-6">
              <label class="form-label">Nombre</label>
              <input type="text" class="form-control" name="nombre" id="cliNombre" required>
            </div>
            <div class="col-6">
              <label class="form-label">Apellidos</label>
              <input type="text" class="form-control" name="apellidos" id="cliApellidos" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="cliEmail" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono" id="cliTelefono">
          </div>
          <div class="mb-3" id="cliPasswordGroup">
            <label class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="password" id="cliPassword" minlength="6">
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
let dtClientes;
let modalCliente;

document.addEventListener('DOMContentLoaded', () => {
    modalCliente = new bootstrap.Modal(document.getElementById('modalCliente'));
    dtClientes = $('#dtClientes').DataTable({
        ...DT_DEFAULTS,
        ajax: {
            url: '<?= URL_API ?>/admin/clientes.php',
            dataSrc: (json) => json.success ? json.data : []
        },
        columns: [
            { data: 'id' },
            { data: null, render: (d) => `${d.nombre} ${d.apellidos}` },
            { data: 'email' },
            { data: null, orderable: true, render: (d) => {
                if (d.google_id) return '<span class="badge bg-danger bg-opacity-75"><i class="bi bi-google me-1"></i>Google</span>';
                return '<span class="badge bg-secondary"><i class="bi bi-pencil-square me-1"></i>Manual</span>';
            }},
            { data: 'email_verificado', render: (d) => d == 1
                ? '<span class="badge bg-success">Sí</span>'
                : '<span class="badge bg-secondary">No</span>'
            },
            { data: 'ultimo_acceso', render: (d) => {
                if (!d) return '<span class="text-muted">Nunca</span>';
                const f = new Date(d);
                return f.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit', year: 'numeric' })
                     + ' ' + f.toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit' });
            }},
            { data: 'telefono', defaultContent: '—' },
            { data: 'total_citas', defaultContent: '0' },
            { data: 'activo', render: (d) => d == 1
                ? '<span class="badge-estado badge-completada">Activo</span>'
                : '<span class="badge-estado badge-cancelada">Inactivo</span>'
            },
            { data: null, orderable: false, render: (d) => `
                <button class="pm-action-btn edit" title="Editar" onclick="editarCliente(${d.id})"><i class="bi bi-pencil"></i></button>
                <button class="pm-action-btn delete" title="Desactivar" onclick="toggleCliente(${d.id}, '${d.nombre} ${d.apellidos}', ${d.activo})"><i class="bi bi-${d.activo == 1 ? 'person-dash' : 'person-check'}"></i></button>
            `}
        ]
    });
});

function abrirModalCliente() {
    document.getElementById('modalClienteTitle').textContent = 'Nuevo Cliente';
    document.getElementById('formCliente').reset();
    document.getElementById('cliId').value = '';
    document.getElementById('cliPassword').setAttribute('required', 'required');
    modalCliente.show();
}

async function editarCliente(id) {
    const res = await apiCall('<?= URL_API ?>/admin/clientes.php?id=' + id);
    if (!res.success) return PM.error('Error', res.error);
    const c = res.data;

    document.getElementById('modalClienteTitle').textContent = 'Editar Cliente';
    document.getElementById('cliId').value = c.id;
    document.getElementById('cliNombre').value = c.nombre;
    document.getElementById('cliApellidos').value = c.apellidos;
    document.getElementById('cliEmail').value = c.email;
    document.getElementById('cliTelefono').value = c.telefono || '';
    document.getElementById('cliPassword').value = '';
    document.getElementById('cliPassword').removeAttribute('required');
    modalCliente.show();
}

document.getElementById('formCliente').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const id = fd.get('id');
    const data = Object.fromEntries(fd);
    if (!data.password) delete data.password;

    const res = await apiCall('<?= URL_API ?>/admin/clientes.php', id ? 'PUT' : 'POST', data);
    if (res.success) {
        PM.toast('success', id ? 'Cliente actualizado' : 'Cliente creado');
        modalCliente.hide();
        dtClientes.ajax.reload();
    } else {
        PM.error('Error', res.error);
    }
});

async function toggleCliente(id, nombre, activo) {
    const accion = activo == 1 ? 'desactivar' : 'activar';
    if (!await PM.confirm(`¿${accion.charAt(0).toUpperCase() + accion.slice(1)} cliente?`, nombre)) return;

    const res = await apiCall('<?= URL_API ?>/admin/clientes.php', 'PATCH', { id, activo: activo == 1 ? 0 : 1 });
    if (res.success) {
        PM.toast('success', `Cliente ${accion === 'desactivar' ? 'desactivado' : 'activado'}`);
        dtClientes.ajax.reload();
    } else {
        PM.error('Error', res.error);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
