const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Testing update existing file...");
  const { data: updateData, error: updateError } = await supabase.storage.from('imagenes_360').update('test.txt', 'hello world 2', {
    cacheControl: '3600',
    upsert: true
  });
  console.log("Update:", updateData, updateError);
}

test();
