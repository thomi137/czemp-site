# Software for WordPress czemp Theme

## Introduction
This is a repository for a WordPress block theme and possibly plugins facilitating a more efficient upload functionality 
for a custom made home site. The theme is currently based on the WordPress Twenty Twenty Five theme, and is as such a 
[Child Theme](https://developer.wordpress.org/themes/advanced-topics/child-themes/) currently. It is hosted here so that
the developer has a means of safely securing the code, but feel free to fork if you find it useful for your intents, provided
that the GPLv2 Licence is adhered to.

## Components

### Development Environment

Normally, the development setup for WordPress suggests to use some distribution
of a virtual machine containing the full stack of DB, WordPress installation and a Webserver.
because the author felt that it leads to more flexibility, he chose to use a dockerized setup and docker compose, as documented
in the `docker-compose.yml` file. 

#### Images
The current `docker-compose.yml` uses standard [Dockerhub](https://hub.docker.com/) images, specifically:

* [Mysql](https://hub.docker.com/_/mysql)
* [PhpMyAdmin](https://hub.docker.com/_/phpmyadmin)
* [WordPress](https://hub.docker.com/_/wordpress)

Note that you should configure images according to your target environment. It may be even worthwhile to create you own `Dockerfile`s. 
Any improvement and comment on this setup is greatly appreciated (as are corresponding PRs, BTW 😉).

#### Usage

Out of the box, this is easy:

    docker-compose up -d

and 

    docker-compose down

to get rid of it. You can access the site at `localhost:80` or whichever port you think is ok, the admin tool fo mysql is at port `8081`.

Note that there are some volumes linked to the local hard drive. This is so that changes in the current code
are directly linked into the docker container. Although this is very convenient, it does only provide some sort of poor man's
hot reload in that the browser needs to be refreshed manually.
Since the author sees that as a minor inconvenience, he did not further fine tune.

However, again, any PR or Email to the [author](mailto:thomas@rosser.ch) on how to improve is greatly appreciated.

Note that the `plugins` and `data` directories of the wordpress image have been mapped to local disk so they are persisted  
between restarts. This ensures you do not have to reinitiate every time you restart the Docker images.

## Theme Files

The theme lives in `cz-theme/` and is a WordPress **block theme** (Full Site Editing / FSE) — templates and template
parts are HTML block markup, not classic PHP template files.

```
cz-theme/
├── functions.php          # thin loader — requires everything under inc/
├── inc/                    # theme logic, split by concern
├── theme.json              # global styles, typography, color palette, spacing
├── templates/               # full-page FSE templates (HTML)
├── parts/                   # reusable template parts (header, footer, overlay)
├── patterns/                 # PHP block patterns (can use dynamic PHP values)
├── blocks/                    # custom Gutenberg blocks (source)
├── build/                      # compiled block JS output — do not edit directly
└── assets/                      # global CSS/JS (not part of the block build)
```

### `inc/`

`functions.php` itself only requires the files below, in order — each covers one concern:

| File | Purpose |
|---|---|
| `setup.php` | Theme support, block registration, script/style enqueues, WebP image output |
| `post-types.php` | `artwork` post type + `collection` taxonomy registration, artwork↔image sync |
| `rest-api.php` | Token-protected `czemp/v1` REST routes used by the migration scripts |
| `collection-thumbnail.php` | Term-meta + focal-point picker admin UI for collection thumbnails |
| `admin.php` | wp-admin list-table columns/filters, Media Library defaults, the per-user default-collection profile field |
| `frontend.php` | Subcollection query scoping, `/galerie/` → `/gallery/` redirect, nav active-state, pagination cap |

### Custom Post Type & Taxonomy

- **`artwork`** ("Werke") — the artwork custom post type. Archive at `/galerie/`, which redirects to the `/gallery/`
  page (a hand-built collection overview) rather than showing a flat, uncategorized dump.
- **`collection`** ("Kollektionen") — hierarchical taxonomy on `artwork`, and also on `attachment` (Media Library),
  so images can be filtered/browsed by collection too. Slug `/kollektion/`.

Templates for these live in `templates/`: `archive-artwork.html`, `single-artwork.html`, `taxonomy-collection.html`.

### Custom Blocks

Registered server-side via `register_block_type()` in `inc/setup.php`, compiled via `@wordpress/scripts`:

- **`czemp-theme/gallery-item`** — image card with configurable hover overlay, focal point, link, title, description.
- **`czemp-theme/artwork-list-item`** — loop-aware post list item (`usesContext: [postId, postType]`), server-rendered.
- **`czemp-theme/sticky-nav`** — wraps `core/navigation` to work around WordPress quirks with the slide-in mobile menu.
- **`czemp-theme/collection-subcategories`** — renders a collection's child-collection tiles.
- **`czemp-theme/breadcrumbs`** — Home / Galerie / Collection / Subcollection / Title trail.

### Building blocks

```bash
cd cz-theme
npm install       # first time
npm run build     # production build — required before blocks appear in the editor
npm start         # watch mode for development
```

## Migration & Deployment

- `scripts/migrate/` — CSV-to-WordPress migration tooling (collections, artworks, media) via the `czemp/v1` REST
  routes; see `scripts/migrate/MIGRATION.md` for the full runbook and status log.
- `scripts/deploy_test.sh` — builds the theme and ships it to the server over SSH/SCP. Prompts for confirmation
  before deploying.

