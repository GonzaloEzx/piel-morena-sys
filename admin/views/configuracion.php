<?php
/**
 * Piel Morena — Admin: Configuración del Sistema
 */
$titulo_admin = 'Configuración';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="pm-panel">
  <div class="pm-panel-header">
    <h3 class="pm-panel-title"><i class="bi bi-gear-fill me-2"></i>Configuraci&oacute;n General</h3>
  </div>
  <div class="pm-panel-body">
    <form id="formConfig">
      <div class="row g-4">
        <!-- Negocio -->
        <div class="col-md-6">
          <h5 class="mb-3" style="font-family:var(--pm-font-heading);color:var(--pm-text-heading)">Datos del Negocio</h5>
          <div class="mb-3">
            <label class="form-label">Nombre del negocio</label>
            <input type="text" class="form-control" name="nombre_negocio" id="cfgNombre">
          </div>
          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono" id="cfgTelefono">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="cfgEmail">
          </div>
          <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" class="form-control" name="direccion" id="cfgDireccion">
          </div>
        </div>

        <!-- Horarios -->
        <div class="col-md-6">
          <h5 class="mb-3" style="font-family:var(--pm-font-heading);color:var(--pm-text-heading)">Horarios y Citas</h5>
          <div class="row g-3 mb-3">
            <div class="col-6">
              <label class="form-label">Hora apertura</label>
              <input type="time" class="form-control" name="horario_apertura" id="cfgApertura">
            </div>
            <div class="col-6">
              <label class="form-label">Hora cierre</label>
              <input type="time" class="form-control" name="horario_cierre" id="cfgCierre">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Intervalo entre citas (minutos)</label>
            <input type="number" class="form-control" name="intervalo_citas" id="cfgIntervalo" min="10" step="5">
          </div>
          <div class="mb-3">
            <label class="form-label">Días laborales</label>
            <div id="cfgDias" class="d-flex flex-wrap gap-2">
              <label class="form-check-label"><input type="checkbox" class="form-check-input" value="1"> Lun</label>
              <label class="form-check-label"><input type="checkbox" class="form-check-input" value="2"> Mar</label>
              <label class="form-check-label"><input type="checkbox" class="form-check-input" value="3"> Mié</label>
              <label class="form-check-label"><input type="checkbox" class="form-check-input" value="4"> Jue</label>
              <label class="form-check-label"><input type="checkbox" class="form-check-input" value="5"> Vie</label>
              <label class="form-check-label"><input type="checkbox" class="form-check-input" value="6"> Sáb</label>
              <label class="form-check-label"><input type="checkbox" class="form-check-input" value="7"> Dom</label>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Moneda</label>
            <input type="text" class="form-control" name="moneda" id="cfgMoneda" maxlength="5">
          </div>
        </div>
      </div>

      <hr class="my-4">
      <div class="text-end">
        <button type="submit" class="btn btn-pm">
          <i class="bi bi-check-lg me-1"></i>Guardar Configuraci&oacute;n
        </button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', cargarConfig);

async function cargarConfig() {
    const res = await apiCall('<?= URL_API ?>/admin/configuracion.php');
    if (!res.success) return PM.error('Error', res.error);

    const cfg = {};
    res.data.forEach(item => { cfg[item.clave] = item.valor; });

    document.getElementById('cfgNombre').value = cfg.nombre_negocio || '';
    document.getElementById('cfgTelefono').value = cfg.telefono || '';
    document.getElementById('cfgEmail').value = cfg.email || '';
    document.getElementById('cfgDireccion').value = cfg.direccion || '';
    document.getElementById('cfgApertura').value = cfg.horario_apertura || '09:00';
    document.getElementById('cfgCierre').value = cfg.horario_cierre || '20:00';
    document.getElementById('cfgIntervalo').value = cfg.intervalo_citas || '30';
    document.getElementById('cfgMoneda').value = cfg.moneda || 'MXN';

    // Días laborales
    const dias = (cfg.dias_laborales || '').split(',');
    document.querySelectorAll('#cfgDias input[type="checkbox"]').forEach(cb => {
        cb.checked = dias.includes(cb.value);
    });
}

document.getElementById('formConfig').addEventListener('submit', async (e) => {
    e.preventDefault();

    // Recopilar días laborales
    const dias = [];
    document.querySelectorAll('#cfgDias input:checked').forEach(cb => dias.push(cb.value));

    const data = {
        nombre_negocio:   document.getElementById('cfgNombre').value,
        telefono:         document.getElementById('cfgTelefono').value,
        email:            document.getElementById('cfgEmail').value,
        direccion:        document.getElementById('cfgDireccion').value,
        horario_apertura: document.getElementById('cfgApertura').value,
        horario_cierre:   document.getElementById('cfgCierre').value,
        intervalo_citas:  document.getElementById('cfgIntervalo').value,
        moneda:           document.getElementById('cfgMoneda').value,
        dias_laborales:   dias.join(',')
    };

    const res = await apiCall('<?= URL_API ?>/admin/configuracion.php', 'PUT', data);
    if (res.success) {
        PM.success('Configuración guardada', 'Los cambios se aplicarán de inmediato.');
    } else {
        PM.error('Error', res.error);
    }
});
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
