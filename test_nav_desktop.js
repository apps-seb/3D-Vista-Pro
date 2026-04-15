const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 1280, height: 720 } });
  await page.goto('http://127.0.0.1:8080/landing.html');
  await page.waitForTimeout(2000);
  await page.screenshot({ path: '/tmp/nav_screenshot_desktop.png' });
  await browser.close();
})();
