<?php
/**
 * Piel Morena — Admin: Reportes y Analytics
 */
$titulo_admin = 'Reportes';
require_once __DIR__ . '/../includes/admin_header.php';

$hoy = date('Y-m-d');
$hace30 = date('Y-m-d', strtotime('-30 days'));
?>

<!-- Filtros globales -->
<div class="pm-panel mb-4">
  <div class="pm-panel-body">
    <div class="d-flex gap-2 align-items-center flex-wrap">
      <i class="bi bi-funnel me-1"></i>
      <label class="form-label mb-0" style="font-size:.8rem">Desde</label>
      <input type="date" class="form-control form-control-sm" id="filtroDesde" value="<?= $hace30 ?>" style="width:auto">
      <label class="form-label mb-0" style="font-size:.8rem">Hasta</label>
      <input type="date" class="form-control form-control-sm" id="filtroHasta" value="<?= $hoy ?>" style="width:auto">
      <button class="btn btn-pm btn-pm-sm" onclick="cargarTodo()"><i class="bi bi-search me-1"></i>Aplicar</button>
      <button class="btn btn-pm-outline btn-pm-sm" onclick="setRango(7)">7 días</button>
      <button class="btn btn-pm-outline btn-pm-sm" onclick="setRango(30)">30 días</button>
      <button class="btn btn-pm-outline btn-pm-sm" onclick="setRango(90)">90 días</button>
    </div>
  </div>
</div>

<!-- KPIs comparativos -->
<div class="row g-3 mb-4" id="kpiRow">
  <div class="col-6 col-lg-3">
    <div class="pm-stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="pm-stat-icon bg-bronce"><i class="bi bi-calendar-check"></i></div>
        <div>
          <div class="pm-stat-value" id="kpiCitas">-</div>
          <div class="pm-stat-label">Citas</div>
          <small class="text-muted" id="kpiCitasVar"></small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="pm-stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="pm-stat-icon bg-verde"><i class="bi bi-cash-stack"></i></div>
        <div>
          <div class="pm-stat-value" id="kpiIngresos">-</div>
          <div class="pm-stat-label">Ingresos</div>
          <small class="text-muted" id="kpiIngresosVar"></small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="pm-stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="pm-stat-icon bg-dorado"><i class="bi bi-receipt"></i></div>
        <div>
          <div class="pm-stat-value" id="kpiTicket">-</div>
          <div class="pm-stat-label">Ticket Promedio</div>
          <small class="text-muted" id="kpiTicketVar"></small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="pm-stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="pm-stat-icon bg-rosa"><i class="bi bi-x-circle"></i></div>
        <div>
          <div class="pm-stat-value" id="kpiCancelacion">-</div>
          <div class="pm-stat-label">Tasa Cancelación</div>
          <small class="text-muted" id="kpiCancelacionVar"></small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Gráfico: Ingresos por período -->
<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="pm-panel">
      <div class="pm-panel-header">
        <h3 class="pm-panel-title"><i class="bi bi-graph-up me-2"></i>Ingresos vs Egresos</h3>
        <div class="d-flex gap-2 align-items-center">
          <select class="form-select form-select-sm" id="agruparIngresos" style="width:auto" onchange="cargarIngresos()">
            <option value="dia">Por día</option>
            <option value="semana">Por semana</option>
            <option value="mes">Por mes</option>
          </select>
          <button class="btn btn-pm-outline btn-pm-sm" onclick="exportarCSV('ingresos')" title="Exportar CSV">
            <i class="bi bi-download"></i>
          </button>
        </div>
      </div>
      <div class="pm-panel-body">
        <canvas id="chartIngresos" height="280"></canvas>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="pm-panel">
      <div class="pm-panel-header">
        <h3 class="pm-panel-title"><i class="bi bi-credit-card me-2"></i>Por Método de Pago</h3>
        <button class="btn btn-pm-outline btn-pm-sm" onclick="exportarCSV('metodo_pago')" title="Exportar CSV">
          <i class="bi bi-download"></i>
        </button>
      </div>
      <div class="pm-panel-body">
        <canvas id="chartMetodoPago" height="280"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Gráfico: Servicios populares + Ingresos por servicio -->
