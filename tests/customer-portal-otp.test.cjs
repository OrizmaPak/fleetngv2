const { chromium } = require('playwright');
const assert = require('node:assert/strict');
const path = require('node:path');
const os = require('node:os');

(async () => {
  const browser = await chromium.launch({channel:'chrome',headless:true});
  const page = await browser.newPage({viewport:{width:1440,height:1000}});
  const errors = [];
  page.on('pageerror', error => errors.push(error.message));
  try {
    await page.goto(process.env.CUSTOMER_PORTAL_URL || 'http://127.0.0.1:8021/customer-portal');
    await page.getByRole('heading',{name:'Welcome to FleetNG'}).waitFor();
    await page.getByText('Development verification: use OTP 123456. No SMS is sent.',{exact:true}).waitFor();
    assert.equal(await page.locator('[data-action="reset"]').count(),0);
    await page.getByLabel('Phone number',{exact:true}).fill('8000000099');
    await page.getByRole('button',{name:'Continue',exact:true}).click();
    await page.getByLabel('Verification code',{exact:true}).fill('000000');
    await page.getByRole('button',{name:'Verify and continue',exact:true}).click();
    await page.getByText('Incorrect code. The development code is 123456.',{exact:true}).waitFor();
    await page.screenshot({path:path.join(os.tmpdir(),'fleetng-local-otp-desktop.png'),fullPage:true});
    // Verify against the real backend without creating an account or logging into existing records.
    const proofResponse = page.waitForResponse(response => response.url().endsWith('/otp/verify') && response.status() === 200);
    await page.route('**/customer-portal/api/login', route => route.fulfill({status:400,contentType:'application/json',body:JSON.stringify({status:false,message:'Verification test complete. No account was opened.'})}));
    await page.getByLabel('Verification code',{exact:true}).fill('123456');
    await page.getByRole('button',{name:'Verify and continue',exact:true}).click();
    const verified = await (await proofResponse).json();
    assert.equal(verified.data.verification_token.length,64);
    await page.getByText('Verification test complete. No account was opened. Please check your details and request another code.',{exact:true}).waitFor();
    for (const width of [320,390,768]) {
      await page.setViewportSize({width,height:844});
      assert.equal(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth),true,'No overflow at ' + width);
      await page.screenshot({path:path.join(os.tmpdir(),'fleetng-local-login-' + width + '.png'),fullPage:true});
    }
    assert.deepEqual(errors,[]);
    console.log('PASS: real local OTP request, wrong-code rejection, 123456 verification, failure recovery, visible hint, and responsive login. No account records changed.');
  } catch (error) {
    console.error('Browser errors:',errors);
    console.error('Page:',(await page.locator('body').innerText()).slice(0,2000));
    throw error;
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
