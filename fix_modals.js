const fs = require('fs');
let index = fs.readFileSync('index.html', 'utf8');

// The replacement above might have created duplicate styles or unwanted changes. Let's fix the specific CSS rule for modal-content.

index = index.replace(/\.modal-content {\s+background: rgba\(17, 17, 17, 0\.45\);([\s\S]*?)color: #1a1a1a;([\s\S]*?)}/g,
`.modal-content {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(242, 101, 34, 0.15);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border-radius: 16px;
            padding: 25px;
            width: 85%;
            max-width: 320px;
            box-sizing: border-box;
            color: #1a1a1a;
            text-align: center;
            position: relative;
        }`);

fs.writeFileSync('index.html', index);
