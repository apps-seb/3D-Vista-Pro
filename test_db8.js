const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Testing insert without upsert on configuracion table...");
  // Let's check what maybeSingle returns for an empty query
  const { data, error } = await supabase.from('configuracion').select('*').eq('id', 999).maybeSingle();
  console.log("Fetch 999:", data, error);

  if (!data) {
    const { error: insertError } = await supabase.from('configuracion').insert([{ id: 999, titulo: 'test' }]);
    console.log("Insert 999:", insertError);
    if (!insertError) {
      await supabase.from('configuracion').delete().eq('id', 999);
    }
  }
}

test();