<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <div class="pm-panel">
      <div class="pm-panel-header">
        <h3 class="pm-panel-title"><i class="bi bi-bar-chart me-2"></i>Servicios Más Consultados</h3>
        <button class="btn btn-pm-outline btn-pm-sm" onclick="exportarCSV('servicios_populares')" title="Exportar CSV">
          <i class="bi bi-download"></i>
        </button>
      </div>
      <div class="pm-panel-body">
        <canvas id="chartServicios" height="280"></canvas>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="pm-panel">
      <div class="pm-panel-header">
        <h3 class="pm-panel-title"><i class="bi bi-trophy me-2"></i>Ingresos por Servicio</h3>
        <button class="btn btn-pm-outline btn-pm-sm" onclick="exportarCSV('ingresos_servicio')" title="Exportar CSV">
          <i class="bi bi-download"></i>
        </button>
      </div>
      <div class="pm-panel-body">
        <canvas id="chartIngresosServicio" height="280"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Gráfico: Clientes + Horarios pico + Días semana -->
<div class="row g-3 mb-4">
  <div class="col-lg-4">
    <div class="pm-panel">
      <div class="pm-panel-header">
        <h3 class="pm-panel-title"><i class="bi bi-people me-2"></i>Clientes</h3>
      </div>
      <div class="pm-panel-body">
        <canvas id="chartClientes" height="240"></canvas>
        <div class="text-center mt-2">
          <small class="text-muted" id="clientesDetalle"></small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="pm-panel">
      <div class="pm-panel-header">
        <h3 class="pm-panel-title"><i class="bi bi-clock me-2"></i>Horarios Pico</h3>
        <button class="btn btn-pm-outline btn-pm-sm" onclick="exportarCSV('horarios')" title="Exportar CSV">
          <i class="bi bi-download"></i>
        </button>
      </div>
      <div class="pm-panel-body">
        <canvas id="chartHorarios" height="240"></canvas>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="pm-panel">
      <div class="pm-panel-header">
        <h3 class="pm-panel-title"><i class="bi bi-calendar-week me-2"></i>Citas por Día</h3>
      </div>
      <div class="pm-panel-body">
        <canvas id="chartDiasSemana" height="240"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Consultas de precio por día -->
<div class="pm-panel mb-4">
  <div class="pm-panel-header">
    <h3 class="pm-panel-title"><i class="bi bi-graph-up-arrow me-2"></i>Consultas de Precio por Día</h3>
    <button class="btn btn-pm-outline btn-pm-sm" onclick="exportarCSV('consultas_dia')" title="Exportar CSV">
      <i class="bi bi-download"></i>
    </button>
  </div>
  <div class="pm-panel-body">
    <canvas id="chartConsultasDia" height="200"></canvas>
  </div>
</div>

<script>
// ── Estado global ──
const CHART_COLORS = [
    '#8B6914', '#D4A853', '#C9956B', '#2C1810', '#A0522D',
    '#CD853F', '#DEB887', '#D2691E', '#BC8F8F', '#F4A460'
];
let charts = {};
let datosIngresos = null;
let datosServicios = null;
let datosTendencias = null;

// ── Helpers ──
function getFechas() {
    return {
        desde: document.getElementById('filtroDesde').value,
        hasta: document.getElementById('filtroHasta').value
    };
}

function setRango(dias) {
    const hasta = new Date();
    const desde = new Date();
    desde.setDate(desde.getDate() - dias);
    document.getElementById('filtroDesde').value = desde.toISOString().slice(0, 10);
    document.getElementById('filtroHasta').value = hasta.toISOString().slice(0, 10);
    cargarTodo();
}

function badgeVariacion(valor) {
    if (valor === null || valor === undefined) return '';
    const icon = valor >= 0 ? 'arrow-up' : 'arrow-down';
    const cls  = valor >= 0 ? 'text-success' : 'text-danger';
    return `<span class="${cls}"><i class="bi bi-${icon}"></i> ${Math.abs(valor)}%</span>`;
}

function destroyChart(key) {
    if (charts[key]) { charts[key].destroy(); charts[key] = null; }
}

// ── Cargar todo ──
function cargarTodo() {
    cargarTendencias();
    cargarIngresos();
    cargarServiciosPopulares();
}

