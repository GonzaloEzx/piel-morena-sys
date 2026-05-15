<?php
/**
 * Piel Morena — Admin: Catalogo de Maquinas Alquiladas
 */
$titulo_admin = 'Maquinas';
$extra_css = '
<style>
  .mq-color-dot { display: inline-block; width: 14px; height: 14px; border-radius: 50%; vertical-align: middle; margin-right: 6px; border: 1px solid rgba(0,0,0,.08); }
  .mq-icon-preview { font-size: 1.2rem; color: var(--pm-bronce, #8A7650); }
  .badge-maquina-activa  { background: #198754; color: #fff; font-size: .72rem; padding: 3px 8px; border-radius: 6px; }
  .badge-maquina-inactiva { background: #6c757d; color: #fff; font-size: .72rem; padding: 3px 8px; border-radius: 6px; }

  .mq-helper {
    background: var(--pm-crema, #ECE7D1); color: var(--pm-texto, #3F352B);
    padding: .75rem 1rem; border-radius: 8px; font-size: .85rem;
    margin-bottom: 1rem;
  }
  .mq-helper i { color: var(--pm-bronce, #8A7650); margin-right: .4rem; }

  /* Color picker grid */
  .mq-color-grid { display: flex; flex-wrap: wrap; gap: .4rem; }
  .mq-color-swatch {
    width: 30px; height: 30px; border-radius: 6px; cursor: pointer;
    border: 2px solid transparent; transition: transform .12s;
  }
  .mq-color-swatch:hover { transform: scale(1.1); }
  .mq-color-swatch.selected { border-color: #3F352B; transform: scale(1.15); }
</style>';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="mq-helper">
  <i class="bi bi-info-circle"></i>
  <strong>Maquinas alquiladas:</strong> Cargá aca los instrumentos que alquilas (laser, criolipo, velaslim, etc).
  Despues de crear una maquina, asignala a sus servicios desde la seccion <a href="<?= URL_ADMIN ?>/views/servicios.php">Servicios</a>
  y cargá los dias que esta disponible en <a href="<?= URL_ADMIN ?>/views/alquileres.php">Alquileres</a>.
</div>

<div class="pm-panel">
  <div class="pm-panel-header">
    <h3 class="pm-panel-title"><i class="bi bi-cpu me-2"></i>Catalogo de Maquinas</h3>
    <button class="btn btn-pm btn-pm-sm" onclick="abrirModalNueva()">
      <i class="bi bi-plus-circle me-1"></i>Nueva Maquina
    </button>
  </div>
  <div class="pm-panel-body">
    <table id="dtMaquinas" class="table table-hover w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>Nombre</th>
          <th>Descripcion</th>
          <th>Servicios</th>
          <th>Alquileres futuros</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- Modal: Nueva / Editar Maquina -->
<div class="modal fade" id="modalMaquina" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitulo"><i class="bi bi-cpu me-2"></i>Nueva Maquina</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="mqId">
        <div class="mb-3">
          <label class="form-label">Nombre <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="mqNombre" maxlength="150" placeholder="Ej: Laser Diodo">
        </div>
        <div class="mb-3">
          <label class="form-label">Descripcion</label>
          <textarea class="form-control" id="mqDescripcion" rows="2" placeholder="Marca, modelo, uso..."></textarea>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-7">
            <label class="form-label">Color (para calendario)</label>
            <div class="mq-color-grid" id="mqColorGrid"></div>
            <input type="hidden" id="mqColor" value="#8A7650">
          </div>
          <div class="col-5">
            <label class="form-label">Icono Bootstrap</label>
            <input type="text" class="form-control" id="mqIcono" placeholder="bi-lightning-charge-fill">
            <div class="form-text">Ver <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a></div>
          </div>
        </div>
        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" id="mqActivo" checked>
          <label class="form-check-label" for="mqActivo">Maquina activa</label>
        </div>
        <div class="mb-0">
          <label class="form-label">Notas</label>
          <textarea class="form-control" id="mqNotas" rows="2" placeholder="Observaciones internas..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-pm-outline btn-pm-sm" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-pm btn-pm-sm" id="mqBtnGuardar" onclick="guardarMaquina()">
          <i class="bi bi-check-lg me-1"></i>Guardar
        </button>
      </div>
    </div>
  </div>
</div>

<script>
let dtMaquinas;
let modalMaquina;
const MQ_COLORES = ['#8A7650', '#B77466', '#7A654F', '#957C62', '#DBCEA5', '#FFE1AF', '#8E977D', '#A85F52', '#6f42c1', '#0d6efd', '#e67e22', '#1abc9c'];

document.addEventListener('DOMContentLoaded', () => {
    modalMaquina = new bootstrap.Modal(document.getElementById('modalMaquina'));

    // Render color picker
    document.getElementById('mqColorGrid').innerHTML = MQ_COLORES.map(c =>
        `<div class="mq-color-swatch" style="background:${c}" data-color="${c}" onclick="selectColor('${c}')"></div>`
    ).join('');
    selectColor('#8A7650');

    dtMaquinas = $('#dtMaquinas').DataTable({
        ...DT_DEFAULTS,
        ajax: {
            url: '<?= URL_API ?>/admin/maquinas.php',
            dataSrc: (json) => json.success ? json.data : []
        },
        columns: [
            { data: 'id' },
            { data: null, render: (d) =>
                `<span class="mq-color-dot" style="background:${d.color}"></span>` +
                (d.icono ? `<i class="bi ${d.icono} mq-icon-preview me-1"></i>` : '') +
                `<strong>${escapeHtml(d.nombre)}</strong>`
            },
            { data: 'descripcion', render: (d) => d ? escapeHtml(d.substring(0, 60)) + (d.length > 60 ? '...' : '') : '<span class="text-muted">—</span>' },
            { data: 'servicios_count', render: (d) => d > 0 ? `<span class="badge bg-primary">${d}</span>` : '<span class="text-muted">0</span>' },
            { data: 'alquileres_futuros', render: (d) => d > 0 ? `<span class="badge bg-success">${d}</span>` : '<span class="text-muted">0</span>' },
            { data: 'activo', render: (d) => parseInt(d) === 1 ? '<span class="badge-maquina-activa">Activa</span>' : '<span class="badge-maquina-inactiva">Inactiva</span>' },
            { data: null, orderable: false, render: (d) => `
                <button class="pm-action-btn edit" title="Editar" onclick="abrirEditar(${d.id})"><i class="bi bi-pencil"></i></button>
                <button class="pm-action-btn delete" title="Eliminar" onclick="eliminarMaquina(${d.id})"><i class="bi bi-trash"></i></button>
            `}
        ],
        order: [[1, 'asc']]
    });
});

function escapeHtml(s) {
    return String(s || '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

function selectColor(c) {
    document.getElementById('mqColor').value = c;
    document.querySelectorAll('.mq-color-swatch').forEach(el => {
        el.classList.toggle('selected', el.dataset.color === c);
    });
}

function abrirModalNueva() {
    document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-cpu me-2"></i>Nueva Maquina';
    document.getElementById('mqId').value = '';
    document.getElementById('mqNombre').value = '';
    document.getElementById('mqDescripcion').value = '';
    document.getElementById('mqIcono').value = '';
    document.getElementById('mqActivo').checked = true;
    document.getElementById('mqNotas').value = '';
    selectColor('#8A7650');
    modalMaquina.show();
}

async function abrirEditar(id) {
    const res = await apiCall('<?= URL_API ?>/admin/maquinas.php?id=' + id);
    if (!res.success) { PM.error('Error', res.error); return; }
    const m = res.data;
    document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Maquina';
    document.getElementById('mqId').value = m.id;
    document.getElementById('mqNombre').value = m.nombre || '';
    document.getElementById('mqDescripcion').value = m.descripcion || '';
    document.getElementById('mqIcono').value = m.icono || '';
    document.getElementById('mqActivo').checked = parseInt(m.activo) === 1;
    document.getElementById('mqNotas').value = m.notas || '';
    selectColor(m.color || '#8A7650');
    modalMaquina.show();
}

async function guardarMaquina() {
    const id          = document.getElementById('mqId').value;
    const nombre      = document.getElementById('mqNombre').value.trim();
    const descripcion = document.getElementById('mqDescripcion').value.trim();
    const color       = document.getElementById('mqColor').value;
    const icono       = document.getElementById('mqIcono').value.trim();
    const activo      = document.getElementById('mqActivo').checked ? 1 : 0;
    const notas       = document.getElementById('mqNotas').value.trim();

    if (!nombre) { PM.error('Nombre requerido', 'Ingresa el nombre de la maquina'); return; }

    const btn = document.getElementById('mqBtnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    const payload = { nombre, descripcion, color, icono, activo, notas };
    const res = id
        ? await apiCall('<?= URL_API ?>/admin/maquinas.php', 'PUT', { id: parseInt(id), ...payload })
        : await apiCall('<?= URL_API ?>/admin/maquinas.php', 'POST', payload);

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Guardar';

    if (res.success) {
        PM.toast('success', id ? 'Maquina actualizada' : 'Maquina creada');
        modalMaquina.hide();
        dtMaquinas.ajax.reload();
    } else {
        PM.error('Error', res.error || 'No se pudo guardar');
    }
}

async function eliminarMaquina(id) {
    const info = await apiCall('<?= URL_API ?>/admin/maquinas.php?id=' + id);
    if (!info.success) { PM.error('Error', info.error); return; }
    const m = info.data;

    let html = `<p><strong>${escapeHtml(m.nombre)}</strong></p>`;
    let canDelete = true;
    if (m.servicios_count > 0) {
        html += `<p class="text-warning">Tiene ${m.servicios_count} servicio(s) asociado(s). Desasignalos antes de eliminar.</p>`;
        canDelete = false;
    }
    if (m.alquileres_futuros > 0) {
        html += `<p class="text-warning">Tiene ${m.alquileres_futuros} alquiler(es) futuro(s). Cancelalos antes de eliminar.</p>`;
        canDelete = false;
    }
    if (canDelete) html += '<p style="font-size:.82rem;color:#A85F52">Esta accion no se puede deshacer.</p>';

    const result = await Swal.fire({
        title: canDelete ? 'Eliminar maquina' : 'No se puede eliminar',
        html: html,
        icon: canDelete ? 'warning' : 'info',
        showCancelButton: canDelete,
        confirmButtonColor: '#A85F52',
        cancelButtonColor: '#8A7650',
        confirmButtonText: canDelete ? 'Eliminar' : 'Entendido',
        cancelButtonText: 'Volver'
    });

    if (!canDelete || !result.isConfirmed) return;

    const res = await apiCall('<?= URL_API ?>/admin/maquinas.php', 'DELETE', { id });
    if (res.success) {
        PM.toast('success', 'Maquina eliminada');
        dtMaquinas.ajax.reload();
    } else {
        PM.error('Error', res.error);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
