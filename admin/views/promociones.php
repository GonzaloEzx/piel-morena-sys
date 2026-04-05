<?php
/**
 * Piel Morena — Admin: CRUD Promociones/Packs
 */
$titulo_admin = 'Promociones';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="pm-panel">
  <div class="pm-panel-header">
    <h3 class="pm-panel-title"><i class="bi bi-gift me-2"></i>Promociones y Packs</h3>
    <button class="btn btn-pm btn-pm-sm" onclick="abrirModalPromo()">
      <i class="bi bi-plus-lg me-1"></i>Nueva Promoción
    </button>
  </div>
  <div class="pm-panel-body">
    <table id="dtPromos" class="table table-hover w-100">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Precio Pack</th>
          <th>Servicios</th>
          <th>Vigencia</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- Modal Promoción -->
<div class="modal fade" id="modalPromo" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPromoTitle">Nueva Promoción</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formPromo">
        <div class="modal-body">
          <input type="hidden" name="id" id="promoId">

          <div class="row g-3 mb-3">
            <div class="col-md-8">
              <label class="form-label">Nombre del Pack</label>
              <input type="text" class="form-control" name="nombre" id="promoNombre" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Precio Pack ($)</label>
              <input type="number" class="form-control" name="precio_pack" id="promoPrecio" step="0.01" min="0.01" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" id="promoDescripcion" rows="2"></textarea>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Duración estimada (min)</label>
              <input type="number" class="form-control" name="duracion_estimada" id="promoDuracion" min="5" step="5" value="60" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Fecha inicio</label>
              <input type="date" class="form-control" name="fecha_inicio" id="promoFechaInicio">
            </div>
            <div class="col-md-4">
              <label class="form-label">Fecha fin</label>
              <input type="date" class="form-control" name="fecha_fin" id="promoFechaFin">
            </div>
          </div>

          <!-- Servicios componentes -->
          <div class="mb-3">
            <label class="form-label">Servicios incluidos en el pack</label>
            <div id="promoServiciosWrap" class="border rounded p-2" style="max-height:220px;overflow-y:auto">
              <div class="text-muted text-center py-3">Cargando servicios...</div>
            </div>
            <small class="form-text text-muted">Seleccioná los servicios y la cantidad de sesiones incluidas</small>
          </div>

          <!-- Disponibilidad -->
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Disponibilidad</label>
              <select class="form-select" name="disponibilidad" id="promoDisponibilidad" onchange="togglePromoGrupo()">
                <option value="auto">Según categoría</option>
                <option value="normal">Normal (calendario libre)</option>
                <option value="jornada">Con Jornada (fechas específicas)</option>
              </select>
            </div>
            <div class="col-md-6 d-none" id="promoGrupoWrap">
              <label class="form-label">Grupo de Jornada</label>
              <select class="form-select" name="id_grupo_jornada" id="promoGrupoJornada">
                <option value="">Misma categoría (Packs)</option>
              </select>
            </div>
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
let dtPromos;
let modalPromo;
let serviciosCatalogo = []; // [{id, nombre, precio, duracion_minutos, categoria}, ...]

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

document.addEventListener('DOMContentLoaded', () => {
    modalPromo = new bootstrap.Modal(document.getElementById('modalPromo'));
    cargarServiciosCatalogo();
    cargarCategoriasGrupo();
    initTabla();
});

// ── Cargar servicios agrupados por categoría para el selector ──
async function cargarServiciosCatalogo() {
    const res = await apiCall('<?= URL_API ?>/admin/servicios.php');
    if (!res.success) return;
    serviciosCatalogo = res.data.filter(s => s.activo == 1);
    renderServiciosSelector();
}

