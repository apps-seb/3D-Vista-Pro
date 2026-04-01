const fs = require('fs');
let index = fs.readFileSync('index.html', 'utf8');

// The original .btn-whatsapp style:
// .btn-whatsapp { display: block; padding: 15px; background: #25D366; color: white; text-decoration: none; font-weight: bold; border-radius: 8px; margin-top: 15px; text-transform: uppercase; font-size: 0.9rem; box-sizing: border-box; }
// Fix hardcoded styles in buttons, labels, etc.
index = index.replace(/style="[^"]*background:\s*#D4AF37[^"]*"/gi, function(match) {
    return match.replace(/#D4AF37/g, '#f26522').replace(/color:\s*#000/g, 'color: #ffffff');
});

index = index.replace(/color:\s*#ddd/gi, 'color: #666');
index = index.replace(/color:\s*#fff/gi, 'color: #1a1a1a');

// We also need to fix modal backgrounds if any were manually hardcoded
index = index.replace(/background:\s*rgba\(0,0,0,0\.2\)/gi, 'background: rgba(242,101,34,0.05)');
index = index.replace(/border-top:\s*1px\s*solid\s*#444/gi, 'border-top: 1px solid rgba(0,0,0,0.1)');
index = index.replace(/border:\s*1px\s*solid\s*rgba\(212,\s*175,\s*55,\s*0\.2\)/gi, 'border: 1px solid rgba(242,101,34,0.2)');
index = index.replace(/color:\s*#D4AF37/gi, 'color: #f26522');

// Fix button WhatsApp green
index = index.replace(/color:\s*white/gi, 'color: #ffffff');
index = index.replace(/color:\s*#ffffff;/gi, 'color: #ffffff;'); // Don't replace if it's already #ffffff text inside #25D366

fs.writeFileSync('index.html', index);
