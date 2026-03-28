<?php
/**
 * Piel Morena — Admin: Galería de Imágenes
 * 6 slots fijos (galeria-01 a galeria-06)
 * Subir imagen pisa la anterior del mismo slot
 */
$titulo_admin = 'Galería';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="pm-page-header">
  <div>
    <h2 class="pm-page-title">Galería del Sitio</h2>
    <p class="pm-page-subtitle">Subí hasta 6 imágenes que se muestran en la galería pública. Al subir una imagen nueva se reemplaza la anterior.</p>
  </div>
</div>

<!-- Grid de 6 slots -->
<div class="row g-4" id="galeriaGrid">
  <?php for ($i = 1; $i <= 6; $i++):
    $nombre = sprintf('galeria-%02d.jpg', $i);
    $ruta = ROOT_PATH . 'assets/img/gallery/' . $nombre;
    $existe = file_exists($ruta);
    $url = $existe ? URL_BASE . '/assets/img/gallery/' . $nombre . '?v=' . filemtime($ruta) : '';
  ?>
  <div class="col-lg-4 col-md-6">
    <div class="pm-galeria-slot" data-slot="<?= $i ?>">
      <div class="pm-galeria-preview" id="preview-<?= $i ?>">
        <?php if ($existe): ?>
          <img src="<?= $url ?>" alt="Galería <?= $i ?>" class="pm-galeria-img" />
        <?php else: ?>
          <div class="pm-galeria-empty">
            <i class="bi bi-image"></i>
            <span>Slot <?= $i ?> — vacío</span>
          </div>
        <?php endif; ?>
      </div>
      <div class="pm-galeria-actions">
        <label class="btn btn-sm btn-pm w-100" style="cursor:pointer;">
          <i class="bi bi-upload me-1"></i><?= $existe ? 'Cambiar imagen' : 'Subir imagen' ?>
          <input type="file" accept="image/jpeg,image/png,image/webp" class="d-none" onchange="subirImagen(this, <?= $i ?>)" />
        </label>
      </div>
      <div class="pm-galeria-info">
        <small class="text-muted"><?= $nombre ?> <?= $existe ? '— ' . round(filesize($ruta) / 1024) . ' KB' : '' ?></small>
      </div>
    </div>
  </div>
  <?php endfor; ?>
</div>

<style>
.pm-galeria-slot {
  background: var(--pm-admin-card-bg, #fff);
  border: 1px solid var(--pm-admin-border, #e8e0d0);
  border-radius: 12px;
  overflow: hidden;
  transition: box-shadow 0.2s;
}
.pm-galeria-slot:hover {
  box-shadow: 0 4px 16px rgba(138,118,80,0.12);
}
.pm-galeria-preview {
  position: relative;
  width: 100%;
  aspect-ratio: 4/3;
  background: var(--pm-crema, #ECE7D1);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.pm-galeria-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.pm-galeria-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  color: var(--pm-bronce, #8A7650);
  opacity: 0.5;
}
.pm-galeria-empty i {
  font-size: 2.5rem;
}
.pm-galeria-empty span {
  font-size: 0.8rem;
}
.pm-galeria-actions {
  padding: 0.75rem;
}
.pm-galeria-info {
  padding: 0 0.75rem 0.75rem;
  text-align: center;
}
.pm-galeria-uploading {
  position: absolute;
  inset: 0;
  background: rgba(248,244,232,0.85);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
}
</style>

<script>
async function subirImagen(input, slot) {
  const file = input.files[0];
  if (!file) return;

  // Validar tamaño
  if (file.size > 5 * 1024 * 1024) {
    Swal.fire({ icon: 'error', title: 'Archivo muy grande', text: 'La imagen no puede superar los 5 MB.', customClass: { popup: 'pm-modal' } });
    input.value = '';
    return;
  }

  // Mostrar spinner
  const preview = document.getElementById('preview-' + slot);
  const oldContent = preview.innerHTML;
  preview.innerHTML += '<div class="pm-galeria-uploading"><div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Subiendo...</span></div></div>';

  const formData = new FormData();
  formData.append('imagen', file);
  formData.append('slot', slot);

  try {
    const resp = await fetch('<?= URL_BASE ?>/api/admin/galeria.php', {
      method: 'POST',
      body: formData
    });
    const data = await resp.json();

    if (data.success) {
      // Actualizar preview con imagen nueva
      preview.innerHTML = '<img src="' + data.data.url + '" alt="Galería ' + slot + '" class="pm-galeria-img" />';

      // Actualizar botón
      const label = preview.closest('.pm-galeria-slot').querySelector('label');
      label.innerHTML = '<i class="bi bi-upload me-1"></i>Cambiar imagen<input type="file" accept="image/jpeg,image/png,image/webp" class="d-none" onchange="subirImagen(this, ' + slot + ')" />';

      Swal.fire({ icon: 'success', title: 'Imagen subida', text: 'Slot ' + slot + ' actualizado correctamente.', timer: 1500, showConfirmButton: false, customClass: { popup: 'pm-modal' } });
    } else {
      preview.innerHTML = oldContent;
      Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'No se pudo subir la imagen.', customClass: { popup: 'pm-modal' } });
    }
  } catch (err) {
    preview.innerHTML = oldContent;
    Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.', customClass: { popup: 'pm-modal' } });
  }

  input.value = '';
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
