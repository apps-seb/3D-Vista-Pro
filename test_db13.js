const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Can we upload using fetch?");
  const res = await fetch(`${supabaseUrl}/storage/v1/object/imagenes_360/test.txt`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${supabaseKey}`,
      'apikey': supabaseKey,
      'Content-Type': 'text/plain'
    },
    body: 'hello'
  });
  console.log("Fetch upload:", res.status, await res.text());
}

test();
