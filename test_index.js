const { chromium } = require('playwright');
const path = require('path');
const express = require('express');

const app = express();
app.use(express.static(__dirname));
const server = app.listen(3000, async () => {
    console.log('Server running on port 3000');

    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    page.on('console', msg => console.log('BROWSER CONSOLE:', msg.text()));
    page.on('pageerror', error => {
        console.log('BROWSER ERROR:', error.message);
        console.log('BROWSER STACK:', error.stack);
    });
    page.on('dialog', async dialog => {
        console.log('DIALOG:', dialog.message());
        await dialog.dismiss();
    });

    await page.goto('http://localhost:3000/index.html', { waitUntil: 'networkidle' });

    // Wait for display-title to not be Cargando...
    await page.waitForFunction(() => {
        const title = document.getElementById('display-title');
        return title && title.textContent !== 'Cargando...';
    });

    const title = await page.locator('#display-title').textContent();
    console.log('DISPLAY TITLE:', title);

    await browser.close();
    server.close();
});
