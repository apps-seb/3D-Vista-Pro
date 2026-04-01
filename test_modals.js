// ensure the inputs inside the modal work smoothly
const fs = require('fs');

let indexHtml = fs.readFileSync('index.html', 'utf8');

// The original `input-glass` was modified.
indexHtml = indexHtml.replace(/background: rgba\(255, 255, 255, 0\.9\);/g, 'background: rgba(0, 0, 0, 0.05);');

// The sim-meses-rango input type=range has a white track that may look invisible on white glass.
indexHtml = indexHtml.replace(/background: rgba\(255, 255, 255, 0\.2\);/g, 'background: rgba(0, 0, 0, 0.1);');

// Change modal buttons text to white specifically instead of replacing everywhere.
// The primary orange is #f26522 and gradient is linear-gradient(135deg, #f26522 0%, #ff8c00 100%)
// We already replaced these above.

fs.writeFileSync('index.html', indexHtml);
