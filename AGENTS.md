# Agent instructions — КБФасад (kbfacade)

WordPress landing for ventilated façades (**КБФасад**), editable via **Elementor Free**.

## Stack

| Layer | Version / notes |
|--------|------------------|
| WordPress | 7.0.3 (core ≥ 6.8 for Elementor 4.x) |
| PHP | ≥ 7.4 (prefer 8.1+) |
| Elementor | Free **4.2.2** (required plugin; do not fork) |
| Theme | `wp-content/themes/kbfacade` |
| Companion plugin | `wp-content/plugins/kbfacade-elementor` |

Design sources live in [`developer-stuff/`](developer-stuff/) (TZ XLSX, preview, palette, logos, fonts, icons).

## Role and priorities

- Prefer **WordPress and Elementor APIs** over custom HTML pages that cannot be edited in Elementor.
- Keep the theme thin: layout canvas, fonts, CSS variables, Elementor theme support.
- Put landing sections, sliders, popup, and form REST logic in the **companion plugin** as Elementor widgets/controls.
- Do **not** modify WordPress core or the Elementor plugin under `wp-content/plugins/elementor/`.
- Keep diffs scoped to the task; match existing naming and structure.

## Where to put code

| Concern | Location |
|--------|----------|
| Theme, fonts, design tokens | `wp-content/themes/kbfacade/` |
| Elementor widgets, forms, popup, template JSON | `wp-content/plugins/kbfacade-elementor/` |
| Spec / design assets (source) | `developer-stuff/` |
| Agent rules | `AGENTS.md` |

## Elementor conventions

- Register widgets on `elementor/widgets/register`.
- Every user-facing string, image, link, and card must be an Elementor control or repeater field.
- Use Elementor responsive controls for desktop / tablet / mobile.
- Landing page structure: importable template under `kbfacade-elementor/templates/` plus widgets with sensible defaults from the TZ.
- Forms: WordPress REST + `wp_mail()`, recipient configurable in WP admin. Success copy must match TZ exactly.
- Site contacts (phones, email, address, map embed, social links, form recipient) live in **Settings → КБФасад** (`kbfacade_site_settings`) and are read by Header / Contacts / Footer widgets — do not duplicate them as Elementor overrides.

## Design system

Palette (from `developer-stuff/Выбранная цветовая палитра.jpeg`):

- Accent orange: `#FF6A13`
- Dark blue: `#003B5C`
- Light blue: `#A8C8E9`
- Light gray: `#E0E0E0`
- Medium gray: `#B0B0B0`
- Dark charcoal: `#343D45`

Typography: **Cera Pro** (local `@font-face` in the theme). Prefer WOFF2 with TTF fallback.

## Accessibility and UX

- Keyboard-accessible navigation, burger, sliders, lightbox, and modal.
- Touch/focus equivalents for hover overlays.
- Slider autoplay must pause on interaction/focus and respect `prefers-reduced-motion`.
- Escape and sanitize all output (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).

## Git and delivery

- Remote: `https://github.com/dimabresky/kbfacade.git`
- Base branch: **`dev`**
- Feature work: `feat/<topic>` or `fix/<topic>` from latest `dev` (see feature-pr-flow).
- Commits: English, [Conventional Commits](https://www.conventionalcommits.org/) (`feat:`, `fix:`, `chore:`, `docs:`, …).
- Never commit secrets (`wp-config.php`, `.env`, keys).
- Open PR into `dev`; merge only after explicit human confirmation.
- Do not force-push unless explicitly requested.

## Out of scope unless requested

- Elementor Pro / paid add-ons.
- Broad refactors unrelated to the task.
- Editing WordPress core or upstream Elementor.
- Committing directly to `dev` for feature work.
