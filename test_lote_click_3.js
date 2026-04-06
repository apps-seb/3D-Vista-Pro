const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    const page = await context.newPage();

    await page.goto('http://localhost:8080/index.html');

    await page.waitForSelector('.btn-whatsapp', { state: 'attached' });
    await page.waitForTimeout(3000);

    const projects = await page.$$eval('#lista-proyectos-publicos > div', els => els.length);
    console.log('Projects found:', projects);

    if (projects > 0) {
        await page.click('#lista-proyectos-publicos > div:first-child');
        await page.waitForTimeout(5000);

        await page.evaluate(() => {
            const preloader = document.getElementById('preloader');
            if(preloader) preloader.style.display = 'none';
            const welcomeModal = document.getElementById('welcome-modal');
            if(welcomeModal) welcomeModal.classList.remove('active');
        });

        await page.waitForTimeout(1000);

        // Wait for `.lote-marker` to be attached.
        const loteMarkers = await page.$$('.lote-marker');
        console.log("Lote HTML elements found:", loteMarkers.length);

        if (loteMarkers.length > 0) {
             const bbox = await loteMarkers[0].boundingBox();
             console.log("Bounding box of HTML element:", bbox);

             if (bbox) {
                 await page.mouse.click(bbox.x + bbox.width / 2, bbox.y + bbox.height / 2);
                 console.log("Clicked HTML marker bounding box center");
             }
        }

        await page.waitForTimeout(1000);
        const isModalActive = await page.$eval('#premium-modal', el => el.classList.contains('active'));
        console.log('Premium modal active after click:', isModalActive);

    }

    await browser.close();
})();
