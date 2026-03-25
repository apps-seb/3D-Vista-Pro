const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Testing config upload using upsert via public url");
  const { data: uploadData, error: uploadError } = await supabase.storage.from('imagenes_360').upload('test.txt', 'hello world', {
    cacheControl: '3600',
    upsert: false
  });
  console.log("Upload:", uploadData, uploadError);
}

test();
