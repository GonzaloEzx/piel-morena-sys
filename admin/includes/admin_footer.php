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

<?php if (!empty($extra_js)): ?>
  <?= $extra_js ?>
<?php endif; ?>

</body>
</html>
