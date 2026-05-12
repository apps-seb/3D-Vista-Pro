const { test, expect } = require('@playwright/test');

test('screenshot of admin html tools map', async ({ page }) => {
  await page.goto('http://localhost:8080/admin.html');
  await page.waitForTimeout(1000); // wait for preloader
  await page.screenshot({ path: 'admin_tools.png', fullPage: true });
});

test('screenshot of index html tools map', async ({ page }) => {
  await page.goto('http://localhost:8080/index.html');
  await page.waitForTimeout(1000); // wait for preloader
  await page.screenshot({ path: 'index_tools.png', fullPage: true });
});
