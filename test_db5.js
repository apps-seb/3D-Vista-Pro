const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Cleanup id 2...");
  await supabase.from('configuracion').delete().eq('id', 2);

  console.log("Testing upload to another bucket?");
  const { data, error } = await supabase.storage.listBuckets();
  console.log("Buckets:", data, error);
}

test();
