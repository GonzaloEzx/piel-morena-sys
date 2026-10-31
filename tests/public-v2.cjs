/** node.exe tests/public-v2.cjs http://localhost:8000 [directorio de capturas] */
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { chromium, webkit } = require('../../../stack/ss-taller/node_modules/playwright-core');
const { default: AxeBuilder } = require('../../../stack/ss-taller/node_modules/@axe-core/playwright');
const base = (process.argv[2] || 'http://localhost:8000').replace(/\/$/, '');
const output = process.argv[3] || path.join(__dirname, '../.playwright/public-v2');
fs.mkdirSync(output, { recursive: true });
let checks = 0;
function check(value, message) { assert.ok(value, message); checks++; }

async function inspect(page, label) {
  await page.locator('img').evaluateAll(async images => {
    await Promise.all(images.map(async image => { image.loading = 'eager'; await image.decode().catch(() => {}); }));
  });
  check(await page.locator('h1').count() === 1, `${label}: un h1`);
  check((await page.locator('meta[name="robots"]').getAttribute('content')).includes('noindex'), `${label}: noindex`);
  check(await page.locator('.pmv-preview').isVisible(), `${label}: aviso de prueba`);
  check(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth), `${label}: sin desborde`);
  const broken = await page.locator('img').evaluateAll(images => images.filter(i => !i.complete || !i.naturalWidth).map(i => i.src));
  check(broken.length === 0, `${label}: imágenes cargadas ${broken.join(', ')}`);
  const links = await page.locator('a[href*="wa.me/"]').evaluateAll(nodes => nodes.map(a => a.href));
  check(links.length > 0 && links.every(href => /^https:\/\/wa\.me\/\d+\?text=/.test(href)), `${label}: mensajes de WhatsApp`);
  check(links.every(href => new URL(href).searchParams.get('text').startsWith('Hola, Piel Morena.')), `${label}: mensaje legible`);
  const violations = (await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21aa']).analyze()).violations;
  check(violations.length === 0, `${label}: axe ${JSON.stringify(violations.map(v => ({ id: v.id, nodes: v.nodes.map(n => n.target) })))}`);
  await page.screenshot({ path: path.join(output, `${label}.jpg`), fullPage: true, type: 'jpeg', quality: 80 });
}

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  const errors = [];
  const unexpected = [];
  const context = await browser.newContext();
  const page = await context.newPage();
  await context.route('https://wa.me/**', route => route.fulfill({ status: 200, body: 'WhatsApp interceptado por la prueba; no se envía ningún mensaje.' }));
  page.on('pageerror', e => errors.push(e.message));
  page.on('request', request => { if (/\/api\/(citas|jornadas|maquinas)\//.test(request.url())) unexpected.push(request.url()); });
  for (const [width, height] of [[1440, 900], [768, 1024], [430, 932], [390, 844], [360, 740], [320, 568]]) {
    await page.setViewportSize({ width, height });
    const response = await page.goto(`${base}/`, { waitUntil: 'networkidle' });
    check(response.status() === 200, `inicio ${width}: HTTP 200`);
    await page.locator('footer.pmv-footer').scrollIntoViewIfNeeded();
    await page.evaluate(() => scrollTo({ top: 0, behavior: 'instant' }));
    await page.waitForTimeout(250);
    await inspect(page, `inicio-${width}`);
    const invalidAnchors = await page.locator('a[href*="#"]').evaluateAll(anchors => anchors.filter(a => a.origin === location.origin && a.pathname === location.pathname && a.hash && !document.getElementById(decodeURIComponent(a.hash.slice(1)))).map(a => a.hash));
    check(invalidAnchors.length === 0, `inicio ${width}: anclas ${invalidAnchors}`);
    if (width < 992) {
      const smallTargets = [];
      for (const control of await page.locator('a, button, summary, input, select').all()) {
        if (!await control.isVisible()) continue;
        const box = await control.boundingBox();
        if (box && (box.width < 47 || box.height < 47)) smallTargets.push({ text: (await control.innerText()).trim().slice(0, 60), width: box.width, height: box.height });
      }
      check(smallTargets.length === 0, `inicio ${width}: controles táctiles ${JSON.stringify(smallTargets)}`);
      check(!await page.locator('.pmv-mobile-bar').isVisible(), 'CTA fija oculta cuando el botón principal es visible');
      await page.locator('.pmv-menu summary').click();
      check(await page.locator('.pmv-menu nav').isVisible(), 'menú móvil abierto');
      const menuBox = await page.locator('.pmv-menu nav').boundingBox();
      check(menuBox && menuBox.width >= width - 48, 'menú móvil con ancho cómodo');
      await page.keyboard.press('Escape');
      check(!await page.locator('.pmv-menu nav').isVisible(), 'Escape cierra menú');
      check(await page.locator('.pmv-menu summary').evaluate(e => e === document.activeElement), 'Escape devuelve foco');
      await page.locator('#servicios').scrollIntoViewIfNeeded();
      await page.waitForTimeout(200);
      const bar = await page.locator('.pmv-mobile-bar').boundingBox();
      check(bar && bar.y + bar.height <= height + 1 && bar.height <= 80, 'CTA móvil compacta dentro de la pantalla');
      const header = await page.locator('.pmv-header').boundingBox();
      check(header && Math.abs(header.y) < 2 && header.height <= 72, 'cabecera permanece visible al desplazarse');
      await page.locator('.pmv-team-disclosure summary').click();
      check(await page.locator('.pmv-team-list').isVisible(), 'equipo disponible al desplegar');
    }
    await page.goto(`${base}/reservar.php?servicio=-1`, { waitUntil: 'networkidle' });
    await inspect(page, `catalogo-${width}`);
    check((await page.locator('#pmv-selected-title').innerText()).includes('Limpieza facial'), 'servicio preseleccionado');
    const selectedHref = await page.locator('.pmv-selected .pmv-button').getAttribute('href');
    check(new URL(selectedHref).searchParams.get('text').includes('Limpieza facial personalizada'), 'WhatsApp conserva el servicio');
  }
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto(`${base}/reservar.php`, { waitUntil: 'networkidle' });
  const firstCategory = await page.locator('#categoria-faciales summary').first().boundingBox();
  check(firstCategory && firstCategory.y + firstCategory.height < 844, 'categorías del catálogo accesibles en la primera pantalla');
  check(await page.locator('.pmv-catalog-group[open]').count() === 0, 'vista inicial permite recorrer todas las categorías');
  await page.locator('#categoria-faciales > summary').click();
  const detail = page.locator('#categoria-faciales .pmv-service-detail').first();
  check((await detail.locator('summary').innerText()).includes('$32.000'), 'precio visible antes de abrir el servicio');
  check((await detail.locator('summary').innerText()).includes('90 min'), 'duración visible antes de abrir el servicio');
  await detail.locator('summary').click();
  check(await detail.locator('a').isVisible(), 'detalles y consulta disponibles al abrir el servicio');
  const search = page.locator('#pmv-search');
  await search.fill('HIDRATACION');
  check(await page.locator('[data-service]:visible').count() === 1, 'búsqueda sin acentos y mayúsculas');
  await search.fill('no-existe-este-servicio');
  check(await page.locator('#pmv-no-results').isVisible(), 'búsqueda vacía con salida a WhatsApp');
  await search.fill('');
  check(!await page.locator('#pmv-no-results').isVisible(), 'limpiar restaura los resultados');
  await page.goto(`${base}/reservar.php?servicio=999999`, { waitUntil: 'networkidle' });
  check(await page.locator('.pmv-selected').count() === 0, 'id inexistente sin selección');
  await page.goto(`${base}/reservar.php?categoria=mirada`, { waitUntil: 'networkidle' });
  check(await page.locator('#categoria-mirada').getAttribute('open') !== null, 'categoría enlazada abierta');
  await page.goto(`${base}/`, { waitUntil: 'networkidle' });
  await page.locator('#pmv-service-select').selectOption('-2');
  const popupPromise = page.waitForEvent('popup');
  await page.locator('#pmPedirTurno button').click();
  const popup = await popupPromise;
  await popup.waitForLoadState();
  check(new URL(popup.url()).searchParams.get('text').includes('Hidratación facial'), 'formulario abre WhatsApp con el servicio');
  await popup.close();
  const noJS = await browser.newContext({ javaScriptEnabled: false, viewport: { width: 390, height: 844 } });
  const staticPage = await noJS.newPage();
  await staticPage.goto(`${base}/reservar.php`, { waitUntil: 'networkidle' });
  await staticPage.locator('#categoria-manos > summary').click();
  check(await staticPage.locator('#categoria-manos ul').isVisible(), 'catálogo funciona sin JS');
  await staticPage.locator('#categoria-manos .pmv-service-detail summary').first().click();
  check(await staticPage.locator('#categoria-manos a').first().isVisible(), 'detalles y consulta funcionan sin JS');
  check((await staticPage.locator('#categoria-manos a').first().getAttribute('href')).includes('wa.me/'), 'WhatsApp funciona sin JS');
  await staticPage.goto(`${base}/`, { waitUntil: 'networkidle' });
  await staticPage.locator('.pmv-menu summary').click();
  check(await staticPage.locator('.pmv-menu nav').isVisible(), 'menú funciona sin JS');
  await staticPage.locator('.pmv-menu summary').click();
  await staticPage.locator('#pmv-service-select').selectOption('-2');
  await staticPage.locator('#pmPedirTurno button').click();
  check((await staticPage.locator('#pmv-selected-title').innerText()).includes('Hidratación'), 'formulario GET conserva el servicio sin JS');
  await page.setViewportSize({ width: 740, height: 360 });
  await page.goto(`${base}/`, { waitUntil: 'networkidle' });
  await page.locator('.pmv-menu summary').click();
  const landscapeMenu = await page.locator('.pmv-menu nav').boundingBox();
  check(landscapeMenu && landscapeMenu.y + landscapeMenu.height <= 361, 'menú dentro de la pantalla horizontal');
  await page.locator('.pmv-menu nav a').last().scrollIntoViewIfNeeded();
  const lastMenuItem = await page.locator('.pmv-menu nav a').last().boundingBox();
  check(lastMenuItem && lastMenuItem.y + lastMenuItem.height <= 361, 'última opción de menú accesible en horizontal');
  check(!await page.locator('.pmv-mobile-bar').isVisible(), 'menú sin superposición de la barra fija');
  check(errors.length === 0, `sin errores JS: ${errors}`);
  check(unexpected.length === 0, `sin APIs de reserva retiradas: ${unexpected}`);
  await browser.close();
  const safari = await webkit.launch({ headless: true });
  const safariContext = await safari.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 1 });
  const safariPage = await safariContext.newPage();
  await safariPage.goto(`${base}/`, { waitUntil: 'networkidle' });
  await safariPage.locator('footer.pmv-footer').scrollIntoViewIfNeeded();
  await safariPage.evaluate(() => scrollTo({ top: 0, behavior: 'instant' }));
  await safariPage.waitForTimeout(200);
  await inspect(safariPage, 'inicio-webkit-mobile');
  await safari.close();
  console.log(`${checks} comprobaciones OK. Capturas: ${output}`);
})().catch(error => { console.error(error); process.exit(1); });
