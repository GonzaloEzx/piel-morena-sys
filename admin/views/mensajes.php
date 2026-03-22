<?php
/**
 * Piel Morena — Admin: Mensajes de Contacto
 */
$titulo_admin = 'Mensajes';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="pm-panel">
  <div class="pm-panel-header">
    <h3 class="pm-panel-title"><i class="bi bi-envelope-fill me-2"></i>Mensajes de Contacto</h3>
  </div>
  <div class="pm-panel-body">
    <table id="dtMensajes" class="table table-hover w-100">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Mensaje</th>
          <th>Fecha</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<script>
let dtMensajes;

document.addEventListener('DOMContentLoaded', () => {
    dtMensajes = $('#dtMensajes').DataTable({
        ...DT_DEFAULTS,
        ajax: {
            url: '<?= URL_API ?>/admin/mensajes.php',
            dataSrc: (json) => json.success ? json.data : []
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'email', render: (d) => `<a href="mailto:${d}">${d}</a>` },
            { data: 'mensaje', render: (d) => {
                const txt = d.length > 60 ? d.substring(0, 60) + '...' : d;
                return `<span title="${d.replace(/"/g, '&quot;')}">${txt}</span>`;
            }},
            { data: 'created_at', render: (d) => formatFecha(d ? d.substring(0, 10) : '') },
            { data: 'leido', render: (d) => d == 1
                ? '<span class="badge-estado badge-completada">Leído</span>'
                : '<span class="badge-estado badge-pendiente">Nuevo</span>'
            },
            { data: null, orderable: false, render: (d) => `
                <button class="pm-action-btn edit" onclick="verMensaje(${d.id})" title="Ver"><i class="bi bi-eye"></i></button>
                <button class="pm-action-btn delete" onclick="eliminarMensaje(${d.id})" title="Eliminar"><i class="bi bi-trash"></i></button>
            `}
        ]
    });
});

async function verMensaje(id) {
    const res = await apiCall('<?= URL_API ?>/admin/mensajes.php?id=' + id);
    if (!res.success) return PM.error('Error', res.error);
    const m = res.data;

    await Swal.fire({
        title: m.nombre,
        html: `
            <div class="text-start">
                <p><strong>Email:</strong> <a href="mailto:${m.email}">${m.email}</a></p>
                ${m.telefono ? `<p><strong>Teléfono:</strong> ${m.telefono}</p>` : ''}
                <p><strong>Fecha:</strong> ${formatFecha(m.created_at ? m.created_at.substring(0, 10) : '')}</p>
                <hr>
                <p>${m.mensaje.replace(/\n/g, '<br>')}</p>
            </div>
        `,
        confirmButtonColor: '#8A7650',
        confirmButtonText: 'Cerrar'
    });

    // Marcar como leído
    if (m.leido == 0) {
        await apiCall('<?= URL_API ?>/admin/mensajes.php', 'PATCH', { id, leido: 1 });
        dtMensajes.ajax.reload();
    }
}

async function eliminarMensaje(id) {
    if (!await PM.confirmDelete()) return;
    const res = await apiCall('<?= URL_API ?>/admin/mensajes.php', 'DELETE', { id });
    if (res.success) {
        PM.toast('success', 'Mensaje eliminado');
        dtMensajes.ajax.reload();
    } else {
        PM.error('Error', res.error);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
