<?php
/**
 * Piel Morena — Admin: qué servicios hace cada profesional y cuándo atiende.
 * La agenda (includes/agenda.php) usa estos datos para sugerir horarios.
 * Pensada primero para el celular: tarjetas en una columna y editor a
 * pantalla completa; en escritorio, grilla y modal.
 */
$titulo_admin = 'Profesionales';
$extra_css = '<style>
  .pm-pro-intro { color: var(--pm-text-muted, #5C4F42); margin-bottom: 1rem; }
  .pm-pro-alerta { border:1px solid #E8C9A0; background:#FBF1E4; color:#6B4520; border-radius:10px; padding:.75rem 1rem; margin-bottom:1rem; }
  .pm-pro-alerta strong { display:block; margin-bottom:.15rem; }
  .pm-pro-grid { display:grid; gap:.75rem; }
  .pm-pro-card { background:#fff; border:1px solid var(--pm-border-light, #E6DFC8); border-radius:12px; padding:1rem; display:flex; flex-direction:column; gap:.5rem; }
  .pm-pro-card[data-activo="0"] { opacity:.7; }
  .pm-pro-nombre { font-weight:600; font-size:1.05rem; margin:0; display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
  .pm-pro-chips { display:flex; flex-wrap:wrap; gap:.35rem; }
  .pm-pro-chip { font-size:.8rem; padding:.2rem .6rem; border-radius:999px; background:#F1ECDD; color:#5C4F42; }
  .pm-pro-chip.vacio { background:#FBF1E4; color:#6B4520; }
  .pm-pro-horario { font-size:.875rem; color: var(--pm-text-muted, #5C4F42); margin:0; }
  .pm-pro-card .btn { min-height:44px; margin-top:auto; }
  .pm-pro-seccion { font-size:1rem; font-weight:600; margin:0 0 .25rem; }
  .pm-pro-ayuda { font-size:.85rem; color: var(--pm-text-muted, #5C4F42); margin-bottom:.75rem; }
  .pm-pro-cat { border:1px solid var(--pm-border-light, #E6DFC8); border-radius:10px; margin-bottom:.5rem; background:#fff; }
  .pm-pro-cat-fila { display:flex; align-items:center; gap:.5rem; padding:.25rem .5rem .25rem .75rem; min-height:52px; }
  .pm-pro-cat-fila .form-check-input { width:1.4em; height:1.4em; margin:0; flex:0 0 auto; }
  .pm-pro-cat-fila label { flex:1; font-weight:500; padding:.5rem 0; cursor:pointer; }
  .pm-pro-cat-cuenta { font-size:.8rem; color: var(--pm-text-muted, #5C4F42); white-space:nowrap; }
  .pm-pro-cat-abrir { min-width:44px; min-height:44px; border:0; background:none; color: var(--pm-tierra, #7A654F); }
  .pm-pro-cat-abrir[aria-expanded="true"] i { transform:rotate(180deg); }
  .pm-pro-servicios { border-top:1px solid var(--pm-border-light, #E6DFC8); padding:.25rem .75rem .5rem; }
  .pm-pro-servicios .form-check { min-height:44px; display:flex; align-items:center; gap:.5rem; padding-left:0; margin:0; }
  .pm-pro-servicios .form-check-input { width:1.2em; height:1.2em; margin:0; flex:0 0 auto; }
  .pm-pro-servicios label { flex:1; padding:.4rem 0; }
  .pm-pro-servicios small { color: var(--pm-text-muted, #5C4F42); }
  .pm-pro-jornada { font-size:.75rem; padding:.1rem .45rem; border-radius:999px; background:#ECE7D1; color:#5C4F42; margin-left:.35rem; }
  .pm-pro-local { min-height:44px; display:flex; align-items:center; gap:.6rem; padding-left:0; }
  .pm-pro-local .form-check-input { margin:0; width:2.6em; height:1.4em; }
  .pm-pro-dia { border-bottom:1px solid var(--pm-border-light, #E6DFC8); padding:.5rem 0; }
  .pm-pro-dia-cab { display:flex; align-items:center; gap:.6rem; min-height:44px; }
  .pm-pro-dia-cab .form-check-input { width:1.4em; height:1.4em; margin:0; }
  .pm-pro-dia-cab label { flex:1; font-weight:500; }
  .pm-pro-franja { display:flex; align-items:center; gap:.4rem; margin:.35rem 0 0 2rem; }
  .pm-pro-franja input { max-width:7.5rem; min-height:44px; }
  .pm-pro-franja .btn { min-width:44px; min-height:44px; }
  .pm-pro-agregar { min-height:44px; margin-left:1.6rem; }
  #modalPro .form-check-input:checked, #modalPro .form-check-input:indeterminate { background-color: var(--pm-bronce, #8A7650); border-color: var(--pm-bronce, #8A7650); }
  #modalPro .form-check-input:focus { border-color: var(--pm-bronce, #8A7650); box-shadow: 0 0 0 .2rem rgba(138,118,80,.25); }
  .pm-pro-agregar, .pm-pro-agregar:hover { color: var(--pm-tierra, #7A654F); text-decoration:none; font-weight:500; }
  .pm-pro-pie { position:sticky; bottom:0; background:#fff; }
  .pm-pro-pie .btn { min-height:44px; }
  @media (min-width: 576px) { .pm-pro-grid { grid-template-columns: repeat(2, 1fr); } }
  @media (min-width: 1200px) { .pm-pro-grid { grid-template-columns: repeat(3, 1fr); } }
</style>';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<p class="pm-pro-intro">Qué servicios hace cada profesional y en qué días y horarios atiende. La agenda usa estos datos para sugerir horarios al cargar un turno.</p>
<div id="proAlertas"></div>
<div class="pm-pro-grid" id="proGrid"><p class="text-muted">Cargando…</p></div>

<div class="modal fade" id="modalPro" tabindex="-1" aria-labelledby="modalProTitulo">
  <div class="modal-dialog modal-lg modal-fullscreen-sm-down modal-dialog-scrollable">
    <form class="modal-content" id="formPro" novalidate>
      <div class="modal-header">
        <h5 class="modal-title" id="modalProTitulo">Profesional</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <section class="mb-4" aria-labelledby="proSecServ">
          <h6 class="pm-pro-seccion" id="proSecServ">Servicios que hace</h6>
          <p class="pm-pro-ayuda">Tildá la categoría completa o abrila para elegir servicio por servicio.</p>
          <div id="proCategorias"></div>
        </section>
        <section aria-labelledby="proSecHor">
          <h6 class="pm-pro-seccion" id="proSecHor">Días y horarios</h6>
          <div class="form-check form-switch pm-pro-local">
            <input class="form-check-input" type="checkbox" role="switch" id="proUsaLocal">
            <label class="form-check-label" for="proUsaLocal">Atiende en el horario del local <small class="text-muted d-block" id="proLocalTexto"></small></label>
          </div>
          <div id="proDias" class="mt-2"></div>
        </section>
      </div>
      <div class="modal-footer pm-pro-pie">
        <button type="button" class="btn btn-pm-outline" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-pm" id="proGuardar">Guardar</button>
      </div>
    </form>
  </div>
</div>

<script>
const Pro = (() => {
    const API = '<?= URL_API ?>/admin/profesionales.php';
    const DIAS = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    const CORTO = ['', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    let modal, actual = null, local = null;

    // "Lun a Vie 09:00–18:00 · Sáb 09:00–13:00"
    function resumenHorario(horarios) {
        const porDia = {};
        horarios.forEach(h => { (porDia[h.dia_semana] ??= []).push(`${h.hora_inicio}–${h.hora_fin}`); });
        const grupos = [];
        for (let d = 1; d <= 7; d++) {
            if (!porDia[d]) continue;
            const franjas = porDia[d].join(' y ');
            const ult = grupos[grupos.length - 1];
            if (ult && ult.franjas === franjas && ult.hasta === d - 1) ult.hasta = d;
            else grupos.push({ desde: d, hasta: d, franjas });
        }
        return grupos.map(g => (g.desde === g.hasta ? CORTO[g.desde]
            : `${CORTO[g.desde]} ${g.hasta - g.desde === 1 ? 'y' : 'a'} ${CORTO[g.hasta]}`) + ' ' + g.franjas).join(' · ');
    }

    async function cargar() {
        const res = await apiCall(API);
        if (!res.success) return PM.error('Error', res.error);
        local = res.data.local;
        const sin = res.data.sin_profesional;
        document.getElementById('proAlertas').innerHTML = sin.length ? `
            <div class="pm-pro-alerta" role="status">
              <strong><i class="bi bi-exclamation-triangle me-1"></i>Sin profesional activa: ${sin.map(c => esc(c.nombre)).join(', ')}</strong>
              Para esos servicios la agenda ofrece cualquier horario libre del local. Asignalos a alguien para que respete sus días y horarios.
            </div>` : '';

        const pros = res.data.profesionales;
        document.getElementById('proGrid').innerHTML = pros.length ? pros.map(p => `
            <article class="pm-pro-card" data-activo="${p.activo}">
              <h3 class="pm-pro-nombre">${esc(p.nombre)} ${esc(p.apellidos)}
                ${p.activo ? '' : '<span class="badge-estado badge-cancelada">Inactiva</span>'}</h3>
              <div class="pm-pro-chips">${p.categorias.length
                ? p.categorias.map(c => `<span class="pm-pro-chip">${esc(c)}</span>`).join('')
                : '<span class="pm-pro-chip vacio">Sin servicios asignados</span>'}</div>
              <p class="pm-pro-horario"><i class="bi bi-clock me-1"></i>${p.horarios.length ? esc(resumenHorario(p.horarios)) : 'Horario del local'}</p>
              <button type="button" class="btn btn-pm-outline" onclick="Pro.abrir(${p.id})">Editar servicios y horario</button>
            </article>`).join('')
            : '<p class="text-muted">No hay profesionales cargadas. Agregalas desde <a href="empleados.php">Empleados</a>.</p>';
    }

    function textoLocal() {
        const dias = local.dias.map(d => ({ dia_semana: d, hora_inicio: local.apertura, hora_fin: local.cierre }));
        return resumenHorario(dias) || `${local.apertura}–${local.cierre}`;
    }

    function pintarCategorias(categorias) {
        document.getElementById('proCategorias').innerHTML = categorias.map(c => `
            <div class="pm-pro-cat" data-cat="${c.id}">
              <div class="pm-pro-cat-fila">
                <input class="form-check-input" type="checkbox" id="cat${c.id}" data-rol="cat">
                <label for="cat${c.id}">${esc(c.nombre)}${c.requiere_jornada ? '<span class="pm-pro-jornada">con jornada</span>' : ''}</label>
                <span class="pm-pro-cat-cuenta" data-rol="cuenta"></span>
                <button type="button" class="pm-pro-cat-abrir" aria-expanded="false" aria-controls="servs${c.id}" aria-label="Ver servicios de ${esc(c.nombre)}"><i class="bi bi-chevron-down"></i></button>
              </div>
              <div class="pm-pro-servicios" id="servs${c.id}" hidden>
                ${c.servicios.map(s => `
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="srv${s.id}" value="${s.id}" data-rol="srv" ${s.asignado ? 'checked' : ''}>
                    <label class="form-check-label" for="srv${s.id}">${esc(s.nombre)} <small>${s.duracion_minutos} min</small></label>
                  </div>`).join('')}
              </div>
            </div>`).join('');
        document.querySelectorAll('.pm-pro-cat').forEach(sincronizarCategoria);
    }

    function sincronizarCategoria(caja) {
        const srv = [...caja.querySelectorAll('[data-rol="srv"]')];
        const n = srv.filter(i => i.checked).length;
        const cat = caja.querySelector('[data-rol="cat"]');
        cat.checked = n === srv.length;
        cat.indeterminate = n > 0 && n < srv.length;
        caja.querySelector('[data-rol="cuenta"]').textContent = `${n} de ${srv.length}`;
    }

    function franjaHtml(ini, fin) {
        return `<div class="pm-pro-franja">
            <input type="time" class="form-control" value="${ini}" aria-label="Entrada" data-rol="ini" step="900">
            <span aria-hidden="true">a</span>
            <input type="time" class="form-control" value="${fin}" aria-label="Salida" data-rol="fin" step="900">
            <button type="button" class="btn btn-link text-muted" data-rol="quitar" aria-label="Quitar franja"><i class="bi bi-x-lg"></i></button>
          </div>`;
    }

    function pintarDias(horarios) {
        const porDia = {};
        horarios.forEach(h => (porDia[h.dia_semana] ??= []).push(h));
        let html = '';
        for (let d = 1; d <= 7; d++) {
            const fr = porDia[d] || [];
            html += `<div class="pm-pro-dia" data-dia="${d}">
                <div class="pm-pro-dia-cab">
                  <input class="form-check-input" type="checkbox" id="dia${d}" data-rol="dia" ${fr.length ? 'checked' : ''}>
                  <label for="dia${d}">${DIAS[d]}</label>
                </div>
                <div data-rol="franjas" ${fr.length ? '' : 'hidden'}>
                  ${fr.map(h => franjaHtml(h.hora_inicio, h.hora_fin)).join('')}
                  <button type="button" class="btn btn-link btn-sm pm-pro-agregar" data-rol="agregar"><i class="bi bi-plus"></i> Agregar franja</button>
                </div>
              </div>`;
        }
        document.getElementById('proDias').innerHTML = html;
    }

    // Al marcar un día se copia la franja del último día marcado; si no hay, la del local
    function franjaSugerida(dia) {
        const previos = [...document.querySelectorAll('.pm-pro-dia')]
            .filter(el => +el.dataset.dia !== dia && el.querySelector('[data-rol="dia"]').checked && el.querySelector('[data-rol="ini"]'));
        const ref = previos.filter(el => +el.dataset.dia < dia).pop() || previos[0];
        if (ref) return [ref.querySelector('[data-rol="ini"]').value, ref.querySelector('[data-rol="fin"]').value];
        return [local.apertura, local.cierre];
    }

    function alternarLocal() {
        document.getElementById('proDias').hidden = document.getElementById('proUsaLocal').checked;
    }

    async function abrir(id) {
        const res = await apiCall(`${API}?id=${id}`);
        if (!res.success) return PM.error('Error', res.error);
        actual = res.data;
        local = res.data.local;
        document.getElementById('modalProTitulo').textContent = `${actual.profesional.nombre} ${actual.profesional.apellidos}`;
        pintarCategorias(actual.categorias);
        document.getElementById('proLocalTexto').textContent = textoLocal();
        document.getElementById('proUsaLocal').checked = actual.usa_horario_local;
        pintarDias(actual.horarios);
        alternarLocal();
        modal.show();
    }

    function leerHorarios() {
        const out = [];
        document.querySelectorAll('.pm-pro-dia').forEach(el => {
            if (!el.querySelector('[data-rol="dia"]').checked) return;
            el.querySelectorAll('.pm-pro-franja').forEach(f => out.push({
                dia_semana: +el.dataset.dia,
                hora_inicio: f.querySelector('[data-rol="ini"]').value,
                hora_fin: f.querySelector('[data-rol="fin"]').value,
            }));
        });
        return out;
    }

    async function guardar(e) {
        e.preventDefault();
        const usaLocal = document.getElementById('proUsaLocal').checked;
        const horarios = usaLocal ? [] : leerHorarios();
        if (!usaLocal && !horarios.length) {
            return PM.error('Falta el horario', 'Marcá al menos un día, o activá "Atiende en el horario del local".');
        }
        if (horarios.some(h => !h.hora_inicio || !h.hora_fin || h.hora_inicio >= h.hora_fin)) {
            return PM.error('Revisá los horarios', 'En cada franja, la salida tiene que ser después de la entrada.');
        }
        const btn = document.getElementById('proGuardar');
        btn.disabled = true;
        const res = await apiCall(API, 'PUT', {
            id: actual.profesional.id,
            servicios: [...document.querySelectorAll('[data-rol="srv"]:checked')].map(i => +i.value),
            usa_horario_local: usaLocal,
            horarios,
        });
        btn.disabled = false;
        if (!res.success) return PM.error('No se guardó', res.error);
        modal.hide();
        PM.toast('success', 'Guardado');
        cargar();
    }

    document.addEventListener('DOMContentLoaded', () => {
        modal = new bootstrap.Modal(document.getElementById('modalPro'));
        document.getElementById('formPro').addEventListener('submit', guardar);
        document.getElementById('proUsaLocal').addEventListener('change', alternarLocal);

        document.getElementById('proCategorias').addEventListener('click', (e) => {
            const b = e.target.closest('.pm-pro-cat-abrir');
            if (!b) return;
            const abierto = b.getAttribute('aria-expanded') === 'true';
            b.setAttribute('aria-expanded', String(!abierto));
            document.getElementById(b.getAttribute('aria-controls')).hidden = abierto;
        });
        document.getElementById('proCategorias').addEventListener('change', (e) => {
            const caja = e.target.closest('.pm-pro-cat');
            if (e.target.dataset.rol === 'cat') {
                caja.querySelectorAll('[data-rol="srv"]').forEach(i => { i.checked = e.target.checked; });
            }
            sincronizarCategoria(caja);
        });

        const dias = document.getElementById('proDias');
        dias.addEventListener('change', (e) => {
            if (e.target.dataset.rol !== 'dia') return;
            const el = e.target.closest('.pm-pro-dia');
            const franjas = el.querySelector('[data-rol="franjas"]');
            if (e.target.checked && !franjas.querySelector('.pm-pro-franja')) {
                franjas.insertAdjacentHTML('afterbegin', franjaHtml(...franjaSugerida(+el.dataset.dia)));
            }
            franjas.hidden = !e.target.checked;
        });
        dias.addEventListener('click', (e) => {
            const b = e.target.closest('button');
            if (!b) return;
            const el = b.closest('.pm-pro-dia');
            if (b.dataset.rol === 'agregar') {
                const ult = [...el.querySelectorAll('[data-rol="fin"]')].pop();
                const ini = ult ? ult.value : local.apertura;
                b.insertAdjacentHTML('beforebegin', franjaHtml(ini, local.cierre > ini ? local.cierre : ini));
            } else if (b.dataset.rol === 'quitar') {
                b.closest('.pm-pro-franja').remove();
                if (!el.querySelector('.pm-pro-franja')) {
                    el.querySelector('[data-rol="dia"]').checked = false;
                    el.querySelector('[data-rol="franjas"]').hidden = true;
                }
            }
        });

        cargar();
    });

    return { abrir };
})();
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
