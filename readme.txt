=== EstateSite Elementor ===
Contributors: estatesite
Tags: real estate, elementor, widgets
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.3
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Elementor widgets, dynamic tags, theme builder integration, and templates library for EstateSite.

== Description ==

Adds EstateSite-specific widgets, dynamic tags, and templates to Elementor. Requires the EstateSite Core plugin and Elementor (free is sufficient; theme-builder features require Pro or our Elementor Enhancer fork).

* 100+ Houzez Elementor widgets ported (single-property variants, property cards v1-v8, search forms, agent cards, agency listings, partners, testimonials, blog posts)
* Theme Builder fork from houzez-studio plugin v1.3.3 — fts_builder CPT, display conditions, header/footer/single template editor
* Self-hosted Houzez template library (367 Elementor templates) fetched on demand
* Houzez_Preview_Query trait so single-* widgets render real preview data when editing Theme Builder templates
* Editor-side "pick a preview post" hint when no target is configured (replaces empty/broken render with actionable message)

== Changelog ==

= 1.0.3 =
* Pivot: Templates are now served live from the EstateSite library at https://dev.estatesite.eu/wp-json/estatesite/v1/templates instead of being downloaded and extracted locally. The previous 140 MB tarball workflow is gone — your site fetches each template only when you actually insert it, and images are sideloaded into your Media Library on demand. Result: customer plugin zip ships without the bundled templates/ directory, no admin button-press required to "install" the library, no PharData extraction, no atomic-swap staging dir, no 170 MB of disk in wp-content/uploads/.
* New: REST proxy POST /wp-json/estatesite-wpelementor/v1/insert-template handles the insert flow customer-side. It calls out to the EstateSite library, runs the returned Elementor JSON through Elementor's own Import_Images pipeline (sha1 dedupe via _elementor_source_image_hash postmeta — re-inserting the same template won't re-upload its images), and returns the localized template ready to drop into the editor.
* New: ESELE_TEMPLATES_LIBRARY_URL wp-config.php constant. Define it to point your site at a different library origin (staging, mirror, internal CDN) without touching plugin code. Defaults to https://dev.estatesite.eu/wp-json/estatesite/v1/templates.
* New: Templates::library_url() / manifest_url() / single_url() / proxy_endpoint_url() / is_remote_mode() resolver API. Themes and add-ons can read the configured library origin and the customer-side proxy endpoint without hardcoding URLs.
* Tweak: Admin "EstateSite → EE Templates" page replaced. The old "Fetch templates" button is gone (no tarball to fetch); the page is now a "Test connection" card that pings the configured library origin and shows manifest entry count + HTTP status so you can verify connectivity at a glance.
* Tweak: Template preview image base URL now defaults to your own site (home_url('/templates')) instead of estatesite.eu/templates. Previews resolve against whichever origin actually serves the template assets.
* Deprecated: includes/class-template-fetcher.php is now a no-op shim. Method signatures preserved for back-compat; fetch() returns a "deprecated — templates are served remotely" message and last_fetch() returns null. If you had wired custom code to the fetcher it will keep loading but stop doing work — switch to the REST proxy.
* Internal: Templates class runs in dual mode. On the dev origin (bundled templates present) it serves the full library over REST; on customer installs (no bundled templates) it acts as a thin resolver that delegates to the REST proxy. Same class, same public API, behavior keyed off filesystem state.
* Internal: CORS headers added to the library REST routes (Access-Control-Allow-Origin: *, Access-Control-Allow-Credentials: false). Scoped to /estatesite/v1/templates* only via WP's rest_pre_serve_request filter — WP core's default cors filter is removed for these routes via priority 1 + remove_filter so the headers we send are the ones the browser sees.

= 1.0.2 =
* Architecture: Stop bundling the 170 MB Houzez template library in the plugin zip. Customer uploading the previous v1.0.0 (140 MB zip) hit "The link you followed has expired" because PHP truncated the POST body — most hosts default upload_max_filesize to 2-8 MB. Plugin zip now 972 KB (down from 140 MB), templates served as a separate tarball from the EstateSite update server, fetched on demand via the new "EstateSite → EE Templates" admin page with a single button click.
* New: includes/class-template-fetcher.php downloads + sha256-verifies + PharData-extracts the tarball into a staging dir, atomically swaps into wp-content/uploads/estatesite-wpelementor/templates/ (survives plugin updates).
* New: includes/admin/class-templates-page.php — admin page with library status (templates installed yes/no, manifest entries, content/image file counts, last-fetch metadata) and "Fetch templates" button.
* Fix: Templates::base_dir() now prefers wp-content/uploads/estatesite-wpelementor/templates/ when present, falls back to bundled plugin/templates/ for dev tree + pre-1.0.2 installs.
* Fix: Editor preview hint. When the Houzez_Preview_Query trait can't resolve a preview post for a single-* widget, the widget now renders an empty WP_Post stub instead of crashing with TypeError on $post->ID, and a red admin notice ("No preview data available — pick a preview post in document settings") is prepended to the widget's output via elementor/widget/render_content filter.
* New: includes/class-preview-signal.php is the cross-class signal registry the trait writes to. Per-using-class trait statics aren't shared across widget instances, so the signal needs an out-of-trait home.

= 1.0.1 =
* Fix: Activation hook + post-activation bootstrap wrapped in try/catch. Customers hitting "The link you followed has expired" when activating got no diagnostic — WP swallowed any throwable during the activation request. Now exceptions are logged to error_log and surfaced as a red admin notice ("EstateSite Elementor bootstrap failed: <message> (<file>:<line>)") so the plugin stays active in a degraded state instead of being rejected outright.

= 1.0.0 =
* First production release.
* PSR-4 autoloader for EstateSite\Elementor\ namespace.
* Houzez framework constant aliases (HOUZEZ_PLUGIN_URL, HOUZEZ_PLUGIN_DIR, HOUZEZ_PLUGIN_IMAGES_URL, HOUZEZ_TEMPLATES, HOUZEZ_VERSION etc.) so ported Houzez widget files build asset URLs against our plugin paths.
* HTF helpers (helpers.php, functions.php, functions-options.php, functions-rewrite.php, security-helpers.php) and HTF classes loaded at boot.
* Self-hosted template catalog — intercepts wp_remote_get('studio.houzez.co/wp-json/favethemes-blocks/v1/templates*') via pre_http_request and serves responses from local files. No runtime dependency on third-party services.
* Theme Builder (ported from houzez-studio v1.3.3) — fts_builder CPT, render-template engine, display conditions for header/footer/single templates.
* Houzez_Preview_Query trait — swaps $GLOBALS['post'] for single-* widgets during Elementor editor preview so they render real data.
* Self-hosted update pipeline via \EstateSite\Core\Update_Checker — manifest at https://dev.estatesite.eu/updates/estatesite-wpelementor.json.

= 0.1.0 =
* Initial scaffolding (Phase 0). Bootstrap and dependency checks.
