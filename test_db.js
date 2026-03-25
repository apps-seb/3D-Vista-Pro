const { createClient } = require('@supabase/supabase-js');
const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
    const { data, error } = await supabase.from('configuracion').select('id, titulo').eq('id', 1).maybeSingle();
    console.log("Config:", data, error);
    const { data: lotes, error: lotesError } = await supabase.from('lotes').select('*');
    console.log("Lotes count:", lotes ? lotes.length : 0, lotesError);
}
test();
