const fs = require('fs');

// We have the new modal backgrounds applied nicely with glassmorphism.
// Let's refine the button colors to match the gradient/white layout perfectly.
// Let's check the index.html
let index = fs.readFileSync('index.html', 'utf8');
index = index.replace(/background: rgba\(255,255,255,0\.1\); color: #1a1a1a; border: 1px solid rgba\(0,0,0,0\.1\);/g, 'background: rgba(242,101,34,0.1); color: #f26522; border: 1px solid rgba(242,101,34,0.3);');
fs.writeFileSync('index.html', index);

// Let's also verify that we haven't inadvertently left testing artifacts.
