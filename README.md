# КБФасад — WordPress + Elementor landing

Adaptive ventilated-façades landing page for **КБФасад**, editable in **Elementor Free**.

## Repository layout

| Path | Purpose |
|------|---------|
| `wp-content/themes/kbfacade/` | Lightweight Elementor-ready theme (fonts, palette, full-width templates) |
| `wp-content/plugins/kbfacade-elementor/` | Section widgets, forms, popup, landing installer |
| `developer-stuff/` | Design sources (TZ, preview, logos, fonts, icons) |
| `AGENTS.md` | Rules for AI/human contributors |

WordPress core, Elementor, uploads and `wp-config.php` are **not** tracked.

## Requirements

- WordPress 7.0.x (Elementor 4.2.x needs WP ≥ 6.8)
- PHP ≥ 7.4
- Elementor Free 4.2.2+

## Setup

1. Install WordPress and Elementor on the server / local stack.
2. Deploy this repository into the WordPress root (or sync `wp-content/themes/kbfacade` and `wp-content/plugins/kbfacade-elementor`).
3. Activate theme **КБФасад**.
4. Activate plugin **КБФасад Elementor**.
5. Open **Tools → КБФасад Landing** → *Install / refresh landing page*.
6. Set recipient email in **Settings → КБФасад Forms**.
7. Edit the page with Elementor; temporary images from the mockup can be replaced in widget media controls.

## Git workflow

- Base branch: `dev`
- Features: `feat/<topic>` → PR into `dev`
- Commits: [Conventional Commits](https://www.conventionalcommits.org/) in English

Remote: https://github.com/dimabresky/kbfacade.git

## Design tokens

- Orange `#FF6A13`
- Navy `#003B5C`
- Sky `#A8C8E9`
- Grays `#E0E0E0` / `#B0B0B0` / `#343D45`
- Font: Cera Pro (bundled TTF in the theme)
