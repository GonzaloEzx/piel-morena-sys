<?php
/**
 * Piel Morena — Admin Dashboard
 */
$titulo_admin = 'Dashboard';
require_once __DIR__ . '/includes/admin_header.php';
?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="pm-stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="pm-stat-icon bg-bronce"><i class="bi bi-calendar-check"></i></div>
        <div>
          <div class="pm-stat-value" id="statCitasHoy">-</div>
          <div class="pm-stat-label">Citas Hoy</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="pm-stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="pm-stat-icon bg-verde"><i class="bi bi-people-fill"></i></div>
        <div>
          <div class="pm-stat-value" id="statClientes">-</div>
          <div class="pm-stat-label">Clientes</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="pm-stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="pm-stat-icon bg-dorado"><i class="bi bi-cash-stack"></i></div>
        <div>
          <div class="pm-stat-value" id="statIngresos">-</div>
          <div class="pm-stat-label">Ingresos Mes</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="pm-stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="pm-stat-icon bg-rosa"><i class="bi bi-stars"></i></div>
        <div>
          <div class="pm-stat-value" id="statServicios">-</div>
          <div class="pm-stat-label">Servicios</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Row: Chart + Próximas Citas -->
<div class="row g-3 mb-4">
  <!-- Servicios más consultados (Chart) -->
  <div class="col-lg-7">
    <div class="pm-panel">
      <div class="pm-panel-header">
        <h3 class="pm-panel-title"><i class="bi bi-graph-up me-2"></i>Servicios M&aacute;s Consultados</h3>
        <span class="pm-stat-label">Últimos 30 días</span>
      </div>
      <div class="pm-panel-body">
        <div class="pm-chart-container">
          <canvas id="chartServicios"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Próximas citas -->
  <div class="col-lg-5">
    <div class="pm-panel">
      <div class="pm-panel-header">
        <h3 class="pm-panel-title"><i class="bi bi-clock me-2"></i>Pr&oacute;ximas Citas</h3>
      </div>
      <div class="pm-panel-body" id="listaCitasProximas">
        <div class="pm-empty-state">
          <i class="bi bi-hourglass-split d-block"></i>
          <p>Cargando...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Row: Citas por Estado -->
