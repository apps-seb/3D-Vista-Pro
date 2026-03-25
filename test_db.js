const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Testing config fetch...");
  const { data, error } = await supabase.from('configuracion').select('*').eq('id', 1).maybeSingle();
  console.log("Fetch:", { data, error });

  console.log("Testing file upload...");
  const { error: uploadError } = await supabase.storage.from('imagenes_360').upload('test.txt', 'hello world', {
    contentType: 'text/plain'
  });
  console.log("Upload:", { error: uploadError });
}

test();