function renderServiciosSelector(seleccionados = []) {
    const wrap = document.getElementById('promoServiciosWrap');
    if (!serviciosCatalogo.length) {
        wrap.innerHTML = '<div class="text-muted text-center py-3">No hay servicios disponibles</div>';
        return;
    }

    // Agrupar por categoría
    const grupos = {};
    serviciosCatalogo.forEach(s => {
        const cat = s.categoria || 'Sin categoría';
        if (!grupos[cat]) grupos[cat] = [];
        grupos[cat].push(s);
    });

    // Map de seleccionados {id_servicio: cantidad}
    const selMap = {};
    seleccionados.forEach(s => { selMap[s.id_servicio] = s.cantidad || 1; });

    let html = '';
    for (const [cat, servicios] of Object.entries(grupos)) {
        html += `<div class="mb-2">
            <small class="fw-bold text-muted text-uppercase">${escapeHtml(cat)}</small>`;
        servicios.forEach(s => {
            const checked = selMap[s.id] ? 'checked' : '';
            const qty = selMap[s.id] || 1;
            html += `
            <div class="d-flex align-items-center gap-2 py-1 ps-2">
                <input class="form-check-input mt-0 promo-srv-check" type="checkbox" value="${s.id}" id="promoSrv${s.id}" ${checked}>
                <label class="form-check-label flex-grow-1 small" for="promoSrv${s.id}">
                    ${escapeHtml(s.nombre)} <span class="text-muted">(${formatPrecio(s.precio)})</span>
                </label>
                <input type="number" class="form-control form-control-sm promo-srv-qty" data-srv="${s.id}"
                       min="1" value="${qty}" style="width:60px" ${checked ? '' : 'disabled'}>
            </div>`;
        });
        html += '</div>';
    }
    wrap.innerHTML = html;

    // Toggle quantity input on checkbox change
    wrap.querySelectorAll('.promo-srv-check').forEach(cb => {
        cb.addEventListener('change', function() {
            const qtyInput = wrap.querySelector(`.promo-srv-qty[data-srv="${this.value}"]`);
            if (qtyInput) {
                qtyInput.disabled = !this.checked;
                if (!this.checked) qtyInput.value = 1;
            }
        });
    });
}

// ── Cargar categorías para grupo jornada ──
async function cargarCategoriasGrupo() {
    const res = await apiCall('<?= URL_API ?>/admin/categorias.php');
    if (!res.success) return;
    const sel = document.getElementById('promoGrupoJornada');
    res.data.forEach(c => {
        sel.innerHTML += `<option value="${c.id}">${escapeHtml(c.nombre)}</option>`;
    });
}

function togglePromoGrupo() {
    const val = document.getElementById('promoDisponibilidad').value;
    const wrap = document.getElementById('promoGrupoWrap');
    if (val === 'jornada') {
        wrap.classList.remove('d-none');
    } else {
        wrap.classList.add('d-none');
        document.getElementById('promoGrupoJornada').value = '';
    }
}

// ── Recoger servicios seleccionados del modal ──
function getServiciosSeleccionados() {
    const servicios = [];
    document.querySelectorAll('.promo-srv-check:checked').forEach(cb => {
        const qty = document.querySelector(`.promo-srv-qty[data-srv="${cb.value}"]`);
        servicios.push({
            id_servicio: parseInt(cb.value),
            cantidad: parseInt(qty?.value || 1)
        });
    });
    return servicios;
}

