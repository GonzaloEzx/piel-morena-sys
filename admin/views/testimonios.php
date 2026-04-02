<?php
/**
 * Piel Morena — Admin: Testimonios
 */
$titulo_admin = 'Testimonios';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="pm-panel">
  <div class="pm-panel-header">
    <div>
      <h3 class="pm-panel-title"><i class="bi bi-chat-quote-fill me-2"></i>Testimonios del Sitio</h3>
      <p class="mb-0 text-muted" style="font-size:.9rem;">La landing trabaja con 6 slots fijos. Mari solo edita y reemplaza el contenido de cada slot desde el lápiz.</p>
    </div>
  </div>
  <div class="pm-panel-body">
    <table id="dtTestimonios" class="table table-hover w-100">
      <thead>
        <tr>
          <th>Slot</th>
          <th>Nombre</th>
          <th>Rol</th>
          <th>Testimonio</th>
          <th>Actualizado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="modalTestimonio" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTestimonioTitle">Editar Slot de Testimonio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formTestimonio">
        <div class="modal-body">
          <input type="hidden" name="id" id="testimonioId">
          <input type="hidden" name="orden" id="testimonioOrden">
          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <label class="form-label">Slot</label>
              <input type="text" class="form-control" id="testimonioSlotLabel" disabled>
            </div>
            <div class="col-md-5">
              <label class="form-label">Nombre</label>
              <input type="text" class="form-control" name="nombre" id="testimonioNombre" required maxlength="120">
            </div>
            <div class="col-md-4">
              <label class="form-label">Rol</label>
              <input type="text" class="form-control" name="rol" id="testimonioRol" maxlength="120" placeholder="Clienta frecuente">
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label">Testimonio</label>
            <textarea class="form-control" name="texto" id="testimonioTexto" rows="6" required maxlength="2000" placeholder="Escribí el testimonio tal como Mari quiere mostrarlo en la landing."></textarea>
          </div>
          <small class="text-muted">La landing mantiene el mismo estilo actual. Acá solo se cargan texto, nombre y rol para el slot seleccionado.</small>
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
let dtTestimonios;
let modalTestimonio;

document.addEventListener('DOMContentLoaded', () => {
    modalTestimonio = new bootstrap.Modal(document.getElementById('modalTestimonio'));

    dtTestimonios = $('#dtTestimonios').DataTable({
        ...DT_DEFAULTS,
        order: [[0, 'asc']],
        ajax: {
            url: '<?= URL_API ?>/admin/testimonios.php',
            dataSrc: (json) => json.success ? json.data : []
        },
        columns: [
            { data: 'orden' },
            { data: 'nombre' },
            { data: 'rol', defaultContent: '—' },
            { data: 'texto', render: (d) => {
                const texto = d || '';
                const corto = texto.length > 120 ? texto.substring(0, 120) + '…' : texto;
                return `<span title="${texto.replace(/"/g, '&quot;')}">${corto}</span>`;
            }},
            { data: 'updated_at', render: (d) => d ? formatFecha(d.substring(0, 10)) : '—' },
            { data: null, orderable: false, render: (d) => `
                <button class="pm-action-btn edit" title="Editar" onclick="editarTestimonio(${d.id})"><i class="bi bi-pencil"></i></button>
            `}
        ]
    });
});

async function editarTestimonio(id) {
    const res = await apiCall('<?= URL_API ?>/admin/testimonios.php?id=' + id);
    if (!res.success) return PM.error('Error', res.error);

    const t = res.data;
    document.getElementById('modalTestimonioTitle').textContent = 'Editar Testimonio';
    document.getElementById('testimonioId').value = t.id;
    document.getElementById('testimonioOrden').value = t.orden || '';
    document.getElementById('testimonioSlotLabel').value = 'Slot ' + (t.orden || '—');
    document.getElementById('testimonioNombre').value = t.nombre || '';
    document.getElementById('testimonioRol').value = t.rol || '';
    document.getElementById('testimonioTexto').value = t.texto || '';
    modalTestimonio.show();
}

document.getElementById('formTestimonio').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const data = Object.fromEntries(fd);
    const res = await apiCall('<?= URL_API ?>/admin/testimonios.php', 'PUT', data);
    if (res.success) {
        PM.toast('success', 'Testimonio actualizado');
        modalTestimonio.hide();
        dtTestimonios.ajax.reload();
    } else {
        PM.error('Error', res.error);
    }
});
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
