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
	__NAMESPACE__ . '\action_enqueue_style_sidebar_assets'
);

/**
 * Registers all slotfill assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function register_style_plugin_scripts(): void {
	wp_register_script(
		'plugin-newsletter-template-styles',
		get_entry_asset_url( 'newsletter-template-styles' ),
		get_asset_dependency_array( 'newsletter-template-styles' ),
		get_asset_version( 'newsletter-template-styles' ),
		true
	);
	wp_set_script_translations( 'plugin-newsletter-template-styles' );
}
add_action( 'init', __NAMESPACE__ . '\register_style_plugin_scripts' );

/**
 * Enqueue block editor assets for this slotfill.
 */
function action_enqueue_style_sidebar_assets(): void {
	$post_type = get_edit_post_type();
	if ( 'nb_template' !== $post_type ) {
		return;
	}
	wp_enqueue_script( 'plugin-newsletter-template-styles' );
}
