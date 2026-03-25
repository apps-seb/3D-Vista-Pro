const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Testing config fetch...");
  const { data, error } = await supabase.from('configuracion').select('*').eq('id', 1).maybeSingle();
  console.log("Fetch:", { data, error });

  console.log("Testing database update...");
  const { error: updateError } = await supabase.from('configuracion').update({ titulo: 'Rincón de Baviera' }).eq('id', 1);
  console.log("Update database:", { error: updateError });
}

test();
