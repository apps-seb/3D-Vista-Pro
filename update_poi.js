const fs = require('fs');

let adminHtml = fs.readFileSync('admin.html', 'utf8');
let indexHtml = fs.readFileSync('index.html', 'utf8');

// 1. Add "Tarjeta Incrustada" to admin.html POI modal
let adminModalReplacement = `
            <select id="poi-tipo-visual" class="input-select" onchange="cambiarTipoPOI(this.value)">
                <option value="texto">Texto Simple</option>
                <option value="imagen">Logo / Imagen</option>
                <option value="vista360">Vista 360 Adicional</option>
                <option value="tarjeta">Tarjeta Incrustada</option>
            </select>
            <input type="text" id="poi-texto" class="input-text" placeholder="Texto (Ej: Zona Social)">

            <div id="poi-tarjeta-container" style="display: none; margin-bottom: 10px;">
                <label class="btn btn-upload" id="lbl-upload-poi-tarjeta-img">
                    Subir Imagen Tarjeta (PNG/JPG)
                    <input type="file" id="upload-poi-tarjeta-img" accept="image/png, image/jpeg" style="display:none;" onchange="cargarImagenTarjetaPOI(event)">
                </label>
                <input type="hidden" id="poi-tarjeta-img-data">
                <img id="poi-tarjeta-img-preview" style="max-width: 100%; max-height: 100px; display: none; margin-top: 10px; border-radius: 4px; border: 1px solid rgba(0,0,0,0.1);" />

                <label class="btn btn-upload" id="lbl-upload-poi-tarjeta-icon" style="margin-top: 10px;">
                    Subir Ícono (Opcional)
                    <input type="file" id="upload-poi-tarjeta-icon" accept="image/png, image/jpeg" style="display:none;" onchange="cargarIconoTarjetaPOI(event)">
                </label>
                <input type="hidden" id="poi-tarjeta-icon-data">
                <img id="poi-tarjeta-icon-preview" style="max-width: 50px; max-height: 50px; display: none; margin-top: 10px; border-radius: 4px; border: 1px solid rgba(0,0,0,0.1);" />

                <input type="text" id="poi-tarjeta-sub" class="input-text" placeholder="Subtítulo (Ej: 25 MINUTOS)" style="margin-top: 10px;">
            </div>
`;
adminHtml = adminHtml.replace(/<select id="poi-tipo-visual" class="input-select" onchange="cambiarTipoPOI\(this.value\)">[\s\S]*?<input type="text" id="poi-texto" class="input-text" placeholder="Texto \(Ej: Zona Social\)">/, adminModalReplacement);

// Update cambiarTipoPOI
let cambiarTipoReplacement = `window.cambiarTipoPOI = function(tipo) {
            document.getElementById('poi-imagen-container').style.display = 'none';
            document.getElementById('poi-vista360-container').style.display = 'none';
            document.getElementById('poi-tarjeta-container').style.display = 'none';

            if (tipo === 'imagen') {
                document.getElementById('poi-imagen-container').style.display = 'block';
            } else if (tipo === 'vista360') {
                document.getElementById('poi-vista360-container').style.display = 'block';
            } else if (tipo === 'tarjeta') {
                document.getElementById('poi-tarjeta-container').style.display = 'block';
            }
        }`;
adminHtml = adminHtml.replace(/window\.cambiarTipoPOI = function\(tipo\) {[\s\S]*?}/, cambiarTipoReplacement);

// Update file upload handlers
let uploadHandlers = `
        window.cargarImagenTarjetaPOI = async function(event) {
            const file = event.target.files[0];
            if (!file) return;
            window.mostrarProgreso(10);
            try {
                const base64Compressed = await window.comprimirImagen(file, 800, 800, 0.9);
                document.getElementById('poi-tarjeta-img-data').value = base64Compressed;
                const preview = document.getElementById('poi-tarjeta-img-preview');
                preview.src = base64Compressed;
                preview.style.display = 'block';
                window.mostrarProgreso(100);
            } catch (error) {
                window.mostrarError("Cargar Imagen Tarjeta POI", error);
            }
        };

        window.cargarIconoTarjetaPOI = async function(event) {
            const file = event.target.files[0];
            if (!file) return;
            window.mostrarProgreso(10);
            try {
                const base64Compressed = await window.comprimirImagen(file, 200, 200, 0.9);
                document.getElementById('poi-tarjeta-icon-data').value = base64Compressed;
                const preview = document.getElementById('poi-tarjeta-icon-preview');
                preview.src = base64Compressed;
                preview.style.display = 'block';
                window.mostrarProgreso(100);
            } catch (error) {
                window.mostrarError("Cargar Icono Tarjeta POI", error);
            }
        };
`;
adminHtml = adminHtml.replace(/window\.cargarLogoPOI = async function\(event\) {[\s\S]*?};/, `window.cargarLogoPOI = async function(event) {
            const file = event.target.files[0];
            if (!file) return;
            window.mostrarProgreso(10);
            try {
                const base64Compressed = await window.comprimirImagen(file, 800, 800, 0.9);
                document.getElementById('poi-img-data').value = base64Compressed;
                const preview = document.getElementById('poi-img-preview');
                preview.src = base64Compressed;
                preview.style.display = 'block';
                window.mostrarProgreso(100);
            } catch (error) {
                window.mostrarError("Cargar Logo POI", error);
            }
        };${uploadHandlers}`);


// Update guardarPOI
let guardarPOIReplacement = `
                let newContent = texto;

                if (tipo === 'imagen') {
                    const imgData = document.getElementById('poi-img-data').value;
                    if (imgData) {
                        newContent = \`IMG|\${imgData}|TEXT|\${texto}\`;
                    }
                } else if (tipo === 'vista360') {
                    const url360 = document.getElementById('poi-360-url').value;
                    const iconData = document.getElementById('poi-360-icon-data').value;
                    if (!url360) {
                        alert("Por favor sube una imagen 360 antes de guardar.");
                        return;
                    }
                    if (iconData) {
                        newContent = \`LINK360|\${url360}|ICON|\${iconData}|TEXT|\${texto}\`;
                    } else {
                        newContent = \`LINK360|\${url360}|TEXT|\${texto}\`;
                    }
                } else if (tipo === 'tarjeta') {
                    const imgData = document.getElementById('poi-tarjeta-img-data').value;
                    const iconData = document.getElementById('poi-tarjeta-icon-data').value;
                    const subText = document.getElementById('poi-tarjeta-sub').value || '';
                    if (!imgData) {
                        alert("Sube la imagen para la tarjeta.");
                        return;
                    }
                    newContent = \`CARD|\${imgData}|ICON|\${iconData}|TEXT|\${texto}|SUB|\${subText}\`;
                }`;
adminHtml = adminHtml.replace(/let newContent = texto;[\s\S]*?}/, guardarPOIReplacement);

fs.writeFileSync('admin.html', adminHtml);
console.log("adminHtml modified successfully");
