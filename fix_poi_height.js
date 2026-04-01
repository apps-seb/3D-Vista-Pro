const fs = require('fs');
let indexHtml = fs.readFileSync('index.html', 'utf8');

// The HTML for isCard is missing a closing div in the template I provided above, let me check:
// <div style="width: 100%; height: 120px; background-image: url('${cardImg}'); background-size: cover; background-position: center; border-bottom: 2px solid white;"></div>
// <div style="display: flex; ...
// </div>
// </div>
// Let's re-write the HTML directly for both files to ensure it's clean and has a transparent background for the marker.

let indexRender = `
                            let htmlContent = '';
                            if (isCard) {
                                htmlContent = \`<div style="display:flex;flex-direction:column;align-items:center;">
                                        <div style="background:#0b6156; border-radius:8px; overflow:hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.5); width: 200px; padding: 0;">
                                            <div style="width: 100%; height: 120px; background-image: url('\${cardImg}'); background-size: cover; background-position: center; border-bottom: 2px solid white;"></div>
                                            <div style="display: flex; align-items: center; padding: 10px; gap: 10px; background: #0b6156;">
                                                \${cardIcon ? \`<img src="\${cardIcon}" style="width:30px; height:30px; object-fit:contain;">\` : ''}
                                                <div style="color: white; font-family: 'Montserrat', sans-serif; text-align: left; line-height: 1.2;">
                                                    <div style="font-size: 0.75rem; text-transform: uppercase;">\${displayText}</div>
                                                    <div style="font-size: 1rem; font-weight: bold;">\${cardSub}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="width:2px;height:50px;background:#0b6156;margin-top:0px;"></div>
                                        <div style="width:12px;height:12px;background:#0b6156;border-radius:50%;border:2px solid white;"></div>
                                       </div>\`;
                            } else if (isLink360) {`;

// No changes needed, the template is good. Just making sure PSV size property doesn't cut it.
// If we set PSV size smaller than actual size it gets cropped or misaligned.
// The actual height of the card:
// Image: 120px
// Bottom text: ~50px
// Stick: 50px
// Dot: 12px
// Total = ~232px. Let's set the size to 200x250.

indexHtml = indexHtml.replace(/isCard \? \{ width: 200, height: 200 \} : \{ width: 120, height: 160 \}/g,
    "isCard ? { width: 200, height: 250 } : { width: 120, height: 160 }");

fs.writeFileSync('index.html', indexHtml);

let adminHtml = fs.readFileSync('admin.html', 'utf8');
adminHtml = adminHtml.replace(/isCard \? \{ width: 200, height: 200 \} : \{ width: 120, height: 160 \}/g,
    "isCard ? { width: 200, height: 250 } : { width: 120, height: 160 }");

fs.writeFileSync('admin.html', adminHtml);
