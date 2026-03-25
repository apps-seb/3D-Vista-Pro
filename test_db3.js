const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Testing file upload to same name...");
  const { error: uploadError } = await supabase.storage.from('imagenes_360').upload('panorama_123.jpg', 'fake_data', {
    contentType: 'image/jpeg',
    upsert: false
  });
  console.log("Upload error without upsert:", { error: uploadError });

  console.log("Testing file upload with upsert...");
  const { error: uploadError2 } = await supabase.storage.from('imagenes_360').upload('panorama_123.jpg', 'fake_data', {
    contentType: 'image/jpeg',
    upsert: true
  });
  console.log("Upload error with upsert:", { error: uploadError2 });
}

test();
