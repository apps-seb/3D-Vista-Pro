const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Check if we can read the url...");
  const { data } = supabase.storage.from('imagenes_360').getPublicUrl('test.jpg');
  console.log("URL:", data);
}

test();
