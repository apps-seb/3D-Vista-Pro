const { chromium } = require('playwright');
const assert = require('assert');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();
    await page.goto('file://' + __dirname + '/admin.html');

    const columnsConfig = await page.evaluate(async () => {
        const { data, error } = await supabaseClient.from('configuracion').select('*').limit(1);
        return { data: data ? Object.keys(data[0] || {}) : null, error };
    });
    console.log("Config columns:", columnsConfig);

    const columnsPois = await page.evaluate(async () => {
        // Just try inserting a dummy POI to see what column fails
        const { error } = await supabaseClient.from('pois').insert([{ id: 'test', content: 'test', yaw: 0, pitch: 0 }]);
        if (error) {
           return error;
        }
        await supabaseClient.from('pois').delete().eq('id', 'test');
        return "SUCCESS";
    });
    console.log("Pois insert result:", columnsPois);

    const columnsPoisAll = await page.evaluate(async () => {
        const { error } = await supabaseClient.from('pois').insert([{ id: 'test2', visualType: 'texto', content: 'test', yaw: 0, pitch: 0 }]);
        if (error) {
           return error;
        }
        await supabaseClient.from('pois').delete().eq('id', 'test2');
        return "SUCCESS_WITH_VISUALTYPE";
    });
    console.log("Pois insert with visualType result:", columnsPoisAll);

    await browser.close();
})();
