<?php
/**
 * Piel Morena — Admin: CRUD Servicios
 */
$titulo_admin = 'Servicios';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="pm-panel">
  <div class="pm-panel-header">
    <h3 class="pm-panel-title"><i class="bi bi-stars me-2"></i>Listado de Servicios</h3>
    <button class="btn btn-pm btn-pm-sm" onclick="abrirModalServicio()">
      <i class="bi bi-plus-lg me-1"></i>Nuevo Servicio
    </button>
  </div>
  <div class="pm-panel-body">
    <table id="dtServicios" class="table table-hover w-100">
      <thead>
        <tr>
          <th>ID</th>
          <th>Servicio</th>
          <th>Jornada</th>
          <th>Categoría</th>
          <th>Precio</th>
          <th>Duración</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- Modal Servicio -->
<div class="modal fade" id="modalServicio" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalServicioTitle">Nuevo Servicio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formServicio">
        <div class="modal-body">
          <input type="hidden" name="id" id="srvId">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="srvNombre" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Categoría</label>
            <select class="form-select" name="id_categoria" id="srvCategoria">
              <option value="">Sin categoría</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" id="srvDescripcion" rows="3"></textarea>
          </div>
          <div class="row g-3">
            <div class="col-6">
              <label class="form-label">Precio ($)</label>
              <input type="number" class="form-control" name="precio" id="srvPrecio" step="0.01" min="0" required>
            </div>
            <div class="col-6">
              <label class="form-label">Duración (min)</label>
              <input type="number" class="form-control" name="duracion_minutos" id="srvDuracion" min="5" step="5" value="30" required>
            </div>
          </div>
          <div class="mt-3">
            <label class="form-label">Imagen</label>
            <input type="file" class="form-control" id="srvImagen" accept="image/*">
            <input type="hidden" name="imagen" id="srvImagenUrl">
            <div id="srvImagenPreview" class="mt-2"></div>
          </div>
          <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" id="srvDestacado" name="destacado" value="1">
            <label class="form-check-label" for="srvDestacado">
              <i class="bi bi-star-fill text-warning me-1"></i>Servicio destacado
              <small class="text-muted d-block">Se mostrará en la sección de servicios del sitio web (máx. 6)</small>
            </label>
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
let dtServicios;
let modalServicio;

document.addEventListener('DOMContentLoaded', () => {
    modalServicio = new bootstrap.Modal(document.getElementById('modalServicio'));
    cargarCategorias();
    initTabla();
});

async function cargarCategorias() {
    const res = await apiCall('<?= URL_API ?>/admin/categorias.php');
    if (res.success) {
        const sel = document.getElementById('srvCategoria');
        res.data.forEach(c => {
            sel.innerHTML += `<option value="${c.id}">${c.nombre}</option>`;
        });
    }
}

function initTabla() {
    dtServicios = $('#dtServicios').DataTable({
        ...DT_DEFAULTS,
        ajax: {
            url: '<?= URL_API ?>/admin/servicios.php',
            dataSrc: function(json) { return json.success ? json.data : []; }
        },
        columns: [
            { data: 'id' },
            { data: 'nombre', render: (d, t, row) => row.destacado == 1
                ? `${d} <i class="bi bi-star-fill text-warning ms-1" title="Destacado"></i>`
                : d
            },
            { data: null, orderable: false, render: (d) => {
                if (d.jornada_origen === 'grupo') {
                    return '<span class="badge text-bg-warning" title="Usa jornadas por grupo"><i class="bi bi-diagram-3 me-1"></i>Grupo</span>';
                }
                if (d.jornada_origen === 'categoria') {
                    return '<span class="badge text-bg-info" title="Usa jornadas por categoría"><i class="bi bi-collection me-1"></i>Categoría</span>';
                }
                return '<span class="text-muted">—</span>';
            }},
            { data: 'categoria', defaultContent: '<span class="text-muted">—</span>' },
            { data: 'precio', render: (d) => formatPrecio(d) },
            { data: 'duracion_minutos', render: (d) => d + ' min' },
            { data: null, orderable: false, render: (d) => `
                <button class="pm-action-btn edit" title="Editar" onclick="editarServicio(${d.id})"><i class="bi bi-pencil"></i></button>
                <button class="pm-action-btn delete" title="Eliminar" onclick="eliminarServicio(${d.id}, '${d.nombre}')"><i class="bi bi-trash"></i></button>
            `}
        ],
        order: [[0, 'desc']]
    });
}

function abrirModalServicio() {
    document.getElementById('modalServicioTitle').textContent = 'Nuevo Servicio';
    document.getElementById('formServicio').reset();
    document.getElementById('srvId').value = '';
    document.getElementById('srvImagenUrl').value = '';
    document.getElementById('srvImagenPreview').innerHTML = '';
    document.getElementById('srvDestacado').checked = false;
    modalServicio.show();
}

async function editarServicio(id) {
    const res = await apiCall('<?= URL_API ?>/admin/servicios.php?id=' + id);
    if (!res.success) return PM.error('Error', res.error);
    const s = res.data;

    document.getElementById('modalServicioTitle').textContent = 'Editar Servicio';
    document.getElementById('srvId').value = s.id;
    document.getElementById('srvNombre').value = s.nombre;
    document.getElementById('srvCategoria').value = s.id_categoria || '';
    document.getElementById('srvDescripcion').value = s.descripcion || '';
    document.getElementById('srvPrecio').value = s.precio;
    document.getElementById('srvDuracion').value = s.duracion_minutos;
    document.getElementById('srvImagenUrl').value = s.imagen || '';
    document.getElementById('srvImagenPreview').innerHTML = s.imagen
        ? `<img src="${s.imagen}" class="rounded" style="max-height:80px">`
        : '';
    document.getElementById('srvDestacado').checked = s.destacado == 1;
    modalServicio.show();
}

// Upload de imagen al seleccionar archivo
document.getElementById('srvImagen').addEventListener('change', async function() {
    if (!this.files[0]) return;
    const fd = new FormData();
    fd.append('imagen', this.files[0]);
    fd.append('tipo', 'servicios');
    const res = await apiCall('<?= URL_API ?>/admin/upload.php', 'POST', fd);
    if (res.success) {
        document.getElementById('srvImagenUrl').value = res.data.url;
        document.getElementById('srvImagenPreview').innerHTML = `<img src="${res.data.url}" class="rounded" style="max-height:80px">`;
        PM.toast('success', 'Imagen subida');
    } else {
        PM.error('Error', res.error);
    }
});

document.getElementById('formServicio').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const id = fd.get('id');
    const url = '<?= URL_API ?>/admin/servicios.php';

    const data = Object.fromEntries(fd);
    data.destacado = document.getElementById('srvDestacado').checked ? 1 : 0;

    const res = await apiCall(url, id ? 'PUT' : 'POST', data);

    if (res.success) {
        PM.toast('success', id ? 'Servicio actualizado' : 'Servicio creado');
        modalServicio.hide();
        dtServicios.ajax.reload();
    } else {
        PM.error('Error', res.error);
    }
});

async function eliminarServicio(id, nombre) {
    if (!await PM.confirmDelete(nombre)) return;

    const res = await apiCall('<?= URL_API ?>/admin/servicios.php', 'DELETE', { id });
    if (res.success) {
        PM.toast('success', 'Servicio eliminado');
        dtServicios.ajax.reload();
    } else {
        PM.error('Error', res.error);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
