const assert = require('node:assert/strict');
const { chromium } = require('playwright');
const path = require('node:path');
const os = require('node:os');

(async () => {
  const browser = await chromium.launch({ channel: 'chrome', headless: true });
  const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
  const errors = [];
  page.on('pageerror', error => errors.push(error.message));

  try {
    await page.goto(process.env.CUSTOMER_PORTAL_URL || 'http://127.0.0.1:8023/customer-portal');
    await page.getByRole('heading', { name: 'Welcome to FleetNG' }).waitFor();
    assert.equal(await page.locator('.login-photo').evaluate(image => image.complete && image.naturalWidth > 0), true);
    assert.equal(await page.locator('.login-visual .brand img').evaluate(image => image.complete && image.naturalWidth > 0), true);
    await page.screenshot({ path: path.join(os.tmpdir(), 'fleetng-login-desktop.png'), fullPage: true });

    await page.getByRole('tab', { name: 'New customer' }).click();
    await page.getByRole('heading', { name: 'Create your account' }).waitFor();
    assert.equal(await page.getByLabel('Email address').count(), 1);
    await page.getByRole('tab', { name: 'Existing customer' }).click();

    for (const width of [1024, 768, 390, 320]) {
      await page.setViewportSize({ width, height: 844 });
      assert.equal(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth), true, `Horizontal overflow at ${width}px`);
      assert.equal(await page.getByRole('button', { name: 'Continue' }).isVisible(), true);
      if (width === 390) await page.screenshot({ path: path.join(os.tmpdir(), 'fleetng-login-mobile.png'), fullPage: true });
    }

    await page.getByLabel('Phone number').fill('8000000099');
    await page.getByRole('button', { name: 'Continue' }).click();
    await page.getByRole('heading', { name: 'Verify your number' }).waitFor();
    assert.equal(await page.locator('#otp-form .form-error').innerText(), '');
    await page.getByRole('button', { name: 'Change details' }).click();
    await page.getByRole('heading', { name: 'Welcome to FleetNG' }).waitFor();
    assert.deepEqual(errors, []);
    console.log('PASS: login, registration, OTP transition, images, and responsive widths.');
  } finally {
    await browser.close();
  }
})().catch(error => { console.error(error); process.exitCode = 1; });
