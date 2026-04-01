const fs = require('fs');
let admin = fs.readFileSync('admin.html', 'utf8');

// For inputs inside modals in admin.html, ensure they are visible
admin = admin.replace(/background:\s*rgba\(0,0,0,0\.5\);/g, 'background: rgba(0,0,0,0.05);');
admin = admin.replace(/border:\s*1px\s*solid\s*rgba\(255,255,255,0\.2\);/g, 'border: 1px solid rgba(0,0,0,0.1);');
admin = admin.replace(/color:\s*#fff;/g, 'color: #1a1a1a;');
admin = admin.replace(/color:\s*#ddd;/g, 'color: #666;');

// Remove the global #fff color override if it messes up inputs in modals
admin = admin.replace(/<h3 style="color: #1a1a1a;">Editar Título<\/h3>/g, '<h3 style="color: #f26522;">Editar Título</h3>');

fs.writeFileSync('admin.html', admin);
