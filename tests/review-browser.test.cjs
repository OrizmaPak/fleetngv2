const {chromium} = require('playwright');
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const base = process.env.FLEETNG_REVIEW_URL || 'http://127.0.0.1:8022';
const fixture = JSON.parse(fs.readFileSync(path.join(__dirname,'../storage/app/review/manifest.json'),'utf8'));
const output = path.join(__dirname,'../storage/app/review/screenshots');
fs.mkdirSync(output,{recursive:true});

(async () => {
  const browser = await chromium.launch({channel:'chrome',headless:true});
  const context = await browser.newContext({viewport:{width:1440,height:1000}});
  const page = await context.newPage();
  const errors = [];
  page.on('pageerror',error=>errors.push({url:page.url(),message:error.message}));
  await context.route('**/*',route=>{
    const url = new URL(route.request().url());
    if (url.protocol === 'http:' && url.hostname === '127.0.0.1') return route.continue();
    return route.fulfill({status:204,body:''});
  });
  const screenshot = name=>page.screenshot({path:path.join(output,name+'.png'),fullPage:true});
  try {
    await page.goto(base+'/superadmin');
    await page.locator('input[name="email"]').fill(fixture.accounts.superadmin.email);
    await page.locator('input[name="password"]').fill('FleetNG-Review-2026!');
    await page.locator('button[type="submit"]').click();
    await page.waitForURL('**/superadmin/analytics');
    const company = fixture.accounts.company.id, merchant = fixture.accounts.merchant.id, driver=fixture.driver_id, client=fixture.customer_id, pickup=fixture.pickup_id;
    const paths=['/analytics','/user-list','/user/add','/user/edit/'+company,'/user/view/'+company,'/user-trip-list/'+company,'/driver-list','/driver/add','/driver/edit/'+driver,'/driver/view/'+driver,'/driver-trip-list/'+driver,'/client-list','/client-edit/'+client,'/trip-list','/trip/add','/trip/detail/1','/location-list','/pickup-location/add','/pickup-location/edit/'+pickup,'/pickup-location/view/'+pickup,'/expenses','/payment-list','/live-tracking','/map-history','/reports','/account-settings','/superadmin/analytics','/superadmin/user-list','/superadmin/user/add','/superadmin/user/edit/'+company,'/superadmin/user/view/'+company,'/superadmin/merchant-list','/superadmin/merchant/add','/superadmin/merchant/edit/'+merchant,'/superadmin/merchant/view/'+merchant,'/superadmin/pages/refund-policy','/superadmin/pages/privacy-policy','/superadmin/pages/terms-conditions','/superadmin/account-settings'];
    const screens=[];
    for (const [index,url] of paths.entries()) {
      const response=await page.goto(base+url);
      assert.equal(response.status(),200,url);
      await page.waitForLoadState('networkidle');
      await screenshot('staff-'+String(index+1).padStart(2,'0'));
      screens.push({url,width:1440,title:await page.title(),overflow:await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth)});
    }
    await page.setViewportSize({width:390,height:844});
    for (const [index,url] of ['/driver-list','/driver/add','/trip-list','/client-list','/reports','/superadmin/user/add'].entries()) {
      await page.goto(base+url); await page.waitForLoadState('networkidle');
      await screenshot('staff-mobile-'+index);
      screens.push({url,width:390,overflow:await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth)});
    }
    await page.setViewportSize({width:1440,height:1000});
    await page.goto(base+'/customer-portal#/payment-return?status=successful&reference=forged');
    await page.getByLabel('Phone number',{exact:true}).fill('8000000000');
    await page.getByRole('button',{name:'Continue',exact:true}).click();
    await page.getByLabel('Verification code',{exact:true}).fill('123456');
    await page.getByRole('button',{name:'Verify and continue',exact:true}).click();
    await page.getByRole('heading',{name:'Payment not verified',exact:true}).waitFor();
    await page.goto(base+'/customer-portal#/overview');
    await page.getByRole('heading',{name:/Good day,/}).waitFor();
    await screenshot('customer-overview');
    await page.getByRole('link',{name:'My account',exact:true}).click();
    await page.getByLabel('Profile photo',{exact:true}).setInputFiles({name:'review.png',mimeType:'image/png',buffer:Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l9sAAAAASUVORK5CYII=','base64')});
    await page.getByRole('button',{name:'Save changes',exact:true}).click();
    await page.getByText('Profile updated.',{exact:true}).waitFor();
    await page.reload();
    await page.locator('.profile-photo').waitFor();
    await page.waitForFunction(()=>{
      const image=document.querySelector('.profile-photo');
      return image && image.complete && image.naturalWidth>0;
    });
    await page.goto(base+'/customer-portal#/book');
    await page.getByLabel('Pickup location',{exact:true}).selectOption(String(pickup));
    await page.getByLabel('Drop-off location',{exact:true}).fill('Browser review delivery');
    await page.getByLabel('Proposed trip cost (NGN)',{exact:true}).fill('120000');
    await page.getByRole('button',{name:'Continue',exact:true}).click();
    await page.getByLabel('Merchant',{exact:true}).selectOption(String(merchant));
    await page.locator('input[name="driver"][value="'+driver+'"]').check();
    await page.getByRole('button',{name:'Continue',exact:true}).click();
    await page.getByRole('button',{name:'Confirm booking',exact:true}).click();
    await page.getByRole('heading',{name:/^Trip #/}).waitFor();
    await screenshot('customer-confirmed-trip');
    await page.getByRole('button',{name:'Pay for trip',exact:true}).click();
    await page.getByRole('button',{name:'Continue to checkout',exact:true}).click();
    await page.getByText('Payment checkout is not configured. No payment has been taken.',{exact:true}).waitFor();
    await page.getByRole('button',{name:'Cancel',exact:true}).click();
    await page.goto(base+'/customer-portal#/payment-return?status=successful&reference=forged');
    await page.getByRole('heading',{name:'Payment not verified',exact:true}).waitFor();
    await screenshot('customer-payment-unverified');
    await page.goto(base+'/superadmin/analytics');
    assert.equal(new URL(page.url()).pathname,'/superadmin/analytics','Customer login must not replace staff identity');
    for (const [role,entry,destination,paths] of [
      ['admin','/admin','/analytics',['/driver-list','/trip-list','/user-list']],
      ['company','/user','/analytics',['/driver-list','/trip-list','/expenses']],
      ['payment','/user','/trip-list',['/trip-list','/payment-list','/account-settings']],
    ]) {
      await context.clearCookies();
      await page.goto(base+entry);
      await page.locator('input[name="email"]').fill(fixture.accounts[role].email);
      await page.locator('input[name="password"]').fill('FleetNG-Review-2026!');
      await page.locator('button[type="submit"]').click();
      await page.waitForURL(base+destination);
      for (const path of paths) {
        assert.equal((await page.goto(base+path)).status(),200,role+' '+path);
        await page.waitForLoadState('networkidle');
      }
      assert.equal((await page.goto(base+'/superadmin/merchant-list')).status(),403,role+' cannot manage merchants');
    }
    fs.writeFileSync(path.join(output,'results.json'),JSON.stringify({screens,errors},null,2));
    assert.deepEqual(errors,[],'Uncaught browser errors');
    assert.deepEqual(screens.filter(screen=>screen.overflow),[],'Page overflow');
    console.log('PASS: 39 staff pages, six mobile screens, all four staff login roles, real customer session, photo upload, booking confirmation, disabled checkout, forged payment return, and separate staff session.');
  } catch(error) {
    fs.writeFileSync(path.join(output,'errors.json'),JSON.stringify(errors,null,2));
    console.error('Page:',(await page.locator('body').innerText()).slice(0,1800));
    await screenshot('failure'); throw error;
  } finally { await browser.close(); }
})().catch(error=>{console.error(error);process.exitCode=1;});
