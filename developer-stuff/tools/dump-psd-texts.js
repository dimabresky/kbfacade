/**
 * Dump editable text layers from the main landing PSD (for copy alignment).
 *
 *   node developer-stuff/tools/dump-psd-texts.js > developer-stuff/tools/psd-texts.txt
 */
const fs = require('fs');
const path = require('path');
const os = require('os');

const ROOT = process.env.KBF_ROOT || path.resolve(__dirname, '../..');
const DEP = path.join(process.env.TEMP || os.tmpdir(), 'kbfacade-psd-export/node_modules');
const { readPsd, initializeCanvas } = require(path.join(DEP, 'ag-psd'));

initializeCanvas(
  (w, h) => ({ width: w, height: h, getContext() { throw new Error('no'); } }),
  (w, h) => ({ width: w, height: h, data: new Uint8ClampedArray(w * h * 4) })
);

const psdName = fs.readdirSync(path.join(ROOT, 'developer-stuff')).find((n) => n.includes('Сайт_1') && n.endsWith('.psd'));
const psd = readPsd(fs.readFileSync(path.join(ROOT, 'developer-stuff', psdName)), {
  skipLayerImageData: true,
  skipCompositeImageData: true,
  skipThumbnail: true,
  useImageData: true,
});

function walk(node, acc = []) {
  if (!node) return acc;
  if (node.children) {
    for (const c of node.children) walk(c, acc);
  } else if (node.text && node.text.text) {
    acc.push({
      name: node.name,
      top: node.top || 0,
      left: node.left || 0,
      text: String(node.text.text).replace(/\r/g, '\n').trim(),
    });
  }
  return acc;
}

const texts = walk(psd).sort((a, b) => a.top - b.top || a.left - b.left);
for (const t of texts) {
  console.log(`--- [${t.top},${t.left}] ${t.name} ---`);
  console.log(t.text);
  console.log('');
}
console.log('TOTAL', texts.length);
