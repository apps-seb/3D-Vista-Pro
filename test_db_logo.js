const { chromium } = require('playwright');
const assert = require('assert');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();
    await page.goto('file://' + __dirname + '/admin.html');

    const configLogoTest = await page.evaluate(async () => {
        const { error } = await supabaseClient.from('configuracion').update({ logo_url: 'test' }).eq('id', 1);
        return error;
    });
    console.log("Config logo_url insert result:", configLogoTest);

    await browser.close();
})();
