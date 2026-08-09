/**
 * Export optimized landing assets from layered PSDs into plugin assets/images.
 *
 * Dependencies (once):
 *   npm install --prefix "%TEMP%\kbfacade-psd-export" ag-psd sharp
 *
 * Usage:
 *   node developer-stuff/tools/export-landing-assets.js
 */
const fs = require('fs');
const path = require('path');
const os = require('os');

const ROOT = process.env.KBF_ROOT || path.resolve(__dirname, '../..');
const DEP = path.join(process.env.TEMP || os.tmpdir(), 'kbfacade-psd-export/node_modules');
const OUT = path.join(ROOT, 'wp-content/plugins/kbfacade-elementor/assets/images');

const { readPsd, initializeCanvas } = require(path.join(DEP, 'ag-psd'));
const sharp = require(path.join(DEP, 'sharp'));

initializeCanvas(
  (width, height) => ({
    width,
    height,
    getContext() {
      throw new Error('Canvas drawing disabled; use imageData');
    },
  }),
  (width, height) => ({
    width,
    height,
    data: new Uint8ClampedArray(width * height * 4),
  })
);

function findPsd(substr) {
  const f = fs.readdirSync(path.join(ROOT, 'developer-stuff')).find((n) => n.includes(substr) && n.endsWith('.psd'));
  if (!f) throw new Error('PSD not found: ' + substr);
  return path.join(ROOT, 'developer-stuff', f);
}

function walk(node, acc = []) {
  if (!node) return acc;
  if (node.children) {
    for (const child of node.children) walk(child, acc);
  } else if (node.name) {
    acc.push(node);
  }
  return acc;
}

function pixels(layer) {
  const src = layer && layer.imageData;
  if (!src || !src.data || !src.width || !src.height) return null;
  return {
    width: src.width,
    height: src.height,
    data: Buffer.from(src.data.buffer, src.data.byteOffset, src.data.byteLength),
  };
}

async function writeJpegWebp(raw, baseNoExt, opts = {}) {
  let pipeline = sharp(raw.data, { raw: { width: raw.width, height: raw.height, channels: 4 } });
  if (opts.crop) pipeline = pipeline.extract(opts.crop);
  if (opts.resize) pipeline = pipeline.resize(opts.resize);
  await pipeline.clone().jpeg({ quality: opts.quality || 84, mozjpeg: true }).toFile(baseNoExt + '.jpg');
  await pipeline.clone().webp({ quality: opts.webpQuality || 82 }).toFile(baseNoExt + '.webp');
  const m = await sharp(baseNoExt + '.jpg').metadata();
  console.log(path.relative(OUT, baseNoExt + '.*'), `${m.width}x${m.height}`);
}

const readOpts = {
  skipLayerImageData: false,
  skipCompositeImageData: true,
  skipThumbnail: true,
  useImageData: true,
};

