<?php
/**
 * Piel Morena — Admin: CRUD Productos
 */
$titulo_admin = 'Productos';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="pm-panel">
  <div class="pm-panel-header">
    <h3 class="pm-panel-title"><i class="bi bi-box-seam me-2"></i>Inventario de Productos</h3>
    <button class="btn btn-pm btn-pm-sm" onclick="abrirModalProducto()">
      <i class="bi bi-plus-lg me-1"></i>Nuevo Producto
    </button>
  </div>
  <div class="pm-panel-body">
    <table id="dtProductos" class="table table-hover w-100">
      <thead>
        <tr>
          <th>ID</th>
          <th>Producto</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- Modal Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalProductoTitle">Nuevo Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formProducto">
        <div class="modal-body">
          <input type="hidden" name="id" id="prodId">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="prodNombre" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" id="prodDescripcion" rows="2"></textarea>
          </div>
          <div class="row g-3">
            <div class="col-4">
              <label class="form-label">Precio ($)</label>
              <input type="number" class="form-control" name="precio" id="prodPrecio" step="0.01" min="0" required>
            </div>
            <div class="col-4">
              <label class="form-label">Stock</label>
              <input type="number" class="form-control" name="stock" id="prodStock" min="0" value="0">
            </div>
            <div class="col-4">
              <label class="form-label">Stock mín.</label>
              <input type="number" class="form-control" name="stock_minimo" id="prodStockMin" min="0" value="5">
            </div>
          </div>
          <div class="mt-3">
            <label class="form-label">Imagen</label>
            <input type="file" class="form-control" id="prodImagen" accept="image/*">
            <input type="hidden" name="imagen" id="prodImagenUrl">
            <div id="prodImagenPreview" class="mt-2"></div>
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
let dtProductos;
let modalProducto;

document.addEventListener('DOMContentLoaded', () => {
    modalProducto = new bootstrap.Modal(document.getElementById('modalProducto'));
    dtProductos = $('#dtProductos').DataTable({
        ...DT_DEFAULTS,
        ajax: {
            url: '<?= URL_API ?>/admin/productos.php',
            dataSrc: (json) => json.success ? json.data : []
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'precio', render: (d) => formatPrecio(d) },
            { data: null, render: (d) => {
                const low = d.stock <= d.stock_minimo;
                return `<span class="${low ? 'text-danger fw-bold' : ''}">${d.stock}</span>` +
                    (low ? ' <i class="bi bi-exclamation-triangle text-danger" title="Stock bajo"></i>' : '');
            }},
            { data: 'activo', render: (d) => d == 1
                ? '<span class="badge-estado badge-completada">Activo</span>'
                : '<span class="badge-estado badge-cancelada">Inactivo</span>'
            },
            { data: null, orderable: false, render: (d) => `
                <button class="pm-action-btn edit" onclick="editarProducto(${d.id})"><i class="bi bi-pencil"></i></button>
                <button class="pm-action-btn delete" onclick="eliminarProducto(${d.id}, '${d.nombre}')"><i class="bi bi-trash"></i></button>
            `}
        ]
    });
});

function abrirModalProducto() {
    document.getElementById('modalProductoTitle').textContent = 'Nuevo Producto';
    document.getElementById('formProducto').reset();
    document.getElementById('prodId').value = '';
    document.getElementById('prodImagenUrl').value = '';
    document.getElementById('prodImagenPreview').innerHTML = '';
    modalProducto.show();
}

async function editarProducto(id) {
    const res = await apiCall('<?= URL_API ?>/admin/productos.php?id=' + id);
    if (!res.success) return PM.error('Error', res.error);
    const p = res.data;
    document.getElementById('modalProductoTitle').textContent = 'Editar Producto';
    document.getElementById('prodId').value = p.id;
    document.getElementById('prodNombre').value = p.nombre;
    document.getElementById('prodDescripcion').value = p.descripcion || '';
    document.getElementById('prodPrecio').value = p.precio;
    document.getElementById('prodStock').value = p.stock;
    document.getElementById('prodStockMin').value = p.stock_minimo;
    document.getElementById('prodImagenUrl').value = p.imagen || '';
    document.getElementById('prodImagenPreview').innerHTML = p.imagen
        ? `<img src="${p.imagen}" class="rounded" style="max-height:80px">`
        : '';
    modalProducto.show();
}

// Upload de imagen al seleccionar archivo
document.getElementById('prodImagen').addEventListener('change', async function() {
    if (!this.files[0]) return;
    const fd = new FormData();
    fd.append('imagen', this.files[0]);
    fd.append('tipo', 'productos');
    const res = await apiCall('<?= URL_API ?>/admin/upload.php', 'POST', fd);
    if (res.success) {
        document.getElementById('prodImagenUrl').value = res.data.url;
        document.getElementById('prodImagenPreview').innerHTML = `<img src="${res.data.url}" class="rounded" style="max-height:80px">`;
        PM.toast('success', 'Imagen subida');
    } else {
        PM.error('Error', res.error);
    }
});

document.getElementById('formProducto').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const id = fd.get('id');
    const res = await apiCall('<?= URL_API ?>/admin/productos.php', id ? 'PUT' : 'POST', Object.fromEntries(fd));
    if (res.success) {
        PM.toast('success', id ? 'Producto actualizado' : 'Producto creado');
        modalProducto.hide();
        dtProductos.ajax.reload();
    } else {
        PM.error('Error', res.error);
    }
});

async function eliminarProducto(id, nombre) {
    if (!await PM.confirmDelete(nombre)) return;
    const res = await apiCall('<?= URL_API ?>/admin/productos.php', 'DELETE', { id });
    if (res.success) {
        PM.toast('success', 'Producto eliminado');
        dtProductos.ajax.reload();
    } else {
        PM.error('Error', res.error);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
