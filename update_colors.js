const fs = require('fs');

function updateFile(filename) {
    let content = fs.readFileSync(filename, 'utf8');

    // Replace #D4AF37 with #f26522
    content = content.replace(/#D4AF37/g, '#f26522');

    // Replace gradient background specifically for the PDF and progress bars
    content = content.replace(/background:\s*#D4AF37/g, 'background: linear-gradient(135deg, #f26522 0%, #ff8c00 100%)');

    // PDF gradient top bar replacement
    content = content.replace(/background: linear-gradient\(90deg, #f26522, #f1c40f\);/g, 'background: linear-gradient(135deg, #f26522 0%, #ff8c00 100%);');

    fs.writeFileSync(filename, content);
    console.log(`Updated ${filename}`);
}

updateFile('index.html');
updateFile('admin.html');
