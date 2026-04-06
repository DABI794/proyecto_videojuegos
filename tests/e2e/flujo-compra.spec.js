import { test, expect } from '@playwright/test';

const USUARIO = {
  email:    'admin@videojuegos.bo',
  password: 'Admin@12345!',
};

const PAYPAL_SANDBOX = {
  email:    'sb-zqcf150073056@personal.example.com',
  password: 'O+I2hXmL',
};

async function login(page) {
  await page.goto('/login');
  await page.fill('input[name="email"]',    USUARIO.email);
  await page.fill('input[name="password"]', USUARIO.password);
  await page.click('button[type="submit"]');
  await page.waitForURL(url => !url.href.includes('/login'));
}

test.describe('GameStore Bolivia — Flujo completo de compra', () => {

  // ─── TEST 1: Login ──────────────────────────────────────────────────────

  test('usuario puede iniciar sesión correctamente', async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]',    USUARIO.email);
    await page.fill('input[name="password"]', USUARIO.password);

    await page.screenshot({ path: 'tests/e2e/evidencias/01-antes-login.png', fullPage: true });

    await page.click('button[type="submit"]');
    await page.waitForURL(url => !url.href.includes('/login'));

    await page.screenshot({ path: 'tests/e2e/evidencias/02-login-exitoso.png', fullPage: true });

    expect(page.url()).not.toContain('/login');
  });

  // ─── TEST 2: Catálogo ───────────────────────────────────────────────────

  test('productos aparecen en el catálogo', async ({ page }) => {
    await login(page);
    await page.goto('/productos');

    await page.waitForSelector('.group.bg-\\[\\#1e293b\\].rounded-2xl');

    const cards = page.locator('.group.bg-\\[\\#1e293b\\].rounded-2xl');
    const cantidad = await cards.count();

    console.log(`Productos encontrados: ${cantidad}`);
    expect(cantidad).toBeGreaterThan(0);

    await page.screenshot({ path: 'tests/e2e/evidencias/03-catalogo-productos.png', fullPage: true });
  });

  // ─── TEST 3: Agregar al carrito desde la card ───────────────────────────

  test('producto se agrega al carrito desde la card', async ({ page }) => {
    await login(page);
    await page.goto('/productos');

    await page.waitForSelector('.group.bg-\\[\\#1e293b\\].rounded-2xl');

    const primeraCard = page.locator('.group.bg-\\[\\#1e293b\\].rounded-2xl').first();
    await primeraCard.hover();

    await page.screenshot({ path: 'tests/e2e/evidencias/04-hover-card.png', fullPage: true });

    const btnAgregar = primeraCard.locator('button:has-text("Agregar al carrito")');
    await btnAgregar.waitFor({ state: 'visible' });
    await btnAgregar.click();

    await expect(primeraCard.locator('button:has-text("¡Agregado!")')).toBeVisible({ timeout: 5000 });

    await page.screenshot({ path: 'tests/e2e/evidencias/05-producto-agregado.png', fullPage: true });
  });

  // ─── TEST 4: Detalle del producto ──────────────────────────────────────

  test('página de detalle muestra precio y botón de compra', async ({ page }) => {
    await login(page);
    await page.goto('/productos');

    await page.waitForSelector('.group.bg-\\[\\#1e293b\\].rounded-2xl');

    await page.locator('.group.bg-\\[\\#1e293b\\].rounded-2xl a').first().click();
    await page.waitForURL(/\/productos\/.+/);

    await expect(page.locator('span.text-\\[\\#6366f1\\].font-extrabold')).toBeVisible();
    await expect(page.locator('button.flex-1:has-text("Agregar al carrito")')
  ).toBeVisible();

    await page.screenshot({ path: 'tests/e2e/evidencias/06-detalle-producto.png', fullPage: true });
  });

  // ─── TEST 5: Flujo completo con PayPal Sandbox ─────────────────────────

  test('compra finaliza y se procesa el pago con PayPal Sandbox', async ({ page, context }) => {

    // 1. Login
    await login(page);

    // 2. Agregar producto al carrito desde el detalle
    await page.goto('/productos');
    await page.waitForSelector('.group.bg-\\[\\#1e293b\\].rounded-2xl');
    await page.locator('.group.bg-\\[\\#1e293b\\].rounded-2xl a').first().click();
    await page.waitForURL(/\/productos\/.+/);
    await page.click('button:has-text("Agregar al carrito")');
    await expect(page.locator('button:has-text("¡Agregado!")')).toBeVisible({ timeout: 5000 });

    await page.screenshot({ path: 'tests/e2e/evidencias/07-agregado-desde-detalle.png', fullPage: true });

    // 3. Ir al carrito y proceder al pago
    await page.goto('/carrito');
    await page.click('button[type="submit"]:has-text("Proceder al pago")');
    await expect(page.locator('h1', { hasText: /Pedido #\d+/ })).toBeVisible({ timeout: 15000 });

    await page.screenshot({ path: 'tests/e2e/evidencias/08-orden-creada-pending.png', fullPage: true });

    // 4. Clic en el botón de PayPal dentro del iframe
    const paypalFrame = page.frameLocator('iframe[title*="PayPal"]').first();
    const btnPaypal = paypalFrame.getByRole('link', { name: 'PayPal' });
    await expect(btnPaypal).toBeVisible({ timeout: 15000 });

    await page.screenshot({ path: 'tests/e2e/evidencias/09-paypal-listo.png', fullPage: true });

    // 5. Capturar popup ANTES del clic
    const popupPromise = context.waitForEvent('page', { timeout: 15000 });
    await btnPaypal.click();
    const popup = await popupPromise;

    await popup.waitForLoadState('domcontentloaded', { timeout: 20000 });
    await popup.screenshot({ path: 'tests/e2e/evidencias/09b-popup-abierto.png', fullPage: true });

    // 6. Llenar email en PayPal Sandbox
    await popup.waitForSelector('input#email', { state: 'visible', timeout: 20000 });
    await popup.locator('input#email').fill(PAYPAL_SANDBOX.email);
    await popup.locator('button#btnNext').click();

    await popup.screenshot({ path: 'tests/e2e/evidencias/09c-email-ingresado.png', fullPage: true });

    // 7. Llenar contraseña y hacer login
    await popup.waitForSelector('input#password', { state: 'visible', timeout: 25000 });
    await popup.locator('input#password').fill(PAYPAL_SANDBOX.password);
    await popup.locator('button#btnLogin').click();

    await popup.screenshot({ path: 'tests/e2e/evidencias/09d-login-paypal.png', fullPage: true });

    // 8. Esperar pantalla de revisión de PayPal
    await popup.waitForLoadState('domcontentloaded', { timeout: 20000 });

    await popup.screenshot({ path: 'tests/e2e/evidencias/09e-revision.png', fullPage: true });

    // 9. Buscar el botón de confirmar — PayPal sandbox usa distintos textos/IDs
    const btnConfirmar = popup.locator([
      'button#payment-submit-btn',
      'button[data-testid="submit-button-initial"]',
      'button:has-text("Continuar")',
      'button:has-text("Continue")',
      'button:has-text("Pay Now")',
      'button:has-text("Pagar ahora")',
    ].join(', ')).first();

    await expect(btnConfirmar).toBeVisible({ timeout: 30000 });

    await popup.screenshot({ path: 'tests/e2e/evidencias/09f-boton-confirmar.png', fullPage: true });

    await btnConfirmar.click();

    // 10. Esperar que el popup se cierre tras confirmar
    await popup.waitForEvent('close', { timeout: 30000 });

    // 11. Verificar modal de éxito en Laravel
    await expect(
      page.locator('text=¡Pago Exitoso! 🎉')
    ).toBeVisible({ timeout: 20000 });

    await page.screenshot({ path: 'tests/e2e/evidencias/10-pago-completado.png', fullPage: true });

  }, { timeout: 120000 });

});