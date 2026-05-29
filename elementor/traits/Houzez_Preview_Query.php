<?php
namespace HouzezThemeFunctionality\Elementor\Traits;

/**
 * Auto-ported from Houzez framework/functions/ to EstateSite Core.
 * Direct fave_* meta access has been rewritten to use \EstateSite\Core\Property::get/set.
 *
 * @package EstateSite\Core\Functions
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Houzez_Preview_Query {

    /**
     * Tracks why fts_apply_preview_swap() did or didn't swap $post for the
     * current widget render. Per-instance and unused outside this trait.
     *
     * Values:
     *   ''                  → swap performed (or trait never invoked)
     *   'not_editor'        → not in editor/preview context, real frontend render
     *   'no_target_picked'  → editor context, no published post of that type exists
     *                         AND user hasn't picked a preview post for that type
     *
     * @var string
     */
    private $fts_preview_swap_status = '';

    /**
     * Decide which post to use as the preview target for the given type.
     *
     * Order of preference:
     *   1. The fts_builder being edited has a `houzez_preview_post_<type>`
     *      setting saved in `_elementor_page_settings` (chosen via our
     *      Preview Settings panel) — use that, validated as published and
     *      of the right post_type.
     *   2. Fall back to the first published post of that type in the DB
     *      (legacy Houzez Studio behaviour).
     *
     * @param string $post_type Target post type ('property', 'houzez_agent', ...).
     * @return int Post ID, or 0 if none available.
     */
    private function fts_resolve_preview_target( $post_type ) {
        // The fts_builder we're inside is the global query's queried object
        // when we're rendering on `/houzez-studio/single-property/?elementor-preview=<id>`.
        // Use $wp_query->queried_object_id rather than get_the_ID() so we
        // get the document ID even if another trait has already swapped $post.
        global $wp_query;
        $doc_id = isset( $wp_query->queried_object_id ) ? (int) $wp_query->queried_object_id : 0;
        if ( ! $doc_id ) {
            $doc_id = get_queried_object_id();
        }

        if ( $doc_id && class_exists( '\\Elementor\\Core\\Settings\\Manager' ) ) {
            // Apply & Preview triggers `document/save/auto` in the editor.
            // For published templates, Elementor writes the autosave to a
            // CHILD post (status=inherit, post_type=revision) — including any
            // updated `houzez_preview_post_<type>` setting. The main post's
            // meta isn't touched until a full save.
            //
            // Without this autosave-aware lookup, our trait reads the stale
            // main-post setting and renders the previous preview target,
            // even though Elementor's iframe just reloaded after Apply & Preview.
            //
            // Prefer the autosave when it exists AND it actually has
            // _elementor_page_settings stored (some autosaves are for the
            // elements tree only).
            $settings_doc_id = $doc_id;
            if ( function_exists( 'wp_get_post_autosave' ) ) {
                $autosave = wp_get_post_autosave( $doc_id, get_current_user_id() );
                if ( $autosave ) {
                    $autosave_settings = get_post_meta( $autosave->ID, '_elementor_page_settings', true );
                    if ( is_array( $autosave_settings ) && ! empty( $autosave_settings ) ) {
                        $settings_doc_id = $autosave->ID;
                    }
                }
            }

            $page_settings = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
            $model         = $page_settings->get_model( $settings_doc_id );
            $preview_type  = $model->get_settings( 'houzez_preview_type' );

            // First: exact post-type match (widget's family matches the user's picked preview type).
            if ( $preview_type === $post_type ) {
                $target_id = (int) $model->get_settings( 'houzez_preview_post_' . $post_type );
                if ( $target_id && self::fts_valid_post( $target_id, $post_type ) ) {
                    return $target_id;
                }
            }

            // Second: cross-type fallback. The widget is from the wrong "family"
            // for this template — e.g. a `single-post` widget (post-content,
            // post-title, post-excerpt) reused inside a single-listing
            // template, calling single_post_preview_query() while the user
            // picked a property. Always return the user-picked target,
            // ignoring the family mismatch — `get_the_content()`/title/excerpt
            // work the same regardless of post_type. This makes any
            // cross-family widget reuse render real data.
            if ( $preview_type ) {
                $target_id = (int) $model->get_settings( 'houzez_preview_post_' . $preview_type );
                if ( $target_id && self::fts_valid_post( $target_id, $preview_type ) ) {
                    return $target_id;
                }
            }
        }

        // Fallback: first published post of the requested type (legacy behaviour).
        $ids = get_posts( [
            'post_type'      => $post_type,
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ] );
        return ! empty( $ids ) ? (int) $ids[0] : 0;
    }

    private static function fts_valid_post( $id, $expected_type ) {
        $p = get_post( $id );
        return $p && $p->post_status === 'publish' && $p->post_type === $expected_type;
    }

    /**
     * Should the preview-time post swap run for the current request?
     * True only when we're inside the Elementor editor or a WP preview
     * AND the current page is an fts_builder/favethemes-blocks doc.
     */
    private function fts_should_run_preview_swap() {
        $is_edit_mode    = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $is_preview_mode = isset( $_GET['preview_id'] ) && ( $_GET['preview'] ?? false ) == true;

        if ( ! ( $is_edit_mode || $is_preview_mode ) ) {
            return false;
        }
        return is_singular( 'fts_builder' ) || is_singular( 'favethemes-blocks' );
    }

    /**
     * Swap the global $post to the resolved preview target so widgets that
     * read `global $post` see real data. Shared by all per-type variants
     * below (single_property_preview_query, single_agent_preview_query, etc).
     */
    private function fts_apply_preview_swap( $post_type ) {
        $this->fts_preview_swap_status = '';
        \EstateSite\Elementor\Preview_Signal::clear();

        if ( ! $this->fts_should_run_preview_swap() ) {
            $this->fts_preview_swap_status = 'not_editor';
            return;
        }
        $target_id = $this->fts_resolve_preview_target( $post_type );
        if ( ! $target_id ) {
            $this->fts_preview_swap_status = 'no_target_picked';
            \EstateSite\Elementor\Preview_Signal::set_failure( (string) $post_type );

            // Seed $post with a benign empty WP_Post so widgets that do
            // `$post->ID` / `get_post_thumbnail_id( $post->ID )` etc. don't
            // generate "Attempt to read property on null" warnings under
            // PHP 8.x. The render still has no real data — fix (c)'s hint
            // is what the user actually sees on top.
            $GLOBALS['post'] = self::fts_empty_post_stub( $post_type );
            return;
        }
        $GLOBALS['post'] = get_post( $target_id );
        setup_postdata( $GLOBALS['post'] );
    }

    /**
     * A fully-zeroed WP_Post object used as a placeholder when no preview
     * target is available. Widget code that reads `$post->ID` sees 0; meta
     * lookups on 0 return null (Core's accessor short-circuits on falsy IDs);
     * Property::get(0, ...) returns the default. No DB queries, no warnings.
     */
    private static function fts_empty_post_stub( $post_type ) {
        $stub               = new \WP_Post( (object) [] );
        $stub->ID           = 0;
        $stub->post_type    = (string) $post_type;
        $stub->post_status  = 'draft';
        $stub->post_title   = '';
        $stub->post_content = '';
        $stub->post_excerpt = '';
        $stub->post_author  = 0;
        $stub->post_date    = '0000-00-00 00:00:00';
        $stub->post_date_gmt = '0000-00-00 00:00:00';
        $stub->filter       = 'raw';
        return $stub;
    }


    public function single_preview_query() {
        // Determine the right post type from the template_type meta. The
        // template_type is set on the fts_builder being edited.
        $tpl_type = '';
        $doc_id   = get_queried_object_id();
        if ( $doc_id ) {
            $tpl_type = (string) get_post_meta( $doc_id, 'fts_template_type', true );
        }
        $map = [
            'single-listing' => 'property',
            'single-agent'   => 'houzez_agent',
            'single-agency'  => 'houzez_agency',
            'single-post'    => 'post',
        ];
        $post_type = $map[ $tpl_type ] ?? 'property';

        $this->fts_apply_preview_swap( $post_type );
    }

    public function single_property_preview_query() {
        $this->fts_apply_preview_swap( 'property' );
    }

    public function single_agent_preview_query() {
        $this->fts_apply_preview_swap( 'houzez_agent' );
    }

    public function single_agency_preview_query() {
        $this->fts_apply_preview_swap( 'houzez_agency' );
    }

    public function single_post_preview_query() {
        $this->fts_apply_preview_swap( 'post' );
    }

    public function reset_preview_query() {
        if ( $this->fts_should_run_preview_swap() ) {
            wp_reset_postdata();
        }
    }
}