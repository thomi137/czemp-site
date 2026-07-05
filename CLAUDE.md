# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WordPress block theme for the website of artist Claudia Zemp. The site showcases artwork using a custom post type (`artwork`) organized by a `collection` taxonomy. Development runs entirely in Docker.

## Development Environment

```bash
# Start the local environment
docker-compose up -d
# or use the helper script
./scripts/start.sh

# Stop
docker-compose down
# or
./scripts/stop.sh
```

- WordPress: http://localhost:80
- phpMyAdmin: http://localhost:8081

The `cz-theme/`, `plugins/`, and `wp-content/` directories are volume-mounted into the container — file changes are live without rebuilding the container, but you must refresh the browser manually. plugins/ does not contain any specific code now, as plugins are not yet planned. wp-Content/ is here so the coede can be directly copied to the docker container

## Theme Build (Custom Blocks)

All block JavaScript is compiled from `cz-theme/blocks/*/index.js` (and `cz-theme/blocks/index.js`) using `@wordpress/scripts`. The compiled output goes to `cz-theme/build/`.

```bash
cd cz-theme
npm install       # first time
npm run build     # production build
npm start         # watch mode (development)
```

Both custom blocks reference `file:../../build/index.js` as their `editorScript`, so a build is required before blocks appear in the editor.

## Architecture

### Theme (`cz-theme/`)

A WordPress **block theme** (FSE — Full Site Editing). Key files:

| File/Dir | Purpose |
|---|---|
| `functions.php` | Registers blocks, custom post type, taxonomy, and enqueues global CSS |
| `theme.json` | Global styles, typography, color palette, spacing |
| `assets/css/global.css` | Global stylesheet, also loaded as editor style |
| `assets/js/responsive-menu-navigation.js` | Mobile menu JS |
| `templates/` | Full-page FSE templates (HTML) |
| `parts/` | Reusable FSE template parts (header, footer, overlay) |
| `patterns/` | PHP block patterns (header, footer, about, gallery intro) |
| `blocks/` | Custom Gutenberg blocks (source) |
| `build/` | Compiled block JS output — do not edit directly |

### Custom Post Type & Taxonomy

Registered in `functions.php`:
- **`artwork`** — Custom post type. Archive slug: `/galerie/`. Supports title, editor, thumbnail, excerpt.
- **`collection`** — Hierarchical taxonomy on `artwork`. Slug: `/kollektion/`.

Templates for these exist in `templates/`: `archive-artwork.html`, `single-artwork.html`, `taxonomy-collection.html`.

### Custom Blocks

Both blocks are registered server-side via `register_block_type()` in `functions.php` and compiled via `@wordpress/scripts`.

- **`czemp-theme/gallery-item`** (`blocks/gallery-item/`) — Image card with configurable hover overlay, focal point, link, title, description, opacity. Has a `save.js` (static save).
- **`czemp-theme/artwork-list-item`** (`blocks/artwork-list-item/`) — Loop-aware post list item using `usesContext: [postId, postType]`. Server-side rendered via `render.php`.
- **`czemp-theme/sticky-nav`** (`blocks/sticky-nav/`) - navigation block that circumpasses WP quirks when animating a slide in-out menu on smaaller screens. It is a container for a `core/navigation` menu.

### FSE Template Hierarchy

```
templates/front-page.html       ← homepage
templates/page-gallery.html     ← gallery page
templates/archive-artwork.html  ← artwork archive
templates/single-artwork.html   ← single artwork
templates/taxonomy-collection.html ← collection term
parts/header.html / footer.html / cz-overlay.html
```

Patterns in `patterns/` are PHP files that inject block markup and can use dynamic PHP values.
