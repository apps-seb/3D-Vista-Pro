const fs = require('fs');
const https = require('https');

https.get('https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/markers-plugin@5.1.4/index.min.js', (res) => {
  let data = '';
  res.on('data', (chunk) => { data += chunk; });
  res.on('end', () => {
    const match = data.match(/exports\.(\w+)=/);
    if (match) {
      console.log('Exported name:', match[1]);
    }
    const match2 = data.match(/global\.(\w+)=/);
    if (match2) {
      console.log('Global name:', match2[1]);
    }

    // Check for UMD format
    const umdMatch = data.match(/root\["([^"]+)"\]/);
    if (umdMatch) {
      console.log('UMD global name:', umdMatch[1]);
    }
  });
});
