<?php
/**
 * Adds the sidebar script to the Post edit screen.
 *
 * This file will be copied to the assets build directory.
 *
 * @package wp-newsletter-builder-demo-plugin
 */

namespace WP_Newsletter_Builder;

add_action(
	'enqueue_block_editor_assets',
	__NAMESPACE__ . '\action_enqueue_status_sidebar_assets'
);

/**
 * Registers all slotfill assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function register_status_plugin_scripts(): void {
	wp_register_script(
		'plugin-newsletter-status',
		get_entry_asset_url( 'newsletter-status' ),
		get_asset_dependency_array( 'newsletter-status' ),
		get_asset_version( 'newsletter-status' ),
		true
	);
	wp_set_script_translations( 'plugin-newsletter-status' );
}
add_action( 'init', __NAMESPACE__ . '\register_status_plugin_scripts' );

/**
 * Enqueue block editor assets for this slotfill.
 */
function action_enqueue_status_sidebar_assets(): void {
	$post_type = get_edit_post_type();
	if ( 'nb_newsletter' !== $post_type ) {
		return;
	}
	wp_enqueue_script( 'plugin-newsletter-status' );
}
