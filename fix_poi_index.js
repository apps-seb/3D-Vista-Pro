const fs = require('fs');

let indexHtml = fs.readFileSync('index.html', 'utf8');

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

indexHtml = indexHtml.replace(/let htmlContent = '';[\s\S]*?if \(isLink360\) {/, indexRender);
fs.writeFileSync('index.html', indexHtml);

let adminHtml = fs.readFileSync('admin.html', 'utf8');
let adminRender = `
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
adminHtml = adminHtml.replace(/let htmlContent = '';[\s\S]*?if \(isLink360\) {/, adminRender);
fs.writeFileSync('admin.html', adminHtml);
