const { createClient } = require('@supabase/supabase-js');

const supabaseUrl = 'https://mungbffwwjobinfcympd.supabase.co';
const supabaseKey = 'sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e';
const supabase = createClient(supabaseUrl, supabaseKey);

async function test() {
  console.log("Upload image as base64 to some other bucket if it exists, maybe 'public'?");
  const { data, error } = await supabase.storage.from('public').upload('test.png', 'test', {
    contentType: 'image/png'
  });
  console.log("Upload public:", data, error);
}

test();