// ── DataTable ──
function initTabla() {
    dtPromos = $('#dtPromos').DataTable({
        ...DT_DEFAULTS,
        ajax: {
            url: '<?= URL_API ?>/admin/promociones.php',
            dataSrc: (json) => json.success ? json.data : []
        },
        columns: [
            { data: 'id' },
            { data: 'nombre', render: (d, t, row) => {
                let html = escapeHtml(d);
                if (row.servicio_activo == 0) {
                    html += ' <i class="bi bi-eye-slash text-muted ms-1" title="Servicio desactivado"></i>';
                }
                return html;
            }},
            { data: 'precio_pack', render: (d) => formatPrecio(d) },
            { data: 'total_servicios', render: (d) => d + (d == 1 ? ' servicio' : ' servicios') },
            { data: null, render: (d) => {
                const inicio = d.fecha_inicio ? formatFecha(d.fecha_inicio) : '';
                const fin = d.fecha_fin ? formatFecha(d.fecha_fin) : '';
                if (!inicio && !fin) return '<span class="text-muted">Sin límite</span>';
                if (inicio && fin) return `${inicio} — ${fin}`;
                if (inicio) return `Desde ${inicio}`;
                return `Hasta ${fin}`;
            }},
            { data: null, render: (d) => {
                const badges = {
                    vigente:    '<span class="badge-estado badge-confirmada">Vigente</span>',
                    programada: '<span class="badge-estado badge-pendiente">Programada</span>',
                    vencida:    '<span class="badge-estado badge-cancelada">Vencida</span>'
                };
                let html = badges[d.estado_vigencia] || d.estado_vigencia;
                if (d.activo == 0) {
                    html = '<span class="badge-estado badge-cancelada">Desactivada</span>';
                }
                return html;
            }},
            { data: null, orderable: false, render: (d) => `
                <button class="pm-action-btn edit" title="Editar" onclick="editarPromo(${d.id})"><i class="bi bi-pencil"></i></button>
                <button class="pm-action-btn delete" title="Desactivar" onclick="desactivarPromo(${d.id}, '${escapeHtml(d.nombre)}')"><i class="bi bi-trash"></i></button>
            `}
        ],
        order: [[0, 'desc']]
    });
}

// ── Abrir modal (nuevo) ──
function abrirModalPromo() {
    document.getElementById('modalPromoTitle').textContent = 'Nueva Promoción';
    document.getElementById('formPromo').reset();
    document.getElementById('promoId').value = '';
    document.getElementById('promoDisponibilidad').value = 'auto';
    document.getElementById('promoGrupoJornada').value = '';
    togglePromoGrupo();
    renderServiciosSelector();
    modalPromo.show();
}

// ── Editar promo ──
async function editarPromo(id) {
    const res = await apiCall('<?= URL_API ?>/admin/promociones.php?id=' + id);
    if (!res.success) return PM.error('Error', res.error);
    const p = res.data;

    document.getElementById('modalPromoTitle').textContent = 'Editar Promoción';
    document.getElementById('promoId').value = p.id;
    document.getElementById('promoNombre').value = p.nombre;
    document.getElementById('promoDescripcion').value = p.descripcion || '';
    document.getElementById('promoPrecio').value = p.precio_pack;
    document.getElementById('promoDuracion').value = p.duracion_estimada;
    document.getElementById('promoFechaInicio').value = p.fecha_inicio || '';
    document.getElementById('promoFechaFin').value = p.fecha_fin || '';
    document.getElementById('promoDisponibilidad').value = p.disponibilidad || 'auto';
    document.getElementById('promoGrupoJornada').value = p.id_grupo_jornada || '';
    togglePromoGrupo();

    // Renderizar servicios con seleccionados
    renderServiciosSelector(p.servicios || []);

    modalPromo.show();
}

// ── Submit (crear/editar) ──
document.getElementById('formPromo').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const id = fd.get('id');
    const data = Object.fromEntries(fd);

    // Agregar servicios seleccionados
    data.servicios = getServiciosSeleccionados();
    if (!data.servicios.length) {
        return PM.error('Error', 'Seleccioná al menos un servicio para el pack');
    }

    if (!data.id_grupo_jornada) data.id_grupo_jornada = null;

    const res = await apiCall('<?= URL_API ?>/admin/promociones.php', id ? 'PUT' : 'POST', data);
    if (res.success) {
        PM.toast('success', id ? 'Promoción actualizada' : 'Promoción creada');
        modalPromo.hide();
        dtPromos.ajax.reload();
    } else {
        PM.error('Error', res.error);
    }
});

// ── Desactivar promo ──
async function desactivarPromo(id, nombre) {
    if (!await PM.confirmDelete(nombre)) return;
    const res = await apiCall('<?= URL_API ?>/admin/promociones.php', 'DELETE', { id });
    if (res.success) {
        PM.toast('success', 'Promoción desactivada');
        dtPromos.ajax.reload();
    } else {
        PM.error('Error', res.error);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
