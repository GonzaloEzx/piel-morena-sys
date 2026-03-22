<?php
/**
 * Piel Morena — Admin: Control de Caja
 */
$titulo_admin = 'Caja';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<!-- Resumen del día -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="pm-stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="pm-stat-icon bg-verde"><i class="bi bi-arrow-down-circle"></i></div>
        <div>
          <div class="pm-stat-value" id="cajaEntradas">$0</div>
          <div class="pm-stat-label">Entradas Hoy</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="pm-stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="pm-stat-icon bg-rosa"><i class="bi bi-arrow-up-circle"></i></div>
        <div>
          <div class="pm-stat-value" id="cajaSalidas">$0</div>
          <div class="pm-stat-label">Salidas Hoy</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="pm-stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="pm-stat-icon bg-dorado"><i class="bi bi-wallet2"></i></div>
        <div>
          <div class="pm-stat-value" id="cajaSaldo">$0</div>
          <div class="pm-stat-label">Saldo Hoy</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="d-grid gap-2">
      <button class="btn btn-pm" onclick="abrirModalMovimiento('entrada')">
        <i class="bi bi-plus-circle me-1"></i>Registrar Entrada
      </button>
      <button class="btn btn-pm-outline" onclick="abrirModalMovimiento('salida')">
        <i class="bi bi-dash-circle me-1"></i>Registrar Salida
      </button>
      <button class="btn btn-pm-outline" onclick="abrirModalVenta()">
        <i class="bi bi-bag-check me-1"></i>Venta Producto
      </button>
      <button class="btn btn-pm btn-pm-sm" style="background:var(--pm-tierra)" onclick="abrirModalCierre()">
        <i class="bi bi-lock me-1"></i>Cerrar Caja
      </button>
    </div>
  </div>
</div>

<!-- Movimientos -->
<div class="pm-panel">
  <div class="pm-panel-header">
    <h3 class="pm-panel-title"><i class="bi bi-cash-stack me-2"></i>Movimientos</h3>
    <div class="d-flex gap-2 align-items-center flex-wrap">
      <label class="form-label mb-0 me-1" style="font-size:.8rem">Desde</label>
      <input type="date" class="form-control form-control-sm" id="cajaFechaDesde" value="<?= date('Y-m-d') ?>" style="width:auto">
      <label class="form-label mb-0 me-1" style="font-size:.8rem">Hasta</label>
      <input type="date" class="form-control form-control-sm" id="cajaFechaHasta" value="<?= date('Y-m-d') ?>" style="width:auto">
      <button class="btn btn-pm btn-pm-sm" onclick="cargarCaja()"><i class="bi bi-search me-1"></i>Filtrar</button>
      <button class="btn btn-pm-outline btn-pm-sm" onclick="resetFechaHoy()"><i class="bi bi-calendar-day me-1"></i>Hoy</button>
    </div>
  </div>
  <div class="pm-panel-body">
    <table id="dtCaja" class="table table-hover w-100">
      <thead>
        <tr>
          <th>ID</th>
          <th>Tipo</th>
          <th>Concepto</th>
          <th>Método</th>
          <th>Monto</th>
          <th>Fecha</th>
          <th>Hora</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- Modal Movimiento -->
<div class="modal fade" id="modalMovimiento" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalMovTitle">Registrar Movimiento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formMovimiento">
        <div class="modal-body">
          <input type="hidden" name="tipo" id="movTipo">
          <div class="mb-3">
            <label class="form-label">Concepto</label>
            <input type="text" class="form-control" name="concepto" id="movConcepto" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Monto ($)</label>
            <input type="number" class="form-control" name="monto" id="movMonto" step="0.01" min="0.01" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Método de pago</label>
            <select class="form-select" name="metodo_pago">
              <option value="efectivo">Efectivo</option>
              <option value="tarjeta">Tarjeta</option>
              <option value="transferencia">Transferencia</option>
              <option value="otro">Otro</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-pm-outline btn-pm-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-pm btn-pm-sm">Registrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Cierre de Caja -->
<div class="modal fade" id="modalCierre" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cerrar Caja del Día</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3 text-center">
          <div class="mb-2"><strong>Resumen del día</strong></div>
          <div class="d-flex justify-content-between mb-1"><span>Entradas:</span><span class="text-success fw-bold" id="cierreEntradas">$0</span></div>
          <div class="d-flex justify-content-between mb-1"><span>Salidas:</span><span class="text-danger fw-bold" id="cierreSalidas">$0</span></div>
          <hr class="my-2">
          <div class="d-flex justify-content-between"><span><strong>Saldo:</strong></span><span class="fw-bold" id="cierreSaldo">$0</span></div>
        </div>
        <div class="mb-3">
          <label class="form-label">Notas (opcional)</label>
          <textarea class="form-control" id="cierreNotas" rows="2" placeholder="Observaciones del cierre..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-pm-outline btn-pm-sm" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-pm btn-pm-sm" onclick="ejecutarCierre()">Confirmar Cierre</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Venta de Producto -->
