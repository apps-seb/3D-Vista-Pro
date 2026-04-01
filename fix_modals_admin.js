const fs = require('fs');
let admin = fs.readFileSync('admin.html', 'utf8');

admin = admin.replace(/\.modal-content {([\s\S]*?)}/g,
`.modal-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 25px;
            border-radius: 16px;
            border: 1px solid rgba(242, 101, 34, 0.15);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 320px;
            box-sizing: border-box;
            color: #1a1a1a;
        }`);

admin = admin.replace(/\.modal-content h3 {([\s\S]*?)}/g,
`.modal-content h3 { margin-top: 0; color: #f26522; font-size: 1.2rem; text-align: center; }`);

admin = admin.replace(/\.modal-glass {([\s\S]*?)}/g,
`.modal-glass {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(242, 101, 34, 0.15) !important;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        }`);

fs.writeFileSync('admin.html', admin);
