const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Testing update on configuracion table...");
  const { error: updateError } = await supabase.from('configuracion').update({ imagen_url: 'https://test.com/image.jpg' }).eq('id', 1);
  console.log("Update error:", updateError);

  console.log("Testing insert on configuracion table...");
  const { error: insertError } = await supabase.from('configuracion').insert([{ id: 2, imagen_url: 'https://test.com/image.jpg' }]);
  console.log("Insert error:", insertError);
}

test();
