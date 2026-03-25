const { chromium } = require('playwright');
const path = require('path');
const express = require('express');

const app = express();
app.use(express.static(__dirname));
const server = app.listen(3000, async () => {
    console.log('Server running on port 3000');

    const browser = await chromium.launch({ headless: true });

    // First, verify current title in index.html
    const indexPage = await browser.newPage();
    await indexPage.goto('http://localhost:3000/index.html', { waitUntil: 'networkidle' });

    await indexPage.waitForFunction(() => {
        const el = document.getElementById('display-title');
        return el && el.textContent !== 'Cargando...';
    });
    const initialTitle = await indexPage.locator('#display-title').textContent();
    console.log('INDEX INITIAL TITLE:', initialTitle);

    // Now open admin.html, change the title, and save
    const adminPage = await browser.newPage();
    adminPage.on('dialog', async dialog => {
        console.log('ADMIN DIALOG:', dialog.message());
        await dialog.accept();
    });

    await adminPage.goto('http://localhost:3000/admin.html', { waitUntil: 'networkidle' });
    await adminPage.waitForFunction(() => {
        const el = document.getElementById('display-title');
        return el && el.textContent !== '🔄 INICIANDO V3...';
    });

    const testTitle = 'Rincón de Baviera V2 Test';
    await adminPage.fill('#input-title', testTitle);
    await adminPage.click('button:has-text("Guardar Título")');

    // Refresh index page to get latest data from db
    await indexPage.reload({ waitUntil: 'networkidle' });
    await indexPage.waitForFunction(() => {
        const el = document.getElementById('display-title');
        return el && el.textContent !== 'Cargando...';
    });
    let updatedTitle = await indexPage.locator('#display-title').textContent();
    console.log('INDEX REFRESHED TITLE:', updatedTitle);

    // Revert the title
    await adminPage.fill('#input-title', initialTitle);
    await adminPage.click('button:has-text("Guardar Título")');

    await indexPage.reload({ waitUntil: 'networkidle' });
    await indexPage.waitForFunction(() => {
        const el = document.getElementById('display-title');
        return el && el.textContent !== 'Cargando...';
    });
    const revertedTitle = await indexPage.locator('#display-title').textContent();
    console.log('INDEX REVERTED AFTER REFRESH TITLE:', revertedTitle);

    await browser.close();
    server.close();
});
