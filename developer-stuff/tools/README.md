# Landing asset export tools

Exports optimized images into `wp-content/plugins/kbfacade-elementor/assets/images/` from the layered PSDs in `developer-stuff/`.

## Prerequisites

- Node.js 18+
- One-time deps:

```bash
npm install --prefix "%TEMP%\kbfacade-psd-export" ag-psd sharp
```

## Export

```bash
node developer-stuff/tools/export-landing-assets.js
```

Sources:

- `developer-stuff/Сайт_1_здесь текст.psd` — hero, gallery, fastenings, objects
- `developer-stuff/Мужик в каске.psd` — transparent worker cutout
- `developer-stuff/Сайт_1_preview.jpg` — clean form-side photo crop (layer 62 also contains form UI)

Do not commit `%TEMP%\kbfacade-psd-export\node_modules`.
