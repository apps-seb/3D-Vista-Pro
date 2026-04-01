const fs = require('fs');
let index = fs.readFileSync('index.html', 'utf8');

index = index.replace(/\.input-glass {([\s\S]*?)}/g,
`.input-glass {
            width: 100%;
            padding: 10px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #1a1a1a;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: inherit;
            transition: border-color 0.3s, background 0.3s;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        .input-glass:focus {
            outline: none;
            border-color: #f26522;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(242, 101, 34, 0.2);
        }`);

fs.writeFileSync('index.html', index);
