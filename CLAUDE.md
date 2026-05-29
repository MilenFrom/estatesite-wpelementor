# EstateSite Elementor — Plugin Notes

## Status

Phase 0 scaffold. Bootstrap + dependency checks + custom widget category. No widgets yet.

## Architecture

- **PSR-4 namespace**: `EstateSite\Elementor\` → `includes/`
- **Class prefix**: `ESEle_`
- **Constants**: `ESELE_VERSION`, `ESELE_FILE`, `ESELE_DIR`, `ESELE_URL`, `ESELE_BASENAME`
- **Hard dependencies**: EstateSite Core plugin + Elementor (free is enough)
- **Update pipeline**: TBD (same as Core — not GitHub-token pattern)

## Files of note

- `estatesite-wpelementor.php` — bootstrap, dependency checks
- `includes/class-plugin.php` — singleton, registers widget category, hooks widget loader
- `includes/class-widgets-loader.php` — registry mapping logical names to widget classes (empty in Phase 0)

## Phase 4 work (later)

Port the 66 Houzez Elementor widgets from `wp-content/plugins/houzez-theme-functionality/elementor/widgets/`:
- 8 property-card-v1..v8 variants
- 7 property-carousel-v1..v7 variants
- Property search, grid, ajax tabs, recent-viewed
- Agent/agency widgets
- Blog posts, partners, testimonials
- Login modal, create-listing button
- Mapbox, OpenStreetMap, Google Map

Each port rewires data fetching to use Core's `\EstateSite\Core\Property` accessor instead of raw `get_post_meta($id, 'fave_*')`.

Also absorbs the 8 widgets currently in `estatesite-houzez` plugin (which will then be sunset).

## Dependencies

- WordPress 6.4+
- PHP 7.4+
- EstateSite Core (this package's hard dep)
- Elementor (free) — required
- Elementor Pro — optional, only needed for theme-builder header/footer overrides
