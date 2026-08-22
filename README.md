# Software for WordPress czemp Theme

## Introduction
This is a repository for a WordPress block theme (and, potentially in future, plugins) for the website of artist
Claudia Zemp. The theme (`cz-theme/`) is a standalone WordPress **block theme** (Full Site Editing / FSE) —
templates are HTML block markup, not classic PHP template files. It started life derived from Twenty Twenty-Five
but has since diverged into its own theme rather than a child theme of it. It is hosted here so that the developer
has a means of safely securing the code, but feel free to fork if you find it useful for your intents, provided
that the GPLv2 Licence is adhered to.

## Components

## Development Environment

Normally, the development setup for WordPress suggests to use some distribution
of a virtual machine containing the full stack of DB, WordPress installation and a Webserver.
because the author felt that it leads to more flexibility, he chose to use a dockerized setup and docker compose, as documented
in the `docker-compose.yml` file.

**In practice, testing currently happens against the live site rather than this Docker setup** — the container
should be kept on a current WordPress version so it stays representative of production.

### Docker Images
The current `docker-compose.yml` uses standard [Dockerhub](https://hub.docker.com/) images, specifically:

* [Mysql](https://hub.docker.com/_/mysql)
* [PhpMyAdmin](https://hub.docker.com/_/phpmyadmin)
* [WordPress](https://hub.docker.com/_/wordpress)

Note that you should configure images according to your target environment. It may be even worthwhile to create you own `Dockerfile`s. 
Any improvement and comment on this setup is greatly appreciated (as are corresponding PRs, BTW 😉).

### Usage

Out of the box, this is easy:

    docker-compose up -d

and 

    docker-compose down

to get rid of it. You can access the site at `localhost:80` or whichever port you think is ok, the admin tool fo mysql is at port `8081`.

Note that there are some volumes linked to the local hard drive. This is so that changes in the current code
are directly linked into the docker container. Although this is very convenient, it does only provide some sort of poor man's
hot reload in that the browser needs to be refreshed manually.
Since the author sees that as a minor inconvenience, he did not further fine tune.

However, again, any PR or Email to the [author](mailto:thomas@prosser.ch) on how to improve is greatly appreciated.

Note that the `plugins` and `wp-content` directories, and the theme itself (`cz-theme/`), are volume-mapped to
local disk so they are persisted between restarts and directly editable without rebuilding the container.

## Theme Files

The theme lives in `cz-theme/` and is a WordPress **block theme** (Full Site Editing / FSE) — templates are HTML
block markup, not classic PHP template files. There is no `parts/` directory: template parts were tried early on
for the header/footer and dropped in favor of two fully dynamic blocks (`site-header`/`site-footer`) referenced
directly by every template — there's no other chrome yet that needs the "independently editable, reused
everywhere" property template parts exist for.

```
cz-theme/
├── functions.php          # thin loader — requires everything under inc/
├── inc/                   # theme logic, split by concern
├── theme.json             # global styles, typography, color palette, spacing
├── templates/             # full-page FSE templates (HTML)
├── patterns/              # PHP block patterns (can use dynamic PHP values)
├── blocks/                # custom Gutenberg blocks (source)
├── build/                 # compiled output — do not edit directly (block JS + minified admin JS)
└── assets/                # global SCSS/JS sources (not part of the wp-scripts block build)
```

### `inc/`

`functions.php` itself only requires the files below — each covers one concern:

| File | Purpose |
|---|---|
| `setup.php` | Theme support, block registration, script/style enqueues |
| `post-types.php` | `artwork` post type + `collection` taxonomy registration, price meta, featured-image↔collection sync |
| `rest-api.php` | Token-protected `czemp/v1` REST routes used by the migration scripts — routes are only *registered* while `CZ_MIGRATE_TOKEN` is defined in `wp-config.php`, so they don't exist (404, not just 403) between migrations |
| `seo.php` | Hand-rolled SEO/social: meta description, Open Graph, Twitter Card |
| `maintenance.php` | Theme-level "coming soon" mode, admin-toggleable |
| `post-dates.php` | Optional start/end visibility-window meta on Posts |
| `sortable.php` | Drag-and-drop artwork ordering per collection (term-meta based, REST + admin UI) |
| `collection-thumbnail.php` | Term-meta + focal-point picker admin UI for collection thumbnails |
| `admin.php` | wp-admin list-table columns/filters, Media Library defaults, Editor-role capability restrictions |
| `frontend.php` | Subcollection query scoping, `/galerie/` → `/gallery/` redirect, nav active-state, single-artwork image sizing, pagination cap |

### Custom Post Type & Taxonomy

- **`artwork`** ("Werke") — the artwork custom post type. Archive at `/galerie/`, which redirects to the `/gallery/`
  page (a hand-built collection overview) rather than showing a flat, uncategorized dump.
- **`collection`** ("Kollektionen") — hierarchical taxonomy on `artwork`. Slug `/kollektion/`.

Templates for these live in `templates/`: `archive-artwork.html`, `single-artwork.html`, `taxonomy-collection.html`.

### Custom Blocks

Registered server-side via `register_block_type()` in `inc/setup.php`, compiled via `@wordpress/scripts`. Prefer
dynamic (`render.php`, no stored markup) over a real `save()` wherever an instance doesn't need per-block editing —
`gallery-item` is the one deliberate exception:

- **`czemp-theme/gallery-item`** — image card with configurable hover overlay, focal point, link, title, description. Has a real `save()`.
- **`czemp-theme/artwork-list-item`** — loop-aware post list item (`usesContext: [postId, postType]`), server-rendered.
- **`czemp-theme/artwork-price`** — renders the current artwork's price meta, if set.
- **`czemp-theme/artwork-nav`** — prev/next artwork navigation (keyboard, swipe, and mouse-only arrow buttons).
- **`czemp-theme/sticky-nav`** — wraps `core/navigation` to handle the slide-in mobile menu.
- **`czemp-theme/breadcrumbs`** — Home / Galerie / Kollektion / … trail.
- **`czemp-theme/collection-subcategories`** — renders a collection's child-collection tiles.
- **`czemp-theme/latest-posts`** — homepage "Aktuelles" tile: currently-visible dated Posts.
- **`czemp-theme/event-archive`** — dated Posts as a tile grid, grouped by year, upcoming/past.
- **`czemp-theme/current-exhibitions`** — hero "scroll to next section" button with an animated chevron cue.
- **`czemp-theme/site-header`** / **`czemp-theme/site-footer`** — fixed site chrome, referenced directly by every template.

### Building the theme

Three independent build steps (block JS, global/shared-breakpoint SCSS, plain admin/editor JS) — see
`cz-theme/`'s own docs for the per-tool watch commands.

```bash
cd cz-theme
npm install       # first time
npm run build     # production build — all three steps, required before the theme renders/blocks appear correctly
npm start         # watch mode — block JS only; see start:css / start:js for the other two
```

## Migration & Deployment

- `scripts/migrate/` — CSV-to-WordPress migration tooling (collections, artworks, media) via the `czemp/v1` REST
  routes; see `scripts/migrate/MIGRATION.md` for the full runbook and status log.
- `scripts/deploy_test.sh` — builds the theme and ships it to the server over SSH/rsync. Prompts for confirmation
  before deploying. This targets the real production site (claudia-zemp.ch) — there is no separate staging
  environment.