<div class="modal fade" id="modalVenta" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Venta de Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formVenta">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Producto</label>
            <select class="form-select" id="ventaProducto" required>
              <option value="">Seleccionar...</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Cantidad</label>
            <input type="number" class="form-control" id="ventaCantidad" min="1" value="1" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Método de pago</label>
            <select class="form-select" id="ventaMetodo">
              <option value="efectivo">Efectivo</option>
              <option value="tarjeta">Tarjeta</option>
              <option value="transferencia">Transferencia</option>
              <option value="otro">Otro</option>
            </select>
          </div>
          <div class="p-2 rounded" style="background:var(--pm-crema)">
            <div class="d-flex justify-content-between"><span>Subtotal:</span><strong id="ventaSubtotal">$0</strong></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-pm-outline btn-pm-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-pm btn-pm-sm">Registrar Venta</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Historial de Cierres -->
<div class="pm-panel mt-4">
  <div class="pm-panel-header" style="cursor:pointer" onclick="toggleCierres()">
    <h3 class="pm-panel-title"><i class="bi bi-clock-history me-2"></i>Historial de Cierres</h3>
    <i class="bi bi-chevron-down" id="cierresChevron"></i>
  </div>
  <div class="pm-panel-body" id="cierresBody" style="display:none">
    <table id="dtCierres" class="table table-hover w-100">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Entradas</th>
          <th>Salidas</th>
          <th>Saldo</th>
          <th>Usuario</th>
          <th>Notas</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<script>
let dtCaja;
let dtCierres;
let modalMov;
let modalCierre;
let modalVenta;
let productosCache = [];

document.addEventListener('DOMContentLoaded', () => {
    modalMov = new bootstrap.Modal(document.getElementById('modalMovimiento'));
    modalCierre = new bootstrap.Modal(document.getElementById('modalCierre'));
    modalVenta = new bootstrap.Modal(document.getElementById('modalVenta'));

    // Eventos de venta
    document.getElementById('ventaProducto').addEventListener('change', calcularSubtotal);
    document.getElementById('ventaCantidad').addEventListener('input', calcularSubtotal);

    dtCaja = $('#dtCaja').DataTable({
        ...DT_DEFAULTS,
        ajax: {
            url: '<?= URL_API ?>/admin/caja.php',
            data: () => ({
                fecha_desde: document.getElementById('cajaFechaDesde').value,
                fecha_hasta: document.getElementById('cajaFechaHasta').value
            }),
            dataSrc: (json) => json.success ? json.data.movimientos : []
        },
        columns: [
            { data: 'id' },
            { data: 'tipo', render: (d) => d === 'entrada'
                ? '<span class="badge-estado badge-completada">Entrada</span>'
                : '<span class="badge-estado badge-cancelada">Salida</span>'
            },
            { data: 'concepto' },
            { data: 'metodo_pago', defaultContent: 'efectivo' },
            { data: null, render: (d) => `<span class="${d.tipo === 'entrada' ? 'text-success' : 'text-danger'} fw-bold">${d.tipo === 'salida' ? '-' : '+'}${formatPrecio(d.monto)}</span>` },
            { data: 'fecha', render: (d) => formatFecha(d) },
            { data: 'created_at', render: (d) => d ? d.substring(11, 16) : '-' }
        ],
        order: [[0, 'desc']],
        drawCallback: function() {
            actualizarResumen();
        }
    });

    cargarCaja();
});

function cargarCaja() {
    dtCaja.ajax.reload();
}

function resetFechaHoy() {
    const hoy = new Date().toISOString().slice(0, 10);
    document.getElementById('cajaFechaDesde').value = hoy;
    document.getElementById('cajaFechaHasta').value = hoy;
    cargarCaja();
}

async function actualizarResumen() {
    const desde = document.getElementById('cajaFechaDesde').value;
    const hasta = document.getElementById('cajaFechaHasta').value;
    const res = await apiCall('<?= URL_API ?>/admin/caja.php?fecha_desde=' + desde + '&fecha_hasta=' + hasta);
    if (res.success) {
        document.getElementById('cajaEntradas').textContent = formatPrecio(res.data.resumen.entradas);
        document.getElementById('cajaSalidas').textContent = formatPrecio(res.data.resumen.salidas);
        document.getElementById('cajaSaldo').textContent = formatPrecio(res.data.resumen.saldo);
    }
}

function abrirModalMovimiento(tipo) {
    document.getElementById('modalMovTitle').textContent = tipo === 'entrada' ? 'Registrar Entrada' : 'Registrar Salida';
    document.getElementById('movTipo').value = tipo;
    document.getElementById('formMovimiento').reset();
    document.getElementById('movTipo').value = tipo;
    modalMov.show();
}

