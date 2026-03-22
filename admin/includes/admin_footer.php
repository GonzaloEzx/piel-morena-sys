  </div><!-- /.pm-admin-content -->
</div><!-- /.pm-admin-main -->

<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

<!-- Bootstrap 5.3 Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>

<!-- Admin JS -->
<script src="<?= URL_ADMIN ?>/assets/js/admin.js?v=<?= @filemtime(ADMIN_PATH . 'assets/js/admin.js') ?: time() ?>"></script>

<!-- Notificaciones campana -->
<script>
(function() {
    const NOTIF_API = '<?= URL_API ?>/notificaciones';
    const badge = document.getElementById('notifBadge');
    const lista = document.getElementById('notifLista');

    async function cargarNotificaciones() {
        try {
            const res = await apiCall(NOTIF_API + '/listar.php?limit=5');
            if (!res.success) return;

            const count = res.data.total_no_leidas;
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }

            const notifs = res.data.notificaciones;
            if (notifs.length === 0) {
                lista.innerHTML = '<div class="text-center text-muted py-3" style="font-size:.82rem">Sin notificaciones</div>';
                return;
            }

            lista.innerHTML = notifs.map(n => {
                const iconos = { recordatorio_cita: 'bell', sistema: 'gear', promocion: 'megaphone', general: 'info-circle' };
                const icon = iconos[n.tipo] || 'bell';
                const ago = timeAgo(n.created_at);
                const bgClass = n.leida == 0 ? 'bg-light' : '';
                return `<a href="#" class="dropdown-item d-flex gap-2 py-2 ${bgClass}" onclick="marcarLeida(${n.id})" style="white-space:normal">
                    <i class="bi bi-${icon} text-muted mt-1"></i>
                    <div>
                        <div style="font-size:.82rem;font-weight:${n.leida == 0 ? '600' : '400'}">${n.titulo}</div>
                        <div class="text-muted" style="font-size:.75rem">${n.mensaje.substring(0, 80)}${n.mensaje.length > 80 ? '...' : ''}</div>
                        <div class="text-muted" style="font-size:.7rem">${ago}</div>
                    </div>
                </a>`;
            }).join('');
        } catch (e) { /* silenciar errores de red */ }
    }

    function timeAgo(dateStr) {
        const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
        if (diff < 60) return 'Hace un momento';
        if (diff < 3600) return 'Hace ' + Math.floor(diff / 60) + ' min';
        if (diff < 86400) return 'Hace ' + Math.floor(diff / 3600) + 'h';
        return 'Hace ' + Math.floor(diff / 86400) + 'd';
    }

    window.marcarLeida = async function(id) {
        await apiCall(NOTIF_API + '/marcar_leida.php', 'PATCH', { id });
        cargarNotificaciones();
    };

    window.marcarTodasLeidas = async function() {
        await apiCall(NOTIF_API + '/marcar_leida.php', 'PATCH', { todas: true });
        cargarNotificaciones();
        PM.toast('success', 'Notificaciones marcadas como leídas');
    };

    // Cargar al inicio y cada 60 segundos
    document.addEventListener('DOMContentLoaded', () => {
        cargarNotificaciones();
        setInterval(cargarNotificaciones, 60000);
    });
})();
</script>

<?php if (!empty($extra_js)): ?>
  <?= $extra_js ?>
<?php endif; ?>

</body>
</html>
