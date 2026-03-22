/**
 * Piel Morena — Admin Panel JavaScript
 * Sidebar toggle, DataTables defaults, CRUD helpers, SweetAlert2 wrappers
 */

'use strict';

/* ============================================
   SIDEBAR TOGGLE
   ============================================ */
const AdminSidebar = (() => {
    const sidebar  = document.getElementById('pmSidebar');
    const toggle   = document.getElementById('pmSidebarToggle');
    let overlay    = null;

    function init() {
        if (!sidebar || !toggle) return;

        // Create overlay element
        overlay = document.createElement('div');
        overlay.className = 'pm-sidebar-overlay';
        document.body.appendChild(overlay);

        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', close);

        // Close on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') close();
        });
    }

    function close() {
        sidebar?.classList.remove('show');
        overlay?.classList.remove('show');
    }

    return { init, close };
})();

/* ============================================
   DATATABLES DEFAULTS
   ============================================ */
const DT_DEFAULTS = {
    language: {
        search:         'Buscar:',
        lengthMenu:     'Mostrar _MENU_ registros',
        info:           'Mostrando _START_ a _END_ de _TOTAL_',
        infoEmpty:      'Sin registros',
        infoFiltered:   '(filtrado de _MAX_ totales)',
        zeroRecords:    'No se encontraron resultados',
        emptyTable:     'No hay datos disponibles',
        paginate: {
            first:    'Primera',
            last:     'Última',
            next:     'Siguiente',
            previous: 'Anterior'
        }
    },
    pageLength: 15,
    responsive: true,
    dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip',
    order: [[0, 'desc']]
};

/* ============================================
   SWEETALERT2 WRAPPERS
   ============================================ */
const PM = {
    toast(icon, title) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon,
            title,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    },

    async confirm(title, text = '') {
        const result = await Swal.fire({
            title,
            text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#8A7650',
            cancelButtonColor: '#A85F52',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        });
        return result.isConfirmed;
    },

    async confirmDelete(nombre = '') {
        const result = await Swal.fire({
            title: 'Eliminar registro',
            html: nombre
                ? `¿Estás seguro de eliminar <strong>${nombre}</strong>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`
                : '¿Estás seguro de eliminar este registro?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#A85F52',
            cancelButtonColor: '#8A7650',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });
        return result.isConfirmed;
    },

    success(title, text = '') {
        Swal.fire({ title, text, icon: 'success', confirmButtonColor: '#8A7650' });
    },

    error(title, text = '') {
        Swal.fire({ title, text, icon: 'error', confirmButtonColor: '#8A7650' });
    }
};

/* ============================================
   API HELPER
   ============================================ */
async function apiCall(url, method = 'GET', data = null) {
    const opts = {
        method,
        headers: { 'Accept': 'application/json' }
    };

    if (data && method !== 'GET') {
        if (data instanceof FormData) {
            opts.body = data;
        } else {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(data);
        }
    }

    try {
        const res = await fetch(url, opts);
        const json = await res.json();
        return json;
    } catch (err) {
        console.error('API Error:', err);
        return { success: false, error: 'Error de conexión' };
    }
}

/* ============================================
   BADGE DE ESTADO
   ============================================ */
function badgeEstado(estado) {
    const labels = {
        pendiente:  'Pendiente',
        confirmada: 'Confirmada',
        en_proceso: 'En proceso',
        completada: 'Completada',
        cancelada:  'Cancelada'
    };
    return `<span class="badge-estado badge-${estado}">${labels[estado] || estado}</span>`;
}

/* ============================================
   FORMAT HELPERS
   ============================================ */
function formatPrecio(n) {
    return '$' + parseFloat(n).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatFecha(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr + 'T00:00:00');
    const dd = String(d.getDate()).padStart(2, '0');
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const yyyy = d.getFullYear();
    return `${dd}/${mm}/${yyyy}`;
}

function formatHora(timeStr) {
    if (!timeStr) return '-';
    const [h, m] = timeStr.split(':');
    const hr = parseInt(h);
    const ampm = hr >= 12 ? 'PM' : 'AM';
    return `${hr > 12 ? hr - 12 : hr || 12}:${m} ${ampm}`;
}

/* ============================================
   INIT
   ============================================ */
document.addEventListener('DOMContentLoaded', () => {
    AdminSidebar.init();
});
