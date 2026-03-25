const https = require('https');

https.get('https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/markers-plugin@5.1.4/index.min.js', (res) => {
  let data = '';
  res.on('data', (chunk) => { data += chunk; });
  res.on('end', () => {
    // Print first 200 characters to see module definition
    console.log(data.substring(0, 300));
  });
});