// ── Tendencias (KPIs + clientes + horarios + días semana) ──
async function cargarTendencias() {
    const {desde, hasta} = getFechas();
    const res = await apiCall(`<?= URL_API ?>/analytics/tendencias.php?fecha_desde=${desde}&fecha_hasta=${hasta}`);
    if (!res.success) return;

    datosTendencias = res.data;
    const c = res.data.comparativa;

    // KPIs
    document.getElementById('kpiCitas').textContent = c.citas.actual;
    document.getElementById('kpiCitasVar').innerHTML = badgeVariacion(c.citas.variacion) + ' vs período anterior';

    document.getElementById('kpiIngresos').textContent = formatPrecio(c.ingresos.actual);
    document.getElementById('kpiIngresosVar').innerHTML = badgeVariacion(c.ingresos.variacion) + ' vs período anterior';

    document.getElementById('kpiTicket').textContent = formatPrecio(c.ticket_promedio.actual);
    document.getElementById('kpiTicketVar').innerHTML = badgeVariacion(c.ticket_promedio.variacion) + ' vs período anterior';

    document.getElementById('kpiCancelacion').textContent = c.tasa_cancelacion.actual + '%';
    const diffCancel = (c.tasa_cancelacion.actual - c.tasa_cancelacion.anterior).toFixed(1);
    const cancelCls = diffCancel <= 0 ? 'text-success' : 'text-danger';
    const cancelIcon = diffCancel <= 0 ? 'arrow-down' : 'arrow-up';
    document.getElementById('kpiCancelacionVar').innerHTML =
        `<span class="${cancelCls}"><i class="bi bi-${cancelIcon}"></i> ${Math.abs(diffCancel)}pp</span> vs anterior`;

    // Clientes (doughnut)
    const cl = res.data.clientes;
    destroyChart('clientes');
    charts.clientes = new Chart(document.getElementById('chartClientes'), {
        type: 'doughnut',
        data: {
            labels: ['Nuevos', 'Recurrentes'],
            datasets: [{
                data: [cl.nuevos, cl.recurrentes],
                backgroundColor: ['#D4A853', '#8B6914']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
    document.getElementById('clientesDetalle').textContent =
        `${cl.total} clientes — ${cl.nuevos} nuevos, ${cl.recurrentes} recurrentes`;

    // Horarios pico (bar)
    const hp = res.data.horarios_pico;
    destroyChart('horarios');
    charts.horarios = new Chart(document.getElementById('chartHorarios'), {
        type: 'bar',
        data: {
            labels: hp.map(h => `${h.hora}:00`),
            datasets: [{
                label: 'Citas',
                data: hp.map(h => h.total),
                backgroundColor: '#C9956B'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Citas por día de semana (bar)
    const diasNombre = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    const dds = res.data.citas_por_dia_semana;
    const diasData = new Array(7).fill(0);
    dds.forEach(d => { diasData[d.dia - 1] = parseInt(d.total); });
    destroyChart('diasSemana');
    charts.diasSemana = new Chart(document.getElementById('chartDiasSemana'), {
        type: 'bar',
        data: {
            labels: diasNombre,
            datasets: [{
                label: 'Citas',
                data: diasData,
                backgroundColor: '#D4A853'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
}

// ── Ingresos ──
async function cargarIngresos() {
    const {desde, hasta} = getFechas();
    const agrupar = document.getElementById('agruparIngresos').value;
    const res = await apiCall(`<?= URL_API ?>/analytics/ingresos.php?fecha_desde=${desde}&fecha_hasta=${hasta}&agrupar=${agrupar}`);
    if (!res.success) return;

    datosIngresos = res.data;
    const pp = res.data.por_periodo;

    // Gráfico ingresos vs egresos (bar)
    destroyChart('ingresos');
    charts.ingresos = new Chart(document.getElementById('chartIngresos'), {
        type: 'bar',
        data: {
            labels: pp.map(p => agrupar === 'mes' ? p.periodo : formatFecha(p.periodo)),
            datasets: [
                {
                    label: 'Ingresos',
                    data: pp.map(p => parseFloat(p.ingresos)),
                    backgroundColor: 'rgba(139, 105, 20, 0.7)',
                    borderColor: '#8B6914',
                    borderWidth: 1
                },
                {
                    label: 'Egresos',
                    data: pp.map(p => parseFloat(p.egresos)),
                    backgroundColor: 'rgba(220, 53, 69, 0.5)',
                    borderColor: '#dc3545',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => formatPrecio(v) } }
            }
        }
    });

    // Método de pago (doughnut)
    const pm = res.data.por_metodo;
    destroyChart('metodoPago');
    charts.metodoPago = new Chart(document.getElementById('chartMetodoPago'), {
        type: 'doughnut',
        data: {
            labels: pm.map(m => m.metodo_pago || 'efectivo'),
            datasets: [{
                data: pm.map(m => parseFloat(m.total)),
                backgroundColor: CHART_COLORS.slice(0, pm.length)
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Ingresos por servicio (horizontal bar)
    const ps = res.data.por_servicio;
    destroyChart('ingresosServicio');
    charts.ingresosServicio = new Chart(document.getElementById('chartIngresosServicio'), {
        type: 'bar',
        data: {
            labels: ps.map(s => s.nombre),
            datasets: [{
                label: 'Ingresos',
                data: ps.map(s => parseFloat(s.total)),
                backgroundColor: CHART_COLORS.slice(0, ps.length)
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { callback: v => formatPrecio(v) } }
            }
        }
    });
}

// ── Servicios populares ──
async function cargarServiciosPopulares() {
    const {desde, hasta} = getFechas();
    const res = await apiCall(`<?= URL_API ?>/analytics/servicios_populares.php?fecha_desde=${desde}&fecha_hasta=${hasta}`);
    if (!res.success) return;

    datosServicios = res.data;

    // Top servicios (horizontal bar)
    const ts = res.data.top_servicios;
    destroyChart('servicios');
    charts.servicios = new Chart(document.getElementById('chartServicios'), {
        type: 'bar',
        data: {
            labels: ts.map(s => s.nombre),
            datasets: [{
                label: 'Consultas',
                data: ts.map(s => parseInt(s.consultas)),
                backgroundColor: CHART_COLORS.slice(0, ts.length)
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Consultas por día (line)
    const cd = res.data.consultas_por_dia;
    destroyChart('consultasDia');
    charts.consultasDia = new Chart(document.getElementById('chartConsultasDia'), {
        type: 'line',
        data: {
            labels: cd.map(d => formatFecha(d.fecha)),
            datasets: [{
                label: 'Consultas de precio',
                data: cd.map(d => parseInt(d.consultas)),
                borderColor: '#8B6914',
                backgroundColor: 'rgba(139, 105, 20, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
}

// ── Exportar CSV ──
function exportarCSV(tipo) {
    let csv = '';
    let filename = '';

    switch (tipo) {
        case 'ingresos':
            if (!datosIngresos) return;
            csv = 'Periodo,Ingresos,Egresos,Movimientos\n';
            datosIngresos.por_periodo.forEach(p => {
                csv += `${p.periodo},${p.ingresos},${p.egresos},${p.movimientos}\n`;
            });
            filename = 'ingresos';
            break;

        case 'metodo_pago':
            if (!datosIngresos) return;
            csv = 'Método de Pago,Total\n';
            datosIngresos.por_metodo.forEach(m => {
                csv += `${m.metodo_pago},${m.total}\n`;
            });
            filename = 'metodos_pago';
            break;

        case 'servicios_populares':
            if (!datosServicios) return;
            csv = 'Servicio,Precio,Consultas\n';
            datosServicios.top_servicios.forEach(s => {
                csv += `"${s.nombre}",${s.precio},${s.consultas}\n`;
            });
            filename = 'servicios_populares';
            break;

        case 'ingresos_servicio':
            if (!datosIngresos) return;
            csv = 'Servicio,Citas Completadas,Total Ingresos\n';
            datosIngresos.por_servicio.forEach(s => {
                csv += `"${s.nombre}",${s.citas},${s.total}\n`;
            });
            filename = 'ingresos_por_servicio';
            break;

        case 'consultas_dia':
            if (!datosServicios) return;
            csv = 'Fecha,Consultas\n';
            datosServicios.consultas_por_dia.forEach(d => {
                csv += `${d.fecha},${d.consultas}\n`;
            });
            filename = 'consultas_precio_dia';
            break;

        case 'horarios':
            if (!datosTendencias) return;
            csv = 'Hora,Citas\n';
            datosTendencias.horarios_pico.forEach(h => {
                csv += `${h.hora}:00,${h.total}\n`;
            });
            filename = 'horarios_pico';
            break;

        default:
            return;
    }

    const {desde, hasta} = getFechas();
    filename += `_${desde}_${hasta}.csv`;

    const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
    URL.revokeObjectURL(link.href);
    PM.toast('success', 'CSV descargado');
}

// ── Init ──
document.addEventListener('DOMContentLoaded', () => {
    cargarTodo();
});
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
