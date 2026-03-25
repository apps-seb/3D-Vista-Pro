const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Check if we can insert a lot...");
  const { error } = await supabase.from('lotes').insert([{ id: 'lote-test', nombre: 'Test', area: 1, precio: 1, estado: 'Disponible', yaw: 0, pitch: 0 }]);
  console.log("Insert Lote:", error);
  if (!error) {
    await supabase.from('lotes').delete().eq('id', 'lote-test');
  }
}

test();
