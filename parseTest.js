const assert = require('assert');

// Simulate parsePoligono function logic
function parsePoligono(polyStr) {
    if (!polyStr) return null;
    try {
        let parsed = JSON.parse(polyStr);
        if (Array.isArray(parsed)) {
            return { points: parsed, strokeWidth: 2 }; // Default width si es formato antiguo
        }
        return parsed; // Asume que es el nuevo formato { points: [...], strokeWidth: X }
    } catch (e) {
        console.warn("Error parseando poligono", e);
        return null;
    }
}

function testParse() {
    let oldFormat = JSON.stringify([[0,0], [1,1], [2,2]]);
    let oldParsed = parsePoligono(oldFormat);
    assert.deepEqual(oldParsed, { points: [[0,0], [1,1], [2,2]], strokeWidth: 2 });

    let newFormat = JSON.stringify({ points: [[0,0], [1,1], [2,2]], strokeWidth: 5 });
    let newParsed = parsePoligono(newFormat);
    assert.deepEqual(newParsed, { points: [[0,0], [1,1], [2,2]], strokeWidth: 5 });
    console.log('parsePoligono tests passed');
}

testParse();
