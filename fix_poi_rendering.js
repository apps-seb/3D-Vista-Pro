const fs = require('fs');

function updateRendering(filename) {
    let content = fs.readFileSync(filename, 'utf8');

    let parsingLogic = `
                            let isCard = false;
                            let cardImg = null;
                            let cardIcon = null;
                            let cardSub = null;

                            // Extraer base64 y texto de p.content porque p.visualtype no existe en admin.html inserción
                            if (p.content && p.content.startsWith('IMG|')) {
                                const parts = p.content.split('|TEXT|');
                                displayVisualType = parts[0];
                                displayText = parts[1] || 'Punto';
                            } else if (p.content && p.content.startsWith('LINK360|')) {
                                isLink360 = true;
                                if (p.content.includes('|ICON|')) {
                                    const parts1 = p.content.split('|ICON|');
                                    link360Url = parts1[0].substring(8);
                                    const parts2 = parts1[1].split('|TEXT|');
                                    link360Icon = parts2[0];
                                    displayText = parts2[1] || 'Vista 360';
                                } else {
                                    const parts = p.content.split('|TEXT|');
                                    link360Url = parts[0].substring(8);
                                    displayText = parts[1] || 'Vista 360';
                                }
                            } else if (p.content && p.content.startsWith('CARD|')) {
                                isCard = true;
                                const parts = p.content.split('|ICON|');
                                cardImg = parts[0].substring(5); // Remove 'CARD|'

                                const subParts = parts[1].split('|TEXT|');
                                cardIcon = subParts[0]; // Can be empty string

                                const textParts = subParts[1].split('|SUB|');
                                displayText = textParts[0];
                                cardSub = textParts[1];
                            }`;

    // In index.html the var is `p.content`, in admin.html it's `content` in `renderPOI`.
    // Wait, let's look at index.html exactly:
    let indexHtml = fs.readFileSync('index.html', 'utf8');

    let indexParse = `
                            let isCard = false;
                            let cardImg = null;
                            let cardIcon = null;
                            let cardSub = null;

                            // Extraer base64 y texto de p.content porque p.visualtype no existe en admin.html inserción
                            if (p.content && p.content.startsWith('IMG|')) {
                                const parts = p.content.split('|TEXT|');
                                displayVisualType = parts[0];
                                displayText = parts[1] || 'Punto';
                            } else if (p.content && p.content.startsWith('LINK360|')) {
                                isLink360 = true;
                                if (p.content.includes('|ICON|')) {
                                    const parts1 = p.content.split('|ICON|');
                                    link360Url = parts1[0].substring(8);
                                    const parts2 = parts1[1].split('|TEXT|');
                                    link360Icon = parts2[0];
                                    displayText = parts2[1] || 'Vista 360';
                                } else {
                                    const parts = p.content.split('|TEXT|');
                                    link360Url = parts[0].substring(8);
                                    displayText = parts[1] || 'Vista 360';
                                }
                            } else if (p.content && p.content.startsWith('CARD|')) {
                                isCard = true;
                                const parts = p.content.split('|ICON|');
                                cardImg = parts[0].substring(5); // Remove 'CARD|'

                                const subParts = parts[1].split('|TEXT|');
                                cardIcon = subParts[0]; // Can be empty string

                                const textParts = subParts[1].split('|SUB|');
                                displayText = textParts[0] || '';
                                cardSub = textParts[1] || '';
                            }
    `;

    indexHtml = indexHtml.replace(/\/\/ Extraer base64 y texto de p\.content porque p\.visualtype no existe en admin\.html inserción[\s\S]*?}/, indexParse);

    // Then add the HTML generation for CARD
    let indexRender = `
                            let htmlContent = '';
                            if (isCard) {
                                htmlContent = \`<div style="display:flex;flex-direction:column;align-items:center;">
                                        <div style="background:#0b6156; border-radius:8px; overflow:hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.5); width: 200px;">
                                            <div style="width: 100%; height: 120px; background: url('\${cardImg}') center center / cover no-repeat;"></div>
                                            <div style="display: flex; align-items: center; padding: 10px; gap: 10px;">
                                                \${cardIcon ? \`<img src="\${cardIcon}" style="width:30px; height:30px; object-fit:contain;">\` : ''}
                                                <div style="color: white; font-family: 'Montserrat', sans-serif; text-align: left;">
                                                    <div style="font-size: 0.75rem; text-transform: uppercase;">\${displayText}</div>
                                                    <div style="font-size: 1rem; font-weight: bold; line-height: 1;">\${cardSub}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="width:2px;height:50px;background:#0b6156;margin-top:0px;"></div>
                                        <div style="width:12px;height:12px;background:#0b6156;border-radius:50%;border:2px solid white;"></div>
                                       </div>\`;
                            } else if (isLink360) {`;

    indexHtml = indexHtml.replace(/let htmlContent = '';[\s\S]*?if \(isLink360\) {/, indexRender);
    fs.writeFileSync('index.html', indexHtml);

    // Now for Admin.html
    let adminHtml = fs.readFileSync('admin.html', 'utf8');
    let adminParse = `
                let isCard = false;
                let cardImg = null;
                let cardIcon = null;
                let cardSub = null;

                if (content && content.startsWith('IMG|')) {
                    const parts = content.split('|TEXT|');
                    displayVisualType = parts[0];
                    displayText = parts[1] || 'Punto';
                } else if (content && content.startsWith('LINK360|')) {
                    isLink360 = true;
                    if (content.includes('|ICON|')) {
                        const parts1 = content.split('|ICON|');
                        link360Url = parts1[0].substring(8);
                        const parts2 = parts1[1].split('|TEXT|');
                        link360Icon = parts2[0];
                        displayText = parts2[1] || 'Vista 360';
                    } else {
                        const parts = content.split('|TEXT|');
                        link360Url = parts[0].substring(8); // quita 'LINK360|'
                        displayText = parts[1] || 'Vista 360';
                    }
                } else if (content && content.startsWith('CARD|')) {
                    isCard = true;
                    const parts = content.split('|ICON|');
                    cardImg = parts[0].substring(5); // Remove 'CARD|'

                    const subParts = parts[1].split('|TEXT|');
                    cardIcon = subParts[0]; // Can be empty string

                    const textParts = subParts[1].split('|SUB|');
                    displayText = textParts[0] || '';
                    cardSub = textParts[1] || '';
                }
    `;
    adminHtml = adminHtml.replace(/if \(content && content\.startsWith\('IMG\|'\)\) {[\s\S]*?}/, adminParse);

    let adminRender = `
                    let htmlContent = '';
                    if (isCard) {
                        htmlContent = \`<div style="display:flex;flex-direction:column;align-items:center;">
                            <div style="background:#0b6156; border-radius:8px; overflow:hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.5); width: 200px;">
                                <div style="width: 100%; height: 120px; background: url('\${cardImg}') center center / cover no-repeat;"></div>
                                <div style="display: flex; align-items: center; padding: 10px; gap: 10px;">
                                    \${cardIcon ? \`<img src="\${cardIcon}" style="width:30px; height:30px; object-fit:contain;">\` : ''}
                                    <div style="color: white; font-family: 'Montserrat', sans-serif; text-align: left;">
                                        <div style="font-size: 0.75rem; text-transform: uppercase;">\${displayText}</div>
                                        <div style="font-size: 1rem; font-weight: bold; line-height: 1;">\${cardSub}</div>
                                    </div>
                                </div>
                            </div>
                            <div style="width:2px;height:50px;background:#0b6156;margin-top:0px;"></div>
                            <div style="width:12px;height:12px;background:#0b6156;border-radius:50%;border:2px solid white;"></div>
                        </div>\`;
                    } else if (isLink360) {`;

    adminHtml = adminHtml.replace(/let htmlContent = '';[\s\S]*?if \(isLink360\) {/, adminRender);
    fs.writeFileSync('admin.html', adminHtml);
}

updateRendering('index.html');
console.log("Rendering updated");
