const { chromium } = require('playwright');
const assert = require('assert');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();
    await page.goto('file://' + __dirname + '/admin.html');

    const poiFixTest = await page.evaluate(async () => {
        try {
            await window.initClient(); // Ensure supabase is connected
            // Wait a little for initClient to complete if it returns promise
        } catch(e) {}

        const id = 'poi-test-fix';
        const { error } = await supabaseClient.from('pois').insert([{ id, content: 'Test POI', yaw: 0, pitch: 0 }]);
        if (error) return error;

        const { error: error2 } = await supabaseClient.from('pois').delete().eq('id', id);
        if (error2) return error2;

        return "SUCCESS";
    });
    console.log("POI Insert with removed visualType:", poiFixTest);

    await browser.close();
})();
