<?php
/**
 * Core latest posts block modifications.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Registers assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function register_latest_posts_scripts(): void {
	wp_register_script(
		'plugin-newsletter-latest-posts',
		get_entry_asset_url( 'wp-newsletter-builder-latest-posts' ),
		get_asset_dependency_array( 'wp-newsletter-builder-latest-posts' ),
		get_asset_version( 'wp-newsletter-builder-latest-posts' ),
		true
	);
	wp_set_script_translations( 'plugin-newsletter-latest-posts' );
}
add_action( 'init', __NAMESPACE__ . '\register_latest_posts_scripts' );

/**
 * Enqueue block editor assets for latest-postss.
 */
function action_enqueue_latest_posts_assets(): void {
	$post_type = get_edit_post_type();
	if ( ( 'nb_newsletter' !== $post_type ) && ( 'nb_template' !== $post_type ) ) {
		return;
	}
	wp_enqueue_script( 'plugin-newsletter-latest-posts' );
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\action_enqueue_latest_posts_assets' );
