const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Check storage permissions?");
  const res = await fetch(`${supabaseUrl}/storage/v1/object/public/imagenes_360/panorama_1720000000000.jpg`, {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${supabaseKey}`,
      'apikey': supabaseKey
    }
  });
  console.log("Fetch read public:", res.status);

  // Try to insert a row into configuracion simulating what the upload catch might do if it swallowed upload error and tried to insert.
  // Actually, upload error IS thrown directly.

}

test();
