<?php
/**
 * Piel Morena — Empleado: Mi Horario
 */
$titulo_admin = 'Mi Horario';
$rol_admin_requerido = 'empleado';
require_once __DIR__ . '/../includes/admin_header.php';

$dias_semana = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
?>

<div class="row g-4">
  <!-- Horario semanal -->
  <div class="col-lg-7">
    <div class="pm-panel">
      <div class="pm-panel-header">
        <h3 class="pm-panel-title"><i class="bi bi-clock me-2"></i>Mi Horario Semanal</h3>
      </div>
      <div class="pm-panel-body">
        <table class="table table-hover" id="tablaHorario">
          <thead>
            <tr>
              <th>Día</th>
              <th>Entrada</th>
              <th>Salida</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody id="horarioBody">
            <tr><td colspan="4" class="text-center text-muted">Cargando...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Servicios asignados -->
  <div class="col-lg-5">
    <div class="pm-panel">
      <div class="pm-panel-header">
        <h3 class="pm-panel-title"><i class="bi bi-stars me-2"></i>Mis Servicios</h3>
      </div>
      <div class="pm-panel-body">
        <div id="serviciosLista">
          <div class="text-center text-muted py-3">Cargando...</div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const DIAS = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

document.addEventListener('DOMContentLoaded', async () => {
    const res = await apiCall('<?= URL_API ?>/admin/mi-horario.php');
    if (!res.success) return;

    // Horarios
    const tbody = document.getElementById('horarioBody');
    const horarios = res.data.horarios;
    if (horarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay horarios configurados</td></tr>';
    } else {
        tbody.innerHTML = horarios.map(h => `
            <tr>
                <td><strong>${DIAS[h.dia_semana] || 'Día ' + h.dia_semana}</strong></td>
                <td>${formatHora(h.hora_inicio)}</td>
                <td>${formatHora(h.hora_fin)}</td>
                <td>${h.activo == 1
                    ? '<span class="badge-estado badge-completada">Activo</span>'
                    : '<span class="badge-estado badge-cancelada">Inactivo</span>'}</td>
            </tr>
        `).join('');
    }

    // Servicios
    const serviciosDiv = document.getElementById('serviciosLista');
    const servicios = res.data.servicios;
    if (servicios.length === 0) {
        serviciosDiv.innerHTML = '<div class="text-center text-muted py-3">No hay servicios asignados</div>';
    } else {
        serviciosDiv.innerHTML = servicios.map(s => `
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                    <div class="fw-medium">${s.nombre}</div>
                    <small class="text-muted">${s.duracion_minutos} min</small>
                </div>
                <span class="fw-bold" style="color:var(--pm-dorado-oscuro)">${formatPrecio(s.precio)}</span>
            </div>
        `).join('');
    }
});
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