<div class="row g-3">
  <div class="col-lg-5">
    <div class="pm-panel">
      <div class="pm-panel-header">
        <h3 class="pm-panel-title"><i class="bi bi-pie-chart me-2"></i>Citas por Estado</h3>
        <span class="pm-stat-label">Últimos 30 días</span>
      </div>
      <div class="pm-panel-body">
        <div class="pm-chart-container" style="height:250px">
          <canvas id="chartEstados"></canvas>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="pm-panel">
      <div class="pm-panel-header">
        <h3 class="pm-panel-title"><i class="bi bi-lightning me-2"></i>Accesos R&aacute;pidos</h3>
      </div>
      <div class="pm-panel-body">
        <div class="row g-2">
          <div class="col-6 col-md-4">
            <a href="<?= URL_ADMIN ?>/views/citas.php" class="btn btn-pm-outline w-100 mb-2">
              <i class="bi bi-calendar-plus me-1"></i>Citas
            </a>
          </div>
          <div class="col-6 col-md-4">
            <a href="<?= URL_ADMIN ?>/views/servicios.php" class="btn btn-pm-outline w-100 mb-2">
              <i class="bi bi-stars me-1"></i>Servicios
            </a>
          </div>
          <div class="col-6 col-md-4">
            <a href="<?= URL_ADMIN ?>/views/clientes.php" class="btn btn-pm-outline w-100 mb-2">
              <i class="bi bi-people me-1"></i>Clientes
            </a>
          </div>
          <div class="col-6 col-md-4">
            <a href="<?= URL_ADMIN ?>/views/caja.php" class="btn btn-pm-outline w-100 mb-2">
              <i class="bi bi-cash-stack me-1"></i>Caja
            </a>
          </div>
          <div class="col-6 col-md-4">
            <a href="<?= URL_ADMIN ?>/views/mensajes.php" class="btn btn-pm-outline w-100 mb-2">
              <i class="bi bi-envelope me-1"></i>Mensajes
              <span class="badge bg-danger ms-1" id="badgeMensajes" style="display:none">0</span>
            </a>
          </div>
          <div class="col-6 col-md-4">
            <a href="<?= URL_ADMIN ?>/views/configuracion.php" class="btn btn-pm-outline w-100 mb-2">
              <i class="bi bi-gear me-1"></i>Config
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const res = await apiCall('<?= URL_API ?>/admin/estadisticas.php');
    if (!res.success) {
        PM.error('Error', res.error || 'No se pudieron cargar las estadísticas');
        return;
    }
    const d = res.data;

    // Stat cards
    document.getElementById('statCitasHoy').textContent = d.citas_hoy;
    document.getElementById('statClientes').textContent = d.clientes_total;
    document.getElementById('statIngresos').textContent = formatPrecio(d.ingresos_mes);
    document.getElementById('statServicios').textContent = d.servicios_activos;

    // Badge mensajes
    if (d.mensajes_no_leidos > 0) {
        const badge = document.getElementById('badgeMensajes');
        badge.textContent = d.mensajes_no_leidos;
        badge.style.display = 'inline';
    }

    // Chart: Servicios populares
    if (d.servicios_populares.length) {
        new Chart(document.getElementById('chartServicios'), {
            type: 'bar',
            data: {
                labels: d.servicios_populares.map(s => s.nombre),
                datasets: [{
                    label: 'Consultas de precio',
                    data: d.servicios_populares.map(s => s.consultas),
                    backgroundColor: [
                        'rgba(138,118,80,0.7)',
                        'rgba(149,124,98,0.7)',
                        'rgba(183,116,102,0.7)',
                        'rgba(219,206,165,0.7)',
                        'rgba(142,151,125,0.7)'
                    ],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { maxRotation: 45 } }
                }
            }
        });
    } else {
        document.getElementById('chartServicios').parentElement.innerHTML =
            '<div class="pm-empty-state"><i class="bi bi-graph-up d-block"></i><p>Sin consultas de precio aún</p></div>';
    }

    // Chart: Citas por estado
    if (d.citas_por_estado.length) {
        const colores = {
            pendiente:  '#FFE1AF',
            confirmada: '#8A7650',
            en_proceso: '#8E977D',
            completada: '#6C5D43',
            cancelada:  '#A85F52'
        };
        new Chart(document.getElementById('chartEstados'), {
            type: 'doughnut',
            data: {
                labels: d.citas_por_estado.map(e => e.estado.charAt(0).toUpperCase() + e.estado.slice(1)),
                datasets: [{
                    data: d.citas_por_estado.map(e => e.total),
                    backgroundColor: d.citas_por_estado.map(e => colores[e.estado] || '#ccc'),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } }
                },
                cutout: '60%'
            }
        });
    } else {
        document.getElementById('chartEstados').parentElement.innerHTML =
            '<div class="pm-empty-state"><i class="bi bi-pie-chart d-block"></i><p>Sin datos de citas</p></div>';
    }

    // Lista próximas citas
    const container = document.getElementById('listaCitasProximas');
    if (d.citas_proximas.length) {
        container.innerHTML = d.citas_proximas.map(c => `
            <div class="pm-quick-item">
                <div class="pm-quick-avatar" style="background:rgba(138,118,80,0.1);color:var(--pm-bronce)">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="pm-quick-info">
                    <div class="pm-quick-name">${c.cliente}</div>
                    <div class="pm-quick-detail">${c.servicio}</div>
                </div>
                <div class="text-end">
                    <div class="pm-quick-time">${formatHora(c.hora_inicio)}</div>
                    <div class="pm-quick-detail">${formatFecha(c.fecha)}</div>
                </div>
            </div>
        `).join('');
    } else {
        container.innerHTML = '<div class="pm-empty-state"><i class="bi bi-calendar-x d-block"></i><p>No hay citas próximas</p></div>';
    }
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