(async () => {
  for (const d of ['hero', 'gallery', 'fastenings', 'objects', 'cta', 'form', 'catalog']) {
    fs.mkdirSync(path.join(OUT, d), { recursive: true });
  }

  // Worker cutout
  {
    const psd = readPsd(fs.readFileSync(findPsd('каске')), readOpts);
    const layers = walk(psd);
    const cutout = layers.find((l) => l.name === 'Слой 1') || layers.find((l) => l.imageData);
    const raw = pixels(cutout);
    if (!raw) throw new Error('Worker layer missing imageData');
    const resized = await sharp(raw.data, { raw: { width: raw.width, height: raw.height, channels: 4 } })
      .resize({ height: 1400, withoutEnlargement: true })
      .png({ compressionLevel: 9 })
      .toBuffer({ resolveWithObject: true });
    fs.writeFileSync(path.join(OUT, 'cta/worker.png'), resized.data);
    await sharp(resized.data).webp({ quality: 82, alphaQuality: 90 }).toFile(path.join(OUT, 'cta/worker.webp'));
    await sharp(resized.data)
      .flatten({ background: { r: 245, g: 245, b: 245 } })
      .jpeg({ quality: 85, mozjpeg: true })
      .toFile(path.join(OUT, 'cta/worker.jpg'));
    const m = await sharp(path.join(OUT, 'cta/worker.png')).metadata();
    console.log('cta/worker.*', `${m.width}x${m.height}`, 'alpha=' + m.hasAlpha);
  }

  // Site raster layers
  {
    const psd = readPsd(fs.readFileSync(findPsd('Сайт_1')), readOpts);
    const byName = Object.fromEntries(walk(psd).map((l) => [l.name, l]));

    const fastenings = ['Слой 46', 'Слой 47', 'Слой 48', 'Слой 49'];
    for (let i = 0; i < fastenings.length; i++) {
      const raw = pixels(byName[fastenings[i]]);
      if (!raw) throw new Error('Missing ' + fastenings[i]);
      await writeJpegWebp(raw, path.join(OUT, `fastenings/fastening-${i + 1}`), {
        resize: { width: 800, withoutEnlargement: true },
      });
    }

    // Hero from Слой 2
    {
      const raw = pixels(byName['Слой 2']);
      if (!raw) throw new Error('Missing Слой 2');
      const top = 90;
      const bottomTrim = 35;
      await writeJpegWebp(raw, path.join(OUT, 'hero/hero-1'), {
        crop: { left: 0, top, width: raw.width, height: raw.height - top - bottomTrim },
        resize: { width: 2000, withoutEnlargement: true },
      });
    }

    // Gallery 1:1:2 from Слой 35
    {
      const raw = pixels(byName['Слой 35']);
      if (!raw) throw new Error('Missing Слой 35');
      const parts = [1, 1, 2];
      const sum = parts.reduce((a, b) => a + b, 0);
      let x = 0;
      for (let i = 0; i < 3; i++) {
        const pw = i === 2 ? raw.width - x : Math.floor((raw.width * parts[i]) / sum);
        await writeJpegWebp(raw, path.join(OUT, `gallery/gallery-${i + 1}`), {
          crop: { left: x, top: 0, width: pw, height: raw.height },
        });
        x += pw;
      }
    }

    // Objects: keep native landscape tiles
    {
      const raw = pixels(byName['Слой 58'] || byName['Слой 60']);
      if (!raw) throw new Error('Missing objects strip');
      const n = 5;
      const cell = Math.floor(raw.width / n);
      for (let i = 0; i < n; i++) {
        const left = i * cell;
        const width = i === n - 1 ? raw.width - left : cell;
        await writeJpegWebp(raw, path.join(OUT, `objects/object-${i + 1}`), {
          crop: { left, top: 0, width, height: raw.height },
          resize: { width: 640, withoutEnlargement: false },
        });
      }
      for (let i = 6; i <= 8; i++) {
        for (const ext of ['jpg', 'webp', 'png']) {
          const p = path.join(OUT, `objects/object-${i}.${ext}`);
          if (fs.existsSync(p)) fs.unlinkSync(p);
        }
      }
    }
  }

  // Form façade: clean photo crop from flattened preview (layer 62 includes UI)
  {
    const preview = fs.readdirSync(path.join(ROOT, 'developer-stuff')).find((n) => n.includes('preview') && n.endsWith('.jpg'));
    if (!preview) throw new Error('preview jpg missing');
    const src = path.join(ROOT, 'developer-stuff', preview);
    const crop = { left: 114, top: 10480, width: 980, height: 700 };
    await sharp(src).extract(crop).jpeg({ quality: 86, mozjpeg: true }).toFile(path.join(OUT, 'form/form-side.jpg'));
    await sharp(src).extract(crop).webp({ quality: 84 }).toFile(path.join(OUT, 'form/form-side.webp'));
    console.log('form/form-side.* 980x700');
  }

  // Solid CTA banner background
  {
    await sharp({
      create: { width: 1980, height: 520, channels: 3, background: { r: 52, g: 61, b: 70 } },
    })
      .jpeg({ quality: 90 })
      .toFile(path.join(OUT, 'cta/cta-banner.jpg'));
    await sharp({
      create: { width: 1980, height: 520, channels: 3, background: { r: 52, g: 61, b: 70 } },
    })
      .webp({ quality: 90 })
      .toFile(path.join(OUT, 'cta/cta-banner.webp'));
  }

  console.log('PSD export complete');
})().catch((e) => {
  console.error(e);
  process.exit(1);
});
