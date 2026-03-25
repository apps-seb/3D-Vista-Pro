const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Check if we can update configuracion with a large string...");
  const largeString = 'data:image/jpeg;base64,' + 'A'.repeat(1024 * 1024 * 5); // 5MB string
  const { error } = await supabase.from('configuracion').update({ imagen_url: largeString }).eq('id', 1);
  console.log("Update large string:", error);

  if (!error) {
     // Revert it
     await supabase.from('configuracion').update({ imagen_url: null }).eq('id', 1);
  }
}

test();
