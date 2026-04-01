const fs = require('fs');

function updateModals(filename) {
    let content = fs.readFileSync(filename, 'utf8');

    // Make modal professional glass effect (similar to landing page)
    content = content.replace(
        /background:\s*rgba\(17,\s*17,\s*17,\s*0\.45\);/g,
        'background: rgba(255, 255, 255, 0.85);\n            color: #1a1a1a;'
    );
    content = content.replace(
        /border:\s*1px\s*solid\s*rgba\(212,\s*175,\s*55,\s*0\.3\);/g,
        'border: 1px solid rgba(242, 101, 34, 0.15);'
    );
    content = content.replace(
        /color:\s*white;\n\s*text-align:\s*center;/g,
        'color: #1a1a1a;\n            text-align: center;'
    );
    // Remove inline color: #fff on text in modals
    content = content.replace(/color:\s*#fff;/g, 'color: #1a1a1a;');
    content = content.replace(/color:\s*#ddd;/g, 'color: #666;');
    content = content.replace(/background:\s*rgba\(34,\s*34,\s*34,\s*0\.5\);/g, 'background: rgba(0, 0, 0, 0.05);');
    content = content.replace(/border:\s*1px\s*solid\s*rgba\(255,255,255,0\.1\);/g, 'border: 1px solid rgba(0,0,0,0.1);');
    content = content.replace(/background:\s*rgba\(0,\s*0,\s*0,\s*0\.3\);/g, 'background: rgba(255, 255, 255, 0.9);');
    content = content.replace(/background:\s*rgba\(0,0,0,0\.2\);/g, 'background: rgba(255, 255, 255, 0.9);');
    content = content.replace(/color:\s*white;/g, 'color: #ffffff;');

    // Fix modal titles color
    content = content.replace(/class="modal-title"/g, 'class="modal-title" style="color: #f26522;"');

    // The btn-whatsapp should have white text
    content = content.replace(/class="btn-whatsapp"/g, 'class="btn-whatsapp" style="color: #ffffff;"');

    fs.writeFileSync(filename, content);
    console.log(`Updated modals in ${filename}`);
}

updateModals('index.html');
// Note: We might want admin to stay dark to match its overall dark mode, but we can make it a bit lighter dark or just follow strictly. The user said: "hacer que los modales no sean negros si no que sean mas profesionales, que se vean como una tarjeta glass liquid"
