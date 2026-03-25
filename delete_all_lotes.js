const { createClient } = require('@supabase/supabase-js');
const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function run() {
    const { data: lotes, error: lotesError } = await supabase.from('lotes').select('id');
    console.log("Current lotes to delete:", lotes);
    if (lotes && lotes.length > 0) {
        for (let lote of lotes) {
            await supabase.from('lotes').delete().eq('id', lote.id);
        }
    }
    console.log("Deleted");
}
run();