document.getElementById('formMovimiento').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const res = await apiCall('<?= URL_API ?>/admin/caja.php', 'POST', Object.fromEntries(fd));
    if (res.success) {
        PM.toast('success', 'Movimiento registrado');
        modalMov.hide();
        cargarCaja();
    } else {
        PM.error('Error', res.error);
    }
});

// ── Cierre de Caja ──
async function abrirModalCierre() {
    const hoy = new Date().toISOString().slice(0, 10);
    const res = await apiCall('<?= URL_API ?>/admin/caja.php?fecha=' + hoy);
    if (res.success) {
        document.getElementById('cierreEntradas').textContent = formatPrecio(res.data.resumen.entradas);
        document.getElementById('cierreSalidas').textContent = formatPrecio(res.data.resumen.salidas);
        document.getElementById('cierreSaldo').textContent = formatPrecio(res.data.resumen.saldo);
    }
    document.getElementById('cierreNotas').value = '';
    modalCierre.show();
}

async function ejecutarCierre() {
    if (!await PM.confirm('¿Confirmar cierre de caja?', 'Esta acción no se puede deshacer')) return;
    const notas = document.getElementById('cierreNotas').value;
    const res = await apiCall('<?= URL_API ?>/caja/cierre_caja.php', 'POST', { notas });
    if (res.success) {
        PM.success('Caja cerrada', `Saldo del día: ${formatPrecio(res.data.saldo)}`);
        modalCierre.hide();
        cargarCierres();
    } else {
        PM.error('Error', res.error);
    }
}

function toggleCierres() {
    const body = document.getElementById('cierresBody');
    const chev = document.getElementById('cierresChevron');
    if (body.style.display === 'none') {
        body.style.display = '';
        chev.className = 'bi bi-chevron-up';
        cargarCierres();
    } else {
        body.style.display = 'none';
        chev.className = 'bi bi-chevron-down';
    }
}

function cargarCierres() {
    if (dtCierres) {
        dtCierres.ajax.reload();
        return;
    }
    dtCierres = $('#dtCierres').DataTable({
        ...DT_DEFAULTS,
        pageLength: 10,
        ajax: {
            url: '<?= URL_API ?>/caja/cierre_caja.php',
            dataSrc: (json) => json.success ? json.data : []
        },
        columns: [
            { data: 'fecha', render: (d) => formatFecha(d) },
            { data: 'total_entradas', render: (d) => `<span class="text-success">${formatPrecio(d)}</span>` },
            { data: 'total_salidas', render: (d) => `<span class="text-danger">${formatPrecio(d)}</span>` },
            { data: 'saldo', render: (d) => `<strong>${formatPrecio(d)}</strong>` },
            { data: 'usuario' },
            { data: 'notas', defaultContent: '-' }
        ],
        order: [[0, 'desc']]
    });
}

// ── Venta de Producto ──
async function abrirModalVenta() {
    const select = document.getElementById('ventaProducto');
    // Cargar productos solo si cache vacio
    if (productosCache.length === 0) {
        const res = await apiCall('<?= URL_API ?>/admin/productos.php');
        if (res.success) {
            productosCache = res.data.filter(p => p.activo == 1 && p.stock > 0);
        }
    }
    select.innerHTML = '<option value="">Seleccionar...</option>';
    productosCache.forEach(p => {
        select.innerHTML += `<option value="${p.id}" data-precio="${p.precio}" data-stock="${p.stock}">${p.nombre} (${formatPrecio(p.precio)}) — Stock: ${p.stock}</option>`;
    });
    document.getElementById('ventaCantidad').value = 1;
    document.getElementById('ventaSubtotal').textContent = '$0';
    document.getElementById('formVenta').reset();
    modalVenta.show();
}

function calcularSubtotal() {
    const sel = document.getElementById('ventaProducto');
    const opt = sel.options[sel.selectedIndex];
    const precio = parseFloat(opt?.dataset?.precio || 0);
    const cant = parseInt(document.getElementById('ventaCantidad').value) || 0;
    document.getElementById('ventaSubtotal').textContent = formatPrecio(precio * cant);
}

document.getElementById('formVenta').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id_producto = document.getElementById('ventaProducto').value;
    const cantidad = parseInt(document.getElementById('ventaCantidad').value);
    const metodo_pago = document.getElementById('ventaMetodo').value;

    if (!id_producto) return PM.error('Error', 'Selecciona un producto');
    if (cantidad < 1) return PM.error('Error', 'Cantidad mínima es 1');

    const res = await apiCall('<?= URL_API ?>/caja/registrar_venta.php', 'POST', { id_producto, cantidad, metodo_pago });
    if (res.success) {
        PM.toast('success', 'Venta registrada');
        modalVenta.hide();
        productosCache = []; // Limpiar cache para recargar stock
        cargarCaja();
    } else {
        PM.error('Error', res.error);
    }
});
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
