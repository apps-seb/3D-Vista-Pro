const fs = require('fs');

// We need to make sure the marker for the card doesn't scale but looks attached and stays still.
// By default, markers in PhotoSphereViewer don't change size when zooming.
// The anchor "bottom center" is important. The pin goes exactly to the clicked coordinates.
let indexHtml = fs.readFileSync('index.html', 'utf8');

// For the CARD, we want `anchor: 'bottom center'`. This is already applied universally to POIs in renderPOI:
// `size: { width: 120, height: 160 }, anchor: 'bottom center'`
// But wait, our card is `width: 200px` now. We should dynamically set the size or just set `size: { width: 200, height: 200 }`.
// Let's modify the size logic in both index and admin

indexHtml = indexHtml.replace(/size: \{ width: 120, height: 160 \}, anchor: 'bottom center',/g,
    "size: isCard ? { width: 200, height: 200 } : { width: 120, height: 160 }, anchor: 'bottom center',");

fs.writeFileSync('index.html', indexHtml);

let adminHtml = fs.readFileSync('admin.html', 'utf8');
adminHtml = adminHtml.replace(/size: \{ width: 120, height: 160 \},/g,
    "size: isCard ? { width: 200, height: 200 } : { width: 120, height: 160 },");
fs.writeFileSync('admin.html', adminHtml);
